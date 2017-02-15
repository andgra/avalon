<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("order"))
{
    ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
    return;
}
global $APPLICATION, $USER, $DB;

if (!isset($arParams['ELEMENT_ID']))
{
    ShowError('404');
    return;
}

$dir=COrderDirection::GetByID($arParams['ELEMENT_ID']);

$arResult['PATH']=$this->GetPath();
$arResult['PATH_TO_SELF']=$APPLICATION->GetCurPage(false);
$arResult['PATH_TO_FORM']=isset($arParams['PATH_TO_FORM'])?$arParams['PATH_TO_FORM']:$this->GetPath().'?form';
$arResult['PATH_TO_COURSES']=isset($arParams['PATH_TO_COURSES'])?$arParams['PATH_TO_COURSES']:$this->GetPath().'?courses&direction_root_id=#direction_root_id#&direction_id=#direction_id#';
$arResult['PATH_TO_DIRECTION']=isset($arParams['PATH_TO_DIRECTION'])?$arParams['PATH_TO_DIRECTION']:$this->GetPath().'?direction_root_id=#direction_root_id#&direction_id=#direction_id#';


$dir['CHILDREN']=COrderDirection::GetChildren($dir['ID']);
foreach($dir['CHILDREN'] as $id=>$child) {
    if ($arParams['ELEMENT_ID'] == '000000003') {
        $dir['CHILDREN'][$id]['URL'] = CComponentEngine::MakePathFromTemplate(
            $arResult['PATH_TO_COURSES'],
            array('direction_root_id' => $dir['ID'], 'direction_id' => $id)
        );
    } else {
        $dir['CHILDREN'][$id]['URL'] = CComponentEngine::MakePathFromTemplate(
            $arResult['PATH_TO_DIRECTION'],
            array('direction_root_id' => $dir['ID'], 'direction_id' => $id)
        );
    }
}


$arResult['DIRECTION']=$dir;
$this->IncludeComponentTemplate();