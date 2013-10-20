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
use emthebi\Extgmaps\Domain\Model\BasicTreeModel;
use emthebi\Extgmaps\Domain\Model\Content;
use emthebi\Extgmaps\Domain\Model\TreeItem;
use \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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
	 * pageRepository
	 *
	 * @var \emthebi\Extgmaps\Domain\Repository\CategoriesRepository
	 * @inject
	 */
	protected $categoriesRepository;

	/**
	 * pageRepository
	 *
	 * @var \emthebi\Extgmaps\Domain\Repository\TagsRepository
	 * @inject
	 */
	protected $tagsRepository;

	/**
	 * @var TreeItem
	 */
	protected $tagsTree;

	/**
	 * @var array
	 */
	protected $thirdLevelTreeItems = array();

	/**
	 * @var TreeItem
	 */
	protected $categoriesTree;

	/**
	 * @throws \TYPO3\CMS\Extbase\Exception
	 */
	public function initializeAction() {
		if(empty($this->settings)) {
			throw new Exception('please include staticFile / TS setup (1381006069)', 1381006069);
		}
		$this->initializeTree();
	}

	/**
	 * Action for single map with with location set by user
	 */
	public function singleMapAction() {
		$mapObjects = array();
		$gridSize = $this->getContentMapGridSize();
		$mapType = $this->getGoogleMapType();

		$queryResultObject = $this->contentRepository->findAllWithGeoData($this->configurationManager->getContentObject()->data['pid'], $this->configurationManager->getContentObject()->data['uid']);
		/* @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $queryResultObject */
		if($queryResultObject->count()) {
			$contentObject = $queryResultObject->getFirst();
			/* @var Content $contentObject */
			$mapObjects[] = $this->fillMapObject($contentObject);
		}

		// inject data from flexForm settings
		foreach($mapObjects[0] as $key => $value) {
			if(isset($this->settings[$key]) && !empty($this->settings[$key])) {
				switch($key) {
					case 'image':
						$image = $this->createInfoBoxImage($this->settings[$key], $this->settings['title']);
						$mapObjects[0][$key] = $image;
						break;
					default:
						$mapObjects[0][$key] = $this->settings[$key];
				}
			}
		}

		// allow custom mapIcon
		if (!empty($this->settings['flexFormMapIcon'])) {
			$mapObjects[0]['mapIcon'] = $this->settings['flexFormMapIcon'];
		}

		$url = null;
		// check if url is an page uid or a string/url
		if(is_numeric($this->settings['url'])) {
			$url = $this->buildUri($this->settings['url']);
		} else {
			$url = $this->settings['url'];

			if ( $parts = parse_url($url) ) {
				if ( !isset($parts["scheme"]) )
				{
					$url = "http://$url";
				}
			}
			$validUrl = filter_var($url, FILTER_VALIDATE_URL);
			if ($validUrl) {
				$url = $this->settings['url'];
			} else {
				$url = null;
			}
		}

		if(!empty($url)) {
			$mapObjects[0]['url'] = $url;
		}

		$mapDefaultGeoData = array(
			'latitude' => $mapObjects[0]['latitude'],
			'longitude' => $mapObjects[0]['longitude']
		);
		$mapDefaultGeoDataAsJson = json_encode($mapDefaultGeoData);

		$mapObjectsAsJson = json_encode($mapObjects);
		$this->view->assign('categoriesTree', json_encode(array()));
		$this->view->assign('mapDefaultGeoData', $mapDefaultGeoDataAsJson);
		$this->view->assign('mapType', $mapType);
		$this->view->assign('gridSize', $gridSize);
		$this->view->assign('mapObjectsAsJson', $mapObjectsAsJson);
	}

	/**
	 * get default geo coordinate from current included plugin to use this as start point
	 * @return string array json encoded
	 */
	protected function getDefaultGeoCoordinates() {
		$mapDefaultGeoData = array(
			'latitude' => 0,
			'longitude' => 0
		);

		$queryResultObject = $this->contentRepository->findAllWithGeoData($this->configurationManager->getContentObject()->data['pid'], $this->configurationManager->getContentObject()->data['uid']);
		/* @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $mapPluginObject */
		if($queryResultObject->count()) {
			$mapPluginObject = $queryResultObject->getFirst();
			/* @var Content $mapPluginObject */
			$mapDefaultGeoData['latitude'] = $mapPluginObject->getLatitude();
			$mapDefaultGeoData['longitude'] = $mapPluginObject->getLongitude();
		}

		return json_encode($mapDefaultGeoData);
	}

	/**
	 * Action for map which has to be placed on pages
	 */
	public function contentMapAction() {

		$mapDefaultGeoData = $this->getDefaultGeoCoordinates();

		$mapObjects = array();
		$pagesWithGeoInformation = $this->pageRepository->findAllWithGeoData(null, $this->configurationManager->getContentObject()->data['pid']);
		$contentElementsWithGeoInformation = $this->contentRepository->findAllWithGeoData($this->configurationManager->getContentObject()->data['pid']);
		$allowedIds = array();
		$allowedIds['categories'] = $this->getAllowedIdsFromFlexForm($this->settings['flexFormCategories']);
		$allowedIds['tags'] = $this->getAllowedIdsFromFlexForm($this->settings['flexFormTags']);

		foreach($pagesWithGeoInformation as $pageWithGeoInformation) {
			/* @var Page $pageWithGeoInformation */
			$mapObjects[] = $this->fillMapObject($pageWithGeoInformation, $allowedIds);
		}
		foreach($contentElementsWithGeoInformation as $contentElementWithGeoInformation) {
			/* @var Content $contentElementWithGeoInformation */
			$mapObjects[] = $this->fillMapObject($contentElementWithGeoInformation, $allowedIds);
		}

		$gridSize = $this->getContentMapGridSize();
		$mapType = $this->getGoogleMapType();
		$mapObjectsAsJson = json_encode($mapObjects);

		$tagsTree = $this->getTreeAsJson('tags');
		$categoriesTree = $this->getTreeAsJson('categories');

		$this->view->assign('mapDefaultGeoData', $mapDefaultGeoData);
		$this->view->assign('mapType', $mapType);
		$this->view->assign('categoriesTree', $categoriesTree);
		$this->view->assign('gridSize', $gridSize);
		$this->view->assign('mapObjectsAsJson', $mapObjectsAsJson);
	}

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	protected function getTreeAsJson($type) {
		$treeItem = null;
		switch($type) {
			case 'tags':
				$treeItem = $this->getTagsTree();
				break;
			case 'categories':
				$treeItem = $this->getCategoriesTree();
				break;
		}

		$treeAsJson = null;

		/* @var TreeItem $treeItem */
		$treeAsArray = $this->getTreeChildren($treeItem);
		$treeAsJson = json_encode($treeAsArray);

		return $treeAsJson;
	}

	/**
	 * helper function to get recursive all children items
	 *
	 * @param TreeItem $tree
	 * @param int      $deep
	 *
	 * @return array
	 */
	protected function getTreeChildren(TreeItem $tree, $deep = 0) {
		$properties = $tree->_getProperties();
		$children = array();
		if($deep == 0) {
			// only on first call
			foreach($properties['children'] as $treeChild) {
				/* @var TreeItem $treeChild */
				$childProperties = $this->getTreeChildren($treeChild);
				$children[] = $childProperties;
			}
		}

		$deep++;

		if(empty($children) && array_key_exists($properties['label'], $this->thirdLevelTreeItems)) {
			$itemArray = $this->thirdLevelTreeItems[$properties['label']];
			foreach($itemArray as $treeItem) {
				if($deep < 2) {
					$children[] = $this->getTreeChildren($treeItem, $deep);
				}
			}
		}
		$properties['children'] = $children;
		return $properties;
	}

	/**
	 * create tree root elements
	 */
	protected function initializeTree() {

		$itemTitle = LocalizationUtility::translate('tagsTree', $this->request->getControllerExtensionKey());
		$tagsTree = $this->getTreeItems($itemTitle);
		$this->setTagsTree($tagsTree);

		$itemTitle = LocalizationUtility::translate('categoriesTree', $this->request->getControllerExtensionKey());
		$categoriesTree = $this->getTreeItems($itemTitle);
		$this->setCategoryTree($categoriesTree);
	}

	/**
	 * @param $type
	 * @param $flexFormData
	 *
	 * @return array
	 */
	protected function getAllowedIdsFromFlexForm($flexFormData) {
		$ids = explode(',', $flexFormData);
		return $ids;
	}

	/**
	 * get an Object an fill array with information which will be used from map marker
	 *
	 * use TypoScript mapping settings to read properties from given object
	 *
	 * @param $currentObject
	 *
	 * @Param array $allowedIds
	 *
	 * @return array
	 * @throws \TYPO3\CMS\Extbase\Exception
	 */
	protected function fillMapObject($currentObject, $allowedIds = array()) {
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

		// get staticDataForGroups
		if(isset($this->settings['staticDataForGroups'][get_class($currentObject)])) {
			$staticDataForGroups = $this->settings['staticDataForGroups'][get_class($currentObject)];
		} else {
			$staticDataForGroups = $this->settings['staticDataForGroups']['default'];
		}

		$mapMarker = array();

		if(!isset($mappings['type']) || empty($mappings['type'])) {
			//@todo insert TS
			throw new Exception('no mapping type set', 123);
		}

		$itemTitle = LocalizationUtility::translate($mappings['type'], $this->request->getControllerExtensionKey());
		$treeChildOfType = $this->getTreeItems($itemTitle);
		$treeChild = null;

		// loop mapping an fetch properties
		foreach($mappings as $mappingTargetProperty => $objectProperty) {
			if($currentObject instanceof AbstractDomainObject && $currentObject->_hasProperty($objectProperty)) {
				$objectValue = '';
				switch($mappingTargetProperty) {
					case 'image':
						if($useFAL) {
							$fileRepository = $this->objectManager->get('TYPO3\CMS\Core\Resource\FileRepository');
							/* @var \TYPO3\CMS\Core\Resource\FileRepository $fileRepository */
							$fileObjects = $fileRepository->findByRelation($tableName, $objectProperty, $currentObject->getUid());

							if(is_array($fileObjects) && count($fileObjects) > 0) {
								$fileObject = $fileObjects[0];
								/* @var \TYPO3\CMS\Core\Resource\FileReference $fileObject */
//								$objectValue = $fileObject->getPublicUrl();
								$objectValue = $this->createInfoBoxImage($fileObject->getPublicUrl(), $currentObject->getTitle());
							}
						}
						break;
					case 'tags':
					case 'categories':
						$items = array();

						foreach($currentObject->_getProperty($objectProperty) as $tagOrCategory) {
							/* @var BasicTreeModel $tagOrCategory */

							// if tags or category not in array, skip entry
							if(!in_array($tagOrCategory->getUid(), $allowedIds[$mappingTargetProperty]) && count($allowedIds[$mappingTargetProperty]) > 0) {
								continue;
							}
							$treeChild = $this->getTreeItems($tagOrCategory->getTitle(), $tagOrCategory->getMapIcon(), $tagOrCategory->getUid());

							if($this->settings['treeThirdLevel'] == $mappingTargetProperty &&
								!isset($this->thirdLevelTreeItems[$itemTitle][$tagOrCategory->getUid()])
							) {

								$this->thirdLevelTreeItems[$itemTitle][$tagOrCategory->getUid()] = $treeChild;
							} else {
								$treeChild->addChildren($itemTitle, $treeChildOfType);
							}

							switch($mappingTargetProperty) {
								case 'tags':
									$this->addChildToTagsTree($tagOrCategory->getUid(), $treeChild);
									break;
								case 'categories':
									$this->addChildToCategoriesTree($tagOrCategory->getUid(), $treeChild);
									break;
							}
							$items[] = $tagOrCategory->getUid();
						}

						$objectValue = $items;
						break;
					default:
						$objectValue = $currentObject->_getProperty($objectProperty);
				}


				if(empty($objectValue)) {
					$objectValue = null;
				}
				$mapMarker[$mappingTargetProperty] = $objectValue;
			}
		}

		foreach($staticDataForGroups as $key => $staticData) {
			$mapMarker[$key] = $staticData;
		}

		return $mapMarker;
	}

	/**
	 * @param string $label
	 * @param string $image
	 * @param int    $uid
	 *
	 * @return TreeItem
	 */
	protected function getTreeItems($label, $image = null, $uid = null) {
		$treeItem = $this->objectManager->get('emthebi\Extgmaps\Domain\Model\TreeItem');
//		$treeItem = new TreeItem;

		/* @var TreeItem $treeItem */
		$treeItem->setLabel($label);
		$treeItem->setImage($image);
		$treeItem->setId($uid);

		return $treeItem;
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

	/**
	 * @param TreeItem $categoriesTree
	 */
	public function setCategoryTree(TreeItem $categoriesTree) {
		$this->categoriesTree = $categoriesTree;
	}

	/**
	 * @return TreeItem
	 */
	public function getCategoriesTree() {
		return $this->categoriesTree;
	}

	/**
	 * @param int      $uid
	 * @param TreeItem $child
	 */
	public function addChildToCategoriesTree($uid, TreeItem $child) {
		$this->categoriesTree->addChildren($uid, $child);
	}

	/**
	 * @param TreeItem $tagsTree
	 */
	public function setTagsTree($tagsTree) {
		$this->tagsTree = $tagsTree;
	}

	/**
	 * @return TreeItem
	 */
	public function getTagsTree() {
		return $this->tagsTree;
	}

	/**
	 * @param int      $uid
	 * @param TreeItem $child
	 */
	public function addChildToTagsTree($uid, TreeItem $child) {
		$this->tagsTree->addChildren($uid, $child);
	}

	/**
	 * build typo3 url
	 *
	 * @param int   $pageUid          uid of the page object
	 * @param array $additionalParams query parameters to be attached to the resulting URI
	 *
	 * @return string
	 */
	protected function buildUri($pageUid, array $additionalParams = array()) {
		$uri = $this->uriBuilder
			->setTargetPageUid($pageUid)
			->setCreateAbsoluteUri(true)
			->setArguments($additionalParams)
			->build();

		return $uri;
	}

	/**
	 * createInfoBoxImage
	 * create an GifBuilder Image
	 *
	 * @param string $imagePath
	 * @param string $title
	 *
	 * @return string img tag
	 */
	public function createInfoBoxImage($imagePath, $title) {
		$imageTag = '';

		if(isset($this->settings['infoBoxImageSize']) && !empty($this->settings['infoBoxImageSize'])) {
			$imageSize = $this->settings['infoBoxImageSize'];
			$imgConf['file'] = 'GIFBUILDER';
			$imgConf['file.']['XY'] = $imageSize['x'] . ',' . $imageSize['y'];
			$imgConf['file.']['format'] = 'jpg';
			$imgConf['file.']['quality'] = 90;
			$imgConf['file.']['10'] = 'IMAGE';
			$imgConf['file.']['10.']['align'] = 'c,t';
			$imgConf['file.']['10.']['offset'] = '0,0';
			$imgConf['file.']['10.']['file'] = $imagePath;
			$imgConf['file.']['10.']['file.']['width'] = $imageSize['x'] . 'c';
			$imgConf['file.']['10.']['file.']['height'] = $imageSize['y'] . 'c-0';

			if($title != '') {
				$imgConf['stdWrap.']['addParams.']['title'] = $title;
			}
			$imageTag = $this->configurationManager->getContentObject()->cObjGetSingle('IMAGE', $imgConf);
		}

		return $imageTag;
	}
}

?>