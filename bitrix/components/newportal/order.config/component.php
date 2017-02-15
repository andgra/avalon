<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)
	die();

if (!CModule::includeModule('order'))
{
	ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
	return;
}

if(!COrderPerms::IsAccessEnabled())
{
	ShowError(GetMessage('ORDER_PERMISSION_DENIED'));
	return;
}

if(IsModuleInstalled('bitrix24'))
	$arResult['BITRIX24'] = true;
else
	$arResult['BITRIX24'] = false;

$arResult['PERM_CONFIG'] = false;
$arResult['IS_ACCESS_ENABLED'] = false;
$orderPerms = COrderPerms::getCurrentUserPermissions();
if(!$orderPerms->HavePerm('CONFIG', BX_ORDER_PERM_NONE))
	$arResult['PERM_CONFIG'] = true;
if($orderPerms->IsAccessEnabled())
	$arResult['IS_ACCESS_ENABLED'] = true;

$arResult['RAND_STRING'] = $this->randString();

$APPLICATION->SetTitle(GetMessage('ORDER_TITLE'));
$this->includeComponentTemplate();