<?php
if (defined ( 'APPPATH' )) {
	require APPPATH . '/Model/statusModel.php';
	require APPPATH . '/Include/smarty/core/Smarty.class.php';
} else {
	die ();
}
class statusControl extends Smarty {
	private static $model = null;
	public function __construct() {
	    parent::__construct();
		if (self::$model == null) {
			self::$model = new statusModel ();
		}
	}
	public function __call($method, $args) {
        parent::assign('errorInfo', 'Invalid Action');
        parent::display('error.html');
	}
	public function index() {
	    $contest = 0;
		$cid = (int) get('cid');
		$pro_id = ( int ) get ( 'pid' );
		if($cid) {
			$contest = $cid;
			if($pro_id > 0)
				$pro_id = self::$model->get_real_id($pro_id, $cid);
		}
		$submit_id = ( int ) get ( 'rid' );
		
		$username = get ( 'Programmer' );
		$lang = ( int ) get ( 'lang' );
		$status = ( int ) get ( 'status' );
		$start = ( int ) get ( 'start' );
		$end = ( int ) get ( 'end' );
		$results = self::$model->getStatus ( $submit_id, $pro_id, $username, $lang, $status, $start, $end, $cid);
		global $langArr;
		global $statusArr;
		parent::assign('langArr', $langArr);
		parent::assign('statusArr', $statusArr);
		parent::assign('contest', $contest);
		if($contest){
            parent::assign('statusNavbar', true);
        }
		parent::assign('lists', $results);
		parent::display('status.html');
	}
}
