<?php
if (defined ( 'APPPATH' )) {
	require APPPATH . '/Model/userModel.php';
	require APPPATH . '/Model/rankModel.php';
	require APPPATH . '/Include/smarty/core/Smarty.class.php';
} else {
	die ();
}
class userControl extends Smarty {
	private static $model = null;
	private static $rankmodel = null;
	public function __construct() {
	    parent::__construct();
		if (self::$model == null) {
			self::$model = new userModel ();
		}
		if (self::$rankmodel == null) {
			self::$rankmodel = new rankModel ();
		}
	}
	public function get_ip_addr() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] )) {
			$uid = ( int ) post ( 'uid' );
			if ($uid == $_SESSION ['user_id']) {
				$ip_addr = post ( 'ip_addr' );
				echo json_encode ( array (
						'info' => get_ip_location ( $ip_addr ) 
				) );
			}
		}
	}
	public function show() {
		$username = urldecode(get ( 'id' ));
		$user_id = self::$model->getId ( $username );
		$arg = self::$model->getStatus ( $user_id );
		parent::assign('status', $arg);
		$arg = self::$model->get_ac_problem ( $user_id );
		parent::assign('ac_pros', $arg);
		$arg = self::$model->get_nac_problem ( $user_id );
		parent::assign('wa_pros', $arg);
		$userinfo = self::$model->get_user_info ( $user_id );
		parent::assign($userinfo);
		$arg = self::$model->get_contest_info ( $user_id );
		parent::assign('contests',$arg);
		$arg = self::$model->get_group_info ();
		parent::assign('groups', $arg);
		$arg = self::$rankmodel->getRank ( 0, $user_id );
		parent::assign('ranks', $arg);
		if ($user_id > 0) {
			parent::display('user.html');
		}
		else
        {
            parent::assign('errorInfo', 'Invalid User');
            parent::display('error.html');
        }

	}
	public function uploadHeader() {
		$allowType = array (
				"jpg",
				"png",
				"gif" 
		);
		$allowMIME = array (
				"image/png",
				"image/jpeg",
				"image/gif" 
		);
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] )) {
			if (isset ( $_FILES ['file'] )) {
				$file = $_FILES ['file'];
				/**
				 * error {
				 * 0 成功
				 * 1超过upload_max_filesize
				 * 2超过MAX_FILE_SIZE
				 * 3部分上传
				 * 4无文件
				 * }
				 */
				$fileinfos = explode ( '.', $file ['name'] );
				$fileinfo = $fileinfos [1];
				if ($file ['error'] == 0 && in_array ( strtolower ( $fileinfo ), $allowType ) && in_array ( $file ['type'], $allowMIME )) {
					$filename = $_SESSION ['user_id'] . time () . rand ( 0, 255 ) . '.' . $fileinfo;
					$oldfile = self::$model->get_filename ( $_SESSION ['user_id'] );
					if (move_uploaded_file ( $file ['tmp_name'], APPPATH . '/Src/Image/header/' . $filename )) {
						if ($oldfile != 'default.jpg') {
							unlink ( APPPATH . '/Src/Image/header/' . $oldfile );
						}
						self::$model->save_filename ( $filename, $_SESSION ['user_id'] );
						echo json_encode ( array (
								'status' => $filename 
						) );
						
					}
				}
			}
		} else {
			echo json_encode ( array (
					'status' => false 
			) );
		}
	}
}
