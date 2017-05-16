<?php
if (defined ( 'APPPATH' )) {
	require APPPATH . '/Model/askModel.php';
} else {
	die ();
}
class askControl {
	private static $model = null;
	public function __construct() {
		if (self::$model == null) {
			self::$model = new askModel ();
		}
	}
	
	public function submit_question() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] )) {
			$pro_id = ( int ) post ( 'pro_id' );
			$topic = post ( 'topic' );
			$user_id = $_SESSION ['user_id'];
			$cid = ( int ) post ( 'contest' );
			$publicMessage = 0;
			if ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] [$cid] )) { // root
				$publicMessage = 1;
			}
			if ($topic) {
				$staus = self::$model->put_question ( $pro_id, $user_id, $topic, $publicMessage, $cid );
				if ($staus) {
					echo json_encode ( array (
							'status' => true 
					) );
					return;
				}
			}
		}
		echo json_encode ( array (
				'status' => false 
		) );
	}
	public function submit_answer() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] )) {
			$topic = post ( 'answer' );
			$user_id = $_SESSION ['user_id'];
			$qid = ( int ) post ( 'question_id' );
			if ($topic) {
				$staus = self::$model->put_answer ( $qid, $user_id, $topic );
				if ($staus) {
					echo json_encode ( array (
							'status' => true 
					) );
					return;
				}
			}
		}
		echo json_encode ( array (
				'status' => false 
		) );
	}
	public function delete_question() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] )) {
			$askid = ( int ) post ( 'question_id' );
			$user_id = $_SESSION ['user_id'];
			$privilege = $_SESSION ['privilege'];
			$cid = ( int ) post ( 'cid' );
			if (self::$model->delete_question ( $askid, $user_id, $cid, $privilege )) {
				echo json_encode ( array (
						'status' => true 
				) );
				return;
			}
		}
		echo json_encode ( array (
				'status' => false 
		) );
	}
	public function delete_answer() {
		if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset ( $_SESSION ['user_id'] )) {
			$askid = ( int ) post ( 'answer_id' );
			$user_id = $_SESSION ['user_id'];
			$privilege = $_SESSION ['privilege'];
			global $contest;
			if (self::$model->delete_answer ( $askid, $user_id, $contest, $privilege )) {
				echo json_encode ( array (
						'status' => true 
				) );
				return;
			}
		}
		echo json_encode ( array (
				'status' => false 
		) );
	}
}
?>