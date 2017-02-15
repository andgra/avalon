<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;
$APPLICATION->SetAdditionalCSS('/bitrix/js/order/css/order.css');
COrderComponentHelper::RegisterScriptLink('/bitrix/js/order/common.js');
CJSCore::Init(array('popup', 'date'));

$ID = $arResult['ID'];
$IDLc = strtolower($ID);
$items = isset($arResult['ITEMS']) ? $arResult['ITEMS'] : array();
$activeItemID =  isset($arResult['ACTIVE_ITEM_ID']) ? $arResult['ACTIVE_ITEM_ID'] : '';
$containerID = "order_ctrl_panel_{$IDLc}";
$wrapperID = "order_ctrl_panel_wrap_{$IDLc}";
$itemWrapperID = "order_ctrl_panel_items_{$IDLc}";
$itemContainerPrefix = "order_ctrl_panel_item_{$IDLc}_";
$itemInfos = array();
$enableSearch = isset($arResult['ENABLE_SEARCH']) ? $arResult['ENABLE_SEARCH'] : true;
$searchContainerID = "order_ctrl_panel_{$IDLc}_search";
$additionaItem = isset($arResult['ADDITIONAL_ITEM']) ? $arResult['ADDITIONAL_ITEM'] : null;
$additionalItemInfo = null;
$isFixed = isset($arResult['IS_FIXED']) ? $arResult['IS_FIXED'] : false;

$itemContainerIDs = array();
$additionalContainerID = '';
?>
<div id="<?=htmlspecialcharsbx($containerID)?>" class="order-header">
<div id="<?=htmlspecialcharsbx($wrapperID)?>" class="order-header-inner">
	<div id="<?=htmlspecialcharsbx($itemWrapperID)?>" class="order-menu-wrap" <?=$enableSearch==true?'':'style="width:100%"'?>><?
		foreach($items as &$item):
			$itemID = isset($item['ID']) ? $item['ID'] : '';
			$itemIDLc = strtolower($itemID);
			$isActive = $itemID === $activeItemID;
			$url = isset($item['URL']) ? $item['URL'] : '#';
			$icon = isset($item['ICON']) ? strtolower($item['ICON']) : '';
			$name = isset($item['NAME']) ? $item['NAME'] : $itemID;
			$briefName = isset($item['BRIEF_NAME']) ? $item['BRIEF_NAME'] : '';
			if($briefName === '')
				$briefName = $name;

			$title = isset($item['TITLE']) ? $item['TITLE'] : '';
			$counter = isset($item['COUNTER']) ? intval($item['COUNTER']) : 0;

			$itemInfo = array(
				'id' => $itemID,
				'name' => $name,
				'icon' => $icon,
				'isActive' => $isActive,
				'url' => $url,
				'actions' => array(),
				'childItems' => array()
			);

			$actions = isset($item['ACTIONS']) ? $item['ACTIONS'] : array();
			foreach($actions as &$action):
				$actionID = isset($action['ID']) ? $action['ID'] : '';
				if($actionID === '')
					continue;

				$itemInfo['actions'][] = array(
					'id' => $actionID,
					'url' => isset($action['URL']) ? $action['URL'] : '',
					'script' => isset($action['SCRIPT']) ? $action['SCRIPT'] : ''
				);
			endforeach;
			unset($action);

			$childItems = isset($item['CHILD_ITEMS']) ? $item['CHILD_ITEMS'] : array();
			foreach($childItems as &$childItem):
				$childItemID = isset($childItem['ID']) ? $childItem['ID'] : '';
				if($childItemID === '')
					continue;

				$itemInfo['childItems'][] = array(
					'id' => $childItemID,
					'name' => isset($childItem['NAME']) ? $childItem['NAME'] : '',
					'icon' => isset($childItem['ICON']) ? $childItem['ICON'] : '',
					'url' => isset($childItem['URL']) ? $childItem['URL'] : ''
				);
			endforeach;
			unset($childItem);

			$itemInfos[] = &$itemInfo;
			unset($itemInfo);

			$itemContainerID = "{$itemContainerPrefix}{$itemIDLc}";
			$itemContainerIDs[] = $itemContainerID;
			?><div class="order-menu-item-wrap" id="<?=htmlspecialcharsbx($itemContainerID)?>"><a href="<?=htmlspecialcharsbx($url)?>" class="order-menu-item<?=$icon !== '' ? ' order-menu-'.htmlspecialcharsbx($icon) : ''?><?=$isActive ? ' order-menu-item-active' : ''?>" title="<?=htmlspecialcharsbx($title)?>"><span class="order-menu-icon"></span><span class="order-menu-name"><?=htmlspecialcharsbx($briefName)?></span><?
				if ($itemID == 'STREAM'):
					?><span class="order-menu-icon-counter order-menu-icon-counter-grey" id="order_menu_counter" style="display: <?=($counter > 0 ? "inline-block": "none")?>;"><?=$counter <= 99 ? $counter : '99+' ?></span><?
				elseif($counter > 0):
					?><span class="order-menu-icon-counter"><?=$counter <= 99 ? $counter : '99+' ?></span><?
				endif;
			?></a></div><?
		endforeach;
		unset($item);
		if(is_array($additionaItem)):
			$icon = isset($additionaItem['ICON']) ? strtolower($additionaItem['ICON']) : '';
			if($icon === '')
				$icon = 'more';

			$itemID = isset($additionaItem['ID']) ? $additionaItem['ID'] : '';
			$itemIDLc = strtolower($itemID);
			$name = isset($additionaItem['NAME']) ? $additionaItem['NAME'] : $itemID;
			$title = isset($additionaItem['TITLE']) ? $additionaItem['TITLE'] : '';

			$additionalItemInfo = array(
				'id' => $itemID,
				'name' => $name,
				'icon' => $icon,
				'isActive' => false,
				'url' => '#',
				'actions' => array(),
				'childItems' => array()
			);

			$additionalContainerID = "{$itemContainerPrefix}{$itemIDLc}";
			?><div class="order-menu-item-wrap" id="<?=htmlspecialcharsbx($additionalContainerID)?>" style="display: none;">
				<a href="#" class="order-menu-item order-menu-<?=htmlspecialcharsbx($icon)?>" title="<?=htmlspecialcharsbx($title)?>">
					<span class="order-menu-icon"></span>
					<span class="order-menu-name"><?=htmlspecialcharsbx($name)?></span>
				</a>
			</div><?
		endif;
	?></div><?
	if($enableSearch):
		$searchInputID = "order_ctrl_panel_{$IDLc}_search_input";
	?><span id="<?=htmlspecialcharsbx($searchContainerID)?>" class="order-search-block">
		<form class="order-search" action="<?=htmlspecialcharsbx($arResult['SEARCH_PAGE_URL'])?>" method="get">
			<span class="order-search-btn"></span>
			<span class="order-search-inp-wrap"><input id="<?=htmlspecialcharsbx($searchInputID)?>" class="order-search-inp" name="q" type="text" autocomplete="off" placeholder="<?=htmlspecialcharsbx(GetMessage('ORDER_CONTROL_PANEL_SEARCH_PLACEHOLDER'))?>"/></span>
			<input type="hidden" name="where" value="order" /><?
			$APPLICATION->IncludeComponent(
				'newportal:search.title',
				'backend',
				array(
					'NUM_CATEGORIES' => 1,
					'CATEGORY_0_TITLE' => 'ORDER',
					'CATEGORY_0' => array(0 => 'order'),
					'USE_LANGUAGE_GUESS' => 'N',
					'PAGE' => $arResult['PATH_TO_SEARCH_PAGE'],
					'CONTAINER_ID' => $searchContainerID,
					'INPUT_ID' => $searchInputID,
					'SHOW_INPUT' => 'N'
				),
				$component,
				array('HIDE_ICONS'=>true)
			);
		?></form>
	</span>
	<?endif;?>
	<span class="order-menu-shadow">
		<span class="order-menu-shadow-right">
			<span class="order-menu-shadow-center"></span>
		</span>
	</span>
	<span class="order-menu-fixed-btn <?=$isFixed ? 'order-lead-header-contact-btn order-lead-header-contact-btn-pin' : 'order-lead-header-contact-btn order-lead-header-contact-btn-unpin'?>">
	</span>
