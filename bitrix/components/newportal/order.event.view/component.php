<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule('order'))
{
	ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
	return;
}
if (!COrderPerms::IsAccessEnabled())
{
	ShowError(GetMessage('ORDER_PERMISSION_DENIED'));
	return;
}

$arParams['PATH_TO_EVENT_LIST'] = OrderCheckPath('PATH_TO_EVENT_LIST', $arParams['PATH_TO_EVENT_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_PHYSICAL_EDIT'] = OrderCheckPath('PATH_TO_PHYSICAL_EDIT', $arParams['PATH_TO_PHYSICAL_EDIT'], $APPLICATION->GetCurPage().'?physical_id=#physical_id#&edit');
$arParams['PATH_TO_CONTACT_EDIT'] = OrderCheckPath('PATH_TO_CONTACT_EDIT', $arParams['PATH_TO_CONTACT_EDIT'], $APPLICATION->GetCurPage().'?contact_id=#contact_id#&edit');
$arParams['PATH_TO_AGENT_EDIT'] = OrderCheckPath('PATH_TO_AGENT_EDIT', $arParams['PATH_TO_AGENT_EDIT'], $APPLICATION->GetCurPage().'?agent_id=#agent_id#&edit');
$arParams['PATH_TO_DIRECTION_EDIT'] = OrderCheckPath('PATH_TO_DIRECTION_EDIT', $arParams['PATH_TO_DIRECTION_EDIT'], $APPLICATION->GetCurPage().'?direction_id=#direction_id#&edit');
$arParams['PATH_TO_NOMEN_EDIT'] = OrderCheckPath('PATH_TO_NOMEN_EDIT', $arParams['PATH_TO_NOMEN_EDIT'], $APPLICATION->GetCurPage().'?nomen_id=#nomen_id#&edit');
$arParams['PATH_TO_COURSE_EDIT'] = OrderCheckPath('PATH_TO_COURSE_EDIT', $arParams['PATH_TO_COURSE_EDIT'], $APPLICATION->GetCurPage().'?course_id=#course_id#&edit');
$arParams['PATH_TO_GROUP_EDIT'] = OrderCheckPath('PATH_TO_GROUP_EDIT', $arParams['PATH_TO_GROUP_EDIT'], $APPLICATION->GetCurPage().'?group_id=#group_id#&edit');
$arParams['PATH_TO_FORMED_GROUP_EDIT'] = OrderCheckPath('PATH_TO_FORMED_GROUP_EDIT', $arParams['PATH_TO_FORMED_GROUP_EDIT'], $APPLICATION->GetCurPage().'?formed_group_id=#formed_group_id#&edit');
$arParams['PATH_TO_APP_EDIT'] = OrderCheckPath('PATH_TO_APP_EDIT', $arParams['PATH_TO_APP_EDIT'], $APPLICATION->GetCurPage().'?app_id=#app_id#&edit');
$arParams['PATH_TO_REG_EDIT'] = OrderCheckPath('PATH_TO_REG_EDIT', $arParams['PATH_TO_REG_EDIT'], $APPLICATION->GetCurPage().'?reg_id=#reg_id#&edit');
$arParams['PATH_TO_TEACHER_EDIT'] = OrderCheckPath('PATH_TO_TEACHER_EDIT', $arParams['PATH_TO_TEACHER_EDIT'], $APPLICATION->GetCurPage().'?teacher_id=#teacher_id#&edit');
$arParams['PATH_TO_ROOM_EDIT'] = OrderCheckPath('PATH_TO_ROOM_EDIT', $arParams['PATH_TO_ROOM_EDIT'], $APPLICATION->GetCurPage().'?room_id=#room_id#&edit');
$arParams['PATH_TO_SCHEDULE_EDIT'] = OrderCheckPath('PATH_TO_SCHEDULE_EDIT', $arParams['PATH_TO_SCHEDULE_EDIT'], $APPLICATION->GetCurPage().'?schedule_id=#schedule_id#&edit');
$arParams['PATH_TO_MARK_EDIT'] = OrderCheckPath('PATH_TO_MARK_EDIT', $arParams['PATH_TO_MARK_EDIT'], $APPLICATION->GetCurPage().'?mark_id=#mark_id#&edit');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');

$arResult['EVENT_ENTITY_LINK'] = isset($arParams['EVENT_ENTITY_LINK']) && $arParams['EVENT_ENTITY_LINK'] == 'Y'? 'Y': 'N';
$arResult['EVENT_HINT_MESSAGE'] = isset($arParams['EVENT_HINT_MESSAGE']) && $arParams['EVENT_HINT_MESSAGE'] == 'N'? 'N': 'Y';
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);
$arResult['INTERNAL'] = isset($arParams['INTERNAL']) && $arParams['INTERNAL'] === 'Y';
$arResult['IS_AJAX_CALL'] = isset($_REQUEST['bxajaxid']) || isset($_REQUEST['AJAX_CALL']);
$arResult['AJAX_MODE'] = isset($arParams['AJAX_MODE']) ? $arParams['AJAX_MODE'] : ($arResult['INTERNAL']? 'N': 'Y');
$arResult['AJAX_ID'] = isset($arParams['AJAX_ID']) ? $arParams['AJAX_ID'] : '';
$arResult['AJAX_OPTION_JUMP'] = isset($arParams['AJAX_OPTION_JUMP']) ? $arParams['AJAX_OPTION_JUMP'] : 'N';
$arResult['AJAX_OPTION_HISTORY'] = isset($arParams['AJAX_OPTION_HISTORY']) ? $arParams['AJAX_OPTION_HISTORY'] : 'N';
$arResult['PATH_TO_EVENT_DELETE'] =  CHTTP::urlAddParams($arParams['PATH_TO_EVENT_LIST'], array('sessid' => bitrix_sessid()));

if(isset($arParams['ENABLE_CONTROL_PANEL']))
{
	$arResult['ENABLE_CONTROL_PANEL'] = (bool)$arParams['ENABLE_CONTROL_PANEL'];
}
else
{
	$arResult['ENABLE_CONTROL_PANEL'] = !(isset($arParams['INTERNAL']) && $arParams['INTERNAL'] === 'Y');
}

CUtil::InitJSCore(array('ajax', 'tooltip'));

$bInternal = false;
if ($arParams['INTERNAL'] == 'Y' || $arParams['GADGET'] == 'Y')
	$bInternal = true;
$arResult['INTERNAL'] = $bInternal;
$arResult['INTERNAL_EDIT'] = false;
if ($arParams['INTERNAL_EDIT'] == 'Y')
	$arResult['INTERNAL_EDIT'] = true;
$arResult['GADGET'] =  isset($arParams['GADGET']) && $arParams['GADGET'] == 'Y'? 'Y': 'N';

$entityType = isset($arParams['ENTITY_TYPE']) ? $arParams['ENTITY_TYPE'] : '';

$arFilter = array();
if ($entityType !== '')
{
	$arFilter['ENTITY_TYPE'] = $arResult['ENTITY_TYPE'] = $entityType;
}

if (isset($arParams['ENTITY_ID']))
{
	if (is_array($arParams['ENTITY_ID']) || $arParams['ENTITY_ID'] != '')
	{
		$arFilter['ENTITY_ID'] = $arResult['ENTITY_ID'] = $arParams['ENTITY_ID'];
	}
}

if(isset($arParams['EVENT_COUNT']))
	$arResult['EVENT_COUNT'] = intval($arParams['EVENT_COUNT']) > 0? intval($arParams['EVENT_COUNT']): 50;
else
	$arResult['EVENT_COUNT'] = 50;

$arResult['PREFIX'] = isset($arParams['PREFIX']) ? strval($arParams['PREFIX']) : '';
$arResult['FORM_ID'] = isset($arParams['FORM_ID']) ? $arParams['FORM_ID'] : '';
$arResult['TAB_ID'] = isset($arParams['TAB_ID']) ? $arParams['TAB_ID'] : '';
$arResult['VIEW_ID'] = isset($arParams['VIEW_ID']) ? $arParams['VIEW_ID'] : '';

$filterFieldPrefix = $bInternal ? "{$arResult['TAB_ID']}_{$arResult['VIEW_ID']}" : '';
if($bInternal)
{
	$filterFieldPrefix = strtoupper($arResult['TAB_ID']).'_'.strtoupper($arResult['VIEW_ID']).'_';
}

$arResult['FILTER_FIELD_PREFIX'] = $filterFieldPrefix;

$tabParamName = $arResult['FORM_ID'] !== '' ? $arResult['FORM_ID'].'_active_tab' : 'active_tab';
$activeTabID = isset($_REQUEST[$tabParamName]) ? $_REQUEST[$tabParamName] : '';

if(strlen($arResult['VIEW_ID']))
	$arResult['GRID_ID'] = $arResult['INTERNAL'] ? 'ORDER_INTERNAL_EVENT_LIST_'.$arResult['TAB_ID'].'_'.$arResult['VIEW_ID']: 'ORDER_EVENT_LIST';
