var listMarkerClusterer = null;
var extGoogleMapLoaded = false;
var infoBox = null;
var extGoogleMap;
var bounds;
var selCats = [];
var selTags = [];
var selTypes = [];
var listClusterMarkers = [];
var mapIconSmall = '/typo3conf/ext/extgmaps/Resources/Public/Images/MapCluster/bubble-green-small.png'
var mapIconMiddle = '/typo3conf/ext/extgmaps/Resources/Public/Images/MapCluster/bubble-green-middle.png'
var mapIconBig = '/typo3conf/ext/extgmaps/Resources/Public/Images/MapCluster/bubble-green-big.png'

var extGoogleMapStyles = [
	{
		url: mapIconSmall,
		height: 45,
		width: 45,
		anchor: [0, 0],
		textColor: '#ffffff',
		textSize: 10
	},
	{
		url: mapIconMiddle,
		height: 45,
		width: 45,
		anchor: [0, 0],
		textColor: '#ffffff',
		textSize: 11
	},
	{
		url: mapIconBig,
		height: 45,
		width: 45,
		anchor: [0, 0],
		textColor: '#ffffff',
		textSize: 12
	}
];

var infoBoxTemplate = jQuery('.infoBoxTemplate');
jQuery('.infoBoxTemplate').remove();

var infoBoxOptions = {
	disableAutoPan: false,
	maxWidth: 0,
//	pixelOffset: new google.maps.Size(0, 0),
	zIndex: null,
	boxClass: 'js_extGMapsInfobox extGMapsInfobox',
	closeBoxMargin: "-20px",
	closeBoxURL: "/typo3conf/ext/extgmaps/Resources/Public/Images/MapCluster/close.png",
	infoBoxClearance: new google.maps.Size(50, 50),
	isHidden: false,
	pane: "floatPane",
	enableEventPropagation: false
};


jQuery(document).ready(function() {
	buildMap();
});

/**
 *
 * @param mapType
 * @returns {*}
 */
function getMapType(mapType) {
	switch(mapType) {
		case 'SATELLITE':
			mapType = google.maps.MapTypeId.SATELLITE;
			break;
		case 'HYBRID':
			mapType = google.maps.MapTypeId.HYBRID;
			break;
		case 'TERRAIN':
			mapType = google.maps.MapTypeId.TERRAIN;
			break;
		default :
			mapType = google.maps.MapTypeId.ROADMAP;
	}
	return mapType;
}

/**
 * build the basic map over the result list
 */
function buildMap() {

	if(extGoogleMapLoaded == false) {
		var myLatLng = new google.maps.LatLng(defaultGeoCoordinates.latitude, defaultGeoCoordinates.longitude);

		bounds = new google.maps.LatLngBounds();

		var mapOptions = {};
		switch(extGMapType) {
			case 'contentMap':
			case 'singleMap':
				mapOptions = {
					zoom: 12,
					center: myLatLng,
					mapTypeId: getMapType(mapType),
					mapTypeControl: false,
					streetViewControl: false
				};
				break;
			case 'fullSizeMap':
				mapOptions = {
					zoom: 8,
					center: myLatLng,
					mapTypeId: getMapType(mapType)
				};
				break;
		}

		extGoogleMap = new google.maps.Map(document.getElementById('js_extGMaps'), mapOptions);

		infoBox = new InfoBox(infoBoxOptions);


		google.maps.event.addListenerOnce(extGoogleMap, 'idle', function() {
			setMarker();
			//do not use filter by use 'singleMap' plugin
			if (extGMapType != 'singleMap') {
				filterMarker();
			}
		});
		//listener for click on map
		google.maps.event.addListener(extGoogleMap, 'click', function() {
			infoBox.close();
		});

		if(listMarkerClusterer) {
			listMarkerClusterer.clearMarkers();
		}
		extGoogleMapLoaded = true;
	}
}

/**
 * create an markerData Object with some information needed for show marker on map
 * @param id
 * @param objElement
 * @returns {*}
 */
