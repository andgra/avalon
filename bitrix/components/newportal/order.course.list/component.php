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
if ($COrderPerms->HavePerm('COURSE', BX_ORDER_PERM_NONE, 'READ'))
{
    ShowError(GetMessage('ORDER_PERMISSION_DENIED'));
    return;
}


$userID = COrderHelper::GetCurrentUserID();
$isAdmin = COrderPerms::IsAdmin();


$arResult['CURRENT_USER_ID'] = $userID;
$arParams['PATH_TO_COURSE_LIST'] = OrderCheckPath('PATH_TO_COURSE_LIST', $arParams['PATH_TO_COURSE_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_COURSE_EDIT'] = OrderCheckPath('PATH_TO_COURSE_EDIT', $arParams['PATH_TO_COURSE_EDIT'], '/order/course/edit/#course_id#');
$arParams['PATH_TO_NOMEN_EDIT'] = OrderCheckPath('PATH_TO_NOMEN_EDIT', $arParams['PATH_TO_NOMEN_EDIT'], '/order/nomen/edit/#nomen_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);

$arResult['IS_AJAX_CALL'] = isset($_REQUEST['bxajaxid']) || isset($_REQUEST['AJAX_CALL']);
$arResult['SESSION_ID'] = bitrix_sessid();
$arResult['GRID_ID']='ORDER_COURSE_LIST_V12';

$COrderCourse=new COrderCourse(false);

CUtil::InitJSCore(array('ajax', 'tooltip'));


$arResult['FORM_ID'] = isset($arParams['FORM_ID']) ? $arParams['FORM_ID'] : '';
$arResult['TAB_ID'] = isset($arParams['TAB_ID']) ? $arParams['TAB_ID'] : '';
$bInternal=false;


if(check_bitrix_sessid()) {
    if(isset($_POST['save']) && $_POST['save'] ==GetMessage('ORDER_BUTTON_SAVE')) {

        foreach($_POST['FIELDS'] as $id => $fields) {
            if(!$COrderCourse->Update($id,$fields))
                $arrError[]=$COrderCourse->LAST_ERROR;
        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_COURSE_LIST']
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
            if(!$COrderCourse->Update($id,$prop))
                $arrError[]=$COrderCourse->LAST_ERROR;

        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_COURSE_LIST']
                )
            );
        }
    }
    if($_REQUEST['action_button_'.$arResult['GRID_ID']]=='delete') {
        foreach($_REQUEST['ID'] as $id) {
            if(!$COrderCourse->Delete($id))
                $arrError[]=$COrderCourse->LAST_ERROR;
        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_COURSE_LIST']
                )
            );
        }
    }
}

ob_start();
$APPLICATION->IncludeComponent('newportal:order.entity.selector',
    '',
    array(
        'ENTITY_TYPE' => 'COURSE',
        'INPUT_NAME' => 'PREV_COURSE',
        'INPUT_VALUE' => isset($resFilter['PREV_COURSE']) ? intval($resFilter['PREV_COURSE']) : '',
        'FORM_NAME' => $arResult['GRID_ID'],
        'MULTIPLE' => 'N',
        'FILTER' => true
    ),
    false,
    array('HIDE_ICONS' => 'Y')
);
$sValCourse = '<div style="padding-top:0.5em;">'.ob_get_contents().'</div>';
ob_end_clean();


$arFilter = $arSort = array();

$arResult['FILTER'] = array(
    array('id' => 'ID', 'name' => GetMessage('ORDER_COLUMN_ID')),
    array('id' => 'TITLE', 'name' => GetMessage('ORDER_COLUMN_TITLE')),
    array('id' => 'PREV_COURSE', 'name' => GetMessage('ORDER_COLUMN_PREV_COURSE_LIST'), 'type' => 'custom', 'value' => $sValCourse, 'default' => true),
    array('id' => 'PREV_COURSE_TITLE', 'name' => GetMessage('ORDER_COLUMN_PREV_COURSE')),
    array('id' => 'ANNOTATION', 'name' => GetMessage('ORDER_COLUMN_ANNOTATION')),
    array('id' => 'DESCRIPTION', 'name' => GetMessage('ORDER_COLUMN_DESCRIPTION')),
    array('id' => 'COURSE_PROG', 'name' => GetMessage('ORDER_COLUMN_COURSE_PROG')),
    array('id' => 'DURATION', 'name' => GetMessage('ORDER_COLUMN_DURATION'))
);

