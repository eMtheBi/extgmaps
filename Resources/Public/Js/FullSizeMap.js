var mapIsInitialised = false;
var scrollPosition = 0;
var defaultOverflow = 'auto';
var mapIsVisible = false;

jQuery('.js_toogleMapContainer').click(function() {

	defaultOverflow = jQuery("html").css('overflow');

	jQuery("html").css("overflow", 'hidden');

	scrollPosition = jQuery(window).scrollTop();
	mapIsVisible = true;

	jQuery('.js_fullSizeMapContainer').show();

	setArrowSliderPosition();

	jQuery('.js_fullScreeButton').show();

	if(mapIsInitialised === false) {
		resizeMap(true);
		buildMap();
		mapIsInitialised = true;
	} else {
		resizeMap(false);
	}
	if (disableMapScrolling) {
		extGoogleMap.setOptions({scrollwheel: false});
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

	currentSiteOffset = jQuery('.extgmaps').offset();
	treeSliderSlideOut = jQuery(window).width() - currentSiteOffset.left + 255;
	treeSliderSlideIn =jQuery(window).width() - currentSiteOffset.left + 100;
}

