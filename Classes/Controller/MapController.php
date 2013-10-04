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

	public function testAction() {
		$pagesWithGeoInformation = $this->pageRepository->findAllWithGeoData();
		$contentElementsWithGeoInformation = $this->contentRepository->findAllWithGeoData($this->configurationManager->getContentObject()->data['pid']);
		foreach ($pagesWithGeoInformation as $pageWithGeoInformation) {
			/* @var \emthebi\Extgmaps\Domain\Model\Page  $pageWithGeoInformation */

			// ------- DEBUG START -------
			DebugUtility::debug(__FILE__ . ' - Line: ' . __LINE__,'Debug: Markus  04.10.13 22:16 ');
			DebugUtility::debug($pageWithGeoInformation);
			// ------- DEBUG END -------
		}
		foreach ($contentElementsWithGeoInformation as $contentElementWithGeoInformation) {
			/* @var \emthebi\Extgmaps\Domain\Model\Content  $contentElementWithGeoInformation */

			// ------- DEBUG START -------
			DebugUtility::debug(__FILE__ . ' - Line: ' . __LINE__,'Debug: Markus  04.10.13 22:16 ');
			DebugUtility::debug($contentElementWithGeoInformation);
			// ------- DEBUG END -------
		}
		die();

	}


}
?>