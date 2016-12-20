$(document).ready(function(){

	// $(".slider-box-hover, .slide-box-hover").click(function(){
	// 	$(this).toggleClass("block-hover-active");
	// });

	$(".success-close").click(function(){
		$(".success-block").css("display", "none");
	});

	// MAIN PAGE--------------------------------------------------------------

		$(".slide-block").on('click', function(){
			$(this).children().css("height", "98%").css("width", "98%");
			$(this).children().children().eq(1).addClass("visible");
			$(this).addClass("remove");
		});

		// add slide-box div
		$(".add-slide-block").click(function(){

			var countOfImageBlocks = $(".slide-block").length;

			$(".slide-box-wrapper").children().eq(countOfImageBlocks-1).after($('<div class="slide-block"><div class="slide-box"><img src="s" alt=""><div class="slide-box-hover"><div class="box-hover-icon icon-big"></div></div></div></div>'));
		});
		// add slide-box div

	// MAIN PAGE--------------------------------------------------------------

	// PORTFOLIO PAGE--------------------------------------------------------

		// add project-block div 
		$(".add-project-block").click(function(){
			
			var countOfProjectBlocks = $(".project-block-wrapper").length;
			
			$(".wr").children().eq(countOfProjectBlocks-1).after($('<div class="project-block-wrapper"><div class="project-block"><img src="s" alt=""><div class="project-block-hover"><div class="project-block-icon"></div></div></div></div>'));
		});
		// add project-block div 

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

		// PROJECT SLIDER PHOTOES
		$(".slider-box").click(function(){
			$(this).find(".slider-box-hover").toggleClass("visible");
		});

		var countOfSliderBoxes = $(".slider-box").length;

		if (countOfSliderBoxes >= 5) {
			$(".slider-add-box").css("display", "none");
		} else {
			$(".slider-add-box").css("display", "inline-block");
		}
		
		$(".slider-add-box").click(function(){
			$(".project-slider-photoes").children().eq(countOfSliderBoxes-1).after($('<div class="slider-box"><img src="" alt="slider-photo"><div class="slider-box-hover"><div class="box-hover-icon"></div></div></div>'));
		});

		// PROJECT SLIDER PHOTOES

	// PORTFOLIO PAGE-------------------------------------------------------

	// MODAL WINDOW---------------------------------------------------------

	// Get the modal
	var modalAdmin = $("#adminModal");

	// Get the button that opens the modal
	var modalBtn1 = $(".slide-block, .slider-box");

	// When the user clicks on the button, open the modal 
	$(modalBtn1).click(function(){
		$(".admin-modal").removeClass("hidden");
	});

	// MODAL WINDOW---------------------------------------------------------

	$("#yesBtn").click(function(){
		// $(".slide-box-wrapper").remove($(".slide-block").hasClass("remove"));
		$(".admin-modal").addClass("hidden");
		$(".slide-box-hover, .slider-box-hover").removeClass("visible");
	});

	$("#noBtn").click(function(){
		$(".admin-modal").addClass("hidden");
		$(".slide-box-hover, .slider-box-hover").removeClass("visible");
	});
});