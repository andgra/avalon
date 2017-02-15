<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION, $USER;
$APPLICATION->SetAdditionalCSS('/bitrix/js/order/css/order.css');
$APPLICATION->AddHeadScript('/bitrix/js/order/interface_grid.js');

if($arResult['ENABLE_CONTROL_PANEL'])
{
	$APPLICATION->IncludeComponent(
		'newportal:order.control_panel',
		'',
		array(
			'ID' => 'EVENT_LIST',
			'ACTIVE_ITEM_ID' => '',
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
}

$gridManagerID = $managerID = $arResult['GRID_ID'].'_MANAGER';
$gridManagerCfg = array(
	'ownerType' => 'EVENT',
	'gridId' => $arResult['GRID_ID'],
	'formName' => "form_{$arResult['GRID_ID']}",
	'allRowsCheckBoxId' => "actallrows_{$arResult['GRID_ID']}",
	'activityEditorId' => '',
	'serviceUrl' => '',
	'filterFields' => array()
);
$prefix = $arResult['GRID_ID'];
for ($i=0, $ic=sizeof($arResult['FILTER']); $i < $ic; $i++)
{
	$filterID = $arResult['FILTER'][$i]['id'];
	if ($arResult['FILTER'][$i]['type'] === 'staff')
	{
		$dbFilterID = $filterID;
		$filterFieldPrefix = $arResult['FILTER_FIELD_PREFIX'];
		if($filterFieldPrefix !== '')
		{
			$dbFilterID = substr($dbFilterID, strlen($filterFieldPrefix));
		}

		$userID = isset($arResult['DB_FILTER'][$dbFilterID])
			? (intval(is_array($arResult['DB_FILTER'][$dbFilterID])
				? $arResult['DB_FILTER'][$dbFilterID][0]
				: $arResult['DB_FILTER'][$dbFilterID]))
			: 0;
		$cUser=COrderStaff::GetByID($userID);
		$userName = $userID != '' ? $cUser['FULL_NAME'] : '';

		ob_start();
		$GLOBALS['APPLICATION']->IncludeComponent('newportal:order.entity.selector',
			'',
			array(
				'ENTITY_TYPE' => 'STAFF',
				'INPUT_NAME' => "{$prefix}_{$filterID}",
				'INPUT_VALUE' => isset($_REQUEST["{$prefix}_{$filterID}"]) ? $_REQUEST["{$prefix}_{$filterID}"] : '',
				'FORM_NAME' => $arResult['GRID_ID'],
				'MULTIPLE' => 'N',
				'FILTER' => true,
			),
			false,
			array('HIDE_ICONS' => 'Y')
		);
		$val = ob_get_clean();

		$arResult['FILTER'][$i]['type'] = 'custom';
		$arResult['FILTER'][$i]['value'] = $val;

		$filterFieldInfo = array(
			'typeName' => 'STAFF',
			'id' => $filterID,
			'params' => array(
				'data' => array(
					'paramName' => "{$filterID}",
					'elementId' => "{$prefix}_{$filterID}"
				),
				'search' => array(
					'paramName' => "{$filterID}_name",
					'elementId' => "{$prefix}_{$filterID}_NAME"
				)
			)
		);

		$gridManagerCfg['filterFields'][] = $filterFieldInfo;
	}
}


	$arResult['GRID_DATA'] = array();
	foreach($arResult['EVENT'] as $arEvent)
	{
		/*$arEvent['FILE_HTML'] = "";
		if(!empty($arEvent['FILES']))
		{
			$arEvent['FILE_HTML'] = '<div class="event-detail-files"><label class="event-detail-files-title">'.GetMessage('ORDER_EVENT_TABLE_FILES').':</label><div class="event-detail-files-list">';
				foreach($arEvent['FILES'] as $key=>$value)
					$arEvent['FILE_HTML'] .= '<div class="event-detail-file"><span class="event-detail-file-number">'.$key.'.</span><span class="event-detail-file-info"><a href="'.htmlspecialcharsbx($value['PATH']).'" target="_blank" class="event-detail-file-link">'.htmlspecialcharsbx($value['NAME']).'</a><span class="event-detail-file-size">('.htmlspecialcharsbx($value['SIZE']).')</span></span></div>';
			$arEvent['FILE_HTML'] .= '</div></div>';
		}*/

		$arActions = array();
		if (COrderPerms::IsAdmin() || ($arEvent['CREATED_BY_ID'] == COrderPerms::GetCurrentUserID() && $arEvent['EVENT_TYPE'] == 0))
		{
			$arActions[] =  array(
				'ICONCLASS' => 'delete',
				'TITLE' => GetMessage('ORDER_EVENT_DELETE_TITLE'),
				'TEXT' => GetMessage('ORDER_EVENT_DELETE'),
				'ONCLICK'=> "BX.OrderEventListManager.items[\"{$managerID}\"].deleteItem(\"{$arEvent['ID']}\")"
			);
		}

		$eventColor = '';
		if ($arEvent['EVENT_TYPE'] == '0')
			$eventColor = 'color: #208c0b';
		elseif ($arEvent['EVENT_TYPE'] == '2')
			$eventColor = 'color: #9c8000';
		$arColumns = array(
			'CREATED_BY_FULL_NAME' => $arEvent['CREATED_BY_FULL_NAME'] == ''? '' :
				'<a href="'.$arEvent['CREATED_BY_LINK'].'" id="balloon_'.$arResult['GRID_ID'].'_'.$arEvent['ID'].'">'.$arEvent['CREATED_BY_FULL_NAME'].'</a>'
				//.'<script type="text/javascript">BX.tooltip('.$arEvent['CREATED_BY_ID'].', "balloon_'.$arResult['GRID_ID'].'_'.$arEvent['ID'].'", "");</script>'
				,
			'EVENT_NAME' => '<span style="'.$eventColor.'">'.$arEvent['EVENT_NAME'].'</span>',
			'EVENT_DESC' => $arEvent['EVENT_DESC'].$arEvent['FILE_HTML'],
			'DATE_CREATE' => FormatDate('x', MakeTimeStamp($arEvent['DATE_CREATE']), (time() + CTimeZone::GetOffset()))
		);
		if ($arResult['EVENT_ENTITY_LINK'] == 'Y')
		{
			$arColumns['ENTITY_TYPE'] = !empty($arEvent['ENTITY_TYPE_TITLE'])? $arEvent['ENTITY_TYPE_TITLE']:(!empty($arEvent['ENTITY_TYPE'])?$arEvent['ENTITY_TYPE']:'');
			$arColumns['ENTITY_TITLE'] = !empty($arEvent['ENTITY_TITLE'])?
				'<a href="'.$arEvent['ENTITY_LINK'].'" id="balloon_'.$arResult['GRID_ID'].'_I_'.$arEvent['ID'].'">'.$arEvent['ENTITY_TITLE'].'</a>'
				//.'<script type="text/javascript">BX.tooltip("'.$arEvent['ENTITY_TYPE'].'_'.$arEvent['ENTITY_ID'].'", "balloon_'.$arResult['GRID_ID'].'_I_'.$arEvent['ID'].'", "/bitrix/components/newportal/order.'.strtolower($arEvent['ENTITY_TYPE']).'.show/card.ajax.php", "order_balloon'.($arEvent['ENTITY_TYPE'] == 'LEAD' || $arEvent['ENTITY_TYPE'] == 'DEAL' || $arEvent['ENTITY_TYPE'] == 'QUOTE' ? '_no_photo': '_'.strtolower($arEvent['ENTITY_TYPE'])).'", true);</script>'
				: '';
		}
		else
		{
			unset($arEvent['ENTITY_TYPE']);
			unset($arEvent['ENTITY_TITLE']);
		}

		$arResult['GRID_DATA'][] = array(
			'id' => $arEvent['ID'],
			'data' => $arEvent,
			'actions' => $arActions,
			'editable' =>($USER->IsAdmin() || ($arEvent['CREATED_BY_ID'] == $USER->GetId() && $arEvent['EVENT_TYPE'] == 0))? true: false,
			'columns' => $arColumns
		);
	}
	$APPLICATION->IncludeComponent('newportal:main.user.link',
		'',
		array(
			'AJAX_ONLY' => 'Y',
			'NAME_TEMPLATE' => $arParams["NAME_TEMPLATE"]
		),
		false,
		array('HIDE_ICONS' => 'Y')
	);

	if(!$arResult['INTERNAL'])
	{
		$APPLICATION->ShowViewContent('order-grid-filter');
	}
	/*else
	{
		// Render toolbar in internal mode
		$toolbarButtons = array();
		if(isset($arResult['ENTITY_TYPE']) && $arResult['ENTITY_TYPE'] !== ''
			&& isset($arResult['ENTITY_ID']) && is_int($arResult['ENTITY_ID']) && $arResult['ENTITY_ID'] > 0)
		{
			$toolbarButtons[] = array(
				'TEXT' => GetMessage('ORDER_EVENT_VIEW_ADD_SHORT'),
				'TITLE' => GetMessage('ORDER_EVENT_VIEW_ADD'),
				'ONCLICK' => "BX.OrderEventListManager.items[\"{$managerID}\"].addItem()",
				'ICON' => 'btn-new'
			);
		}

		$toolbarButtons[] = array(
			'TEXT' => 'FILTER',
			'TEXT' => GetMessage('ORDER_EVENT_VIEW_SHOW_FILTER_SHORT'),
			'TITLE' => GetMessage('ORDER_EVENT_VIEW_SHOW_FILTER'),
			'ICON' => 'order-filter-light-btn',
			'ALIGNMENT' => 'right',
			'ONCLICK' => "BX.InterfaceGridFilterPopup.toggle('{$arResult['GRID_ID']}', this)"
		);

		$APPLICATION->IncludeComponent(
			'newportal:order.interface.toolbar',
			'',
			array(
				'TOOLBAR_ID' => $toolbarID,
				'BUTTONS' => $toolbarButtons
			),
			$component,
			array('HIDE_ICONS' => 'Y')
		);
	}*/

	$APPLICATION->IncludeComponent(
		'newportal:order.interface.grid',
		'',
		array(
			'GRID_ID' => $arResult['GRID_ID'],
			'HEADERS' => $arResult['HEADERS'],
			'SORT' => $arResult['SORT'],
			'SORT_VARS' => $arResult['SORT_VARS'],
			'ROWS' => $arResult['GRID_DATA'],
			'FOOTER' => array(array('title' => GetMessage('ORDER_ALL'), 'value' => $arResult['ROWS_COUNT'])),
			'EDITABLE' => 'Y',
			'ACTIONS' => array(),
			'ACTION_ALL_ROWS' => false,
			'NAV_OBJECT' => $arResult['DB_LIST'],
			'FORM_ID' => $arResult['FORM_ID'],
			'TAB_ID' => $arResult['TAB_ID'],
			'AJAX_MODE' => $arResult['AJAX_MODE'],
			'AJAX_ID' => $arResult['AJAX_ID'],
			'AJAX_OPTION_JUMP' => $arResult['AJAX_OPTION_JUMP'],
			'AJAX_OPTION_HISTORY' => $arResult['AJAX_OPTION_HISTORY'],
			'AJAX_LOADER' => isset($arParams['AJAX_LOADER']) ? $arParams['AJAX_LOADER'] : null,
			'FILTER' => $arResult['FILTER'],
			'FILTER_PRESETS' => $arResult['FILTER_PRESETS'],
			'FILTER_TEMPLATE' => $arResult['INTERNAL'] ? 'popup' : '',
			'SHOW_FORM_TAG' => $arResult['INTERNAL'] && $arResult['INTERNAL_EDIT'] ? 'N' : 'Y',
			'MANAGER' => array(
				'ID' => $gridManagerID,
				'CONFIG' => $gridManagerCfg
			)
		),
		$component
	);

if ($arResult['EVENT_HINT_MESSAGE'] == 'Y' && COption::GetOptionString('order', 'mail', '') != ''):
?>
<div class="order_notice_message"><?=GetMessage('ORDER_IMPORT_EVENT', Array('%EMAIL%' => COption::GetOptionString('order', 'mail', '')));?></div>
<?endif;?>

<script type="text/javascript">
	BX.ready(
		function()
		{
			BX.OrderEventListManager.messages =
			{
				deletionConfirmationDlgTitle: "<?=GetMessageJS('ORDER_EVENT_DELETE_TITLE')?>",
				deletionConfirmationDlgContent: "<?=GetMessageJS('ORDER_EVENT_DELETE_CONFIRM')?>",
				deletionConfirmationDlgBtn: "<?=GetMessageJS('ORDER_EVENT_DELETE')?>"
			};

			BX.OrderEventListManager.create("<?=CUtil::JSEscape($managerID)?>",
				{
					addItemUrl: "/bitrix/components/bitrix/order.event.add/box.php",
					deleteItemUrl: "<?=CUtil::JSEscape($arResult['PATH_TO_EVENT_DELETE'])?>",
					entityTypeName: "<?=CUtil::JSEscape($arResult['ENTITY_TYPE'])?>",
					entityId: "<?=CUtil::JSEscape($arResult['ENTITY_ID'])?>",
					gridId: "<?=CUtil::JSEscape($arResult['GRID_ID'])?>",
					tabId: "<?=CUtil::JSEscape($arResult['TAB_ID'])?>",
					formId: "<?=CUtil::JSEscape($arResult['FORM_ID'])?>"
				}
			);
		}
	);
</script>
