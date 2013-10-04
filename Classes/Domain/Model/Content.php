<?php
namespace emthebi\Extgmaps\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Markus Bloch <markus@emthebi.de>
 *  Markus Bloch <markus@emthebi.de>
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
class Content extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	protected $title;

	protected $bodyText;

	/**
	 * description for content
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * latitude for content
	 *
	 * @var float
	 */
	protected $latitude;

	/**
	 * longitude for content
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

}
?>