$arResult['HEADERS']=array(
    array('id' => 'ID', 'name' => GetMessage('ORDER_COLUMN_ID'), 'default' => true, 'sort' => 'ID', 'editable' => false),
    array('id' => 'TITLE', 'name' => GetMessage('ORDER_COLUMN_TITLE'), 'default' => true, 'sort' => 'TITLE', 'editable' => false),
    array('id' => 'ANNOTATION', 'name' => "<div style='margin: 0px 100px;'>".GetMessage('ORDER_COLUMN_ANNOTATION')."</div>", 'default' => true, 'sort' => 'ANNOTATION', 'editable' => false,'type'=>'textarea'),
    array('id' => 'DESCRIPTION', 'name' => GetMessage('ORDER_COLUMN_DESCRIPTION'), 'default' => true, 'sort' => 'DESCRIPTION', 'editable' => false,'type'=>'textarea'),
    array('id' => 'COURSE_PROG', 'name' => GetMessage('ORDER_COLUMN_COURSE_PROG'), 'default' => true, 'sort' => 'COURSE_PROG', 'editable' => false),
    array('id' => 'DURATION', 'name' => GetMessage('ORDER_COLUMN_DURATION'), 'default' => true, 'sort' => 'DURATION', 'editable' => false),
    array('id' => 'PREV_COURSE', 'name' => GetMessage('ORDER_COLUMN_PREV_COURSE'), 'default' => true, 'sort' => 'PREV_COURSE_TITLE', 'editable' => false),
    array('id' => 'EXAM', 'name' => "<div style='margin: 0px 50px;'>".GetMessage('ORDER_COLUMN_EXAM')."</div>", 'default' => true, 'sort' => 'EXAM', 'editable' => false),
    array('id' => 'LITER', 'name' => "<div style='margin: 0px 50px;'>".GetMessage('ORDER_COLUMN_LITER')."</div>", 'default' => true, 'sort' => 'LITER', 'editable' => false),
    array('id' => 'DOC', 'name' => "<div style='margin: 0px 50px;'>".GetMessage('ORDER_COLUMN_DOC')."</div>", 'default' => true, 'sort' => 'DOC', 'editable' => false),
    array('id' => 'NOMEN', 'name' => "<div style='margin: 0px 50px;'>".GetMessage('ORDER_COLUMN_NOMEN')."</div>", 'default' => true, 'sort' => 'NOMEN', 'editable' => false),
    array('id' => 'TEACHER', 'name' => "<div style='margin: 0px 50px;'>".GetMessage('ORDER_COLUMN_TEACHER')."</div>", 'default' => true, 'sort' => 'TEACHER', 'editable' => false),
);


$arResult['SORT_VARS']=array('by'=>'by','order'=>'order');
if(isset($_REQUEST['by']) && isset($_REQUEST['order'])) {
    $arResult['SORT']=array($_REQUEST['by']=>$_REQUEST['order']);
}
else {
    $arResult['SORT'] = array('TITLE' => 'asc');
}




if (intval($arParams['COURSE_COUNT']) <= 0)
    $arParams['COURSE_COUNT'] = 20;

$arNavParams = array(
    'nPageSize' => $arParams['COURSE_COUNT']
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
$arImmutableFilters = array('ID', 'PREV_COURSE','EXAM','LITER','DOC','NOMEN','TEACHER');
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

$dbRes=COrderCourse::GetListEx($arResult['SORT'],$arFilter);
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
        "PATH_TO_COURSE_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_COURSE_EDIT'],
            array('course_id' => $el["ID"])
        ),
        "PATH_TO_PREV_COURSE_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_COURSE_EDIT'],
            array('course_id' => $el["PREV_COURSE"])
        ),
        "PATH_TO_COURSE_DELETE" => $arParams['PATH_TO_COURSE_LIST'].'?action_button_'.$arResult['GRID_ID'].'=delete&ID[]='.$el["ID"].'&sessid='.$arResult['SESSION_ID'],
        "PATH_TO_STAFF_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_STAFF_EDIT'],
            array('staff_id' => $userID)
        ),
        "PATH_TO_MODIFY_BY" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_PHYSICAL_EDIT'],
            array('physical_id' => $el["MODIFY_BY_ID"])
        ),
    ));
    $arElems[$el['ID']]=$el;
}

$arResult["COURSE"]=$arElems;

$res=COrderNomen::GetListEx(array(),array(),false,false,array('ID','TITLE'));
while($el=$res->Fetch()) {
    $arResult["NOMEN"][$el['ID']]=$el;
}

$res=COrderPhysical::GetListEx(array(),array(),false,false,array('ID','FULL_NAME'));
while($el=$res->Fetch()) {
    $arResult["PHYSICAL"][$el['ID']]=$el;
}

$arResult['AJAX_MODE']=$arParams['AJAX_MODE'];
$arResult['AJAX_ID']=$arParams['AJAX_ID'];
$arResult['AJAX_OPTION_JUMP']=$arParams['AJAX_OPTION_JUMP'];
$arResult['AJAX_OPTION_HISTORY']=$arParams['AJAX_OPTION_HISTORY'];
$arResult['PERMS']['ADD']    = false;
$arResult['PERMS']['EDIT']   = false;
$arResult['PERMS']['DELETE'] = false;
/*$arResult['PERMS']['ADD']    = !$COrderPerms->HavePerm('COURSE', BX_ORDER_PERM_NONE, 'ADD');
$arResult['PERMS']['EDIT']   = !$COrderPerms->HavePerm('COURSE', BX_ORDER_PERM_NONE, 'EDIT');
$arResult['PERMS']['DELETE'] = !$COrderPerms->HavePerm('COURSE', BX_ORDER_PERM_NONE, 'DELETE');*/






$this->IncludeComponentTemplate();
?>