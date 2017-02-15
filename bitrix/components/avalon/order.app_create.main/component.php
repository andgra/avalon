<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("order"))
{
    ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
    return;
}
global $APPLICATION, $USER, $DB;
$arResult['PATH_TO_SELF']=$APPLICATION->GetCurPage(false);
$arResult['PATH_TO_FORM']=isset($arParams['PATH_TO_FORM'])?$arParams['PATH_TO_FORM']:$this->GetPath().'?form';

$tree=COrderDirection::GetTree();

$rootDirections=COrderHelper::GetRootDirectionList();
foreach($tree as $id=>$el) {
    if(!isset($rootDirections[$id])) {
        unset($tree[$id]);
    } else {
        foreach($el['CHILD_DIRECTIONS'] as $cId => $cEl) {
            //KSK url to courses
            if($id=='000000003') {
                $tree[$id]['CHILD_DIRECTIONS'][$cId]['URL']=CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_COURSES'],
                    array('direction_root_id' => $id,'direction_id' => $cId)
                );
            } else {
                $tree[$id]['CHILD_DIRECTIONS'][$cId]['URL']=CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_DIRECTION'],
                    array('direction_root_id' => $id,'direction_id' => $cId)
                );
            }
            unset($cEl['CHILD_DIRECTIONS']);
        }
    }
}

$arResult['DIRECTION']=$tree;
$this->IncludeComponentTemplate();