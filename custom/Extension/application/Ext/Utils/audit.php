<?php

class auditReports
{
	private $parent;
	private $module;
	private $processed = array();
	private $tableArray = array();

	/**
	 * auditReports constructor.
	 * @param string $module - the bean name of the parent module
	 */
	public function __construct($module)
	{
		$bean = BeanFactory::newBean($module);
		$this->parent = $bean;
		$this->module = $module;
	}

	/**
	 * Refresh all Audit Report related information
	 * @param int $recordsToProcess
	 */
	public function buildAuditSupport($recordsToProcess = -1)
	{
		$newLangList = array();
		//I arbitrarily check the date on the english language file, you can check any you want.
		$fileTime = filemtime("custom/Extension/application/Ext/Language/en_us.sugar_{$this->module}_field_name_list.php");
		if ($fileTime !== false) {
			$timeSinceUpdate = time() - $fileTime;
		} else {
			$timeSinceUpdate = time();
		}
		//Crete a language file with all the translated names for all the fields contained in this module
		//Just do this once a day, you can adjust as needed
		if (!empty($this->module) && $timeSinceUpdate > 86400) {
			foreach ($this->parent->field_defs as $fieldName => $fieldDefinition) {
				if (isset($this->parent->field_defs[$fieldName]['vname']) && !empty($this->parent->field_defs[$fieldName]['vname'])) {
					$newLangList[$fieldName] = translate($this->parent->field_defs[$fieldName]['vname'],
						$this->module);
				}
			}
			$dropDownList = $this->module . '_field_name_list';
			$this->addItemsToDropdown($dropDownList, $newLangList);
			$GLOBALS['log']->fatal("Audit: Creation of the {$dropDownList} option list is complete.");
		}

		$table = strtolower($this->module);
		//We want to remove any records that hold no data what so ever.
		$deleteSQL = "DELETE FROM {$table}_audit
                   	  WHERE COALESCE(before_value_text,'')='' AND COALESCE(after_value_text,'')='' AND
                            COALESCE(before_value_string,'')='' AND COALESCE(after_value_string,'')=''";
		$result = $GLOBALS['db']->query($deleteSQL, true);
		if ($result) {
			$numOfRows = $GLOBALS['db']->getAffectedRowCount($result);
		} else {
			$numOfRows = 0;
		}
		if ($numOfRows > 0) {
			$GLOBALS['log']->fatal("Audit: Deleted {$numOfRows} empty rows from the audit table {$table}_audit.");
		}
		//Now we want to go to the audit table and collect up all the records that have not been translated as yet.  We
		// do this by getting all the records with a NULL in BOTH before_value_text and after_value_text
		$usedIDs = "SELECT a.id,a.field_name,a.data_type,a.before_value_string,a.after_value_string
					FROM {$table}_audit a
                    WHERE COALESCE(before_value_text,'')='' AND COALESCE(after_value_text,'')=''
                    ORDER BY date_created DESC";
		if ($recordsToProcess > 0) {
			$usedIDs .= " LIMIT {$recordsToProcess}";
		}
		$result = $GLOBALS['db']->query($usedIDs, true);
		if ($result) {
			$numOfRows = $GLOBALS['db']->getRowCount($result);
		} else {
			$numOfRows = 0;
		}
		//We don't cache these for a few reasons.  Mainly because of quoting issues.  I might work this out in
		// future versions.
		$noCache = array('name', 'phone', 'varchar');
		$GLOBALS['log']->fatal("Audit: Processing {$numOfRows} rows from {$table}_audit");
		$processCount = 0;

		while ($hash = $GLOBALS['db']->fetchByAssoc($result)) {
			//Every 500 rows check how many are left
			if ($processCount++ % 1000 == 0) {
				$remainingSQL = "SELECT count(a.id)
								 FROM {$table}_audit a
                    			 WHERE COALESCE(before_value_text,'')='' AND COALESCE(after_value_text,'')=''";
				$count = $GLOBALS['db']->getOne($remainingSQL);
				$GLOBALS['log']->fatal("Audit: Number of unprocessed rows remaining in {$table}_audit: {$count} [{$processCount}/{$numOfRows}]");
			}
			$fieldName = $hash['field_name'];
			$dataType = $hash['data_type'];
			$bvs = $hash['before_value_string'];
			$avs = $hash['after_value_string'];
			$id = $hash['id'];

			//Handle before_value_string
			$isProcessed = (array_key_exists('before' . $bvs . $fieldName, $this->processed) && !in_array($dataType,
					$noCache));
			if (!$isProcessed && !empty($bvs)) {
				if (!in_array($dataType, $noCache)) {
					$this->processed['before' . $bvs . $fieldName] = 1;
				}
				$this->updateAuditTable($dataType, $table, $bvs, $fieldName, $id, 'before');
			}

			//Handle after_value_string
			$isProcessed = (array_key_exists('after' . $avs . $fieldName, $this->processed) && !in_array($dataType,
					$noCache));
			if (!$isProcessed && !empty($avs)) {
				if (!in_array($dataType, $noCache)) {
					$this->processed['after' . $avs . $fieldName] = 1;
				}
				$this->updateAuditTable($dataType, $table, $avs, $fieldName, $id, 'after');
			}
		}
		$GLOBALS['log']->fatal("Audit: Audit Rebuild for the {$table}_audit is complete.");
	}

