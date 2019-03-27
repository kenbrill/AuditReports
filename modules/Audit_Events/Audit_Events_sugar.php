<?php

class Audit_Events_sugar extends Basic
{
	public $new_schema = true;
	public $module_dir = 'Audit_Events';
	public $object_name = 'Audit_Events';
	public $table_name = 'audit_events';
	public $importable = false;
	public $id;
	public $type;
	public $parent_id;
	public $module_name;
	public $source;
	public $data;
	public $date_created;
	public $deleted;
	public $disable_custom_fields = true;
	public $disable_row_level_security = true;

	public function bean_implements($interface)
	{
		switch ($interface) {
			case 'ACL':
				return true;
		}
		return false;
	}

}
