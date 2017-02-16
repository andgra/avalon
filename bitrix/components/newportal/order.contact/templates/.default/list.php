<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;
$APPLICATION->IncludeComponent(
	'newportal:order.control_panel',
	'',
	array(
		'ID' => 'CONTACT_LIST',
		'ACTIVE_ITEM_ID' => 'PERSON.CONTACT',
		'PATH_TO_PHYSICAL_LIST' => isset($arResult['PATH_TO_PHYSICAL_LIST']) ? $arResult['PATH_TO_PHYSICAL_LIST'] : '',
		'PATH_TO_PHYSICAL_EDIT' => isset($arResult['PATH_TO_PHYSICAL_EDIT']) ? $arResult['PATH_TO_PHYSICAL_EDIT'] : '',
		'PATH_TO_CONTACT_LIST' => isset($arResult['PATH_TO_CONTACT_LIST']) ? $arResult['PATH_TO_CONTACT_LIST'] : '',
		'PATH_TO_CONTACT_EDIT' => isset($arResult['PATH_TO_CONTACT_EDIT']) ? $arResult['PATH_TO_CONTACT_EDIT'] : '',
		'PATH_TO_AGENT_LIST' => isset($arResult['PATH_TO_AGENT_LIST']) ? $arResult['PATH_TO_AGENT_LIST'] : '',
		'PATH_TO_AGENT_EDIT' => isset($arResult['PATH_TO_AGENT_EDIT']) ? $arResult['PATH_TO_AGENT_EDIT'] : '',
		'PATH_TO_DIRECTION_LIST' => isset($arResult['PATH_TO_DIRECTION_LIST']) ? $arResult['PATH_TO_DIRECTION_LIST'] : '',
		'PATH_TO_DIRECTION_EDIT' => isset($arResult['PATH_TO_DIRECTION_EDIT']) ? $arResult['PATH_TO_DIRECTION_EDIT'] : '',
		'PATH_TO_NOMEN_LIST' => isset($arResult['PATH_TO_NOMEN_LIST']) ? $arResult['PATH_TO_NOMEN_LIST'] : '',
		'PATH_TO_NOMEN_EDIT' => isset($arResult['PATH_TO_NOMEN_EDIT']) ? $arResult['PATH_TO_NOMEN_EDIT'] : '',
		'PATH_TO_COURSE_LIST' => isset($arResult['PATH_TO_COURSE_LIST']) ? $arResult['PATH_TO_COURSE_LIST'] : '',
		'PATH_TO_COURSE_EDIT' => isset($arResult['PATH_TO_COURSE_EDIT']) ? $arResult['PATH_TO_COURSE_EDIT'] : '',
		'PATH_TO_GROUP_LIST' => isset($arResult['PATH_TO_GROUP_LIST']) ? $arResult['PATH_TO_GROUP_LIST'] : '',
		'PATH_TO_GROUP_EDIT' => isset($arResult['PATH_TO_GROUP_EDIT']) ? $arResult['PATH_TO_GROUP_EDIT'] : '',
		'PATH_TO_FORMED_GROUP_LIST' => isset($arResult['PATH_TO_FORMED_GROUP_LIST']) ? $arResult['PATH_TO_FORMED_GROUP_LIST'] : '',
		'PATH_TO_FORMED_GROUP_EDIT' => isset($arResult['PATH_TO_FORMED_GROUP_EDIT']) ? $arResult['PATH_TO_FORMED_GROUP_EDIT'] : '',
		'PATH_TO_APP_LIST' => isset($arResult['PATH_TO_APP_LIST']) ? $arResult['PATH_TO_APP_LIST'] : '',
		'PATH_TO_APP_EDIT' => isset($arResult['PATH_TO_APP_LIST']) ? $arResult['PATH_TO_APP_LIST'] : '',
		'PATH_TO_REG_LIST' => isset($arResult['PATH_TO_REG_LIST']) ? $arResult['PATH_TO_REG_LIST'] : '',
		'PATH_TO_REG_EDIT' => isset($arResult['PATH_TO_REG_EDIT']) ? $arResult['PATH_TO_REG_EDIT'] : '',
		'PATH_TO_CONFIG' => isset($arResult['PATH_TO_CONFIG']) ? $arResult['PATH_TO_CONFIG'] : '',
	),
	$component
);
$APPLICATION->ShowViewContent('order-grid-filter');
$APPLICATION->IncludeComponent(
	'newportal:order.contact.menu',
	'',
	array(
		'PATH_TO_CONTACT_LIST' => $arResult['PATH_TO_CONTACT_LIST'],
		'PATH_TO_CONTACT_EDIT' => $arResult['PATH_TO_CONTACT_EDIT'],
		'ELEMENT_ID' => $arResult['VARIABLES']['contact_id'],
		'TYPE' => 'list'
	),
	$component
);?>

<?
$APPLICATION->IncludeComponent(
	'newportal:order.contact.list',
	'',
	array(
		'CONTACT_COUNT' => '20',
		'PATH_TO_CONTACT_EDIT' => $arResult['PATH_TO_CONTACT_EDIT'],
		'PATH_TO_PHYSICAL_EDIT' => $arResult['PATH_TO_PHYSICAL_EDIT'],
		'PATH_TO_AGENT_EDIT' => $arResult['PATH_TO_AGENT_EDIT'],
		'NAME_TEMPLATE' => $arParams['NAME_TEMPLATE'],
		'AJAX_MODE' => 'Y',
		'AJAX_ID' => '',
		'AJAX_OPTION_JUMP' => 'N',
		'AJAX_OPTION_HISTORY' => 'N',
	),
	$component
);?>