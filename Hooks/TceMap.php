<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Markus Bloch <markus@emthebi.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package extgmaps
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class TceMap {

	/**
	 * $incomingData = array($items,$iArray,$config,$table,$row,$field)
	 * @param $incomingData
	 *
	 *
	 * @return string
	 */
	public function displayMap($incomingData)    {
		$table = $incomingData['table'];
		$field = $incomingData['field'];
		$row = $incomingData['row'];
		$this->pObj = $incomingData['pObj'];
		if ($row['latitude'] == 0) $row['latitude'] = 53.238638116487536;
		if ($row['longitude'] == 0) $row['longitude'] = 7.503833770751953;
		$content = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>';
		$content .= '<div id="mapdiv" style="width:600px; height:400px;"></div>';
		$content .= '
			<script type="text/javascript">

				var mapReloaded = false;
				var latlng = new google.maps.LatLng('.$row['latitude'].', '.$row['longitude'].');
				var myOptions = {
					zoom: 8,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};

				var myMap = new google.maps.Map(document.getElementById("mapdiv"), myOptions);

				myMarker = new google.maps.Marker({
					position: latlng,
					map: myMap,
					draggable:true,
					animation: google.maps.Animation.DROP
				});

				google.maps.event.addListener(myMarker, "dragend", function(){
					updateFields(myMarker.getPosition());
				});

				function updateFields(position){
					document.getElementsByName("data[' . $table . ']['.$row['uid'].'][latitude]_hr")[0].value = parseFloat(position.lat());
					typo3form.fieldGet("data[' . $table . ']['.$row['uid'].'][latitude]","","","'.$row['uid'].'","");
					TBE_EDITOR.fieldChanged("' . $table . '","'.$row['uid'].'","latitude","data[tx_poi_poi]['.$row['uid'].'][latitude]");
					document.getElementsByName("data[' . $table . ']['.$row['uid'].'][longitude]_hr")[0].value = parseFloat(position.lng());
					typo3form.fieldGet("data[' . $table . ']['.$row['uid'].'][longitude]","","","'.$row['uid'].'","");
					TBE_EDITOR.fieldChanged("' . $table . '","'.$row['uid'].'","longitude","data[tx_poi_poi]['.$row['uid'].'][longitude]");
				}
			</script>';

		return $content;
	}

}
?>