<?php

	include_once("config.php");

	if ($_SERVER['REQUEST_METHOD'] != 'POST') return;
	if ($_POST['action'] != 'feedback') return;

	$theme  = $_POST['theme'];
	$name  = $_POST['name'];
	$email  = $_POST['email'];
	$msg  = $_POST['msg'];
				
	mail($EMAIL_ADMIN, "Вопрос на сайте", "Тема: $theme\n\n$msg",
	"From: $name <$email>\r\nContent-Type: text/plain; charset=utf-8\r\n");
	echo "OK";
?>