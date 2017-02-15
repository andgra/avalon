<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arResult['PATH_TO_FORM']=isset($arParams['PATH_TO_FORM'])?$arParams['PATH_TO_FORM']:'/order/app_create/form/';
$arResult['PATH_TO_BASKET']=isset($arParams['PATH_TO_BASKET'])?$arParams['PATH_TO_BASKET']:'/bitrix/components/newportal/order.app_create.basket/ajax.php?'.bitrix_sessid_get();
session_start();
foreach($_SESSION['BASKET'] as $entity)
{
    if($entity['ENTITY_TYPE']=='DIRECTION')
        $arResult["ENTITY"][]=array_merge(COrderDirection::GetByID($entity['ENTITY_ID']),array('TYPE'=>'DIRECTION'));
    if($entity['ENTITY_TYPE']=='NOMEN')
        $arResult["ENTITY"][]=array_merge(COrderNomen::GetByID($entity['ENTITY_ID']),array('TYPE'=>'NOMEN'));
    if($entity['ENTITY_TYPE']=='GROUP')
        $arResult["ENTITY"][]=array_merge(COrderGroup::GetByID($entity['ENTITY_ID']),array('TYPE'=>'GROUP'));
    if($entity['ENTITY_TYPE']=='FORMED_GROUP')
        $arResult["ENTITY"][]=array_merge(COrderFormedGroup::GetByID($entity['ENTITY_ID']),array('TYPE'=>'FORMED_GROUP'));
    

}

$arResult['PATH']=$this->GetPath();
$this->IncludeComponentTemplate();
?>