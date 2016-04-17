
var currentPos = 0;
var currentMode = 'TENANT';
var currentAction = 'loadusers';

function onshowStats(data) {

	var obj = jQuery.parseJSON(data);
	
	$("#content").html('Пользователей онлайн: ' + obj.online + ', всего: ' + obj.total + '<br>' + obj.installs + '<br><br><b>Статистика посещаемости за последние 30 дней</b><br>' + obj.days);
}

function showStats() {

	if ($('#btn_STATS').attr('class') == 'btn_navigation_green') return;
	
	$('#btn_TENANT, #btn_REALTOR, #btn_CODES, #btn_SUBSCRIPTIONS').attr('class', 'btn_navigation_white');
	$('#btn_STATS').attr('class', 'btn_navigation_green');
	
	$("#btn_more").hide();
	$("#content").html('<img src="../../img/ajax.gif"/>');
	
	$.post(postFile, {action: 'statistics', hash: passHash}, onshowStats);
}

function selectNavigationButton(mode) {
	$('#btn_TENANT, #btn_REALTOR, #btn_CODES, #btn_STATS, #btn_SUBSCRIPTIONS').attr('class', 'btn_navigation_white');
	$('#btn_' + mode).attr('class', 'btn_navigation_green');
}

function onDeleteUser(data) {

	if (data == 'ERROR') {
		alert('Ошибка!');
		return;
	}
	
	$('#delete_' + data).attr('src', 'img/icon_success.png');
}

function deleteUser(userId) {

	if ($('#delete_' + userId).attr('src') != 'img/btn_delete.png') return;

	var returnValue = confirm("Удалить " + userId + '?');
    if (!returnValue) return;
    
    $('#delete_' + userId).attr('src', '../../img/ajax.gif');
    
    $.post(postFile, {action: 'deleteuser', userid: userId, hash: passHash}, onDeleteUser);
}

function load(startPos, mode) {

	if (mode == 'TENANT' && $('#btn_TENANT').attr('class') == 'btn_navigation_green') return;
	if (mode == 'REALTOR' && $('#btn_REALTOR').attr('class') == 'btn_navigation_green') return;
	if (mode == 'CODES' && $('#btn_CODES').attr('class') == 'btn_navigation_green') return;
	if (mode == 'SUBSCRIPTIONS' && $('#btn_SUBSCRIPTIONS').attr('class') == 'btn_navigation_green') return;

	currentPos = startPos;
	currentMode = mode;
	selectNavigationButton(mode);
	
	$("#btn_more").hide();
	$("#content").html('<img src="../../img/ajax.gif"/>');
	
	if (mode == 'TENANT' | mode == 'REALTOR') currentAction = 'loadusers';
	if (mode == 'CODES') currentAction = 'loadcodes';
	if (mode == 'SUBSCRIPTIONS') currentAction = 'loadsubs';
	
	$.post(postFile, {action: currentAction, mode: currentMode, pos: currentPos, hash: passHash}, onLoad);
}

function loadMore() {
	$("#btn_more").html('<img src="../../img/ajax.gif"/>');
	$.post(postFile, {action: currentAction, mode: currentMode, pos: currentPos, hash: passHash}, onLoad);
}

function onLoad(data) {

	$("#btn_more").html('<span class="btn_navigation_white" onClick="loadMore()">Показать еще</span>').hide();

	var obj = jQuery.parseJSON(data);
	if (obj.task == 'loadusers') taskUsers(obj);
	if (obj.task == 'loadcodes') taskCodes(obj);
	if (obj.task == 'loadsubs') taskSubs(obj);
}

