<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule('order')) {
    ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
    return;
}

CJSCore::Init(array('access', 'window','jquery'));
//CModule::IncludeModule('fileman');
if (!function_exists('ProcessRegs')) {
    function ProcessRegs($appID,$gridID)
    {
        if (isset($_REQUEST['FIELDS']) && is_array($_REQUEST['FIELDS'])) {
            $COrderReg = new COrderReg();
            foreach ($_REQUEST['FIELDS'] as $regID => $regFields) {
                if (isset($_REQUEST['ORDER_PERSON_PHYSICAL_ID_' . $regID . '_VALUE'])) {
                    $regFields['PHYSICAL_ID'] = $_REQUEST['ORDER_PERSON_PHYSICAL_ID_' . $regID . '_VALUE'];
                    $regFields['ENTITY_ID'] = $_REQUEST['ORDER_STRUCTURE_ENTITY_ID_' . $regID . '_VALUE'];
                    $regFields['ENTITY_TYPE'] = strtolower($_REQUEST['ORDER_STRUCTURE_ENTITY_ID_' . $regID . '_TYPE']);
                    if (isset($_REQUEST[$regID . '_FLAG']) && $_REQUEST[$regID . '_FLAG'] == 'NEW') {
                        $regFields['ID'] = COrderHelper::GetNewID();
                        $regFields['SHARED'] = 'N';
                        $regFields['APP_ID'] = $appID;
                        if (!$COrderReg->Add($regFields)) {
                            ShowError($COrderReg->LAST_ERROR);
                            return false;
                        }
                    } else {
                        if (!$COrderReg->Update($regID, $regFields)) {
                            ShowError($COrderReg->LAST_ERROR);
                            return false;
                        }
                    }

                }
            }
        } elseif ($_REQUEST['action_button_' . $gridID] == 'delete') {
            $COrderReg = new COrderReg();
            foreach ($_REQUEST['ID'] as $regID) {
                if (!$COrderReg->Delete($regID)) {
                    ShowError($COrderReg->LAST_ERROR);
                    return false;
                }
            }
        }
        return true;
    }
}


global $USER, $DB;

$userID = COrderHelper::GetCurrentUserID();

$COrderPerms = COrderPerms::GetCurrentUserPermissions();
//$COrderPerms = COrderPerms::GetUserPermissions(32750);

