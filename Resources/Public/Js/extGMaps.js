var listMarkerClusterer = [];
var extGoogleMapLoaded = [];
var infoBox = [];
var mapData = [];
var extGoogleMap = [];
var bounds;
var selCats = [];
var selTags = [];
var selTypes = [];
var listClusterMarkers = [];
var mapIconSmall = '/typo3conf/ext/extgmaps/Resources/Public/Images/MapCluster/clusterIconSmall.png'
var mapIconMiddle = '/typo3conf/ext/extgmaps/Resources/Public/Images/MapCluster/clusterIconMiddle.png'
var mapIconBig = '/typo3conf/ext/extgmaps/Resources/Public/Images/MapCluster/clusterIconBig.png'

var extGoogleMapStyles = [
	{
		url: mapIconSmall,
		height: 45,
		width: 45,
		anchor: [0, 0],
		textColor: '#000',
		textSize: 10
	},
	{
		url: mapIconMiddle,
		height: 45,
		width: 45,
		anchor: [0, 0],
		textColor: '#000',
		textSize: 11
	},
	{
		url: mapIconBig,
		height: 45,
		width: 45,
		anchor: [0, 0],
		textColor: '#000',
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

	jQuery(mapObjects).each(function(loop, mapObject) {

		if(extGoogleMapLoaded[mapObject.contentId] == undefined) {

			var myLatLng = new google.maps.LatLng(mapObject.defaultGeoCoordinates.latitude, mapObject.defaultGeoCoordinates.longitude);

			bounds = new google.maps.LatLngBounds();

			var mapOptions = {};
			switch(mapObject.extGMapType) {
				case 'contentMap':
				case 'singleMap':
					mapOptions = {
						zoom: 12,
						center: myLatLng,
						mapTypeId: getMapType(mapObject.mapType),
						mapTypeControl: false,
						streetViewControl: false
					};
					break;
				case 'fullSizeMap':
					mapOptions = {
						zoom: 8,
						center: myLatLng,
						mapTypeId: getMapType(mapObject.mapType)
					};
					break;
			}
//			extGoogleMap[mapObject.contentId] = new google.maps.Map(document.getElementById('js_extGMaps'), mapOptions);#
			var currentMapDiv = jQuery('#c' + mapObject.contentId + ' #js_extGMaps');
			extGoogleMap[mapObject.contentId] = new google.maps.Map(currentMapDiv[0], mapOptions);

			infoBox[mapObject.contentId] = new InfoBox(infoBoxOptions);
			google.maps.event.addListenerOnce(extGoogleMap[mapObject.contentId], 'idle', function() {
				setMarker(mapObject);
				//do not use filter by use 'singleMap' plugin
				if(mapObject.extGMapType != 'singleMap') {
					filterMarker(mapObject);
				}
			});
			//listener for click on map
			google.maps.event.addListener(extGoogleMap[mapObject.contentId], 'click', function() {
				infoBox[mapObject.contentId].close();
			});

			if(listMarkerClusterer[mapObject.contentId]) {
				listMarkerClusterer[mapObject.contentId].clearMarkers();
			}
			extGoogleMapLoaded[mapObject.contentId] = true;
		}
	});
}

/**
 * create an markerData Object with some information needed for show marker on map
 *
 * @param id
 * @param objElement
 * @param mapObject
 * @returns {{}|*}
 */
function getMapMarker(id, objElement, mapObject) {
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

	mapObject.mapDataJson[id].marker = new google.maps.Marker({
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
	return markerData;
}

/**
 * open infoBox on marker click and add content to infoBox
 *
 * @param markerData
 * @param mapObject
 */
function addListenerForMarker(markerData, mapObject) {
	google.maps.event.addListener(mapObject.mapDataJson[markerData.id].marker, 'click', function() {
		infoBoxTemplate.find('.title').html(markerData.title);
		infoBoxTemplate.find('.header').html(markerData.header);
		infoBoxTemplate.find('.image').html(markerData.image);
		infoBoxTemplate.find('.description').html(markerData.description);
		if (markerData.url != undefined) {
			infoBoxTemplate.find('.detailLink').attr('href',markerData.url);
		} else {
			infoBoxTemplate.find('.detailLink').remove();
		}

		extGoogleMap[mapObject.contentId].setCenter(markerData.markerLatLng);
		infoBox[mapObject.contentId].open(extGoogleMap[mapObject.contentId], this);
		infoBox[mapObject.contentId].setContent(infoBoxTemplate.html());
	});
}

/**
 * if listMarkerClusterer isset, clear Cluster and add new Markers,
 * else create new MarkerClusterer
 *
 * @param mapObject
 */
function doClustering(mapObject) {
	if(listMarkerClusterer[mapObject.contentId]) {
		listMarkerClusterer[mapObject.contentId].clearMarkers();
		listMarkerClusterer[mapObject.contentId].addMarkers(listClusterMarkers[mapObject.contentId], false);
	} else {
		listMarkerClusterer[mapObject.contentId] = new MarkerClusterer(extGoogleMap[mapObject.contentId], listClusterMarkers[mapObject.contentId], {
			styles: extGoogleMapStyles,
			gridSize: parseInt(mapObject.gridSize),
			maxZoom: (extGoogleMap[mapObject.contentId].mapTypes[extGoogleMap[mapObject.contentId].getMapTypeId()].maxZoom - 1)
		});
	}
}

/**
 * add Markers to the map
 *
 * @param mapObject
 */
function setMarker(mapObject) {
	if(extGoogleMapLoaded[mapObject.contentId] == true) {
		listClusterMarkers[mapObject.contentId] = [];
		jQuery.each(mapObject.mapDataJson, function(id, objElement) {

			var markerData = getMapMarker(id, objElement, mapObject);

			listClusterMarkers[mapObject.contentId].push(mapObject.mapDataJson[id].marker);

			addListenerForMarker(markerData,mapObject);

		});
		gridSize = 40;

		extGoogleMap[mapObject.contentId].fitBounds(bounds);
		if (extGoogleMap[mapObject.contentId].getZoom() > 12) {
			extGoogleMap[mapObject.contentId].setZoom(12);
		}
		doClustering(mapObject);
	}
}

/**
 * filter marker array with selected checkboxes
 *
 * @param mapObject
 */
function filterMarker(mapObject) {

	if(listClusterMarkers[mapObject.contentId]) {
		listMarkerClusterer[mapObject.contentId].clearMarkers();
	}
	listClusterMarkers[mapObject.contentId] = [];

	currentBounds = extGoogleMap[mapObject.contentId].getBounds();
	if(extGoogleMapLoaded[mapObject.contentId] == true) {
		listClusterMarkers[mapObject.contentId] = [];
		jQuery.each(mapObject.mapDataJson, function(id, objElement) {
			var markerData = false;
			mapObject.mapDataJson[id].marker.setMap(null);
			if(currentBounds.contains(mapObject.mapDataJson[id].marker.position) == true) {
				markerData = getMapMarker(id, objElement, mapObject);
			}
			if(mapObject.mapDataJson[id].categories != null && arrayIntersect(selCats, mapObject.mapDataJson[id].categories).length > 0) { //selCats.length == 0 ||
				listClusterMarkers[mapObject.contentId].push(mapObject.mapDataJson[id].marker);
			}
			if(mapObject.mapDataJson[id].tags != null && arrayIntersect(selTags, mapObject.mapDataJson[id].tags).length > 0) { //selCats.length == 0 ||
				listClusterMarkers[mapObject.contentId].push(mapObject.mapDataJson[id].marker);
			}
			if(mapObject.mapDataJson[id].tags != null && arrayIntersect(selTypes, mapObject.mapDataJson[id].categories).length > 0) { //selCats.length == 0 ||
				listClusterMarkers[mapObject.contentId].push(mapObject.mapDataJson[id].marker);
			}
			if(markerData) {
				addListenerForMarker(markerData,mapObject);
			}

		});

		// set map to with bounds for current marker
//		extGoogleMap.fitBounds(bounds);

		doClustering(mapObject);
	}
}

/**
 * show all markers
 *
 * @param mapObject
 */
function showAllMarker(mapObject) {
	if(listClusterMarkers[mapObject.contentId]) {
		listMarkerClusterer[mapObject.contentId].clearMarkers();
	}
	listClusterMarkers[mapObject.contentId] = [];

	currentBounds = extGoogleMap[mapObject.contentId].getBounds();
	if(extGoogleMapLoaded[mapObject.contentId] == true) {
		listClusterMarkers[mapObject.contentId] = [];
		jQuery.each(mapObject.mapDataJson, function(id, objElement) {
			var markerData = false;
			mapObject.mapDataJson[id].marker.setMap(null);
			if(currentBounds.contains(mapObject.mapDataJson[id].marker.position) == true) {
				markerData = getMapMarker(id, objElement,mapObject);
			}

			listClusterMarkers[mapObject.contentId].push(mapObject.mapDataJson[id].marker);
			if(markerData) {
				addListenerForMarker(markerData,mapObject);
			}

		});
		// set map to with bounds for current marker
		doClustering(mapObject);
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