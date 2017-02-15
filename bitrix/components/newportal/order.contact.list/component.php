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
if ($COrderPerms->HavePerm('CONTACT', BX_ORDER_PERM_NONE, 'READ'))
{
    ShowError(GetMessage('ORDER_PERMISSION_DENIED'));
    return;
}


$userID = COrderHelper::GetCurrentUserID();
$isAdmin = COrderPerms::IsAdmin();


$arResult['CURRENT_USER_ID'] = $userID;
$arParams['PATH_TO_CONTACT_LIST'] = OrderCheckPath('PATH_TO_CONTACT_LIST', $arParams['PATH_TO_CONTACT_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_CONTACT_EDIT'] = OrderCheckPath('PATH_TO_CONTACT_EDIT', $arParams['PATH_TO_CONTACT_EDIT'], '/order/contact/edit/#contact_id#');
$arParams['PATH_TO_PHYSICAL_EDIT'] = OrderCheckPath('PATH_TO_PHYSICAL_EDIT', $arParams['PATH_TO_PHYSICAL_EDIT'], '/order/physical/edit/#physical_id#');
$arParams['PATH_TO_AGENT_EDIT'] = OrderCheckPath('PATH_TO_AGENT_EDIT', $arParams['PATH_TO_AGENT_EDIT'], '/order/agent/edit/#agent_id#');
$arParams['PATH_TO_NOMEN_EDIT'] = OrderCheckPath('PATH_TO_NOMEN_EDIT', $arParams['PATH_TO_NOMEN_EDIT'], '/order/nomen/edit/#nomen_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);

$arResult['IS_AJAX_CALL'] = isset($_REQUEST['bxajaxid']) || isset($_REQUEST['AJAX_CALL']);
$arResult['SESSION_ID'] = bitrix_sessid();
$arResult['GRID_ID']='ORDER_CONTACT_LIST_V12';

$COrderContact=new COrderContact(false);

CUtil::InitJSCore(array('ajax', 'tooltip'));


$arResult['FORM_ID'] = isset($arParams['FORM_ID']) ? $arParams['FORM_ID'] : '';
$arResult['TAB_ID'] = isset($arParams['TAB_ID']) ? $arParams['TAB_ID'] : '';
$bInternal=false;


if(check_bitrix_sessid()) {
    if(isset($_POST['save']) && $_POST['save'] ==GetMessage('ORDER_BUTTON_SAVE')) {

        foreach($_POST['FIELDS'] as $id => $fields) {
            if(!$COrderContact->Update($id,$fields))
                $arrError[]=$COrderContact->LAST_ERROR;
        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_CONTACT_LIST']
                )
            );
        }
    }
    if(isset($_POST['apply']) && $_POST['apply']==GetMessage('ORDER_BUTTON_APPLY') &&
        ($_POST['ACTION_DESCRIPTION']!='' || $_POST['ACTION_START_DATE']!=''
        || $_POST['ACTION_END_DATE']!='' || $_POST['ACTION_ASSIGNED_ID']!='')) {
        $prop=array();
        if($_POST['action_button_'.$arResult['GRID_ID']]=='set_description' && $_POST['ACTION_DESCRIPTION']!='') {
            $prop['DESCRIPTION'] = $_POST['ACTION_DESCRIPTION'];
        }
        if($_POST['action_button_'.$arResult['GRID_ID']]=='set_start_date' && $_POST['ACTION_START_DATE']!='') {
            $prop['START_DATE'] = $_POST['ACTION_START_DATE'];
        }
        if($_POST['action_button_'.$arResult['GRID_ID']]=='set_end_date' && $_POST['ACTION_END_DATE']!='') {
            $prop['END_DATE'] = $_POST['ACTION_END_DATE'];
        }
        if($_POST['action_button_'.$arResult['GRID_ID']]=='set_assigned_id' && $_POST['ACTION_ASSIGNED_ID']!='') {
            $prop['ASSIGNED_ID'] = $_POST['ACTION_ASSIGNED_ID'];
        }


        foreach($_POST['ID'] as $id) {
            if(!$COrderContact->Update($id,$prop))
                $arrError[]=$COrderContact->LAST_ERROR;

        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_CONTACT_LIST']
                )
            );
        }
    }
    if($_REQUEST['action_button_'.$arResult['GRID_ID']]=='delete') {
        foreach($_REQUEST['ID'] as $id) {
            if(!$COrderContact->Delete($id))
                $arrError[]=$COrderContact->LAST_ERROR;
        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_CONTACT_LIST']
                )
            );
        }
    }
}

