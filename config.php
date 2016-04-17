<?
	// доступ к базе данных MySQL
	$dbhost = "mysql.hostinger.ru"; 
	$dbname = "u919523784_arend";
	$dbuser = "u919523784_amank";
	$dbpass = "A555555a";
	
	// доступ к панели управления приложением http://arenda-app.ru/arenda_admin
	$ADMINPANEL_LOGIN = 'admin';
	$ADMINPANEL_PASSWORD = '21232f297a57a5a743894a0e4a801fc3';	// MD5 хэш пароля (admin)

	// доступ к смс шлюзу WebSMS.Ru для отправки смс с кодом подтверждения
	$WEBSMS_LOGIN = "test login";
	$WEBSMS_PASSWORD = "test pass";

	// текст новости для риелторов (опционально)
	$NEWS_REALTORS = "Уважаемый риелтор, мы запустились недавно. Возможно, в Вашем городе пока еще нет соискателей, но скоро их будет очень много. Чтобы зарабатывать еще больше, расскажите людям о приложении, например, в социальных сетях.";

	$EMAIL_ADMIN = "support@arenda-app.ru";		// почта админа для оповещения о новых вопросах пользователей и новых оплатах (подписках)

	$VERSION_IOS = "1.0";						// какая последняя версия приложения доступна в App Store?
	$VERSION_ANDROID = "1.05";					// какая последняя версия приложения доступна в Google Play?

	$SEARCH_RADIUS = 40;						// для риелторов: в радиусе сколько км искать соискателей
	$SEARCH_MAXTIME = 48;						// для НОВЫХ риелторов: за сколько последних часов показать соискателей

	$SMS_DEBUG = false;							// отправлять риелторам смс с кодом или это режим отладки
	$SMS_DEBUG_PHONE = 9173576135;				// тестовый номер телефона, на который не будет приходить смс с кодом

	$PUSH_NOTIFICATIONS = true;					// включена ли рассылка Push уведомлений (когда соискатель делает запрос)


	// проверка на запрещенные слова
	function checkWords($str) {

		$arr = array("сук", "лох", "чмо", "хер", "залу", "бля", "хуй", "пизд", "член", "пид", "гандон", "ебан", "www", "http", ".com", ".ru", ".net",".kz");
		$ii = 0;
		$is_str = str_replace(' ', '', strtolower("str_".$str)); 
		
		for ($i=0; $i<count($arr); $i++) {
			if (strpos($is_str, $arr[$i]) == true) $ii++;
		}
		
		return $ii;
	}

	// проверка SQL инъекций
	function checkSQL($str) {

		$arr = array("select", "insert", "delete", "union", "/*", "drop", "create", "into", "benchmark", "'");
		$ii = 0;
		$is_str = str_replace(' ', '', strtolower("str_".$str)); 
		
		for ($i=0; $i<count($arr); $i++) {
			if (strpos($is_str, $arr[$i]) == true) $ii++;
		}
		
		return $ii;
	}

	// запись действий в лог-файл
	function writeLog($fileName, $newLine, $text) {
		
		$fp = fopen($fileName, "r+");
		flock($fp, 1);
		fseek($fp, 0, SEEK_END);
		fputs($fp, $newLine.date("[d.m.Y H:i:s]")." ".$_SERVER['REMOTE_ADDR']." > ".$text."\n");
		flock($fp, 3);
		fclose($fp);
	}

	// подключение к базе данных
	function db_connect($dbhost, $dbuser, $dbpass, $dbname) {
		mysql_connect($dbhost, $dbuser, $dbpass)or die('error: ' . mysql_error());
		mysql_select_db($dbname);
	}

?>