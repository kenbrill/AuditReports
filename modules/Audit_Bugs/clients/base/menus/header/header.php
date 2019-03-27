<?php
$moduleName = 'Audit_Bugs';
$viewdefs[$moduleName]['base']['menu']['header'] = array(
	array(
		'route'      => "#$moduleName/create",
		'label'      => 'LNK_NEW_RECORD',
		'acl_action' => 'create',
		'acl_module' => $moduleName,
		'icon'       => 'fa-plus',
	),
	array(
		'route'      => "#$moduleName",
		'label'      => 'LNK_LIST',
		'acl_action' => 'list',
		'acl_module' => $moduleName,
		'icon'       => 'fa-bars',
	),
);
