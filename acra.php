<?php

	if ($_SERVER['REQUEST_METHOD'] != 'POST') return;

	$user_androidVersion = $_POST['ANDROID_VERSION'];
	$user_appVersionName = $_POST['APP_VERSION_NAME'];
	$user_phoneBrand = $_POST['BRAND'];
	$user_phoneModel = $_POST['PHONE_MODEL'];
	$user_crashDate = $_POST['USER_CRASH_DATE'];
	$user_memoryTotal = $_POST['TOTAL_MEM_SIZE'];
	$user_memoryAvailable = $_POST['AVAILABLE_MEM_SIZE'];
	$user_stackTrace = $_POST['STACK_TRACE'];

	$crashFileName = date("d.m.Y_H-i-s")."_".$user_appVersionName."_".str_replace(' ', '_', $user_phoneBrand."_".$user_phoneModel).".txt";

	$fp = fopen("android_crashes/".$crashFileName, "w");
	fwrite($fp, "Android version: $user_androidVersion \nApp version: $user_appVersionName \nPhone brand: $user_phoneBrand \nPhone model: $user_phoneModel \nTotal memory: $user_memoryTotal \nAvailable memory: $user_memoryAvailable \nCrash time: $user_crashDate \n\n$user_stackTrace");
	fclose($fp);

?> 