<?
/** @global CMain $APPLICATION */

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/order/config/index.php");
$APPLICATION->SetTitle(GetMessage('ORDER_TITLE'));

$APPLICATION->includeComponent('newportal:order.control_panel', '',
	array(
		'ID' => 'CONFIG_MENU',
		'ACTIVE_ITEM_ID' => 'CONFIG'
	),
	$component
);

$APPLICATION->includeComponent('newportal:order.config', '', array('SHOW_TITLE' => 'N'), $component);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
