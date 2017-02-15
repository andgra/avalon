<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)die();
global $APPLICATION;
$APPLICATION->SetAdditionalCSS('/bitrix/js/order/css/order.css');
$toolbarID =  $arParams['TOOLBAR_ID'];
$prefix =  $toolbarID.'_';
?><div class="bx-order-view-menu" id="<?=htmlspecialcharsbx($toolbarID)?>"><?

$moreItems = array();
$enableMoreButton = false;
$labelText = '';
foreach($arParams['BUTTONS'] as $k => $item):
	if ($item['LABEL'] === true)
	{
		$labelText = isset($item['TEXT']) ? $item['TEXT'] : '';
		continue;
	}
	if(!$enableMoreButton && isset($item['NEWBAR']) && $item['NEWBAR'] === true):
		$enableMoreButton = true;
		continue;
	endif;

	if($enableMoreButton):
		$moreItems[] = $item;
		continue;
	endif;

	$link = isset($item['LINK']) ? $item['LINK'] : '#';
	$text = isset($item['TEXT']) ? $item['TEXT'] : '';
	$title = isset($item['TITLE']) ? $item['TITLE'] : '';
	$type = isset($item['TYPE']) ? $item['TYPE'] : 'context';
	$code = isset($item['CODE']) ? $item['CODE'] : '';
	$visible = isset($item['VISIBLE']) ? (bool)$item['VISIBLE'] : true;
	$target = isset($item['TARGET']) ? $item['TARGET'] : '';

	$iconBtnClassName = '';
	if (isset($item['ICON']))
	{
		$iconBtnClassName = 'order-'.$item['ICON'];
	}

	$onclick = isset($item['ONCLICK']) ? $item['ONCLICK'] : '';
	if ($type == 'toolbar-split-left')
	{
		$item_tmp = reset($item['LINKS']);
		?><span class="order-toolbar-btn-split order-toolbar-btn-left <?=$iconBtnClassName; ?>"
			<? if ($code !== '') { ?> id="<?=htmlspecialcharsbx("{$prefix}{$code}"); ?>"<? } ?>
			<? if (!$visible) { ?> style="display: none;"<? } ?>>
			<span class="order-toolbar-btn-split-l"
				title="<?=(isset($item_tmp['TITLE']) ? htmlspecialcharsbx($item_tmp['TITLE']) : ''); ?>"
				<? if (isset($item_tmp['ONCLICK'])) { ?> onclick="<?=htmlspecialcharsbx($item_tmp['ONCLICK']); ?>; return false;"<? } ?>>
				<span class="order-toolbar-btn-split-bg"><span class="order-toolbar-btn-icon"></span><?
					echo (isset($item_tmp['TEXT']) ? htmlspecialcharsbx($item_tmp['TEXT']) : '');
				?></span>
			</span><span class="order-toolbar-btn-split-r" onclick="btnMenu_<?=$k; ?>.ShowMenu(this);">
			<span class="order-toolbar-btn-split-bg"></span></span>
		</span>
		<script>
			var btnMenu_<?=$k; ?> = new PopupMenu('bxBtnMenu_<?=$k; ?>', 1010);
			btnMenu_<?=$k; ?>.SetItems([
				<? foreach ($item['LINKS'] as $v) { ?>
				{
					'DEFAULT': <?=(isset($v['DEFAULT']) && $v['DEFAULT'] ? 'true' : 'false'); ?>,
					'DISABLED': <?=(isset($v['DISABLED']) && $v['DISABLED'] ? 'true' : 'false'); ?>,
					'ICONCLASS': "<?=(isset($v['ICONCLASS']) ? htmlspecialcharsbx($v['ICONCLASS']) : ''); ?>",
					'ONCLICK': "<?=(isset($v['ONCLICK']) ? $v['ONCLICK'] : ''); ?>; return false;",
					'TEXT': "<?=(isset($v['TEXT']) ? htmlspecialcharsbx($v['TEXT']) : ''); ?>",
					'TITLE': "<?=(isset($v['TITLE']) ? htmlspecialcharsbx($v['TITLE']) : ''); ?>"
				},
				<? } ?>
			]);
		</script><?
	}
	else if ($type == 'toolbar-left')
	{
		?><a class="order-toolbar-btn order-toolbar-btn-left <?=$iconBtnClassName; ?>"
			<? if ($code !== '') { ?> id="<?=htmlspecialcharsbx("{$prefix}{$code}"); ?>"<? } ?>
			href="<?=htmlspecialcharsbx($link)?>"
			<? if($target !== '') { ?> target="<?=$target?>"<? } ?>
			title="<?=htmlspecialcharsbx($title)?>"
			<? if ($onclick !== '') { ?> onclick="<?=htmlspecialcharsbx($onclick); ?>; return false;"<? } ?>
			<? if (!$visible) { ?> style="display: none;"<? } ?>>
			<span class="order-toolbar-btn-icon"></span><span><?=htmlspecialcharsbx($text); ?></span></a><?
	}
	else if ($type == 'toolbar-conv-scheme')
	{
		$params = isset($item['PARAMS']) ? $item['PARAMS'] : array();

		$schemeName = isset($params['SCHEME_NAME']) ? $params['SCHEME_NAME'] : null;
		$schemeDescr = isset($params['SCHEME_DESCRIPTION']) ? $params['SCHEME_DESCRIPTION'] : null;
		$name = isset($params['NAME']) ? $params['NAME'] : $code;
		$entityID = isset($params['ENTITY_ID']) ? (int)$params['ENTITY_ID'] : 0;
		$entityTypeID = isset($params['ENTITY_TYPE_ID']) ? (int)$params['ENTITY_TYPE_ID'] : COrderOwnerType::Undefined;
		$isPermitted = isset($params['IS_PERMITTED']) ? (bool)$params['IS_PERMITTED'] : false;
		$lockScript = isset($params['LOCK_SCRIPT']) ? $params['LOCK_SCRIPT'] : '';

		$options = CUserOptions::GetOption("order.interface.toobar", "conv_scheme_selector", array());
		$hintKey = 'enable_'.strtolower($name).'_hint';
		$enableHint = !(isset($options[$hintKey]) && $options[$hintKey] === 'N');
		$hint = isset($params['HINT']) ? $params['HINT'] : array();

		$iconBtnClassName = $isPermitted ? 'order-btn-convert' : 'order-btn-convert order-btn-convert-blocked';
		$originUrl = $APPLICATION->GetCurPage();

		$containerID = "{$prefix}{$code}";
		$labelID = "{$prefix}{$code}_label";
		$buttonID = "{$prefix}{$code}_button";

		if($isPermitted && $entityTypeID === COrderOwnerType::Lead)
		{
			Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/order/order.js');
		}
		?>
		<span class="order-btn-convert-wrap">
			<a class="bx-context-button <?=$iconBtnClassName; ?>"
				id="<?=htmlspecialcharsbx($containerID); ?>"
				href="<?=htmlspecialcharsbx($link)?>"
				<? if($target !== '') { ?> target="<?=$target?>"<? } ?>
				title="<?=htmlspecialcharsbx($title)?>"
				onclick="return false;"
				<? if (!$visible) { ?> style="display: none;"<? } ?>>
				<span class="bx-context-button-icon"></span>
				<span>
					<?=htmlspecialcharsbx($text);?>
					<span class="order-btn-convert-text" id="<?=htmlspecialcharsbx($labelID);?>">
						<?=htmlspecialcharsbx($schemeDescr)?>
					</span>
				</span>
			</a>
			<span class="order-btn-convert-arrow" id="<?=htmlspecialcharsbx($buttonID);?>"></span><?
			?><script type="text/javascript">
				BX.ready(
					function()
					{
						//region Toolbar script
						<?$selectorID = CUtil::JSEscape($name);?>
						<?$originUrl = CUtil::JSEscape($originUrl);?>
						<?if($isPermitted):?>
							<?if($entityTypeID === COrderOwnerType::Lead):?>
								BX.OrderLeadConversionSchemeSelector.create(
									"<?=$selectorID?>",
									{
										entityId: <?=$entityID?>,
										scheme: "<?=$schemeName?>",
										containerId: "<?=$containerID?>",
										labelId: "<?=$labelID?>",
										buttonId: "<?=$buttonID?>",
										originUrl: "<?=$originUrl?>",
										enableHint: <?=CUtil::PhpToJSObject($enableHint)?>,
										hintMessages: <?=CUtil::PhpToJSObject($hint)?>
									}
								);
							<?elseif($entityTypeID === COrderOwnerType::Deal):?>
								BX.OrderDealConversionSchemeSelector.create(
									"<?=$selectorID?>",
									{
										entityId: <?=$entityID?>,
										scheme: "<?=$schemeName?>",
										containerId: "<?=$containerID?>",
										labelId: "<?=$labelID?>",
										buttonId: "<?=$buttonID?>",
										originUrl: "<?=$originUrl?>",
										enableHint: <?=CUtil::PhpToJSObject($enableHint)?>,
										hintMessages: <?=CUtil::PhpToJSObject($hint)?>
									}
								);

								BX.addCustomEvent(window,
									"OrderCreateQuoteFromDeal",
									function()
									{
										BX.OrderDealConverter.getCurrent().convert(
											<?=$entityID?>,
											BX.OrderDealConversionScheme.createConfig(BX.OrderDealConversionScheme.quote),
											"<?=$originUrl?>"
										);
									}
								);

								BX.addCustomEvent(window,
									"OrderCreateInvoiceFromDeal",
									function()
									{
										BX.OrderDealConverter.getCurrent().convert(
											<?=$entityID?>,
											BX.OrderDealConversionScheme.createConfig(BX.OrderDealConversionScheme.invoice),
											"<?=$originUrl?>"
										);
									}
								);
							<?elseif($entityTypeID === COrderOwnerType::Quote):?>
								BX.OrderQuoteConversionSchemeSelector.create(
									"<?=$selectorID?>",
									{
										entityId: <?=$entityID?>,
										scheme: "<?=$schemeName?>",
										containerId: "<?=$containerID?>",
										labelId: "<?=$labelID?>",
										buttonId: "<?=$buttonID?>",
										originUrl: "<?=$originUrl?>",
										enableHint: <?=CUtil::PhpToJSObject($enableHint)?>,
										hintMessages: <?=CUtil::PhpToJSObject($hint)?>
									}
								);

								BX.addCustomEvent(window,
									"OrderCreateDealFromQuote",
									function()
									{
										BX.OrderQuoteConverter.getCurrent().convert(
											<?=$entityID?>,
											BX.OrderQuoteConversionScheme.createConfig(BX.OrderQuoteConversionScheme.deal),
											"<?=$originUrl?>"
										);
									}
								);

								BX.addCustomEvent(window,
									"OrderCreateInvoiceFromQuote",
									function()
									{
										BX.OrderQuoteConverter.getCurrent().convert(
											<?=$entityID?>,
											BX.OrderQuoteConversionScheme.createConfig(BX.OrderQuoteConversionScheme.invoice),
											"<?=$originUrl?>"
										);
									}
								);
							<?endif;?>
						<?elseif($lockScript !== ''):?>
							var showLockInfo = function()
							{
								<?=$lockScript?>
							};
							BX.bind(BX("<?=$containerID?>"), "click", showLockInfo );
							<?if($entityTypeID === COrderOwnerType::Deal):?>
								BX.addCustomEvent(window, "OrderCreateQuoteFromDeal", showLockInfo);
								BX.addCustomEvent(window, "OrderCreateInvoiceFromDeal", showLockInfo);
							<?elseif($entityTypeID === COrderOwnerType::Quote):?>
								BX.addCustomEvent(window, "OrderCreateDealFromQuote", showLockInfo);
								BX.addCustomEvent(window, "OrderCreateInvoiceFromQuote", showLockInfo);
							<?endif;?>
						<?endif;?>
						//endregion
					}
				);
			</script><?
		?></span><?
	}
	else
	{
		?><a class="bx-context-button <?=$iconBtnClassName; ?>"
			<? if ($code !== '') { ?> id="<?=htmlspecialcharsbx("{$prefix}{$code}"); ?>"<? } ?>
			href="<?=htmlspecialcharsbx($link)?>"
			<? if($target !== '') { ?> target="<?=$target?>"<? } ?>
			title="<?=htmlspecialcharsbx($title)?>"
			<? if ($onclick !== '') { ?> onclick="<?=htmlspecialcharsbx($onclick); ?>; return false;"<? } ?>
			<? if (!$visible) { ?> style="display: none;"<? } ?>>
			<span class="bx-context-button-icon"></span><span><?=htmlspecialcharsbx($text); ?></span></a><?
	}

endforeach;
if(!empty($moreItems)):
	?><a class="bx-context-button order-btn-more" href="#">
		<span class="bx-context-button-icon"></span>
		<span><?=htmlspecialcharsbx(GetMessage('ORDER_INTERFACE_TOOLBAR_BTN_MORE'))?></span>
	</a>
	<script type="text/javascript">
		BX.ready(
			function()
			{
				BX.InterfaceToolBar.create(
					"<?=CUtil::JSEscape($toolbarID)?>",
					BX.OrderParamBag.create(
						{
							"containerId": "<?=CUtil::JSEscape($toolbarID)?>",
							"prefix": "<?=CUtil::JSEscape($prefix)?>",
							"moreButtonClassName": "order-btn-more",
							"items": <?=CUtil::PhpToJSObject($moreItems)?>
						}
					)
				);
			}
		);
	</script>
<?
endif;
if ($labelText != ''):
?><div class="order-toolbar-label2"><span id="<?= $toolbarID.'_label' ?>"><?=htmlspecialcharsbx($labelText)?></span></div><?
endif;
?></div>
