<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;
$APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/order-entity-show.css');
$APPLICATION->IncludeComponent(
	'newportal:order.control_panel',
	'',
	array(
		'ID' => 'CONFIG_PERMS',
		'ACTIVE_ITEM_ID' => 'CONFIG',
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

if($arResult['NEED_FOR_REBUILD_COMPANY_ATTRS']):
	?><div id="rebuildCompanyAttrsMsg" class="order-view-message">
		<?=GetMessage('ORDER_CONFIG_PERMS_REBUILD_COMPANY_ATTRS', array('#ID#' => 'rebuildCompanyAttrsLink', '#URL#' => '#'))?>
	</div><?
endif;

if($arResult['NEED_FOR_REBUILD_CONTACT_ATTRS']):
	?><div id="rebuildContactAttrsMsg" class="order-view-message">
		<?=GetMessage('ORDER_CONFIG_PERMS_REBUILD_CONTACT_ATTRS', array('#ID#' => 'rebuildContactAttrsLink', '#URL#' => '#'))?>
	</div><?
endif;

if($arResult['NEED_FOR_REBUILD_DEAL_ATTRS']):
	?><div id="rebuildDealAttrsMsg" class="order-view-message">
		<?=GetMessage('ORDER_CONFIG_PERMS_REBUILD_DEAL_ATTRS', array('#ID#' => 'rebuildDealAttrsLink', '#URL#' => '#'))?>
	</div><?
endif;

if($arResult['NEED_FOR_REBUILD_LEAD_ATTRS']):
	?><div id="rebuildLeadAttrsMsg" class="order-view-message">
		<?=GetMessage('ORDER_CONFIG_PERMS_REBUILD_LEAD_ATTRS', array('#ID#' => 'rebuildLeadAttrsLink', '#URL#' => '#'))?>
	</div><?
endif;

if($arResult['NEED_FOR_REBUILD_QUOTE_ATTRS']):
	?><div id="rebuildQuoteAttrsMsg" class="order-view-message">
		<?=GetMessage('ORDER_CONFIG_PERMS_REBUILD_QUOTE_ATTRS', array('#ID#' => 'rebuildQuoteAttrsLink', '#URL#' => '#'))?>
	</div><?
endif;

if($arResult['NEED_FOR_REBUILD_INVOICE_ATTRS']):
	?><div id="rebuildInvoiceAttrsMsg" class="order-view-message">
		<?=GetMessage('ORDER_CONFIG_PERMS_REBUILD_INVOICE_ATTRS', array('#ID#' => 'rebuildInvoiceAttrsLink', '#URL#' => '#'))?>
	</div><?
endif;

$APPLICATION->IncludeComponent(
	'newportal:order.config.perms.role.edit',
	'',
	Array(
		'ROLE_ID' => $arResult['VARIABLES']['role_id'],
		'PATH_TO_ROLE_EDIT' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['role_edit'],
		'PATH_TO_ENTITY_LIST' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['entity_list']
	),
	$component
);

if($arResult['NEED_FOR_REBUILD_COMPANY_ATTRS']
	|| $arResult['NEED_FOR_REBUILD_CONTACT_ATTRS']
	|| $arResult['NEED_FOR_REBUILD_DEAL_ATTRS']
	|| $arResult['NEED_FOR_REBUILD_LEAD_ATTRS']
	|| $arResult['NEED_FOR_REBUILD_QUOTE_ATTRS']
	|| $arResult['NEED_FOR_REBUILD_INVOICE_ATTRS']):
?><script type="text/javascript">
BX.ready(
	function()
	{
		BX.OrderEntityAccessManager.messages =
		{
			rebuildCompanyAccessAttrsDlgTitle: "<?=GetMessageJS('ORDER_CONFIG_PERMS_REBUILD_COMPANY_ATTR_DLG_TITLE')?>",
			rebuildCompanyAccessAttrsDlgSummary: "<?=GetMessageJS('ORDER_CONFIG_PERMS_REBUILD_COMPANY_ATTR_DLG_SUMMARY')?>",
			rebuildContactAccessAttrsDlgTitle: "<?=GetMessageJS('ORDER_CONFIG_PERMS_REBUILD_CONTACT_ATTR_DLG_TITLE')?>",
			rebuildContactAccessAttrsDlgSummary: "<?=GetMessageJS('ORDER_CONFIG_PERMS_REBUILD_CONTACT_ATTR_DLG_SUMMARY')?>",
			rebuildDealAccessAttrsDlgTitle: "<?=GetMessageJS('ORDER_CONFIG_PERMS_REBUILD_DEAL_ATTR_DLG_TITLE')?>",
			rebuildDealAccessAttrsDlgSummary: "<?=GetMessageJS('ORDER_CONFIG_PERMS_REBUILD_DEAL_ATTR_DLG_SUMMARY')?>",
			rebuildLeadAccessAttrsDlgTitle: "<?=GetMessageJS('ORDER_CONFIG_PERMS_REBUILD_LEAD_ATTR_DLG_TITLE')?>",
			rebuildLeadAccessAttrsDlgSummary: "<?=GetMessageJS('ORDER_CONFIG_PERMS_REBUILD_LEAD_ATTR_DLG_SUMMARY')?>",
			rebuildQuoteAccessAttrsDlgTitle: "<?=GetMessageJS('ORDER_CONFIG_PERMS_REBUILD_QUOTE_ATTR_DLG_TITLE')?>",
			rebuildQuoteAccessAttrsDlgSummary: "<?=GetMessageJS('ORDER_CONFIG_PERMS_REBUILD_QUOTE_ATTR_DLG_SUMMARY')?>",
			rebuildInvoiceAccessAttrsDlgTitle: "<?=GetMessageJS('ORDER_CONFIG_PERMS_REBUILD_INVOICE_ATTR_DLG_TITLE')?>",
			rebuildInvoiceAccessAttrsDlgSummary: "<?=GetMessageJS('ORDER_CONFIG_PERMS_REBUILD_INVOICE_ATTR_DLG_SUMMARY')?>"
		};
		BX.OrderLongRunningProcessDialog.messages =
		{
			startButton: "<?=GetMessageJS('ORDER_CONFIG_PERMS_LRP_DLG_BTN_START')?>",
			stopButton: "<?=GetMessageJS('ORDER_CONFIG_PERMS_LRP_DLG_BTN_STOP')?>",
			closeButton: "<?=GetMessageJS('ORDER_CONFIG_PERMS_LRP_DLG_BTN_CLOSE')?>",
			wait: "<?=GetMessageJS('ORDER_CONFIG_PERMS_LRP_DLG_WAIT')?>",
			requestError: "<?=GetMessageJS('ORDER_CONFIG_PERMS_LRP_DLG_REQUEST_ERR')?>"
		};

		var mgr = BX.OrderEntityAccessManager.create("mgr", { serviceUrl: "<?=SITE_DIR?>bitrix/components/bitrix/order.config.perms/ajax.php?&<?=bitrix_sessid_get()?>" });
		//COMPANY
		BX.addCustomEvent(
			mgr,
			"ON_COMPANY_ATTRS_REBUILD_COMPLETE",
			function()
			{
				var msg = BX("rebuildCompanyAttrsMsg");
				if(msg)
				{
					msg.style.display = "none";
				}
			}
		);
		var companyLink = BX("rebuildCompanyAttrsLink");
		if(companyLink)
		{
			BX.bind(
				companyLink,
				"click",
				function(e)
				{
					mgr.rebuildCompanyAttrs();
					return BX.PreventDefault(e);
				}
			);
		}
		//CONTACT
		BX.addCustomEvent(
			mgr,
			"ON_CONTACT_ATTRS_REBUILD_COMPLETE",
			function()
			{
				var msg = BX("rebuildContactAttrsMsg");
				if(msg)
				{
					msg.style.display = "none";
				}
			}
		);
		var contactLink = BX("rebuildContactAttrsLink");
		if(contactLink)
		{
			BX.bind(
				contactLink,
				"click",
				function(e)
				{
					mgr.rebuildContactAttrs();
					return BX.PreventDefault(e);
				}
			);
		}
		//DEAL
		BX.addCustomEvent(
			mgr,
			"ON_DEAL_ATTRS_REBUILD_COMPLETE",
			function()
			{
				var msg = BX("rebuildDealAttrsMsg");
				if(msg)
				{
					msg.style.display = "none";
				}
			}
		);
		var dealLink = BX("rebuildDealAttrsLink");
		if(dealLink)
		{
			BX.bind(
				dealLink,
				"click",
				function(e)
				{
					mgr.rebuildDealAttrs();
					return BX.PreventDefault(e);
				}
			);
		}
		//LEAD
		BX.addCustomEvent(
			mgr,
			"ON_LEAD_ATTRS_REBUILD_COMPLETE",
			function()
			{
				var msg = BX("rebuildLeadAttrsMsg");
				if(msg)
				{
					msg.style.display = "none";
				}
			}
		);
		var leadLink = BX("rebuildLeadAttrsLink");
		if(leadLink)
		{
			BX.bind(
				leadLink,
				"click",
				function(e)
				{
					mgr.rebuildLeadAttrs();
					return BX.PreventDefault(e);
				}
			);
		}
		//QUOTE
		BX.addCustomEvent(
			mgr,
			"ON_QUOTE_ATTRS_REBUILD_COMPLETE",
			function()
			{
				var msg = BX("rebuildQuoteAttrsMsg");
				if(msg)
				{
					msg.style.display = "none";
				}
			}
		);
		var quoteLink = BX("rebuildQuoteAttrsLink");
		if(quoteLink)
		{
			BX.bind(
				quoteLink,
				"click",
				function(e)
				{
					mgr.rebuildQuoteAttrs();
					return BX.PreventDefault(e);
				}
			);
		}
		//INVOICE
		BX.addCustomEvent(
			mgr,
			"ON_INVOICE_ATTRS_REBUILD_COMPLETE",
			function()
			{
				var msg = BX("rebuildInvoiceAttrsMsg");
				if(msg)
				{
					msg.style.display = "none";
				}
			}
		);
		var invoiceLink = BX("rebuildInvoiceAttrsLink");
		if(invoiceLink)
		{
			BX.bind(
				invoiceLink,
				"click",
				function(e)
				{
					mgr.rebuildInvoiceAttrs();
					return BX.PreventDefault(e);
				}
			);
		}
	}
);
</script><?
endif;