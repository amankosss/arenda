<?php

	include_once("../config.php");

	if ($_SERVER['REQUEST_METHOD'] != 'POST') return;
	if ($_POST['hash'] != $ADMINPANEL_PASSWORD) return;
	
	db_connect($dbhost, $dbuser, $dbpass, $dbname);
	mysql_query("SET NAMES 'utf8'");
	
	$datetime = date("d.m.Y H:i:s");
	$time = time();
	
	// запрос статистики
	if ($_POST['action'] == 'statistics') {
	
		// получаем статистику посещаемости за 30 дней
		$daysArray = "";
		for ($i = 0; $i <= 30; $i++) {
			
			$statsDate = date("d.m.Y", time() - $i * 86400);
			
			$res = mysql_query(" SELECT count(DISTINCT(USERID)) FROM USERS WHERE MODE = 'TENANT' AND ENTERDATE LIKE '%$statsDate%' ");
			$res = mysql_fetch_row($res);
			$tenants = (int)$res[0];
			
			$res = mysql_query(" SELECT count(DISTINCT(USERID)) FROM USERS WHERE MODE = 'REALTOR' AND ENTERDATE LIKE '%$statsDate%' ");
			$res = mysql_fetch_row($res);
			$realtors = (int)$res[0];
			
			$users = $tenants + $realtors;
				
  			$daysArray = $daysArray.$statsDate.": ".$users." чел. (соискателей: ".$tenants.", риелторов: ".$realtors.")<br>";
		} 
		
		// получаем кол-во онлайн пользователей
		$onlineMinutes = time()-180;
		$res = mysql_query(" SELECT COUNT(*) FROM USERS WHERE ENTERTIME > '$onlineMinutes' ");
		$res = mysql_fetch_row($res);
		$onlineUsers = (int)$res[0];
		
		// получаем кол-во iOS пользователей
		$res = mysql_query(" SELECT COUNT(*) FROM USERS WHERE OS = 'iOS' ");
		$res = mysql_fetch_row($res);
		$iOS = (int)$res[0];
		
		// получаем кол-во Android пользователей
		$res = mysql_query(" SELECT COUNT(*) FROM USERS WHERE OS = 'Android' ");
		$res = mysql_fetch_row($res);
		$android = (int)$res[0];
		
		$installs = $iOS + $android;
		
		// получаем кол-во соискателей
		$res = mysql_query(" SELECT COUNT(*) FROM USERS WHERE MODE = 'TENANT' ");
		$res = mysql_fetch_row($res);
		$tenants = (int)$res[0];
		
		// получаем кол-во риелторов
		$res = mysql_query(" SELECT COUNT(*) FROM USERS WHERE MODE = 'REALTOR' ");
		$res = mysql_fetch_row($res);
		$realtors = (int)$res[0];
		
		$installsTotal = "iOS: ".$iOS.", Android: ".$android.", Соискателей: ".$tenants.", Риелторов: ".$realtors;
	
		echo json_encode(array( "days" => $daysArray, "online" => $onlineUsers, "total" => $installs, "installs" => $installsTotal ));
		return;
	}
		
	// если это удаление
	if ($_POST['action'] == 'deleteuser') {
	
		$mode = $_POST['mode'];
		$userID = $_POST['userid'];
		
		mysql_query(" DELETE FROM USERS WHERE USERID = '$userID' ");
		echo $userID;
		return;	
	}

	// если это загрузка соискателей или риелторов
	if ($_POST['action'] == 'loadusers') {
	
		$pos = $_POST['pos'];
		$mode = $_POST['mode'];
		
		$res = mysql_query(" SELECT COUNT(*) FROM USERS WHERE MODE = '$mode' ");
		$totalUsers = mysql_fetch_row($res);

		$res = mysql_query(" SELECT * FROM USERS WHERE MODE = '$mode' ORDER BY INDX DESC LIMIT $pos, 50 ");
		
		$usersArray = array();
		while ($arr = mysql_fetch_array($res)) {
		
			$stars = $arr['STARS'];
			if ($stars == 0) $stars = '&nbsp;';
			
			$token = $arr['TOKEN'];
			if ($token == '') $token = 'Токен отсутствует'; else $token = 'Токен есть';
			
			$notify = $arr['NOTIFY'];
			if ($notify == 'YES') $notify = 'Уведомления включены'; else $notify = 'Уведомления отключены';
			
			$searching = $arr['SEARCHING'];
			if ($searching == 'NO') $searching = '&nbsp;';
			if ($searching == 'YES') $searching = 'В поиске';
			if ($searching == 'CANCELED') $searching = 'Поиск отменен';
			
			$searchTime = $arr['SEARCHTIME'];
			if ($searchTime == 0) $searchTime = '-'; else $searchTime = str_replace(date("d.m.Y"), 'сегодня в', date("d.m.Y H:i:s", $searchTime));  
		
			$searchCancelTime = $arr['SEARCHCANCELTIME'];
			if ($searchCancelTime == 0) $searchCancelTime = '-'; else $searchCancelTime = str_replace(date("d.m.Y"), 'сегодня в', date("d.m.Y H:i:s", $searchCancelTime));  
		
			$rentRegion = $arr['REGION'];
		
			$rentRooms =  $arr['ROOMS'];
			if ($rentRooms == 0) {
				$rentRooms = '-';
				if ($searching != '&nbsp;') $rentRooms = 'Комната';
			}
		
			$rentTime =  $arr['RENTTIME'];
			if ($rentTime == '') $rentTime = '-';
			if ($rentTime == 'MONTH') $rentTime = 'На длит. срок';
			if ($rentTime == 'DAY') $rentTime = 'Посуточно';
			
			$rentFlatCost =  $arr['FLATCOST'];
			if ($searching != '&nbsp;') $rentFlatCost .= ' руб.'; else $rentFlatCost = '-';
			
			$rentRealtorCost =  $arr['REALTORCOST'];
			if ($searching != '&nbsp;') $rentRealtorCost .= ' руб.'; else $rentRealtorCost = '-';
			
			$rentComment =  $arr['COMMENT'];
			if ($rentComment == '-') $rentComment = 'Комментарий отсутствует';
		
			$usersArray[] = array(
		
				"indx" => $arr['INDX'],
				"active" => $arr['ACTIVE'],
				"userid" => $arr['USERID'],
				"phone" => $arr['PHONE'],
				"mode" => $arr['MODE'],
				"latitude" => $arr['LATITUDE'],
				"longitude" => $arr['LONGITUDE'],
				"regtime" => str_replace(date("d.m.Y"), 'сегодня в', date("d.m.Y H:i:s", $arr['REGTIME'])),
				"entertime" => str_replace(date("d.m.Y"), 'сегодня в', date("d.m.Y H:i:s", $arr['ENTERTIME'])),   
				"phonechangetime" => $arr['PHONECHANGETIME'],
				"stars" => $stars,
				"appversion" => $arr['APPVERSION'],
				"os" => $arr['OS'],
				"osversion" => $arr['OSVERSION'],
				"device" => $arr['DEVICE'],
				"token" => $token,
				"notify" => $notify,
				"ip" => $arr['IP'],
				"searching" => $searching,
				"searchtime" => $searchTime,
				"searchcanceltime" => $searchCancelTime,
				"region" => $rentRegion,
				"regionlatitude" => $arr['REGIONLATITUDE'],
				"regionlongitude" => $arr['REGIONLONGITUDE'],
				"rooms" => $rentRooms,
				"renttime" => $rentTime,
				"flatcost" => $rentFlatCost,
				"realtorcost" => $rentRealtorCost,
				"comment" => $rentComment,
			);
		}

		echo json_encode( array( "task" => "loadusers", "count" => $totalUsers[0], "users" => $usersArray ) );
		return;
	}
	
	// если это загрузка списка кодов
	if ($_POST['action'] == 'loadcodes') {
	
		$pos = $_POST['pos'];
		
		$res = mysql_query(" SELECT COUNT(*) FROM CODES ");
		$totalCodes = mysql_fetch_row($res);
	
		$res = mysql_query(" SELECT * FROM CODES ORDER BY INDX DESC LIMIT $pos, 50 ");

		$codesArray = array();
		while ($arr = mysql_fetch_array($res)) {
		
			$codesOldPhone = $arr['OLDPHONE'];
			if ($codesOldPhone == '') $codesOldPhone = '&nbsp;';
			
			$codesActivateTime = $arr['ACTIVATETIME'];
			if ($codesActivateTime == 0) $codesActivateTime = '-'; else $codesActivateTime = str_replace(date("d.m.Y"), 'сегодня в', date("d.m.Y H:i:s", $codesActivateTime));
		
			$codesArray[] = array(
		
				"indx" => $arr['INDX'],
				"phone" => $arr['PHONE'],
				"oldphone" => $codesOldPhone,
				"codeid" => $arr['CODEID'],
				"activated" => $arr['ACTIVATED'],
				"requesttime" => str_replace(date("d.m.Y"), 'сегодня в', date("d.m.Y H:i:s", $arr['REQUESTTIME'])),
				"activatetime" => $codesActivateTime,   
				"os" => $arr['OS'],
				"osversion" => $arr['OSVERSION'],
				"device" => $arr['DEVICE'],
				"ip" => $arr['IP'],

			);
		
		}
	
		echo json_encode( array( "task" => "loadcodes", "count" => $totalCodes[0], "codes" => $codesArray ) );
	}

	// если это загрузка списка подписок
	if ($_POST['action'] == 'loadsubs') {

		$pos = $_POST['pos'];

		$currentTime = time();
		
		$res = mysql_query(" SELECT COUNT(*) FROM PAYMENTS WHERE EXPIRETIME > $currentTime ");
		$totalPayments = mysql_fetch_row($res);
	
		$res = mysql_query(" SELECT * FROM PAYMENTS WHERE EXPIRETIME > $currentTime ORDER BY EXPIRETIME DESC LIMIT $pos, 50 ");

		$subsArray = array();
		while ($arr = mysql_fetch_array($res)) {
		
			$subsArray[] = array(
		
				"indx" => $arr['INDX'],
				"userid" => $arr['USERID'],
				"paymentdate" => $arr['PAYMENTDATE'],
				"expiredate" => $arr['EXPIREDATE'],
				"desc" => $arr['DESCRIPTION'],
				"times" => $arr['TIMES'],

			);
		
		}
	
		echo json_encode( array( "task" => "loadsubs", "count" => $totalPayments[0], "subs" => $subsArray ) );
	}
	

?>