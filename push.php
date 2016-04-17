<?php

	// формирует текст Push уведомления
	function createPushMessage($requestRooms, $requestRentTime, $requestCost) {
	
		$returnRooms = 'комнату';
		if ($requestRooms == 1) $returnRooms = '1-ком. квapтиpy';
		if ($requestRooms == 2) $returnRooms = '2-ком. квapтиpy';
		if ($requestRooms == 3) $returnRooms = '3-ком. квapтиpy';
		if ($requestRooms == 4) $returnRooms = '4+ком. квapтиpy';
	
		$returnRentTime = 'пocyтoчнo';
		if ($requestRentTime == 'MONTH') $returnRentTime = 'нa длитeльный cpoк';
	
		return "Ищyт $returnRentTime $returnRooms нe дopoжe $requestCost pyб.";
	}
	
	// подсчет кол-ва риелторов
	function countRealtors($lat, $lng, $os, $radius) {
		$oneWeek = time() - 604800;		
		$query = mysql_query("SELECT COUNT(*) FROM ( SELECT ( 6371 * acos( cos( radians($lat) ) * cos( radians( LATITUDE ) ) * cos( radians( LONGITUDE ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( LATITUDE ) ) ) ) AS distance FROM USERS WHERE ACTIVE = 'YES' AND MODE = 'REALTOR' AND TOKEN != '' AND OS = '$os' AND NOTIFY = 'YES' AND ENTERTIME > $oneWeek HAVING distance < $radius) AS totalcount");
		$arr = mysql_fetch_row($query);
		return (int)$arr[0];
	}
	
	// получение iOS риелторов из базы и прогон Push уведомлений по ним
	function loadAppleRealtorsAndSendPush($userPhone, $server, $realtorsCount, $startPos, $lat, $lng, $radius, $msg) {
	
		$queryStep = 100;
	
		if (!$server) {
			$timer_start = microtime(true);
			$server = openApplePushServer();
			$timer_finish = microtime(true);
		}

		$oneWeek = time() - 604800;	
		
		$timer_start = microtime(true);
		$query = mysql_query("SELECT TOKEN, ( 6371 * acos( cos( radians($lat) ) * cos( radians( LATITUDE ) ) * cos( radians( LONGITUDE ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( LATITUDE ) ) ) ) AS distance FROM USERS WHERE ACTIVE = 'YES' AND MODE = 'REALTOR' AND TOKEN != '' AND OS = 'iOS' AND NOTIFY = 'YES' AND ENTERTIME > $oneWeek HAVING distance < $radius ORDER BY INDX ASC LIMIT $startPos, $queryStep");

		while ($arr = mysql_fetch_array($query))
			sendPushToAppleServer($server, $msg, $arr['TOKEN']);
		$timer_finish = microtime(true);
		
		usleep(100000);
		$startPos+=$queryStep;
		
		if ($startPos < $realtorsCount) {
			loadAppleRealtorsAndSendPush($userPhone, $server, $realtorsCount, $startPos, $lat, $lng, $radius, $msg);
		} else closeApplePushServer($server);
	}
	
	// получение Android риелторов из базы и прогон Push уведомлений по ним
	function loadAndroidRealtorsAndSendPush($userPhone, $realtorsCount, $startPos, $lat, $lng, $radius, $msg) {
	
		$queryStep = 100;
	
		$oneWeek = time() - 604800;	
		
		$timer_start = microtime(true);
		$query = mysql_query("SELECT TOKEN, ( 6371 * acos( cos( radians($lat) ) * cos( radians( LATITUDE ) ) * cos( radians( LONGITUDE ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( LATITUDE ) ) ) ) AS distance FROM USERS WHERE ACTIVE = 'YES' AND MODE = 'REALTOR' AND TOKEN != '' AND OS = 'Android' AND NOTIFY = 'YES' AND ENTERTIME > $oneWeek HAVING distance < $radius ORDER BY INDX ASC LIMIT $startPos, $queryStep");

		$tokensArray = array();
		while ($arr = mysql_fetch_array($query))
			$tokensArray[] = $arr['TOKEN'];
		
		sendPushToAndroidServer($msg, $tokensArray);
		
		$timer_finish = microtime(true);
	
		usleep(100000);
		$startPos+=$queryStep;
		
		if ($startPos < $realtorsCount) loadAndroidRealtorsAndSendPush($userPhone, $realtorsCount, $startPos, $lat, $lng, $radius, $msg);
	}

	// функция открывает соединение с сервером Apple
	function openApplePushServer() {

		$apnsCert = "push.pem";
		$apnsPort = 2195; 
		$streamContext = stream_context_create();
		stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
		$resultServer = stream_socket_client('ssl://gateway.push.apple.com'. ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);

		return $resultServer;
	}

	// функция закрывает соединение с сервером Apple
	function closeApplePushServer($pushServer) {

		socket_close($pushServer);
		fclose($pushServer);
	}

	// функция отправляет Push сообщение на указанный iOS Token
	function sendPushToAppleServer($pushServer, $pushMessage, $pushDeviceToken) {

		if (strlen($pushDeviceToken) != 64) return;

		$payload['aps'] = array('alert' => $pushMessage, 'badge' => 1, 'sound' => 'default');
		$output = json_encode($payload);
		$token = pack('H*', $pushDeviceToken);
		$resultMessage = chr(0) . chr(0) . chr(32) . $token . chr(0) . chr(strlen($output)) . $output;
		fwrite($pushServer, $resultMessage);
	}

	// функция отправляет Push сообщение на указанный массив Android токенов
	function sendPushToAndroidServer($txt, $tokenArray) {

		$apiKey = "AIzaSyAG5TANM7EqkqQ8I04u_Yb4HFlSWwQEQs0"; // он же Server Key
					
		$url = 'https://android.googleapis.com/gcm/send';
		$fields = array('registration_ids' => $tokenArray, 'data' => array('message' => $txt));
		$headers = array('Authorization: key='.$apiKey, 'Content-Type: application/json');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_exec($ch);
		curl_close($ch);
	}

?>