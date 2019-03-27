<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

$viewdefs['Audit_Quotes']['base']['filter']['basic'] = array(
	'create'                  => true,
	'quicksearch_field'       => array('field_name'),
	'quicksearch_priority'    => 1,
	'quicksearch_split_terms' => false,
	'filters'                 => array(
		array(
			'id'                => 'all_records',
			'name'              => 'LBL_LISTVIEW_FILTER_ALL',
			'filter_definition' => array(),
			'editable'          => false
		),
	),
);
