<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule('order'))
{
	ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
	return;
}

if(!COrderPerms::IsAccessEnabled())
{
	ShowError(GetMessage('ORDER_PERMISSION_DENIED'));
	return;
}

// Preparing of URL templates -->
$arParams['PATH_TO_APP_LIST'] = (isset($arParams['PATH_TO_APP_LIST']) && $arParams['PATH_TO_APP_LIST'] !== '') ? $arParams['PATH_TO_APP_LIST'] : '#SITE_DIR#order/app/';
$arParams['PATH_TO_APP_EDIT'] = (isset($arParams['PATH_TO_APP_EDIT']) && $arParams['PATH_TO_APP_EDIT'] !== '') ? $arParams['PATH_TO_APP_EDIT'] : '#SITE_DIR#order/app/edit/#app_id#/';
$arParams['PATH_TO_REG_LIST'] = (isset($arParams['PATH_TO_REG_LIST']) && $arParams['PATH_TO_REG_LIST'] !== '') ? $arParams['PATH_TO_REG_LIST'] : '#SITE_DIR#order/reg/';
$arParams['PATH_TO_REG_EDIT'] = (isset($arParams['PATH_TO_REG_EDIT']) && $arParams['PATH_TO_REG_EDIT'] !== '') ? $arParams['PATH_TO_REG_EDIT'] : '#SITE_DIR#order/reg/edit/#reg_id#/';
$arParams['PATH_TO_PHYSICAL_LIST'] = (isset($arParams['PATH_TO_PHYSICAL_LIST']) && $arParams['PATH_TO_PHYSICAL_LIST'] !== '') ? $arParams['PATH_TO_PHYSICAL_LIST'] : '#SITE_DIR#order/physical/';
$arParams['PATH_TO_PHYSICAL_EDIT'] = (isset($arParams['PATH_TO_PHYSICAL_EDIT']) && $arParams['PATH_TO_PHYSICAL_EDIT'] !== '') ? $arParams['PATH_TO_PHYSICAL_EDIT'] : '#SITE_DIR#order/physical/edit/#physical_id#/';
$arParams['PATH_TO_CONTACT_LIST'] = (isset($arParams['PATH_TO_CONTACT_LIST']) && $arParams['PATH_TO_CONTACT_LIST'] !== '') ? $arParams['PATH_TO_CONTACT_LIST'] : '#SITE_DIR#order/contact/';
$arParams['PATH_TO_CONTACT_EDIT'] = (isset($arParams['PATH_TO_CONTACT_EDIT']) && $arParams['PATH_TO_CONTACT_EDIT'] !== '') ? $arParams['PATH_TO_CONTACT_EDIT'] : '#SITE_DIR#order/contact/edit/#contact_id#/';
$arParams['PATH_TO_AGENT_LIST'] = (isset($arParams['PATH_TO_AGENT_LIST']) && $arParams['PATH_TO_AGENT_LIST'] !== '') ? $arParams['PATH_TO_AGENT_LIST'] : '#SITE_DIR#order/agent/';
$arParams['PATH_TO_AGENT_EDIT'] = (isset($arParams['PATH_TO_AGENT_EDIT']) && $arParams['PATH_TO_AGENT_EDIT'] !== '') ? $arParams['PATH_TO_AGENT_EDIT'] : '#SITE_DIR#order/agent/edit/#agent_id#/';
$arParams['PATH_TO_DIRECTION_LIST'] = (isset($arParams['PATH_TO_DIRECTION_LIST']) && $arParams['PATH_TO_DIRECTION_LIST'] !== '') ? $arParams['PATH_TO_DIRECTION_LIST'] : '#SITE_DIR#order/direction/';
$arParams['PATH_TO_DIRECTION_EDIT'] = (isset($arParams['PATH_TO_DIRECTION_EDIT']) && $arParams['PATH_TO_DIRECTION_EDIT'] !== '') ? $arParams['PATH_TO_DIRECTION_EDIT'] : '#SITE_DIR#order/direction/edit/#direction_id#/';
$arParams['PATH_TO_NOMEN_LIST'] = (isset($arParams['PATH_TO_NOMEN_LIST']) && $arParams['PATH_TO_NOMEN_LIST'] !== '') ? $arParams['PATH_TO_NOMEN_LIST'] : '#SITE_DIR#order/nomen/';
$arParams['PATH_TO_NOMEN_EDIT'] = (isset($arParams['PATH_TO_NOMEN_EDIT']) && $arParams['PATH_TO_NOMEN_EDIT'] !== '') ? $arParams['PATH_TO_NOMEN_EDIT'] : '#SITE_DIR#order/nomen/edit/#nomen_id#/';
$arParams['PATH_TO_COURSE_LIST'] = (isset($arParams['PATH_TO_COURSE_LIST']) && $arParams['PATH_TO_COURSE_LIST'] !== '') ? $arParams['PATH_TO_COURSE_LIST'] : '#SITE_DIR#order/course/';
$arParams['PATH_TO_COURSE_EDIT'] = (isset($arParams['PATH_TO_COURSE_EDIT']) && $arParams['PATH_TO_COURSE_EDIT'] !== '') ? $arParams['PATH_TO_COURSE_EDIT'] : '#SITE_DIR#order/course/edit/#course_id#/';
$arParams['PATH_TO_GROUP_LIST'] = (isset($arParams['PATH_TO_GROUP_LIST']) && $arParams['PATH_TO_GROUP_LIST'] !== '') ? $arParams['PATH_TO_GROUP_LIST'] : '#SITE_DIR#order/group/';
$arParams['PATH_TO_GROUP_EDIT'] = (isset($arParams['PATH_TO_GROUP_EDIT']) && $arParams['PATH_TO_GROUP_EDIT'] !== '') ? $arParams['PATH_TO_GROUP_EDIT'] : '#SITE_DIR#order/group/edit/#group_id#/';
$arParams['PATH_TO_FORMED_GROUP_LIST'] = (isset($arParams['PATH_TO_FORMED_GROUP_LIST']) && $arParams['PATH_TO_FORMED_GROUP_LIST'] !== '') ? $arParams['PATH_TO_FORMED_GROUP_LIST'] : '#SITE_DIR#order/formed_group/';
$arParams['PATH_TO_FORMED_GROUP_EDIT'] = (isset($arParams['PATH_TO_FORMED_GROUP_EDIT']) && $arParams['PATH_TO_FORMED_GROUP_EDIT'] !== '') ? $arParams['PATH_TO_FORMED_GROUP_EDIT'] : '#SITE_DIR#order/formed_group/edit/#formed_group_id#/';
$arParams['PATH_TO_SYNC'] = (isset($arParams['PATH_TO_SYNC']) && $arParams['PATH_TO_SYNC'] !== '') ? $arParams['PATH_TO_SYNC'] : '#SITE_DIR#order/sync/';
$arParams['PATH_TO_EVENTS_LIST'] = (isset($arParams['PATH_TO_EVENTS_LIST']) && $arParams['PATH_TO_EVENTS_LIST'] !== '') ? $arParams['PATH_TO_EVENTS_LIST'] : '#SITE_DIR#order/events/';
$arParams['PATH_TO_REPORT_LIST'] = (isset($arParams['PATH_TO_REPORT_LIST']) && $arParams['PATH_TO_REPORT_LIST'] !== '') ? $arParams['PATH_TO_REPORT_LIST'] : '#SITE_DIR#order/report/';
$arParams['PATH_TO_CONFIG'] = (isset($arParams['PATH_TO_CONFIG']) && $arParams['PATH_TO_CONFIG'] !== '') ? $arParams['PATH_TO_CONFIG'] : '#SITE_DIR#order/config/';
/*$arParams['PATH_TO_SEARCH_PAGE'] = (isset($arParams['PATH_TO_SEARCH_PAGE']) && $arParams['PATH_TO_SEARCH_PAGE'] !== '') ? $arParams['PATH_TO_SEARCH_PAGE'] : '#SITE_DIR#search/index.php?where=crm';*/
//<-- Preparing of URL templates