function getMapMarker(id, objElement) {
	var markerLatLng = new google.maps.LatLng(objElement.latitude, objElement.longitude);
	bounds.extend(markerLatLng);
	var picto = '';
	if (objElement.mapIcon != undefined) {
		picto = objElement.mapIcon;
	} else {
		picto = mapIconSmall;
	}
	var image = new google.maps.MarkerImage(picto, null, null, null, new google.maps.Size(25, 25));
	var title = (objElement.title) ? objElement.title + ' ' : '';

	mapDataJson[id].marker = new google.maps.Marker({
		position: markerLatLng,
		title: title,
		icon: image
	});

	markerData = {};

	markerData['title'] = objElement.title;
	markerData['header'] = objElement.header;
	markerData['description'] = objElement.description;
	markerData['url'] = objElement.url;
	markerData['image'] = objElement.image;
	markerData['markerLatLng'] = markerLatLng;
	markerData['id'] = id;
	console.log(markerData);
	return markerData;
}

/**
 * open infoBox on marker click and add content to infoBox
 * @param markerData
 */
function addListenerForMarker(markerData) {
	google.maps.event.addListener(mapDataJson[markerData.id].marker, 'click', function() {
		infoBoxTemplate.find('.title').html(markerData.title);
		infoBoxTemplate.find('.header').html(markerData.header);
		infoBoxTemplate.find('.image').html(markerData.image);
		infoBoxTemplate.find('.description').html(markerData.description);
		if (markerData.url != undefined) {
			infoBoxTemplate.find('.detailLink').attr('href',markerData.url);
		} else {
			infoBoxTemplate.find('.detailLink').remove();
		}

		extGoogleMap.setCenter(markerData.markerLatLng);
		infoBox.open(extGoogleMap, this);
		infoBox.setContent(infoBoxTemplate.html());
	});
}

/**
 * if listMarkerClusterer isset, clear Cluster and add new Markers,
 * else create new MarkerClusterer
 */
function doClustering() {
	if(listMarkerClusterer) {
		listMarkerClusterer.clearMarkers();
		listMarkerClusterer.addMarkers(listClusterMarkers, false);
	} else {
		listMarkerClusterer = new MarkerClusterer(extGoogleMap, listClusterMarkers, {
			styles: extGoogleMapStyles,
			gridSize: parseInt(gridSize),
			maxZoom: (extGoogleMap.mapTypes[extGoogleMap.getMapTypeId()].maxZoom - 1)
		});
	}
}

/**
 * add Markers to the map
 */
function setMarker() {
	if(extGoogleMapLoaded == true) {
		listClusterMarkers = [];
		jQuery.each(mapDataJson, function(id, objElement) {

			var markerData = getMapMarker(id, objElement);

			listClusterMarkers.push(mapDataJson[id].marker);

			addListenerForMarker(markerData);

		});
		gridSize = 40;

		extGoogleMap.fitBounds(bounds);
		if (extGoogleMap.getZoom() > 12) {
			extGoogleMap.setZoom(12);
		}
		doClustering();
	}
}
/**
 * filter marker array with selected checkboxes
 */
function filterMarker() {

	if(listClusterMarkers) {
		listMarkerClusterer.clearMarkers();
	}
	listClusterMarkers = [];

	currentBounds = extGoogleMap.getBounds();
	if(extGoogleMapLoaded == true) {
		listClusterMarkers = [];
		jQuery.each(mapDataJson, function(id, objElement) {
			var markerData = false;
			mapDataJson[id].marker.setMap(null);
			if(currentBounds.contains(mapDataJson[id].marker.position) == true) {
				markerData = getMapMarker(id, objElement);
			}
			if(mapDataJson[id].categories != null && arrayIntersect(selCats, mapDataJson[id].categories).length > 0) { //selCats.length == 0 ||
				listClusterMarkers.push(mapDataJson[id].marker);
			}
			if(mapDataJson[id].tags != null && arrayIntersect(selTags, mapDataJson[id].tags).length > 0) { //selCats.length == 0 ||
				listClusterMarkers.push(mapDataJson[id].marker);
			}
			if(mapDataJson[id].tags != null && arrayIntersect(selTypes, mapDataJson[id].categories).length > 0) { //selCats.length == 0 ||
				listClusterMarkers.push(mapDataJson[id].marker);
			}
			if(markerData) {
				addListenerForMarker(markerData);
			}

		});

		// set map to with bounds for current marker
//		extGoogleMap.fitBounds(bounds);

		doClustering();
	}
}

/**
 * arrayIntersect function
 * @param arrayToFilter
 * @param mapMarker
 * @returns {filter|*|filter|filter|filter|filter}
 */
function arrayIntersect(arrayToFilter, mapMarker) {
	return arrayToFilter.filter(function(i) {
		return mapMarker.indexOf(i) > -1;
	});
}