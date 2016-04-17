<?
	$referer = $_SERVER['HTTP_REFERER'];
	$ip = $_SERVER["REMOTE_ADDR"];
	$userAgent = $_SERVER['HTTP_USER_AGENT'];

	$datetime = date("[d.m.Y H:i:s]");
	  
	$fp = fopen("downloads.txt","r+");
	flock($fp, 1);
	fseek($fp, 0, SEEK_END);
	fputs($fp, "$datetime $ip : $referer > $userAgent\n");
	flock($fp, 3);
	fclose($fp);

	$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
	$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
	$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
	$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");

	$link = "http://arenda-app.ru";

	if ($Android) {
		$link = "http://arenda-app.ru/googleplay";
	} 

	if ($iPod or $iPhone or $iPad) {
		$link = "http://arenda-app.ru/appstore";
	} 

	header("Location:$link");
?>
