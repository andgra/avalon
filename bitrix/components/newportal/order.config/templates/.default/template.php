<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arResult */

$siteDir = rtrim(SITE_DIR, '/');

/* Menu items */
$tabs = array();
//$tabs['where_to_begin'] = GetMessage("ORDER_CONFIG_TAB_WHERE_TO_BEGIN");
//$tabs['settings_forms_and_reports'] = GetMessage("ORDER_CONFIG_TAB_SETTINGS_FORMS_AND_REPORTS");
//$tabs['creation_on_the_basis'] = GetMessage("ORDER_CONFIG_TAB_CREATION_ON_THE_BASIS");
//$tabs['printed_forms_of_documents'] = GetMessage("ORDER_CONFIG_TAB_PRINTED_FORMS_OF_DOCUMENTS");
$tabs['rights'] = GetMessage("ORDER_CONFIG_TAB_RIGHTS");
//$tabs['automation'] = GetMessage("ORDER_CONFIG_TAB_AUTOMATION");
//$tabs['work_with_mail'] = GetMessage("ORDER_CONFIG_TAB_WORK_WITH_MAIL");
//$tabs['integration'] = GetMessage("ORDER_CONFIG_TAB_INTEGRATION");
/*if($arResult['BITRIX24'])
	$tabs['apps'] = GetMessage("ORDER_CONFIG_TAB_APPS");*/
$tabs['other'] = GetMessage("ORDER_CONFIG_TAB_OTHER");

