<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("order"))
{
    ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
    return;
}
global $APPLICATION, $USER, $DB;

if (!isset($arParams['ELEMENT_ID']) || !isset($arParams['DIRECTION_ROOT_ID']))
{
    ShowError('404');
    return;
}

$dir=COrderDirection::GetByID($arParams['ELEMENT_ID']);

if ($dir['PARENT_ID']!=$arParams['DIRECTION_ROOT_ID'])
{
    ShowError('404');
    return;
}
$arResult['PATH']=$this->GetPath();
$arResult['PATH_TO_SELF']=$APPLICATION->GetCurPage(false);
$arResult['PATH_TO_COURSES']=CComponentEngine::MakePathFromTemplate(
    $arParams['PATH_TO_COURSES'],
    array('direction_root_id' => $arParams['DIRECTION_ROOT_ID'],'direction_id' => $arParams['ELEMENT_ID'])
);

$dir['URL']=(isset($arParams['PATH_TO_ENROLL'])?$arParams['PATH_TO_ENROLL'].'?':
        $this->GetPath().'?enroll&').'direction_id='.$dir['ID'];

$arResult['DIRECTION']=$dir;
$this->IncludeComponentTemplate();