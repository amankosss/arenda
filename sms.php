<?php 

	function sendSMS($to, $msg, $login, $password) { 
		$u = 'http://www.websms.ru/http_in5.asp'; 
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, 
		'Http_username='.urlencode($login).'&Http_password='.urlencode($password).'&Phone_list='.$to.'&Message='.urlencode($msg)); 
		curl_setopt($ch, CURLOPT_URL, $u); 
		$u = trim(curl_exec($ch)); 
		curl_close($ch); 
	}  

?>
