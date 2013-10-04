<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'emthebi.' . $_EXTKEY,
	'Articlemap',
	array(
		'Map' => 'test',
		
	),
	// non-cacheable actions
	array(
		'Map' => 'test',
		
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'emthebi.' . $_EXTKEY,
	'Map',
	array(
		'Map' => '',
		
	),
	// non-cacheable actions
	array(
		'Map' => '',
		
	)
);

?>