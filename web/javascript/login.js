'use strict';

$(document).ready(function(){

	$('#auth-form').on('submit', function(e){
		e.preventDefault();
		var self = $(this);

		$('p.error').remove();

		$.post(self.attr('action'), self.serialize(), function(resp){
			if (resp.success == true)
				location.href=location.href.replace(/\/login/, '');
			else
				self.after('<p class="error" style="text-align:center;">Неверный логин или пароль</p>');
		}, 'json');
	});

});