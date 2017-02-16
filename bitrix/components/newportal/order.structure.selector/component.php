<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CUtil::InitJSCore(array('ajax', 'popup', 'jquery', 'structure'));

$arParams["form_name"] = !empty($arParams["form_name"]) ? $arParams["form_name"] : "form1";
$arResult['PATH_TO_DIRECTION']=OrderCheckPath('PATH_TO_DIRECTION_EDIT', '', '/order/direction/edit/#direction_id#');
$arResult['PATH_TO_NOMEN']=OrderCheckPath('PATH_TO_NOMEN_EDIT', '', '/order/nomen/edit/#nomen_id#');
$arResult['PATH_TO_GROUP']=OrderCheckPath('PATH_TO_GROUP_EDIT', '', '/order/group/edit/#group_id#');
$arResult['PATH_TO_FORMED_GROUP']=OrderCheckPath('PATH_TO_FORMED_GROUP_EDIT', '', '/order/formed_group/edit/#formed_group_id#');
$arResult['SELECTED']=array();
if(isset($arParams['SELECTED'])
    && isset($arParams['SELECTED']['TYPE']) && $arParams['SELECTED']['TYPE']!=''
    && isset($arParams['SELECTED']['VALUE']) && $arParams['SELECTED']['VALUE']!='') {
    $arResult['SELECTED'] = array(
        'type' => strtolower($arParams['SELECTED']['TYPE']),
        'value' => $arParams['SELECTED']['VALUE'],
        'title' => $arParams['SELECTED']['TITLE']
    );
    switch($arResult['SELECTED']['type']) {
        case 'direction':
            $arResult['SELECTED']['url']=CComponentEngine::makePathFromTemplate($arResult['PATH_TO_DIRECTION'], array(
                'direction_id' => $arResult['SELECTED']['value']
            ));
            break;
        case 'nomen':
            $arResult['SELECTED']['url']=CComponentEngine::makePathFromTemplate($arResult['PATH_TO_NOMEN'], array(
                'nomen_id' => $arResult['SELECTED']['value']
            ));
            break;
        case 'group':
            $arResult['SELECTED']['url']=CComponentEngine::makePathFromTemplate($arResult['PATH_TO_GROUP'], array(
                'group_id' => $arResult['SELECTED']['value']
            ));
            break;
        case 'formed_group':
            $arResult['SELECTED']['url']=CComponentEngine::makePathFromTemplate($arResult['PATH_TO_FORMED_GROUP'], array(
                'formed_group_id' => $arResult['SELECTED']['value']
            ));
            break;
    }
}
if(isset($arParams['PARAMS']) && is_array($arParams['PARAMS']) &&
    !empty($arParams['PARAMS'])
) {
    $arResult['PARAMS']=$arParams['PARAMS'];
}
$arResult['ID']=$arParams['ID'];
$arResult["RANDOM"] = $this->randString();
$arResult['READONLY']=isset($arParams['READONLY'])?$arParams['READONLY']:false;

$this->IncludeComponentTemplate();