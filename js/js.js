	function GetURLParameter(sParam) {
	    var sPageURL = window.location.search.substring(1);
	    var sURLVariables = sPageURL.split('&');
	    
	    for (var i = 0; i < sURLVariables.length; i++) {
	        var sParameterName = sURLVariables[i].split('=');
	        if (sParameterName[0] == sParam) return sParameterName[1];
	    }
	}

	var requiredField = 'Необходимое поле';

	$(document).ready(function () {

		var technical = GetURLParameter('support');
		if (technical == 'yes') $('#support').arcticmodal();

		$('#support_name, #support_email, #support_msg').val(requiredField);
		$('#support_name, #support_email, #support_msg').css("font-style", "italic");
		$('#support_name, #support_email, #support_msg').css("color", "#999");

		$('#support_name, #support_email, #support_msg').focus(function() {
			$(this).css("background","#fff");
		});
	 
		$('#support_name, #support_email, #support_msg').focus(function() {
			$(this).css("color", "#666");
			$(this).css("background", "#fff");
			
			if ($(this).val() == requiredField) {
				$(this).val('');
				$(this).css("font-style", "normal");
			}
			
		}).blur(function() {
	  		if ($(this).val() == '') {
	  			$(this).val(requiredField);
	  			$(this).css("font-style", "italic");
	  			$(this).css("color", "#999");
	  		}
		}) 

		$('a[href*=#]').bind("click", function(e){
			var anchor = $(this);
			$('html, body').stop().animate({scrollTop: $(anchor.attr('href')).offset().top}, 1000);
			e.preventDefault();
		}); 

	});

	function support() {
		$('#support_loader').hide();
		$('#support_ok').hide();
		$('#support_forms').show();
		$('#support_btn').show();
		$('#support').arcticmodal();
	}

	function isEmail(sEmail) {
		var a = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
		return a.test(sEmail);
	}

	function onFeedback(data) {
		if (data != "OK") return;

		$('#support_name, #support_email, #support_msg').val(requiredField);
		$('#support_theme').val('');
		$('#support_name, #support_email, #support_msg').css("font-style", "italic");
		$('#support_name, #support_email, #support_msg').css("color", "#999");
		$('#support_loader').hide();
		$('#support_ok').fadeIn('fast');
	}

	function Feedback() {
		var userName = $('#support_name').val();
		var userEmail = $('#support_email').val();
		var userTheme = $('#support_theme').val();
		var userMsg = $('#support_msg').val();
		var feedbackError = false;
	 
		if ($.trim(userName) == '') {
			$('#support_name').css("background","#FFD7D7");
			feedbackError = true;
		}
		 
		if ($('#support_name').css('font-style') == 'italic') {
			$('#support_name').css("background","#FFD7D7"); 
			feedbackError = true;
		}
		
		if (!isEmail(userEmail)) {
			$('#support_email').css("background","#FFD7D7"); 
			feedbackError = true;
		}
		
		if ($.trim(userMsg) == '') {
			$('#support_msg').css("background","#FFD7D7"); 
			feedbackError = true;
		}
		
		if ($('#support_msg').css('font-style') == 'italic') {
			$('#support_msg').css("background","#FFD7D7"); 
			feedbackError = true;
		}
	 
		if (feedbackError) return;
		
		$('#support_btn, #support_forms').hide();
		$('#support_loader').fadeIn('fast');
		
		$.post("feedback.php", {action: 'feedback', 
								name: userName, 
								email: userEmail,
								theme: userTheme, 
								msg: userMsg}, onFeedback);
	}