</div>
</div>
<script type="text/javascript" bxrunfirst="true">
	(function()
		{
			if(typeof(BX.OrderControlPanelSliderInitData) === "undefined")
			{
				BX.OrderControlPanelSliderInitData = {};
			}

			var containers = <?=CUtil::PhpToJSObject($itemContainerIDs)?>;
			var lastIndex = containers.length - 1;
			if(lastIndex < 0)
			{
				return;
			}

			var additional = document.getElementById("<?=CUtil::JSEscape($additionalContainerID)?>");
			if(!additional)
			{
				return;
			}

			var first = document.getElementById(containers[0]);
			var ceiling = BX.pos(first).top;

			var borderIndex = -1;
			for(var j = lastIndex; j > 0; j--)
			{
				var current = document.getElementById(containers[j]);
				if(BX.pos(current).top <= ceiling)
				{
					borderIndex = j;
					break;
				}
			}

			if(borderIndex < 0)
			{
				borderIndex = 0;
			}

			if(borderIndex < lastIndex)
			{
				var border = document.getElementById(containers[borderIndex]);
				border.parentNode.insertBefore(additional, border);
				additional.style.display = "";
			}
			BX.OrderControlPanelSliderInitData["<?=CUtil::JSEscape($ID)?>"] = { borderingItemIndex: borderIndex };
		}
	)();
</script>
<script type="text/javascript">
	BX.ready(
			function()
			{
				var panel = BX.OrderControlPanel.create(
						"<?=CUtil::JSEscape($ID)?>",
						BX.OrderParamBag.create(
							{
								"containerId": "<?=CUtil::JSEscape($containerID)?>",
								"wrapperId": "<?=CUtil::JSEscape($wrapperID)?>",
								"itemContainerPrefix": "<?=CUtil::JSEscape($itemContainerPrefix)?>",
								"itemInfos": <?=CUtil::PhpToJSObject($itemInfos)?>,
								"additionalItemInfo": <?=is_array($additionalItemInfo) ? CUtil::PhpToJSObject($additionalItemInfo) : 'null' ?>,
								"itemWrapperId": "<?=CUtil::JSEscape($itemWrapperID)?>",
								"searchContainerId": "<?=CUtil::JSEscape($searchContainerID)?>",
								"anchorId": "<?=CUtil::JSEscape($searchContainerID)?>",
								"isFixed": <?= $isFixed ? 'true' : 'false'?>
							}
						)
				);
				BX.OrderControlPanel.setDefault(panel);
			}
	);
</script>