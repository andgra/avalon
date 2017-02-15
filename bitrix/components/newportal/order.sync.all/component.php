<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule('order'))
{
	ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
	return;
}

global $APPLICATION, $USER;
$COrderPerms = new COrderPerms($USER->GetID());
if (!COrderPerms::IsAdmin() && $COrderPerms->HavePerm('SYNC', BX_ORDER_PERM_NONE, 'READ'))
{
	ShowError(GetMessage('ORDER_PERMISSION_DENIED'));
	return;
}

$arResult['PATH_TO_SYNC_DIRECTION'] = OrderCheckPath('PATH_TO_SYNC_DIRECTION', $arParams['PATH_TO_SYNC_DIRECTION'], $APPLICATION->GetCurPage().'?direction');
$arResult['PATH_TO_SYNC_INDEX'] = OrderCheckPath('PATH_TO_SYNC_INDEX', $arParams['PATH_TO_SYNC_INDEX'], $APPLICATION->GetCurPage());
$arResult['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);
$arResult['BACK_URL'] = $arParams['PATH_TO_SYNC_INDEX'];
$arResult['FORM_ID'] = 'ORDER_SYNC_CONFIG';
$arResult['FIELDS'] = array();
//var_dump($_POST);
$arResult['START_MANUALLY'] = "N";
if(isset($_POST['START_MANUALLY']) && check_bitrix_sessid() && $_POST['START_MANUALLY']=="Y"
	&& isset($_POST['ENTITY_TO_SYNC']) && is_array($_POST['ENTITY_TO_SYNC']) && !empty($_POST['ENTITY_TO_SYNC'])) {
	$arResult['START_MANUALLY'] = "Y";
	$arResult['ENTITY']=$_POST['ENTITY_TO_SYNC'];
	$arResult['ENTITY_TITLE']=COrderEntitySelectorHelper::PrepareEntityTitles();
}
$this->IncludeComponentTemplate();

$APPLICATION->AddChainItem(GetMessage('ORDER_SYNC_LIST'), $arParams['PATH_TO_SYNC_INDEX']);

?>
