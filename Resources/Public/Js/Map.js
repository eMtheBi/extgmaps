var listMarkerClusterer = null;
var extGoogleMapLoaded = false;
var infoBox = null;
var extGoogleMap;
var bounds;

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
	infoBoxClearance: new google.maps.Size(300, 320),
	isHidden: false,
	pane: "floatPane",
	enableEventPropagation: false
};
var listClusterMarkers = [];

/**
 *
 * @param mapType
 * @returns {*}
 */
function getMapType(mapType) {
	switch (mapType) {
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

		var mapOptions = {
			zoom: 8,
			center: myLatLng,
			mapTypeId: getMapType(mapType)
		};

		extGoogleMap = new google.maps.Map(document.getElementById('js_extGMaps'), mapOptions);

		infoBox = new InfoBox(infoBoxOptions);

		google.maps.event.addListenerOnce(extGoogleMap, 'idle', function() {
			mapsMarker();
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

/**
 * add Markers to the map
 */

function mapsMarker() {
	if(extGoogleMapLoaded == true) {
		listClusterMarkers = [];
		jQuery.each(mapData, function(id, objElement) {
			if(typeof(mapData[id].marker) == "undefined") {
				var myLatlng = new google.maps.LatLng(objElement[3], objElement[4]);
				bounds.extend(myLatlng);
				var picto = '/typo3conf/ext/extgmaps/Resources/Public/Images/MapCluster/bubble-green-small.png';
				var image = new google.maps.MarkerImage(picto, null, null, null, new google.maps.Size(25, 25));
				var title = objElement[0] + ' ' + objElement[5] + ' ' + objElement[6];
				mapData[id].marker = new google.maps.Marker({
					position: myLatlng,
					title: title,
					icon: image
				});
			}
			listClusterMarkers.push(mapData[id].marker);

			google.maps.event.addListener(mapData[id].marker, 'click', function() {
				infoBox.setContent('');
				infoBox.open(extGoogleMap, this);
				infoBox.setContent(title);
			});

		});
		extGoogleMap.fitBounds(bounds);
		if(listMarkerClusterer) {
			listMarkerClusterer.clearMarkers();
			listMarkerClusterer.addMarkers(listClusterMarkers, false);
		} else {
			listMarkerClusterer = new MarkerClusterer(extGoogleMap, listClusterMarkers, {
				styles: extGoogleMapStyles,
				gridSize: 40,
				maxZoom: (extGoogleMap.mapTypes[extGoogleMap.getMapTypeId()].maxZoom - 1)
			});
		}
	}
}