<?php
if (defined ( 'APPPATH' )) {
	require APPPATH . '/admin/aModel/aContestModel.php';
	require APPPATH . '/admin/aModel/aUserModel.php';
	require APPPATH . '/Model/codeModel.php';
} else {
	die ( 'contestMControl' );
}
class contestMControl {
	private static $model = null;
	private static $userModel = null;
	private static $codeModel = null;
	public function __construct() {
		if (self::$model == null) {
			self::$model = new aContestModel ();
		}
		if (self::$userModel == null) {
			self::$userModel = new aUserModel ();
		}
		if (self::$codeModel == null) {
			self::$codeModel = new codeModel ();
		}
	}
	public function page() {
		if (isset ( $_SESSION ['user_id'] ) && ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] ))) {
			$args = self::$model->get_lists ();
			aVIEW::loopshow ( 'contest_list', $args );
		} else {
			aVIEW::show ( 'error', array (
					'errorInfo' => 'Admin Error' 
			) );
		}
	}
	public function del_contest() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] ) && $_SESSION ['privilege'] [0] == 1) {
			$cid = ( int ) post ( 'contestId' );
			$status = self::$model->del_contest ( $cid );
			if ($status == 1)
				echo json_encode ( array (
						'status' => true 
				) );
			else
				echo json_encode ( array (
						'status' => false,
						'info' => '删除失败' 
				) );
		}
	}
	public function edit() {
		$cid = ( int ) get ( 'id' );
		
		if ($cid == 0) {
			if (isset ( $_SESSION ['user_id'] ) && $_SESSION ['privilege'] [0] == 1) {
				aVIEW::loopshow ( 'contest_edit', array () );
			} else {
				aVIEW::show ( 'error', array (
						'errorInfo' => 'Admin Error' 
				) );
			}
		} else {
			if (isset ( $_SESSION ['user_id'] ) && ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] [$cid] ))) {
				$args [] = self::$model->get_lists ( $cid );
				$args [] = self::$model->get_problem_list ( $cid, - 2 );
				$args [] = self::$userModel->get_group_info ();
				$args [] = self::$model->get_users ( $cid );
				aVIEW::loopshow ( 'contest_edit', $args );
			} else {
				aVIEW::show ( 'error', array (
						'errorInfo' => 'Admin Error' 
				) );
			}
		}
	}
	public function pro_check() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] )) {
			$pro_id = ( int ) post ( 'pro_id' );
			$args = self::$model->get_problem_info ( $pro_id );
			if ($args) {
				echo json_encode ( array (
						'status' => true,
						'pro_title' => $args 
				) );
				return;
			}
		}
		echo json_encode ( array (
				'status' => false 
		) );
	}
	public function save() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] )) {
			$cid = ( int ) post ( "contest_id" );
			$contest_name = post ( 'contest_name' );
			$username = post ( 'username' );
			$c_stime = ( int ) post ( 'c_stime' );
			$c_etime = ( int ) post ( 'c_etime' );
			$contest_pass = post ( 'contest_pass' );
			$prolist = post ( 'prolist' );
			$auNum = (int)post('auNum');
            $agNum = (int)post('agNum');
            $cuNum = (int)post('cuNum');
            $feNum = (int)post('feNum');
            $options = (int)post('options');
			$info = '';
			if (! strlen ( $contest_name ))
				$info .= '比赛名称为空<br/>';
			if (! strlen ( $username ) || ! self::$model->check_user ( $username ))
				$info .= '比赛管理员为空或者非法<br/>';
			if (! (strlen ( $contest_pass ) >= 6 || $contest_pass == '1' || $contest_pass == ' 2'))
				$info .= '比赛权限没有设置正确请检查<br/>';
			if ($c_stime < '1407064249' || $c_etime < $c_stime)
				$info .= '比赛时间不正确<br/>';
			if ($info == '') {
				if ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] [$cid] )) {
					$status = self::$model->add_problem ( $prolist, $cid, $contest_name, $username, $contest_pass, $c_stime, $c_etime, $auNum, $agNum, $cuNum, $feNum, $options );
					if ($status > 0)
						echo json_encode ( array (
								'status' => true,
								'contest_id' => $status 
						) );
					else
						echo json_encode ( array (
								'status' => false,
								'info' => '修改失败' 
						) );
				} else {
					echo json_encode ( array (
							'status' => false,
							'info' => 'Privilege Error' 
					) );
				}
			} else {
				echo json_encode ( array (
						'status' => false,
						'info' => $info 
				) );
			}
		}
	}
	public function save_user() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
			$cid = ( int ) post ( 'cid' );
			if ($cid) {
				if (isset ( $_SESSION ['username'] ) && ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] [$cid] ))) {
					$userlist = post ( 'users' );
					$status = self::$model->save_users ( $cid, $userlist );
					if ($status)
						echo json_encode ( array (
								'status' => true 
						) );
					else
						echo json_encode ( array (
								'status' => false,
								'info' => '' 
						) );
				}
			}
		}
	}
	public function get_balloon() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
			$cid = ( int ) post ( 'cid' );
			if ($cid != 0 && isset ( $_SESSION ['user_id'] ) && ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] [$cid] ))) {
				$args = self::$model->balloon ( $cid );
				if ($args)
					echo json_encode ( array (
							'status' => true,
							'info' => $args 
					) );
				else
					echo json_encode ( array (
							'status' => false 
					) );
			}
		}
	}
	public function send_balloon() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
			$cid = ( int ) post ( 'cid' );
			if ($cid != 0 && isset ( $_SESSION ['user_id'] ) && ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] [$cid] ))) {
				$submit_id = ( int ) post ( 'sid' );
				$args = self::$model->setballoon ( $submit_id );
				if ($args == 1)
					echo json_encode ( array (
							'status' => true 
					) );
				else
					echo json_encode ( array (
							'status' => false,
							'info' => '操作失败，请重试' 
					) );
			}
		}
	}
	public function rejudge() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] )) {
			$cid = ( int ) post ( 'cid' );
			if ($cid != 0 && ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] [$cid] ))) {
				$proid = ( int ) post ( 'pro_id' );
				$proid = self::$model->get_real_Id ( $proid, $cid );
				$username = post ( 'username' );
				$user_id = self::$userModel->getId ( $username );
				$submit_id = ( int ) post ( 'submit_id' );
				$rejudgeall = post ( 'rejudgeall' );
				$status = self::$model->rejudge ( $cid, $proid, $user_id, $submit_id, $rejudgeall );
				echo json_encode ( array (
						'status' => $status 
				) );
			}
		}
	}
	public function sim() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] )) {
			$cid = ( int ) post ( 'cid' );
			if ($cid != 0 && ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] [$cid] ))) {
				
				$args = self::$model->get_sim ( $cid );
				echo json_encode ( array (
						'status' => true,
						'info' => $args 
				) );
			}
		}
	}
	public function get_code() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] )) {
			$cid = ( int ) post ( 'cid' );
			if ($cid != 0 && ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] [$cid] ))) {
				global $langArr;
				$sid1 = ( int ) post ( 'sid1' );
				$sid2 = ( int ) post ( 'sid2' );
				$arg = self::$codeModel->getCode ( $sid1, $cid );
				$args ['code1'] = htmlspecialchars ( $arg ['code'] );
				$args ['username1'] = $arg ['username'] . '(' . $arg ['nickname'] . ')';
				$args ['run_time1'] = $arg ['run_time'];
				$args ['run_memory1'] = $arg ['run_memory'];
				$args ['submit_time1'] = date("Y-m-d H:i:s", $arg['submit_time']);
				$args ['pro_id1'] = $arg ['pro_id'];
				$args ['submit_id1'] = $arg ['submit_id'];
				$args ['status1'] = $arg ['status'];
				$args ['style1'] = $arg ['lang'] == 2 ? 'cpp' : $langArr [$arg ['lang']];
				$arg = self::$codeModel->getCode ( $sid2, $cid );
				$args ['username2'] = $arg ['username'] . '(' . $arg ['nickname'] . ')';
				$args ['run_time2'] = $arg ['run_time'];
				$args ['run_memory2'] = $arg ['run_memory'];
				$args ['submit_time2'] = date("Y-m-d H:i:s", $arg['submit_time']);
				$args ['pro_id2'] = $arg ['pro_id'];
				$args ['code2'] = htmlspecialchars ( $arg ['code'] );
				$args ['submit_id2'] = $arg ['submit_id'];
				$args ['status2'] = $arg ['status'];
				$args ['style2'] = $arg ['lang'] == 2 ? 'cpp' : $langArr [$arg ['lang']];
				
				echo json_encode ( array (
						'status' => true,
						'info' => $args 
				) );
			}
		}
	}
	public function mis_sim() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] )) {
			$cid = ( int ) post ( 'cid' );
			if ($cid != 0 && ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] [$cid] ))) {
				$sid1 = ( int ) post ( 'sid1' );
				$sid2 = ( int ) post ( 'sid2' );
				$status = self::$model->fix_mis_sim ( $sid1, $sid2 );
				echo json_encode ( array (
						'status' => $status 
				) );
			}
		}
	}
	
	public function do_sim() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] )) {
			$cid = ( int ) post ( 'cid' );
			if ($cid != 0 && ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] [$cid] ))) {
				$sid = ( int ) post ( 'sid' );
				$status = self::$model->do_cheating( $sid, $cid );
				if($status != 0)
				echo json_encode ( array (
						'status' => true
				) );
			}
		}
	}
}