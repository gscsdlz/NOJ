<?php

class userModel extends DB {
	public function __construct() {
		parent::__construct ();
	}
	public function getId($username) {
		$result = parent::query_one ( "SELECT user_id FROM users WHERE username = ? LIMIT 1", array($username ));
		if($result)
			return $result[0];
		else
			return -1;
	}
	
	public function getStatus($user_id) {
		$result = parent::query ( "SELECT status, count(*) FROM `status` where user_id = ? AND contest_id = 0 GROUP BY (status)", array($user_id ));
		$arr = array (
				0,
				0,
				0,
				0,
				0,
				0,
				0,
				0 
		);
		if ($result->rowCount () != 0) {
			while ( $row = $result->fetch ( PDO::FETCH_NUM ) ) {
					$arr[$row[0] - 4] = $row [1];
			}
		}
		return $arr;
	}
	
	public function get_ac_problem($user_id) {
		$result = parent::query("SELECT DISTINCT pro_id FROM status WHERE user_id = ? AND status = 4 AND contest_id=0", array($user_id));
		$arr = null;
		while($row = $result->fetch(PDO::FETCH_NUM)) {
			$arr[] = $row[0];
		}
		if($arr)
			sort($arr);
		return $arr;
	}
	
	public function get_nac_problem($user_id) {
		$result = parent::query("SELECT DISTINCT pro_id FROM status WHERE user_id = ? AND status != 4 AND contest_id = 0 AND pro_id NOT IN (SELECT DISTINCT pro_id FROM status WHERE user_id = ? AND status = 4)", array($user_id, $user_id));
		$arr = null;
		while($row = $result->fetch(PDO::FETCH_NUM)) {
			$arr[] = $row[0];
		}
		if($arr)
			sort($arr);
		return $arr;
	}
	
	public function get_user_info($user_id) {
		$result = parent::query("SELECT * FROM `users` lEFT JOIN `team` ON `team`.group_id = `users`.group_id WHERE user_id = ?", array($user_id));
		
		return $result->fetch(PDO::FETCH_NAMED);
	}
	
	/**
	 * 
	 * @param unknown $user_id
	 * 获取用户参与过的比赛的信息，过题数
	 * @return 如上
	 */
	public function get_contest_info($user_id){
		$result = parent::query("SELECT contest_name, COUNT(pro_id), contest.contest_id FROM contest INNER JOIN status ON (contest.contest_id = status.contest_id) WHERE status.user_id = ? GROUP BY status.contest_id", array($user_id));
		if($result->rowCount() != 0) {
			while($row = $result->fetch(PDO::FETCH_NUM)){
				$res = parent::query_one("SELECT COUNT(DISTINCT pro_id) FROM  status WHERE status.user_id = ? AND status = 4 AND contest_id = ?", array($user_id, $row[2]));
				$row[1] = $res[0];
				$args[] = $row;
			}
			return $args;
		} else {
			return null;
		}
	}
	/**
	 * 获取小组的所有信息
	 * @return mixed
	 */
	public function get_group_info() {
		$result = parent::query("SELECT * FROM `team`");
		while($row = $result->fetch(PDO::FETCH_NUM)){
			$arg[] = $row;
		}
		return $arg;
	}
	
	public function save_filename($filename, $user_id) {
		parent::query("UPDATE users SET headerpath = ? WHERE user_id = ?", array($filename, $user_id));
		return true;
	}
	
	public function get_filename($user_id) {
		$res = parent::query_one("SELECT headerpath FROM users WHERE user_id = ?", array($user_id));
		return $res[0];
	}
}
?>