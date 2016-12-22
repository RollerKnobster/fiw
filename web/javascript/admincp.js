'use strict';

$(document).ready(function(){
	$('#save-social-form').on('submit', function(e){
		e.preventDefault();

		$.post($(this).attr('action'), $(this).serialize(), function(data) {
			if (data.success)
				// alert('Saving social: success');
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

	// submit message window
	$(".success-close").click(function(){
		$(".success-block").css("display", "none");
	});
});