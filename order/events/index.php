<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/order/public/events/index.php");
$APPLICATION->SetTitle(GetMessage("ORDER_TITLE"));
?><?$APPLICATION->IncludeComponent(
	"newportal:order.event.view",
	"",
	Array(
		"ENTITY_ID" => "",
		"EVENT_COUNT" => "20",
		"EVENT_ENTITY_LINK" => "Y",
		"PATH_TO_PHYSICAL_EDIT" => "/order/physical/edit/#physical_id#/",
		"PATH_TO_CONTACT_EDIT" => "/order/contact/edit/#contact_id#/",
		"PATH_TO_AGENT_EDIT" => "/order/agent/edit/#agent_id#/",
		"PATH_TO_DIRECTION_EDIT" => "/order/direction/edit/#direction_id#/",
		"PATH_TO_NOMEN_EDIT" => "/order/nomen/edit/#nomen_id#/",
		"PATH_TO_COURSE_EDIT" => "/order/course/edit/#course_id#/",
		"PATH_TO_GROUP_EDIT" => "/order/group/edit/#group_id#/",
		"PATH_TO_FORMED_GROUP_EDIT" => "/order/formed_group/edit/#formed_group_id#/",
		"PATH_TO_APP_EDIT" => "/order/app/edit/#app_id#/",
		"PATH_TO_REG_EDIT" => "/order/reg/edit/#reg_id#/",
		"PATH_TO_TEACHER_EDIT" => "/order/teacher/edit/#teacher_id#/",
		"PATH_TO_ROOM_EDIT" => "/order/room/edit/#room_id#/",
		"PATH_TO_SCHEDULE_EDIT" => "/order/schedule/edit/#schedule_id#/",
		"PATH_TO_MARK_EDIT" => "/order/mark/edit/#mark_id#/",
		"PATH_TO_STAFF_EDIT" => "/company/personal/user/#staff_id#/"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>