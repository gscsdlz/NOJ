<?php
if(defined('APPPATH')) {
	require_once APPPATH.'/Model/codeModel.php';
	require_once APPPATH.'/Include/smarty/core/Smarty.class.php';
} else {
	die ();
}
class codeControl extends Smarty {
	private static $model = null;
	public function __construct() {
	    parent::__construct();
		if(self::$model == null) {
			self::$model = new codeModel();
		}
	}
	
	public function show() {
		$submit_id = (int)get("id");
		$res = self::$model->getCode($submit_id);
		parent::assign($res);
		parent::assign('options', 0);
		global $langArr;
		global $statusArr;
		parent::assign('langArr', $langArr);
		parent::assign('statusArr', $statusArr);
		if($res) {
			parent::display('code.html');
		} else {
            parent::assign('errorInfo', 'Invalid Id');
            parent::display('error.html');
		}
		
	}
	
	public function ce() {
		$submit_id = (int)get("id");
		$res = self::$model->getCEInfo($submit_id);
        parent::assign($res);
        parent::assign('options', 0);
        global $langArr;
        global $statusArr;
        parent::assign('langArr', $langArr);
        parent::assign('statusArr', $statusArr);
		if($res) {
            parent::display('code.html');
		} else {
            parent::assign('errorInfo', 'Invalid Id');
            parent::display('error.html');
		}
	}
}
?>