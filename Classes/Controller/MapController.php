<?php
namespace emthebi\Extgmaps\Controller;

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
use \TYPO3\CMS\Core\Utility\DebugUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Exception;
use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use emthebi\Extgmaps\Domain\Model\Page;
use emthebi\Extgmaps\Domain\Model\Tags;
use emthebi\Extgmaps\Domain\Model\Categories;
use emthebi\Extgmaps\Domain\Model\Content;
use emthebi\Extgmaps\Domain\Model\MapMarker;
use \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;

/**
 *
 *
 * @package extgmaps
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class MapController extends ActionController {

	/**
	 * pageRepository
	 *
	 * @var \emthebi\Extgmaps\Domain\Repository\PageRepository
	 * @inject
	 */
	protected $pageRepository;

	/**
	 * pageRepository
	 *
	 * @var \emthebi\Extgmaps\Domain\Repository\ContentRepository
	 * @inject
	 */
	protected $contentRepository;

	/**
	 * @throws \TYPO3\CMS\Extbase\Exception
	 */
	public function initializeAction() {
		if(empty($this->settings)) {
			throw new Exception('please include staticFile / TS setup (1381006069)', 1381006069);
		}
	}

	/**
	 * Action for map which has to be placed on pages
	 */
	public function contentMapAction() {

		$mapObjects = array();
		$pagesWithGeoInformation = $this->pageRepository->findAllWithGeoData();
		$contentElementsWithGeoInformation = $this->contentRepository->findAllWithGeoData($this->configurationManager->getContentObject()->data['pid']);
		foreach($pagesWithGeoInformation as $pageWithGeoInformation) {
			/* @var Page $pageWithGeoInformation */
			$mapObjects[] = $this->fillMapObject($pageWithGeoInformation);
		}
		foreach($contentElementsWithGeoInformation as $contentElementWithGeoInformation) {
			/* @var Content $contentElementWithGeoInformation */
			$mapObjects[] = $this->fillMapObject($contentElementWithGeoInformation);
		}
		$gridSize = $this->getContentMapGridSize();
		$mapType = $this->getGoogleMapType();
		$mapObjectsAsJson = json_encode($mapObjects);

		$this->view->assign('mapType', $mapType);
		$this->view->assign('gridSize', $gridSize);
		$this->view->assign('mapObjectsAsJson', $mapObjectsAsJson);
	}

	/**
	 * get an Object an fill array with information which will be used from map marker
	 *
	 * use TypoScript mapping settings to read properties from given object
	 *
	 * @param $currentObject
	 *
	 * @return array
	 */
	protected function fillMapObject($currentObject) {
		$useFAL = false;
		$tableName = '';

		// get table name to fetch data from FAL
		if(isset($this->settings['tableMappings'][get_class($currentObject)])) {
			$useFAL = true;
			$tableName = $this->settings['tableMappings'][get_class($currentObject)];
		}

		// get mappings to read out properties
		if(isset($this->settings['mapMarkerMappings'][get_class($currentObject)])) {
			$mappings = $this->settings['mapMarkerMappings'][get_class($currentObject)];
		} else {
			$mappings = $this->settings['mapMarkerMappings']['default'];
		}

		$mapMarker = array();

		// loop mapping an fetch properties
		foreach ($mappings as  $ObjectProperty => $mappingTargetProperty) {
			if ($currentObject instanceof AbstractDomainObject && $currentObject->_hasProperty($ObjectProperty)) {
				$objectValue = '';
				switch($ObjectProperty) {
					case 'image':
						if ($useFAL) {
							$fileRepository = $this->objectManager->get('TYPO3\CMS\Core\Resource\FileRepository');
							/* @var \TYPO3\CMS\Core\Resource\FileRepository $fileRepository*/
							$fileObjects = $fileRepository->findByRelation($tableName, $ObjectProperty, $currentObject->getUid());

							if(is_array($fileObjects) && count($fileObjects) > 0) {
								$fileObject = $fileObjects[0];
								/* @var \TYPO3\CMS\Core\Resource\FileReference $fileObject */
								$objectValue = $fileObject->getPublicUrl();
							}
						}
						break;
					case 'tags':
					case 'categories':
						$tags = array();
						foreach ($currentObject->_getProperty($ObjectProperty) as $tag){
							/* @var Tags $tag */
							$tags[] = $tag->getUid();
						}
						$objectValue = $tags;
						break;
					default:
						$objectValue = $currentObject->_getProperty($ObjectProperty);
				}
				if (empty($objectValue)) {
					$objectValue = null;
				}
				$mapMarker[$mappingTargetProperty] = $objectValue;
			}
		}
		return $mapMarker;
	}

	/**
	 * return gridSize
	 *
	 * @return int value of gridSize
	 * @throws \TYPO3\CMS\Extbase\Exception
	 */
	protected function getContentMapGridSize() {
		if(isset($this->settings['flexFormGridSize'])) {
			$gridSize = $this->settings['flexFormGridSize'];
		} else {
			if(!isset($this->settings['fallbackGridSize'])) {
				throw new Exception('no fallback gridSize found (1381007299)', 1381007299);
			}
			$gridSize = $this->settings['fallbackGridSize'];
		}

		return $gridSize;
	}

	protected function getGoogleMapType() {

		$mapType = 'ROADMAP';
		if(isset($this->settings['flexFormMapType'])) {
			$mapType = $this->settings['flexFormMapType'];
		}
		return $mapType;
	}
}

?>