/* Settings items */
$items = array();
if($arResult['PERM_CONFIG'])
{
	/*$items['tab_content_where_to_begin']['STATUS']['URL'] = $siteDir.'/order/config/status/';
	$items['tab_content_where_to_begin']['STATUS']['ICON_CLASS'] = 'img-book';
	$items['tab_content_where_to_begin']['STATUS']['NAME'] = GetMessage("ORDER_CONFIG_STATUS");
	$items['tab_content_where_to_begin']['CURRENCY']['URL'] = $siteDir.'/order/config/currency/';
	$items['tab_content_where_to_begin']['CURRENCY']['ICON_CLASS'] = 'img-curr';
	$items['tab_content_where_to_begin']['CURRENCY']['NAME'] = GetMessage("ORDER_CONFIG_CURRENCY");
	$items['tab_content_where_to_begin']['LOCATIONS']['URL'] = $siteDir.'/order/config/locations/';
	$items['tab_content_where_to_begin']['LOCATIONS']['ICON_CLASS'] = 'img-location';
	$items['tab_content_where_to_begin']['LOCATIONS']['NAME'] = GetMessage("ORDER_CONFIG_LOCATIONS");
	$items['tab_content_where_to_begin']['TAX']['URL'] = $siteDir.'/order/config/tax/';
	$items['tab_content_where_to_begin']['TAX']['ICON_CLASS'] = 'img-taxes';
	$items['tab_content_where_to_begin']['TAX']['NAME'] = GetMessage("ORDER_CONFIG_TAX");
	$items['tab_content_where_to_begin']['MEASURE']['URL'] = $siteDir.'/order/config/measure/';
	$items['tab_content_where_to_begin']['MEASURE']['ICON_CLASS'] = 'img-units';
	$items['tab_content_where_to_begin']['MEASURE']['NAME'] = GetMessage("ORDER_CONFIG_MEASURE");
	$items['tab_content_where_to_begin']['PRODUCT_PROPS']['URL'] = $siteDir.'/order/config/productprops/';
	$items['tab_content_where_to_begin']['PRODUCT_PROPS']['ICON_CLASS'] = 'img-properties';
	$items['tab_content_where_to_begin']['PRODUCT_PROPS']['NAME'] = GetMessage("ORDER_CONFIG_PRODUCT_PROPS");
	$items['tab_content_settings_forms_and_reports']['FIELDS']['URL'] = $siteDir.'/order/config/fields/';
	$items['tab_content_settings_forms_and_reports']['FIELDS']['ICON_CLASS'] = 'img-fields';
	$items['tab_content_settings_forms_and_reports']['FIELDS']['NAME'] = GetMessage("ORDER_CONFIG_FIELDS");
	$items['tab_content_settings_forms_and_reports']['SLOT']['URL'] = $siteDir.'/order/config/widget/';
	$items['tab_content_settings_forms_and_reports']['SLOT']['ICON_CLASS'] = 'img-reports';
	$items['tab_content_settings_forms_and_reports']['SLOT']['NAME'] = GetMessage("ORDER_CONFIG_SLOT");
	$items['tab_content_printed_forms_of_documents']['PS']['URL'] = $siteDir.'/order/config/ps/';
	$items['tab_content_printed_forms_of_documents']['PS']['ICON_CLASS'] = 'img-payment';
	$items['tab_content_printed_forms_of_documents']['PS']['NAME'] = GetMessage("ORDER_CONFIG_PS");*/
	$items['tab_content_rights']['PERMS']['URL'] = $siteDir.'/order/config/perms/';
	$items['tab_content_rights']['PERMS']['ICON_CLASS'] = 'img-permissions';
	$items['tab_content_rights']['PERMS']['NAME'] = GetMessage("ORDER_CONFIG_PERMS");

	/*$items['tab_content_automation']['BP']['URL'] = $siteDir.'/order/config/bp/';
	$items['tab_content_automation']['BP']['ICON_CLASS'] = 'img-bp';
	$items['tab_content_automation']['BP']['NAME'] = GetMessage("ORDER_CONFIG_BP");
	$items['tab_content_work_with_mail']['SENDSAVE']['URL'] = $siteDir.'/order/config/sendsave/';
	$items['tab_content_work_with_mail']['SENDSAVE']['ICON_CLASS'] = 'img-email-int';
	$items['tab_content_work_with_mail']['SENDSAVE']['NAME'] = GetMessage("ORDER_CONFIG_SENDSAVE");
	$items['tab_content_integration']['EXTERNAL_SALE']['URL'] = $siteDir.'/order/config/external_sale/';
	$items['tab_content_integration']['EXTERNAL_SALE']['ICON_CLASS'] = 'img-shop';
	$items['tab_content_integration']['EXTERNAL_SALE']['NAME'] = GetMessage("ORDER_CONFIG_EXTERNAL_SALE");*/
	/*if(LANGUAGE_ID === 'ru' || LANGUAGE_ID === 'ua')
	{
		$items['tab_content_integration']['SYNC']['URL'] = $siteDir.'/order/config/sync/';
		$items['tab_content_integration']['SYNC']['ICON_CLASS'] = 'img-1c';
		$items['tab_content_integration']['SYNC']['NAME'] = GetMessage("ORDER_CONFIG_EXCH1C");
	}*/
	$items['tab_content_other']['OTHER']['URL'] = $siteDir.'/order/config/other/';
	$items['tab_content_other']['OTHER']['ICON_CLASS'] = 'img-other';
	$items['tab_content_other']['OTHER']['NAME'] = GetMessage("ORDER_CONFIG_OTHER");
	/*
	$items['tab_content_other']['REFERENCE']['URL'] = '#';
	$items['tab_content_other']['REFERENCE']['ICON_CLASS'] = 'img-help';
	$items['tab_content_other']['REFERENCE']['NAME'] = GetMessage("ORDER_CONFIG_REFERENCE");
	*/

	/*if($arResult['BITRIX24'])
	{
		$items['tab_content_apps']['ORDER_APPLICATION']['URL'] = $siteDir.'/marketplace/?category=order';
		$items['tab_content_apps']['ORDER_APPLICATION']['ICON_CLASS'] = 'img-app';
		$items['tab_content_apps']['ORDER_APPLICATION']['NAME'] = GetMessage("ORDER_CONFIG_ORDER_APPLICATION");
		$items['tab_content_apps']['MIGRATION_OTHER_ORDER']['URL'] = $siteDir.'/marketplace/?category=migration';
		$items['tab_content_apps']['MIGRATION_OTHER_ORDER']['ICON_CLASS'] = 'img-migration';
		$items['tab_content_apps']['MIGRATION_OTHER_ORDER']['NAME'] = GetMessage("ORDER_CONFIG_MIGRATION_OTHER_ORDER");
	}*/
}
/*if($arResult['IS_ACCESS_ENABLED'])
{
	$items['tab_content_work_with_mail']['MAIL_TEMPLATES']['URL'] = $siteDir.'/order/config/mailtemplate/';
	$items['tab_content_work_with_mail']['MAIL_TEMPLATES']['ICON_CLASS'] = 'img-email';
	$items['tab_content_work_with_mail']['MAIL_TEMPLATES']['NAME'] = GetMessage("ORDER_CONFIG_MAIL_TEMPLATES");
}*/

/*
$items['tab_content_creation_on_the_basis']['LEAD']['URL'] = '#';
$items['tab_content_creation_on_the_basis']['LEAD']['ICON_CLASS'] = 'img-leads';
$items['tab_content_creation_on_the_basis']['LEAD']['NAME'] = GetMessage("ORDER_CONFIG_LEAD");
$items['tab_content_creation_on_the_basis']['DEAL']['URL'] = '#';
$items['tab_content_creation_on_the_basis']['DEAL']['ICON_CLASS'] = 'img-deals';
$items['tab_content_creation_on_the_basis']['DEAL']['NAME'] = GetMessage("ORDER_CONFIG_DEAL");
$items['tab_content_creation_on_the_basis']['QOUTE']['URL'] = '#';
$items['tab_content_creation_on_the_basis']['QOUTE']['ICON_CLASS'] = 'img-offers';
$items['tab_content_creation_on_the_basis']['QOUTE']['NAME'] = GetMessage("ORDER_CONFIG_QOUTE");
$items['tab_content_creation_on_the_basis']['CONTACT']['URL'] = '#';
$items['tab_content_creation_on_the_basis']['CONTACT']['ICON_CLASS'] = 'img-contacts';
$items['tab_content_creation_on_the_basis']['CONTACT']['NAME'] = GetMessage("ORDER_CONFIG_CONTACT");
$items['tab_content_creation_on_the_basis']['COMPANY']['URL'] = '#';
$items['tab_content_creation_on_the_basis']['COMPANY']['ICON_CLASS'] = 'img-company';
$items['tab_content_creation_on_the_basis']['COMPANY']['NAME'] = GetMessage("ORDER_CONFIG_COMPANY");
$items['tab_content_creation_on_the_basis']['INVOICE']['URL'] = '#';
$items['tab_content_creation_on_the_basis']['INVOICE']['ICON_CLASS'] = 'img-accounts';
$items['tab_content_creation_on_the_basis']['INVOICE']['NAME'] = GetMessage("ORDER_CONFIG_INVOICE");
*/

