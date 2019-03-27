<?PHP
require_once('modules/Audit_Accounts/Audit_Accounts_sugar.php');

class Audit_Accounts extends Audit_Accounts_sugar
{
	static $already_ran = false;
	//Set this to the name of the parent module
	public $module = 'Accounts';

	public function __construct()
	{
		parent::__construct();
		// - When you run a report it instantiates this bean several times, we only need
		//    to run this code once.
		// - The action 'BuildReportModuleTree' only happens when you run a report
		//    so this will keep this code from running UNLESS you are actually
		//    running a report
		if ((isset(self::$already_ran) && self::$already_ran == true) ||
			$_REQUEST['action'] != 'BuildReportModuleTree') {
			return;
		}
		self::$already_ran = true;
		//This code updates the language file and updates any records in the audit
		// table that do not have translated values in the 'text' fields.
		$ar = new auditReports($this->module);
		$ar->buildAuditSupport();
	}
}
