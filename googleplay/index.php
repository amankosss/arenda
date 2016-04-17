<?
$referer = $_SERVER['HTTP_REFERER'];
$ip = $_SERVER["REMOTE_ADDR"];
$datetime = date("[d.m.Y H:i:s]");
  
$fp = fopen("downloads.txt","r+");
flock($fp, 1);
fseek($fp, 0, SEEK_END);
fputs($fp, "$datetime Google Play > $ip : $referer\n");
flock($fp, 3);
fclose($fp);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="image_src" href="googleplay.png">
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<title>Google Play: Arenda</title>
<meta http-equiv="refresh" content="0;URL=https://play.google.com/store/apps/details?id=kz.app.arenda" />
</head>
<body>
</body>
</html>