/* Content description */
//$contentDescription['tab_content_where_to_begin'] = GetMessage("ORDER_CONFIG_DESCRIPTION_WHERE_TO_BEGIN");
//$contentDescription['tab_content_settings_forms_and_reports'] = GetMessage("ORDER_CONFIG_DESCRIPTION_SETTINGS_FORMS_AND_REPORTS");
//$contentDescription['tab_content_printed_forms_of_documents'] = GetMessage("ORDER_CONFIG_DESCRIPTION_PRINTED_FORMS_OF_DOCUMENTS");
$contentDescription['tab_content_rights'] = GetMessage("ORDER_CONFIG_DESCRIPTION_RIGHTS");
//$contentDescription['tab_content_automation'] = GetMessage("ORDER_CONFIG_DESCRIPTION_AUTOMATION");
//$contentDescription['tab_content_work_with_mail'] = GetMessage("ORDER_CONFIG_DESCRIPTION_WORK_WITH_MAIL");
$contentDescription['tab_content_integration'] = GetMessage("ORDER_CONFIG_DESCRIPTION_INTEGRATION");
$contentDescription['tab_content_other'] = GetMessage("ORDER_CONFIG_DESCRIPTION_OTHER");
//$contentDescription['tab_content_creation_on_the_basis'] = GetMessage("ORDER_CONFIG_DESCRIPTION_CREATION_ON_THE_BASIS");
/*if($arResult['BITRIX24'])
	$contentDescription['tab_content_apps'] = GetMessage("ORDER_CONFIG_DESCRIPTION_APP");*/

foreach($tabs as $tabId => $tabName)
{
	if(!array_key_exists('tab_content_'.$tabId, $items))
		unset($tabs[$tabId]);
}
?>

<div class="order-container">
<div class="view-report-wrapper-container">
<?if(!empty($tabs)):?>
	<div class="view-report-wrapper-wrapp">
	<div class="view-report-wrapper-shell">

		<div class="view-report-sidebar view-report-sidebar-settings">
			<? $counter = 0; ?>
			<? foreach($tabs as $tabId => $tabName): ?>
				<? $class = (!$counter) ? 'sidebar-tab sidebar-tab-active' : 'sidebar-tab'?>
				<a href="javascript:void(0)" class="<?=$class?>" id="tab_<?=$tabId?>"
				   onclick="javascript:BX['OrderConfigClass_<?= $arResult['RAND_STRING']?>'].selectTab('<?=$tabId ?>');">
					<?=$tabName?>
				</a>
				<? $counter++; ?>
			<? endforeach; ?>
		</div>

		<div class="view-report-wrapper">
			<? $counter = 0; ?>
			<? foreach($items as $contentId => $contentList): ?>
				<? $class = (!$counter)? 'view-report-wrapper-inner active' : 'view-report-wrapper-inner'?>
				<div class="<?= $class ?>" id="<?=$contentId?>">
					<? foreach($contentList as $itemData): ?>
						<a href="<?=$itemData['URL']?>" class="view-report-wrapper-inner-item">
							<span class="view-report-wrapper-inner-img <?=$itemData['ICON_CLASS']?>"></span>
							<span class="view-report-wrapper-inner-title"><?=$itemData['NAME']?></span>
						</a>
					<? endforeach; ?>
					<div class="view-report-wrapper-inner-clarification">
						<?=$contentDescription[$contentId]?>
					</div>
				</div>
				<? $counter++; ?>
			<? endforeach; ?>
		</div>

	</div>
	</div>
<?else:?>
	<div class="order-config-error-container"><?=GetMessage("ORDER_CONFIG_NO_ACCESS_ERROR")?></div>
<?endif;?>
</div>
</div>

<script type="text/javascript">
	BX(function () {
		BX['OrderConfigClass_<?= $arResult['RAND_STRING']?>'] = new BX.OrderConfigClass({
			randomString: '<?= $arResult['RAND_STRING'] ?>',
			tabs: <?=CUtil::PhpToJsObject(array_keys($tabs))?>
		});
	});
</script>