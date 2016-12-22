$(document).ready(function(){

	var windowWidth = $(window).width();

// Header---Header---Header---Header---Header---Header---
	$(".slider").bxSlider({
		auto: true,
		pause: 4000, 
		useCSS: false,
		// adaptiveHeight: true,
		// pagerCustom: '#slider'
		// mode: 'fade'
   		// captions: true
		// adaptiveHeightSpeed: 200,
	});

	// header-block margin
		$(window).resize(function(){
			windowWidth = $(window).width();
			headerBlockWidth = $(".header-block").width();

			if (windowWidth < 1300) {
				$(".header-block").css("margin-left", -windowWidth/2);
			} else {
				$(".header-block").css("margin-left", -headerBlockWidth / 2);
			}
		});
	// end header-block margin

	// плавний якір
	$(".menu li, .footer-menu li").on("click", "a", function(event){
		event.preventDefault();

		var idMenu = $(this).attr("href")
		
		var top = $(idMenu).offset().top;


		$("body, html").animate({scrollTop: top}, 1500);
	});
	// end плавний якір

// Header---Header---Header---Header---Header---Header---

// Portfolio---Portfolio---Portfolio---Portfolio---Portfolio---
	$(".open-projects").click(function(){
		$(".portfolio-open-projects").css("display", "none");
		$(".portfolio-block-hidden, .resizer-hidden").css("display", "block");
		$(window).trigger('resize').trigger('scroll');

		// $(".big-img").bxSlider({
		//   pagerCustom: ".small_pictures"
		// });
	});

	$(".close-projects").click(function(){
		$(".portfolio-open-projects").css("display", "block");
		$(".portfolio-block-hidden, .resizer-hidden").css("display", "none");
		$(window).trigger('resize').trigger('scroll');

		$("html, body").animate({
          	scrollTop: $(".portfolio").offset().top
    	}, 1500);
	});
// Portfolio---Portfolio---Portfolio---Portfolio---Portfolio---

// Services---Services---Services---Services---Services---

	// changing information on click 
	$(".categories-wrapper").children().click(function(){
		for (var i = 0; i < $(".categories-description").children().eq(0).children().eq(0).children().length; i++) {
			if( $(".categories-description").children().eq(0).children().eq(0).children().eq(i).hasClass($(this)[0].id + "-description") ) {
				if ($(".categories-description").children().eq(0).children().eq(0).children().eq(i).hasClass("description-hidden")) {
					$(".categories-description").children().eq(0).children().eq(0).children().eq(i).toggleClass("description-hidden");
				}
				
			} else {
				if ($(".categories-description").children().eq(0).children().eq(0).children().eq(i).hasClass("description-hidden") !== true) {
					$(".categories-description").children().eq(0).children().eq(0).children().eq(i).addClass("description-hidden");
				}
			}
		}

		$(".categories-description").children().eq(0).children().eq(0).css("top", 0);
		$(globalCustomScroll).mCustomScrollbar("update");

	});
	// changing information on click 

	// styling scroll
	
	// styling scroll

// Services---Services---Services---Services---Services---

// Contacts---Contacts---Contacts---Contacts---Contacts---
	$(".parallax-layer").parallax({
		imageSrc: 'image/parallax/parallax.png'
	});

	// up-btn
		$(window).scroll(function(){
			if ($(this).scrollTop() > 500) {
			$('.scrollup').fadeIn();
			} else {
			$('.scrollup').fadeOut();
			}
			});
			 
			$('.scrollup').click(function(){
			$("html, body").animate({ scrollTop: 0 }, 1000);
			return false;
			});
	// end up-btn

	// modal slider
	// $(".big-img").bxSlider({
	//   pagerCustom: ".small_pictures"
	// });
	// end modal slider
	// Contacts---Contacts---Contacts---Contacts---Contacts---

	// MODAL WINDOW------------------
	var modalWindow = $(".modal");

	var portfolioBlock = $(".portfolio-block");

	var closeModal = $(".close-btn");


	$('.portfolio-block').on('click', function(){
		var idNum = $(this).parent().attr('id').replace(/[^0-9]/g, ''),
			self = $(this),
			modalWin = $('#myModal-'+idNum);

		if (!idNum) 
			return false;

		if (!self.data('initialized')){

			$('.big-img', modalWin).bxSlider({
			  pagerCustom: '#modal-slider-'+idNum
			});

			self.data('initialized', 1);
		}

		modalWin.css('opacity', '1').css('visibility', 'visible');
	});


	closeModal.click(function(){
		modalWindow.css("opacity", "0").css("visibility", "hidden");
	});

	$(window).click(function(event){
		//console.log(event);
		var arr = $(".modal-pic");

		
		var ifFind = false;
		var parentEls = $(event.toElement).parents().map(function() {
			//console.log(this);
			if ($(this).hasClass("modal-pic")) {
				ifFind = true;
			}
		    // return this.tagName;
		});

		if(!ifFind) {
			for (var i=0; i<arr.length; i++) {
				if ($(arr[i]).css("visibility") == "visible") {
					$(arr[i]).css("opacity", "0").css("visibility", "hidden");
				}
			}
		}
	// END MODAL WINDOW-----------------
	});

});

