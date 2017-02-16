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
if ($COrderPerms->HavePerm('NOMEN', BX_ORDER_PERM_NONE, 'READ'))
{
    ShowError(GetMessage('ORDER_PERMISSION_DENIED'));
    return;
}


$userID = COrderHelper::GetCurrentUserID();
$isAdmin = COrderPerms::IsAdmin();


$arResult['CURRENT_USER_ID'] = $userID;
$arParams['PATH_TO_NOMEN_LIST'] = OrderCheckPath('PATH_TO_NOMEN_LIST', $arParams['PATH_TO_NOMEN_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_NOMEN_EDIT'] = OrderCheckPath('PATH_TO_NOMEN_EDIT', $arParams['PATH_TO_NOMEN_EDIT'], '/order/nomen/edit/#nomen_id#');
$arParams['PATH_TO_DIRECTION_EDIT'] = OrderCheckPath('PATH_TO_DIRECTION_EDIT', $arParams['PATH_TO_DIRECTION_EDIT'], '/order/direction/edit/#direction_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);

$arResult['IS_AJAX_CALL'] = isset($_REQUEST['bxajaxid']) || isset($_REQUEST['AJAX_CALL']);
$bInternal=(isset($arParams['EXTERNAL_ID']) && isset($arParams['FORM_ID']));
$arResult['SESSION_ID'] = bitrix_sessid();
if($bInternal) {
    $arResult['EXTERNAL_EDIT']=$arParams['EDIT'];
    $arResult['EXTERNAL_TYPE']=$arParams['EXTERNAL_TYPE'];
    $arResult['GRID_ID']=$arParams['GRID_ID'];
} else {
    $arResult['GRID_ID']='ORDER_NOMEN_LIST_V12';
}

$COrderNomen=new COrderNomen(false);

CUtil::InitJSCore(array('ajax', 'tooltip'));


$arResult['FORM_ID'] = isset($arParams['FORM_ID']) ? $arParams['FORM_ID'] : '';
$arResult['TAB_ID'] = isset($arParams['TAB_ID']) ? $arParams['TAB_ID'] : '';


if(check_bitrix_sessid() && !$bInternal) {
    if(isset($_POST['save']) && $_POST['save'] ==GetMessage('ORDER_BUTTON_SAVE')) {

        foreach($_POST['FIELDS'] as $id => $fields) {
            if(!$COrderNomen->Update($id,$fields))
                $arrError[]=$COrderNomen->LAST_ERROR;
        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_NOMEN_LIST']
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
            if(!$COrderNomen->Update($id,$prop))
                $arrError[]=$COrderNomen->LAST_ERROR;

        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_NOMEN_LIST']
                )
            );
        }
    }
    if($_REQUEST['action_button_'.$arResult['GRID_ID']]=='delete') {
        foreach($_REQUEST['ID'] as $id) {
            if(!$COrderNomen->Delete($id))
                $arrError[]=$COrderNomen->LAST_ERROR;
        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_NOMEN_LIST']
                )
            );
        }
    }
}


if(!$bInternal) {


    ob_start();
    $APPLICATION->IncludeComponent('newportal:order.entity.selector',
        '',
        array(
            'ENTITY_TYPE' => 'DIRECTION',
            'INPUT_NAME' => 'DIRECTION_ID',
            'INPUT_VALUE' => isset($resFilter['DIRECTION_ID']) ? intval($resFilter['DIRECTION_ID']) : '',
            'FORM_NAME' => $arResult['GRID_ID'],
            'MULTIPLE' => 'N',
            'FILTER' => true
        ),
        false,
        array('HIDE_ICONS' => 'Y')
    );
    $sValDirection = '<div style="padding-top:0.5em;">'.ob_get_contents().'</div>';
    ob_end_clean();


    $arFilter = $arSort = array();

    $arResult['FILTER'] = array(
        array('id' => 'ID', 'name' => GetMessage('ORDER_COLUMN_ID')),
        array('id' => 'TITLE', 'name' => GetMessage('ORDER_COLUMN_TITLE')),
        array('id' => 'DIRECTION_ID', 'name' => GetMessage('ORDER_COLUMN_DIRECTION_LIST'), 'type' => 'custom', 'value' => $sValDirection, 'default' => true),
        array('id' => 'DIRECTION_TITLE', 'name' => GetMessage('ORDER_COLUMN_DIRECTION')),
        array('id' => 'SEMESTER', 'name' => GetMessage('ORDER_COLUMN_SEMESTER')),
        array('id' => 'PRIVATE', 'name' => GetMessage('ORDER_COLUMN_PRIVATE'), 'type'=>'checkbox'),
    );
}

