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

CJSCore::Init(array('access', 'window'));

$arParams['PATH_TO_ROLE_EDIT'] = OrderCheckPath('PATH_TO_ROLE_EDIT', $arParams['PATH_TO_ROLE_EDIT'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_ENTITY_LIST'] = OrderCheckPath('PATH_TO_ENTITY_LIST', $arParams['PATH_TO_ENTITY_LIST'], $APPLICATION->GetCurPage());

// save settings
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['ACTION'] == 'save' && check_bitrix_sessid())
{
	$arPerms = isset($_POST['PERMS'])? $_POST['PERMS']: array();
	$COrderRole = new COrderRole();
	$COrderRole->SetRelation($arPerms);
	LocalRedirect($APPLICATION->GetCurPage());
}

// get role list
$arResult['PATH_TO_ROLE_ADD'] = CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_ROLE_EDIT'],
	array(
		'role_id' => 0
	)
);
$arResult['ROLE'] = array();
$obRes = COrderRole::GetList();
while ($arRole = $obRes->Fetch())
{
	$arRole['PATH_TO_EDIT'] = CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_ROLE_EDIT'],
		array(
			'role_id' => $arRole['ID']
		)
	);
	$arRole['PATH_TO_DELETE'] = CHTTP::urlAddParams(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_ROLE_EDIT'],
		array(
			'role_id' => $arRole['ID']
		)),
		array('delete' => '1', 'sessid' => bitrix_sessid())
	);
	$arRole['NAME'] = htmlspecialcharsbx($arRole['NAME']);
	$arResult['ROLE'][$arRole['ID']] = $arRole;
}

// get role relation
$arResult['RELATION'] = array();
$arResult['RELATION_ENTITY'] = array();
$obRes = COrderRole::GetRelation();
while ($arRelation = $obRes->Fetch())
{
	$arResult['RELATION'][$arRelation['RELATION']] = $arRelation;
	$arResult['RELATION_ENTITY'][$arRelation['RELATION']] = true;
}
$bid=COrderHelper::GetIdByGuid();
$CAccess = new CAccess();
$arNames = $CAccess->GetNames(array_keys($arResult['RELATION_ENTITY']));
foreach ($arResult['RELATION'] as &$arRelation)
{
	//Issue #43598
	$arRelation['NAME'] = htmlspecialcharsbx($arNames[$arRelation['RELATION']]['name']);
	$providerName = $arNames[$arRelation['RELATION']]['provider'];
	if(!empty($providerName))
	{
		$arRelation['NAME'] = '<b>'.htmlspecialcharsbx($providerName).':</b> '.$arRelation['NAME'];
	}
}
unset($arRelation);

	//$arResult['DISABLED_PROVIDERS'] = array('group');

$this->IncludeComponentTemplate();

$APPLICATION->SetTitle(GetMessage('ORDER_PERMS_ENTITY_LIST'));
$APPLICATION->AddChainItem(GetMessage('ORDER_PERMS_ENTITY_LIST'), $arParams['PATH_TO_ENTITY_LIST']);

?>