	/**
	 * @param $dropDownList
	 * @param $item_list
	 */
	private function addItemsToDropdown($dropDownList, $item_list)
	{
		require_once('modules/ModuleBuilder/MB/ModuleBuilder.php');
		require_once('modules/ModuleBuilder/parsers/parser.dropdown.php');
		$parser = new ParserDropDown();
		$params = array();
		$_REQUEST['view_package'] = 'studio'; //need this in parser.dropdown.php
		$params['view_package'] = 'studio';
		$params['dropdown_name'] = $dropDownList; //replace with the dropdown name
		$params['dropdown_lang'] = 'en_us';//create your list...substitute with db query as needed
		foreach ($item_list as $k => $v) { //merge new and old values
			$drop_list[] = array($k, $v);
		}
		//TODO:Update this to use namespaces
		$json = getJSONobj();
		$params['list_value'] = $json->encode($drop_list);
		$parser->saveDropDown($params);
	}

	/**
	 * This is my version 0.1 of code to figure out what module a 'relate' ID is relating to.  Its not very good but
	 *  it works reliably.  I want to make it a but more fields_defs aware in the next version and remove some of this
	 *  hard coded logic
	 *
	 * @param string $fieldName
	 * @param string $value
	 * @return string
	 */
	private function getRelateValue($fieldName, $value)
	{
		$fieldDefs = $this->parent->field_defs[$fieldName];
		if (isset($fieldDefs['type']) && ($fieldDefs['type'] == 'relate' || $fieldDefs['type'] == 'email')) {
			$module = $fieldDefs['module'];
		} elseif (isset($this->parent->field_defs[$fieldDefs['group']]['module'])) {
			$module = $this->parent->field_defs[$fieldDefs['group']]['module'];
		} elseif (stristr($fieldDefs['name'], 'user') !== false) {
			$module = 'Users';
		} elseif (stristr($fieldDefs['name'], 'team') !== false) {
			$module = 'Teams';
		} elseif ($fieldName == 'parent_id') {
			$fieldDefs = $this->parent->field_defs['parent_name'];
			$module = $fieldDefs['module'];
		} else {
			//if there is a field in the audit table I can't translate I put this in the log to alert me
			$GLOBALS['log']->fatal("Unhandled fieldName in {$this->parent->module} Audit trail: {$fieldName}");
			$module = null;
		}

		if (!array_key_exists($module, $this->tableArray) && $module != null) {
			$relBean = BeanFactory::getBean($module);
			if (isset($relBean->field_defs['first_name'])) {
				$mainName = "CONCAT(first_name,' ',last_name)";
			} elseif (isset($relBean->field_defs['email_address'])) {
				$mainName = 'email_address';
			} else {
				$mainName = 'name';
			}
			$modInfo = $relBean->table_name . '/' . $mainName;
			$this->tableArray[$module] = $modInfo;
		} else {
			$modInfo = $this->tableArray[$module];
		}
		list($table_name, $name) = explode('/', $modInfo, 2);
		$dataSQL = "SELECT {$name} FROM {$table_name} WHERE id='{$value}'";
		$nameOfRecord = $GLOBALS['db']->getOne($dataSQL);
		//if the parent has been deleted from the system use this
		if (empty($nameOfRecord)) {
			$nameOfRecord = '---COULD NOT LOCATE PARENT RECORD---';
		}
		return $nameOfRecord;
	}

