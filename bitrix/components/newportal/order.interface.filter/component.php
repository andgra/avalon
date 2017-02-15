<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule('order'))
{
	ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
	return;
}

$gridID = $arParams['GRID_ID'];
$gridContext = COrderGridContext::Get($gridID);
if(empty($gridContext) && isset($arParams['FILTER_FIELDS']))
{
	$gridContext = COrderGridContext::Parse($arParams['FILTER_FIELDS']);
}
$arResult['FILTER_INFO'] = isset($gridContext['FILTER_INFO']) ? $gridContext['FILTER_INFO'] : array();
$this->IncludeComponentTemplate();
