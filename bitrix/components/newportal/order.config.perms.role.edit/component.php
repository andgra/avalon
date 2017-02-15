<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule('order'))
{
	ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
	return;
}

$OrderPerms = COrderPerms::GetCurrentUserPermissions();
if (!$OrderPerms->HavePerm('CONFIG', BX_ORDER_PERM_CONFIG, 'WRITE'))
{
	ShowError(GetMessage('ORDER_PERMISSION_DENIED'));
	return;
}

$arParams['PATH_TO_ROLE_EDIT'] = OrderCheckPath('PATH_TO_ROLE_EDIT', $arParams['PATH_TO_ROLE_EDIT'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_ENTITY_LIST'] = OrderCheckPath('PATH_TO_ENTITY_LIST', $arParams['PATH_TO_ENTITY_LIST'], $APPLICATION->GetCurPage());

$arParams['ROLE_ID'] = (int) $arParams['ROLE_ID'];
$bVarsFromForm = false;

$arResult['PATH_TO_ROLE_EDIT'] = CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_ROLE_EDIT'],
	array(
		'role_id' => $arParams['ROLE_ID']
	)
);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['save']) || isset($_POST['apply'])) && check_bitrix_sessid())
{
	$bVarsFromForm = true;
	$arFields = array(
		'NAME' => $_POST['NAME'],
		'RELATION' => isset($_POST['ROLE_PERMS'])? $_POST['ROLE_PERMS']: Array()
	);

	$COrderRole = new CorderRole();
	if ($arParams['ROLE_ID'] > 0)
	{
		if (!$COrderRole->Update($arParams['ROLE_ID'], $arFields))
			$arResult['ERROR_MESSAGE'] = $arFields['RESULT_MESSAGE'];
	}
	else
	{
		$arParams['ROLE_ID'] = $COrderRole->Add($arFields);
		if ($arParams['ROLE_ID'] === false)
			$arResult['ERROR_MESSAGE'] = $arFields['RESULT_MESSAGE'];
	}

	if (empty($arResult['ERROR_MESSAGE']))
	{
		if (isset($_POST['apply']))
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_ROLE_EDIT'],
				array(
					'role_id' => $arParams['ROLE_ID']
				)
			));
		else
			LocalRedirect($arParams['PATH_TO_ENTITY_LIST']);
	}
	else
		ShowError($arResult['ERROR_MESSAGE']);

	$arResult['ROLE'] = array(
		'ID' => $arParams['ROLE_ID'],
		'NAME' => $arFields['NAME']
	);
	$arResult['ROLE_PERMS'] = $arFields['RELATION'];
}
else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete']) && check_bitrix_sessid() && $arParams['ROLE_ID'] > 0)
{
	$COrderRole = new COrderRole();
	$COrderRole->Delete($arParams['ROLE_ID']);
	LocalRedirect($arParams['PATH_TO_ENTITY_LIST']);
}

if (!$bVarsFromForm)
{
	if ($arParams['ROLE_ID'] > 0)
	{
		$obRes = COrderRole::GetList(array(), array('ID' => $arParams['ROLE_ID']));
		$arResult['ROLE'] = $obRes->Fetch();
		if ($arResult['ROLE'] == false)
			$arParams['ROLE_ID'] = 0;
	}

	if ($arParams['ROLE_ID'] <= 0)
	{
		$arResult['ROLE']['ID'] = 0;
		$arResult['ROLE']['NAME'] = '';
	}

	$arResult['ROLE_PERMS'] = array();

}
if ($arParams['ROLE_ID'] > 0 && !$bVarsFromForm)
	$arResult['~ROLE_PERMS'] = COrderRole::GetRolePerms($arParams['ROLE_ID']);
if (!$bVarsFromForm)
	$arResult['ROLE_PERMS'] = $arResult['~ROLE_PERMS'];

$arResult['ENTITY'] = array(
	'PHYSICAL' => GetMessage('ORDER_ENTITY_TYPE_PHYSICAL'),
	'CONTACT' => GetMessage('ORDER_ENTITY_TYPE_CONTACT'),
	'AGENT' => GetMessage('ORDER_ENTITY_TYPE_AGENT'),
	'DIRECTION' => GetMessage('ORDER_ENTITY_TYPE_DIRECTION'),
	'NOMEN' => GetMessage('ORDER_ENTITY_TYPE_NOMEN'),
	'COURSE' => GetMessage('ORDER_ENTITY_TYPE_COURSE'),
	'GROUP' => GetMessage('ORDER_ENTITY_TYPE_GROUP'),
	'FORMED_GROUP' => GetMessage('ORDER_ENTITY_TYPE_FORMED_GROUP'),
	'APP' => GetMessage('ORDER_ENTITY_TYPE_APP'),
	'REG' => GetMessage('ORDER_ENTITY_TYPE_REG'),
);

