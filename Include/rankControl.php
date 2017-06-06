<?php
if (defined ( 'APPPATH' )) {
	require APPPATH.'/Model/rankModel.php';
	require APPPATH.'/Include/smarty/core/Smarty.class.php';
} else {
	die ();
}

class rankControl extends Smarty {
	private static $model = null;
	public function __construct() {
		parent::__construct();
		if (self::$model == null) {
			self::$model = new rankModel ();
		}
	}
	
	public function page() {
		$page = (int)get('id');
		$args = self::$model->getRank($page);
		parent::assign('lists', $args[1]);
		parent::assign('pageMax', $args[0][0]);
        parent::assign('total', $args[0][1]);
		parent::display('ranklist.html');
	}
}
?>
