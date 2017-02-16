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

$arResult['REG_TAB_ID'] = 'tab_reg';
$arResult['REG_EDITOR_ID'] = 'reg_' . strval($arParams['ELEMENT_ID']) . '_regs';
ob_start();
$APPLICATION->IncludeComponent('newportal:order.reg.list',
	'',
	array(
		'EXTERNAL_ID' => $arResult['ELEMENT']['ID']!=''?$arResult['ELEMENT']['ID']:false,
		'EXTERNAL_TYPE' => 'FORMED_GROUP',
		'FORM_ID' => $arResult['REG_FORM_ID'],
		'GRID_ID' => $arResult['REG_GRID_ID'],
		'TAB_ID' => $arResult['REG_TAB_ID'],
		'EDIT' => $arResult['PERM_EDIT']
	),
	false,
	array('HIDE_ICONS' => 'Y', 'ACTIVE_COMPONENT' => 'Y')
);
$sFormedGroupHtml .= ob_get_contents();
ob_end_clean();


$arResult['FIELDS'][$arResult['REG_TAB_ID']][] = array(
	'id' => 'DIRECTION_REG_ROWS',
	'colspan' => true,
	'type' => 'order_reg_list',
	'value' => $sFormedGroupHtml
);
if (!empty($arResult['FIELDS'][$arResult['REG_TAB_ID']])) {
	$arTabs[] = array(
		'id' => $arResult['REG_TAB_ID'],
		'name' => GetMessage('ORDER_TAB_REG_NAME'),
		'title' => GetMessage('ORDER_TAB_REG_TITLE'),
		'icon' => '',
		'fields' => $arResult['FIELDS'][$arResult['REG_TAB_ID']]
	);
}

if($arResult['ELEMENT']['ID']!=''):
	$arResult['EVENT_TAB_ID']='tab_event';
	$arResult['FIELDS'][$arResult['EVENT_TAB_ID']][] = array(
		'id' => 'section_event_grid',
		'name' => GetMessage('ORDER_SECTION_EVENT_MAIN'),
		'type' => 'section'
	);
	$arResult['EVENT_EDITOR_ID'] = 'formed_group_'.strval($arParams['ELEMENT_ID']).'_events';
	if($arParams['ELEMENT_ID'] !='') {
		ob_start();
		$APPLICATION->IncludeComponent('newportal:order.event.view',
			'',
			array(
				'AJAX_OPTION_ADDITIONAL' => "FORMED_GROUP_{$arResult['ELEMENT']['ID']}_EVENT",
				'ENTITY_TYPE' => 'formed_group',
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
		'id' => 'FORMED_GROUP_EVENT',
		'name' => GetMessage('ORDER_FIELD_FORMED_GROUP_EVENT'),
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
	? GetMessage('ORDER_FORMED_GROUP_EDIT_TITLE',
		array(
			'#ID#' => $elementID,
			'#TITLE#' => isset($arResult['ELEMENT']['GROUP_TITLE']) ?
				$arResult['ELEMENT']['GROUP_TITLE'].' ('.$arResult['ELEMENT']['DATE_START'].'-'.$arResult['ELEMENT']['DATE_END'].')' : ''
		)
	)
	: GetMessage('ORDER_FORMED_GROUP_CREATE_TITLE');
/*
*/
$formCustomHtml = '<input type="hidden" name="formed_group_id" value="'.$elementID.'"/>'.$arResult['FORM_CUSTOM_HTML'];


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
			BX('<?=$arResult["GRID_ID"];?>_apply').onclick=function(e) {
				var mainForm = document.forms['form_'+'<?=$arResult["GRID_ID"];?>'];
				var url=mainForm.action+'?';
				var i;
				/*for(i=0; i<mainForm.length; i++) {
				 if(mainForm[i].type!='checkbox' || mainForm[i].checked)
				 url+=mainForm[i].name+'='+mainForm[i].value+'&';
				 }*/
				var regForm = document.forms['form_'+'<?=$arResult["REG_GRID_ID"];?>'];
				for(i=0; i<regForm.length; i++) {
					if(regForm[i].type!='checkbox' || regForm[i].checked)
						url+=regForm[i].name+'='+regForm[i].value+'&';
				}
				url=url.substr(0,url.length-1);
				mainForm.action=url;
			};
		}
	);
</script>
