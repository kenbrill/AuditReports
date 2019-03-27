<?php

class SugarWidgetFieldjson extends SugarWidgetReportField
{
	public function queryFilterEquals($layout_def)
	{
		return $this->_get_column_select($layout_def) . "='" . $GLOBALS['db']->quote($layout_def['input_name0']) . "'\n";
	}

	public function queryFilterNot_Equals_Str($layout_def)
	{
		$field_name = $this->_get_column_select($layout_def);
		$input_name0 = $GLOBALS['db']->quote($layout_def['input_name0']);
		return "{$field_name} != '{$input_name0}' OR ({$field_name} IS NULL)\n";
	}

	public function queryFilterContains(&$layout_def)
	{
		return $this->_get_column_select($layout_def) . " LIKE '%" . $GLOBALS['db']->quote($layout_def['input_name0']) . "%'\n";
	}

	public function queryFilterdoes_not_contain(&$layout_def)
	{
		$field_name = $this->_get_column_select($layout_def);
		$input_name0 = $GLOBALS['db']->quote($layout_def['input_name0']);
		return "{$field_name} NOT LIKE '%{$input_name0}%' OR ({$field_name} IS NULL)\n";
	}

	public function queryFilterStarts_With(&$layout_def)
	{
		return $this->_get_column_select($layout_def) . " LIKE '" . $GLOBALS['db']->quote($layout_def['input_name0']) . "%'\n";
	}

	public function queryFilterEnds_With(&$layout_def)
	{
		return $this->_get_column_select($layout_def) . " LIKE '%" . $GLOBALS['db']->quote($layout_def['input_name0']) . "'\n";
	}

	public function queryFilterone_of($layout_def)
	{
		foreach ($layout_def['input_name0'] as $key => $value) {
			$layout_def['input_name0'][$key] = $GLOBALS['db']->quote($value);
		}
		return $this->_get_column_select($layout_def) . " IN ('" . implode("','", $layout_def['input_name0']) . "')\n";
	}

	public function displayInput($layout_def)
	{
		$str = '<input type="text" size="20" value="' . $layout_def['input_name0'] . '" name="' . $layout_def['name'] . '">';
		return $str;
	}

	/**
	 * This is where the magic happens.
	 * @param $layout_def
	 * @return string
	 */
	public function displayListPlain($layout_def)
	{
		$value = $this->_get_list_value($layout_def);
		$displayValue = json_decode(htmlspecialchars_decode($value), true);
		if (isset($layout_def['widget_type']) && $layout_def['widget_type'] == 'checkbox') {
			if ($value != '' && ($value == 'on' || intval($value) == 1 || $value == 'yes')) {
				return "<input name='checkbox_display' class='checkbox' type='checkbox' disabled='true' checked>";
			}
			return "<input name='checkbox_display' class='checkbox' type='checkbox' disabled='true'>";
		}
		if ($layout_def['name'] == 'source') {
			$divID = $layout_def['name'] . '_' . $layout_def['fields']['PRIMARYID'];
			return "<div onclick=\"document.getElementById('{$divID}').style.display='block';\">{$displayValue['subject']['_type']}</div><div id='{$divID}' style='display: none;overflow: hidden;'><pre>" . print_r($displayValue,
					true) . '</pre></div>';
		} else {
			return '<pre>' . print_r($displayValue, true) . '</pre>';
		}
	}
}
