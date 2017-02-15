<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/order/public/course/index.php");
$APPLICATION->SetTitle(GetMessage("ORDER_TITLE"));
?><?$APPLICATION->IncludeComponent(
	"newportal:order.course",
	"",
	array(
		"SEF_MODE" => "Y",
		"PATH_TO_COURSE_EDIT" => "/order/course/edit/#course_id#/",
		"PATH_TO_NOMEN_EDIT" => "/order/nomen/edit/#nomen_id#/",
		"PATH_TO_STAFF_PROFILE" => "/company/personal/user/#staff_id#/",
		"ELEMENT_ID" => $_REQUEST["course_id"],
		"SEF_FOLDER" => "/order/course/",
		"COMPONENT_TEMPLATE" => "",
		"NAME_TEMPLATE" => "",
		"SEF_URL_TEMPLATES" => array(
			"index" => "index.php",
			"list" => "list/",
			"edit" => "edit/#course_id#/",
		),
		"VARIABLE_ALIASES" => Array(
			"index" => Array(),
			"list" => Array(),
			"edit" => Array(),
		),
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>