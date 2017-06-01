<?php

class statusModel extends DB {
	public function __construct() {
		parent::__construct ();
	}
	public function getStatus($submit_id, $pro_id, $username, $lang, $status, $start, $end, $contest_id = 0) {
		$q = "SELECT status.submit_id, submit_time, status.pro_id, run_time, run_memory, codes.code_length, lang, status.status, username, score FROM";
		$q .= " status LEFT JOIN codes ON (status.submit_id = codes.submit_id), users WHERE users.user_id = status.user_id AND contest_id = $contest_id ";
		if ($submit_id)
			$q .= "AND status.submit_id = $submit_id ";
		if ($pro_id)
			$q .= "AND status.pro_id = $pro_id ";
		if ($username)
			$q .= "AND username = ? ";
		if ($lang)
			$q .= "AND lang = $lang ";
		if ($status)
			$q .= "AND status = $status ";
		
		$maxNum = STATUSPAGEMAXSIZE;
		if ($start) {
			$q .= "AND status.submit_id <= $start ";
		} else if ($end) {
			$start = $this->getStart ( $end, $maxNum, $q, $username);
			$q .= "AND status.submit_id <= $start ";
		}
		$q .= "ORDER BY status.submit_id DESC LIMIT $maxNum";
		$result = parent::query ( $q , array($username));
		if ($result->rowCount () != 0) {
			while ( $row = $result->fetch ( PDO::FETCH_NUM ) ) {
				if($contest_id){
                    $row[2] =  $this->get_inner_id($row[2], $contest_id);
					if($row[9] != -1) {
					    $row[9] = $this->get_max_score($row[9], $contest_id, $row[2]);
                    } else {
					    unset($row[9]);
                    }
				} else {//更正比赛模式下 真实ID和虚拟ID
                    unset($row[9]); //普通模式不启用OI
                }
				$arr [] = $row;
			}
			return $arr;
		} else {
			return null;
		}
	}
	private function getStart($end, $maxNum, $q, $username) {
		$res = parent::query ( $q . "AND status.submit_id >= $end LIMIT $maxNum" ,array($username));
		if ($res->rowCount () != 0) {
			while ( $row = $res->fetch ( PDO::FETCH_NUM ) ) {
				$id = $row [0];
			}
			return $id;
		} else {
			return null;
		}
	}
	
	public function get_inner_id($pro_id, $contestId){
		$res = parent::query_one("SELECT inner_id FROM contest_pro WHERE contest_id = ? AND pro_id = ?", array($contestId, $pro_id));
		if($res){
			return $res[0];
		} else {
			return -1;
		}
	}
	
	public function get_real_id($pro_id, $contestId){
		$res = parent::query_one("SELECT pro_id FROM contest_pro WHERE contest_id = ? AND inner_id = ?", array($contestId, $pro_id));
		if($res){
			return $res[0];
		} else {
			return -1;
		}
	}

	public function get_max_score($rate, $inner_id, $cid) {
	    $res = parent::query_one("SELECT max_score FROM contest_pro WHERE contest_id = ? AND inner_id = ?", [$inner_id, $cid]);
	    return $res[0] * $rate / 100;
    }
}
