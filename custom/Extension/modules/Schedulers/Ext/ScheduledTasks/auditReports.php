<?php
if (!defined('sugarEntry') || !sugarEntry) {
	die('Not A Valid Entry Point');
}

array_push($job_strings, 'auditReports');

function auditReports()
{
	//Just add all the modules you have added to this list by PARENT MODULE
	$auditedModules = array('Accounts', 'Bugs', 'Cases', 'Contacts', 'IN_Customer_Contacts', 'IN_Customers',
							'IN_Orders', 'Opportunities', 'Quotes', 'Tasks');
	$GLOBALS['log']->fatal('----->Scheduler fired job of type auditReports()');
	foreach ($auditedModules as $auditedModule) {
		$ar = new auditReports($auditedModule);
		$ar->buildAuditSupport(1000);
	}
	$GLOBALS['log']->fatal('----->Scheduler finished job of type auditReports()');
	return true;
}