ob_start();
$APPLICATION->IncludeComponent('newportal:order.entity.selector',
    '',
    array(
        'ENTITY_TYPE' => 'STAFF',
        'INPUT_NAME' => 'MODIFY_BY_ID',
        'INPUT_VALUE' => isset($_REQUEST['MODIFY_BY_ID']) ? intval($_REQUEST['MODIFY_BY_ID']) : '',
        'FORM_NAME' => $arResult['GRID_ID'],
        'MULTIPLE' => 'N',
        'FILTER' => true
    ),
    false,
    array('HIDE_ICONS' => 'Y')
);
$sValModifyBy = '<div style="padding-top:0.5em;">'.ob_get_contents().'</div>';
ob_end_clean();

ob_start();
$APPLICATION->IncludeComponent('newportal:order.entity.selector',
    '',
    array(
        'ENTITY_TYPE' => 'STAFF',
        'INPUT_NAME' => 'ASSIGNED_ID',
        'INPUT_VALUE' => isset($_REQUEST['ASSIGNED_ID']) ? intval($_REQUEST['ASSIGNED_ID']) : '',
        'FORM_NAME' => $arResult['GRID_ID'],
        'MULTIPLE' => 'N',
        'FILTER' => true
    ),
    false,
    array('HIDE_ICONS' => 'Y')
);
$sValAssigned = '<div style="padding-top:0.5em;">'.ob_get_contents().'</div>';
ob_end_clean();


$arFilter = $arSort = array();

$arResult['FILTER'] = array(
    array('id' => 'ID', 'name' => GetMessage('ORDER_COLUMN_ID')),
    array('id' => 'GUID', 'name' => GetMessage('ORDER_COLUMN_GUID')),
    array('id' => 'FULL_NAME', 'name' => GetMessage('ORDER_COLUMN_FULL_NAME')),
    array('id' => 'SHARED', 'name' => GetMessage('ORDER_COLUMN_SHARED'), 'type' => 'checkbox', 'default' => true),
    array('id' => 'ASSIGNED_ID', 'name' => GetMessage('ORDER_COLUMN_ASSIGNED_ID_LIST'), 'type' => 'custom','value'=>$sValAssigned),
    array('id' => 'START_DATE', 'name' => GetMessage('ORDER_COLUMN_START_DATE'), 'type' => 'date'),
    array('id' => 'END_DATE', 'name' => GetMessage('ORDER_COLUMN_END_DATE'), 'type' => 'date'),
    array('id' => 'EMAIL', 'name' => GetMessage('ORDER_COLUMN_EMAIL')),
    array('id' => 'PHONE', 'name' => GetMessage('ORDER_COLUMN_PHONE')),
    array('id' => 'DESCRIPTION', 'name' => GetMessage('ORDER_COLUMN_DESCRIPTION')),
    array('id' => 'MODIFY_DATE', 'name' => GetMessage('ORDER_COLUMN_MODIFY_DATE'), 'type' => 'date'),
    array('id' => 'MODIFY_BY_ID',  'name' => GetMessage('ORDER_COLUMN_MODIFY_BY_ID'), 'type' => 'custom', 'value' => $sValModifyBy),
);