$arResult['ACTIVE_ITEM_ID'] = isset($arParams['ACTIVE_ITEM_ID']) ? $arParams['ACTIVE_ITEM_ID'] : '';
//$arResult['ENABLE_SEARCH'] = isset($arParams['ENABLE_SEARCH']) && is_bool($arParams['ENABLE_SEARCH']) ? $arParams['ENABLE_SEARCH'] : true ;
$arResult['ENABLE_SEARCH'] = false;
$arResult['SEARCH_PAGE_URL'] = CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_SEARCH_PAGE']);

$arResult['ID'] = isset($arParams['ID']) ? $arParams['ID'] : '';
if($arResult['ID'] === '')
{
	$arResult['ID'] = 'DEFAULT';
}

$isAdmin = COrderPerms::IsAdmin();
$userPermissions = COrderPerms::GetCurrentUserPermissions();

// Prepere standard items -->
$stdItems['APP'] = array(
	'ID' => 'APP',
	'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_APP'),
	'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_APP_TITLE'),
	'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_APP_LIST']),
);

$stdItems['REG'] = array(
	'ID' => 'REG',
	'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_REG'),
	'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_REG_TITLE'),
	'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_REG_LIST']),
);

// Person section
$stdItems['PERSON'] = array(
	'ID' => 'PERSON',
	'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_PERSON'),
	'URL'=>'#',
	'ICON'=>'more',
	'CHILD_ITEMS'=>array(
		array(
			'ID' => 'PHYSICAL',
			'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_PHYSICAL'),
			'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_PHYSICAL_TITLE'),
			'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_LIST']),
		),
		array(
			'ID' => 'CONTACT',
			'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_CONTACT'),
			'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_CONTACT_TITLE'),
			'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_CONTACT_LIST']),
		),
		array(
			'ID' => 'AGENT',
			'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_AGENT'),
			'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_AGENT_TITLE'),
			'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_AGENT_LIST']),
		)
	)
);
//End Person section


