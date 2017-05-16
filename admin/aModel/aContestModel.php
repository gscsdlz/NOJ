<?php
if (defined ( 'APPPATH' )) {
	require APPPATH . '/Model/contestModel.php';
} else {
	die ( 'aContestModel' );
}
class aContestModel extends contestModel {
	public function __construct() {
		parent::__construct ();
	}
	public function get_problem_info($pro_id) {
		$res = parent::query_one ( "SELECT pro_title FROM problem WHERE pro_id = ?", array($pro_id ));
		if ($res) {
			return $res [0];
		} else {
			return null;
		}
	}
	public function add_problem($prolist, $cid, $contest_name, $username, $contest_pass, $c_stime, $c_etime, $auNum, $agNum, $cuNum, $feNum, $options) {
		$user_id = $this->check_user ( $username );
		if ($cid == 0) {
			$newContest = true; // 避免再次检查题目问题
			$cid = parent::query ( "INSERT INTO contest (contest_name, user_id, contest_pass, c_stime, c_etime, au, ag, cu, fe, options) VALUES (?, ? ,? ,? ,?, ?, ?, ? , ?, ?)", array($contest_name, $user_id, $contest_pass, $c_stime, $c_etime, $auNum, $agNum, $cuNum, $feNum, $options ));
			if ($cid <= 0)
				return - 1;
		} else {
			$status = parent::query ( "UPDATE contest SET contest_name = ?, user_id= ?, contest_pass= ?, c_stime = ? , c_etime = ?, au = ?, ag = ?, cu = ?, fe = ?, options = ? WHERE contest_id = ?", array($contest_name, $user_id, $contest_pass, $c_stime, $c_etime, $auNum, $agNum, $cuNum, $feNum, $options, $cid ));
		}
		$pro_arr = array ();
		if (count ( $prolist ))
			foreach ( $prolist as $row ) {
				$inner_id = $row [0];
				$pro_id = $row [1];
				$pro_arr [] = $pro_id;
				$res = parent::query_one ( "SELECT inner_id FROM contest_pro WHERE contest_id = ? AND pro_id = ?", array($cid, $pro_id ));
				if ($res) {
					if ($res [0] != $inner_id) {
						parent::query ( "UPDATE contest_pro SET inner_id = ? WHERE contest_id = ? AND pro_id = ?", array($inner_id, $cid, $pro_id ));
					}
				} else {
					parent::query ( "INSERT INTO contest_pro (contest_id, inner_id, pro_id) VALUES (?, ? ,?)", array($cid, $inner_id, $pro_id ));
				}
			}
		if (! isset ( $newContest )) {
			$res = parent::query ( "SELECT pro_id FROM contest_pro WHERE contest_id = ?", array($cid ));
			while ( $row = $res->fetch ( PDO::FETCH_NUM ) ) {
				if (! in_array ( $row [0], $pro_arr )) {
					//parent::query("DELETE FROM status WHERE contest_id = ? AND pro_id = ?", array($cid, $row[0]));
					parent::query("DELETE FROM contest_pro WHERE contest_id = ? AND pro_id = ? LIMIT 1", array($cid, $row [0] ));
				}
			}
		}
		return $cid;
	}
	public function del_contest($cid) {
		return parent::query ( "DELETE FROM contest WHERE contest_id = ? LIMIT 1", array($cid ));
	}
	public function check_user($username) {
		$res = parent::query_one ( "SELECT user_id FROM users WHERE username = ?", array($username ));
		if ($res) {
			return $res [0];
		}
		return false;
	}
	public function get_users($cid) {
		$res = parent::query ( "SELECT users.user_id, username, nickname FROM contest_user LEFT JOIN users ON(contest_user.user_id = users.user_id) WHERE contest_id = ?", array($cid ));
		$args = array ();
		while ( $row = $res->fetch ( PDO::FETCH_NUM ) ) {
			$args [] = $row;
		}
		return $args;
	}
	public function save_users($cid, $lists) {
        $res = parent::query ( "SELECT user_id FROM contest_user WHERE contest_id = ?", array($cid ));
		$ids = array ();
		while ( $row = $res->fetch ( PDO::FETCH_NUM ) ) {
            $ids [] = $row [0];
        }
		if(count($lists)) {
            foreach ($lists as $id) {
                if (!in_array($id, $ids))
                    parent::query("INSERT INTO contest_user VALUES  (?, ?) ", array($id, $cid));
            }
        }
		foreach ( $ids as $id ) {
            if (!in_array($id, $lists)) {
                parent::query("DELETE FROM contest_user WHERE contest_id = ? AND user_id = ? LIMIT 1", array($cid, $id));
            }
		}
		return true;
	}
	public function balloon($cid) {
		$fb = array();
		
		$res = parent::query ( "SELECT username, seat, pro_id, users.user_id, submit_time, submit_id, balloon FROM users LEFT JOIN `status` ON (`status`.user_id = users.user_id) WHERE contest_id = ? AND status = 4  AND seat != NULL ORDER BY submit_time DESC", array($cid ));
		$args = array ();
		$k = 0;
		
		while ( $row = $res->fetch ( PDO::FETCH_NUM ) ) {
			$inner = parent::get_inner_Id ( $row [2], $cid );
			$findSig = false;
			foreach ( $args as $cel ) {
				if ($row [0] == $cel [0] &&  $inner == $cel [2]) {
					$findSig = true;
					break;
				}
			}
			if ($findSig == false) {
				$args [$k] = $row;
				$args [$k] [4] = date ( 'Y-m-d H:i:s', $row [4] );
				$args [$k] [2] = $inner;
				$args [$k] [7] = 0;
				if(isset($fb[$inner])) {
					if($fb[$inner][0] > $row[4]) {
						$fb[$inner][0] = $row[4];
						$fb[$inner][1] = $k;
					}
				}
				else {
					$fb[$inner] = array($row[4], $k);
				}
				$k++;
			}
		}
		foreach ($fb as $value) {
			$args[$value[1]][7] = 1;
		}
		return $args;
	}
	public function setballoon($sid) {
		return parent::query ( "UPDATE status SET balloon = 1 WHERE submit_id = ?", array($sid ));
	}
	public function rejudge($cid, $pro_id, $user_id, $submit_id, $rejudgeall) {
		$args = array ();
		$res = parent::query ( "SELECT submit_id FROM status WHERE contest_id = ? AND pro_id = ?", array($cid, $pro_id ));
		if ($res->rowCount () != 0) {
			while ( $row = $res->fetch ( PDO::FETCH_NUM ) ) {
				$args [] = $row [0];
			}
		}
		if ($submit_id > 0)
			$args [] = $submit_id;
			$res = parent::query ( "SELECT submit_id FROM status WHERE contest_id = ? AND user_id = ?", array($cid, $user_id ));
		if ($res->rowCount () != 0) {
			while ( $row = $res->fetch ( PDO::FETCH_NUM ) ) {
				$args [] = $row [0];
			}
		}
		if ($rejudgeall) {

            $res = parent::query ( "SELECT ce_info.submit_id FROM ce_info LEFT JOIN status on (ce_info.submit_id = status.submit_id) WHERE ce_info.info= '' and status.status = 11 ORDER BY ce_info.submit_id ASC LIMIT 0, 50 ");
			//$res = parent::query ( "SELECT submit_id FROM status WHERE contest_id = ?", array($cid ));
			if ($res->rowCount () != 0) {
				while ( $row = $res->fetch ( PDO::FETCH_NUM ) ) {
					$args [] = $row [0];
				}
			}
		}
		foreach ( $args as $id ) {
			parent::query ( "UPDATE status SET status = 12 WHERE submit_id = ?", array($id ));
		}
		foreach ( $args as $id ) {
			redisDB::rpush ( 'submit_id', $id );
		}
		return count ( $args );
	}
	/**
	 * 这里选出来是两行数据 所以是一行一行的操作 将第n行和第n + 1行 同时录入到第m行
	 * @param unknown $cid
	 * @return unknown[]
	 */
	public function get_sim($cid) {
	    $res = parent::query("SELECT s_id, sim_s_id, sim FROM sim WHERE contest_id = ?", array($cid));
	    $args = array();
	    $k = 0;
	    while($row = $res->fetch(PDO::FETCH_NUM)) {
            $s_id = $row[0];
            $sim_s_id = $row[1];
            $res1 = parent::query_one("SELECT pro_id, username, nickname, submit_id, seat, submit_time
		FROM `users` LEFT JOIN `status` ON (users.user_id = status.user_id) WHERE submit_id = ? ", array($s_id));
            $pro_id = parent::get_inner_Id($res1[0], $cid);
            if($pro_id == 1000 || $pro_id == 1001 || $pro_id == 1003)
                continue;
            $args[$k][0] =  $pro_id;
            $args[$k][1] = $row[2];
            $args[$k][2] = $res1[1];
            $args[$k][3] = $res1[2];
            $args[$k][4] = $res1[3];
            $args[$k][5] = $res1[4];
            $args[$k][6] = date("H:i:s", $res1[5]);

            $res2 = parent::query_one("SELECT pro_id, username, nickname, submit_id, seat, submit_time
		FROM `users` LEFT JOIN `status` ON (users.user_id = status.user_id) WHERE submit_id = ? ", array($sim_s_id));
            $args[$k][7] = $res2[1];
            $args[$k][8] = $res2[2];
            $args[$k][9] = $res2[3];
            $args[$k][10] = $res2[4];
            $args[$k][11] = date("H:i:s", $res2[5]);
            $k++;
	    }

		return $args;
	}

	public function fix_mis_sim($sid1, $sid2) {
		parent::query("UPDATE status SET status = 4 WHERE submit_id = ?", array($sid1));
		parent::query("UPDATE status SET status = 4 WHERE submit_id = ?", array($sid2));
		return parent::query("DELETE FROM sim WHERE s_id = ? AND sim_s_id = ?", array($sid2, $sid1));
	}

	public function do_cheating($sid, $cid) {
		$arg = parent::query_one("SELECT pro_id, user_id FROM status WHERE submit_id = ?", array($sid));
		return parent::query("UPDATE status SET status = 13 WHERE pro_id = ? AND contest_id = ? AND user_id = ?", array($arg[0], $cid, $arg[1]));
	}
}
