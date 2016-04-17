<?php

	if ($_SERVER['REQUEST_METHOD'] 	!= 'POST') return;
	if ($_POST['action'] 			!= 'settings') return;

	include_once("config.php");
	db_connect($dbhost, $dbuser, $dbpass, $dbname);
	mysql_query("SET NAMES 'utf8'");

	$time 		= time();
	$datetime 	= date("d.m.Y H:i:s");
	$ip  		= $_SERVER['REMOTE_ADDR'];

	$task  		= $_POST['task'];
	$userID  	= $_POST['userid'];
	$phone  	= $_POST['phone'];
	$id  		= $_POST['id'];
	$stars  	= $_POST['stars'];
	$text  		= $_POST['text'];
	$token  	= $_POST['token'];
	$mode 		= $_POST['mode'] 	== '1' ? 'TENANT' : 'REALTOR';
	$notify 	= $_POST['notify'] 	== '1' ? 'YES' 	  : 'NO';
	
	$app  		= $_POST['app'];
	$os  		= $_POST['os'];
	$version  	= $_POST['version'];
	$device  	= $_POST['device'];
	$latitude  	= (float)$_POST['latitude'];
	$longitude  = (float)$_POST['longitude'];
	
	// проверка на SQL инъекции
	if (checkSQL("$task $userID $phone $id $stars $text $token $app $os $version $device $latitude $longitude") > 0) exit();
	
	$coordsParams = ", LATITUDE = '$latitude', LONGITUDE = '$longitude'";
	if ($latitude == 0) $coordsParams = '';
	
	// если это обновление всей информации о пользователе
	if	($task == 'updateinfo') {
		mysql_query(" UPDATE USERS SET STARS = '$stars', MODE = '$mode', NOTIFY = '$notify', ENTERDATE = '$datetime', ENTERTIME = '$time' $coordsParams, APPVERSION = '$app', OS = '$os', OSVERSION = '$version', DEVICE = '$device', IP = '$ip' WHERE USERID = '$userID' AND CODEID = '$id' ");
		
		// также ведем лог посещаемости
		mysql_query(" INSERT INTO `LOG_USERS` (`USERID`, `PHONE`, `MODE`, `ENTERDATE`, `ENTERTIME`, `STARS`, `OS`, `OSVERSION`, `DEVICE`, `IP`)
			VALUES ('$userID', '$phone', '$mode', '$datetime', '$time', '$stars', '$os', '$version', '$device', '$ip') ");
		return;
	}
	
	// если это обновление Device Token
	if	($task == 'updatetoken') {
		mysql_query(" UPDATE USERS SET TOKEN = '$token' WHERE USERID = '$userID' AND CODEID = '$id' ");
		return;
	}
	
	// если это обратная связь
	if	($task == 'contactus') {
		mail($EMAIL_ADMIN, "Обратная связь", "Пользователь: $userID\nРежим: $mode\nОценка: $stars\n\nВерсия приложения: $app\nОС: $os $version\nУстройство: $device\n\nСообщение: $text", "From: Arenda App <$EMAIL_ADMIN>\r\nContent-Type: text/plain; charset=utf-8\r\n");
		echo "contactusSuccess";
	}

?>