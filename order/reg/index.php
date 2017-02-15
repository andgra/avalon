<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/order/public/reg/index.php");
$APPLICATION->SetTitle(GetMessage("ORDER_TITLE"));
?><?$APPLICATION->IncludeComponent(
	"newportal:order.reg",
	"",
	array(
		"SEF_MODE" => "Y",
		"PATH_TO_CONTACT_EDIT" => "/order/contact/edit/#contact_id#/",
		"PATH_TO_LEAD_EDIT" => "/order/lead/edit/#lead_id#/",
		"PATH_TO_LEAD_CONVERT" => "/order/lead/convert/#lead_id#/",
		"PATH_TO_USER_PROFILE" => "/company/personal/user/#staff_id#/",
		"PATH_TO_APP_EDIT" => "/order/app/edit/#app_id#/",
		"ELEMENT_ID" => $_REQUEST["reg_id"],
		"SEF_FOLDER" => "/order/reg/",
		"COMPONENT_TEMPLATE" => "",
		"NAME_TEMPLATE" => "",
		"SEF_URL_TEMPLATES" => array(
			"index" => "index.php",
			"list" => "list/",
			"edit" => "edit/#reg_id#/",
		),
		"VARIABLE_ALIASES" => Array(
			"index" => Array(),
			"list" => Array(),
			"edit" => Array(),
		),
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>