<?php
if(defined('APPPATH')) {
    require APPPATH. '/Include/PHPMailer/class.phpmailer.php';
    require APPPATH. '/Include/PHPMailer/class.smtp.php';
} else {
    die();
}
function get($arg) { // 获取GET['']
	if (isset ( $_GET [$arg] )) {
		return $_GET [$arg];
	} else {
		return null;
	}
}
function post($arg) { // 获取POST['']
	if (isset ( $_POST [$arg] )) {
		return $_POST [$arg];
	} else {
		return null;
	}
}

/**
 * 通过抓取站长之家的网页，或者IP到地址的转换
 */
function get_ip_location($ip) {
	if (strlen ( $ip ) <= 7)
		return '未知地址';
	else {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, "http://ip.chinaz.com/" . $ip );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		$location = strrpos ( $output, "Whwtdhalf w50-0" );
		$i = 0;
		for($i = $location; $i < $location + 100; $i ++) {
			if ($output [$i] == '<')
				break;
		}
		return substr ( $output, $location + 17, $i - ($location + 17) );
	}
	return $ip;
}

/**
 * 格式化时间为小时:分钟:秒
 */
function format_time($t) {
	if ($t < 0)
		return;
	$h = ( int ) ($t / 60 / 60);
	$t -= $h * 60 * 60;
	$m = ( int ) ($t / 60);
	$t -= $m * 60;
	return $h . ':' . $m . ':' . $t;
}
/**
 * 登录超时检测
 */
function session_check() {
	session_start ();
	if (isset ( $_SESSION ['timeout'] )) { // 检查登录情况
		if ($_SESSION ['timeout'] < time ()) { // 登录超时
			$_SESSION = array ();
			session_destroy ();
			setcookie ( 'PHPSESSID', '', time () - 3600, '/', '', 0, 0 );
			return false;
		}
		$_SESSION ['timeout'] = time () + LOGINTIMEOUT; // 刷新时间戳 @config.php
		return true;
	}
	return false;
}
/**
 * 刷新频率检查
 */
function reload_check() {
	return false;
	$time = time ();
	if ($_SESSION ['refresh'] + 1 >= $time) {
		return true;
	} else {
		$_SESSION ['refresh'] = $time;
	}
}

/**
 * 登录权限检测 专用于后台模块
 */
function privilege_check() {
	if (session_check ()) {
		if ($_SESSION ['privilege'] [0] == 1 || isset ( $_SESSION ['privilege'] [1] )) {
			return true;
		}
	}
	return false;
}
/**
 * 输出比赛成绩的CSV文件
 */
function output_csv($cid, $data, $header) {
	$path = "./Src/File/contest_rankList" . $cid . ".csv";
	$file = fopen ( $path, "w+" );
	if ($file) {
		fputs ( $file,  "\xef\xbb\xbf"."比赛排名数据-实时更新\n" );
		fputs ( $file,  "\xef\xbb\xbf"."排名,用户名,所在小组,通过题目数,总时长" );
		$str = "";
		$ids = array ();
		ksort($header);
		foreach ( $header as $id => $t ) {
			$str .= "," . $id;
			$ids [] = $id;
		}
		fputs ( $file,  "\xef\xbb\xbf".$str. "\n" );
		$k = 1;
		
		foreach ( $data as $row ) {
			$str = "";
			$str .= $k ++;
			$str .= "," . '"'.$row [2] . '(' . $row [4] . ')"';
			$str .= "," . $row [3];
			$str .= "," . $row [1];
			$str .= "," . format_time ( $row [0] );
			foreach ( $ids as $i ) {
				if (isset ( $row [$i] )) {
					
					if (isset ( $row [$i] [2] ) && $row [$i] [2] == 1) { // 第一个通过该题
						$str .= "," . format_time ( $row [$i] [0] );
						if ($row [$i] [1])
							$str .= '(-' . $row [$i] [1] . ')';
					} else if ($row [$i] [0] && ! $row [$i] [1]) // 通过且没有罚时
						$str .= "," . format_time ( $row [$i] [0] );
					else if ($row [$i] [0] && $row [$i] [1]) // 通过且有罚时
						$str .= "," . format_time ( $row [$i] [0] ) . '(-' . $row [$i] [1] . ')';
					else
						$str .= ",".'-' . $row [$i] [1];
				} else {
					$str .= ",";
				}
			}
			fputs ( $file,  "\xef\xbb\xbf".$str. "\n" );
		}
		fclose($file);
	}
}


function sendMail($to, $title, $body) {
    $mail = new PHPMailer ();
    $mail->SMTPDebug = false;
    $mail->isSMTP ();
    $mail->SMTPAuth = true;
    $mail->Host = 'smtp.qq.com';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->Hostname = 'http://acm.nuc.edu.cn';
    $mail->CharSet = 'UTF-8';
    $mail->FromName = '中北大学ACM程序设计创新实验室';
    $mail->Username = '842063523';
    $mail->Password = 'vmbzvpilgddsbegf';
    $mail->From = '842063523@qq.com';
    $mail->isHTML ( true );
    $mail->addAddress ( $to);
    $mail->Subject = $title;
    $mail->Body = $body;
    $status = $mail->send ();
    return $status;
}
