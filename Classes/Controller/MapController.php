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
		$mapObjectsAsJson = json_encode($mapObjects);

		$queryResultObject = $this->contentRepository->findAllWithGeoData($this->configurationManager->getContentObject()->data['pid'],$this->configurationManager->getContentObject()->data['uid']);
		/* @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $queryResultObject*/
		if ($queryResultObject->count()) {
			$contentObject = $queryResultObject->getFirst();
			/* @var Content $contentObject */
			$mapObjects[] = $this->fillMapObject($contentObject);
		}

		// ------- DEBUG START -------
		DebugUtility::debug(__FILE__ . ' - Line: ' . __LINE__,'Debug: Markus B.  19.10.13 14:59 ');
		DebugUtility::debug($mapObjects);
		// ------- DEBUG END -------
		$this->view->assign('mapType', $mapType);
		$this->view->assign('gridSize', $gridSize);
		$this->view->assign('mapObjectsAsJson', $mapObjectsAsJson);

	}
	/**
	 * Action for map which has to be placed on pages
	 */
	public function contentMapAction() {

		$mapObjects = array();
		$pagesWithGeoInformation = $this->pageRepository->findAllWithGeoData(null, $this->configurationManager->getContentObject()->data['pid']);
		$contentElementsWithGeoInformation = $this->contentRepository->findAllWithGeoData($this->configurationManager->getContentObject()->data['pid']);
		$allowedIds = array();
		$allowedIds['categories'] =  $this->getAllowedIdsFromFlexForm($this->settings['flexFormCategories']);
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
	protected function getTreeAsJson($type){
		$treeItem = null;
		switch ($type) {
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
	 * @param TreeItem $tree
	 * @param int      $deep
	 *
	 * @return array
	 */
	protected function getTreeChildren (TreeItem $tree,$deep = 0) {
		$properties = $tree->_getProperties();
		$children = array();
		if ($deep == 0) {
			// only on first call
			foreach($properties['children'] as $treeChild) {
				/* @var TreeItem $treeChild */
				$childProperties = $this->getTreeChildren($treeChild);
				$children[] = $childProperties;
			}
		}

		$deep++ ;

		if (empty($children) && array_key_exists($properties['label'],$this->thirdLevelTreeItems)) {
			$itemArray = $this->thirdLevelTreeItems[$properties['label']];
			foreach($itemArray as $treeItem) {
				if ($deep < 2)
				$children[] = $this->getTreeChildren($treeItem, $deep);
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

		$mapMarker = array();

		if (!isset($mappings['type']) || empty($mappings['type'])) {
			//@todo insert TS
			throw new Exception('no mapping type set',123);
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
								$objectValue = $fileObject->getPublicUrl();
							}
						}
						break;
					case 'tags':
					case 'categories':
						$items = array();

							foreach($currentObject->_getProperty($objectProperty) as $tagOrCategory) {
								/* @var BasicTreeModel $tagOrCategory */

								// if tags or category not in array, skip entry
								if (!in_array($tagOrCategory->getUid(), $allowedIds[$mappingTargetProperty]) && count($allowedIds[$mappingTargetProperty]) > 0) {
									continue;
								}
								$treeChild = $this->getTreeItems($tagOrCategory->getTitle(),$tagOrCategory->getMapIcon(),$tagOrCategory->getUid());

								if ($this->settings['treeThirdLevel'] == $mappingTargetProperty &&
									!isset($this->thirdLevelTreeItems[$itemTitle][$tagOrCategory->getUid()])) {

									$this->thirdLevelTreeItems[$itemTitle][$tagOrCategory->getUid()] = $treeChild;
								} else {
									$treeChild->addChildren($itemTitle,$treeChildOfType);
								}


								switch ($mappingTargetProperty) {
									case 'tags':
										$this->addChildToTagsTree($tagOrCategory->getUid(),$treeChild);
										break;
									case 'categories':
										$this->addChildToCategoriesTree($tagOrCategory->getUid(),$treeChild);
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
		return $mapMarker;
	}

	/**
	 * @param string $label
	 * @param string $image
	 * @param int $uid
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
	 * @param int	   $uid
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
	 * @param int	   $uid
	 * @param TreeItem $child
	 */
	public function addChildToTagsTree($uid, TreeItem $child) {
		$this->tagsTree->addChildren($uid, $child);
	}
}

?>