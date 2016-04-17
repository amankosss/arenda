<?
$referer = $_SERVER['HTTP_REFERER'];
$ip = $_SERVER["REMOTE_ADDR"];
$datetime = date("[d.m.Y H:i:s]");
  
$fp = fopen("downloads.txt","r+");
flock($fp, 1);
fseek($fp, 0, SEEK_END);
fputs($fp, "$datetime App Store > $ip : $referer\n");
flock($fp, 3);
fclose($fp);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="image_src" href="appstore.png">
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>App Store: Arenda</title>
<meta http-equiv="refresh" content="1;URL=https://itunes.apple.com/ru/app/arenda-arenda-kvartir/id1079416771?mt=8" />
</head>
<body>
Пожалуйста, подождите ... или нажмите кнопку Обновить.
</body>
</html>
