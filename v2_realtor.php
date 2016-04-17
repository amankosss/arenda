<?php

	if ($_SERVER['REQUEST_METHOD'] 	!= 'POST') return;
	if ($_POST['action'] 			!= 'loadusers') return;
	
	include_once("config.php");
	db_connect($dbhost, $dbuser, $dbpass, $dbname);
	mysql_query("SET NAMES 'utf8'");

	$time 		= time();
	$datetime 	= date("d.m.Y H:i:s");
	$ip 		= $_SERVER['REMOTE_ADDR'];

	$userID  	= $_POST['userid'];
	$phone 		= $_POST['phone'];
	$id 		= $_POST['id'];

	$actionTime = (int)$_POST['actiontime'];
	if ($actionTime == 0) $actionTime = $time - ($SEARCH_MAXTIME * 3600);	
	
	$app  		= $_POST['app'];
	$os  		= $_POST['os'];
	$latitude 	= (float)$_POST['latitude'];
	$longitude 	= (float)$_POST['longitude'];
	
	// проверка на SQL инъекции
	if (checkSQL("$userID $phone $id $actionTime $app $os $latitude $longitude") > 0) exit();
	
	// активен ли наш текущий номер телефона?
	$res 	= mysql_query(" SELECT COUNT(*) FROM USERS WHERE USERID = '$userID' AND CODEID = '$id' AND ACTIVE = 'YES' ");
	$count 	= mysql_fetch_row($res);

	// если такого активного номера нет
	if ($count[0] == 0) {
		echo "errorPhone";
		return;
	}
	
	// если версия у iOS риелтора устарела
	if ($os == 'iOS' && ((float)$app < (float)$VERSION_IOS)) {
		echo "updateApp*".$VERSION_IOS;
		return;
	}
	
	// если версия у Android риелтора устарела
	if ($os == 'Android' && ((float)$app < (float)$VERSION_ANDROID)) {
		echo "updateApp*".$VERSION_ANDROID;
		return;
	}

	// по умолчанию звонки разрешены и продлять подписку не требуется
	$CONFIG_CALLS = 1;

	// $res = mysql_query(" SELECT EXPIRETIME FROM PAYMENTS WHERE USERID = '$userID' ");
	// $arr = mysql_fetch_array($res);
	// $expireTime = (int)$arr['EXPIRETIME'];

	// // если это первый запуск приложения риелтором, то запускаем тестовый период на 1 час
	// if ($expireTime == 0) {
	// 	$testTime = time() + 3600;
	// 	$testDate = date("d.m.Y H:i:s", $testTime);
	// 	mysql_query(" INSERT INTO `PAYMENTS` (`USERID`, `PAYMENTDATE`, `EXPIREDATE`, `EXPIRETIME`, `DESCRIPTION`) VALUES ('$userID', '-', '$testDate', '$testTime', 'Тестовый период') ");
	// } else {
	// 	// если проплаченное/тестовое время истекло, то запрещаем звонки и просим продлить подписку
	// 	if (time() - $expireTime > 0) $CONFIG_CALLS = 0;
	// }

	// получаем список соискателей в текущем регионе
	$res = mysql_query("SELECT PHONE, SEARCHING, ACTIONTIME, SEARCHTIME, REGION, ROOMS, RENTTIME, FLATCOST, REALTORCOST, COMMENT, ( 6371 * acos( cos( radians($latitude) ) * cos( radians( REGIONLATITUDE ) ) * cos( radians( REGIONLONGITUDE ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( REGIONLATITUDE ) ) ) ) AS distance FROM USERS WHERE ACTIVE = 'YES' AND PHONE != '$phone' AND MODE = 'TENANT' AND SEARCHING != 'NO' AND ACTIONTIME > $actionTime HAVING distance < $SEARCH_RADIUS ORDER BY ACTIONTIME ASC LIMIT 50");
	while ($arr = mysql_fetch_array($res)) $USERS = $USERS.$arr['PHONE']."|".$arr['SEARCHING']."|".$arr['ACTIONTIME']."|".$arr['SEARCHTIME']."|".$arr['REGION']."|".$arr['ROOMS']."|".$arr['RENTTIME']."|".$arr['FLATCOST']."|".$arr['REALTORCOST']."|".$arr['COMMENT']."*";

	echo mb_substr("users"."*".$NEWS_REALTORS."|".$CONFIG_CALLS."*".$USERS, 0, -1);
?>