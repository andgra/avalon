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


$arResult['DIRECTION_TAB_ID'] = 'tab_direction';
$arResult['DIRECTION_EDITOR_ID'] = 'direction_' . strval($arParams['ELEMENT_ID']) . '_children';
ob_start();
$APPLICATION->IncludeComponent('newportal:order.direction.list',
	'',
	array(
		'EXTERNAL_ID' => $arResult['ELEMENT']['ID']!=''?$arResult['ELEMENT']['ID']:false,
		'EXTERNAL_TYPE' => 'DIRECTION',
		'FORM_ID' => $arResult['FORM_ID'].'_CHILDREN_LIST',
		'GRID_ID' => $arResult['GRID_ID'].'_CHILDREN_LIST',
		'TAB_ID' => $arResult['DIRECTION_TAB_ID'],
		'EDIT' => $arResult['PERM_EDIT']
	),
	false,
	array('HIDE_ICONS' => 'Y', 'ACTIVE_COMPONENT' => 'Y')
);
$sChildrenHtml .= ob_get_contents();
ob_end_clean();


$arResult['FIELDS'][$arResult['DIRECTION_TAB_ID']][] = array(
	'id' => 'DIRECTION_CHILDREN_ROWS',
	'colspan' => true,
	'type' => 'order_direction_list',
	'value' => $sChildrenHtml
);
if (!empty($arResult['FIELDS'][$arResult['DIRECTION_TAB_ID']])) {
	$arTabs[] = array(
		'id' => $arResult['DIRECTION_TAB_ID'],
		'name' => GetMessage('ORDER_TAB_CHILDREN_NAME'),
		'title' => GetMessage('ORDER_TAB_CHILDREN_TITLE'),
		'icon' => '',
		'fields' => $arResult['FIELDS'][$arResult['DIRECTION_TAB_ID']]
	);
}

$arResult['NOMEN_TAB_ID'] = 'tab_nomen';
$arResult['NOMEN_EDITOR_ID'] = 'nomen_' . strval($arParams['ELEMENT_ID']) . '_nomens';
ob_start();
$APPLICATION->IncludeComponent('newportal:order.nomen.list',
	'',
	array(
		'EXTERNAL_ID' => $arResult['ELEMENT']['ID']!=''?$arResult['ELEMENT']['ID']:false,
		'EXTERNAL_TYPE' => 'DIRECTION',
		'FORM_ID' => $arResult['FORM_ID'].'_NOMEN_LIST',
		'GRID_ID' => $arResult['GRID_ID'].'_NOMEN_LIST',
		'TAB_ID' => $arResult['NOMEN_TAB_ID'],
		'EDIT' => $arResult['PERM_EDIT']
	),
	false,
	array('HIDE_ICONS' => 'Y', 'ACTIVE_COMPONENT' => 'Y')
);
$sNomenHtml .= ob_get_contents();
ob_end_clean();


$arResult['FIELDS'][$arResult['NOMEN_TAB_ID']][] = array(
	'id' => 'DIRECTION_NOMEN_ROWS',
	'colspan' => true,
	'type' => 'order_nomen_list',
	'value' => $sNomenHtml
);
if (!empty($arResult['FIELDS'][$arResult['NOMEN_TAB_ID']])) {
	$arTabs[] = array(
		'id' => $arResult['NOMEN_TAB_ID'],
		'name' => GetMessage('ORDER_TAB_NOMEN_NAME'),
		'title' => GetMessage('ORDER_TAB_NOMEN_TITLE'),
		'icon' => '',
		'fields' => $arResult['FIELDS'][$arResult['NOMEN_TAB_ID']]
	);
}

if($arResult['ELEMENT']['ID']!=''):
	$arResult['EVENT_TAB_ID']='tab_event';
	$arResult['FIELDS'][$arResult['EVENT_TAB_ID']][] = array(
		'id' => 'section_event_grid',
		'name' => GetMessage('ORDER_SECTION_EVENT_MAIN'),
		'type' => 'section'
	);
	$arResult['EVENT_EDITOR_ID'] = 'direction_'.strval($arParams['ELEMENT_ID']).'_events';
	if($arParams['ELEMENT_ID'] !='') {
		ob_start();
		$APPLICATION->IncludeComponent('newportal:order.event.view',
			'',
			array(
				'AJAX_OPTION_ADDITIONAL' => "DIRECTION_{$arResult['ELEMENT']['ID']}_EVENT",
				'ENTITY_TYPE' => 'direction',
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
	}
	$sEventHtml .= ob_get_contents();
	ob_end_clean();



	$arResult['FIELDS'][$arResult['EVENT_TAB_ID']][] = array(
		'id' => 'DIRECTION_EVENT',
		'name' => GetMessage('ORDER_FIELD_DIRECTION_EVENT'),
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
	? GetMessage('ORDER_DIRECTION_EDIT_PAGE_TITLE',
		array(
			'#ID#' => $elementID,
			'#TITLE#' => isset($arResult['ELEMENT']['TITLE']) ? $arResult['ELEMENT']['TITLE'] : ''
		)
	)
	: GetMessage('ORDER_DIRECTION_CREATE_TITLE');
/*
*/
$formCustomHtml = '<input type="hidden" name="direction_id" value="'.$elementID.'"/>'.$arResult['FORM_CUSTOM_HTML'];

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
			"standard_buttons" => $arResult['PERM_EDIT'],
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
