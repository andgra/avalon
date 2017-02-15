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
//$COrderPerms = COrderPerms::GetUserPermissions(32750);
if ($COrderPerms->HavePerm('REG', BX_ORDER_PERM_NONE, 'READ'))
{
    ShowError(GetMessage('ORDER_PERMISSION_DENIED'));
    return;
}


$arResult['STATUS_LIST_WRITE']=COrderHelper::GetEnumList('REG',"STATUS");




$userID = COrderHelper::GetCurrentUserID();
$isAdmin = COrderPerms::IsAdmin();


$arResult['CURRENT_USER_ID'] = $userID;
$arParams['PATH_TO_REG_LIST'] = OrderCheckPath('PATH_TO_REG_LIST', $arParams['PATH_TO_REG_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_REG_EDIT'] = OrderCheckPath('PATH_TO_REG_EDIT', $arParams['PATH_TO_REG_EDIT'], '/order/reg/edit/#reg_id#');
$arParams['PATH_TO_APP_EDIT'] = OrderCheckPath('PATH_TO_APP_EDIT', $arParams['PATH_TO_APP_EDIT'], '/order/app/edit/#app_id#');
$arParams['PATH_TO_PHYSICAL_EDIT'] = OrderCheckPath('PATH_TO_PHYSICAL_EDIT', $arParams['PATH_TO_PHYSICAL_EDIT'], '/order/physical/edit/#physical_id#');
$arParams['PATH_TO_DIRECTION_EDIT'] = OrderCheckPath('PATH_TO_DIRECTION_EDIT', $arParams['PATH_TO_DIRECTION_EDIT'], '/order/direction/edit/#direction_id#');
$arParams['PATH_TO_NOMEN_EDIT'] = OrderCheckPath('PATH_TO_NOMEN_EDIT', $arParams['PATH_TO_NOMEN_EDIT'], '/order/nomen/edit/#nomen_id#');
$arParams['PATH_TO_COURSE_EDIT'] = OrderCheckPath('PATH_TO_COURSE_EDIT', $arParams['PATH_TO_COURSE_EDIT'], '/order/course/edit/#course_id#');
$arParams['PATH_TO_GROUP_EDIT'] = OrderCheckPath('PATH_TO_GROUP_EDIT', $arParams['PATH_TO_GROUP_EDIT'], '/order/group/edit/#group_id#');
$arParams['PATH_TO_FORMED_GROUP_EDIT'] = OrderCheckPath('PATH_TO_FORMED_GROUP_EDIT', $arParams['PATH_TO_FORMED_GROUP_EDIT'], '/order/formed_group/edit/#formed_group_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);

$arResult['IS_AJAX_CALL'] = isset($_REQUEST['bxajaxid']) || isset($_REQUEST['AJAX_CALL']);
$bInternal=(isset($arParams['EXTERNAL_ID']) && isset($arParams['FORM_ID']));
$arResult['SESSION_ID'] = bitrix_sessid();
if($bInternal) {
    $arResult['EXTERNAL_TYPE']=$arParams['EXTERNAL_TYPE'];
    $arResult['EXTERNAL_EDIT']=$arParams['EDIT'];
    $arResult['GRID_ID']=$arParams['GRID_ID'];
} else {
    $arResult['GRID_ID']='ORDER_REG_LIST_V12';
}
$COrderReg=new COrderReg(false);

CUtil::InitJSCore(array('ajax', 'tooltip'));


$arResult['FORM_ID'] = isset($arParams['FORM_ID']) ? $arParams['FORM_ID'] : '';
$arResult['TAB_ID'] = isset($arParams['TAB_ID']) ? $arParams['TAB_ID'] : '';
if(check_bitrix_sessid() && !$bInternal) {
    if(isset($_POST['save']) && $_POST['save'] ==GetMessage('ORDER_BUTTON_SAVE')) {

        foreach($_POST['FIELDS'] as $id => $fields) {
            if(!$COrderReg->Update($id,$fields))
                $arrError[]=$COrderReg->LAST_ERROR;
        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_REG_LIST']
                )
            );
        }
    }
    if(isset($_POST['apply']) && $_POST['apply']==GetMessage('ORDER_BUTTON_APPLY') &&
        ($_POST['ACTION_STATUS_ID']!=''  || $_POST['ACTION_PERIOD']!=''
            || $_POST['ACTION_DESCRIPTION']!='' || $_POST['ACTION_ENTITY_ID']!='')) {
        $prop=array();
        if($_POST['action_button_'.$arResult['GRID_ID']]=='set_status' && $_POST['ACTION_STATUS_ID']!='') {
            $prop['STATUS'] = $_POST['ACTION_STATUS_ID'];
        }

        if($_POST['action_button_'.$arResult['GRID_ID']]=='set_period' && $_POST['ACTION_PERIOD']!='') {
            $prop['PERIOD'] = $_POST['ACTION_PERIOD'];
        }

        if($_POST['action_button_'.$arResult['GRID_ID']]=='set_description' && $_POST['ACTION_DESCRIPTION']!='') {
            $prop['DESCRIPTION'] = $_POST['ACTION_DESCRIPTION'];
        }
        if($_POST['action_button_'.$arResult['GRID_ID']]=='set_entity' && $_POST['ACTION_ENTITY_ID']!='') {
            $prop['ENTITY_ID'] = $_POST['ACTION_ENTITY_ID'];
        }
        foreach($_POST['ID'] as $id) {
            if(!$COrderReg->Update($id,$prop))
                $arrError[]=$COrderReg->LAST_ERROR;
        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_REG_LIST']
                )
            );
        }
    }
    if($_REQUEST['action_button_'.$arResult['GRID_ID']]=='delete') {
        foreach($_REQUEST['ID'] as $id) {
            if(!$COrderReg->Delete($id))
                $arrError[]=$COrderReg->LAST_ERROR;
        }

        if(!empty($arrError)) {
            foreach($arrError as $err)
                ShowError($err);
        } else {
            LocalRedirect(
                CComponentEngine::MakePathFromTemplate(
                    $arParams['PATH_TO_REG_LIST']
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
            'ENTITY_TYPE' => array('DIRECTION', 'NOMEN', 'GROUP', 'FORMED_GROUP'),
            'INPUT_NAME' => 'ENTITY_ID',
            'INPUT_VALUE' => isset($_REQUEST['ENTITY_ID']) ? intval($_REQUEST['ENTITY_ID']) : '',
            'FORM_NAME' => $arResult['GRID_ID'],
            'MULTIPLE' => 'N',
            'FILTER' => true
        ),
        false,
        array('HIDE_ICONS' => 'Y')
    );
    $sValEntity = '<div style="padding-top:0.5em;">' . ob_get_contents() . '</div>';
    ob_end_clean();


    ob_start();
    $APPLICATION->IncludeComponent('newportal:order.entity.selector',
        '',
        array(
            'ENTITY_TYPE' => 'USER',
            'INPUT_NAME' => 'MODIFY_BY_ID',
            'INPUT_VALUE' => isset($_REQUEST['MODIFY_BY_ID']) ? intval($_REQUEST['MODIFY_BY_ID']) : '',
            'FORM_NAME' => $arResult['GRID_ID'],
            'MULTIPLE' => 'N',
            'FILTER' => true
        ),
        false,
        array('HIDE_ICONS' => 'Y')
    );
    $sValModifyBy = '<div style="padding-top:0.5em;">' . ob_get_contents() . '</div>';
    ob_end_clean();

    $arFilter = $arSort = array();

    $arResult['FILTER'] = array(
        array('id' => 'ID', 'name' => GetMessage('ORDER_COLUMN_ID')),
        array('id' => 'APP_ID', 'name' => GetMessage('ORDER_COLUMN_APP_ID')),
        array('id' => 'PHYSICAL_FULL_NAME', 'name' => GetMessage('ORDER_COLUMN_PHYSICAL'), 'default' => true),
        array('id' => 'ENTITY_ID', 'name' => GetMessage('ORDER_COLUMN_ENTITY_LIST'), 'type' => 'custom', 'value' => $sValEntity),
        array('id' => 'ENTITY_TITLE', 'name' => GetMessage('ORDER_COLUMN_ENTITY')),
        array('id' => 'PAST', 'name' => GetMessage('ORDER_COLUMN_PAST'), 'default' => true, 'type' => 'checkbox'),
        array('id' => 'STATUS', 'params' => array('multiple' => 'Y'), 'name' => GetMessage('ORDER_COLUMN_STATUS'), 'default' => true, 'type' => 'list', 'items' => $arResult['STATUS_LIST_WRITE'], 'default' => true),
        array('id' => 'PERIOD', 'name' => GetMessage('ORDER_COLUMN_PERIOD'), 'type' => 'date'),
        array('id' => 'DESCRIPTION', 'name' => GetMessage('ORDER_COLUMN_DESCRIPTION')),
        array('id' => 'ASSIGNED_FULL_NAME', 'name' => GetMessage('ORDER_COLUMN_ASSIGNED'), 'default' => true),
        array('id' => 'MODIFY_DATE', 'name' => GetMessage('ORDER_COLUMN_MODIFY_DATE'), 'type' => 'date'),
        array('id' => 'MODIFY_BY_ID', 'name' => GetMessage('ORDER_COLUMN_MODIFY_BY_ID'), 'type' => 'custom', 'value' => $sValModifyBy),
        array('id' => 'SHARED', 'name' => GetMessage('ORDER_COLUMN_SHARED'), 'type' => 'checkbox'),
    );
}


$defaultPhysical = COrderEntitySelectorHelper::GetPersonSelector(
    'PHYSICAL_ID_NEW_%ID%',
    'physical',
    array()
);


$arResult['HEADERS'] = array(
    array('id' => 'ID', 'name' => GetMessage('ORDER_COLUMN_ID'), 'default' => true, 'sort' => 'ID', 'editable' => false),
    array('id' => 'PHYSICAL_ID', 'name' => GetMessage('ORDER_COLUMN_PHYSICAL'), 'default' => true, 'sort' => 'PHYSICAL_FULL_NAME', 'editable' => array('default_value'=>$defaultPhysical),'type'=>'person_selector'),
    array('id' => 'PAST', 'name' => GetMessage('ORDER_COLUMN_PAST'), 'default' => true, 'sort' => 'PAST', 'editable' => true, 'type' => 'checkbox'),
    array('id' => 'STATUS', 'name' => GetMessage('ORDER_COLUMN_STATUS'), 'default' => true, 'sort' => 'STATUS_TITLE', 'editable' => array('items' => $arResult['STATUS_LIST_WRITE'],'default_value'=>'NEW'), 'type' => 'list'),
    array('id' => 'PERIOD', 'name' => GetMessage('ORDER_COLUMN_PERIOD'), 'default' => true, 'sort' => 'PERIOD', 'editable' => array('default_value'=>date('d.m.Y', mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")))), 'type' => 'date'),
    array('id' => 'DESCRIPTION', 'name' => GetMessage('ORDER_COLUMN_DESCRIPTION'), 'default' => true, 'sort' => 'DESCRIPTION', 'editable' => true,'type'=>'textarea'),
    array('id' => 'MODIFY_DATE', 'name' => GetMessage('ORDER_COLUMN_MODIFY_DATE'), 'default' => true, 'sort' => 'MODIFY_DATE', 'editable' => false, 'type' => 'date'),
    array('id' => 'MODIFY_BY_ID', 'name' => GetMessage('ORDER_COLUMN_MODIFY_BY_ID'), 'default' => true, 'sort' => 'MODIFY_BY_FULL_NAME', 'editable' => false),
);

if(!$bInternal || strtoupper($arResult['EXTERNAL_TYPE'])!='APP') {
    $defaultApp=COrderEntitySelectorHelper::GetSelector(
        'APP',
        array(
            'INPUT_NAME'=>'APP_ID_NEW_%ID%',
            'FORM_ID'=>$arResult['GRID_ID'],
            'FILTER'=>array('!STATUS'=>array('CONVERTED','DENIED'))
        )
    );

    $arResult['HEADERS']=array_merge($arResult['HEADERS'],array(
        array('id' => 'APP_ID', 'name' => GetMessage('ORDER_COLUMN_APP_ID'), 'default' => true, 'sort' => 'APP_ID', 'editable' => array('default_value'=>array('ADD_ONLY'=>'Y','VALUE'=>$defaultApp)),'type'=>'custom'),
        array('id' => 'ASSIGNED_ID', 'name' => GetMessage('ORDER_COLUMN_ASSIGNED'), 'default' => true, 'sort' => 'ASSIGNED_FULL_NAME', 'editable' => false),
    ));
}
if(!$bInternal || strtoupper($arResult['EXTERNAL_TYPE'])!='FORMED_GROUP') {
    $defaultEntity=COrderEntitySelectorHelper::GetStructureSelector(
        'ENTITY_ID_NEW_%ID%',
        array()
    );

    $arResult['HEADERS']=array_merge($arResult['HEADERS'],array(
        array('id' => 'ENTITY_ID', 'name' => GetMessage('ORDER_COLUMN_ENTITY'), 'default' => true, 'sort' => 'ENTITY_TITLE', 'editable' => array('default_value'=>$defaultEntity),'type'=>'structure_selector'),
    ));
}
if(!$bInternal) {
    $arResult['HEADERS']=array_merge($arResult['HEADERS'],array(
        array('id' => 'SHARED', 'name' => GetMessage('ORDER_COLUMN_SHARED'), 'default' => true, 'sort' => 'ID', 'editable' => false, 'type' => 'checkbox'),
    ));
}

$arResult['SORT_VARS']=array('by'=>'by','order'=>'order');
if(isset($_REQUEST['by']) && isset($_REQUEST['order'])) {
    $arResult['SORT']=array($_REQUEST['by']=>$_REQUEST['order']);
}
else {
    $arResult['SORT'] = array('MODIFY_DATE' => 'desc');
}


$arSelect=array();
if(!$bInternal) {
    if (intval($arParams['REG_COUNT']) <= 0)
        $arParams['REG_COUNT'] = 20;

    $arNavParams = array(
        'nPageSize' => $arParams['REG_COUNT']
    );

    $arNavigation = CDBResult::GetNavParams($arNavParams);
    $CGridOptions = new COrderGridOptions($arResult['GRID_ID']);
    $arNavParams = $CGridOptions->GetNavParams($arNavParams);
    $arNavParams['bShowAll'] = false;



    $arFilter = array();
    $arFilter += $CGridOptions->GetFilter($arResult['FILTER']);

    if($arFilter['GRID_FILTER_APPLIED']==false)
        $arFilter=array();

    if(isset($arFilter['PERIOD_datesel']) && $arFilter['PERIOD_datesel'] === 'days' && isset($arFilter['PERIOD_from']))
    {
        $arFilter['PERIOD_to'] = ConvertTimeStamp(strtotime(date("Y-m-d", time())));
    }


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
    $arImmutableFilters = array('ID', 'APP_ID', 'ENTITY_ID', 'MODIFY_BY_ID','SHARED');
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
        case 'app':
            $arFilter=array('APP_ID'=>$arParams['EXTERNAL_ID']);
            break;
        case 'formed_group':
            $arFilter=array(
                'ENTITY_TYPE'=>'formed_group',
                'ENTITY_ID'=>$arParams['EXTERNAL_ID']
            );
            break;
    }
    $arResult['FILTER']=array();
}

$dbRes=COrderReg::GetListEx($arResult['SORT'],$arFilter,false,false,$arSelect);

$dbRes->NavStart($arNavParams['nPageSize'],$arNavParams['bShowAll']);
$arResult['NAV_OBJECT']=$dbRes;
$arResult['ROWS_COUNT']=$dbRes->SelectedRowsCount();
$arResult['PAGE_SIZE']=$arNavParams['nPageSize'];
$arResult['PAGE_NUM']=$dbRes->NavPageNomer;

$tree=COrderDirection::GetTree();
$bEdit=$bDelete=false;
$CAccess = new CAccess();
$arResult['PROVIDER_NAMES']=$CAccess->GetProviderNames();
while($el=$dbRes->Fetch()) {
    $pDir=COrderReg::GetDirection($el,$tree);


    foreach($el as $code=>$val) {
        $arTilt['~'.$code]=$val;
        if(isset($dbRes->arUserFields[$code]) && $dbRes->arUserFields[$code]['MULTIPLE']=='Y') {
            $arTilt[$code]=unserialize(htmlspecialcharsback($val));
        }
    }
    $el=array_merge($el,$arTilt);
    $bAssigned=(isset($el['ASSIGNED_ID']) && $el['ASSIGNED_ID']!='')?in_array($el['ASSIGNED_ID'],CAccess::GetUserCodesArray($USER->GetID())):false;
    if($bAssigned) {
        $bEdit=$bDelete=true;
    }
    if($el['PHYSICAL_ID']=='') {
        $el['~PHYSICAL_ID'] = COrderEntitySelectorHelper::GetPersonSelector(
            'PHYSICAL_ID_' . $el['ID'],
            'physical',
            array(
                'FULL_NAME'=>$el['PHYSICAL_FULL_NAME'],
            )
        );
    } else {
        $el['~PHYSICAL_ID'] = COrderEntitySelectorHelper::GetPersonSelector(
            'PHYSICAL_ID_' . $el['ID'],
            'physical',
            array(
                'ID'=>$el['PHYSICAL_ID'],
                'FULL_NAME'=>$el['PHYSICAL_FULL_NAME'],
            )
        );
    }
    $el['~ENTITY_ID']=COrderEntitySelectorHelper::GetStructureSelector(
        'ENTITY_ID_'.$el['ID'],
        isset($el['ENTITY_ID']) ? array(
            'TYPE'=>strtolower($el['ENTITY_TYPE']),
            'VALUE'=>$el['ENTITY_ID'],
            'TITLE'=>$el['ENTITY_TITLE'],
        ):array()
    );

    $el['PATH_TO_APP_EDIT']=CComponentEngine::MakePathFromTemplate(
        $arParams['PATH_TO_APP_EDIT'],
        array('app_id' => $el["APP_ID"])
    );
    $el=array_merge($el,array(
        "PATH_TO_REG_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_REG_EDIT'],
            array('reg_id' => htmlspecialchars($el["ID"]))
        ),
        "PATH_TO_REG_DELETE" => ($bInternal?$el['PATH_TO_APP_EDIT']:$arParams['PATH_TO_REG_LIST']).'?action_button_'.$arResult['GRID_ID'].'=delete&ID[]='.$el["ID"].'&sessid='.$arResult['SESSION_ID'],
        "PATH_TO_PHYSICAL_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_PHYSICAL_EDIT'],
            array('physical_id' => $el["PHYSICAL_ID"])
        ),
        "PATH_TO_ENTITY_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_'.strtoupper($el["ENTITY_TYPE"]).'_EDIT'],
            array(strtolower($el["ENTITY_TYPE"]).'_id' => htmlspecialchars($el["ENTITY_ID"]))
        ),
        "PATH_TO_STAFF_EDIT" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_STAFF_EDIT'],
            array('staff_id' => $userID)
        ),
        "PATH_TO_USER_MODIFIER" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_STAFF_EDIT'],
            array('staff_id' => $el["MODIFY_BY_ID"])
        ),
        "PATH_TO_ASSIGNED" => CComponentEngine::MakePathFromTemplate(
            $arParams['PATH_TO_STAFF_EDIT'],
            array('staff_id' => $el["ASSIGNED_ID"])
        ),
        "ENTITY_TYPE_NAME" => GetMessage(strtoupper($el['ENTITY_TYPE']).'_TYPE_NAME'),
        //"PERM_ADD"=>$COrderPerms->CheckEnityAccess('REG', 'ADD',array('STATUS'.$el['STATUS'],$pDir)),
        "PERM_EDIT"=>$bAssigned || $COrderPerms->CheckEnityAccess('REG', 'EDIT',array('STATUS'.$el['STATUS'],$pDir)),
        "PERM_DELETE"=>$bAssigned || $COrderPerms->CheckEnityAccess('REG', 'DELETE',array('STATUS'.$el['STATUS'],$pDir)),
        "PERM_FILTER"=>array('STATUS'.$el['STATUS'],$pDir),
    ));
    $arAssigned[]=$el['ASSIGNED_ID'];
    $arElems[$el['ID']]=$el;
}
$arNames = $CAccess->GetNames($arAssigned);
foreach($arElems as $id=>$el) {
    $arElems[$id]['ASSIGNED_TITLE']='<b>'.htmlspecialcharsbx(COrderHelper::GetProviderName($arNames[$el['ASSIGNED_ID']]['provider_id'],$el['ASSIGNED_ID'],$arResult['PROVIDER_NAMES'])).'</b>: '.htmlspecialcharsbx($arNames[$el['ASSIGNED_ID']]['name']);
}
$arResult["REG"]=$arElems;


$arResult['AJAX_MODE']=$arParams['AJAX_MODE'];
$arResult['AJAX_ID']=$arParams['AJAX_ID'];
$arResult['AJAX_OPTION_JUMP']=$arParams['AJAX_OPTION_JUMP'];
$arResult['AJAX_OPTION_HISTORY']=$arParams['AJAX_OPTION_HISTORY'];
$arResult['PERMS']['ADD']    = !$COrderPerms->HavePerm('REG', BX_ORDER_PERM_NONE, 'ADD');
$arResult['PERMS']['EDIT']   = (!$COrderPerms->HavePerm('REG', BX_ORDER_PERM_NONE, 'EDIT') || $bEdit)
    && (!isset($arResult['EXTERNAL_EDIT']) || $arResult['EXTERNAL_EDIT']==true);
$arResult['PERMS']['DELETE'] = (!$COrderPerms->HavePerm('REG', BX_ORDER_PERM_NONE, 'DELETE') || $bDelete)
    && (!isset($arResult['EXTERNAL_EDIT']) || $arResult['EXTERNAL_EDIT']==true);
$arResult['INTERNAL']=$bInternal;





$this->IncludeComponentTemplate();
?>