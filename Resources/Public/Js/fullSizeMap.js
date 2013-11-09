var mapIsInitialised = false;
jQuery('.js_toogleMapContainer').click(function() {

	if(mapIsInitialised === false) {
		resizeMap(true);
		buildMap();
	}
	jQuery('.js_fullSizeMapContainer').fadeIn();
	jQuery('.js_fullScreeButton').fadeIn();
	mapIsInitialised = true;
});


jQuery('.js_fullScreeButton').click(function() {
	jQuery('.js_fullScreeButton').fadeOut();
	jQuery('.js_fullSizeMapContainer').fadeOut();
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