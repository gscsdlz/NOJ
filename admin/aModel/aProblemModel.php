<?php
if (defined ( 'APPPATH' )) {
	require APPPATH . '/Model/problemModel.php';
} else {
	die ( 'aProblemModel' );
}
class aProblemModel extends problemModel {
	public function __construct() {
		parent::__construct ();
	}
	public function get_list($listId) {
		@override;
		$pms = PROBLEMPAGEMAXSIZE;
		$listId *= $pms;
		
		$arr [] = array (
				parent::get_maxProblem (),
				$pms 
		);
		$result = parent::query ( "SELECT pro_id, pro_title, visible FROM problem LIMIT $listId, $pms" );
		if ($result->rowCount () != 0) {
			while ( $row = $result->fetch ( PDO::FETCH_NUM ) ) {
				$arr [] = $row;
			}
			return $arr;
		} else
			return null;
	}
	public function get_all_hidden() {
		$result = parent::query ( "SELECT pro_id, pro_title, visible FROM problem WHERE visible = 0");
		if ($result->rowCount () != 0) {
			while ( $row = $result->fetch ( PDO::FETCH_NUM ) ) {
				$arr [] = $row;
			}
			return $arr;
		} else
			return null;
	}
	public function set_problem($pro_id, $visible) {
		return parent::query ( "UPDATE problem SET visible = ? WHERE pro_id = ? LIMIT 1", array($visible, $pro_id ));
	}
	
	public function del_problem($pro_id) {
		return parent::query("DELETE FROM problem WHERE pro_id = ? LIMIT 1",array($pro_id));
	}
	
	public function update_pro($pro_id, $pro_title, $time_limit, $memory_limit, $pro_descrip,$pro_in,$pro_out,$pro_dataIn,$pro_dataOut, $hint, $author) {
		return parent::query("UPDATE problem SET pro_title = ?, time_limit = ?, memory_limit = ?, pro_descrip = ?, pro_in = ?, pro_out = ?, pro_dataIn = ?, pro_dataOut = ?, hint = ?, author=? WHERE pro_id = ?", array($pro_title, $time_limit, $memory_limit, $pro_descrip,$pro_in,$pro_out,$pro_dataIn,$pro_dataOut, $hint, $author,$pro_id));
	}
	
	public function get_maxId() {
		$res = parent::query_one("SELECT MAX(pro_id) FROM problem");
		return $res[0];
	}
	
	public function insert_pro($pro_title, $time_limit, $memory_limit, $pro_descrip, $pro_in, $pro_out, $pro_dataIn, $pro_dataOut, $hint, $author ){
		return parent::query("INSERT INTO problem VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)", array($pro_title, $time_limit, $memory_limit, $pro_descrip, $pro_in, $pro_out, $pro_dataIn, $pro_dataOut, $hint, $author));
	}
}