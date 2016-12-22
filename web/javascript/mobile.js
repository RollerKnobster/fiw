$(document).ready(function(){

	// плавний якір
	$('.m-nav li').on('click', 'a', function(event){
		event.preventDefault();

		var menuId = $(this).attr('href') 
		var top = $(menuId).offset().top;

		$('body, html').animate({scrollTop: top}, 1500);
	});
	// end плавний якір

	// header-menu
	$('.menu-open').click(function(){
		$('.m-menu').addClass('m-menu-active');
		$('.menu-open').addClass('m-hidden');
		$('.menu-close').removeClass('m-hidden');
	});

	$('.menu-close, a').click(function(){
		$('.m-menu').removeClass('m-menu-active');
		$('.menu-open').removeClass('m-hidden');
		$('.menu-close').addClass('m-hidden');
	});

	$('.m-nav').on('click', '.m-li', function(e){
		$(this).siblings('li').removeClass('active-li');
		$(this).addClass('active-li');
	});
	// end header-menu

	// portfolio resizer
	// end portfolio resizer

	// back btn
	$('.back-btn').click(function(){
		$('.mobile-modal').css({
			opacity: "0",
			visibility: "hidden"
		});
	});
	// end back btn

	// slider
	$('.m-slider').bxSlider();
	// end slider

	$('.m-resizer').on('click', function(){
		$('#modalPortfolio').css({
			opacity: "1",
			visibility: "visible"
		});
	});

	$('.m-portfolio-block').on('click', function(){
		var numId = $(this).parent().attr('id').replace(/[^0-9]/g, ''),
			self = $(this),
			winModal = $('#modalProject-'+numId);

		if (!numId)
			return false;

		winModal.css('opacity', '1').css('visibility', 'visible');
	});

	$('.m-box-wr').on('click', function(){
		$('#servicesModal').css({
			opacity: "1",
			visibility: "visible"
		});
	});
});