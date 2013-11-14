/**
 * set position top for div.sliderImg
 */
function setArrowSliderPosition() {
	jQuery('.sliderImg').css({
		"top": (jQuery('.js_themeTree').outerHeight()-jQuery('.js_sliderImg').outerHeight())/2 + 'px'
	});
}

/**
 * slide theme tree inside visible part
 */
function showThemeTree() {
	var currentSiteOffset = jQuery('.extgmaps').offset();
	jQuery('.treeSlider').animate({
		'left': treeSliderSlideOut
	}, 200, 'swing', function() {
		jQuery('.js_arrow_left').show();
		jQuery('.js_arrow_right').hide();
	});
}

/**
 * slide theme tree outside visible part
 */
function hideThemeTree() {
	var currentSiteOffset = jQuery('.extgmaps').offset();
	jQuery('.treeSlider').animate({
		'left': treeSliderSlideIn
	}, 200, 'swing', function() {
		jQuery('.js_arrow_left').hide();
		jQuery('.js_arrow_right').show();
	});
}

var sliderVisible = false;
jQuery('.js_sliderImg').click(function() {
	if(sliderVisible) {
		hideThemeTree();
		sliderVisible = false;
	} else {
		showThemeTree();
		sliderVisible = true;
	}
});