else
	$arResult['GRID_ID'] = $arResult['INTERNAL'] ? 'ORDER_INTERNAL_EVENT_LIST_'.$arResult['TAB_ID'] : 'ORDER_EVENT_LIST';


if(check_bitrix_sessid())
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_button_'.$arResult['GRID_ID']]))
	{
		if ($_POST['action_button_'.$arResult['GRID_ID']] == 'delete' && isset($_POST['ID']) && is_array($_POST['ID']))
		{
			$COrderEvent =  new COrderEvent;
			foreach($_POST['ID'] as $ID)
				$COrderEvent->Delete($ID);
			unset($_POST['ID'], $_REQUEST['ID']); // otherwise the filter will work
		}

		if (!$arResult['IS_AJAX_CALL'])
			LocalRedirect('?'.$arParams['FORM_ID'].'_active_tab=tab_event');
	}
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['action_'.$arResult['GRID_ID']]))
	{
		if ($_REQUEST['action_'.$arResult['GRID_ID']] == 'delete' && isset($_REQUEST['ID']) && $_REQUEST['ID'] > 0)
		{
			$COrderEvent =  new COrderEvent;
			$COrderEvent->Delete($_REQUEST['ID']);
			unset($_REQUEST['ID']); // otherwise the filter will work
		}

		if (!$arResult['IS_AJAX_CALL'])
			LocalRedirect('?'.$arParams['FORM_ID'].'_active_tab=tab_event');
	}
	else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action_'.$arResult['GRID_ID']]))
	{
		if ($_GET['action_'.$arResult['GRID_ID']] == 'delete')
		{
			$COrderEvent =  new COrderEvent;
			$COrderEvent->Delete($_GET['ID']);
			unset($_GET['ID'], $_REQUEST['ID']); // otherwise the filter will work
		}

		if (!$arResult['IS_AJAX_CALL'])
			LocalRedirect($bInternal ? '?'.$arParams['FORM_ID'].'_active_tab='.$arResult['TAB_ID'] : '');
	}
}

$arResult['FILTER'] = array();
$arResult['FILTER2LOGIC'] = array('EVENT_DESC');
$arResult['FILTER_PRESETS'] = array();

