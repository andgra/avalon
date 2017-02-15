<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/order/public/formed_group/index.php");
$APPLICATION->SetTitle(GetMessage("ORDER_TITLE"));
?><?$APPLICATION->IncludeComponent(
	"newportal:order.formed_group",
	"",
	array(
		"SEF_MODE" => "Y",
		"PATH_TO_FORMED_GROUP_EDIT" => "/order/formed_group/edit/#formed_group_id#/",
		"PATH_TO_GROUP_EDIT" => "/order/group/edit/#group_id#/",
		"PATH_TO_NOMEN_EDIT" => "/order/nomen/edit/#nomen_id#/",
		"PATH_TO_STAFF_PROFILE" => "/company/personal/user/#staff_id#/",
		"ELEMENT_ID" => $_REQUEST["formed_group_id"],
		"SEF_FOLDER" => "/order/formed_group/",
		"COMPONENT_TEMPLATE" => "",
		"NAME_TEMPLATE" => "",
		"SEF_URL_TEMPLATES" => array(
			"index" => "index.php",
			"list" => "list/",
			"edit" => "edit/#formed_group_id#/",
		),
		"VARIABLE_ALIASES" => Array(
			"index" => Array(),
			"list" => Array(),
			"edit" => Array(),
		),
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>