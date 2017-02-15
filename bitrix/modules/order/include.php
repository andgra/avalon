<?php
IncludeModuleLangFile(__FILE__);

// Permissions -->
define('BX_ORDER_PERM_NONE', '');
define('BX_ORDER_PERM_SELF', 'A');
define('BX_ORDER_PERM_DEPARTMENT', 'D');
define('BX_ORDER_PERM_SUBDEPARTMENT', 'F');
define('BX_ORDER_PERM_OPEN', 'O');
define('BX_ORDER_PERM_ALL', 'X');
define('BX_ORDER_PERM_CONFIG', 'C');
// <-- Permissions


// Entity IDs -->
define('BX_ORDER_PHYSICAL_ID', 1);
define('BX_ORDER_CONTACT_ID', 2);
define('BX_ORDER_AGENT_ID', 3);
define('BX_ORDER_DIRECTION_ID', 4);
define('BX_ORDER_NOMEN_ID', 5);
define('BX_ORDER_COURSE_ID', 6);
define('BX_ORDER_GROUP_ID', 7);
define('BX_ORDER_FORMED_GROUP_ID', 8);
define('BX_ORDER_APP_ID', 9);
define('BX_ORDER_REG_ID', 10);
define('BX_ORDER_STAFF_ID', 11);
define('BX_ORDER_TEACHER_ID', 12);
define('BX_ORDER_ROOM_ID', 13);
define('BX_ORDER_SCHEDULE_ID', 14);
define('BX_ORDER_MARK_ID', 15);
// <-- Entity IDs

// Entities -->
define('BX_ORDER_PHYSICAL', 'PHYSICAL');
define('BX_ORDER_CONTACT', 'CONTACT');
define('BX_ORDER_AGENT', 'AGENT');
define('BX_ORDER_DIRECTION', 'DIRECTION');
define('BX_ORDER_NOMEN', 'NOMEN');
define('BX_ORDER_COURSE', 'COURSE');
define('BX_ORDER_GROUP', 'GROUP');
define('BX_ORDER_FORMED_GROUP', 'FORMED_GROUP');
define('BX_ORDER_APP', 'APP');
define('BX_ORDER_REG', 'REG');
define('BX_ORDER_STAFF', 'STAFF');
define('BX_ORDER_TEACHER', 'TEACHER');
define('BX_ORDER_ROOM', 'ROOM');
define('BX_ORDER_SCHEDULE', 'SCHEDULE');
define('BX_ORDER_MARK', 'MARK');
// <-- Entities

require_once($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/order/functions.php');
global $APPLICATION;
$APPLICATION->SetAdditionalCSS('/bitrix/js/order/css/order.css');
require_once($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/order/js.php');

CModule::AddAutoloadClasses(
    'order',
    array(
        'COrderHelper' => 'classes/general/order_helper.php',
        'COrderReg' => 'classes/general/order_reg.php',
        'COrderGroup' => 'classes/general/order_group.php',
        'COrderFormedGroup' => 'classes/general/order_formed_group.php',
        'COrderNomen' => 'classes/general/order_nomen.php',
        'COrderCourse' => 'classes/general/order_course.php',
        'COrderDirection' => 'classes/general/order_direction.php',
        'COrderContact' => 'classes/general/order_contact.php',
        'COrderPhysical' => 'classes/general/order_physical.php',
        'COrderAgent' => 'classes/general/order_agent.php',
        'COrderApp' => 'classes/general/order_app.php',
        'COrderLead' => 'classes/general/order_lead.php',
        'COrderPerms' => 'classes/general/order_perms.php',
        'COrderRole' => 'classes/general/order_role.php',
        'COrderStaff' => 'classes/general/order_staff.php',
        'COrderTeacher' => 'classes/general/order_teacher.php',
        'COrderRoom' => 'classes/general/order_room.php',
        'COrderSchedule' => 'classes/general/order_schedule.php',
        'COrderMark' => 'classes/general/order_mark.php',
        'COrderEntitySelectorHelper' => 'classes/general/order_entity_selector_helper.php',
        'COrderGridOptions' => 'classes/general/order_grids.php',
        'COrderGridContext' => 'classes/general/order_grids.php',
        'COrderComponentHelper' => 'classes/general/order_component_helper.php',
        'COrderUrlUtil' => 'classes/general/order_url_util.php',
        'COrderAuthorizationHelper' => 'classes/general/order_authorization_helper.php',
        'COrderConnection' => 'classes/general/order_connection.php',
        'COrderEntityListBuilder' => 'classes/general/order_entity_list_builder.php',
        'COrderEvent' => 'classes/general/order_event.php',
        'COrderReportManager' => 'classes/general/order_report_helper.php',
        'COrderReportHelper' => 'classes/general/order_report_helper.php',
        'Bitrix\Order\AppTable' => 'lib/app.php',
        '\Bitrix\Order\AppTable' => 'lib/app.php',
        'Bitrix\Order\StaffTable' => 'lib/staff.php',
        '\Bitrix\Order\StaffTable' => 'lib/staff.php',
        'Bitrix\Order\EnumsTable' => 'lib/enums.php',
        '\Bitrix\Order\EnumsTable' => 'lib/enums.php',
        'Bitrix\Order\DirectionTable' => 'lib/direction.php',
        '\Bitrix\Order\DirectionTable' => 'lib/direction.php',
    )
);