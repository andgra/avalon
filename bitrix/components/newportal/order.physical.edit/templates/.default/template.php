<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;
$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/order-entity-show.css");

$arTabs = array();
$arTabs[] = array(
	'id' => 'tab_1',
	'name' => GetMessage('ORDER_TAB_1'),
	'title' => GetMessage('ORDER_TAB_1_TITLE'),
	'icon' => '',
	'fields'=> $arResult['FIELDS']['tab_1']
);

if($arResult['ELEMENT']['ID']!=''):
	$arResult['EVENT_TAB_ID']='tab_event';
	$arResult['FIELDS'][$arResult['EVENT_TAB_ID']][] = array(
		'id' => 'section_event_grid',
		'name' => GetMessage('ORDER_SECTION_EVENT_MAIN'),
		'type' => 'section'
	);
	$arResult['EVENT_EDITOR_ID'] = 'physical_'.strval($arParams['ELEMENT_ID']).'_events';
	ob_start();
	$APPLICATION->IncludeComponent('newportal:order.event.view',
		'',
		array(
			'AJAX_OPTION_ADDITIONAL' => "PHYSICAL_{$arResult['ELEMENT']['ID']}_EVENT",
			'ENTITY_TYPE' => 'physical',
			'ENTITY_ID' => $arResult['ELEMENT']['ID'],
			'PATH_TO_STAFF_EDIT' => $arParams['PATH_TO_STAFF_EDIT'],
			'FORM_ID' => $arResult['FORM_ID'],
			'TAB_ID' => $arResult['EVENT_TAB_ID'],
			'INTERNAL' => 'Y',
			'ENABLE_CONTROL_PANEL' => false
		),
		false,
		array('HIDE_ICONS' => 'Y', 'ACTIVE_COMPONENT' => 'Y')
	);
	$sEventHtml .= ob_get_contents();
	ob_end_clean();



	$arResult['FIELDS'][$arResult['EVENT_TAB_ID']][] = array(
		'id' => 'PHYSICAL_EVENT',
		'name' => GetMessage('ORDER_FIELD_PHYSICAL_EVENT'),
		'colspan' => true,
		'type' => 'order_event_view',
		'value' => $sEventHtml
	);
	if(!empty($arResult['FIELDS'][$arResult['EVENT_TAB_ID']]))
	{
		//$eventCount = intval($arResult[EVENT_COUNT]);
		$arTabs[] = array(
			'id' => $arResult['EVENT_TAB_ID'],
			'name' => GetMessage('ORDER_TAB_HISTORY_NAME'),
			'title' => GetMessage('ORDER_TAB_HISTORY_TITLE'),
			'icon' => '',
			'fields' => $arResult['FIELDS'][$arResult['EVENT_TAB_ID']]
		);
	}
endif;







$elementID = isset($arResult['ELEMENT']['ID']) ? $arResult['ELEMENT']['ID'] : '';

$arResult['ORDER_CUSTOM_PAGE_TITLE'] =
	$elementID !=''
	? GetMessage('ORDER_PHYSICAL_EDIT_TITLE',
		array(
			'#ID#' => $elementID,
			'#FULL_NAME#' => isset($arResult['ELEMENT']['FULL_NAME']) ? $arResult['ELEMENT']['FULL_NAME'] : ''
		)
	)
	: GetMessage('ORDER_PHYSICAL_CREATE_TITLE');
/*
*/
$formCustomHtml = '<input type="hidden" name="physical_id" value="'.$elementID.'"/>'.$arResult['FORM_CUSTOM_HTML'];

var_dump($arResult['ELEMENT']);
$APPLICATION->IncludeComponent(
	'newportal:order.interface.form.tactile',
	'',
	array(
		'IS_NEW' => $elementID=='',
		'MODE'=> 'EDIT',
		'TITLE' => $arResult["ORDER_CUSTOM_PAGE_TITLE"],
		'FORM_ID' => $arResult["FORM_ID"],
		'DATA' => $arResult["ELEMENT"],
		'TABS' => $arTabs,
		'BUTTONS' => array(
			"standard_buttons" => !($arResult["ELEMENT"]['SHARED']=='Y') && $arResult['PERM_EDIT'],
			"back_url" => $arResult["BACK_URL"],
			"custom_html" => $formCustomHtml,
		),
		/*'FIELD_SETS' => isset($arParams['~FIELD_SETS']) ? $arParams['~FIELD_SETS'] : array(),
		'ENABLE_USER_FIELD_CREATION' => isset($arParams['~ENABLE_USER_FIELD_CREATION']) ? $arParams['~ENABLE_USER_FIELD_CREATION'] : 'Y',
		'USER_FIELD_ENTITY_ID' => isset($arParams['~USER_FIELD_ENTITY_ID']) ? $arParams['~USER_FIELD_ENTITY_ID'] : '',*/
		'SHOW_SETTINGS' => 'Y'
	),
	false, array('HIDE_ICONS' => 'Y')
);

$APPLICATION->AddHeadScript('/bitrix/js/order/instant_editor.js');
?>
<script type="text/javascript">


	BX.ready(
		function()
		{
			var formID = 'form_' + '<?= $arResult['FORM_ID'] ?>';
			var form = BX(formID);
		}
	);
</script>