$arResult['CURRENT_USER_ID'] = $userID;
$arParams['PATH_TO_APP_LIST'] = OrderCheckPath('PATH_TO_APP_LIST', $arParams['PATH_TO_APP_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_APP_EDIT'] = OrderCheckPath('PATH_TO_APP_EDIT', $arParams['PATH_TO_APP_EDIT'], '/order/app/edit/#app_id#');
$arParams['PATH_TO_REG_EDIT'] = OrderCheckPath('PATH_TO_REG_EDIT', $arParams['PATH_TO_REG_EDIT'], '/order/reg/edit/#reg_id#');
$arParams['PATH_TO_CONTACT_EDIT'] = OrderCheckPath('PATH_TO_CONTACT_EDIT', $arParams['PATH_TO_CONTACT_EDIT'], '/order/contact/edit/#contact_id#');
$arParams['PATH_TO_AGENT_EDIT'] = OrderCheckPath('PATH_TO_AGENT_EDIT', $arParams['PATH_TO_AGENT_EDIT'], '/order/agent/edit/#agent_id#');
$arParams['PATH_TO_GROUP_EDIT'] = OrderCheckPath('PATH_TO_GROUP_EDIT', $arParams['PATH_TO_GROUP_EDIT'], '/order/group/edit/#group_id#');
$arParams['PATH_TO_DIRECTION_EDIT'] = OrderCheckPath('PATH_TO_DIRECTION_EDIT', $arParams['PATH_TO_DIRECTION_EDIT'], '/order/direction/edit/#direction_id#');
$arParams['PATH_TO_NOMEN_EDIT'] = OrderCheckPath('PATH_TO_NOMEN_EDIT', $arParams['PATH_TO_NOMEN_EDIT'], '/order/nomen/edit/#nomen_id#');
$arParams['PATH_TO_COURSE_EDIT'] = OrderCheckPath('PATH_TO_COURSE_EDIT', $arParams['PATH_TO_COURSE_EDIT'], '/order/course/edit/#course_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#", "#/NOBR#"), array("", ""), $arParams["NAME_TEMPLATE"]);
$arParams['ELEMENT_ID'] = isset($arParams['ELEMENT_ID']) ? (int)$arParams['ELEMENT_ID'] : 0;

$bRead = $bDelete = $bEdit = false;
$bVarsFromForm = false;

$COrderApp = new COrderApp();
$COrderReg = new COrderReg(false);

if (!empty($arParams['ELEMENT_ID'])) {
    $arEntityAttr = $COrderPerms->GetEntityAttr('APP', array($arParams['ELEMENT_ID']));
    $bEdit = true;
}
$tree = COrderDirection::GetTree();

$arFields = null;
if ($bEdit) {
    $arFilter = array(
        'ID' => $arParams['ELEMENT_ID'],
        //'PERMISSION' => 'WRITE'
    );
    $res = COrderApp::GetListEx(array(), $arFilter);
    $arFields = $res->Fetch();
    if ($arFields === false) {
        $bEdit = false;
    } else    $pDir = COrderApp::GetDirections($arFields);
} else {
    $arFields = array(
        'ID' => 0
    );

    if (isset($_GET['physical_id'])) {
        $arFields['PHYSICAL_ID'] = intval($_GET['physical_id']);
    }
}


if ($bEdit) {
    $bAssigned = (isset($arFields['ASSIGNED_ID']) && $arFields['ASSIGNED_ID'] != '') ? in_array($arFields['ASSIGNED_ID'], CAccess::GetUserCodesArray($USER->GetID())) : false;
    $arEntityAttrTemp = is_array($arEntityAttr[$arParams['ELEMENT_ID']]) ?
        array_merge($arEntityAttr[$arParams['ELEMENT_ID']], array('STATUS' . $arFields['STATUS']), $pDir) :
        array_merge(array('STATUS' . $arFields['STATUS']), $pDir);
    $arResult['PERM_EDIT'] = $bEdit = ($bAssigned || $COrderPerms->CheckEnityAccess('APP', 'EDIT', $arEntityAttrTemp)) && $arFields['STATUS']!='CONVERTED';
    $arResult['PERM_READ'] = $bRead = $bAssigned || $COrderPerms->CheckEnityAccess('APP', 'READ', $arEntityAttrTemp);
    $arResult['PERM_DELETE'] = $bDelete = ($bAssigned || $COrderPerms->CheckEnityAccess('APP', 'DELETE', $arEntityAttrTemp)) && $arFields['STATUS']!='CONVERTED';
} else {
    $bAdd = !$COrderPerms->HavePerm('APP', BX_ORDER_PERM_NONE, 'ADD') && empty($arParams['ELEMENT_ID']);
}
$isPermitted = $bEdit || $bRead || $bAdd;
$onlyRead = $bRead && !$bEdit;

if (!$isPermitted) {
    ShowError(GetMessage('ORDER_PERMISSION_DENIED'));
    return;
}


$bInternal = false;
if (isset($arParams['INTERNAL_FILTER']) && !empty($arParams['INTERNAL_FILTER']))
    $bInternal = true;
$arResult['INTERNAL'] = $bInternal;

$arResult['TAX_MODE'] = 'N';


$isExternal = ($bEdit || $bRead) && isset($arFields['ORIGINATOR_ID']) && isset($arFields['ORIGIN_ID']) && intval($arFields['ORIGINATOR_ID']) > 0 && intval($arFields['ORIGIN_ID']) > 0;

$arResult['ELEMENT'] = is_array($arFields) ? $arFields : null;
unset($arFields);


$arResult['FORM_ID'] = !empty($arParams['FORM_ID']) ? $arParams['FORM_ID'] : 'ORDER_APP_EDIT_V12';
$arResult['GRID_ID'] = 'ORDER_APP_EDIT_V12';
$arResult['REG_FORM_ID'] = $arResult['FORM_ID'].'_REG_LIST';
$arResult['REG_GRID_ID'] = $arResult['GRID_ID'].'_REG_LIST';


do {
    if (check_bitrix_sessid()) {
        $bVarsFromForm = true;
        $DB->StartTransaction();
        if (isset($_POST['save']) || isset($_POST['saveAndView']) || isset($_POST['saveAndAdd']) || isset($_POST['apply'])
            || $_REQUEST['action_button_' . $arResult['REG_GRID_ID']] == 'delete') {
            $arSrcElement = ($bEdit || $bRead) ? $arResult['ELEMENT'] : array();


            if (isset($_POST['DESCRIPTION'])) {
                $comments = isset($_POST['DESCRIPTION']) ? trim($_POST['DESCRIPTION']) : '';
                if ($comments !== '' && strpos($comments, '<') !== false) {
                    $sanitizer = new CBXSanitizer();
                    $sanitizer->ApplyDoubleEncode(false);
                    $sanitizer->SetLevel(CBXSanitizer::SECURE_LEVEL_MIDDLE);
                    //Crutch for for Chrome line break behaviour in HTML editor.
                    $sanitizer->AddTags(array('div' => array()));
                    $comments = $sanitizer->SanitizeHtml($comments);
                }
                $arFields['DESCRIPTION'] = $comments;
            }

            if (isset($_POST['ORDER_PERSON_AGENT_ID_VALUE'])) {
                $arFields['AGENT_ID'] = $_POST['ORDER_PERSON_AGENT_ID_VALUE'];
            } elseif (isset($arSrcElement['AGENT_ID'])) {
                $arFields['AGENT_ID'] = $arSrcElement['AGENT_ID'];
            }

            $arFields['SOURCE']='';

            if (isset($_POST['SOURCE'])) {
                $arFields['SOURCE'] = $_POST['SOURCE'];
            } elseif (isset($arSrcElement['SOURCE'])) {
                $arFields['SOURCE'] = $arSrcElement['SOURCE'];
            }

            if ($arFields['SOURCE']=='OTHER' && isset($_POST['SOURCE_TEXT'])) {
                $arFields['SOURCE'] .= $_POST['SOURCE_TEXT'];
            } elseif ($arFields['SOURCE']=='OTHER' && isset($arSrcElement['SOURCE_TEXT'])) {
                $arFields['SOURCE'] .= $arSrcElement['SOURCE_TEXT'];
            }


            if ($arSrcElement['AGENT_ID'] == '') {
                if (isset($_POST['AGENT_PHONE']) && $_POST['AGENT_PHONE'] != '')
                    $arFields['AGENT_PHONE'] = trim($_POST['AGENT_PHONE']);
                elseif (isset($arSrcElement['AGENT_PHONE']) && $arSrcElement['AGENT_PHONE'] != '')
                    $arFields['AGENT_PHONE'] = $arSrcElement['AGENT_PHONE'];

                if (isset($_POST['AGENT_EMAIL']) && $_POST['AGENT_EMAIL'] != '')
                    $arFields['AGENT_EMAIL'] = trim($_POST['AGENT_EMAIL']);
                elseif (isset($arSrcElement['AGENT_EMAIL']) && $arSrcElement['AGENT_EMAIL'] != '')
                    $arFields['AGENT_EMAIL'] = $arSrcElement['AGENT_EMAIL'];
            }

            if (isset($_POST['ASSIGNED_ID_VALUE'])) {
                $arFields['ASSIGNED_ID'] = $_POST['ASSIGNED_ID_VALUE'];
            } elseif (isset($arSrcElement['ASSIGNED_ID'])) {
                $arFields['ASSIGNED_ID'] = $arSrcElement['ASSIGNED_ID'];
            }


            if (isset($_POST['STATUS'])) {
                $arFields['STATUS'] = trim($_POST['STATUS']);
            } elseif (isset($arSrcElement['STATUS'])) {
                $arFields['STATUS'] = $arSrcElement['STATUS'];
            }



            if (isset($_POST['HAND_MADE_VALUE'])) {
                $arFields['HAND_MADE'] = trim($_POST['HAND_MADE_VALUE']);
            } elseif (isset($arSrcElement['HAND_MADE'])) {
                $arFields['HAND_MADE'] = $arSrcElement['HAND_MADE'];
            }


            if (isset($_POST['PAST'])) {
                $arFields['PAST'] = trim($_POST['PAST']);
            } elseif (isset($arSrcElement['PAST'])) {
                $arFields['PAST'] = $arSrcElement['PAST'];
            }

            $ID = isset($arResult['ELEMENT']['ID']) ? $arResult['ELEMENT']['ID'] : '';
            if ($ID != '') {
                if(!ProcessRegs($ID,$arResult['REG_GRID_ID'])) {
                    $DB->Rollback();
                    break;
                }
                if (!$COrderApp->Update($ID, $arFields)) {
                    ShowError($COrderApp->LAST_ERROR);
                    $DB->Rollback();
                    break;
                }
            } else {
                /*if (isset($_POST['ORDER_APP_EDIT_V12_CHANGE_BTN_REG_ID'])) {
                    $arFields['REG_ID'] = $_POST['ORDER_APP_EDIT_V12_CHANGE_BTN_REG_ID'];
                } elseif (isset($arSrcElement['REG_ID'])) {
                    $arFields['REG_ID'] = $arSrcElement['REG_ID'];
                }*/


                $arFields['ID'] = COrderHelper::GetNewID();
                $ID=$arFields['ID'];
                if(!ProcessRegs($ID,$arResult['REG_GRID_ID'])) {
                    $DB->Rollback();
                    break;
                }
                if (!$COrderApp->Add($arFields)) {
                    ShowError($COrderApp->LAST_ERROR);
                    $DB->Rollback();
                    break;
                }

            }




            $DB->Commit();
            if (isset($_POST['apply']) || isset($_POST['saveAndView'])
                || $_REQUEST['action_button_' . $arResult['REG_GRID_ID']] == 'delete') {
                LocalRedirect(
                    CComponentEngine::MakePathFromTemplate(
                        $arParams['PATH_TO_APP_EDIT'],
                        array('app_id' => $ID)
                    )
                );
            } elseif (isset($_POST['saveAndAdd'])) {
                LocalRedirect(
                    CComponentEngine::MakePathFromTemplate(
                        $arParams['PATH_TO_APP_EDIT'],
                        array('app_id' => 0)
                    )
                );
            }


            // save
            LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_APP_LIST'], array()));
        }
    } elseif (isset($_GET['delete']) && check_bitrix_sessid()) {
        if ($bDelete) {
            $DB->StartTransaction();
            if (!$COrderApp->Delete($arResult['ELEMENT']['ID'])) {
                ShowError($COrderApp->LAST_ERROR);
                $DB->Rollback();
                break;
            }

            $DB->Commit();
            LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_APP_LIST']));
            return;
        } else {
            ShowError(GetMessage('ORDER_DELETE_ERROR'));
            break;
        }
    }
} while (false);

$arResult['BACK_URL'] = $arParams['PATH_TO_APP_LIST'];

if (isset($_GET['agent_legal']) && ($_GET['agent_legal'] == 'Y' || $_GET['agent_legal'] == 'N'))
    $arResult['ELEMENT']['AGENT_LEGAL'] = $_GET['agent_legal'];

$arResult['EDIT'] = $bEdit;

$arResult['FIELDS'] = array();


$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'section_app_info',
    'name' => GetMessage('ORDER_SECTION_APP_INFO'),
    'type' => 'section'
);
/*$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'TITLE',
	'name' => GetMessage('ORDER_FIELD_TITLE_APP'),
	'params' => array('size' => 50),
	'value' => isset($arResult['ELEMENT']['~TITLE']) ? $arResult['ELEMENT']['~TITLE'] : '',
	'type' => 'text'
);*/


if ($arResult['ELEMENT']['ID'] != '') {
    $arResult['FIELDS']['tab_1'][] = array(
        'id' => 'ID',
        'name' => GetMessage('ORDER_FIELD_ID'),
        'type' => 'text',
        'VALUE' => $arResult['ELEMENT']['ID'],
        'params' => array('readonly' => "readonly"),
        'persistent' => true
    );
    $arResult['FIELDS']['tab_1'][] = array(
        'id' => 'AGENT_LEGAL',
        'name' => GetMessage('ORDER_FIELD_AGENT_LEGAL'),
        'type' => 'text',
        'value' => isset($arResult['ELEMENT']['AGENT_LEGAL']) ? GetMessage('ORDER_AGENT_LEGAL_'.$arResult['ELEMENT']['AGENT_LEGAL']) : '',
        'params'=>array('readonly'=>'readonly'),
        'persistent' => true
    );
} else {
    $arResult['FIELDS']['tab_1'][] = array(
        'id' => 'AGENT_LEGAL',
        'name' => GetMessage('ORDER_FIELD_AGENT_LEGAL'),
        'type' => 'list',
        'value' => $arResult['ELEMENT']['AGENT_LEGAL']=='Y'?'Y':'N',
        'items' => array(
            'N' => GetMessage('ORDER_AGENT_LEGAL_N'),
            'Y' => GetMessage('ORDER_AGENT_LEGAL_Y')
        ),
        'persistent' => true
    );
}


$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'AGENT_ID',
    'name' => GetMessage('ORDER_FIELD_AGENT_ID'),
    'type' => 'order_person_selector',
    'componentParams' => array(
        'TYPE' => 'agent',
        'ID' => 'AGENT_ID',
        'SELECTED' => array(
            'ID' => $arResult['ELEMENT']['AGENT_ID'],
            'LEGAL' => $arResult['ELEMENT']['AGENT_LEGAL'],
            'TITLE' => $arResult['ELEMENT']['AGENT_TITLE'],
            'PHONE' => $arResult['ELEMENT']['AGENT_PHONE'],
            'EMAIL' => $arResult['ELEMENT']['AGENT_EMAIL'],
            'CONTACT_ID' => $arResult['ELEMENT']['CONTACT_ID'],
            'CONTACT_FULL_NAME' => $arResult['ELEMENT']['CONTACT_FULL_NAME'],
            'CONTACT_PHONE' => $arResult['ELEMENT']['CONTACT_PHONE'],
            'CONTACT_EMAIL' => $arResult['ELEMENT']['CONTACT_EMAIL'],
        ),
        'READONLY'=>$onlyRead,
    ),
    'persistent' => true
);

