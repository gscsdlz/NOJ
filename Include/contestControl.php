<?php
if (defined ( 'APPPATH' )) {
	require APPPATH . '/Model/contestModel.php';
	require_once APPPATH . '/Model/problemModel.php';
	require APPPATH . '/Model/statusModel.php';
	require APPPATH . '/Model/codeModel.php';
	require APPPATH . '/Model/rankModel.php';
	require APPPATH . '/Include/smarty/core/Smarty.class.php';
	require APPPATH . '/Model/askModel.php';
} else {
	die ();
}
class contestControl extends Smarty {
	private static $model = null;
	private static $problemModel = null;
	private static $statusModel = null;
	private static $codeModel = null;
	private static $rankModel = null;
	private static $askModel = null;
	private static $userModel = null;
	public function __construct() {
	    parent::__construct();
		if (self::$model == null) {
			self::$model = new contestModel ();
		}
		if (self::$problemModel == null) {
			self::$problemModel = new problemModel ();
		}
		if (self::$statusModel == null) {
			self::$statusModel = new statusModel ();
		}
		if (self::$codeModel == null) {
			self::$codeModel = new codeModel ();
		}
		if (self::$rankModel == null) {
			self::$rankModel = new rankModel ();
		}
		if (self::$askModel == null) {
			self::$askModel = new askModel ();
		}
	}
	public function page() {
		$args = self::$model->get_lists ();
		parent::assign('lists', $args);
		parent::display( 'contest_list.html');
	}
	public function show() {
		$cid = get ( 'id' );
		$contest = $cid;
		parent::assign('contest', $contest);

		if (isset ( $_SESSION ['user_id'] ))
			$uid = $_SESSION ['user_id'];
		else
			$uid = 0;
		$status = self::$model->privilege_check ( $cid, $uid );
		/**
		 * $status = 0 一切正常开始显示
		 * = 1弹出需要输入密码框
		 * = -1比赛未开始
		 * = -2 比赛权限不足
		 */
		if (isset ( $_SESSION ['privilege'] ) && ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] [$contest] )))
			$status = 0;
		$args= self::$model->get_lists ( $cid );
        parent::assign('problemNavbar','true');
		parent::assign('info', $args[0]);
        parent::assign('title', $args[0]['contest_name']);
		if ($status == 0) { // 检查用户权限以及比赛是否开始
			parent::assign('lists', self::$model->get_problem_list ( $cid ));
			parent::display('contest_problem_list.html');
		} else if ($status == 1) {
            parent::assign('pass', true);
            parent::display('contest_problem_list.html');
		} else if ($status == - 1) {
            parent::assign('timeError', true);
            parent::display('contest_problem_list.html');
		} else {
            parent::assign('privilegeError', true);
            parent::display('contest_problem_list.html');
		}
	}
	public function check() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
			$cid = ( int ) get ( 'id' );
			$password = post ( 'contestpass' );
			if (isset ( $_SESSION ['user_id'] )) {
				$user_id = $_SESSION ['user_id'];
				if (self::$model->check ( $user_id, $cid, $password ))
					echo json_encode ( array (
							'status' => true 
					) );
				else
					echo json_encode ( array (
							'status' => false 
					) );
			}
		}
	}
	public function problem() {

		
		$innerId = get ( 'pid' );
		$contestId = get ( 'id' );
		$contest = $contestId;
        parent::assign('contest', $contest);
		$admin = false;
		if (isset ( $_SESSION ['user_id'] ) && ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] [$contestId] )))
			$admin = true;
		
		if (isset ( $_SESSION ['user_id'] ))
			$uid = $_SESSION ['user_id'];
		else
			$uid = 0;
		if ($admin || self::$model->privilege_check ( $contestId, $uid) == 0) { // 检查比赛是否开始
			$problemId = self::$model->get_real_id ( $innerId, $contestId );
			$body = self::$model->get_problem ( $problemId, $innerId );
			$submits = self::$problemModel->get_submits ( $problemId, $contest );
			
			if ($body) {
				$body ['aSubmit'] = $submits [0];
				$body ['tSubmit'] = $submits [1];
				parent::assign($body);
				parent::assign('problemNavbar','true');
				parent::display('problem.html');
			} else {
                parent::assign('errorInfo', 'Invalid Id');
                parent::display('error.html');
			}
		} else {
            parent::assign('errorInfo', 'Time Error');
            parent::display('error.html');
		}
	}
	public function code() {
		$submit_id = ( int ) get ( 'pid' );
		$contestId = ( int ) get ( 'id' );
		$contest = $contestId;
        parent::assign('contest', $contest);
		$res = self::$codeModel->getCode ( $submit_id, $contest );
        parent::assign($res);
        parent::assign('options', self::$model->get_options($contest));
        global $langArr;
        global $statusArr;
        parent::assign('langArr', $langArr);
        parent::assign('statusArr', $statusArr);
		if ($res) {
            parent::assign('statusNavbar','true');
			parent::display('code.html');
		} else {
            parent::assign('errorInfo', 'Invalid Id');
            parent::display('error.html');
		}
	}
	public function ce() {

		$submit_id = ( int ) get ( 'pid' );
		$contestId = ( int ) get ( 'id' );
		$contest = $contestId;
        parent::assign('contest', $contest);
		$res = self::$codeModel->getCEInfo ( $submit_id, $contest );
        parent::assign($res);
        parent::assign('options', -1);
        global $langArr;
        global $statusArr;
        parent::assign('langArr', $langArr);
        parent::assign('statusArr', $statusArr);
		if ($res) {
            parent::assign('statusNavbar','true');
		    parent::display('code.html');
		} else {
            parent::assign('errorInfo', 'Invalid Id');
            parent::display('error.html');
		}
	}
	public function ranklist() {

		$page = ( int ) get('pid');
		$cid = ( int ) get ( 'id' );
		$team = urldecode(get('string'));
		if($team == 'ALL' || $team == "")
		    $team = -1;
		$contest = $cid;
        parent::assign('contest', $contest);
		parent::assign('ids', self::$model->get_all_inner_id ( $cid ));
		$info = self::$model->get_lists($cid);
        parent::assign('title', $info[0]['contest_name']);
		parent::assign($info[0]);
		if (count($info[0]) != 0) {
		    $args = self::$rankModel->contest_rank ( $cid , $page, $team);
		    parent::assign($args[0]);
            parent::assign('ranks', $args[1]);
            unset($args);
            parent::assign('rankNavbar','true');
            parent::display('contest_ranklist.html');
		} else {
            parent::assign('errorInfo', 'Time Error');
            parent::display('error.html');
		}
	}
	public function asklist() {
		$cid = get ( 'id' );

		$contest = $cid;
        parent::assign('contest', $contest);
		parent::assign('pros', self::$model->get_problem_list ( $cid ));
		parent::assign('lists', self::$askModel->get_list_by_cid ( $cid ));
        parent::assign('askNavbar','true');
		parent::display('contest_asklist.html');
	}
	public function ask() {
		$cid = ( int ) get ( 'id' );
		$topic = ( int ) get ( 'pid' );
		$contest = $cid;
        parent::assign('contest', $contest);
		$args = self::$askModel->get_answer ( $topic );
		parent::assign('info', $args[0]);
		parent::assign('lists', $args[2]);
		unset($args);
        parent::assign('askNavbar','true');
		parent::display('contest_ask.html');
	}
	
	public function get_message() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
			$cid = ( int ) post ( 'cid' );
			$time = time ();
			$status = self::$askModel->get_newMessage ( $cid, $time );
			if ($status == false) {
				echo json_encode ( array (
						'status' => false
				) );
			} else {
				echo json_encode ( array (
						'status' => true,
						'info' => $status
				) );
			}
		}
	}
	
	public function export() {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$cid = (int)post('id');
			$args [] = self::$model->get_all_inner_id ( $cid );
			$args [] = self::$rankModel->contest_rank ( $cid , $page, true);
		}
	}
}