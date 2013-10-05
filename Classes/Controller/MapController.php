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
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *
 *
 * @package extgmaps
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class MapController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

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
		if (empty($this->settings)) {
			throw new \TYPO3\CMS\Extbase\Exception('please include staticFile / TS setup (1381006069)',1381006069);
		}
	}

	/**
	 * Action for map which has to be placed on pages
	 */
	public function contentMapAction() {


		$mapObjects = array();
		$pagesWithGeoInformation = $this->pageRepository->findAllWithGeoData();
		$contentElementsWithGeoInformation = $this->contentRepository->findAllWithGeoData($this->configurationManager->getContentObject()->data['pid']);
		foreach ($pagesWithGeoInformation as $pageWithGeoInformation) {
			/* @var \emthebi\Extgmaps\Domain\Model\Page  $pageWithGeoInformation */
			$mapObjects[] = $pageWithGeoInformation;
		}
		foreach($contentElementsWithGeoInformation as $contentElementWithGeoInformation) {
			/* @var \emthebi\Extgmaps\Domain\Model\Content $contentElementWithGeoInformation */
			$mapObjects[] = $contentElementWithGeoInformation;

//			if($contentElementWithGeoInformation->getContentType() == 'textpic') {
//				$fileRepository = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\FileRepository');
//				$fileObjects = $fileRepository->findByRelation('tt_content', 'image', $contentElementWithGeoInformation->getUid());
//				if(is_array($fileObjects) && count($fileObjects) > 0) {
//					foreach($fileObjects as $fileObject) {
//						/* @var \TYPO3\CMS\Core\Resource\FileReference $fileObject */
////						$contentElementWithGeoInformation->setImage($fileObject->getPublicUrl());
//					}
//				}
//			}
		}
		$gridSize = $this->getContentMapGridSize();

		$this->view->assign('gridSize',$gridSize);
		$this->view->assign('mapObjects',$mapObjects);
	}

	/**
	 * return gridSize
	 * @return int value of gridSize
	 * @throws \TYPO3\CMS\Extbase\Exception
	 */
	protected function getContentMapGridSize() {
		if (isset($this->settings['flexFormGridSize'])) {
			$gridSize = $this->settings['flexFormGridSize'];
		} else {
			if (!isset($this->settings['fallbackGridSize'])) {
				throw new \TYPO3\CMS\Extbase\Exception('no fallback gridSize found (1381007299)',1381007299);
			}
			$gridSize = $this->settings['fallbackGridSize'];
		}

		return $gridSize;
	}

}

?>