$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'AGENT_PHONE',
    'type' => 'hidden',
    'value' => isset($arResult['ELEMENT']['AGENT_PHONE']) ? $arResult['ELEMENT']['AGENT_PHONE'] : '',
);
$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'AGENT_EMAIL',
    'type' => 'hidden',
    'value' => isset($arResult['ELEMENT']['AGENT_EMAIL']) ? $arResult['ELEMENT']['AGENT_EMAIL'] : '',
);
$arResult['STATUS_LIST'] = COrderHelper::GetEnumList('APP', "STATUS");
if(!isset($arResult['ELEMENT']['STATUS']) || $arResult['ELEMENT']['STATUS']!='CONVERTED') {
    unset($arResult['STATUS_LIST']['CONVERTED']);
}
$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'STATUS',
    'name' => GetMessage('ORDER_FIELD_STATUS'),
    'items' => $arResult['STATUS_LIST'],
    'type' => 'list',
    'value' => (isset($arResult['ELEMENT']['STATUS']) ? $arResult['ELEMENT']['STATUS'] : ''),
    'params' => $onlyRead ? array('disabled' => "disabled") : null,
    'persistent' => true
);

/*if($arResult['ELEMENT']['ID'] != '') {
    $arResult['FIELDS']['tab_1'][] = array(
        'id' => 'PERIOD',
        'name' => GetMessage('ORDER_FIELD_PERIOD'),
        'type' => 'label',
        'value' => isset($arResult['ELEMENT']['PERIOD']) ? ConvertTimeStamp(MakeTimeStamp($arResult['ELEMENT']['PERIOD']), 'SHORT', SITE_ID) : '',
        'params' => array('readonly' => "readonly"),
        'persistent' => true
    );
}*/

