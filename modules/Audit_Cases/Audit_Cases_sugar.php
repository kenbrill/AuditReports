<?php

class Audit_Cases_sugar extends Basic
{
	public $new_schema = true;
	public $module_dir = 'Audit_Cases';
	public $object_name = 'Audit_Cases';
	public $table_name = 'cases_audit';
	public $importable = false;
	public $id;
	public $name;
	public $date_entered;
	public $created_by;
	public $created_by_name;
	public $deleted;
	public $created_by_link;
	public $field_name;
	public $data_type;
	public $before_value_text;
	public $after_value_text;
	public $before_value_string;
	public $after_value_string;
	public $event_id;
	public $event_id_link;
	public $disable_row_level_security = true;
	public $disable_custom_fields = true;

	public function bean_implements($interface)
	{
		switch ($interface) {
			case 'ACL':
				return true;
		}
		return false;
	}

}
