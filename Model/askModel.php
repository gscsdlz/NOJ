<?php
class askModel extends DB {
	public function __construct() {
		parent::__construct ();
	}
	
	public function get_list_by_cid($cid) {
		$res = parent::query("SELECT question.*, users.username FROM users LEFT JOIN question ON(users.user_id = question.user_id) WHERE contest_id = ? ORDER BY ask_time DESC", array($cid));
		if($res->rowCount() != 0) {
			while($row = $res->fetch(PDO::FETCH_NUM)) {
				$args[] = $row;
			}
			return $args;
		}
		return null;
	}
	
	public function put_answer($question_id, $user_id, $topic) {
		$time = time();
		return parent::query("INSERT INTO answer (user_id, topic_answer, question_id, replay_time) VALUES(?,?,?,?)", array($user_id, $topic, $question_id, $time));
		
	}
	
	public function get_newMessage($cid, $time) {
		$res = parent::query("SELECT topic_question FROM question WHERE contest_id = ? AND urgent = 1 AND ask_time > ? ORDER BY ask_time DESC", array($cid, $time -  60 * 60 ));
		if($res->rowCount() == 0)
			return false;
		else { 
			$args = array();
			while($row = $res->fetch(PDO::FETCH_NUM)) {
				$args[] = $row[0];
			}
			return $args;
		}
	}
	
	public function put_question($pro_id, $user_id, $topic, $publicMessage, $cid = 0) {
		$time = time();
		return parent::query("INSERT INTO question (pro_id, user_id, topic_question, contest_id, ask_time, urgent) VALUES(?,?,?,?,?, ?)", array($pro_id, $user_id, $topic, $cid, $time, $publicMessage));
	}
	
	public function delete_question($ask_id, $user_id, $cid, $pri) {
		if($pri[0] == 1 || isset($pri[1][$cid])) {
			return parent::query("DELETE FROM question WHERE question_id = ?", array($ask_id));
		}
		$realId = parent::query("SELECT user_id FROM question WHERE question_id = ? AND user_id = ?", array($ask_id, $user_id));
		if($realId->rowCount() != 0) {
			return parent::query("DELETE FROM question WHERE question_id = ?", array($ask_id));
		}
		return false;
	}
	
	public function delete_answer($question_id, $user_id,  $cid, $pri) {
		if($pri[0] == 1 || isset($pri[1][$cid])) {
			return parent::query("DELETE FROM answer WHERE answer_id = ?", array($question_id));
		}
		$realId = parent::query("SELECT user_id FROM answer WHERE answer_id = ? AND user_id = ?", array($question_id, $user_id));
		if($realId->rowCount() != 0) {
			return parent::query("DELETE FROM answer WHERE answer_id = ?", array($question_id));
		}
		return false;
	}
	
	
	public function get_answer($question_id) {
		$args[] = parent::query_one("SELECT question.*, users.username FROM users LEFT JOIN question ON(users.user_id = question.user_id) WHERE question.question_id = ?", array($question_id));
		$res = parent::query("SELECT answer.*, users.username FROM  users LEFT JOIN answer ON(users.user_id = answer.user_id) WHERE question_id = ?", array($question_id));
		if($res->rowCount() != 0) {
			while($row = $res->fetch(PDO::FETCH_NUM)) {
				$args[] = $row;
			}
		}
		return $args;
	}
}
?>