if (!$onlyRead) {
    ob_start();
    $ar = array(
        'inputName' => 'DESCRIPTION',
        'inputId' => 'DESCRIPTION',
        'height' => '180',
        'content' => isset($arResult['ELEMENT']['DESCRIPTION']) ? $arResult['ELEMENT']['DESCRIPTION'] : '',
        'bUseFileDialogs' => false,
        'bFloatingToolbar' => false,
        'bArisingToolbar' => false,
        'bResizable' => true,
        'bSaveOnBlur' => true,
        'toolbarConfig' => array(
            'Bold', 'Italic', 'Underline', 'Strike',
            'BackColor', 'ForeColor',
            'CreateLink', 'DeleteLink',
            'InsertOrderedList', 'InsertUnorderedList', 'Outdent', 'Indent'
        )
    );
    $LHE = new CLightHTMLEditor;
    $LHE->Show($ar);
    $sVal = ob_get_contents();
    ob_end_clean();
    $arResult['FIELDS']['tab_1'][] = array(
        'id' => 'DESCRIPTION',
        'name' => GetMessage('ORDER_FIELD_DESCRIPTION'),
        'type' => 'vertical_container',
        'value' => $sVal,
        'persistent' => true
    );
} else {
    $arResult['FIELDS']['tab_1'][] = array(
        'id' => 'DESCRIPTION',
        'name' => GetMessage('ORDER_FIELD_DESCRIPTION'),
        'type' => 'textarea',
        'params' => array('readonly' => ''),
        'value' => isset($arResult['ELEMENT']['DESCRIPTION']) ? $arResult['ELEMENT']['DESCRIPTION'] : '',
        'persistent' => true
    );
}