// Structure section
$stdItems['STRUCTURE'] = array(
	'ID' => 'STRUCTURE',
	'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_STRUCTURE'),
	'URL'=>'#',
	'ICON'=>'more',
	'CHILD_ITEMS'=>array(
		array(
			'ID' => 'DIRECTION',
			'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_DIRECTION'),
			'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_DIRECTION_TITLE'),
			'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_DIRECTION_LIST']),
		),
		array(
			'ID' => 'NOMEN',
			'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_NOMEN'),
			'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_NOMEN_TITLE'),
			'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_NOMEN_LIST']),
		),
		array(
			'ID' => 'COURSE',
			'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_COURSE'),
			'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_COURSE_TITLE'),
			'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_COURSE_LIST']),
		),
		array(
			'ID' => 'GROUP',
			'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_GROUP'),
			'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_GROUP_TITLE'),
			'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_GROUP_LIST']),
		),
		array(
			'ID' => 'FORMED_GROUP',
			'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_FORMED_GROUP'),
			'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_FORMED_GROUP_TITLE'),
			'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_FORMED_GROUP_LIST']),
		)
	)
);
// End Structure section






$stdItems['REPORT'] = array(
	'ID' => 'REPORT',
	'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_REPORT'),
	'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_REPORT'),
	'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_REPORT_LIST']),
);

$stdItems['EVENTS'] = array(
	'ID' => 'EVENTS',
	'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_EVENTS'),
	'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_EVENTS'),
	'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_EVENTS_LIST']),
);


if($isAdmin || !$userPermissions->HavePerm('SYNC', BX_ORDER_PERM_NONE, 'READ')) {
	$stdItems['SYNC'] = array(
		'ID' => 'SYNC',
		'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_SYNC'),
		'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_SYNC'), //title
		'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_SYNC']),
	);
}


if($isAdmin || $userPermissions->HavePerm('CONFIG', BX_ORDER_PERM_CONFIG, 'WRITE')) {
	$stdItems['SETTINGS'] = array(
		'ID' => 'CONFIG',
		'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_CONFIG'),
		'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_CONFIG'), //title
		'URL' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_CONFIG']),
	);
}
// <-- Prepere standard items

$items = array();
$itemInfos = isset($arParams['ITEMS']) && is_array($arParams['ITEMS']) ? $arParams['ITEMS'] : array();
if(empty($itemInfos))
{
	$items = array_values($stdItems);
}
else
{
	foreach($itemInfos as &$itemInfo)
	{
		$itemID = isset($itemInfo['ID']) ? strtoupper($itemInfo['ID']) : '';
		if(isset($stdItems[$itemID]))
		{
			$item = $stdItems[$itemID];
			$items[] = $item;
		}
		else
		{
			$items[] = array(
				'ID' => $itemID,
				'NAME' => isset($itemInfo['NAME']) ? $itemInfo['NAME'] : $itemID,
				'URL' => isset($itemInfo['URL']) ? $itemInfo['URL'] : '',
				'COUNTER' => isset($itemInfo['COUNTER']) ? intval($itemInfo['COUNTER']) : 0,
				'ICON' => isset($itemInfo['ICON']) ? $itemInfo['ICON'] : ''
			);
		}
	}
	unset($itemInfo);
}


/*$events = GetModuleEvents('crm', 'OnAfterCrmControlPanelBuild');
while($event = $events->Fetch())
{
    ExecuteModuleEventEx($event, array(&$items));
}
*/
$arResult['ITEMS'] = &$items;
unset($items);

$arResult['ADDITIONAL_ITEM'] = array(
	'ID' => 'MORE',
	'NAME' => GetMessage('ORDER_CTRL_PANEL_ITEM_MORE'),
	'TITLE' => GetMessage('ORDER_CTRL_PANEL_ITEM_MORE_TITLE'),
	'ICON' => 'more'
);

$options = CUserOptions::GetOption('order.control.panel', strtolower($arResult['ID']));
if(!$options)
{
	$options = array('fixed' => 'N');
}
$arResult['IS_FIXED'] = isset($options['fixed']) && $options['fixed'] === 'Y';

$this->IncludeComponentTemplate();
