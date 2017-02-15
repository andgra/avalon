<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();



if (!CModule::IncludeModule('order'))
{
    ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
    return;
}
CJSCore::Init ();
global $USER_FIELD_MANAGER, $USER, $APPLICATION;

$COrderPerms = COrderPerms::GetCurrentUserPermissions();
if ($COrderPerms->HavePerm('DIRECTION', BX_ORDER_PERM_NONE, 'READ'))
{
    ShowError(GetMessage('ORDER_PERMISSION_DENIED'));
    return;
}


$userID = COrderHelper::GetCurrentUserID();
$isAdmin = COrderPerms::IsAdmin();


$arResult['CURRENT_USER_ID'] = $userID;
$arParams['PATH_TO_DIRECTION_LIST'] = OrderCheckPath('PATH_TO_DIRECTION_LIST', $arParams['PATH_TO_DIRECTION_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_DIRECTION_EDIT'] = OrderCheckPath('PATH_TO_DIRECTION_EDIT', $arParams['PATH_TO_DIRECTION_EDIT'], '/order/direction/edit/#direction_id#');
$arParams['PATH_TO_PHYSICAL_EDIT'] = OrderCheckPath('PATH_TO_PHYSICAL_EDIT', $arParams['PATH_TO_PHYSICAL_EDIT'], '/order/physical/edit/#physical_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);

$arResult['IS_AJAX_CALL'] = isset($_REQUEST['bxajaxid']) || isset($_REQUEST['AJAX_CALL']);
$arResult['SESSION_ID'] = bitrix_sessid();
$arResult['GRID_ID']='ORDER_DIRECTION_LIST_V12';

$COrderDirection=new COrderDirection(false);

CUtil::InitJSCore(array('ajax', 'tooltip'));


$arResult['FORM_ID'] = isset($arParams['FORM_ID']) ? $arParams['FORM_ID'] : '';
$arResult['TAB_ID'] = isset($arParams['TAB_ID']) ? $arParams['TAB_ID'] : '';
$bInternal=false;


if(check_bitrix_sessid()) {
    if(isset($_POST['save']) && $_POST['save'] ==GetMessage('ORDER_BUTTON_SAVE')) {

        foreach($_POST['FIELDS'] as $id => $fields) {
            if(!$COrderDirection->Update($id,$fields))
                $arrError[]=$COrderDirection->LAST_ERROR;
        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_DIRECTION_LIST']
                )
            );
        }
    }
    if(isset($_POST['apply']) && $_POST['apply']==GetMessage('ORDER_BUTTON_APPLY') &&
        ($_POST['ACTION_STATUS_ID']!='' || $_POST['ACTION_DESCRIPTION']!=''
            || $_POST['ACTION_ASSIGNED_ID']!='')) {
        $prop=array();
        if($_POST['action_button_'.$arResult['GRID_ID']]=='set_status' && $_POST['ACTION_STATUS_ID']!='') {
            $prop['STATUS'] = $_POST['ACTION_STATUS_ID'];
        }

        if($_POST['action_button_'.$arResult['GRID_ID']]=='set_description' && $_POST['ACTION_DESCRIPTION']!='') {
            $prop['DESCRIPTION'] = $_POST['ACTION_DESCRIPTION'];
        }

        if($_REQUEST['action_button_'.$arResult['GRID_ID']]=='set_assigned' && $_POST['ACTION_ASSIGNED_ID']!='') {
            $prop['ASSIGNED_ID'] = $_POST['ACTION_ASSIGNED_ID'];
        }

        foreach($_POST['ID'] as $id) {
            if(!$COrderDirection->Update($id,$prop))
                $arrError[]=$COrderDirection->LAST_ERROR;

        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_DIRECTION_LIST']
                )
            );
        }
    }
    if($_REQUEST['action_button_'.$arResult['GRID_ID']]=='delete') {
        foreach($_REQUEST['ID'] as $id) {
            if(!$COrderDirection->Delete($id))
                $arrError[]=$COrderDirection->LAST_ERROR;
        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_DIRECTION_LIST']
                )
            );
        }
    }
}


ob_start();
$APPLICATION->IncludeComponent('newportal:order.entity.selector',
    '',
    array(
        'ENTITY_TYPE' => 'PHYSICAL',
        'INPUT_NAME' => 'MANAGER_ID',
        'INPUT_VALUE' => isset($resFilter['MANAGER_ID']) ? intval($resFilter['MANAGER_ID']) : '',
        'FORM_NAME' => $arResult['GRID_ID'],
        'MULTIPLE' => 'N',
        'FILTER' => true
    ),
    false,
    array('HIDE_ICONS' => 'Y')
);
$sValManager = '<div style="padding-top:0.5em;">'.ob_get_contents().'</div>';
ob_end_clean();

