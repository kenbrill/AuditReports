<?php
//Define the parent module here, We need both plural and singular (biggest
// mistake SugarCRM ever made) because of Opportunities mostly.  By changing these two
// values we can use this same vardefs for any module with an audit table
$module = 'Quotes';
$moduleSingular = 'Quote';

$dictionary['Audit_' . $module] = array(
	'table'              => strtolower($module) . '_audit',
	'audited'            => false,
	'activity_enabled'   => false,
	'duplicate_merge'    => false,
	'fields'             => array(
		'id'                  => array(
			'name'       => 'id',
			'type'       => 'id',
			'reportable' => false,
		),
		'date_created'        => array(
			'name'  => 'date_created',
			'vname' => 'LBL_DATE_ENTERED',
			'type'  => 'datetime',
		),
		//These two fields define the relationship between the audit table
		// and the parent module.
		'parent_id'           => array(
			'name'       => 'parent_id',
			'type'       => 'id',
			'reportable' => false,  //We don't need to report on the ID
			'required'   => true,
		),
		'parent_name'         =>
			array(
				'name'         => 'parent_name',
				'type'         => 'link',
				'relationship' => 'audits_' . $module . '_parent',
				'vname'        => 'LBL_PARENT_NAME',
				'link_type'    => 'one',
				'module'       => $module,
				'bean_name'    => $moduleSingular,
				'source'       => 'non-db',
			),
		//These two fields define the relationship between the audit table
		// and the user that is being audited.
		'created_by'          => array(
			'name'       => 'created_by',
			'vname'      => 'LBL_CREATED',
			'reportable' => false,  //We don't need to report on the ID
			'type'       => 'id',
		),
		'created_by_link'     =>
			array(
				'name'         => 'created_by_link',
				'type'         => 'link',
				'relationship' => 'Audit_' . $module . '_created_by',
				'vname'        => 'LBL_CREATED_BY_USER',
				'link_type'    => 'one',
				'module'       => 'Users',
				'bean_name'    => 'User',
				'source'       => 'non-db',
			),
		//These two fields define the relationship between the audit table
		// and the event that created the audit trail.
		'event_id'            => array(
			'name'       => 'event_id',
			'vname'      => 'LBL_EVENT_ID',
			'type'       => 'id',
			'reportable' => false,  //We don't need to report on the ID
		),
		'event_id_link'       =>
			array(
				'name'         => 'event_id_link',
				'type'         => 'link',
				'relationship' => 'Audit_' . $module . '_event',
				'vname'        => 'LBL_EVENT_TEXT',
				'link_type'    => 'one',
				'module'       => 'Audit_Events', //This ties to our new module
				'bean_name'    => 'Audit_Events',
				'source'       => 'non-db',
			),
		//For this field I made it an ENUM and I have code that created the
		// language file for it once a day
		'field_name'          => array(
			'name'     => 'field_name',
			'vname'    => 'LBL_FIELD_NAME',
			'type'     => 'enum',
			'len'      => 100,
			'options'  => $module . '_field_name_list',
			'required' => true,
		),
		'data_type'           => array(
			'name'     => 'data_type',
			'type'     => 'varchar',
			'len'      => 100,
			'vname'    => 'LBL_DATA_TYPE',
			'required' => true,
		),
		//These two fields will always contain the translated (English in my case) data
		// from the before_value_string or after_value_string fields.  If the fieldtype is
		// TEXT then this field will already contain the TEXT that was entered in the field and
		// thus needs no translation
		'before_value_text'   => array(
			'name'  => 'before_value_text',
			'type'  => 'text',
			'vname' => 'LBL_BEFORE_TEXT',
		),
		'after_value_text'    => array(
			'name'  => 'after_value_text',
			'vname' => 'LBL_AFTER_TEXT',
			'type'  => 'text',
		),
		//These two fields will always contain the Database keys for the
		// data audited
		'before_value_string' => array(
			'name'  => 'before_value_string',
			'type'  => 'varchar',
			'len'   => 255,
			'vname' => 'LBL_BEFORE_VALUE',
		),
		'after_value_string'  => array(
			'name'  => 'after_value_string',
			'vname' => 'LBL_AFTER_VALUE',
			'type'  => 'varchar',
			'len'   => 255,
		),
		//Every module requires a delete field or reports will fail with a SQL error
		'deleted'             => array(
			'name'       => 'deleted',
			'vname'      => 'LBL_DELETED',
			'type'       => 'bool',
			'required'   => true,
			'default'    => '0',
			'reportable' => false,  //We don't need to report on the Deleted flag
		),
	),
	'indices'            => array(
		array('name' => 'pk', 'type' => 'primary', 'fields' => array('id')),
		array('name' => 'parent_id', 'type' => 'index', 'fields' => array('parent_id')),
		array('name' => 'event_id', 'type' => 'index', 'fields' => array('event_id')),
		array('name' => 'pa_ev_id', 'type' => 'index', 'fields' => array('parent_id', 'event_id')),
		array('name' => 'after_value', 'type' => 'index', 'fields' => array('after_value_string')),
	),

	//Every audit table has 3 relationships.
	'relationships'      => array(
		'Audit_' . $module . '_created_by' => array(
			'lhs_module'        => 'Users',
			'lhs_table'         => 'users',
			'lhs_key'           => 'id',
			'rhs_module'        => 'Audit_' . $module,
			'rhs_table'         => strtolower($module) . '_audit',
			'rhs_key'           => 'created_by',
			'relationship_type' => 'one-to-many'
		),
		'Audit_' . $module . '_event'      => array(
			'lhs_module'        => 'Audit_Events',
			'lhs_table'         => 'audit_events',
			'lhs_key'           => 'id',
			'rhs_module'        => 'Audit_' . $module,
			'rhs_table'         => strtolower($module) . '_audit',
			'rhs_key'           => 'event_id',
			'relationship_type' => 'one-to-many'
		),
		'audits_' . $module . '_parent'    => array(
			'lhs_module'        => $module,
			'lhs_table'         => strtolower($module),
			'lhs_key'           => 'id',
			'rhs_module'        => 'Audit_' . $module,
			'rhs_table'         => strtolower($module) . '_audit',
			'rhs_key'           => 'parent_id',
			'relationship_type' => 'one-to-many'
		),
	),
	//We don't need any of this stuff
	'optimistic_locking' => false,
	'unified_search'     => false,
	'full_text_search'   => false,
);
