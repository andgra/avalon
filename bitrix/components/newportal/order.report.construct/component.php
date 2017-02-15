<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$requiredModules = array('order', 'report', 'intranet', 'socialnetwork');
foreach ($requiredModules as $requiredModule)
{
	if (!CModule::IncludeModule($requiredModule))
	{
		ShowError(GetMessage(strtoupper($requiredModule).'_MODULE_NOT_INSTALLED'));
		return 0;
	}
}


$arResult['ACTION'] = $arResult['ACTION'] = isset($arParams['ACTION']) ? $arParams['ACTION'] : 'create';
$reportID = $arResult['REPORT_ID'] = isset($arParams['REPORT_ID']) ? intval($arParams['REPORT_ID']) : 0;
$reportData = $arResult['REPORT_DATA'] = COrderReportManager::getReportData($reportID);
$reportOwnerID = $arResult['REPORT_OWNER_ID'] = $reportData && isset($reportData['OWNER_ID']) ? $reportData['OWNER_ID'] : '';
if($reportOwnerID === '' && $_SERVER['REQUEST_METHOD'] === 'POST')
{
	$reportOwnerID = $arResult['REPORT_OWNER_ID'] = isset($_POST['reportOwnerID']) ? $_POST['reportOwnerID'] : '';
}
$arResult['REPORT_HELPER_CLASS'] = $reportOwnerID !== '' ? COrderReportManager::getOwnerHelperClassName($reportOwnerID) : '';

$this->IncludeComponentTemplate();
?>
