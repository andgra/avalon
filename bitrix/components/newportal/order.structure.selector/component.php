<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CUtil::InitJSCore(array('ajax', 'popup', 'jquery', 'structure'));

$arParams["form_name"] = !empty($arParams["form_name"]) ? $arParams["form_name"] : "form1";
$arResult['SELECTED']=array();
if(isset($arParams['SELECTED'])
    && isset($arParams['SELECTED']['TYPE']) && $arParams['SELECTED']['TYPE']!=''
    && isset($arParams['SELECTED']['VALUE']) && $arParams['SELECTED']['VALUE']!='')
    $arResult['SELECTED']=array(
        'type'=>$arParams['SELECTED']['TYPE'],
        'value'=>$arParams['SELECTED']['VALUE'],
        'title'=>$arParams['SELECTED']['TITLE']
    );

if(isset($arParams['PARAMS']) && is_array($arParams['PARAMS']) &&
    !empty($arParams['PARAMS'])
) {
    $arResult['PARAMS']=$arParams['PARAMS'];
}
$arResult['ID']=$arParams['ID'];
$arResult["RANDOM"] = $this->randString();


$this->IncludeComponentTemplate();