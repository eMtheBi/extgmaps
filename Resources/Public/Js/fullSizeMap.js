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
//	catdiv.fadeOut();
	jQuery('.js_fullScreeButton').fadeOut();
	jQuery('.js_fullSizeMapContainer').fadeOut();
});

jQuery(window).resize(function() {
	resizeMap(false);
});

function resizeMap(firstCall) {
	var currentSiteOffset = jQuery('.extgmaps').offset();

	jQuery('.js_fullScreeButton ').css({
		"left": jQuery(window).width()-currentSiteOffset.left-180
	}).show();

	jQuery('.themeTree ').css({
		"left": jQuery(window).width()-currentSiteOffset.left-180
	});


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