$arPerms = array(
	'READ', 'ADD', 'EDIT', 'DELETE'
);

$arResult['ENTITY_FIELDS'] = array(
	'APP' => array(
		'STATUS' => COrderHelper::GetEnumList('APP',"STATUS"),
		'DIRECTION'=>array(
			'000000001'=>GetMessage('ORDER_ENTITY_FIELD_DIRECTION_VVO'),
			'000000002'=>GetMessage('ORDER_ENTITY_FIELD_DIRECTION_AISH'),
			"000000003"=>GetMessage('ORDER_ENTITY_FIELD_DIRECTION_KSK'),
			"000000017"=>GetMessage('ORDER_ENTITY_FIELD_DIRECTION_PP'),
		)
	),
	'REG' => array(
		'STATUS' => COrderHelper::GetEnumList('REG',"STATUS"),
		'DIRECTION'=>array(
			'000000001'=>GetMessage('ORDER_ENTITY_FIELD_DIRECTION_VVO'),
			'000000002'=>GetMessage('ORDER_ENTITY_FIELD_DIRECTION_AISH'),
			"000000003"=>GetMessage('ORDER_ENTITY_FIELD_DIRECTION_KSK'),
			"000000017"=>GetMessage('ORDER_ENTITY_FIELD_DIRECTION_PP'),
		)
	)
);

$arResult['ROLE_PERM']['PHYSICAL'] =
$arResult['ROLE_PERM']['CONTACT'] =
$arResult['ROLE_PERM']['AGENT'] =
$arResult['ROLE_PERM']['DIRECTION'] =
$arResult['ROLE_PERM']['NOMEN'] =
$arResult['ROLE_PERM']['COURSE'] =
$arResult['ROLE_PERM']['GROUP'] =
$arResult['ROLE_PERM']['FORMED_GROUP'] =
$arResult['ROLE_PERM']['APP'] =
$arResult['ROLE_PERM']['REG'] =
$arResult['ROLE_PERM']['SYNC'] = array(
	BX_ORDER_PERM_NONE => GetMessage('ORDER_PERMS_TYPE_'.BX_ORDER_PERM_NONE),
	BX_ORDER_PERM_SELF => GetMessage('ORDER_PERMS_TYPE_'.BX_ORDER_PERM_SELF),
	BX_ORDER_PERM_DEPARTMENT => GetMessage('ORDER_PERMS_TYPE_'.BX_ORDER_PERM_DEPARTMENT),
	BX_ORDER_PERM_SUBDEPARTMENT => GetMessage('ORDER_PERMS_TYPE_'.BX_ORDER_PERM_SUBDEPARTMENT),
	BX_ORDER_PERM_OPEN => GetMessage('ORDER_PERMS_TYPE_'.BX_ORDER_PERM_OPEN),
	BX_ORDER_PERM_ALL => GetMessage('ORDER_PERMS_TYPE_'.BX_ORDER_PERM_ALL)
);

//unset($arResult['ROLE_PERM']['INVOICE'][BX_ORDER_PERM_OPEN]);

$arResult['PATH_TO_ROLE_DELETE'] =  CHTTP::urlAddParams(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_ROLE_EDIT'],
	array(
		'role_id' => $arResult['ROLE']['ID']
	)),
	array('delete' => '1', 'sessid' => bitrix_sessid())
);

foreach ($arPerms as $perm)
{
	foreach ($arResult['ENTITY'] as $entityType => $entityName)
	{
		if (isset($arResult['ENTITY_FIELDS'][$entityType]))
		{
			foreach ($arResult['ENTITY_FIELDS'][$entityType] as $fieldID => $arFieldValue)
			{
				foreach ($arFieldValue as $fieldValueID => $fieldValue)
				{
					if (!isset($arResult['ROLE_PERMS'][$entityType][$perm][$fieldID][$fieldValueID]) || $arResult['ROLE_PERMS'][$entityType][$perm][$fieldID][$fieldValueID] == '-')
						$arResult['ROLE_PERMS'][$entityType][$perm][$fieldID][$fieldValueID] = $arResult['ROLE_PERMS'][$entityType][$perm]['-'];
				}
			}
		}
	}
}

$this->IncludeComponentTemplate();

$APPLICATION->SetTitle(GetMessage('ORDER_PERMS_ROLE_EDIT'));
$APPLICATION->AddChainItem(GetMessage('ORDER_PERMS_ENTITY_LIST'), $arParams['PATH_TO_ENTITY_LIST']);
$APPLICATION->AddChainItem(GetMessage('ORDER_PERMS_ROLE_EDIT'), $arResult['PATH_TO_ROLE_EDIT']);

?>