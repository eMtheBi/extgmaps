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

/**
 *
 *
 * @package extgmaps
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Categories extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * title
	 *
	 * @var \string
	 * @validate NotEmpty
	 */
	protected $title;

	/**
	 * icon for map
	 *
	 * @var \string
	 * @validate NotEmpty
	 */
	protected $mapIcon;

	/**
	 * Returns the title
	 *
	 * @return \string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param \string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the mapIcon
	 *
	 * @return \string $mapIcon
	 */
	public function getMapIcon() {
		return $this->mapIcon;
	}

	/**
	 * Sets the mapIcon
	 *
	 * @param \string $mapIcon
	 * @return void
	 */
	public function setMapIcon($mapIcon) {
		$this->mapIcon = $mapIcon;
	}

}
?>