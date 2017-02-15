<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/order/config/locations/index.php");
global $APPLICATION;

$APPLICATION->SetTitle(GetMessage("ORDER_TITLE"));
$APPLICATION->IncludeComponent(
	"newportal:order.config.locations",
	".default",
	array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/order/config/locations/"
	),
	false
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
