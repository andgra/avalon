<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
if (!CModule::IncludeModule("order"))
{
    ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
    return;
}
if (!isset($arParams['ELEMENT_ID']) || !isset($arParams['DIRECTION_ROOT_ID']))
{
    ShowError('404');
    return;
}
global $APPLICATION, $USER, $DB;
session_start();

$arResult['DIRECTION']['ID']=$arParams['ELEMENT_ID'];
$arResult['DIRECTION']=COrderDirection::GetByID($arResult['DIRECTION']['ID']);
//Only KSK is permitted
if($arParams['DIRECTION_ROOT_ID']!='000000003' || $arResult['DIRECTION']['PARENT_ID']!=$arParams['DIRECTION_ROOT_ID']) {
    ShowError('404');
    return;
}

$arResult['PATH']=$this->GetPath();
$arResult['PATH_TO_SELF']=$APPLICATION->GetCurPage(false);
$arResult['PATH_TO_ENROLL']=isset($arParams['PATH_TO_ENROLL'])?$arParams['PATH_TO_ENROLL']:'?enroll';


$res=COrderNomen::GetListEx(array(),array('DIRECTION_ID'=>$arResult['DIRECTION']['ID']));
while($el=$res->Fetch()) {
    $el['PRICE']=unserialize($el['PRICE']);
    $arNomenList[$el['ID']]=$el;
}

$res=COrderCourse::GetListEx();
while($el=$res->Fetch()) {
    $el['NOMEN']=unserialize($el['NOMEN']);
    $el['EXAM']=unserialize($el['EXAM']);
    $el['LITER']=unserialize($el['LITER']);
    $el['DOC']=unserialize($el['DOC']);
    $el['TEACHER']=unserialize($el['TEACHER']);
    foreach($el['NOMEN'] as $nomen) {
        if(isset($arNomenList[$nomen['NOMEN_ID']])) {
            $arNomenList[$nomen['NOMEN_ID']]['COURSE'][$el['ID']]=$el;
        }
    }
}
//$arGroupList=COrderGroup::GetListAr(array(),array());
$res=COrderFormedGroup::GetListEx(array(),array('>DATE_START'=>date('d.m.Y')));
while($el=$res->Fetch()) {
    $el['URL']=$arResult['PATH_TO_ENROLL'].'?formed_group_id='.$el['ID'];
    $arFormedGroupList[$el['ID']]=$el;
}
foreach($arNomenList as $id=>$el) {
    $arNomenList[$id]['URL']=$arResult['PATH_TO_ENROLL'].'?nomen_id='.$el['ID'];
    $arNomenList[$id]['FORMED_GROUP']=array_filter($arFormedGroupList, function($itm) use ($el) {
        return $itm['NOMEN_ID']==$el['ID'];
    });
}
$arResult['NOMEN']=$arNomenList;

$this->IncludeComponentTemplate();