$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'PAST',
    'name' => GetMessage('ORDER_FIELD_PAST'),
    'type' => 'checkbox',
    'value' => (isset($arResult['ELEMENT']['PAST']) ? $arResult['ELEMENT']['PAST']:'N'),
    'params' => ($arResult['ELEMENT']['SHARED']=='Y' || $onlyRead)?array('disabled'=>"disabled"):null,
    'persistent' => true
);

$srcVal=(isset($arResult['ELEMENT']['SOURCE']) ? $arResult['ELEMENT']['SOURCE'] : '');
$srcText=(isset($arResult['ELEMENT']['SOURCE_TEXT']) ? $arResult['ELEMENT']['SOURCE_TEXT'] : '');
$srcItems=COrderHelper::GetEnumList('APP','SOURCE');
$srcParams=$onlyRead ? array('disabled' => "disabled") : null;
ob_start();
?>
<select class="order-item-table-select" id="order-source-select" name="SOURCE"<?=$srcParams?>><?
    if(is_array($srcItems)):
        foreach($srcItems as $k=>$v):
            ?><option value="<?=htmlspecialcharsbx($k)?>"<?=($k==$srcVal? ' selected':'')?>><?=htmlspecialcharsEx($v)?></option><?
        endforeach;
    endif;
    ?></select>
