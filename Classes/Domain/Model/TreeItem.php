<?php
namespace Emthebi\Extgmaps\Domain\Model;

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
class TreeItem {

	/**
	 * @var string
	 */
	protected $test = 'test';

	/**
	 * @param string $test
	 */
	public function setTest($test) {
		$this->test = $test;
	}

	/**
	 * @return string
	 */
	public function getTest() {
		return $this->test;
	}

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $image;

	/**
	 * @var array<TreeItem>
	 */
	protected $children;

	/**
	 * @param int|string $uid
	 * @param TreeItem $child
	 */
	public function addChildren($uid, TreeItem $child) {

		if(!isset($this->children[$uid])) {
			$this->children[$uid] = $child;
		} else {

			$childTemp = $this->children[$uid];
			/* @var TreeItem $childTemp */
			$newChildrenData = $child->getChildren();
			if(!empty($newChildrenData)) {
				foreach($newChildrenData as $newChild) {
					/* @var TreeItem $newChild */
					$childId = $newChild->getId();
					if(empty($childId)) {
						$childId = $newChild->getLabel();
					}
					if(empty($childId)) {
						continue;
					}

					$childTemp->addChildren($childId, $newChild);
				}
			}
		}
	}

	/**
	 * @param array $children
	 */
	public function setChildren($children) {
		$this->children = $children;
	}

	/**
	 * @return array
	 */
	public function getChildren() {
		return $this->children;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $image
	 */
	public function setImage($image) {
		$this->image = $image;
	}

	/**
	 * @return string
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * Returns a hash map of property names and property values. Only for internal use.
	 *
	 * @return array The properties
	 */
	public function _getProperties() {
		$properties = get_object_vars($this);
		foreach($properties as $propertyName => $propertyValue) {
			if(substr($propertyName, 0, 1) === '_') {
				unset($properties[$propertyName]);
			}
		}
		return $properties;
	}
}