ob_start();
$APPLICATION->IncludeComponent('newportal:order.entity.selector',
    '',
    array(
        'ENTITY_TYPE' => 'DIRECTION',
        'INPUT_NAME' => 'PARENT_ID',
        'INPUT_VALUE' => isset($resFilter['PARENT_ID']) ? intval($resFilter['PARENT_ID']) : '',
        'FORM_NAME' => $arResult['GRID_ID'],
        'MULTIPLE' => 'N',
        'FILTER' => true
    ),
    false,
    array('HIDE_ICONS' => 'Y')
);
$sValParent = '<div style="padding-top:0.5em;">'.ob_get_contents().'</div>';
ob_end_clean();


$arFilter = $arSort = array();

$arResult['FILTER'] = array(
    array('id' => 'ID', 'name' => GetMessage('ORDER_COLUMN_ID')),
    array('id' => 'TITLE', 'name' => GetMessage('ORDER_COLUMN_TITLE')),
    array('id' => 'PARENT_ID', 'name' => GetMessage('ORDER_COLUMN_PARENT_LIST'), 'type' => 'custom', 'value' => $sValParent, 'default' => true),
    array('id' => 'PARENT_TITLE', 'name' => GetMessage('ORDER_COLUMN_PARENT')),
    array('id' => 'MANAGER_ID', 'name' => GetMessage('ORDER_COLUMN_MANAGER_LIST'), 'type' => 'custom', 'value' => $sValManager, 'default' => true),
    array('id' => 'PRIVATE', 'name' => GetMessage('ORDER_COLUMN_PRIVATE'), 'type'=>'checkbox'),
    array('id' => 'DESCRIPTION', 'name' => GetMessage('ORDER_COLUMN_DESCRIPTION')),
    array('id' => 'MANAGER_FULL_NAME', 'name' => GetMessage('ORDER_COLUMN_MANAGER')),
);

$arResult['HEADERS']=array(
    array('id' => 'ID', 'name' => GetMessage('ORDER_COLUMN_ID'), 'default' => true, 'sort' => 'ID', 'editable' => false),
    array('id' => 'TITLE', 'name' => GetMessage('ORDER_COLUMN_TITLE'), 'default' => true, 'sort' => 'TITLE', 'editable' => false),
    array('id' => 'PARENT_ID', 'name' => GetMessage('ORDER_COLUMN_PARENT'), 'default' => true, 'sort' => 'PARENT_TITLE', 'editable' => false),
    array('id' => 'MANAGER_ID', 'name' => GetMessage('ORDER_COLUMN_MANAGER'), 'default' => true, 'sort' => 'MANAGER_FULL_NAME', 'editable' => false),
    array('id' => 'PRIVATE', 'name' => GetMessage('ORDER_COLUMN_PRIVATE'), 'default' => true, 'sort' => 'PRIVATE', 'editable' => false,'type'=>'checkbox'),
    array('id' => 'DESCRIPTION', 'name' => GetMessage('ORDER_COLUMN_DESCRIPTION'), 'default' => true, 'sort' => 'DESCRIPTION', 'editable' => false,'type'=>'textarea'),
    array('id' => 'MODIFY_DATE', 'name' => GetMessage('ORDER_COLUMN_MODIFY_DATE'), 'default' => true, 'sort' => 'MODIFY_DATE', 'editable' => false, 'type' => 'date'),
    array('id' => 'MODIFY_BY_ID', 'name' => GetMessage('ORDER_COLUMN_MODIFY_BY_ID'), 'default' => true, 'sort' => 'MODIFY_BY_FULL_NAME', 'editable' => false),
);


$arResult['SORT_VARS']=array('by'=>'by','order'=>'order');
if(isset($_REQUEST['by']) && isset($_REQUEST['order'])) {
    $arResult['SORT']=array($_REQUEST['by']=>$_REQUEST['order']);
}
else {
    $arResult['SORT'] = array('TITLE' => 'asc');
}



if (intval($arParams['DIRECTION_COUNT']) <= 0)
    $arParams['DIRECTION_COUNT'] = 20;

$arNavParams = array(
    'nPageSize' => $arParams['DIRECTION_COUNT']
);

$arNavigation = CDBResult::GetNavParams($arNavParams);
$CGridOptions = new COrderGridOptions($arResult['GRID_ID']);
$arNavParams = $CGridOptions->GetNavParams($arNavParams);
$arNavParams['bShowAll'] = false;



$arFilter = array();
$arFilter += $CGridOptions->GetFilter($arResult['FILTER']);

if($arFilter['GRID_FILTER_APPLIED']==false)
    $arFilter=array();

// converts data from filter
if (isset($arFilter['FIND_list']) && !empty($arFilter['FIND']))
{
    if ($arFilter['FIND_list'] == 't_n_ln')
    {
        $arFilter['TITLE'] = $arFilter['FIND'];
        $arFilter['NAME'] = $arFilter['FIND'];
        $arFilter['LAST_NAME'] = $arFilter['FIND'];
        $arFilter['LOGIC'] = 'OR';
    }
    else
        $arFilter[strtoupper($arFilter['FIND_list'])] = $arFilter['FIND'];
    unset($arFilter['FIND_list'], $arFilter['FIND']);
}

