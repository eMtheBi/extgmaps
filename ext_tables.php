<?php
if(!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Articlemap',
	'article map'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Map',
	'full size map'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'extended goggle maps extension');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_extgmaps_domain_model_tags', 'EXT:extgmaps/Resources/Private/Language/locallang_csh_tx_extgmaps_domain_model_tags.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_extgmaps_domain_model_tags');
$TCA['tx_extgmaps_domain_model_tags'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:extgmaps/Resources/Private/Language/locallang_db.xlf:tx_extgmaps_domain_model_tags',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'title,map_icon,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Tags.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_extgmaps_domain_model_tags.gif'
	),
);

$addToPages = Array(
	'latitude' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:extgmaps/Resources/Private/Language/locallang_db.xlf:tx_extgmaps_domain_model_page.latitude',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'longitude' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:extgmaps/Resources/Private/Language/locallang_db.xlf:tx_extgmaps_domain_model_page.longitude',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'tags' => Array(
		"exclude" => 1,
		"label" => "LLL:EXT:extgmaps/Resources/Private/Language/locallang_db.xlf:tx_extgmaps_domain_model_page.tags",
		'config' => array(
			'type' => 'select',
			'foreign_table' => 'tx_extgmaps_domain_model_tags',
			'size' => 10,
			'autoSizeMax' => 30,
			'maxitems' => 9999,
			'multiple' => 1,
			'wizards' => array(
				'_PADDING' => 1,
				'_VERTICAL' => 1,
				'edit' => array(
					'type' => 'popup',
					'title' => 'Edit',
					'script' => 'wizard_edit.php',
					'icon' => 'edit2.gif',
					'popup_onlyOpenIfSelected' => 1,
					'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
				),
				'add' => Array(
					'type' => 'script',
					'title' => 'Create new',
					'icon' => 'add.gif',
					'params' => array(
						'table' => 'tx_extgmaps_domain_model_tags',
						'pid' => '###CURRENT_PID###',
						'setValue' => 'prepend'
					),
					'script' => 'wizard_add.php',
				),
			),
		),
	),

//	'map' => Array(
//		'exclude' => 0,
//		'l10n_mode' => 'exclude',
//		'label' => 'LLL:EXT:extgmaps/Resources/Private/Language/locallang_db.xlf:tx_extgmaps_domain_model_page.map',
//		'config' => Array(
//			'type' => 'user',
//			'userFunc' => 'tx_extgmaps_map->displayMap'
//		)
//	),

);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("pages", $addToPages, 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("pages",
	"--div--;Tags,tags," .
	"--div--;GeoVerortung,latitude,longitude"
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_content", $addToPages, 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("tt_content",
	"--div--;Tags,tags," .
	"--div--;GeoVerortung,latitude,longitude"
);

?>