<div <?=$srcVal!='OTHER'?'style="display: none"':''?> id="order-source-textarea">
    <textarea name="SOURCE_TEXT" class="order-offer-textarea"><?=$srcText?></textarea>
</div>
<script>
    $(document).ready(function() {
        $("#order-source-select").change(function(){
            if($(this).val()=='OTHER'){
                $("#order-source-textarea").css("display",'block');
            } else {
                $("#order-source-textarea").css("display",'none');
            }
        });
    });

</script>
<?
$sSourceValue=ob_get_clean();
$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'SOURCE',
    'name' => GetMessage('ORDER_FIELD_SOURCE'),
    'type' => 'custom',
    'value' => $sSourceValue,
    'persistent' => true
);

$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'HAND_MADE',
    'name' => GetMessage('ORDER_FIELD_HAND_MADE'),
    'type' => 'checkbox',
    'value' => $arResult['ELEMENT']['ID'] != ''?(isset($arResult['ELEMENT']['HAND_MADE']) ? $arResult['ELEMENT']['HAND_MADE']:'N'):'Y',
    'params' => array('disabled' => "disabled"),
    'persistent' => true
);

$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'HAND_MADE_VALUE',
    'type' => 'hidden',
    'value' => $arResult['ELEMENT']['ID'] != ''?(isset($arResult['ELEMENT']['HAND_MADE']) ? $arResult['ELEMENT']['HAND_MADE']:'N'):'Y',
    'persistent' => true
);

if($arResult['ELEMENT']['ID'] == '') {
    $arResult['ELEMENT']['ASSIGNED_ID']='U'.$USER->GetID();
}