	/**
	 * This function does the SQL to update the before_value_text and after_value_text values in the audit table
	 *
	 * @param string $dataType
	 * @param string $table
	 * @param string $currentValue
	 * @param string $fieldName
	 * @param string $id
	 * @param string $auditFieldPrefix
	 */
	private function updateAuditTable($dataType, $table, $currentValue, $fieldName, $id, $auditFieldPrefix)
	{
		global $app_list_strings;
		switch ($dataType) {
			case 'name':
			case 'phone':
			case 'varchar':
				$currentValue = $GLOBALS['db']->quote(htmlspecialchars_decode(str_replace('\\', '', $currentValue),
					ENT_QUOTES));
				if (stristr($currentValue, "'") !== false) {
					$updateSQL = "UPDATE {$table}_audit SET {$auditFieldPrefix}_value_text='{$currentValue}' WHERE id='{$id}'";
				} else {
					$updateSQL = "UPDATE {$table}_audit SET {$auditFieldPrefix}_value_text='{$currentValue}' WHERE {$auditFieldPrefix}_value_string='{$currentValue}'";
					$this->processed[$auditFieldPrefix . $currentValue . $fieldName] = 1;
				}
				$GLOBALS['db']->query($updateSQL, true);
				break;
			case 'int':
			case 'date':
			case 'decimal':
				//These values I update ALL records that have the same text in the string field.  This means I only and
				// to use one SQL update to add translated values to potentially tens or hundreds of records.
				$currentValue = $GLOBALS['db']->quote(htmlspecialchars_decode(str_replace('\\', '', $currentValue),
					ENT_QUOTES));
				$updateSQL = "UPDATE {$table}_audit SET {$auditFieldPrefix}_value_text='{$currentValue}' WHERE {$auditFieldPrefix}_value_string='{$currentValue}'";
				$GLOBALS['db']->query($updateSQL, true);
				break;
			case 'currency':
				//I mass update these fields too but I include a hard coded $ as I couldn't get the SugarCRM currency
				// code to work for some reason.  I will fix this in a future version
				if (empty($currentValue)) {
					$newValue = '0.00';
				} else {
					$newValue = number_format($currentValue, 2, '.', ',');
				}
				//TODO: use SugarCRM currency functions
				$updateSQL = "UPDATE {$table}_audit SET {$auditFieldPrefix}_value_text='\${$newValue}' WHERE {$auditFieldPrefix}_value_string='{$currentValue}'";
				$GLOBALS['db']->query($updateSQL, true);
				break;
			case 'datetimecombo':
			case 'datetime':
				//In this one, we format the date to the Admins date format and timezone.  We cant do it to the report user as we store this
				//perfectly in the DB
				$datetime = new datetime($currentValue, new DateTimeZone('UTC'));
				$timedate = new TimeDate();
				$formattedDate = $timedate->asUser($datetime, BeanFactory::getBean('Users', '1'));
				$updateSQL = "UPDATE {$table}_audit SET {$auditFieldPrefix}_value_text='{$formattedDate} EDT' WHERE {$auditFieldPrefix}_value_string='{$currentValue}'";
				$GLOBALS['db']->query($updateSQL, true);
				break;
			case 'relate':
			case 'id':
			case 'email':
			case 'team_list':
				if ($dataType = 'email' && stristr($currentValue, '@') !== false) {
					$updateSQL = "UPDATE {$table}_audit SET {$auditFieldPrefix}_value_text='{$currentValue}' WHERE {$auditFieldPrefix}_value_string='{$currentValue}'";
				} else {
					//Get the name of the related record
					$nameOfRecord = $this->getRelateValue($fieldName, $currentValue);
					$updateSQL = "UPDATE {$table}_audit SET {$auditFieldPrefix}_value_text='{$nameOfRecord}' WHERE {$auditFieldPrefix}_value_string='{$currentValue}'";
				}
				$GLOBALS['db']->query($updateSQL, true);
				break;
			case 'bool':
				//Checkboxes
				if ($currentValue == 1) {
					$checked = 'Checked';
				} else {
					$checked = 'Unchecked';
				}
				$updateSQL = "UPDATE {$table}_audit SET {$auditFieldPrefix}_value_text='{$checked}' WHERE {$auditFieldPrefix}_value_string='{$currentValue}' AND
																									data_type='{$dataType}'";
				$GLOBALS['db']->query($updateSQL, true);
				break;
			case 'enum':
			case 'multienum':
				//Get a translated Dropdown value
				$optionList = $this->parent->field_defs[$fieldName]['options'];
				$optionArray = $app_list_strings[$optionList];
				if ($dataType == 'enum') {
					$nameOfOption = $optionArray[$currentValue];
					if (empty($nameOfOption)) {
						$nameOfOption = $currentValue . ' [UNDEFINED]';
					}
				} else {
					$listOfNames = array();
					$list = explode('^,^', substr($currentValue, 1, -1));
					foreach ($list as $item) {
						$nameOfOption = $optionArray[$item];
						if (empty($nameOfOption)) {
							$nameOfOption = $currentValue . ' [UNDEFINED]';
						}
						$listOfNames[] = $nameOfOption;
					}
					$nameOfOption = implode(', ', $listOfNames);
				}
				$updateSQL = "UPDATE {$table}_audit SET {$auditFieldPrefix}_value_text='{$nameOfOption}' WHERE {$auditFieldPrefix}_value_string='{$currentValue}' AND
																										 field_name='{$fieldName}'";
				$GLOBALS['db']->query($updateSQL, true);
				break;
			default:
				//If we have a new or unaccounted for dataTyp just log it to the sugarcrm.log file
				$GLOBALS['log']->fatal("Unhandled DataType in {$this->parent->module} Audit trail: {$dataType}");
				break;
		}
	}
}
