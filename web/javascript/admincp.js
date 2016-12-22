'use strict';

$(document).ready(function(){
	$('#save-social-form').on('submit', function(e){
		e.preventDefault();

		$.post($(this).attr('action'), $(this).serialize(), function(data) {
			if (data.success)
				$(".success-block").toggle();
			else
				alert('Saving social: error');
		}, 'json');
	});

	$('#add_slide_block').on('change', function(e){

		var formData = new FormData();
		formData.append('image', e.target.files[0])

		$.ajax({
			url: $(this).data('action'),
			type: 'POST',
			data: formData,
			dataType: 'json',
			async: false,
			success: function (data) {
				if (data.success == true) {
					var new_slide = $('.slide-block:last').clone(),
						img_src = new_slide.find('img').attr('src').replace(/\/slider\/.*$/, '/slider/'+data.img);
					new_slide.find('img').attr('src', img_src);
					$('.slide-block:last').after(new_slide);
				}
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	$('.slide-box-wrapper').on('click', '.rm-slide-btn', function(){
		var slide = $(this).data('image'),
			action = $(this).data('action'),
			self = $(this);

		if (slide !== null && action != null) {
			$.post(action, {filename: slide}, function(data){
				console.log(data);
				if (data.success == true)
					self.closest('.slide-block').remove();
			}, 'json');
		}
	});

	/* portfolio */
	$('.new-project').on('click', function(){
		$('.project-about').removeClass('hidden');
		$('.project-slider-photoes .slider-box:not(:first)').remove();
	});

	$('#slider_add_photo').on('change', function(e){

		var formData = new FormData();
		formData.append('image', e.target.files[0]);
		formData.append('portfolio_id', parseInt($('#portfolio_id').val()));

		$.ajax({
			url: $(this).data('action'),
			type: 'POST',
			data: formData,
			dataType: 'json',
			async: false,
			success: function (data) {
				if (data.success == true) {
					var slide = $('.project-slider-photoes .slider-box:first').clone();
					slide.find('img').attr('src', data.data);
					slide.removeClass('hidden');
					$('.project-slider-photoes label').before(slide);
				}
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	$('#portfolio_form').on('submit', function(e){
		e.preventDefault();

		$.post($(this).attr('action'), $(this).serialize(), function(data){
			console.alert(data);
		}, 'json');
	});

	$('.slider-add-box').on('click', function(e){
		var portfolioId = parseInt($('#portfolio_id').val());

		if (portfolioId < 1) {
			e.preventDefault();
			e.stopPropagation();
			alert('Перед добавление фото сохраните проект!');
		}

		if ($('.project-slider-photoes .slider-box:not(:first)').length > 4) {
			e.preventDefault();
			e.stopPropagation();
			alert('Не больше 5 слайдов на проект!');
		}
	});

	$('.projects-wrapper').on('click', '.project-block', function(){
		var token = $(this).data('token');

		$.post('/admin/portfolio/get_one', {token: token}, function(data){
			if(data.success) {
				var form = $('#portfolio_form')[0];
				form.id.value = data.data.id;
				form.name.value = data.data.name;
				form.price.value = data.data.price;
				form.address.value = data.data.address;
				form.director.value = data.data.director;
				$('.project-slider-photoes .slider-box:not(:first)').remove();
				if (data.data.images.length) {
					$.each(data.data.images, function(i, item){
						var slide = $('.project-slider-photoes .slider-box:first').clone();
						slide.find('img').attr('src', item);
						slide.removeClass('hidden');
						$('.project-slider-photoes label').before(slide);
					});
				}
				$('.project-about').removeClass('hidden');
			}
		}, 'json');
	});

	$('.project-slider-photoes').on('click', '.slider-box-hover', function(){
		var url = $(this).data('url'),
			portfolioId = parseInt($('#portfolio_id').val()),
			fileName = $(this).siblings('img').attr('src').replace(/.*\/([a-z0-9\.]+)$/g, '$1'),
			self = $(this);

			$('#adminModal').on('click', '#yesBtn', function(){
				$.post(url, {portfolio_id: portfolioId, filename: fileName}, function(data){
					if (data.success == true) {
						self.closest('.slider-box').remove();
					}
					$('#adminModal').off('click', '#yesBtn');
					$('#adminModal').addClass('hidden');
				}, 'json');
			});

			$('#adminModal').find('img').attr('src', $(this).siblings('img').attr('src'));
			$('#adminModal').removeClass('hidden');
	});

	$('#adminModal').on('click', '#noBtn', function(){
		$('#adminModal').off('click', '#yesBtn');
		$('#adminModal').removeClass('hidden');
	});

	$('#remove_project').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();

		var url = $(this).data('url'),
			portfolioId = parseInt($('#portfolio_id').val());

		$.post(url, {portfolio_id: portfolioId}, function(data){
			if (data.success == true) {
				location.reload();
			}
		}, 'json');
	});

	$(".success-close").click(function(){
		$(".success-block").css("display", "none");
	});

});
