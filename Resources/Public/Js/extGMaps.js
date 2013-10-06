var listMarkerClusterer = null;
var extGoogleMapLoaded = false;
var infoBox = null;
var extGoogleMap;
var bounds;
var mapData;
var selCats = [];
var listClusterMarkers = [];

var extGoogleMapStyles = [
	{
		url: '/typo3conf/ext/extgmaps/Resources/Public/Images/MapCluster/bubble-green-small.png',
		height: 45,
		width: 45,
		anchor: [0, 0],
		textColor: '#ffffff',
		textSize: 10
	},
	{
		url: '/typo3conf/ext/extgmaps/Resources/Public/Images/MapCluster/bubble-green-middle.png',
		height: 45,
		width: 45,
		anchor: [0, 0],
		textColor: '#ffffff',
		textSize: 11
	},
	{
		url: '/typo3conf/ext/extgmaps/Resources/Public/Images/MapCluster/bubble-green-big.png',
		height: 45,
		width: 45,
		anchor: [0, 0],
		textColor: '#ffffff',
		textSize: 12
	}
];

var infoBoxOptions = {
	disableAutoPan: false,
	maxWidth: 0,
	pixelOffset: new google.maps.Size(0, 0),
	zIndex: null,
	boxClass: 'js_extGMapsInfobox extGMapsInfobox',
	closeBoxMargin: "0px",
	closeBoxURL: "/typo3conf/ext/extgmaps/Resources/Public/Images/MapCluster/close.png",
//	infoBoxClearance: new google.maps.Size(300, 320),
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

	if(extGoogleMapLoaded == false) {
		var myLatLng = new google.maps.LatLng(54.897997816965606, 8.372894287109375);

		bounds = new google.maps.LatLngBounds();

		switch(extGMapType) {
			case 'contentMap':
				var mapOptions = {
					zoom: 8,
					center: myLatLng,
					mapTypeId: getMapType(mapType),
					mapTypeControl: false,
					streetViewControl: false
				};
				break;
			case 'fullSizeMap':
				var mapOptions = {
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
			filterMarker();
		});

		if(listMarkerClusterer) {
			listMarkerClusterer.clearMarkers();
		}
		extGoogleMapLoaded = true;
	}
}


jQuery(document).ready(function() {
	buildMap();
});

function getMapMarker(id,objElement) {
	var markerLatLng = new google.maps.LatLng(objElement[3], objElement[4]);
	bounds.extend(markerLatLng);
	var picto = '/typo3conf/ext/extgmaps/Resources/Public/Images/MapCluster/bubble-green-small.png';
	var image = new google.maps.MarkerImage(picto, null, null, null, new google.maps.Size(25, 25));
	var title = objElement[0] + ' ' + objElement[5] + ' ' + objElement[6];
	mapData[id].marker = new google.maps.Marker({
		position: markerLatLng,
		title: title,
		icon: image
	});

	markerData = new Object();
	markerData['title'] = title;
	markerData['markerLatLng'] = markerLatLng;
	markerData['id'] = id;
	return markerData;
}

function addListenerForMarker(markerData) {
	google.maps.event.addListener(mapData[markerData.id].marker, 'click', function() {
		extGoogleMap.setCenter(markerData.markerLatLng);
		infoBox.open(extGoogleMap, this);
		infoBox.setContent(markerData.title);

	});
}

function handleClustering() {
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
		jQuery.each(mapData, function(id, objElement) {

			var markerData = getMapMarker(id,objElement);

			listClusterMarkers.push(mapData[id].marker);

			addListenerForMarker(markerData);

		});
		gridSize = 40;

		extGoogleMap.fitBounds(bounds);
		handleClustering();
	}
}

function filterMarker() {

	if(listClusterMarkers) {
		listMarkerClusterer.clearMarkers();
	}
	listClusterMarkers = [];

	currentBounds = extGoogleMap.getBounds();
	if(extGoogleMapLoaded == true) {
		listClusterMarkers = [];
		jQuery.each(mapData, function(id, objElement) {
			var markerData = false;
			mapData[id].marker.setMap(null);
			if(currentBounds.contains(mapData[id].marker.position) == true) {
				markerData = getMapMarker(id,objElement);
			}
			if(arrayIntersect(selCats, mapData[id][7]).length > 0) { //selCats.length == 0 ||
				listClusterMarkers.push(mapData[id].marker);
			}
			if (markerData) {

				addListenerForMarker(markerData);
			}

		});
		extGoogleMap.fitBounds(bounds);
		handleClustering();
	}
}

function arrayIntersect(a, b) {
	return a.filter(function(i) {
		return b.indexOf(i) > -1;
	});
}