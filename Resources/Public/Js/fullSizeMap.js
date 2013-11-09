var mapIsInitialised = false;
var scrollPosition = 0;
var defaultOverflow = 'auto';

jQuery('.js_toogleMapContainer').click(function() {

	defaultOverflow = jQuery("html").css('overflow');

	jQuery("html").css("overflow", 'hidden');

	scrollPosition = jQuery(window).scrollTop();
	if(mapIsInitialised === false) {
		resizeMap(true);
		buildMap();
	}
	extGoogleMap.setOptions({scrollwheel: false});
	jQuery('.js_fullSizeMapContainer').show();
	jQuery('.sliderImg').css({
		"top": (jQuery('.js_themeTree').outerHeight()-jQuery('.js_sliderImg').outerHeight())/2 + 'px'
	});
	jQuery('.js_fullScreeButton').show();
	jQuery(window).scrollTop(0);
	mapIsInitialised = true;

});

/**
 * slide theme tree inside visible part
 */
function showThemeTree() {
	var currentSiteOffset = jQuery('.extgmaps').offset();
	jQuery('.treeSlider').animate({
		'left': jQuery(window).width() - currentSiteOffset.left + 255
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
		'left': jQuery(window).width() - currentSiteOffset.left + 100
	}, 200, 'swing', function() {
		jQuery('.js_arrow_left').hide();
		jQuery('.js_arrow_right').show();
	});
}

var sliderVisible = true;
jQuery('.js_sliderImg').click(function() {
	if(sliderVisible) {
		hideThemeTree();
		sliderVisible = false;
	} else {
		showThemeTree();
		sliderVisible = true;
	}
});

// hide google map and allow scrolling
jQuery('.js_fullScreeButton').click(function() {
	jQuery(window).scrollTop(scrollPosition);
	jQuery('.js_fullScreeButton').hide();
	jQuery('.js_fullSizeMapContainer').hide();
	extGoogleMap.setOptions({scrollwheel: true});
	jQuery("html").css("overflow", defaultOverflow);
});

jQuery(window).resize(function() {
	resizeMap(false);
});

/**
 * resize google map for actual browser size
 * @param firstCall
 */
function resizeMap(firstCall) {
	var currentSiteOffset = jQuery('.extgmaps').offset();

	jQuery('.js_fullScreeButton,.js_treeSlider').css({
		"left": jQuery(window).width() - currentSiteOffset.left + 100
	}).show();

	jQuery('#js_extGMaps').css({
		"width": jQuery(window).width(),
		"height": jQuery(window).height(),
		"left": -currentSiteOffset.left,
		"top": -currentSiteOffset.top
	});

	if(!firstCall) {
		google.maps.event.trigger(extGoogleMap, "resize");
	}
}