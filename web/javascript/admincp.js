'use strict';
var empty_pic = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

$(document).ready(function(){
	$('#save-social-form').on('submit', function(e){
		e.preventDefault();

		$.post($(this).attr('action'), $(this).serialize(), function(data) {
			if (data.success)
				$('.success-block').toggle();
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

			function appearSuccessBlock() {
				$('.success-block').find('p').text('Перед добавлением фото сохраните проект!');
				$('.success-block').toggle();
			}
			appearSuccessBlock(); 
		}

		if ($('.project-slider-photoes .slider-box:not(:first)').length > 4) {
			e.preventDefault();
			e.stopPropagation();

			function appearSuccessBlock() {
				$('.success-block').find('p').text('Не больше 5 слайдов на проект!');
				$('.success-block').toggle();
			}
			appearSuccessBlock(); 
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

	/* employers */
	$('.worker-photo').on('change', function(){
		var reader = new FileReader(),
			self = $(this);
		reader.onload = function(e) {
			self.siblings('img').attr('src', e.target.result);
		}
		reader.readAsDataURL(this.files[0]);
		self.siblings('input[name=remove_pic]').remove();
	});

	$('.del-photo').on('click', function(){
		$(this).siblings('img').attr('src', empty_pic);
		$(this).before('<input type="hidden" name="remove_pic" value="1">');
	});

	$('.worker-form').on('submit', function(e){
		e.preventDefault();

		var formData = new FormData(this);
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: formData,
			dataType: 'json',
			async: false,
			success: function (data) {
				if (data.success == true) {
					function appearSuccessBlock() {
						$('.success-block').find('p').text('Данные обновлены!');
						$('.success-block').toggle();
					}
					appearSuccessBlock(); 
				} else {
					function appearSuccessBlock() {
						$('.success-block').find('p').text('Обновить не удалось!');
						$('.success-block').toggle();
					}
					appearSuccessBlock();
				}
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	$('#about-text-form').on('submit', function(e){
		e.preventDefault();

		$.post($(this).attr('action'), $(this).serialize(), function(resp){
			console.info(resp);
		}, 'json');
	});

	$('.success-close').click(function(){
		$('.success-block').css('display', 'none');
	});

	$(".project-block-wrapper").click(function(){
		var arrProjectBlocks = $(".project-block-wrapper");
		var count = 0;

		$(".project-description input").removeClass("hidden");
		$(".project-slider-photoes").removeClass("hidden");
		$(".slider-add-box").removeClass("invisible");


		if ($(this).find(".project-block-hover").hasClass("visible")) {
			$(this).find(".project-block-hover").removeClass("visible");
			
			$(".project-description input").addClass("hidden");
			$(".project-slider-photoes").addClass("hidden");
		
		} else {
			for (var i = 0; i < arrProjectBlocks.length; i++) {
				if ($(arrProjectBlocks[i]).find(".project-block-hover").hasClass("visible")) {
					count++;
				}
			}
			if (count === 0) {
				$(this).find(".project-block-hover").addClass("visible");
			} else {
				for (var i = 0; i < arrProjectBlocks.length; i++) {
					if ($(arrProjectBlocks[i]).find(".project-block-hover").hasClass("visible")) {
						$(arrProjectBlocks[i]).find(".project-block-hover").removeClass("visible");
					}
				}
				$(this).find(".project-block-hover").addClass("visible");
			}
		}

	});
});
