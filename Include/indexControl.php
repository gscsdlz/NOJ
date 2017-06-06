<?php
if(defined('APPPATH')) {
	require APPPATH.'/Include/smarty/core/Smarty.class.php';
} else {
	die();
}
class indexControl extends Smarty{
	public function __construct() {
	    parent::__construct();
	}
	public function index() {
	    parent::display("default.html");
	}
	public function help(){
		parent::display("help.html");
	}
	public function __call($method, $args) {
        parent::assign('errorInfo', 'Invalid Action');
        parent::display('error.html');
	}
}
