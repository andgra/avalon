<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule('order'))
	return;

$currentUserID = COrderHelper::GetCurrentUserID();
$COrderPerms = COrderPerms::GetCurrentUserPermissions();
if ($COrderPerms->HavePerm('NOMEN', BX_ORDER_PERM_NONE))
	return;

$arParams['PATH_TO_NOMEN_LIST'] = OrderCheckPath('PATH_TO_NOMEN_LIST', $arParams['PATH_TO_NOMEN_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_NOMEN_EDIT'] = OrderCheckPath('PATH_TO_NOMEN_EDIT', $arParams['PATH_TO_NOMEN_EDIT'], $APPLICATION->GetCurPage().'?nomen_id=#nomen_id#&edit');

$arParams['ELEMENT_ID'] = isset($arParams['ELEMENT_ID']) ? $arParams['ELEMENT_ID'] : '';

if (!isset($arParams['TYPE']))
	$arParams['TYPE'] = 'list';

if (isset($_REQUEST['copy']))
	$arParams['TYPE'] = 'copy';

$toolbarID = 'toolbar_nomen_'.$arParams['TYPE'];
if($arParams['ELEMENT_ID'] !='')
{
	$toolbarID .= '_'.$arParams['ELEMENT_ID'];
}
$arResult['TOOLBAR_ID'] = $toolbarID;

$arResult['BUTTONS'] = array();


if ($arParams['TYPE'] == 'list')
{
	$bRead   = !$COrderPerms->HavePerm('NOMEN', BX_ORDER_PERM_NONE, 'READ');
	//$bAdd    = !$COrderPerms->HavePerm('NOMEN', BX_ORDER_PERM_NONE, 'ADD');
	$bEdit  = !$COrderPerms->HavePerm('NOMEN', BX_ORDER_PERM_NONE, 'EDIT');
	$bAdd =false;
	$bDelete = false;
}
else
{
	$bRead   = COrderDirection::CheckReadPermission($arParams['ELEMENT_ID'], $COrderPerms);
	//$bAdd    = COrderDirection::CheckCreatePermission($COrderPerms);
	//$bEdit  = COrderDirection::CheckUpdatePermission($arParams['ELEMENT_ID'], $COrderPerms);
	//$bDelete = COrderDirection::CheckDeletePermission($arParams['ELEMENT_ID'], $COrderPerms);
	$bAdd=false;
	$bEdit=false;
	$bDelete=false;
}

if (!$bRead && !$bAdd && !$bEdit)
	return false;

if($arParams['TYPE'] === 'list')
{
	if ($bAdd)
	{
		$arResult['BUTTONS'][] = array(
			'TEXT' => GetMessage('NOMEN_ADD'),
			'TITLE' => GetMessage('NOMEN_ADD_TITLE'),
			'LINK' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_NOMEN_EDIT'],
				array(
					'nomen_id' => 0
				)
			),
			//'ICON' => 'btn-new',
			'HIGHLIGHT' => true
		);
	}

	/*if ($bImport)
	{
		$arResult['BUTTONS'][] = array(
			'TEXT' => GetMessage('DEAL_IMPORT'),
			'TITLE' => GetMessage('DEAL_IMPORT_TITLE'),
			'LINK' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_DEAL_IMPORT'], array()),
			'ICON' => 'btn-import'
		);
	}

	if ($bExport)
	{
		$arResult['BUTTONS'][] = array(
			'TITLE' => GetMessage('DEAL_EXPORT_CSV_TITLE'),
			'TEXT' => GetMessage('DEAL_EXPORT_CSV'),
			'LINK' => CHTTP::urlAddParams(
				CComponentEngine::MakePathFromTemplate($APPLICATION->GetCurPage(), array()),
				array('type' => 'csv', 'ncc' => '1')
			),
			'ICON' => 'btn-export'
		);

		$arResult['BUTTONS'][] = array(
			'TITLE' => GetMessage('DEAL_EXPORT_EXCEL_TITLE'),
			'TEXT' => GetMessage('DEAL_EXPORT_EXCEL'),
			'LINK' => CHTTP::urlAddParams(
				CComponentEngine::MakePathFromTemplate($APPLICATION->GetCurPage(), array()),
				array('type' => 'excel', 'ncc' => '1')
			),
			'ICON' => 'btn-export'
		);
	}*/

	if(count($arResult['BUTTONS']) > 1)
	{
		//Force start new bar after first button
		array_splice($arResult['BUTTONS'], 1, 0, array(array('NEWBAR' => true)));
	}

	$this->IncludeComponentTemplate();
	return;
}




