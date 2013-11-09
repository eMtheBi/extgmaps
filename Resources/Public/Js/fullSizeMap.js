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

	extGoogleMap.setOptions({scrollwheel:false});

	jQuery('.js_fullSizeMapContainer').show();
	jQuery('.js_fullScreeButton').show();
	jQuery(window).scrollTop(0);
	mapIsInitialised = true;
});

jQuery('.js_fullScreeButton').click(function() {
	jQuery(window).scrollTop(scrollPosition);
	jQuery('.js_fullScreeButton').hide();
	jQuery('.js_fullSizeMapContainer').hide();
	extGoogleMap.setOptions({scrollwheel:true});
	jQuery("html").css("overflow", defaultOverflow);
});

jQuery(window).resize(function() {
	resizeMap(false);
});



function resizeMap(firstCall) {
	var currentSiteOffset = jQuery('.extgmaps').offset();

	jQuery('.js_fullScreeButton,.themeTree').css({
		"left": jQuery(window).width()-340
	}).show();

	jQuery('#js_extGMaps').css({
		"width":jQuery(window).width(),
		"height":jQuery(window).height(),
		"left": -currentSiteOffset.left,
		"top": -currentSiteOffset.top
	});

	if (!firstCall) {
		google.maps.event.trigger(extGoogleMap, "resize");
	}
}