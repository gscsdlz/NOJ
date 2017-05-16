<?php
if (defined ( 'APPPATH' )) {
	require APPPATH . '/admin/aModel/aProblemModel.php';
	require APPPATH . '/admin/aInclude/dataFile.class.php';
} else {
	die ( 'problemMControl' );
}
class problemMControl {
	private static $model = null;
	public function __construct() {
		if (self::$model == null) {
			self::$model = new aProblemModel ();
		}
	}
	public function page() {
		if (isset ( $_SESSION ['user_id'] ) && $_SESSION ['privilege'] [0] == 1) {
			$pageId = get ( 'id' );
			if (! $pageId)
				$pageId = 0;
			$_GET ['pageid'] = $pageId; // 这里重新设置ID的意义在于 @problem_list:4 需要通过读取GET数组确定分页单元的显示
			$lists = self::$model->get_list ( $pageId );
			if ($lists)
				aVIEW::loopshow ( 'problem_list', $lists );
			else
				aVIEW::show ( 'error', array (
						'errorInfo' => 'Invalid Id' 
				) );
		} else {
			aVIEW::show ( 'error', array (
					'errorInfo' => 'Admin Error' 
			) );
		}
	}
	public function del_problem() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] ) && $_SESSION ['privilege'] [0] == 1) {
			$id = ( int ) post ( 'pro_id' );
			$status = self::$model->del_problem ( $id );
			if ($status)
				echo json_encode ( array (
						'status' => true 
				) );
			else
				echo json_encode ( array (
						'status' => '删除失败' 
				) );
		}
	}
	public function show_problem() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] ) && $_SESSION ['privilege'] [0] == 1) {
			$id = ( int ) post ( 'id' );
			$status = self::$model->set_problem ( $id, 1 );
			if ($status)
				echo json_encode ( array (
						'status' => true 
				) );
		}
	}
	public function hide_problem() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] ) && $_SESSION ['privilege'] [0] == 1) {
			$id = ( int ) post ( 'id' );
			$status = self::$model->set_problem ( $id, 0 );
			if ($status)
				echo json_encode ( array (
						'status' => true 
				) );
		}
	}
	public function hidden() {
		$lists = self::$model->get_all_hidden ();
		aVIEW::loopshow ( 'problem_list', $lists );
	}
	public function edit() {
		if (isset ( $_SESSION ['user_id'] ) && $_SESSION ['privilege'] [0] == 1) {
			$id = ( int ) get ( 'id' );
			$body = array ();
			if ($id != 0) { // 不是新增题目
				$args [] = self::$model->get_problem ( $id );
				if ($args) {
					$args [] = get_dir_name ( $id );
					aVIEW::loopshow ( 'problem_edit', $args );
				} else
					aVIEW::show ( 'error', array (
							'errorInfo' => 'Invalid Id' 
					) );
			} else {
				aVIEW::loopshow ( 'problem_edit', array () );
			}
		} else {
			aVIEW::show ( 'error', array (
					'errorInfo' => 'Admin Error' 
			) );
		}
	}
	public function editPost() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] ) && $_SESSION ['privilege'] [0] == 1) {
			$id = ( int ) post ( 'id' );
			$body = self::$model->get_problem ( $id );
			echo json_encode ( $body );
		}
	}
	public function savePro() {
		if (isset ( $_SESSION ['user_id'] ) && $_SESSION ['privilege'] [0] == 1) {
			$pro_id = ( int ) post ( 'pro_id' );
			$pro_title = post ( 'pro_title' );
			$time_limit = ( int ) post ( 'time_limit' );
			$memory_limit = ( int ) post ( 'memory_limit' );
			$pro_descrip = post ( 'pro_descrip' );
			$pro_in = post ( 'pro_in' );
			$pro_out = post ( 'pro_out' );
			$pro_dataIn = post ( 'pro_dataIn' );
			$pro_dataOut = post ( 'pro_dataOut' );
			$hint = post ( 'hint' );
			$author = post ( 'author' );
			
			if ($pro_id == 0) {
				$status = self::$model->insert_pro ( $pro_title, $time_limit, $memory_limit, $pro_descrip, $pro_in, $pro_out, $pro_dataIn, $pro_dataOut, $hint, $author );
				if ($status) {
					echo json_encode ( array (
							'status' => true,
							'pro_id' => self::$model->get_maxId () 
					) );
				}
			} else {
				$status = self::$model->update_pro ( $pro_id, $pro_title, $time_limit, $memory_limit, $pro_descrip, $pro_in, $pro_out, $pro_dataIn, $pro_dataOut, $hint, $author );
				if ($status) {
					echo json_encode ( array (
							'status' => '更新成功' 
					) );
				} else {
					echo json_encode ( array (
							'status' => '更新失败，请重试' 
					) );
				}
			}
		}
	}
	public function del_file() {
		if (isset ( $_SESSION ['user_id'] ) && $_SESSION ['privilege'] [0] == 1) {
			$pro_id = ( int ) post ( 'pro_id' );
			$filename = post ( 'filename' );
			$info = delete_file ( $pro_id, $filename );
			if ($info) {
				echo json_encode ( array (
						'status' => true,
						'info' => $info 
				) );
			} else {
				echo json_encode ( array (
						'status' => false 
				) );
			}
		}
	}
	public function download() {
		if (isset ( $_SESSION ['user_id'] ) && $_SESSION ['privilege'] [0] == 1) {
			$pro_id = ( int ) get ( 'pro_id' );
			$filename = get ( 'filename' );
			download_file ( $pro_id, $filename );
		}
	}
	public function upload() {
		if (isset ( $_SESSION ['user_id'] ) && $_SESSION ['privilege'] [0] == 1) {
			$allowType = array (
					"in",
					"out" 
			);
			if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
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
					if ($file ['error'] == 0 && in_array ( $fileinfo , $allowType ) ) {
						$filename = $file ['name'];
						
						$status = upload_file ( get('id'), $filename , $file['tmp_name']);
						if ($status) {
							echo json_encode ( array (
									'filename' => $filename 
							) );
							return;
						}
					}
				}
			}
		}
		echo json_encode ( array (
				'status' => false 
		) );
	}
}