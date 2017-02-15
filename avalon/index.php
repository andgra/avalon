<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/order/public/avalon/index.php");
$APPLICATION->SetTitle(GetMessage("ORDER_TITLE"));
?><?$APPLICATION->IncludeComponent(
	"avalon:order.app_create",
	"",
	array(
		"SEF_MODE" => "Y",
		"ELEMENT_ID" => $_REQUEST["direction_id"],
		"SEF_FOLDER" => "/avalon/",
		"COMPONENT_TEMPLATE" => "",
		"NAME_TEMPLATE" => "",
		"SEF_URL_TEMPLATES" => array(
			"index" => "",
			"direction_root" => "#direction_root_id#/",
			"direction" => "#direction_root_id#/#direction_id#/",
			"courses" => "#direction_root_id#/#direction_id#/courses/",
			"enroll" => "enroll/",
		),
		"VARIABLE_ALIASES" => Array(
			"index" => Array(),
			"direction_root" => Array(),
			"direction" => Array(),
			"nomen" => Array(),
			"enroll" => Array(),
		),
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>