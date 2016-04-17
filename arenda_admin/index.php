<?php

include_once("../config.php");

$postFile = 'index.php';
$bodyOnLoad = '<body>';
$logged = false;

$mobilePreview = <<<html
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
html;

$login = $_COOKIE['login'];
$pass = $_COOKIE['pass'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
	$login  = $_POST['s_login'];
	$pass  = md5($_POST['s_pass']);
}

if (($login == $ADMINPANEL_LOGIN) and ($pass == $ADMINPANEL_PASSWORD)) {
	$mobilePreview = '';
	
$bodyOnLoad = <<<html
<body onLoad="load(0, 'TENANT')">
html;

	$postFile = '_post.php';
	$logged = true;
}

setcookie("login", $login);
setcookie("pass", $pass);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<?=$mobilePreview;?>
<title>Arenda - Панель управления</title>
<link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="js.js"></script>
<link rel="stylesheet" href="img/style.css">
</head>

<?= $bodyOnLoad;?>

<script>
var passHash = "<? echo $pass ?>";
var postFile = "<? echo $postFile ?>";
</script>

<?php

if ($logged == true) {
	
// ведем лог доступа в админ панель при успешном входе
$fp = fopen("log.txt","r+");
flock($fp,1);
fseek($fp,0,SEEK_END);
fputs($fp, date("[d.m.Y H:i:s]")." ".$_SERVER['REMOTE_ADDR']." > ".$_SERVER['HTTP_USER_AGENT']."\n");
flock($fp,3);
fclose($fp);
	
$body = <<<html

<table border="0" align="center" cellpadding="0" cellspacing="0">
 <tr>
    <td width="400" valign="top" align="center" class="ft_normal"><a href="http://arenda-app.ru" target="_blank"><img src="../img/logo.png"/></a><br>Панель управления v.1.0 от 20.10.2015</td>
	<td width="40"></td>
	<td width="200" valign="middle" class="ft_normal">
		• <a class="navlink" href="https://194.58.103.42/manager/ispmgr" target="_blank">Войти на сервер</a><br>
		• <a class="navlink" href="https://itunesconnect.apple.com" target="_blank">iTunes Connect</a><br>
		• <a class="navlink" href="https://play.google.com/apps/publish" target="_blank">Консоль Google Play</a><br>
	<form id="form" name="form" method="post" action="">
		<p class="ft_normal" style="margin-top:10px;"><input type="submit" value="Выйти" class="btn_navigation_exit"/></p>
	</form>
	
	</td>
  </tr> 
</table>

<p class="ft_normal" align="center" style="margin-top:30px"><span id="btn_TENANT" class="btn_navigation_white" onClick="load(0, 'TENANT')">Соискатели</span> <span id="btn_REALTOR" class="btn_navigation_white" onClick="load(0, 'REALTOR')">Риелторы</span> <span id="btn_CODES" class="btn_navigation_white" onClick="load(0, 'CODES')">Запросы кодов</span> <span id="btn_SUBSCRIPTIONS" class="btn_navigation_white" onClick="load(0, 'SUBSCRIPTIONS')">Подписки</span> <span class="btn_navigation_white">Push рассылка</span> <span id="btn_STATS" class="btn_navigation_white" onClick="showStats()">Статистика</span></p>

<div class="ft_caption" id="content" align="center" style="margin-top:40px;"></div>
<div id="btn_more" align="center" style="margin-top:40px; display:none;"></div>

html;

} else {

	// ведем лог доступа в админ панель при неуспешном входе	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$bad_login = $_POST['s_login'];
		$bad_pass = $_POST['s_pass'];

		if (trim("$bad_login$bad_pass") != '') {
			$fp = fopen("log.txt","r+");
			flock($fp, 1);
			fseek($fp, 0, SEEK_END);
			fputs($fp, date("[d.m.Y H:i:s]")." $bad_login / $bad_pass / ".$_SERVER['REMOTE_ADDR']." / ".$_SERVER['HTTP_USER_AGENT']."\n");
			flock($fp, 3);
			fclose($fp);
		}
	}
	
$body = <<<html
<div align="center">
	<form id="form" name="form" method="post" action="">
			<div style="width: 240px;">
				<p class="ft_normal" align="left">Введите логин<br><input type="text" name="s_login" class="edits_login"/></p>
				<br>
				<p class="ft_normal" align="left">Введите пароль<br><input type="password" name="s_pass" class="edits_login"/></p>
				<br>
				<p class="ft_normal"><input type="submit" value="Войти" class="btn_green"/></p>
			</div>
			<br>
	</form>
</div>

html;

}

print $body;

?>

</body>
</html>