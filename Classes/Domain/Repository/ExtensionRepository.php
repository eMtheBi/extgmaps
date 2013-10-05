<?php
namespace emthebi\Extgmaps\Domain\Repository;

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
class ExtensionRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * returns all Data and ignores storage Page
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function findAllIgnoreStorage() {
		$query = $this->createQuery();
		$respectStoragePage = $this->getUseRespectStoragePage($query);
		$query->getQuerySettings()->setRespectStoragePage($respectStoragePage);
		$result = $query->execute();

		return $result;
	}
	/**
	 * returns all Data with geo information an having tags
	 * @param int $pid
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function findAllWithGeoData($pid = null) {
		$query = $this->createQuery();

		if (empty($pid)) {
			$respectStoragePage = $this->getUseRespectStoragePage($query);
			$query->getQuerySettings()->setRespectStoragePage($respectStoragePage);
		} else {
			$query->getQuerySettings()->setStoragePageIds(array($pid));
		}
		$where = array();
//		$where[] = $query->greaterThan('tags',0);
//		$where[] = $query->greaterThan('categories',0);
		$where[] = $query->greaterThan('longitude',0);
		$where[] = $query->greaterThan('latitude',0);
		$query->matching($query->logicalAnd($where));

		$result = $query->execute();

		return $result;
	}

	/**
	 * check if RespectStoragePage should be set or not.
	 * true: one or more storagePages will be set
	 * false: no storagePages are selected
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\Query $query
	 * @return bool
	 */
	protected function getUseRespectStoragePage(\TYPO3\CMS\Extbase\Persistence\Generic\Query $query) {
		$respectStoragePage = false;
		$querySettings = $query->getQuerySettings();
		$storagePageIds = $querySettings->getStoragePageIds();
		if(count($storagePageIds) > 0) {
			$respectStoragePage = count($storagePageIds) == 1 && $storagePageIds[0] == 0 ? false : true;
		}
		return $respectStoragePage;
	}

}
?>