<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


CUtil::InitJSCore( array('ajax' , 'popup', 'jquery','person' ));
$contId="order_person_container_".$id;
$selected=$arParams['SELECTED'];
$arResult['PATH_TO_PHYSICAL']=OrderCheckPath('PATH_TO_PHYSICAL_EDIT', '', '/order/physical/edit/#physical_id#');
$arResult['PATH_TO_CONTACT']=OrderCheckPath('PATH_TO_CONTACT_EDIT', '', '/order/contact/edit/#contact_id#');
$arResult['PATH_TO_AGENT']=OrderCheckPath('PATH_TO_AGENT_EDIT', '', '/order/agent/edit/#agent_id#');
$arResult['SHOW']=isset($arParams['SHOW'])?$arParams['SHOW']:false;

$newSelected['id']=isset($selected['ID'])?$selected['ID']:'';
switch(strtolower($arParams['TYPE'])) {
    case 'physical':
        $newSelected['title']=isset($selected['FULL_NAME'])?$selected['FULL_NAME']:'';
        $newSelected['phone']=isset($selected['PHONE'])?$selected['PHONE']:'';
        $newSelected['email']=isset($selected['EMAIL'])?$selected['EMAIL']:'';
        $newSelected['url']=CComponentEngine::makePathFromTemplate($arResult['PATH_TO_PHYSICAL'], array(
            'physical_id' => $newSelected['id']
        ));
        if(is_array($arResult['SHOW']) && $arResult['SHOW']['TYPE']=='contact')
            $newSelected['url']=CComponentEngine::makePathFromTemplate($arResult['PATH_TO_CONTACT'], array(
                'contact_id' => $arResult['SHOW']['ID']
            ));
        break;
    case 'contact':
        $newSelected['title']=isset($selected['FULL_NAME'])?$selected['FULL_NAME']:'';
        $newSelected['phone']=isset($selected['PHONE'])?$selected['PHONE']:'';
        $newSelected['email']=isset($selected['EMAIL'])?$selected['EMAIL']:'';
        break;
    case 'agent':
        $newSelected['legal']=isset($selected['LEGAL'])?$selected['LEGAL']:'N';
        $newSelected['title']=isset($selected['TITLE'])?$selected['TITLE']:'';
        $newSelected['phone']=isset($selected['PHONE'])?$selected['PHONE']:'';
        $newSelected['email']=isset($selected['EMAIL'])?$selected['EMAIL']:'';
        $newSelected['url']=CComponentEngine::makePathFromTemplate($arResult['PATH_TO_AGENT'], array(
            'agent_id' => $newSelected['id']
        ));
        if($newSelected['legal']=='Y') {
            $newSelected['contact_id']=isset($selected['CONTACT_ID'])?$selected['CONTACT_ID']:'';
            $newSelected['contact_title']=isset($selected['CONTACT_FULL_NAME'])?$selected['CONTACT_FULL_NAME']:'';
            $newSelected['contact_phone']=isset($selected['CONTACT_PHONE'])?$selected['CONTACT_PHONE']:'';
            $newSelected['contact_email']=isset($selected['CONTACT_EMAIL'])?$selected['CONTACT_EMAIL']:'';
            $newSelected['contact_url']=CComponentEngine::makePathFromTemplate($arResult['PATH_TO_CONTACT'], array(
                'contact_id' => $newSelected['contact_id']
            ));
        }
        break;
}
foreach($newSelected as $i=>$v) {
    $newSelected[$i]=htmlspecialcharsback($v);
}
$arResult['ID']=$arParams['ID'];
$arResult['SELECTED']=$newSelected;
$arResult['TYPE']=strtolower($arParams['TYPE']);
$arResult['READONLY']=isset($arParams['READONLY'])?$arParams['READONLY']:false;
$arResult["RANDOM"] = $this->randString();


$this->IncludeComponentTemplate();