if (COrderPerms::IsAdmin() || !$COrderPerms->HavePerm('CONFIG', BX_ORDER_PERM_NONE, 'WRITE')) {
    $CAccess = new CAccess();
    $arNames = $CAccess->GetNames(array($arResult['ELEMENT']['ASSIGNED_ID']));
    $arResult['PROVIDER_NAMES'] = $CAccess->GetProviderNames();
    $arResult['ELEMENT']['ASSIGNED_TITLE'] = htmlspecialcharsbx($arNames[$arResult['ELEMENT']['ASSIGNED_ID']]['name']);
    //$arResult['ELEMENT']['ASSIGNED_TYPE'] = $arNames[$arResult['ELEMENT']['ASSIGNED_ID']]['provider_id'];
    $arResult['ELEMENT']['ASSIGNED_TYPE_NAME'] = htmlspecialcharsbx($arNames[$arResult['ELEMENT']['ASSIGNED_ID']]['provider']);
    $arResult['FIELDS']['tab_1'][] = array(
        'id' => 'ASSIGNED_ID',
        'name' => GetMessage('ORDER_FIELD_ASSIGNED_ID'),
        'type' => 'link',
        'componentParams' => array(
            'HREF' => 'javascript:void(0)',
            'VALUE' => $arResult['ELEMENT']['ASSIGNED_TITLE'],
            'ENTITY_ID' => $arResult['ELEMENT']['ASSIGNED_ID'],
            //'ENTITY_TYPE' => $arResult['ELEMENT']['ASSIGNED_TYPE'],
            'ENTITY_TYPE_NAME' => '<b>' . $arResult['ELEMENT']['ASSIGNED_TYPE_NAME'] . ':</b> ',
        ),
        'params' => array('name' => "orderUserSelect", 'onclick' => "OrderSelectEntity(); return false"),
        'persistent' => true
    );
} else {
    $CAccess = new CAccess();
    $arNames = $CAccess->GetNames(array($arResult['ELEMENT']['ASSIGNED_ID']));
    $arResult['PROVIDER_NAMES'] = $CAccess->GetProviderNames();
    $arResult['ELEMENT']['ASSIGNED_TITLE'] = htmlspecialcharsbx($arNames[$arResult['ELEMENT']['ASSIGNED_ID']]['name']);
    //$arResult['ELEMENT']['ASSIGNED_TYPE'] = $arNames[$arResult['ELEMENT']['ASSIGNED_ID']]['provider_id'];
    $arResult['ELEMENT']['ASSIGNED_TYPE_NAME'] = htmlspecialcharsbx($arNames[$arResult['ELEMENT']['ASSIGNED_ID']]['provider']);
    $arResult['FIELDS']['tab_1'][] = array(
        'id' => 'ASSIGNED_ID',
        'name' => GetMessage('ORDER_FIELD_ASSIGNED_ID'),
        'type' => 'link',
        'componentParams' => array(
            'HREF' => 'javascript:void(0)',
            'VALUE' => $arResult['ELEMENT']['ASSIGNED_TITLE'],
            'ENTITY_ID' => $arResult['ELEMENT']['ASSIGNED_ID'],
            //'ENTITY_TYPE' => $arResult['ELEMENT']['ASSIGNED_TYPE'],
            'ENTITY_TYPE_NAME' => '<b>' . $arResult['ELEMENT']['ASSIGNED_TYPE_NAME'] . ':</b> ',
        ),
        'params' => array('name' => "orderUserSelect", 'onclick' => "return false"),
        'persistent' => true
    );
}


if ($arResult['ELEMENT']['ID'] != '') {
    $arResult['FIELDS']['tab_1'][] = array(
        'id' => 'MODIFY_DATE',
        'name' => GetMessage('ORDER_FIELD_MODIFY_DATE'),
        'type' => 'text',
        'params' => array('readonly' => ''),
        'value' => isset($arResult['ELEMENT']['MODIFY_DATE']) ? ConvertTimeStamp(MakeTimeStamp($arResult['ELEMENT']['MODIFY_DATE']), 'SHORT', SITE_ID) : '',
        'persistent' => true
    );

    $arResult['FIELDS']['tab_1'][] = array(
        'id' => 'MODIFY_BY',
        'name' => GetMessage('ORDER_FIELD_MODIFY_BY'),
        'type' => 'link',
        'componentParams' => array(
            'HREF' => CComponentEngine::makePathFromTemplate($arParams['PATH_TO_STAFF_EDIT'], array(
                'staff_id' => $arResult['ELEMENT']['MODIFY_BY']
            )),
            'VALUE' => $arResult['ELEMENT']['MODIFY_BY_FULL_NAME'],
            //'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
        ),
        //'params' => array('readonly'=>"readonly"),
        'persistent' => true
    );
}



/*$icnt = count($arResult['FIELDS']['tab_1']);


if (count($arResult['FIELDS']['tab_1']) == $icnt)
	unset($arResult['FIELDS']['tab_1'][$icnt - 1]);*/


$this->IncludeComponentTemplate();

include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/newportal/order.app/include/nav.php');
?>
