<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/order/config/perms/index.php");
$APPLICATION->SetTitle(GetMessage("ORDER_TITLE"));
?> <?$APPLICATION->IncludeComponent(
	"newportal:order.config.perms",
	"",
	Array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/order/config/perms/",
		"SEF_URL_TEMPLATES" => Array(
			"PATH_TO_ENTITY_LIST" => "",
			"PATH_TO_ROLE_EDIT" => "#role_id#/edit/"
		),
		"VARIABLE_ALIASES" => Array(
			"PATH_TO_ENTITY_LIST" => Array(),
			"PATH_TO_ROLE_EDIT" => Array(),
		)
	)
);?> <?


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>