if (!$bInternal)
{
	$arResult['FILTER2LOGIC'] = array('EVENT_DESC');

	$arResult['FILTER'] = array(
		array('id' => 'ID', 'name' => 'ID', 'default' => false),
	);

	$enabledEntityTypeNames = array();
	$currentUserPerms = COrderPerms::GetCurrentUserPermissions();
	if (!$currentUserPerms->HavePerm('PHYSICAL', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'PHYSICAL';
	}
	if (!$currentUserPerms->HavePerm('CONTACT', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'CONTACT';
	}
	if (!$currentUserPerms->HavePerm('AGENT', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'AGENT';
	}
	if (!$currentUserPerms->HavePerm('DIRECTION', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'DIRECTION';
	}
	if (!$currentUserPerms->HavePerm('NOMEN', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'NOMEN';
	}
	if (!$currentUserPerms->HavePerm('COURSE', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'COURSE';
	}
	if (!$currentUserPerms->HavePerm('GROUP', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'GROUP';
	}
	if (!$currentUserPerms->HavePerm('FORMED_GROUP', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'FORMED_GROUP';
	}
	if (!$currentUserPerms->HavePerm('APP', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'APP';
	}
	if (!$currentUserPerms->HavePerm('REG', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'REG';
	}
	if (!$currentUserPerms->HavePerm('TEACHER', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'TEACHER';
	}
	if (!$currentUserPerms->HavePerm('ROOM', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'ROOM';
	}
	if (!$currentUserPerms->HavePerm('SCHEDULE', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'SCHEDULE';
	}
	if (!$currentUserPerms->HavePerm('MARK', BX_ORDER_PERM_NONE, 'READ'))
	{
		$enabledEntityTypeNames[] = 'MARK';
	}

	if(!empty($enabledEntityTypeNames))
	{
		ob_start();
		$GLOBALS['APPLICATION']->IncludeComponent('newportal:order.entity.selector',
			'',
			array(
				'ENTITY_TYPE' => $enabledEntityTypeNames,
				'INPUT_NAME' => 'ENTITY',
				'INPUT_VALUE' => isset($_REQUEST['ENTITY']) ? $_REQUEST['ENTITY'] : '',
				'FORM_NAME' => $arResult['GRID_ID'],
				'MULTIPLE' => 'N',
				'FILTER' => true,
			),
			false,
			array('HIDE_ICONS' => 'Y')
		);
		$sVal = ob_get_contents();
		ob_end_clean();

		$arResult['FILTER'][] =
			array('id' => 'ENTITY', 'name' => GetMessage('ORDER_COLUMN_ENTITY'), 'default' => true, 'type' => 'custom', 'value' => $sVal);
	}

	$arEntityType = Array(
		'' => '',
		'PHYSICAL' => GetMessage('ORDER_ENTITY_TYPE_PHYSICAL'),
		'CONTACT' => GetMessage('ORDER_ENTITY_TYPE_CONTACT'),
		'AGENT' => GetMessage('ORDER_ENTITY_TYPE_AGENT'),
		'DIRECTION' => GetMessage('ORDER_ENTITY_TYPE_DIRECTION'),
		'NOMEN' => GetMessage('ORDER_ENTITY_TYPE_NOMEN'),
		'COURSE' => GetMessage('ORDER_ENTITY_TYPE_COURSE'),
		'GROUP' => GetMessage('ORDER_ENTITY_TYPE_GROUP'),
		'FORMED_GROUP' => GetMessage('ORDER_ENTITY_TYPE_FORMED_GROUP'),
		'APP' => GetMessage('ORDER_ENTITY_TYPE_APP'),
		'REG' => GetMessage('ORDER_ENTITY_TYPE_REG'),
		'TEACHER' => GetMessage('ORDER_ENTITY_TYPE_TEACHER'),
		'ROOM' => GetMessage('ORDER_ENTITY_TYPE_ROOM'),
		'SCHEDULE' => GetMessage('ORDER_ENTITY_TYPE_SCHEDULE'),
		'MARK' => GetMessage('ORDER_ENTITY_TYPE_MARK'),
	);

	$arResult['FILTER'] = array_merge(
		$arResult['FILTER'],
		array(
			array('id' => 'ENTITY_TYPE', 'name' => GetMessage('ORDER_COLUMN_ENTITY_TYPE'), 'default' => true, 'type' => 'list', 'items' => $arEntityType),
			//array('id' => 'EVENT_TYPE', 'name' => GetMessage('ORDER_COLUMN_EVENT_TYPE'), 'default' => true, 'type' => 'list', 'items' => array('' => '', '0' => GetMessage('ORDER_EVENT_TYPE_USER'), '1' => GetMessage('ORDER_EVENT_TYPE_CHANGE'), '2' => GetMessage('ORDER_EVENT_TYPE_SNS'))),
			//array('id' => 'EVENT_ID', 'name' => GetMessage('ORDER_COLUMN_EVENT_NAME'), 'default' => true, 'type' => 'list', 'items' => array('' => '')),
			array('id' => 'EVENT_DESC', 'name' => GetMessage('ORDER_COLUMN_EVENT_DESC')),
			array('id' => 'CREATED_BY_ID',  'name' => GetMessage('ORDER_COLUMN_CREATED_BY_ID'), 'default' => true, 'enable_settings' => false, 'type' => 'staff'),
			array('id' => 'DATE_CREATE', 'name' => GetMessage('ORDER_COLUMN_DATE_CREATE'), 'default' => true, 'type' => 'date')
		)
	);

	$currentUserID = COrderHelper::GetCurrentUserID();
	$cUser=COrderStaff::GetByID($currentUserID);
	$currentUserName = $cUser['FULL_NAME'];
	$arResult['FILTER_PRESETS'] = array(
		'filter_change_today' => array('name' => GetMessage('ORDER_PRESET_CREATE_TODAY'), 'fields' => array('DATE_CREATE_datesel' => 'today')),
		'filter_change_yesterday' => array('name' => GetMessage('ORDER_PRESET_CREATE_YESTERDAY'), 'fields' => array('DATE_CREATE_datesel' => 'yesterday')),
		'filter_change_my' => array('name' => GetMessage('ORDER_PRESET_CREATE_MY'), 'fields' => array( 'CREATED_BY_ID' => $currentUserID, 'CREATED_BY_ID_name' => $currentUserName))
	);
}
elseif(isset($arParams['SHOW_INTERNAL_FILTER']) && strtoupper(strval($arParams['SHOW_INTERNAL_FILTER'])) === 'Y')
{
	$arResult['FILTER'] = array(
		array('id' => "{$filterFieldPrefix}ID", 'name' => 'ID', 'default' => false),
		//array('id' => "{$filterFieldPrefix}EVENT_TYPE", 'name' => GetMessage('ORDER_COLUMN_EVENT_TYPE'), 'default' => true, 'type' => 'list', 'items' => array('' => '', '0' => GetMessage('ORDER_EVENT_TYPE_USER'), '1' => GetMessage('ORDER_EVENT_TYPE_CHANGE'), '2' => GetMessage('ORDER_EVENT_TYPE_SNS'))),
		//array('id' => "{$filterFieldPrefix}EVENT_ID", 'name' => GetMessage('ORDER_COLUMN_EVENT_NAME'), 'default' => true, 'type' => 'list', 'items' => array('' => '')/* + COrderStatus::GetStatusList('EVENT_TYPE')*/),
		array('id' => "{$filterFieldPrefix}EVENT_DESC", 'name' => GetMessage('ORDER_COLUMN_EVENT_DESC')),
		array('id' => "{$filterFieldPrefix}CREATED_BY_ID",  'name' => GetMessage('ORDER_COLUMN_CREATED_BY_ID'), 'default' => true, 'enable_settings' => false, 'type' => 'staff'),
		array('id' => "{$filterFieldPrefix}DATE_CREATE", 'name' => GetMessage('ORDER_COLUMN_DATE_CREATE'), 'default' => true, 'type' => 'date'),
	);
}

$arResult['HEADERS'] = array();
$arResult['HEADERS'][] = array('id' => 'ID', 'name' => 'ID', 'sort' => 'id', 'default' => false, 'editable' => false);
$arResult['HEADERS'][] = array('id' => 'DATE_CREATE', 'name' => GetMessage('ORDER_COLUMN_DATE_CREATE'), 'sort' => 'date_create', 'default' => true, 'editable' => false, 'width'=>'140px');
if ($arResult['EVENT_ENTITY_LINK'] == 'Y')
{
	$arResult['HEADERS'][] = array('id' => 'ENTITY_TYPE', 'name' => GetMessage('ORDER_COLUMN_ENTITY_TYPE'), 'sort' => false, 'default' => true, 'editable' => false);
	$arResult['HEADERS'][] = array('id' => 'ENTITY_TITLE', 'name' => GetMessage('ORDER_COLUMN_ENTITY_TITLE'), 'sort' => false, 'default' => true, 'editable' => false);
}
$arResult['HEADERS'][] = array('id' => 'CREATED_BY_FULL_NAME', 'name' => GetMessage('ORDER_COLUMN_CREATED_BY'), 'sort' => false, 'default' => true, 'editable' => false);
$arResult['HEADERS'][] = array('id' => 'EVENT_NAME', 'name' => GetMessage('ORDER_COLUMN_EVENT_NAME'), 'sort' => false, 'default' => true, 'editable' => false);
$arResult['HEADERS'][] = array('id' => 'EVENT_DESC', 'name' => GetMessage('ORDER_COLUMN_EVENT_DESC'), 'sort' => false, 'default' => true, 'editable' => false);

$arNavParams = array(
	'nPageSize' => $arResult['EVENT_COUNT']
);

$CGridOptions = new COrderGridOptions($arResult['GRID_ID']);

if (($arResult['TAB_ID'] === '' || $arResult['TAB_ID'] === $activeTabID)
	&& isset($_REQUEST['clear_filter']) && $_REQUEST['clear_filter'] == 'Y')
{
	$urlParams = array();
	foreach($arResult['FILTER'] as $arFilterField)
	{
		$filterFieldID = $arFilterField['id'];
		if ($arFilterField['type'] == 'staff')
		{
			$urlParams[] = $filterFieldID.'_name';
		}
		if ($arFilterField['type'] == 'date')
		{
			$urlParams[] = $filterFieldID.'_datesel';
			$urlParams[] = $filterFieldID.'_days';
			$urlParams[] = $filterFieldID.'_from';
			$urlParams[] = $filterFieldID.'_to';
		}

		$urlParams[] = $filterFieldID;
	}
	$urlParams[] = 'clear_filter';
	$CGridOptions->GetFilter(array());
	if($arResult['TAB_ID'] !== '')
	{
		$urlParams[] = $tabParamName;
		LocalRedirect($APPLICATION->GetCurPageParam(
			urlencode($tabParamName).'='.urlencode($arResult['TAB_ID']),
			$urlParams));
	}
	else
	{
		LocalRedirect($APPLICATION->GetCurPageParam('',$urlParams));
	}
}

$arGridFilter = $CGridOptions->GetFilter($arResult['FILTER']);

$prefixLength = strlen($filterFieldPrefix);

if($prefixLength == 0)
{
	$arFilter = array_merge($arFilter, $arGridFilter);
}
else
{
	foreach($arGridFilter as $key=>&$value)
	{
		$arFilter[substr($key, $prefixLength)] = $value;
	}
}
unset($value);

foreach ($arFilter as $k => $v)
{
	$arMatch = array();
	if (preg_match('/(.*)_from$/i'.BX_UTF_PCRE_MODIFIER, $k, $arMatch))
	{
		$arFilter['>='.$arMatch[1]] = $v;
		unset($arFilter[$k]);
	}
	else if (preg_match('/(.*)_to$/i'.BX_UTF_PCRE_MODIFIER, $k, $arMatch))
	{
		if ($arMatch[1] == 'DATE_CREATE' && !preg_match('/\d{1,2}:\d{1,2}(:\d{1,2})?$/'.BX_UTF_PCRE_MODIFIER, $v))
			$v .=  ' 23:59:59';

		$arFilter['<='.$arMatch[1]] = $v;
		unset($arFilter[$k]);
	}
	else if (in_array($k, $arResult['FILTER2LOGIC']))
	{
		// Bugfix #26956 - skip empty values in logical filter
		$v = trim($v);
		if($v !== '')
		{
			//Bugfix #42761 replace logic field name
			$arFilter['?'.($k === 'EVENT_DESC' ? 'VALUE_OLD' : $k)] = $v;
		}
		unset($arFilter[$k]);
	}
	else if ($k == 'CREATED_BY_ID')
	{
		// For suppress comparison by LIKE
		$arFilter['=CREATED_BY_ID'] = $v;
		unset($arFilter['CREATED_BY_ID']);
	}
}

$_arSort = $CGridOptions->GetSorting(array(
	'sort' => array('date_create' => 'desc'),
	'vars' => array('by' => 'by', 'order' => 'order')
));

$arResult['SORT'] = !empty($arSort) ? $arSort : $_arSort['sort'];
$arResult['SORT_VARS'] = $_arSort['vars'];

$arNavParams = $CGridOptions->GetNavParams($arNavParams);
$arNavParams['bShowAll'] = false;
$arSelect = $CGridOptions->GetVisibleColumns();
// HACK: ignore entity related fields if entity info is not displayed
if ($arResult['EVENT_ENTITY_LINK'] !== 'Y')
{
	$key = array_search('ENTITY_TYPE', $arSelect, true);
	if($key !== false)
	{
		unset($arSelect[$key]);
	}

	$key = array_search('ENTITY_TITLE', $arSelect, true);
	if($key !== false)
	{
		unset($arSelect[$key]);
	}
}

$CGridOptions->SetVisibleColumns($arSelect);

$nTopCount = false;
if ($arResult['GADGET'] == 'Y')
{
	$nTopCount = $arResult['EVENT_COUNT'];
}

if($nTopCount > 0)
{
	$arNavParams['nTopCount'] = $nTopCount;
}

$arEntityList = Array();
$arResult['EVENT'] = Array();

$obRes = COrderEvent::GetListEx($arResult['SORT'], $arFilter, false, $arNavParams, array(), array());

$arResult['DB_LIST'] = $obRes;
$arResult['ROWS_COUNT'] = $obRes->NavRecordCount;
// Prepare raw filter ('=CREATED_BY' => 'CREATED_BY')
$arResult['DB_FILTER'] = array();
foreach($arFilter as $filterKey => &$filterItem)
{
	$info = CSqlUtil::GetFilterOperation($filterKey);
	$arResult['DB_FILTER'][$info['FIELD']] = $filterItem;
}
unset($filterItem);

while ($arEvent = $obRes->Fetch())
{
	//$arEvent['~FILES'] = $arEvent['FILES'];
	$arEvent['~EVENT_NAME'] = $arEvent['EVENT_NAME'];
	if (!empty($arEvent['CREATED_BY_ID']))
		$arEvent['~CREATED_BY_FULL_NAME'] = CUser::FormatName(
			$arParams["NAME_TEMPLATE"],
			array(
				'LOGIN' => $arEvent['CREATED_BY_LOGIN'],
				'NAME' => $arEvent['CREATED_BY_NAME'],
				'LAST_NAME' => $arEvent['CREATED_BY_LAST_NAME'],
				'SECOND_NAME' => $arEvent['CREATED_BY_SECOND_NAME']
			),
			true, false
		);
	$arEvent['DATE_CREATE'] = $arEvent['DATE_CREATE'];
	$arEvent['CREATED_BY_FULL_NAME'] = htmlspecialcharsbx($arEvent['~CREATED_BY_FULL_NAME']);
	$arEvent['CREATED_BY_LINK'] = CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_STAFF_EDIT'], array('staff_id' => $arEvent['CREATED_BY_ID']));
	$arEvent['EVENT_NAME'] = htmlspecialcharsbx($arEvent['~EVENT_NAME']);
	$arEvent['ENTITY_TYPE_TITLE']=isset($arEntityType[$arEvent['ENTITY_TYPE']])?$arEntityType[$arEvent['ENTITY_TYPE']]:$arEvent['ENTITY_TYPE'];

	$arEvent['VALUE_OLD'] = strip_tags($arEvent['VALUE_OLD'], '<br>');
	$arEvent['VALUE_NEW'] = strip_tags($arEvent['VALUE_NEW'], '<br>');
	switch($arEvent['ENTITY_TYPE']) {
		case 'PHYSICAL':
			if(!isset($arPhysical)) {
				$res=COrderPhysical::GetListEx(array(),array(),false,false,array('FULL_NAME','ID'));
				while($el=$res->Fetch()) {
					$arPhysical[$el['ID']]=$el;
				}
			}
			if($arEvent['ENTITY_FIELD']=='SHARED') {
				$arEvent['VALUE_OLD'] = $arEvent['VALUE_OLD']=='N'?GetMessage('ORDER_FIELD_CHECKBOX_NO'):GetMessage('ORDER_FIELD_CHECKBOX_YES');
				$arEvent['VALUE_NEW'] = $arEvent['VALUE_NEW']=='N'?GetMessage('ORDER_FIELD_CHECKBOX_NO'):GetMessage('ORDER_FIELD_CHECKBOX_YES');
			}
			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => isset($arPhysical[$arEvent['ENTITY_ID']])?$arPhysical[$arEvent['ENTITY_ID']]['FULL_NAME']:('['.$arEvent['ENTITY_ID'].']'),
				'ENTITY_LINK' => isset($arPhysical[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'], array('physical_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));
			break;
		case 'CONTACT':
			if(!isset($arContact)) {
				$res=COrderContact::GetListEx(array(),array(),false,false,array('FULL_NAME','ID'));
				while($el=$res->Fetch()) {
					$arContact[$el['ID']]=$el;
				}
			}
			if($arEvent['ENTITY_FIELD']=='ASSIGNED_ID') {
				if(!isset($arStaff)) {
					$res=COrderStaff::GetListEx(array(),array(),false,false,array('FULL_NAME','ID'));
					while($el=$res->Fetch()) {
						$arStaff[$el['ID']]=$el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arAgent[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_STAFF_EDIT'],array('staff_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arAgent[$arEvent['VALUE_OLD']]['FULL_NAME'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arAgent[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_STAFF_EDIT'],array('staff_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arAgent[$arEvent['VALUE_NEW']]['FULL_NAME'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			} elseif($arEvent['ENTITY_FIELD']=='AGENT_ID') {
				if (!isset($arAgent)) {
					$res = COrderAgent::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
					while ($el = $res->Fetch()) {
						$arAgent[$el['ID']] = $el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arAgent[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_AGENT_EDIT'],array('agent_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arAgent[$arEvent['VALUE_OLD']]['TITLE'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arAgent[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_AGENT_EDIT'],array('agent_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arAgent[$arEvent['VALUE_NEW']]['TITLE'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			} elseif($arEvent['ENTITY_FIELD']=='GUID') {
				if(!isset($arPhysical)) {
					$res=COrderPhysical::GetListEx(array(),array(),false,false,array('FULL_NAME','ID'));
					while($el=$res->Fetch()) {
						$arPhysical[$el['ID']]=$el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arPhysical[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'],array('physical_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arPhysical[$arEvent['VALUE_OLD']]['FULL_NAME'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arPhysical[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'],array('physical_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arPhysical[$arEvent['VALUE_NEW']]['FULL_NAME'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			}
			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => isset($arContact[$arEvent['ENTITY_ID']])?$arContact[$arEvent['ENTITY_ID']]['FULL_NAME']:('['.$arEvent['ENTITY_ID'].']'),
				'ENTITY_LINK' => isset($arContact[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_CONTACT_EDIT'], array('contact_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));
			break;
		case 'AGENT':
			if (!isset($arAgent)) {
				$res = COrderAgent::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
				while ($el = $res->Fetch()) {
					$arAgent[$el['ID']] = $el;
				}
			}
			if($arEvent['ENTITY_FIELD']=='LEGAL') {
				$arEvent['VALUE_OLD'] = $arEvent['VALUE_OLD']=='N'?GetMessage('ORDER_FIELD_CHECKBOX_NO'):GetMessage('ORDER_FIELD_CHECKBOX_YES');
				$arEvent['VALUE_NEW'] = $arEvent['VALUE_NEW']=='N'?GetMessage('ORDER_FIELD_CHECKBOX_NO'):GetMessage('ORDER_FIELD_CHECKBOX_YES');
			}
			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => isset($arAgent[$arEvent['ENTITY_ID']])?$arAgent[$arEvent['ENTITY_ID']]['TITLE']:('['.$arEvent['ENTITY_ID'].']'),
				'ENTITY_LINK' => isset($arAgent[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_AGENT_EDIT'], array('agent_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));

			break;
		case 'DIRECTION':
			if (!isset($arDirection)) {
				$res = COrderDirection::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
				while ($el = $res->Fetch()) {
					$arDirection[$el['ID']] = $el;
				}
			}
			if($arEvent['ENTITY_FIELD']=='MANAGER_ID') {
				if(!isset($arPhysical)) {
					$res=COrderPhysical::GetListEx(array(),array(),false,false,array('FULL_NAME','ID'));
					while($el=$res->Fetch()) {
						$arPhysical[$el['ID']]=$el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arPhysical[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'],array('physical_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arPhysical[$arEvent['VALUE_OLD']]['FULL_NAME'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arPhysical[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'],array('physical_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arPhysical[$arEvent['VALUE_NEW']]['FULL_NAME'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			} elseif($arEvent['ENTITY_FIELD']=='PARENT_ID') {

				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arDirection[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_DIRECTION_EDIT'],array('direction_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arDirection[$arEvent['VALUE_OLD']]['TITLE'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arDirection[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_DIRECTION_EDIT'],array('direction_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arDirection[$arEvent['VALUE_NEW']]['TITLE'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			} elseif($arEvent['ENTITY_FIELD']=='PRIVATE') {
				$arEvent['VALUE_OLD'] = $arEvent['VALUE_OLD']=='N'?GetMessage('ORDER_FIELD_CHECKBOX_NO'):GetMessage('ORDER_FIELD_CHECKBOX_YES');
				$arEvent['VALUE_NEW'] = $arEvent['VALUE_NEW']=='N'?GetMessage('ORDER_FIELD_CHECKBOX_NO'):GetMessage('ORDER_FIELD_CHECKBOX_YES');
			} elseif($arEvent['ENTITY_FIELD']=='BEHAVIOR') {
				if (!isset($arDirectionBehavior)) {
					$arDirectionBehavior=COrderHelper::GetEnumList('DIRECTION','BEHAVIOR');
				}
				$newPrefix=true;
				if(substr($arEvent['VALUE_OLD'], 0, 6) == 'SELECT') {
					$newPrefix=false;
					$oldEntityType=substr($arEvent['VALUE_OLD'], 6, strpos($arEvent['VALUE_OLD'],'#')-6);
					$oldEntityId=substr($arEvent['VALUE_OLD'], strpos($arEvent['VALUE_OLD'],'#')+1);
					$oldClassName = 'COrder' . Bitrix\Main\Entity\Base::snake2camel($oldEntityType);
					$oldArName = 'ar' . Bitrix\Main\Entity\Base::snake2camel($oldEntityType);
					if (!isset($$oldArName)) {
						$res = $oldClassName::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
						while ($el = $res->Fetch()) {
							$arTemp[$el['ID']]=$el;
						}
						$$oldArName=$arTemp;
					}
					$oldArEntity=$$oldArName;
					$arEvent['VALUE_OLD'] =$arDirectionBehavior['SELECT'].': '.GetMessage('ORDER_FIELD_DIRECTION_ENTITY_TITLE_'.strtoupper($oldEntityType)).' | '.(isset($oldArEntity[$oldEntityId])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_'.strtoupper($oldEntityType).'_EDIT'],array($oldEntityType.'_id' => $oldEntityId)) . '" target="_blank">'.$oldArEntity[$oldEntityId]['TITLE'].'</a>':$oldEntityId);
				} else {
					$arEvent['VALUE_OLD'] = $arDirectionBehavior[$arEvent['VALUE_OLD']];
				}
				if(substr($arEvent['VALUE_NEW'], 0, 6) == 'SELECT') {
					$newEntityType=substr($arEvent['VALUE_NEW'], 6, strpos($arEvent['VALUE_NEW'],'#')-6);
					$newEntityId=substr($arEvent['VALUE_NEW'], strpos($arEvent['VALUE_NEW'],'#')+1);
					$newClassName = 'COrder' . Bitrix\Main\Entity\Base::snake2camel($newEntityType);
					$newArName = 'ar' . Bitrix\Main\Entity\Base::snake2camel($newEntityType);
					if (!isset($$newArName)) {
						$res = $newClassName::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
						while ($el = $res->Fetch()) {
							$arTemp[$el['ID']]=$el;
						}
						$$newArName=$arTemp;
					}
					$newArEntity=$$newArName;
					$arEvent['VALUE_NEW'] ='';
					if($newPrefix) {
						$arEvent['VALUE_NEW'] .=$arDirectionBehavior['SELECT'].': ';
					}
					$arEvent['VALUE_NEW'] .=GetMessage('ORDER_FIELD_DIRECTION_ENTITY_TITLE_'.strtoupper($newEntityType)).' | '.(isset($newArEntity[$newEntityId])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_'.strtoupper($newEntityType).'_EDIT'],array($newEntityType.'_id' => $newEntityId)) . '" target="_blank">'.$newArEntity[$newEntityId]['TITLE'].'</a>':$newEntityId);
				} else {
					$arEvent['VALUE_NEW'] = $arDirectionBehavior[$arEvent['VALUE_NEW']];
				}
			}
			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => isset($arDirection[$arEvent['ENTITY_ID']])?$arDirection[$arEvent['ENTITY_ID']]['TITLE']:('['.$arEvent['ENTITY_ID'].']'),
				'ENTITY_LINK' => isset($arDirection[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_DIRECTION_EDIT'], array('direction_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));

			break;
		case 'NOMEN':
			if (!isset($arNomen)) {
				$res = COrderNomen::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
				while ($el = $res->Fetch()) {
					$arNomen[$el['ID']] = $el;
				}
			}
			if($arEvent['ENTITY_FIELD']=='PRICE') {
				$oldPrice=unserialize($arEvent['VALUE_OLD']);
				$arEvent['VALUE_OLD']='<table>';
				if(isset($oldPrice['PRICE_PHYSICAL']))
					$arEvent['VALUE_OLD'].='<tr><td>'.GetMessage('ORDER_FIELD_PRICE_PHYSICAL').'</td><td>'.$oldPrice['PRICE_PHYSICAL'].'</td></tr>';
				if(isset($oldPrice['PRICE_LEGAL']))
					$arEvent['VALUE_OLD'].='<tr><td>'.GetMessage('ORDER_FIELD_PRICE_LEGAL').'</td><td>'.$oldPrice['PRICE_LEGAL'].'</td></tr>';
				if(isset($oldPrice['PRICE_OPT']))
					$arEvent['VALUE_OLD'].='<tr><td>'.GetMessage('ORDER_FIELD_PRICE_OPT').'</td><td>'.$oldPrice['PRICE_OPT'].'</td></tr>';
				$arEvent['VALUE_OLD'].='</table>';

				$newPrice=unserialize($arEvent['VALUE_NEW']);
				$arEvent['VALUE_NEW']='<table>';
				if(isset($newPrice['PRICE_PHYSICAL']))
					$arEvent['VALUE_NEW'].='<tr><td>'.GetMessage('ORDER_FIELD_PRICE_PHYSICAL').'</td><td>'.$newPrice['PRICE_PHYSICAL'].'</td></tr>';
				if(isset($newPrice['PRICE_LEGAL']))
					$arEvent['VALUE_NEW'].='<tr><td>'.GetMessage('ORDER_FIELD_PRICE_LEGAL').'</td><td>'.$newPrice['PRICE_LEGAL'].'</td></tr>';
				if(isset($newPrice['PRICE_OPT']))
					$arEvent['VALUE_NEW'].='<tr><td>'.GetMessage('ORDER_FIELD_PRICE_OPT').'</td><td>'.$newPrice['PRICE_OPT'].'</td></tr>';
				$arEvent['VALUE_NEW'].='</table>';

			} elseif($arEvent['ENTITY_FIELD']=='DIRECTION_ID') {
				if (!isset($arDirection)) {
					$res = COrderDirection::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
					while ($el = $res->Fetch()) {
						$arDirection[$el['ID']] = $el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arDirection[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_DIRECTION_EDIT'],array('direction_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arDirection[$arEvent['VALUE_OLD']]['TITLE'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arDirection[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_DIRECTION_EDIT'],array('direction_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arDirection[$arEvent['VALUE_NEW']]['TITLE'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			} elseif($arEvent['ENTITY_FIELD']=='PRIVATE') {
				$arEvent['VALUE_OLD'] = $arEvent['VALUE_OLD']=='N'?GetMessage('ORDER_FIELD_CHECKBOX_NO'):GetMessage('ORDER_FIELD_CHECKBOX_YES');
				$arEvent['VALUE_NEW'] = $arEvent['VALUE_NEW']=='N'?GetMessage('ORDER_FIELD_CHECKBOX_NO'):GetMessage('ORDER_FIELD_CHECKBOX_YES');
			}
			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => isset($arNomen[$arEvent['ENTITY_ID']])?$arNomen[$arEvent['ENTITY_ID']]['TITLE']:('['.$arEvent['ENTITY_ID'].']'),
				'ENTITY_LINK' => isset($arNomen[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_NOMEN_EDIT'], array('nomen_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));

			break;
		case 'COURSE':
			if (!isset($arCourse)) {
				$res = COrderCourse::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
				while ($el = $res->Fetch()) {
					$arCourse[$el['ID']] = $el;
				}
			}
			if($arEvent['ENTITY_FIELD']=='EXAM') {
				$oldExam=unserialize($arEvent['VALUE_OLD']);
				$arEvent['VALUE_OLD']='';
				if(count($oldExam)>0) {
					$valExam = '<tr><th>' . GetMessage('ORDER_FIELD_COURSE_EXAM_TITLE') . '</th><th>' . GetMessage('ORDER_FIELD_COURSE_EXAM_MARK') . '</th></tr>';
					foreach ($oldExam as $num => $item) {
						$valExam .= '<tr><td>' . $item['EXAM_TITLE'] . '</td><td>' . $item['EXAM_MARK'] . '</td></tr>';
					}
					$arEvent['VALUE_OLD']='<table>'.$valExam.'</table>';
				}

				$newExam=unserialize($arEvent['VALUE_NEW']);
				$arEvent['VALUE_NEW']='';
				if(count($newExam)>0) {
					$valExam = '<tr><th>' . GetMessage('ORDER_FIELD_COURSE_EXAM_TITLE') . '</th><th>' . GetMessage('ORDER_FIELD_COURSE_EXAM_MARK') . '</th></tr>';
					foreach ($newExam as $num => $item) {
						$valExam .= '<tr><td>' . $item['EXAM_TITLE'] . '</td><td>' . $item['EXAM_MARK'] . '</td></tr>';
					}
					$arEvent['VALUE_NEW']='<table>'.$valExam.'</table>';
				}
			}
			if($arEvent['ENTITY_FIELD']=='LITER') {
				$oldLiter=unserialize($arEvent['VALUE_OLD']);
				$arEvent['VALUE_OLD']='';
				if(count($oldLiter)>0) {
					$valLiter = '<tr><th>' . GetMessage('ORDER_FIELD_COURSE_LITER_ID') . '</th></tr>';
					foreach ($oldLiter as $num => $item) {
						$valLiter .= '<tr><td>' . $item['LITER_ID'] . '</td></tr>';
					}
					$arEvent['VALUE_NEW']='<table>'.$valLiter.'</table>';
				}

				$newLiter=unserialize($arEvent['VALUE_NEW']);
				$arEvent['VALUE_NEW']='';
				if(count($newLiter)>0) {
					$valLiter = '<tr><th>' . GetMessage('ORDER_FIELD_COURSE_LITER_ID') . '</th></tr>';
					foreach ($newLiter as $num => $item) {
						$valLiter .= '<tr><td>' . $item['LITER_ID'] . '</td></tr>';
					}
					$arEvent['VALUE_NEW']='<table>'.$valLiter.'</table>';
				}
			}
			if($arEvent['ENTITY_FIELD']=='DOC') {
				$oldDoc = unserialize($arEvent['VALUE_OLD']);
				$arEvent['VALUE_OLD'] = '';
				if (count($oldDoc) > 0) {
					$valDoc = '<tr><th>' . GetMessage('ORDER_FIELD_COURSE_DOC_TITLE') . '</th></tr>';
					foreach ($oldDoc as $num => $item) {
						$valDoc .= '<tr><td>' . $item['DOC_TITLE'] . '</td></tr>';
					}
					$arEvent['VALUE_OLD'] = '<table>' . $valDoc . '</table>';
				}

				$newDoc = unserialize($arEvent['VALUE_NEW']);
				$arEvent['VALUE_NEW'] = '';
				if (count($newDoc) > 0) {
					$valDoc = '<tr><th>' . GetMessage('ORDER_FIELD_COURSE_DOC_TITLE') . '</th></tr>';
					foreach ($newDoc as $num => $item) {
						$valDoc .= '<tr><td>' . $item['DOC_TITLE'] . '</td></tr>';
					}
					$arEvent['VALUE_NEW'] = '<table>' . $valDoc . '</table>';
				}
			}
			if($arEvent['ENTITY_FIELD']=='DOC') {
				if (!isset($arNomen)) {
					$res = COrderNomen::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
					while ($el = $res->Fetch()) {
						$arNomen[$el['ID']] = $el;
					}
				}
				$oldNomen = unserialize($arEvent['VALUE_OLD']);
				$arEvent['VALUE_OLD'] = '';
				if (count($oldNomen) > 0) {
					$valNomen = '<tr><th>' . GetMessage('ORDER_FIELD_COURSE_NOMEN_ID') . '</th><th>' . GetMessage('ORDER_FIELD_COURSE_NOMEN_TITLE') . '</th></tr>';
					foreach ($oldNomen as $num => $item) {
						$valNomen .= '<tr><td>' . $item['NOMEN_ID'] . '</td>';
						$valNomen .= '<td><a href="'.CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_NOMEN_EDIT'], array('nomen_id' => $item['NOMEN_ID'])).'" target="_blank">' . $arNomen[$item['NOMEN_ID']]['TITLE'] . '</a></td></tr>';
					}
					$arEvent['VALUE_OLD'] = '<table>' . $valNomen . '</table>';
				}

				$newNomen = unserialize($arEvent['VALUE_NEW']);
				$arEvent['VALUE_NEW'] = '';
				if (count($newNomen) > 0) {
					$valNomen = '<tr><th>' . GetMessage('ORDER_FIELD_COURSE_NOMEN_ID') . '</th><th>' . GetMessage('ORDER_FIELD_COURSE_NOMEN_TITLE') . '</th></tr>';
					foreach ($newNomen as $num => $item) {
						$valNomen .= '<tr><td>' . $item['NOMEN_ID'] . '</td>';
						$valNomen .= '<td><a href="'.CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_NOMEN_EDIT'], array('nomen_id' => $item['NOMEN_ID'])).'" target="_blank">' . $arNomen[$item['NOMEN_ID']]['TITLE'] . '</a></td></tr>';
					}
					$arEvent['VALUE_NEW'] = '<table>' . $valNomen . '</table>';
				}
			}
			if($arEvent['ENTITY_FIELD']=='TEACHER') {
				if(!isset($arPhysical)) {
					$res=COrderPhysical::GetListEx(array(),array(),false,false,array('FULL_NAME','ID'));
					while($el=$res->Fetch()) {
						$arPhysical[$el['ID']]=$el;
					}
				}
				$oldTeacher = unserialize($arEvent['VALUE_OLD']);
				$arEvent['VALUE_OLD'] = '';
				if (count($oldTeacher) > 0) {
					$valTeacher = '<tr><th>' . GetMessage('ORDER_FIELD_COURSE_TEACHER_ID') . '</th><th>' . GetMessage('ORDER_FIELD_COURSE_TEACHER_FULL_NAME') . '</th></tr>';
					foreach ($oldTeacher as $num => $item) {
						$valTeacher .= '<tr><td>' . $item['TEACHER_ID'] . '</td>';
						$valTeacher .= '<td><a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'], array('physical_id' => $item['TEACHER_ID'])) . '" target="_blank">' . $arPhysical[$item['TEACHER_ID']]['FULL_NAME'] . '</a></td></tr>';
					}
					$arEvent['VALUE_OLD'] = '<table>' . $valTeacher . '</table>';
				}

				$newTeacher = unserialize($arEvent['VALUE_NEW']);
				$arEvent['VALUE_NEW'] = '';
				if (count($newTeacher) > 0) {
					$valTeacher = '<tr><th>' . GetMessage('ORDER_FIELD_COURSE_TEACHER_ID') . '</th><th>' . GetMessage('ORDER_FIELD_COURSE_TEACHER_FULL_NAME') . '</th></tr>';
					foreach ($newTeacher as $num => $item) {
						$valTeacher .= '<tr><td>' . $item['TEACHER_ID'] . '</td>';
						$valTeacher .= '<td><a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'], array('physical_id' => $item['TEACHER_ID'])) . '" target="_blank">' . $arPhysical[$item['TEACHER_ID']]['FULL_NAME'] . '</a></td></tr>';
					}
					$arEvent['VALUE_NEW'] = '<table>' . $valTeacher . '</table>';
				}

			} elseif($arEvent['ENTITY_FIELD']=='PARENT_ID') {

				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arDirection[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_DIRECTION_EDIT'],array('direction_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arDirection[$arEvent['VALUE_OLD']]['TITLE'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arDirection[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_DIRECTION_EDIT'],array('direction_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arDirection[$arEvent['VALUE_NEW']]['TITLE'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			}
			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => isset($arCourse[$arEvent['ENTITY_ID']])?$arCourse[$arEvent['ENTITY_ID']]['TITLE']:('['.$arEvent['ENTITY_ID'].']'),
				'ENTITY_LINK' => isset($arCourse[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_COURSE_EDIT'], array('course_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));

			break;
		case 'GROUP':
			if (!isset($arGroup)) {
				$res = COrderGroup::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
				while ($el = $res->Fetch()) {
					$arGroup[$el['ID']] = $el;
				}
			}
			if($arEvent['ENTITY_FIELD']=='NOMEN_ID') {
				if (!isset($arNomen)) {
					$res = COrderNomen::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
					while ($el = $res->Fetch()) {
						$arNomen[$el['ID']] = $el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arNomen[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_NOMEN_EDIT'],array('nomen_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arNomen[$arEvent['VALUE_OLD']]['TITLE'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arNomen[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_NOMEN_EDIT'],array('nomen_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arNomen[$arEvent['VALUE_NEW']]['TITLE'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			} elseif($arEvent['ENTITY_FIELD']=='PRIVATE') {
				$arEvent['VALUE_OLD'] = $arEvent['VALUE_OLD']=='N'?GetMessage('ORDER_FIELD_CHECKBOX_NO'):GetMessage('ORDER_FIELD_CHECKBOX_YES');
				$arEvent['VALUE_NEW'] = $arEvent['VALUE_NEW']=='N'?GetMessage('ORDER_FIELD_CHECKBOX_NO'):GetMessage('ORDER_FIELD_CHECKBOX_YES');
			}
			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => isset($arGroup[$arEvent['ENTITY_ID']])?$arGroup[$arEvent['ENTITY_ID']]['TITLE']:('['.$arEvent['ENTITY_ID'].']'),
				'ENTITY_LINK' => isset($arGroup[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_GROUP_EDIT'], array('group_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));

			break;
		case 'FORMED_GROUP':
			if (!isset($arFormedGroup)) {
				$res = COrderFormedGroup::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
				while ($el = $res->Fetch()) {
					$arFormedGroup[$el['ID']] = $el;
				}
			}

			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => isset($arFormedGroup[$arEvent['ENTITY_ID']])?$arFormedGroup[$arEvent['ENTITY_ID']]['TITLE']:('['.$arEvent['ENTITY_ID'].']'),
				'ENTITY_LINK' => isset($arFormedGroup[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_FORMED_GROUP_EDIT'], array('formed_group_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));

			break;
		case 'APP':
			if (!isset($arApp)) {
				$res = COrderApp::GetListEx(array(), array(), false, false, array('ID'));
				while ($el = $res->Fetch()) {
					$arApp[$el['ID']] = $el;
				}
			}
			if($arEvent['ENTITY_FIELD']=='AGENT_ID') {
				if (!isset($arAgent)) {
					$res = COrderAgent::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
					while ($el = $res->Fetch()) {
						$arAgent[$el['ID']] = $el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arAgent[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_AGENT_EDIT'],array('agent_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arAgent[$arEvent['VALUE_OLD']]['TITLE'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arAgent[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_AGENT_EDIT'],array('agent_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arAgent[$arEvent['VALUE_NEW']]['TITLE'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			} elseif($arEvent['ENTITY_FIELD']=='STATUS') {
				if (!isset($arAppStatus)) {
					$arAppStatus=COrderHelper::GetEnumList('APP','STATUS');
				}
				$arEvent['VALUE_OLD'] = $arAppStatus[$arEvent['VALUE_OLD']];
				$arEvent['VALUE_NEW'] = $arAppStatus[$arEvent['VALUE_NEW']];
			} elseif($arEvent['ENTITY_FIELD']=='PAST') {
				$arEvent['VALUE_OLD'] = $arEvent['VALUE_OLD']=='N'?GetMessage('ORDER_FIELD_CHECKBOX_NO'):GetMessage('ORDER_FIELD_CHECKBOX_YES');
				$arEvent['VALUE_NEW'] = $arEvent['VALUE_NEW']=='N'?GetMessage('ORDER_FIELD_CHECKBOX_NO'):GetMessage('ORDER_FIELD_CHECKBOX_YES');
			} elseif($arEvent['ENTITY_FIELD']=='SOURCE') {
				if (!isset($arAppSource)) {
					$arAppSource=COrderHelper::GetEnumList('APP','SOURCE');
				}
				if(substr($arEvent['VALUE_OLD'], 0, 5) == 'OTHER') {
					$arEvent['VALUE_OLD']=GetMessage('ORDER_FIELD_APP_SOURCE_OTHER').substr($arEvent['VALUE_OLD'], 5);
				} else {
					$arEvent['VALUE_OLD'] = $arAppSource[$arEvent['VALUE_OLD']];
				}
				if(substr($arEvent['VALUE_NEW'], 0, 5) == 'OTHER') {
					$arEvent['VALUE_NEW']=GetMessage('ORDER_FIELD_APP_SOURCE_OTHER').substr($arEvent['VALUE_NEW'], 5);
				} else {
					$arEvent['VALUE_NEW'] = $arAppSource[$arEvent['VALUE_NEW']];
				}
			} elseif($arEvent['ENTITY_FIELD']=='REG') {
				if (!isset($arReg)) {
					$res = COrderReg::GetListEx(array(), array(), false, false, array('ID'));
					while ($el = $res->Fetch()) {
						$arReg[$el['ID']] = $el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arReg[$arEvent['VALUE_OLD']])?('<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_REG_EDIT'],array('reg_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . '['.$arEvent['VALUE_OLD'].']' . '</a>'):('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arReg[$arEvent['VALUE_NEW']])?('<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_REG_EDIT'],array('reg_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . '['.$arEvent['VALUE_NEW'].']' . '</a>'):('['.$arEvent['VALUE_NEW'].']')):'';
			} elseif($arEvent['ENTITY_FIELD']=='ASSIGNED_ID') {
				if(!isset($CAccess))
					$CAccess = new CAccess();
				$arNames = $CAccess->GetNames(array($arEvent['VALUE_OLD'],$arEvent['VALUE_NEW']));
				$assignedOld = isset($arNames[$arEvent['VALUE_OLD']])?htmlspecialcharsbx($arNames[$arEvent['VALUE_OLD']]['name']):'';
				$providerOld = isset($arNames[$arEvent['VALUE_OLD']])?htmlspecialcharsbx($arNames[$arEvent['VALUE_OLD']]['provider']):'';
				if(!empty($providerOld))
				{
					$assignedOld = '<b>'.htmlspecialcharsbx($providerOld).':</b> '.$assignedOld;
				}
				$assignedNew = isset($arNames[$arEvent['VALUE_OLD']])?htmlspecialcharsbx($arNames[$arEvent['VALUE_NEW']]['name']):'';
				$providerNew = isset($arNames[$arEvent['VALUE_OLD']])?htmlspecialcharsbx($arNames[$arEvent['VALUE_NEW']]['provider']):'';
				if(!empty($providerNew))
				{
					$assignedNew = '<b>'.htmlspecialcharsbx($providerNew).':</b> '.$assignedNew;
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?($assignedOld!='')?$assignedOld:('['.$arEvent['VALUE_OLD'].']'):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?($assignedNew!='')?$assignedNew:('['.$arEvent['VALUE_NEW'].']'):'';
			}
			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => '['.$arEvent['ENTITY_ID'].']',
				'ENTITY_LINK' => isset($arApp[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_APP_EDIT'], array('app_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));
			break;
		case 'REG':
			if (!isset($arReg)) {
				$res = COrderReg::GetListEx(array(), array(), false, false, array('ID'));
				while ($el = $res->Fetch()) {
					$arReg[$el['ID']] = $el;
				}
			}
			if($arEvent['ENTITY_FIELD']=='PHYSICAL_ID') {
				if (!isset($arPhysical)) {
					$res = COrderPhysical::GetListEx(array(), array(), false, false, array('FULL_NAME', 'ID'));
					while ($el = $res->Fetch()) {
						$arPhysical[$el['ID']] = $el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arPhysical[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'],array('physical_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arPhysical[$arEvent['VALUE_OLD']]['FULL_NAME'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arPhysical[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'],array('physical_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arPhysical[$arEvent['VALUE_NEW']]['FULL_NAME'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			} elseif($arEvent['ENTITY_FIELD']=='STATUS') {
				if (!isset($arRegStatus)) {
					$arRegStatus=COrderHelper::GetEnumList('REG','STATUS');
				}
				$arEvent['VALUE_OLD'] = $arRegStatus[$arEvent['VALUE_OLD']];
				$arEvent['VALUE_NEW'] = $arRegStatus[$arEvent['VALUE_NEW']];
			} elseif($arEvent['ENTITY_FIELD']=='ENTITY') {
				if (!isset($arDirection)) {
					$res = COrderDirection::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
					while ($el = $res->Fetch()) {
						$arDirection[$el['ID']] = $el;
					}
				}
				if (!isset($arNomen)) {
					$res = COrderNomen::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
					while ($el = $res->Fetch()) {
						$arNomen[$el['ID']] = $el;
					}
				}
				if (!isset($arGroup)) {
					$res = COrderGroup::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
					while ($el = $res->Fetch()) {
						$arGroup[$el['ID']] = $el;
					}
				}
				if (!isset($arFormedGroup)) {
					$res = COrderFormedGroup::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
					while ($el = $res->Fetch()) {
						$arFormedGroup[$el['ID']] = $el;
					}
				}

				$ary = explode('#_#', $arEvent['VALUE_OLD']);
				$arEvent['VALUE_OLD'] = $ary[1];
				$type=strtoupper($ary[0]);
				switch($type) {
					case 'DIRECTION':
						$arEvent['VALUE_OLD'] = '<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_DIRECTION_EDIT'],array('direction_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arDirection[$arEvent['VALUE_OLD']]['TITLE'] . '</a>';
						break;
					case 'NOMEN':
						$arEvent['VALUE_OLD'] = '<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_NOMEN_EDIT'],array('nomen_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arNomen[$arEvent['VALUE_OLD']]['TITLE'] . '</a>';
						break;
					case 'GROUP':
						$arEvent['VALUE_OLD'] = '<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_GROUP_EDIT'],array('group_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arGroup[$arEvent['VALUE_OLD']]['TITLE'] . '</a>';
						break;
					case 'FORMED_GROUP':
						$arEvent['VALUE_OLD'] = '<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_FORMED_GROUP_EDIT'],array('formed_group_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arFormedGroup[$arEvent['VALUE_OLD']]['TITLE'] . '</a>';
						break;

				}
				$ary = explode('#_#', $arEvent['VALUE_NEW']);
				$arEvent['VALUE_NEW'] = $ary[1];
				$type=strtoupper($ary[0]);
				switch($type) {
					case 'DIRECTION':
						$arEvent['VALUE_NEW'] = '<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_DIRECTION_EDIT'],array('direction_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arDirection[$arEvent['VALUE_NEW']]['TITLE'] . '</a>';
						break;
					case 'NOMEN':
						$arEvent['VALUE_NEW'] = '<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_NOMEN_EDIT'],array('nomen_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arNomen[$arEvent['VALUE_NEW']]['TITLE'] . '</a>';
						break;
					case 'GROUP':
						$arEvent['VALUE_NEW'] = '<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_GROUP_EDIT'],array('group_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arGroup[$arEvent['VALUE_NEW']]['TITLE'] . '</a>';
						break;
					case 'FORMED_GROUP':
						$arEvent['VALUE_NEW'] = '<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_FORMED_GROUP_EDIT'],array('formed_group_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arFormedGroup[$arEvent['VALUE_NEW']]['TITLE'] . '</a>';
						break;
				}

			}
			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => '['.$arEvent['ENTITY_ID'].']',
				'ENTITY_LINK' =>isset($arReg[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_APP_EDIT'], array('app_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));
			break;
		case 'TEACHER':
			if (!isset($arTeacher)) {
				$res = COrderTeacher::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
				while ($el = $res->Fetch()) {
					$arTeacher[$el['ID']] = $el;
				}
			}
			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => isset($arTeacher[$arEvent['ENTITY_ID']])?$arTeacher[$arEvent['ENTITY_ID']]['TITLE']:('['.$arEvent['ENTITY_ID'].']'),
				'ENTITY_LINK' => isset($arTeacher[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_TEACHER_EDIT'], array('teacher_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));

			break;
		case 'ROOM':
			if (!isset($arRoom)) {
				$res = COrderRoom::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
				while ($el = $res->Fetch()) {
					$arRoom[$el['ID']] = $el;
				}
			}
			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => isset($arRoom[$arEvent['ENTITY_ID']])?$arRoom[$arEvent['ENTITY_ID']]['TITLE']:('['.$arEvent['ENTITY_ID'].']'),
				'ENTITY_LINK' => isset($arRoom[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_ROOM_EDIT'], array('room_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));

			break;
		case 'SCHEDULE':
			if (!isset($arSchedule)) {
				$res = COrderSchedule::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
				while ($el = $res->Fetch()) {
					$arSchedule[$el['ID']] = $el;
				}
			}
			if($arEvent['ENTITY_FIELD']=='GROUP_ID') {
				if (!isset($arGroup)) {
					$res = COrderGroup::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
					while ($el = $res->Fetch()) {
						$arGroup[$el['ID']] = $el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arGroup[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_GROUP_EDIT'],array('group_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arGroup[$arEvent['VALUE_OLD']]['TITLE'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arGroup[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_GROUP_EDIT'],array('group_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arGroup[$arEvent['VALUE_NEW']]['TITLE'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			}elseif($arEvent['ENTITY_FIELD']=='ROOM_ID') {
				if (!isset($arRoom)) {
					$res = COrderRoom::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
					while ($el = $res->Fetch()) {
						$arRoom[$el['ID']] = $el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arRoom[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_ROOM_EDIT'],array('room_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arRoom[$arEvent['VALUE_OLD']]['TITLE'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arRoom[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_ROOM_EDIT'],array('room_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arRoom[$arEvent['VALUE_NEW']]['TITLE'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			}elseif($arEvent['ENTITY_FIELD']=='COURSE_ID') {
				if (!isset($arCourse)) {
					$res = COrderCourse::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
					while ($el = $res->Fetch()) {
						$arCourse[$el['ID']] = $el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arCourse[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_COURSE_EDIT'],array('course_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arCourse[$arEvent['VALUE_OLD']]['TITLE'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arCourse[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_COURSE_EDIT'],array('course_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arCourse[$arEvent['VALUE_NEW']]['TITLE'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			}if($arEvent['ENTITY_FIELD']=='TEACHER_ID') {
			if (!isset($arTeacher)) {
				$res = COrderTeacher::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
				while ($el = $res->Fetch()) {
					$arTeacher[$el['ID']] = $el;
				}
			}
			$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arTeacher[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_TEACHER_EDIT'],array('teacher_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arTeacher[$arEvent['VALUE_OLD']]['TITLE'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
			$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arTeacher[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_TEACHER_EDIT'],array('teacher_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arTeacher[$arEvent['VALUE_NEW']]['TITLE'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
		}
			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => '['.$arEvent['ENTITY_ID'].']',
				'ENTITY_LINK' => isset($arSchedule[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_SCHEDULE_EDIT'], array('schedule_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));

			break;
		case 'MARK':
			if (!isset($arMark)) {
				$res = COrderMark::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
				while ($el = $res->Fetch()) {
					$arMark[$el['ID']] = $el;
				}
			}
			if($arEvent['ENTITY_FIELD']=='PHYSICAL_ID') {
				if (!isset($arPhysical)) {
					$res = COrderPhysical::GetListEx(array(), array(), false, false, array('FULL_NAME', 'ID'));
					while ($el = $res->Fetch()) {
						$arPhysical[$el['ID']] = $el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arPhysical[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'],array('physical_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arPhysical[$arEvent['VALUE_OLD']]['FULL_NAME'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arPhysical[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'],array('physical_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arPhysical[$arEvent['VALUE_NEW']]['FULL_NAME'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			}elseif($arEvent['ENTITY_FIELD']=='GROUP_ID') {
				if (!isset($arGroup)) {
					$res = COrderGroup::GetListEx(array(), array(), false, false, array('TITLE', 'ID'));
					while ($el = $res->Fetch()) {
						$arGroup[$el['ID']] = $el;
					}
				}
				$arEvent['VALUE_OLD'] = strlen($arEvent['VALUE_OLD'])>0?(isset($arGroup[$arEvent['VALUE_OLD']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_GROUP_EDIT'],array('group_id' => $arEvent['VALUE_OLD'])) . '" target="_blank">' . $arGroup[$arEvent['VALUE_OLD']]['TITLE'] . '</a>':('['.$arEvent['VALUE_OLD'].']')):'';
				$arEvent['VALUE_NEW'] = strlen($arEvent['VALUE_NEW'])>0?(isset($arGroup[$arEvent['VALUE_NEW']])?'<a href="' . CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_GROUP_EDIT'],array('group_id' => $arEvent['VALUE_NEW'])) . '" target="_blank">' . $arGroup[$arEvent['VALUE_NEW']]['TITLE'] . '</a>':('['.$arEvent['VALUE_NEW'].']')):'';
			}
			$arEvent=array_merge($arEvent,array(
				'ENTITY_TITLE' => '['.$arEvent['ENTITY_ID'].']',
				'ENTITY_LINK' => isset($arMark[$arEvent['ENTITY_ID']])?CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_MARK_EDIT'], array('mark_id' => $arEvent['ENTITY_ID'])):'javascript:void()'
			));

			break;
	}
	if (strlen($arEvent['VALUE_OLD'])>255 && strlen($arEvent['VALUE_NEW'])>255)
	{
		$arEvent['EVENT_DESC'] = '<div id="event_desc_short_'.$arEvent['ID'].'"><a href="#more" onclick="order_event_desc('.$arEvent['ID'].'); return false;">'.GetMessage('ORDER_EVENT_DESC_MORE').'</a></div>';
		$arEvent['EVENT_DESC'] .= '<div id="event_desc_full_'.$arEvent['ID'].'" style="display: none"><b>'.GetMessage('ORDER_EVENT_DESC_BEFORE').'</b>:<br>'.($arEvent['VALUE_OLD']).'<br><br><b>'.GetMessage('ORDER_EVENT_DESC_AFTER').'</b>:<br>'.($arEvent['VALUE_NEW']).'</div>';
	}
	elseif (strlen($arEvent['VALUE_OLD'])>255)
	{
		$arEvent['EVENT_DESC'] = '<div id="event_desc_short_'.$arEvent['ID'].'">'.substr(($arEvent['VALUE_OLD']), 0, 252).'... <a href="#more" onclick="order_event_desc('.$arEvent['ID'].'); return false;">'.GetMessage('ORDER_EVENT_DESC_MORE').'</a></div>';
		$arEvent['EVENT_DESC'] .= '<div id="event_desc_full_'.$arEvent['ID'].'" style="display: none">'.($arEvent['VALUE_OLD']).'</div>';
	}
	else if (strlen($arEvent['VALUE_NEW'])>255)
	{
		$arEvent['EVENT_DESC'] = '<div id="event_desc_short_'.$arEvent['ID'].'">'.substr(($arEvent['VALUE_NEW']), 0, 252).'... <a href="#more" onclick="order_event_desc('.$arEvent['ID'].'); return false;">'.GetMessage('ORDER_EVENT_DESC_MORE').'</a></div>';
		$arEvent['EVENT_DESC'] .= '<div id="event_desc_full_'.$arEvent['ID'].'" style="display: none">'.($arEvent['VALUE_NEW']).'</div>';
	}
	else if (strlen($arEvent['VALUE_OLD'])>0 && strlen($arEvent['VALUE_NEW'])>0) {

		$arEvent['EVENT_DESC'] = ($arEvent['VALUE_OLD']).' <span>&rarr;</span> '.($arEvent['VALUE_NEW']);
	}
	else
		$arEvent['EVENT_DESC'] = !empty($arEvent['VALUE_NEW'])? ($arEvent['VALUE_NEW']): '';
	$arEvent['EVENT_DESC'] = nl2br($arEvent['EVENT_DESC']);

	/*$arEvent['FILES'] = $arEvent['~FILES'] = $arEvent['FILES'] !== '' ? unserialize($arEvent['FILES']) : array();
	if (!empty($arEvent['FILES']))
	{
		$i=1;
		$arFiles = array();
		$arFilter = array(
			'@ID' => implode(',', $arEvent['FILES'])
		);
		$rsFile = CFile::GetList(array(), $arFilter);
		while($arFile = $rsFile->Fetch())
		{
			$arFiles[$i++] = array(
				'NAME' => $arFile['ORIGINAL_NAME'],
				'PATH' => CComponentEngine::MakePathFromTemplate(
					'/bitrix/components/bitrix/order.event.view/show_file.php?eventId=#event_id#&fileId=#file_id#',
					array('event_id' => $arEvent['ID'], 'file_id' => $arFile['ID'])
				),
				'SIZE' => CFile::FormatSize($arFile['FILE_SIZE'], 1)
			);
		}
		$arEvent['FILES'] = $arFiles;
	}*/
	$arEntityList[$arEvent['ENTITY_TYPE']][$arEvent['ENTITY_ID']] = $arEvent['ENTITY_ID'];

	$arResult['EVENT'][] = $arEvent;
}



$this->IncludeComponentTemplate();

return $obRes->SelectedRowsCount();

?>