$arResult['HEADERS']=array(
    array('id' => 'ID', 'name' => GetMessage('ORDER_COLUMN_ID'), 'default' => true, 'sort' => 'ID', 'editable' => false),
    array('id' => 'TITLE', 'name' => GetMessage('ORDER_COLUMN_TITLE'), 'default' => true, 'sort' => 'TITLE', 'editable' => false),
    array('id' => 'SEMESTER', 'name' => GetMessage('ORDER_COLUMN_SEMESTER'), 'default' => true, 'sort' => 'SEMESTER', 'editable' => false),
    array('id' => 'PRICE', 'name' => GetMessage('ORDER_COLUMN_PRICE'), 'default' => true, 'sort' => 'PRICE', 'editable' => false),
    array('id' => 'PRIVATE', 'name' => GetMessage('ORDER_COLUMN_PRIVATE'), 'default' => true, 'sort' => 'PRIVATE', 'editable' => false,'type'=>'checkbox'),
);

if(!$bInternal || strtoupper($arResult['EXTERNAL_TYPE'])!='DIRECTION') {
    $arResult['HEADERS'][]=array('id' => 'DIRECTION_ID', 'name' => GetMessage('ORDER_COLUMN_DIRECTION'), 'default' => true, 'sort' => 'DIRECTION_TITLE', 'editable' => false);
}


$sortPrefix=($bInternal?$arResult['GRID_ID']:"");
$arResult['SORT_VARS']=array('by'=>$sortPrefix.'by','order'=>$sortPrefix.'order');
if(isset($_REQUEST[$sortPrefix.'by']) && isset($_REQUEST[$sortPrefix.'order'])) {
    $arResult['SORT']=array($_REQUEST[$sortPrefix.'by'] => $_REQUEST[$sortPrefix.'order']);
}
else {
    $arResult['SORT'] = array('TITLE' => 'asc');
}



if(!$bInternal) {
    if (intval($arParams['NOMEN_COUNT']) <= 0)
        $arParams['NOMEN_COUNT'] = 20;

    $arNavParams = array(
        'nPageSize' => $arParams['NOMEN_COUNT']
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
    $arImmutableFilters = array('ID', 'DIRECTION_ID','PRICE');
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
} else {
    $arNavParams = array(
        'nPageSize' => 0
    );
    $arNavigation = CDBResult::GetNavParams($arNavParams);
    $CGridOptions = new COrderGridOptions($arResult['GRID_ID']);
    $arNavParams['bShowAll'] = false;
    $arFilter=array();
    switch (strtolower($arParams['EXTERNAL_TYPE'])) {
        case 'direction':
            $arFilter=array('DIRECTION_ID'=>$arParams['EXTERNAL_ID']);
            break;
    }
    $arResult['FILTER']=array();
}

$dbRes=COrderNomen::GetListEx($arResult['SORT'],$arFilter);
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
        "PATH_TO_NOMEN_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_NOMEN_EDIT'],
            array('nomen_id' => $el["ID"])
        ),
        "PATH_TO_DIRECTION_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_DIRECTION_EDIT'],
            array('direction_id' => $el["ID"])
        ),
        "PATH_TO_NOMEN_DELETE" => $arParams['PATH_TO_NOMEN_LIST'].'?action_button_'.$arResult['GRID_ID'].'=delete&ID[]='.$el["ID"].'&sessid='.$arResult['SESSION_ID'],
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

$arResult["NOMEN"]=$arElems;


$arResult['AJAX_MODE']=$arParams['AJAX_MODE'];
$arResult['AJAX_ID']=$arParams['AJAX_ID'];
$arResult['AJAX_OPTION_JUMP']=$arParams['AJAX_OPTION_JUMP'];
$arResult['AJAX_OPTION_HISTORY']=$arParams['AJAX_OPTION_HISTORY'];
$arResult['PERMS']['ADD']    = false;
$arResult['PERMS']['EDIT']   = false;
$arResult['PERMS']['DELETE'] = false;
/*$arResult['PERMS']['ADD']    = !$COrderPerms->HavePerm('NOMEN', BX_ORDER_PERM_NONE, 'ADD');
$arResult['PERMS']['EDIT']   = !$COrderPerms->HavePerm('NOMEN', BX_ORDER_PERM_NONE, 'EDIT');
$arResult['PERMS']['DELETE'] = !$COrderPerms->HavePerm('NOMEN', BX_ORDER_PERM_NONE, 'DELETE');*/
$arResult['INTERNAL']=$bInternal;



$this->IncludeComponentTemplate();
?>