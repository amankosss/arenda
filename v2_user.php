<?php

	if ($_SERVER['REQUEST_METHOD'] 	!= 'POST') return;
	if ($_POST['action'] 			!= 'arenda') return;

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
	
	$region  	= $_POST['region'];
	$rooms  	= $_POST['rooms'];
	$rentTime  	= $_POST['renttime'] == '0' ? 'MONTH' : 'DAY';
	$cost  		= $_POST['cost'];
	$realtor  	= $_POST['realtor'];
	
	$comment 	= str_replace('*', '', $_POST['comment']);
	$comment 	= str_replace('|', '', $comment);
	if (trim($comment) == '') $comment = '-';
	
	$reason  	= $_POST['reason'];
	
	$regionLatitude  	= (float)$_POST['regionlat'];
	$regionLongitude  	= (float)$_POST['regionlng'];

	$app  		= $_POST['app'];
	$os  		= $_POST['os'];
	$version  	= $_POST['version'];
	$device  	= $_POST['device'];
	$latitude  	= (float)$_POST['latitude'];
	$longitude  = (float)$_POST['longitude'];
	
	// проверка на SQL инъекции
	if (checkSQL("$task $userID $phone $id $region $rooms $cost $realtor $comment $regionLatitude $regionLongitude $app $os $version $device $latitude $longitude") > 0) exit();
	
	// активен ли наш текущий номер телефона?
	$res 	= mysql_query(" SELECT COUNT(*) FROM USERS WHERE USERID = '$userID' AND CODEID = '$id' AND ACTIVE = 'YES' ");
	$count 	= mysql_fetch_row($res);
	
	// если такого активного номера нет
	if ($count[0] == 0) {
		echo "errorAccount";
		return;
	}
	
	// если версия у iOS соискателя устарела
	if ($os == 'iOS' && ((float)$app < (float)$VERSION_IOS)) {
		echo "updateApp*".$VERSION_IOS;
		return;
	}
	
	// если версия у Android соискателя устарела
	if ($os == 'Android' && ((float)$app < (float)$VERSION_ANDROID)) {
		echo "updateApp*".$VERSION_ANDROID;
		return;
	}
	
	// если мы подтверждаем поиск квартиры
	if	($task == 'submit') {
	
		// проверка на запрещенные слова
		if (checkWords($comment) > 0) {
			echo "errorComment";
			return;
		}
		
		mysql_query(" UPDATE USERS SET PHONE = '$phone', SEARCHING = 'YES', ACTIONTIME = '$time', SEARCHDATE = '$datetime', SEARCHTIME = '$time', REGION = '$region', REGIONLATITUDE = '$regionLatitude', REGIONLONGITUDE = '$regionLongitude', ROOMS = '$rooms', RENTTIME = '$rentTime', FLATCOST = '$cost', REALTORCOST = '$realtor', COMMENT = '$comment' WHERE USERID = '$userID' AND CODEID = '$id' ");
		echo "submitSuccess";
		return;
	}
	
	// если мы отменяем поиск квартиры
	if	($task == 'cancel') {
		mysql_query(" UPDATE USERS SET SEARCHING = 'CANCELED', ACTIONTIME = '$time', SEARCHCANCELDATE = '$datetime', SEARCHCANCELTIME = '$time' WHERE USERID = '$userID' AND CODEID = '$id' ");
		echo "cancelSuccess";		
		return;
	}
	
	// если это рассылка push уведомлений
	if	($task == 'pushes' && $PUSH_NOTIFICATIONS) {
	
		$searchInLatitude = $regionLatitude;
		$searchInLongitude = $regionLongitude;
		
		include_once("push.php");

		$pushMessage = createPushMessage($rooms, $rentTime, $cost);
		
		// перед рассылкой по риелторам, вначале узнаем их кол-во в округе
		$realtorsCount = countRealtors($searchInLatitude, $searchInLongitude, "iOS", $SEARCH_RADIUS);
		$realtorsCountAndroid = countRealtors($searchInLatitude, $searchInLongitude, "Android", $SEARCH_RADIUS);

		// рассылаем конкретно по iOS риелторам
		if ($realtorsCount > 0) loadAppleRealtorsAndSendPush($phone, false, $realtorsCount, 0, $searchInLatitude, $searchInLongitude, $SEARCH_RADIUS, $pushMessage);
	
		// рассылаем конкретно по Android риелторам
		if ($realtorsCountAndroid > 0) loadAndroidRealtorsAndSendPush($phone, $realtorsCountAndroid, 0, $searchInLatitude, $searchInLongitude, $SEARCH_RADIUS, $pushMessage);
	}
	
?>