<?php

	if ($_SERVER['REQUEST_METHOD'] 	!= 'POST') return;
	if ($_POST['action'] 			!= 'newuser') return;

	include_once("config.php");
	include_once("sms.php");

	db_connect($dbhost, $dbuser, $dbpass, $dbname);
	mysql_query("SET NAMES 'utf8'");

	$time 		= time();
	$datetime 	= date("d.m.Y H:i:s");
	$ip  		= $_SERVER['REMOTE_ADDR'];

	$task  		= $_POST['task'];
	$userID  	= $_POST['userid'];
	$phone  	= $_POST['phone'];
	$oldPhone  	= $_POST['oldphone'];
	$id  		= $_POST['id'];
	$mode 		= $_POST['mode'] == '1' ? 'TENANT' : 'REALTOR';
	
	$app  		= $_POST['app'];
	$os  		= $_POST['os'];
	$version  	= $_POST['version'];
	$device  	= $_POST['device'];
	$latitude  	= $_POST['latitude'];
	$longitude  = $_POST['longitude'];
	
	// проверка на SQL инъекции
	if (checkSQL("$task $userID $phone $id $oldPhone $app $os $version $device $latitude $longitude") > 0) exit();
	
	// если это регистрация соискателя
	if	($task == 'newtenant') {
	
		$res = mysql_query("SELECT COUNT(*) FROM USERS WHERE USERID = '$userID' ");
		$count = mysql_fetch_row($res);
		
		// если такой соискатель уже существует в базе
		if ($count[0] != 0) {
			echo "tenantExists";
			return;
		}
		
		mysql_query(" INSERT INTO `USERS` (`USERID`, `CODEID`, `MODE`, `LATITUDE`, `LONGITUDE`, `REGDATE`, `REGTIME`, `ENTERDATE`, `ENTERTIME`, `APPVERSION`, `OS`, `OSVERSION`, `DEVICE`, `IP`)
			VALUES ('$userID', '$id', 'TENANT', '$latitude', '$longitude', '$datetime', '$time', '$datetime', '$time', '$app', '$os', '$version', '$device', '$ip') ");
	
		echo "regTenantSuccess";
		return;
	}
	
	// если это первичное получение кода для подтверждения регистрации
	if	($task == 'newcode') {

		// 1. проверка на неавторизованный номер, повторную смену номера или бан (только при смене номера)
		if ($oldPhone != '') {
		
			// активен ли наш текущий номер телефона?
			$res = mysql_query(" SELECT COUNT(*) FROM USERS WHERE USERID = '$oldPhone' AND CODEID = '$id' AND ACTIVE = 'YES' ");
			$count = mysql_fetch_row($res);
		
			// если такого активного номера нет, то сообщаем об этом
			if ($count[0] == 0) {
				echo "errorPhone";
				return;
			}
			
			// менял ли пользователь свой номер уже сегодня?
			$timeMinus24hours = time() - 86400;
			$res = mysql_query(" SELECT COUNT(*) FROM USERS WHERE USERID = '$oldPhone' AND PHONECHANGETIME > '$timeMinus24hours' ");
			$count = mysql_fetch_row($res);
		
			// если да, то сообщаем об этом
			if ($count[0] != 0) {
				echo "alreadyChanged";
				return;
			}
		}
	
		// 2. затем проверяем, заблокирован ли указанный пользователем новый номер телефона?
		$res = mysql_query(" SELECT COUNT(*) FROM USERS WHERE USERID = '$phone' AND ACTIVE = 'NO' ");
		$count = mysql_fetch_row($res);
		
		// если да, то сообщаем об этом
		if ($count[0] != 0) {
			echo "wrongPhone";
			return;
		}
		
		// 3. теперь проверяем не запрашивал ли пользователь пароль в течение последней минуты
		$timeMinus60seconds = time() - 60;
		$res = mysql_query(" SELECT COUNT(*) FROM CODES WHERE REQUESTTIME > '$timeMinus60seconds' AND IP = '$ip' ");
		$count = mysql_fetch_row($res);
		
		// если запрашивал, то сообщаем об этом
		if ($count[0] != 0) {
			echo "pleaseWait";
			return;
		}
		
		// 4. дополнительная защита - пользователь в течение дня может запросить код только не более 5 раз
		$datetime24hours = date("d.m.Y");
		$res = mysql_query(" SELECT COUNT(*) FROM CODES WHERE REQUESTDATE LIKE '%$datetime24hours%' AND IP = '$ip' ");
		$count = mysql_fetch_row($res);
		
		// если лимит достигнут, то сообщаем об этом
		if ($count[0] > 4) {
			echo "dayLimit";
			return;
		}
	
		$codeID = rand(100000, 999999);
		
		// 5. [не передаем] если все проверки пройдены, то отправляем запрос на отправку смс через шлюз
		// if (!$SMS_DEBUG && $SMS_DEBUG_PHONE != (int)$phone) sendSMS("7$phone", "Vash kod: $codeID", $WEBSMS_LOGIN, $WEBSMS_PASSWORD);	

		// 6. и сохраняем связку номер-код в таблице кодов
		mysql_query(" INSERT INTO `CODES` (`PHONE`, `OLDPHONE`, `CODEID`, `REQUESTDATE`, `REQUESTTIME`, `OS`, `OSVERSION`, `DEVICE`, `IP`) VALUES ('$phone', '$oldPhone', '111111', '$datetime', '$time', '$os', '$version', '$device', '$ip') ");
		
		echo "codeSent";
		return;
	}
	
	// если это подтверждение регистрации
	if	($task == 'checkcode') {
	
		// 1. вначале проверяем, есть ли такая связка номер-код в базе
		$res = mysql_query(" SELECT COUNT(*) FROM CODES WHERE PHONE = '$phone' AND CODEID = '$id' AND ACTIVATED = 'NO' ");
		$count = mysql_fetch_row($res);
		
		// если нет, то отправляем ошибку
		if ($count[0] == 0) {
			echo "errorCode";
			return;
		}
		
		// 2. если есть, то связку активируем и удаляем предыдущий/новый аккаунт (если есть)
		mysql_query(" UPDATE CODES SET ACTIVATED = 'YES', ACTIVATEDATE = '$datetime', ACTIVATETIME = '$time' WHERE PHONE = '$phone' AND CODEID = '$id' ");
		mysql_query(" DELETE FROM USERS WHERE USERID = '$phone' ");
		
		// 3. если это смена номера, то перезаписываем данные о пользователе
		if ($oldPhone != '') {
			mysql_query(" UPDATE USERS SET USERID = '$phone', PHONE = '$phone', CODEID = '$id', PHONECHANGEDATE = '$datetime', PHONECHANGETIME = '$time' WHERE USERID = '$oldPhone' ");
		} else {
			// 4. если это первичная регистрация, то добавляем пользователя в базу
			mysql_query(" INSERT INTO `USERS` (`USERID`, `PHONE`, `CODEID`, `MODE`, `LATITUDE`, `LONGITUDE`, `REGDATE`, `REGTIME`, `ENTERDATE`, `ENTERTIME`, `APPVERSION`, `OS`, `OSVERSION`, `DEVICE`, `IP`)
			VALUES ('$phone', '$phone', '$id', '$mode', '$latitude', '$longitude', '$datetime', '$time', '$datetime', '$time', '$app', '$os', '$version', '$device', '$ip') ");
			
			// удаляем если что старый UserID (соискателя)
			if (trim($userID) != '') mysql_query(" DELETE FROM USERS WHERE USERID = '$userID' ");	
		}
		
		echo "regSuccess";
		return;
	}

?>