$arResult['HEADERS']=array(
    array('id' => 'ID', 'name' => GetMessage('ORDER_COLUMN_ID'), 'default' => true, 'sort' => 'ID', 'editable' => false),
    array('id' => 'GUID', 'name' => GetMessage('ORDER_COLUMN_GUID'), 'default' => true, 'sort' => 'GUID', 'editable' => false),
    array('id' => 'SHARED', 'name' => GetMessage('ORDER_COLUMN_SHARED'), 'default' => true, 'sort' => 'SHARED', 'editable' => false,'type'=>'checkbox'),
    array('id' => 'FULL_NAME', 'name' => GetMessage('ORDER_COLUMN_FULL_NAME'), 'default' => true, 'sort' => 'FULL_NAME', 'editable' => false),
    array('id' => 'AGENT_ID', 'name' => GetMessage('ORDER_COLUMN_AGENT_ID'), 'default' => true, 'sort' => 'AGENT_TITLE', 'editable' => false),
    array('id' => 'START_DATE', 'name' => GetMessage('ORDER_COLUMN_START_DATE'), 'default' => true, 'sort' => 'START_DATE', 'editable' => true,'type'=>'date'),
    array('id' => 'END_DATE', 'name' => GetMessage('ORDER_COLUMN_END_DATE'), 'default' => true, 'sort' => 'END_DATE', 'editable' => true,'type'=>'date'),
    array('id' => 'EMAIL', 'name' => GetMessage('ORDER_COLUMN_EMAIL'), 'default' => true, 'sort' => 'EMAIL', 'editable' => false),
    array('id' => 'PHONE', 'name' => GetMessage('ORDER_COLUMN_PHONE'), 'default' => true, 'sort' => 'PHONE', 'editable' => false),
    array('id' => 'DESCRIPTION', 'name' => GetMessage('ORDER_COLUMN_DESCRIPTION'), 'default' => true, 'sort' => 'DESCRIPTION', 'editable' => false,'type'=>'textarea'),
    array('id' => 'ASSIGNED_ID', 'name' => GetMessage('ORDER_COLUMN_ASSIGNED_ID'), 'default' => true, 'sort' => 'ASSIGNED_FULL_NAME', 'editable' => false),
    array('id' => 'MODIFY_DATE', 'name' => GetMessage('ORDER_COLUMN_MODIFY_DATE'), 'default' => true, 'sort' => 'MODIFY_DATE', 'editable' => false, 'type' => 'date'),
    array('id' => 'MODIFY_BY_ID', 'name' => GetMessage('ORDER_COLUMN_MODIFY_BY_ID'), 'default' => true, 'sort' => 'MODIFY_BY_FULL_NAME', 'editable' => false),
);


$arResult['SORT_VARS']=array('by'=>'by','order'=>'order');
if(isset($_REQUEST['by']) && isset($_REQUEST['order'])) {
    $arResult['SORT']=array($_REQUEST['by']=>$_REQUEST['order']);
}
else {
    $arResult['SORT'] = array('FULL_NAME' => 'asc');
}




if (intval($arParams['CONTACT_COUNT']) <= 0)
    $arParams['CONTACT_COUNT'] = 20;

$arNavParams = array(
    'nPageSize' => $arParams['CONTACT_COUNT']
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
$arImmutableFilters = array('ID', 'ASSIGNED_ID', 'MODIFY_BY_ID');
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

$dbRes=COrderContact::GetListEx($arResult['SORT'],$arFilter);
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
        "PATH_TO_CONTACT_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_CONTACT_EDIT'],
            array('contact_id' => $el["ID"])
        ),
        "PATH_TO_AGENT_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_AGENT_EDIT'],
            array('agent_id' => $el["AGENT_ID"])
        ),
        "PATH_TO_PHYSICAL_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_PHYSICAL_EDIT'],
            array('physical_id' => $el["GUID"])
        ),
        "PATH_TO_ASSIGNED" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_STAFF_EDIT'],
            array('staff_id' => $el["ASSIGNED_ID"])
        ),
        "PATH_TO_CONTACT_DELETE" => $arParams['PATH_TO_CONTACT_LIST'].'?action_button_'.$arResult['GRID_ID'].'=delete&ID[]='.$el["ID"].'&sessid='.$arResult['SESSION_ID'],
        "PATH_TO_USER_MODIFIER" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_STAFF_EDIT'],
            array('staff_id' => $el["MODIFY_BY_ID"])
        ),
        "PATH_TO_STAFF_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_STAFF_EDIT'],
            array('staff_id' => $userID)
        ),
    ));
    $arElems[$el['ID']]=$el;
}

$arResult["CONTACT"]=$arElems;


$arResult['AJAX_MODE']=$arParams['AJAX_MODE'];
$arResult['AJAX_ID']=$arParams['AJAX_ID'];
$arResult['AJAX_OPTION_JUMP']=$arParams['AJAX_OPTION_JUMP'];
$arResult['AJAX_OPTION_HISTORY']=$arParams['AJAX_OPTION_HISTORY'];
$arResult['PERMS']['ADD']    = !$COrderPerms->HavePerm('CONTACT', BX_ORDER_PERM_NONE, 'ADD');
$arResult['PERMS']['EDIT']   = !$COrderPerms->HavePerm('CONTACT', BX_ORDER_PERM_NONE, 'EDIT');
$arResult['PERMS']['DELETE'] = !$COrderPerms->HavePerm('CONTACT', BX_ORDER_PERM_NONE, 'DELETE');






$this->IncludeComponentTemplate();
?>