//CCrmEntityHelper::PrepareMultiFieldFilter($arFilter);
$arImmutableFilters = array('ID', 'PARENT_ID', 'MANAGER_ID');
foreach ($arFilter as $k => $v)
{
    if(in_array($k, $arImmutableFilters, true))
    {
        continue;
    }

    $arMatch = array();

    if(in_array($k, array('STATUS')))
    {
        // Bugfix #23121 - to suppress comparison by LIKE
        $arFilter['='.$k] = $v;
        unset($arFilter[$k]);
    }
    elseif($k === 'ORIGINATOR_ID')
    {
        // HACK: build filter by internal entities
        $arFilter['=ORIGINATOR_ID'] = $v !== '__INTERNAL' ? $v : null;
        unset($arFilter[$k]);
    }
    elseif (preg_match('/(.*)_from$/i'.BX_UTF_PCRE_MODIFIER, $k, $arMatch))
    {
        if(strlen($v) > 0)
        {
            $arFilter['>='.$arMatch[1]] = $v;
        }
        unset($arFilter[$k]);
    }
    elseif (preg_match('/(.*)_to$/i'.BX_UTF_PCRE_MODIFIER, $k, $arMatch))
    {
        if(strlen($v) > 0)
        {
            if (($arMatch[1] == 'DATE_CREATE' || $arMatch[1] == 'DATE_MODIFY') && !preg_match('/\d{1,2}:\d{1,2}(:\d{1,2})?$/'.BX_UTF_PCRE_MODIFIER, $v))
            {
                $v .=  ' 23:59:59';
            }
            $arFilter['<='.$arMatch[1]] = $v;
        }
        unset($arFilter[$k]);
    }
    elseif (in_array($k, $arResult['FILTER2LOGIC']))
    {
        // Bugfix #26956 - skip empty values in logical filter
        $v = trim($v);
        if($v !== '')
        {
            $arFilter['?'.$k] = $v;
        }
        unset($arFilter[$k]);
    }
    elseif (strpos($k, 'UF_') !== 0 && $k != 'LOGIC')
    {
        $arFilter['%'.$k] = $v;
        unset($arFilter[$k]);
    }
}

$dbRes=COrderDirection::GetListEx($arResult['SORT'],array_merge($arFilter,array('!ID'=>'000000000')));
$dbRes->NavStart($arNavParams['nPageSize'],$arNavParams['bShowAll']);
$arResult['NAV_OBJECT']=$dbRes;
$arResult['ROWS_COUNT']=$dbRes->SelectedRowsCount();
$arResult['PAGE_SIZE']=$arNavParams['nPageSize'];
$arResult['PAGE_NUM']=$dbRes->NavPageNomer;


while($el=$dbRes->Fetch()) {
    foreach($el as $code=>$val) {
        $arTilt['~'.$code]=$val;
        if(isset($dbRes->arUserFields[$code]) && $dbRes->arUserFields[$code]['MULTIPLE']=='Y') {
            $arTilt[$code]=unserialize(htmlspecialcharsback($val));
        }
    }
    $el=array_merge($el,$arTilt);
    $el=array_merge($el,array(
        "PATH_TO_DIRECTION_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_DIRECTION_EDIT'],
            array('direction_id' => $el["ID"])
        ),
        "PATH_TO_PARENT_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_DIRECTION_EDIT'],
            array('direction_id' => $el["PARENT_ID"])
        ),
        "PATH_TO_DIRECTION_DELETE" => $arParams['PATH_TO_DIRECTION_LIST'].'?action_button_'.$arResult['GRID_ID'].'=delete&ID[]='.$el["ID"].'&sessid='.$arResult['SESSION_ID'],
        "PATH_TO_STAFF_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_STAFF_EDIT'],
            array('staff_id' => $userID)
        ),
        "PATH_TO_MANAGER" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_PHYSICAL_EDIT'],
            array('physical_id' => $el["MANAGER_ID"])
        ),
        "PATH_TO_MODIFY_BY" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_PHYSICAL_EDIT'],
            array('physical_id' => $el["MODIFY_BY_ID"])
        ),
    ));
    $arElems[$el['ID']]=$el;
}

$arResult["DIRECTION"]=$arElems;


$arResult['AJAX_MODE']=$arParams['AJAX_MODE'];
$arResult['AJAX_ID']=$arParams['AJAX_ID'];
$arResult['AJAX_OPTION_JUMP']=$arParams['AJAX_OPTION_JUMP'];
$arResult['AJAX_OPTION_HISTORY']=$arParams['AJAX_OPTION_HISTORY'];
$arResult['PERMS']['ADD']    = false;
$arResult['PERMS']['EDIT']   = false;
$arResult['PERMS']['DELETE'] = false;
/*$arResult['PERMS']['ADD']    = !$COrderPerms->HavePerm('DIRECTION', BX_ORDER_PERM_NONE, 'ADD');
$arResult['PERMS']['EDIT']   = !$COrderPerms->HavePerm('DIRECTION', BX_ORDER_PERM_NONE, 'EDIT');
$arResult['PERMS']['DELETE'] = !$COrderPerms->HavePerm('DIRECTION', BX_ORDER_PERM_NONE, 'DELETE');*/






$this->IncludeComponentTemplate();
?>