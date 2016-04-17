<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Arenda - аренда квартир, снять квартиру</title>
<link rel="stylesheet" href="img/style.css">
<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico"/>
<link rel="image_src" href="img/logo.png"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="js/modal.js"></script>
<script src="js/js.js"></script>
<link rel="stylesheet" href="js/modal.css">
</head>
<body>

<div style="display: none;">
<img src="img/logo.png"/>
</div>

<div style="display: none;">
<div class="box-modal" id="support">
<div class="box-modal_close arcticmodal-close"><img src="img/btn_close.png" class="btn"/></div>

<table id="support_forms" width="700" border="0" align="center" cellpadding="0" cellspacing="0">
 <tr>
    <td width="200" height="60">
    <p align="right" class="font_support">Ваше имя :</p></td>
    <td width="500"><input id="support_name" type="text" class="edit_main"/></td>
  </tr>
 <tr>
    <td width="200" height="60">
    <p align="right" class="font_support">Ваш Email :</p></td>
    <td width="500"><input id="support_email" type="text" class="edit_main"/></td>
  </tr>
  <tr>
    <td width="200" height="60">
    <p align="right" class="font_support">Тема сообщения :</p></td>
    <td width="500"><input id="support_theme" type="text" value="" class="edit_main"/></td>
  </tr>   
   
  <tr>
    <td width="200" height="190">
    <p align="right" class="font_support">Текст :</p></td>
    <td width="500"><textarea id="support_msg" name="textarea" class="edit_text" maxlength="500"></textarea></td>
  </tr>   
</table>
<p id="support_btn" align="center"><img onClick="Feedback()" src="img/btn_send.png" class="btn"/></p>
<p id="support_loader" align="center" style="display:none;"><img src="img/ajax.gif"/></p>
<p id="support_ok" align="center" style="display:none;"><img src="img/img_ok.png"/></p>
</div>
</div>

<table border="0" align="center" cellpadding="0" cellspacing="0">
 <tr>
    <td width="400" valign="middle">
    <img src="img/screen_main.png"/>
	</td>
    <td width="600" valign="top">
	<p align="center" class="font_normal" style="margin-top:40px;"><img src="img/logo.png"/></p>
	<p align="center" class="font_big" style="margin-top:40px;">Хотите быстро, удобно и<br>выгодно снять квартиру?</p>
	<p align="center" class="font_normal" style="margin-top:30px;"><a href="appstore" target="_blank"><img src="img/btn_download_ios.png" class="btn"/></a><br><a href="googleplay" target="_blank"><img src="img/btn_download_android.png" class="btn"/></a></p>
	<p align="center" class="font_bold" style="margin-top:6px;">Просто установите БЕСПЛАТНОЕ приложение</p>
	<p align="center" class="font_big" style="margin-top:20px;">Как это работает?</p>
	<p align="center" class="font_normal"><a href="#tenant" target="_blank"><img src="img/arrow.png" class="btn"/></a></p>
	</td>
	<td width="20"></td>
  </tr> 
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:30px;"><tr><td width="100%" bgcolor="#53B90F">

<table id="tenant" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:30px; margin-bottom:30px;">
 <tr>
 	<td width="20"></td>
    <td width="600" valign="top">
	<p align="center" class="font_normal"><img src="img/icon_tenant.png"/></p>
	<p align="center" class="font_big_white" style="margin-top:10px;">Если Вы ищете жильё</p>
	<p align="left" class="font_normal_white" style="margin-top:20px;">• Вам больше не нужно просматривать в интернете объявления квартир, фотографии и цены которых очень часто нереальны, лично обзванивать и платить риелторам от 50% до 100%.</p>
	<p align="left" class="font_normal_white" style="margin-top:20px;">• Достаточно запустить приложение Arenda и кинуть клич на весь город так, что сотни риелторов моментально получат уведомление об этом, при этом Вы сами устанавливаете максимальную сумму, которую готовы заплатить им. За счет такой конкуренции между риелторами Вы можете намного выгоднее снять жилье!</p>
	<p align="left" class="font_normal_white" style="margin-top:20px;">• Как только Вы найдете жильё, Вас больше не будут беспокоить звонками.</p>
	</td>
    <td width="400" valign="middle">
    <img src="img/screen_user.png"/>
	</td>
  </tr> 
</table>

</td></tr> </table>

<table id="realtor" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:30px;">
 <tr>
    <td width="400" valign="middle">
    <img src="img/screen_realtor.png"/>
	</td>
	<td width="600" valign="top">
	<p align="center" class="font_normal" style="margin-top:40px;"><img src="img/icon_realtor.png"/></p>
	<p align="center" class="font_big" style="margin-top:10px;">Вы работаете риелтором?<br>Или хотите дополнительно зарабатывать?</p>
	<p align="left" class="font_normal" style="margin-top:20px;">• Благодаря приложению Вы моментально узнаете о каждом новом соискателе жилья в Вашем городе.</p>
	<p align="left" class="font_normal" style="margin-top:20px;">• Это сотни или тысячи соискателей ежедневно, которых Вам не нужно искать.</p>
	<p align="left" class="font_normal" style="margin-top:20px;">• Вы даже можете совмещать эту работу с Вашей основной работой и зарабатывать еще больше.</p>
	</td>
	<td width="20"></td>
  </tr> 
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:30px;"><tr><td width="100%" bgcolor="#111">

<p align="center" class="font_copyright" style="margin-top:30px;"><a href="facebook" target="_blank"><img src="img/btn_fb.png" class="btn"/></a><a href="twitter" target="_blank"><img src="img/btn_twitter.png" class="btn" style="margin-left:6px;"/></a><a href="vkontakte" target="_blank"><img src="img/btn_vk.png" class="btn" style="margin-left:6px;"/></a></p>
<p align="center" class="font_normal" style="margin-top:20px;"><img class="btn" src="img/btn_support.png" onClick="support()" style="cursor:pointer;"/></p>
<p align="center" class="font_normal" style="margin-top:10px;"><a href="agreement" target="_blank"><img class="btn" src="img/btn_agreement.png"/></a><br><br></p>

</td></tr> </table>

</body>
</html>
