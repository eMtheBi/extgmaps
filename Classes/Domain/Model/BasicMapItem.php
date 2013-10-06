<?php
namespace emthebi\Extgmaps\Domain\Model;
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

use \TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
/**
 *
 * @package extgmaps
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class BasicMapItem extends AbstractEntity  {

	/**
	 * latitude
	 *
	 * @var float
	 */
	protected $latitude;

	/**
	 * longitude
	 *
	 * @var float
	 */
	protected $longitude;

	/**
	 * tags
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\emthebi\Extgmaps\Domain\Model\Tags>
	 */
	protected $tags;

	/**
	 * categories
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\emthebi\Extgmaps\Domain\Model\Categories>
	 */
	protected $categories;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * description
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * image
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 * @lazy
	 */
	protected $image;


	/**
	 * header
	 *
	 * @var string
	 */
	protected $header;

	/**
	 * __construct
	 *
	 * @return Page
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		/**
		 * Do not modify this method!
		 * It will be rewritten on each save in the extension builder
		 * You may modify the constructor of this class instead
		 */
		$this->tags = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->categories = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Returns the latitude
	 *
	 * @return float $latitude
	 */
	public function getLatitude() {
		return $this->latitude;
	}

	/**
	 * Sets the latitude
	 *
	 * @param float $latitude
	 * @return void
	 */
	public function setLatitude($latitude) {
		$this->latitude = $latitude;
	}

	/**
	 * Returns the longitude
	 *
	 * @return float $longitude
	 */
	public function getLongitude() {
		return $this->longitude;
	}

	/**
	 * Sets the longitude
	 *
	 * @param float $longitude
	 * @return void
	 */
	public function setLongitude($longitude) {
		$this->longitude = $longitude;
	}

	/**
	 * Adds a Tags
	 *
	 * @param \emthebi\Extgmaps\Domain\Model\Tags $tag
	 * @return void
	 */
	public function addTag(\emthebi\Extgmaps\Domain\Model\Tags $tag) {
		$this->tags->attach($tag);
	}

	/**
	 * Removes a Tags
	 *
	 * @param \emthebi\Extgmaps\Domain\Model\Tags $tagToRemove The Tags to be removed
	 * @return void
	 */
	public function removeTag(\emthebi\Extgmaps\Domain\Model\Tags $tagToRemove) {
		$this->tags->detach($tagToRemove);
	}

	/**
	 * Returns the tags
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\emthebi\Extgmaps\Domain\Model\Tags> $tags
	 */
	public function getTags() {
		return $this->tags;
	}

	/**
	 * Sets the tags
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\emthebi\Extgmaps\Domain\Model\Tags> $tags
	 * @return void
	 */
	public function setTags(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $tags) {
		$this->tags = $tags;
	}

	/**
	 * Adds a Categories
	 *
	 * @param \emthebi\Extgmaps\Domain\Model\Categories $category
	 * @return void
	 */
	public function addCategories(\emthebi\Extgmaps\Domain\Model\Categories $category) {
		$this->categories->attach($category);
	}

	/**
	 * Removes a Categories
	 *
	 * @param \emthebi\Extgmaps\Domain\Model\Categories $categoryToRemove The Categories to be removed
	 * @return void
	 */
	public function removeCategories(\emthebi\Extgmaps\Domain\Model\Categories $categoryToRemove) {
		$this->categories->detach($categoryToRemove);
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\emthebi\Extgmaps\Domain\Model\Categories> $categories
	 */
	public function setCategories($categories) {
		$this->categories = $categories;
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage <\emthebi\Extgmaps\Domain\Model\Categories> $categories
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $headerImage
	 *
	 * @return void
	 */
	public function setImage($image) {
		$this->image = $image;
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	public function getImage() {
		if (!is_object($this->image)){
			return null;
		} elseif ($this->image instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy) {
			$this->image->_loadRealInstance();
		}
		return $this->image->getOriginalResource();
	}

	/**
	 * @param string $header
	 */
	public function setHeader($header) {
		$this->header = $header;
	}

	/**
	 * @return string
	 */
	public function getHeader() {
		return $this->header;
	}
}