function taskUsers(obj) {

	if (obj.count == 0) {
		$("#content").html('Нет ни одного зарегистрированного ' + (currentMode == 'TENANT' ? 'соискателя' : 'риелтора'));
		return;
	}
	
	if	(currentPos == 0) $("#content").html('Зарегистрированные ' + (currentMode == 'TENANT' ? 'соискатели' : 'риелторы') +' (' + obj.count + ' чел.)' +
	
			'<div class="linebody" style="background:#53B90F; margin-top:30px;">' +
				'<div class="usertop" style="width:40px">#</div>' +
				'<div class="usertop" style="width:150px">Телефон<br>ID</div>' +
				'<div class="usertop" style="width:80px">Широта<br>долгота</div>' +
				'<div class="usertop" style="width:120px">Регистрация<br>дата входа</div>' +
				'<div class="usertop" style="width:60px">Оценка</div>' +
				'<div class="usertop" style="width:100px">Версия, ОС<br>устройство</div>' +
				'<div class="usertop" style="width:120px">Статус</div>' +
				'<div class="usertop" style="width:120px">Дата начала и<br>отмены поиска</div>' +
				'<div class="usertop" style="width:120px">Кол-во комнат<br>период</div>' +
				'<div class="usertop" style="width:120px">Цена за квартиру<br>комиссия</div>' +
				'<div class="usertop" style="width:200px">Район поиска</div>' +
				'<div class="usertop" style="width:20px">&nbsp;</div>' +
				'<div class="usertop" style="width:48px">&nbsp;</div>' +
				'<div class="usertop" style="width:32px">&nbsp;</div>' +
			'</div>'
		
	);
	
	for (i = 0; i < obj.users.length; i++) {
	
		var coordinates = obj.users[i].latitude == 0 ? '&nbsp;' : '<a href="http://google.ru/search?q='+ obj.users[i].latitude + '+' + obj.users[i].longitude + '" target="_blank">' + obj.users[i].latitude + '<br>' + obj.users[i].longitude + '</a>';
	
		var userPhone = obj.users[i].phone == '' ? '' : '+7' + obj.users[i].phone;
	
		$("#content").html( $("#content").html() + 
	
			'<div class="linebody">' +
				'<div class="user" style="width:40px">' + (i+1+currentPos) + '</div>' +
				'<div class="user" style="width:150px" title="IP адрес: ' + obj.users[i].ip + '"><b>' + userPhone + '</b><br> ' + obj.users[i].userid + '</div>' +
				'<div class="user" style="width:80px">' + coordinates + '</div>' +
				'<div class="user" style="width:120px">' + obj.users[i].regtime + '<br>' + obj.users[i].entertime + '</div>' +
				'<div class="user" style="width:60px">' + obj.users[i].stars + '</div>' +
				'<div class="user" style="width:100px" title="' + obj.users[i].token + '\n' + obj.users[i].notify + '">v.' + obj.users[i].appversion + '<br><a href="http://google.ru/search?q='+ obj.users[i].device + '" target="_blank">' + obj.users[i].os + ' ' + obj.users[i].osversion + '</a></div>' +
				'<div class="user" style="width:120px">' + obj.users[i].searching + '</div>' +
				'<div class="user" style="width:120px">' + obj.users[i].searchtime + '<br>' + obj.users[i].searchcanceltime + '</div>' +
				'<div class="user" style="width:120px">' + obj.users[i].rooms + '<br>' + obj.users[i].renttime + '</div>' +
				'<div class="user" style="width:120px">' + obj.users[i].flatcost + '<br>' + obj.users[i].realtorcost + '</div>' +
				'<div class="user" style="width:200px" title="' + obj.users[i].comment + '"><a href="http://google.ru/search?q='+ obj.users[i].regionlatitude + '+' + obj.users[i].regionlongitude + '" target="_blank">&nbsp;' + obj.users[i].region + '</a></div>' +	
				'<div class="usertop" style="width:20px">&nbsp;</div>' +
				'<div class="user" style="width:48px"><img id="ban_' + obj.users[i].userid + '" src="img/btn_switch_on.png" onClick="banUser(&quot;' + obj.users[i].userid + '&quot;)" style="cursor:pointer"/></div>' +
				'<div class="user" style="width:32px"><img id="delete_' + obj.users[i].userid + '" src="img/btn_delete.png" onClick="deleteUser(&quot;' + obj.users[i].userid + '&quot;)" style="cursor:pointer"/></div>' +
			'</div>'
		
		);

	}
	
	currentPos = currentPos + obj.users.length;
	if (obj.count > currentPos) $("#btn_more").show();
}

	function taskCodes(obj) {

		if (obj.count == 0) {
			$("#content").html('Нет ни одного запрошенного кода');
			return;
		}
		
		if	(currentPos == 0) $("#content").html('Запрошенные коды ' + '(' + obj.count + ' шт.)' +
		
				'<div class="linebody" style="background:#53B90F; margin-top:30px; width:1020px;">' +
					'<div class="usertop" style="width:40px">#</div>' +
					'<div class="usertop" style="width:100px">Телефон</div>' +
					'<div class="usertop" style="width:80px">Код</div>' +
					'<div class="usertop" style="width:100px">Активиран</div>' +
					'<div class="usertop" style="width:100px">Старый телефон</div>' +
					'<div class="usertop" style="width:120px">Дата запроса и активации</div>' +
					'<div class="usertop" style="width:120px">ОС</div>' +
					'<div class="usertop" style="width:240px">Устройство</div>' +
					'<div class="usertop" style="width:120px">IP</div>' +
				'</div>'
			
		);
		
		for (i = 0; i < obj.codes.length; i++) {
		
			var activated = obj.codes[i].activated == 'NO' ? '<b>НЕТ</b>' : 'Да';

			$("#content").html( $("#content").html() + 
		
				'<div class="linebody" style="width:1020px">' +
					'<div class="user" style="width:40px">' + (i+1+currentPos) + '</div>' +
					'<div class="user" style="width:100px">+7' + obj.codes[i].phone + '</div>' +
					'<div class="user" style="width:80px">' + obj.codes[i].codeid + '</div>' +
					'<div class="user" style="width:100px">' + activated + '</div>' +
					'<div class="user" style="width:100px">' + obj.codes[i].oldphone + '</div>' +
					'<div class="user" style="width:120px">' + obj.codes[i].requesttime + '<br>' + obj.codes[i].activatetime + '</div>' +
					'<div class="user" style="width:120px">' + obj.codes[i].os + ' ' + obj.codes[i].osversion + '</div>' +
					'<div class="user" style="width:240px">' + obj.codes[i].device + '</div>' +
					'<div class="user" style="width:120px"><a href="http://whatismyipaddress.com/ip/'+ obj.codes[i].ip + '" target="_blank">' + obj.codes[i].ip + '</a></div>' +	
				'</div>'
			
			);

		}
		
		currentPos = currentPos + obj.codes.length;
		if (obj.count > currentPos) $("#btn_more").show();
	}

