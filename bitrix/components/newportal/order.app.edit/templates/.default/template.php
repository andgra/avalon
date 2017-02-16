<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;
CUtil::InitJSCore();
$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/order-entity-show.css");

$arTabs = array();
$arTabs[] = array(
    'id' => 'tab_1',
    'name' => GetMessage('ORDER_TAB_1'),
    'title' => GetMessage('ORDER_TAB_1_TITLE'),
    'icon' => '',
    'fields' => $arResult['FIELDS']['tab_1']
);

//if ($arResult['ELEMENT']['ID'] != ''):
    $arResult['REG_TAB_ID'] = 'tab_reg';
    $arResult['FIELDS'][$arResult['REG_TAB_ID']][] = array(
        'id' => 'section_reg_grid',
        'name' => GetMessage('ORDER_SECTION_REG_MAIN'),
        'type' => 'section'
    );
    $arResult['REG_EDITOR_ID'] = 'reg_' . strval($arParams['ELEMENT_ID']) . '_regs';
    ob_start();
    $APPLICATION->IncludeComponent('newportal:order.reg.list',
        '',
        array(
            'EXTERNAL_ID' => $arResult['ELEMENT']['ID']!=''?$arResult['ELEMENT']['ID']:false,
            'EXTERNAL_TYPE' => 'APP',
            'FORM_ID' => $arResult['REG_FORM_ID'],
            'GRID_ID' => $arResult['REG_GRID_ID'],
            'TAB_ID' => $arResult['REG_TAB_ID'],
            'EDIT' => $arResult['PERM_EDIT']
        ),
        false,
        array('HIDE_ICONS' => 'Y', 'ACTIVE_COMPONENT' => 'Y')
    );
    $sRegHtml .= ob_get_contents();
    ob_end_clean();

    $arResult['FIELDS'][$arResult['REG_TAB_ID']][] = array(
        'id' => 'APP_REG_ROWS',
        'name' => GetMessage('ORDER_FIELD_APP_REG'),
        'colspan' => true,
        'type' => 'order_reg_list',
        'value' => $sRegHtml
    );
    if (!empty($arResult['FIELDS'][$arResult['REG_TAB_ID']])) {
        //$regCount = intval($arResult[REG_COUNT]);
        $arTabs[] = array(
            'id' => $arResult['REG_TAB_ID'],
            'name' => GetMessage('ORDER_TAB_REG_NAME'),
            'title' => GetMessage('ORDER_TAB_REG_TITLE'),
            'icon' => '',
            'fields' => $arResult['FIELDS'][$arResult['REG_TAB_ID']]
        );
    }


    $arResult['EVENT_TAB_ID'] = 'tab_event';
    $arResult['FIELDS'][$arResult['EVENT_TAB_ID']][] = array(
        'id' => 'section_event_grid',
        'name' => GetMessage('ORDER_SECTION_EVENT_MAIN'),
        'type' => 'section'
    );
    $arResult['EVENT_EDITOR_ID'] = 'app_' . strval($arParams['ELEMENT_ID']) . '_events';
    ob_start();
    $APPLICATION->IncludeComponent('newportal:order.event.view',
        '',
        array(
            'AJAX_OPTION_ADDITIONAL' => "APP_{$arResult['ELEMENT']['ID']}_EVENT",
            'ENTITY_TYPE' => 'app',
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
        'id' => 'APP_EVENT',
        'name' => GetMessage('ORDER_FIELD_APP_EVENT'),
        'colspan' => true,
        'type' => 'order_event_view',
        'value' => $sEventHtml
    );
    if (!empty($arResult['FIELDS'][$arResult['EVENT_TAB_ID']])) {
        //$eventCount = intval($arResult[EVENT_COUNT]);
        $arTabs[] = array(
            'id' => $arResult['EVENT_TAB_ID'],
            'name' => GetMessage('ORDER_TAB_HISTORY_NAME'),
            'title' => GetMessage('ORDER_TAB_HISTORY_TITLE'),
            'icon' => '',
            'fields' => $arResult['FIELDS'][$arResult['EVENT_TAB_ID']]
        );
    }
//endif;


$elementID = isset($arResult['ELEMENT']['ID']) ? $arResult['ELEMENT']['ID'] : 0;

$arResult['ORDER_CUSTOM_PAGE_TITLE'] =
    $elementID > 0
        ? GetMessage('ORDER_APP_EDIT_TITLE',
        array(
            '#ID#' => $elementID,
            '#TITLE#' => isset($arResult['ELEMENT']['TITLE']) ? $arResult['ELEMENT']['TITLE'] : ''
        )
    )
        : GetMessage('ORDER_REG_CREATE_TITLE');
/*
*/
$formCustomHtml = '<input type="hidden" name="app_id" value="' . $elementID . '"/>' . $arResult['FORM_CUSTOM_HTML'];
/*$APPLICATION->IncludeComponent(
	"newportal:order.interface.form",
	"edit",
	Array(
		"FORM_ID" => $arResult["FORM_ID"],
		"GRID_ID" => $arResult["GRID_ID"],
		"TABS" => $arTabs,
		//"FIELD_SETS" => array(
			//0 => $registrationFieldset,
		//),
		"BUTTONS" => array(
			"standard_buttons" => true,
			"back_url" => $arResult["BACK_URL"],
			"custom_html" => $formCustomHtml,
		),
		"IS_NEW" => $elementID<=0,
		"TITLE" => $arResult["ORDER_CUSTOM_PAGE_TITLE"],
		"ENABLE_TACTILE_INTERFACE" => "Y",
		"DATA" => $arResult["ELEMENT"],
		"SHOW_SETTINGS" => "Y"
	),
	false
);*/

$APPLICATION->IncludeComponent(
    'newportal:order.interface.form.tactile',
    '',
    array(
        'IS_NEW' => $elementID <= 0,
        'MODE' => 'EDIT',
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
        function () {
            var formID = 'form_' + '<?= $arResult['FORM_ID'] ?>';
            var select = BX.findChild(BX('agent_legal_wrap'), {'name': 'AGENT_LEGAL', 'tag': 'select'}, true);
            BX.bind(select, 'change', function (e) {
                document.location.href = "?agent_legal=" + e.target.value;
            });

            if (BX.type.isFunction(OrderSelectEntityInit)) {
                OrderSelectEntityInit();
            }
        }
    );
    var fieldID = "ASSIGNED_ID";
    var assignedSelected = "<?=$arResult['ELEMENT']['ASSIGNED_ID']?>";
    var arOrderSelected = <?=CUtil::PhpToJsObject($arResult['ELEMENT']['ASSIGNED_ID']!=''?array($arResult['ELEMENT']['ASSIGNED_ID']=>true):array());?>;
    var arProviderNames = <?=CUtil::PhpToJsObject($arResult['PROVIDER_NAMES'])?>;
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
</script>
