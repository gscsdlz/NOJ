<?php
class submitModel extends DB {
	public function __construct() {
		parent::__construct ();
	}
	public function get_real_id($pro_id, $contestId) {
		$res = parent::query_one ( "SELECT pro_id FROM contest_pro WHERE contest_id = ? AND inner_id = ?", array (
				$contestId,
				$pro_id 
		) );
		if ($res) {
			return $res [0];
		} else {
			return - 1;
		}
	}
	/**
	 * 该函数用于检查当前的比赛提交是否满足时间要求
	 * 返回值说明
	 *
	 * @param unknown $contestId        	
	 * @return boolean
	 */
	public function timeCheck($contestId) {
		$timeNow = time ();
		$times = parent::query ( "SELECT c_stime, c_etime FROM contest WHERE contest_id = ? ", array (
				$contestId 
		) );
		if ($times->rowCount () != 0) {
			$row = $times->fetch ( PDO::FETCH_NUM );
			if ($timeNow < $row [0])
				return false;
			if ($timeNow > $row [1])
				return false;
			return true;
		}
		return false;
	}
	public function insertCode($user_id, $pro_id, $lang, $codes, $contestId) {
		if ($contestId) {
			if (! $this->timeCheck ( $contestId )) {
				return false;
			}
			$pro_id = $this->get_real_id ( $pro_id, $contestId );
		}
		$result = parent::query_one ( "SELECT MAX(pro_id) FROM problem" );
		$maxId = $result [0];
		if ($pro_id > $maxId || $pro_id < 1000)
			return false;
		global $langArr;
		if ($lang <= 0 || $lang >= count ( $langArr ))
			return false;
		$time = time ();
		$submit_id = parent::transaction_query ( array (
				"INSERT INTO codes (code, code_length)  VALUES (?, ?)" => array (
						$codes,
						strlen ( $codes ) 
				),
				"INSERT INTO status (pro_id, user_id, submit_time, lang, contest_id) VALUES (?, ?, ?,?, ?)" => array (
						$pro_id,
						$user_id,
						$time,
						$lang,
						$contestId 
				) 
		) );
		if ($submit_id > 0) {
			redisDB::rpush ( "submit_id", $submit_id );
			return true;
		} else
			return false;
	}
}
