<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CUtil::InitJSCore(array('ajax', 'popup'));

$APPLICATION->SetAdditionalCSS('/bitrix/js/order/css/order.css');
$APPLICATION->AddHeadScript('/bitrix/js/order/interface_form.js');
$APPLICATION->AddHeadScript('/bitrix/js/order/order.js');
$fieldName = $arParams["arUserField"]["~FIELD_NAME"];
$formPressetName = $arParams["form_name"];
$formPressetChoiseName = str_replace('filter_', 'filters_', $formPressetName);
?>


<div id="order-<?=$fieldName?>-box">
	<div  class="order-button-open">
		<a id="order-<?=$fieldName?>-open" href="#open" onclick="return ORDER_set<?=$fieldName?>(this, true)"><?=GetMessage('ORDER_FF_CHOISE');?></a>
		<input id="order-<?=$fieldName?>-input-temp" type="text" name="<?=$fieldName?>"  style="display:none" onchange="return ORDER_set<?=$fieldName?>(BX.findPreviousSibling(this, { 'tagName':'A' }), false)" />
	</div>
</div>

<script type="text/javascript">
	function ORDER_set<?=$fieldName?>(el, bOpen)
	{
		var subIdName = '';
		if (document.forms['<?=CUtil::JSEscape($formPressetChoiseName)?>'])
		{
			subIdName = document.forms['<?=CUtil::JSEscape($formPressetChoiseName)?>'].filters_list.value;
			if (!subIdName)
				subIdName = 'add'+Math.round(Math.random()*1000000);

			BX.addCustomEvent('onWindowClose', function(ev, dd) {
				var opt = document.forms['<?=CUtil::JSEscape($formPressetChoiseName)?>'].filters_list;
				if (!opt)
					return ;

				for(var i = 0; i < opt.options.length; i++)
				{
					for (var j in obOrder)
					{
						if (j.indexOf(opt.options[i].value) != -1)
							obOrder[j].Clear();
					}
				}
			});
		}
		var orderID = ORDER.Set(
			el,
			'<?=CUtil::JSEscape($fieldName)?>',
			subIdName,
			<?echo CUtil::PhpToJsObject($arResult['ELEMENT']);?>,
			false,
			false,
			<?echo CUtil::PhpToJsObject($arResult['ENTITY_TYPE']);?>,
			{
				'physical': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_PHYSICAL'))?>',
				'contact': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_CONTACT'))?>',
				'agent': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_AGENT'))?>',
				'course': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_COURSE'))?>',
				'direction': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_DIRECTION'))?>',
				'group': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_GROUP'))?>',
				'formed_group': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_FORMED_GROUP'))?>',
				'nomen': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_NOMEN'))?>',
				'reg': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_REG'))?>',
				'app': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_APP'))?>',
				'user': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_USER'))?>',
				'lead': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_LEAD'))?>',
				'ok': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_OK'))?>',
				'cancel': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_CANCEL'))?>',
				'close': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_CLOSE'))?>',
				'wait': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_SEARCH'))?>',
				'noresult': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_NO_RESULT'))?>',
				'add' : '<?=CUtil::JSEscape(GetMessage('ORDER_FF_CHOISE'))?>',
				'edit' : '<?=CUtil::JSEscape(GetMessage('ORDER_FF_CHANGE'))?>',
				'search' : '<?=CUtil::JSEscape(GetMessage('ORDER_FF_SEARCH'))?>',
				'last' : '<?=CUtil::JSEscape(GetMessage('ORDER_FF_LAST'))?>'
			});

		// temporary input, need for correct job presets
		if (el.nextElementSibling)
		{
			var tmpInput = el.nextElementSibling;
			var tmpInputValue = '';
			tmpInputValue = tmpInput.value;
			//tmpInput.parentNode.removeChild(tmpInput);
			if (tmpInputValue != '')
				obOrder[orderID].PopupSetItem(tmpInputValue);
		}

		if (bOpen && obOrder[orderID])
			obOrder[orderID].Open();

		return false;
	}

	// through "ready" necessary because the presets are initialized so
	BX.ready(function() {
		if (document.forms['<?=$formPressetName?>'])
		{
			var el_a = BX.findChild(document.forms['<?=$formPressetName?>'], {attr : {id : "order-<?=$fieldName?>-open"}}, true, false);
			if (el_a)
				ORDER_set<?=$fieldName?>(el_a, false);
		}
	})

</script>