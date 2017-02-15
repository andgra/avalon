<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
/** CMain $APPLICATION */
global $APPLICATION;
?>
<? $APPLICATION->IncludeComponent(
	'bitrix:report.list',
	'',
	array(
		'REPORT_TITLE' => GetMessage('ORDER_REPORT_LIST_APP'),
		'PATH_TO_REPORT_LIST' => $arParams['PATH_TO_REPORT_REPORT'],
		'PATH_TO_REPORT_CONSTRUCT' => $arParams['PATH_TO_REPORT_CONSTRUCT'],
		'PATH_TO_REPORT_VIEW' => $arParams['PATH_TO_REPORT_VIEW'],
		'REPORT_HELPER_CLASS' => 'COrderReportHelper'
	),
	false
);?>
