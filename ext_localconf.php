<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'emthebi.' . $_EXTKEY,
	'SingleMap',
	array(
		'Map' => 'singleMap',
		
	),
	// non-cacheable actions
	array(
		'Map' => 'singleMap',
		
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'emthebi.' . $_EXTKEY,
	'ContentMap',
	array(
		'Map' => 'contentMap',

	),
	// non-cacheable actions
	array(
		'Map' => 'contentMap',

	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'emthebi.' . $_EXTKEY,
	'Map',
	array(
		'Map' => 'fullSizeMap',
		
	),
	// non-cacheable actions
	array(
		'Map' => 'fullSizeMap',
		
	)
);
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:' . $_EXTKEY . '/Hooks/TceHook.php:TceHook';
//$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:' . $_EXTKEY . '/Hooks/TceHook.php:&Emthebi\\Extgmaps\\Hooks\\TceMap->displayMap';
?>