<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;

CUtil::InitJSCore(array('ajax', 'popup'));

$APPLICATION->SetAdditionalCSS('/bitrix/js/order/css/order.css');
$APPLICATION->AddHeadScript('/bitrix/js/order/interface_form.js');
$APPLICATION->AddHeadScript('/bitrix/js/order/order.js');

$fieldName = $arParams['arUserField']['~FIELD_NAME'];
$formName = isset($arParams['form_name']) ? strval($arParams['form_name']) : '';
$fieldUID = strtolower(str_replace('_', '-', $fieldName));
if($formName !== '')
{
	$fieldUID = strtolower(str_replace('_', '-', $formName)).'-'.$fieldUID;
}
$funcSuffix = uniqid();
?>
<div id="order-<?=$fieldUID?>-box">
	<font class="order-button-open">
		<a id="order-<?=$fieldUID?>-open" href="#open" onclick="obOrder[this.id].Open(); return false;" class=""><?=GetMessage('ORDER_FF_CHOISE');?></a>
	</font>
</div>
<script type="text/javascript">
	var _BX_ORDER_FIELD_INIT_<?=$funcSuffix?> = function()
	{
		if(typeof(ORDER) == 'undefined')
		{
			BX.loadCSS('/bitrix/js/order/css/order.css');
			BX.loadScript('/bitrix/js/order/order.js', _BX_ORDER_FIELD_INIT_<?=$funcSuffix?>);
			return;
		}

		ORDER.Set(
			BX('order-<?=$fieldUID?>-open'),
			'<?=CUtil::JSEscape($fieldName)?>',
			'',
			<?echo CUtil::PhpToJsObject($arResult['ELEMENT']);?>,
			false,
			<?=($arResult['MULTIPLE']=='Y'? 'true': 'false')?>,
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
			}
		);
	};

	BX.ready(
		function()
		{
			// Timeout was added for calendar javascript compatibility
			window.setTimeout(_BX_ORDER_FIELD_INIT_<?=$funcSuffix?>, 100);
		}
	);
</script>