/*if ($arParams['TYPE'] == 'show' && !empty($arParams['ELEMENT_ID']))
{
	if($bEdit)
	{
		$arResult['BUTTONS'][] = array(
			'TEXT' => GetMessage('APP_EDIT'),
			'TITLE' => GetMessage('APP_EDIT_TITLE'),
			'LINK' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_APP_EDIT'],
				array(
					'app_id' => $arParams['ELEMENT_ID']
				)
			),
			'ICON' => 'btn-edit'
		);
	}
}*/

/*if ($arParams['TYPE'] == 'show' && $bRead && !empty($arParams['ELEMENT_ID']))
{
	$arResult['BUTTONS'][] = array(
		'TEXT' => GetMessage('APP_SHOW'),
		'TITLE' => GetMessage('APP_SHOW_TITLE'),
		'LINK' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_APP_SHOW'],
			array(
				'app_id' => $arParams['ELEMENT_ID']
			)
		),
		'ICON' => 'btn-view'
	);
}*/

/*if (($arParams['TYPE'] == 'edit' || $arParams['TYPE'] == 'show') && $bAdd
	&& !empty($arParams['ELEMENT_ID']) && !isset($_REQUEST['copy']))
{
	$arResult['BUTTONS'][] = array(
		'TEXT' => GetMessage('APP_COPY'),
		'TITLE' => GetMessage('APP_COPY_TITLE'),
		'LINK' => CHTTP::urlAddParams(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_APP_EDIT'],
			array(
				'app_id' => $arParams['ELEMENT_ID']
			)),
			array('copy' => 1)
		),
		'ICON' => 'btn-copy'
	);
}*/

$qty = count($arResult['BUTTONS']);

if (!empty($arResult['BUTTONS']) && $arParams['TYPE'] == 'edit' && empty($arParams['ELEMENT_ID']))
	$arResult['BUTTONS'][] = array('SEPARATOR' => true);
elseif ($qty >= 3)
	$arResult['BUTTONS'][] = array('NEWBAR' => true);

if ($arParams['TYPE'] == 'edit' && $bDelete && !empty($arParams['ELEMENT_ID']))
{
	$arResult['BUTTONS'][] = array(
		'TEXT' => GetMessage('NOMEN_DELETE'),
		'TITLE' => GetMessage('NOMEN_DELETE_TITLE'),
		'LINK' => "javascript:nomen_delete('".GetMessage('NOMEN_DELETE_DLG_TITLE')."', '".GetMessage('NOMEN_DELETE_DLG_MESSAGE')."', '".GetMessage('NOMEN_DELETE_DLG_BTNTITLE')."', '".CHTTP::urlAddParams(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_NOMEN_EDIT'],
			array(
				'nomen_id' => $arParams['ELEMENT_ID']
			)),
			array('delete' => '', 'sessid' => bitrix_sessid())
		)."')",
		'ICON' => 'btn-delete'
	);
}

if ($bAdd)
{
	$arResult['BUTTONS'][] = array(
		'TEXT' => GetMessage('NOMEN_ADD'),
		'TITLE' => GetMessage('NOMEN_ADD_TITLE'),
		'LINK' => CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_NOMEN_EDIT'],
			array(
				'nomen_id' => 0
			)
		),
		'TARGET' => '_blank',
		'ICON' => 'btn-new'
	);
}

$this->IncludeComponentTemplate();
?>
