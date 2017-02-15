<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;

$APPLICATION->IncludeComponent(
	'avalon:order.app_create.courses',
	'',
	array(
		'PATH_TO_DIRECTION_ROOT' => $arResult['PATH_TO_DIRECTION_ROOT'],
		'PATH_TO_DIRECTION' => $arResult['PATH_TO_DIRECTION'],
		'PATH_TO_COURSES' => $arResult['PATH_TO_COURSES'],
		'PATH_TO_ENROLL' => $arResult['PATH_TO_ENROLL'],
		'DIRECTION_ROOT_ID' => $arResult['VARIABLES']['direction_root_id'],
		'ELEMENT_ID' => $arResult['VARIABLES']['direction_id'],
		'NAME_TEMPLATE' => $arParams['NAME_TEMPLATE'],
		'AJAX_MODE' => 'Y',
		'AJAX_ID' => '',
		'AJAX_OPTION_JUMP' => 'N',
		'AJAX_OPTION_HISTORY' => 'N',
	),
	$component
);?>