<?php
namespace Emthebi\Extgmaps\ViewHelpers;

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

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class CssViewHelper
 *
 * Add css files to template
 *
 * Example:
 * {namespace ext=Emthebi\Extgmaps\ViewHelpers}
 * <ext:css src="myFile.css"/>
 *
 * @package Emthebi\Extgmaps\ViewHelpers
 */
class CssViewHelper extends AbstractViewHelper {

	/**
	 * Add Css files
	 *
	 * @param mixed $src
	 * @param string $footer
	 *
	 * @return void
	 */
	public function render($src = null) {

		if($inlineJs = $this->renderChildren()) {
			$name = $this->controllerContext->getRequest()->getControllerExtensionName() . '.' . md5($inlineJs);
			$pageRenderer = $GLOBALS['TSFE']->getPageRenderer();
			/* @var  \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer */
			$pageRenderer->addCssInlineBlock($name, $inlineJs);
		}
		if($src) {
			foreach(is_array($src) ? $src : array($src) as $file) {
				$this->includeCss($file);
			}
		}
	}

	/**
	 * include the css
	 * @param string $file
	 */
	private function includeCss($file) {
		$file .= substr($file, -3, 3) != '.css' ? '.css' : '';
		$filePath = implode('/',
			array_merge(
				array('Resources', 'Public', 'Css'),
				array($file)
			)
		);
		if(file_exists(ExtensionManagementUtility::extPath(
			$this->controllerContext->getRequest()->getControllerExtensionKey(), $filePath))
		) {
			$file = ExtensionManagementUtility::siteRelPath(
					$this->controllerContext->getRequest()->getControllerExtensionKey()) . $filePath;
			$pageRenderer = $GLOBALS['TSFE']->getPageRenderer();
			/* @var  \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer */
			$pageRenderer->addCssFile($file);
		}
	}
}

?>