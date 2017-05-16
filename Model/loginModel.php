<?php

class loginModel extends DB {
	public function __construct() {
		parent::__construct ();
	}
	
	private function get_privilege($user_id) {
		$res = parent::query("SELECT contest_id FROM contest WHERE user_id = ?", array($user_id));
		$args = array();
		while($row = $res->fetch(PDO::FETCH_NUM)) {
			$args[$row[0]] = 1;
		}
		return $args;
	}
	
	public function login($username, $password) {
		if (! empty ( $username ) && ! empty ( $password )) {
			$res = parent::query ( "SELECT password, user_id, privilege, username FROM users WHERE username=?", array($username ));
			$arr = $res->fetch ( PDO::FETCH_NUM );
			
			if ($res->rowCount () != 0  && $arr[3] == $username && sha1 ( $password ) == $arr [0]) { //通过修改username字段为binary类型 解决
				parent::query("UPDATE users SET lasttime = ?, lastip = ? WHERE user_id = ?", array(time(), $_SERVER['HTTP_X_REAL_IP'], $arr[1]));
				if($arr[2] == -1)
					return array($arr[1], $this->get_privilege($arr[1]));
				return array($arr [1], 1);
			}
		}
		return null;
	}
	public function register($username, $password, $password2, $nickname, $email) {
		if (! empty ( $username ) && ! empty ( $password ) && $password == $password2 && ! empty ( $email )) {
			$res = parent::query ( "SELECT user_id FROM users WHERE username=?", array($username ));
			if ($res->rowCount () != 0) {
				return - 1; // username has already been used
			}
			$res = parent::query ( "SELECT user_id FROM users WHERE email=?", array($email ));
			if ($res->rowCount () != 0) {
				return - 2; // email has already been used
			}
			parent::query( "INSERT INTO users (user_id, username, password, nickname, email) VALUES (NULL, ?, ?, ? ,?)", array($username, sha1 ( $password ), $nickname, $email));
			return 0;
		}
		return 1;
	} 
	public function updateInfo($userid, $password, $password2, $nickname, $email, $qq, $motto, $group) {
	    $status = 0;
		if ($password && $password == $password2) {
			$status = parent::query ( "UPDATE users SET password = sha1(?)	 WHERE user_id = ? LIMIT 1", array($password, $userid ));
		}
		$res = parent::query ( "SELECT user_id FROM users WHERE email=? AND user_id != ?", array($email, $userid ));
		if ($res->rowCount () != 0) {
			return - 1; // 邮箱已经被使用过了
		}
		$res = parent::query ( "SELECT * FROM `group` WHERE group_id = ?", array($group ));
		if ($res->rowCount () == 0) {
			return - 2; // groupID不合法
		}
		$status += parent::query ( "UPDATE users SET nickname=?, email=?, qq=?, motto=?, group_id = ? WHERE user_id = ? LIMIT 1", array($nickname, $email,$qq,$motto, $group, $userid));
		return $status;
	}

	public function get_mail($username) {
	    $res = parent::query_one("SELECT email FROM users WHERE username = ?", array($username));
	    if($res)
            return $res[0];
	    else
	        return null;
    }

    public function reset_pass($username, $pass) {
	    $row = parent::query("UPDATE users SET password=SHA1(?) WHERE username = ? LIMIT 1", array($pass, $username));
	    if($row == 1)
	        return true;
	    else
	        return false;
    }
}
