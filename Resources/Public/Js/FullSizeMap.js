var mapIsInitialised = false;
var scrollPosition = 0;
var defaultOverflow = 'auto';
var mapIsVisible = false;

jQuery('.js_toogleMapContainer').click(function() {

	defaultOverflow = jQuery("html").css('overflow');

	jQuery("html").css("overflow", 'hidden');

	scrollPosition = jQuery(window).scrollTop();
	mapIsVisible = true;
	if (disableMapScrolling) {
		extGoogleMap.setOptions({scrollwheel: false});
	}
	jQuery('.js_fullSizeMapContainer').show();

	jQuery('.sliderImg').css({
		"top": (jQuery('.js_themeTree').outerHeight()-jQuery('.js_sliderImg').outerHeight())/2 + 'px'
	});

	jQuery('.js_fullScreeButton').show();

	if(mapIsInitialised === false) {
		resizeMap(true);
		buildMap();
		mapIsInitialised = true;
	} else {
		resizeMap(false);
	}

	jQuery(window).scrollTop(0);
});

// hide google map and allow scrolling
jQuery('.js_fullScreeButton').click(function() {
	jQuery(window).scrollTop(scrollPosition);
	jQuery('.js_fullScreeButton').hide();
	jQuery('.js_fullSizeMapContainer').hide();
	if (disableMapScrolling) {
		extGoogleMap.setOptions({scrollwheel: true});
	}
	jQuery("html").css("overflow", defaultOverflow);
	mapIsVisible = false;
});

jQuery(window).resize(function() {
	if (mapIsVisible) {
		resizeMap(false);
	}
});

/**
 * resize google map for actual browser size
 * @param firstCall
 */
function resizeMap(firstCall) {
	var currentSiteOffset = jQuery('.extgmaps').offset();

	if (sliderVisible) {
		jQuery('.js_fullScreeButton').css({
			"left": jQuery(window).width() - currentSiteOffset.left + 100
		}).show();
		jQuery('.js_treeSlider').css({
			"left": jQuery(window).width() - currentSiteOffset.left + 255
		}).show();
	} else {
		jQuery('.js_fullScreeButton,.js_treeSlider').css({
			"left": jQuery(window).width() - currentSiteOffset.left + 100
		}).show();

	}

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