function taskSubs(obj) {

		if (obj.count == 0) {
			$("#content").html('Нет ни одной активной подписки');
			return;
		}
		
		if	(currentPos == 0) $("#content").html('Активные подписки ' + '(' + obj.count + ' шт.)' +
		
				'<div class="linebody" style="background:#53B90F; margin-top:30px; width:680px">' +
					'<div class="usertop" style="width:40px">#</div>' +
					'<div class="usertop" style="width:100px">ID</div>' +
					'<div class="usertop" style="width:120px">Дата оплаты</div>' +
					'<div class="usertop" style="width:120px">Дата окончания</div>' +
					'<div class="usertop" style="width:200px">Описание</div>' +
					'<div class="usertop" style="width:100px">Продление</div>' +
				'</div>'
			
		);
		
		for (i = 0; i < obj.subs.length; i++) {

			$("#content").html( $("#content").html() + 
		
				'<div class="linebody" style="width:680px">' +
					'<div class="user" style="width:40px">' + (i+1+currentPos) + '</div>' +
					'<div class="user" style="width:100px"><b>' + obj.subs[i].userid + '</b></div>' +
					'<div class="user" style="width:120px">' + obj.subs[i].paymentdate + '</div>' +
					'<div class="user" style="width:120px">' + obj.subs[i].expiredate + '</div>' +
					'<div class="user" style="width:200px">' + obj.subs[i].desc + '</div>' +
					'<div class="user" style="width:100px">' + obj.subs[i].times + '</div>' +
				'</div>'
			
			);

		}
		
		currentPos = currentPos + obj.subs.length;
		if (obj.count > currentPos) $("#btn_more").show();
	}


