<?php
	function get_dir_name($pro_id){
		$dir = "/home/judge/data/".$pro_id;
		if(is_dir($dir)) {
			if($dh = opendir($dir)) {
				while(($file = readdir($dh)) != false) {
					if(strlen($file) <= 2)
						continue;
					$files[] = $file;
				}
			}
		}
		return $files;
	}
	
	function delete_file($pro_id, $filename) {
		if(rename("/home/judge/data/".$pro_id."/".$filename, "/home/judge/data/tmp/".$pro_id."_".$filename)) {
			return "/home/judge/data/tmp/".$pro_id."_".$filename;
		} else {
			return false;
		}
	}
	
	function download_file($pro_id, $filename) {
		if(copy("/home/judge/data/".$pro_id."/".$filename, "/var/www/html/OJ/Src/".$filename)) {
			$filepath = "/var/www/html/OJ/Src/".$filename;
			$file=fopen($filepath,"r");
			header("Content-Type: application/octet-stream");
			header("Accept-Ranges: bytes");
			header("Accept-Length: ".filesize($filepath));
			header("Content-Disposition: attachment; filename=".$filename);
			echo fread($file,filesize($filepath));
			fclose($file);
			unlink($filepath);
		} else {
			return false;
		}
	}
	
	function upload_file($pro_id, $filename, $tmp_name) {
		if(!is_dir("/home/judge/data/".$pro_id)) {
			mkdir("/home/judge/data/".$pro_id);
		}
		if(move_uploaded_file ( $tmp_name, '/home/judge/data/'.$pro_id .'/' . $filename ))
				return true;
		return false;
	}
?>
