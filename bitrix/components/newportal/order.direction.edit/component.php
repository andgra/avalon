<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule('order')) {
    ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
    return;
}

//CModule::IncludeModule('fileman');


global $USER_FIELD_MANAGER, $USER;

$userID = COrderHelper::GetCurrentUserID();

$COrderPerms = COrderPerms::GetCurrentUserPermissions();

$arResult['CURRENT_USER_ID'] = $userID;
$arParams['PATH_TO_DIRECTION_LIST'] = OrderCheckPath('PATH_TO_DIRECTION_LIST', $arParams['PATH_TO_DIRECTION_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_DIRECTION_EDIT'] = OrderCheckPath('PATH_TO_DIRECTION_EDIT', $arParams['PATH_TO_DIRECTION_EDIT'], '/order/direction/edit/#direction_id#');
$arParams['PATH_TO_PHYSICAL_EDIT'] = OrderCheckPath('PATH_TO_PHYSICAL_EDIT', $arParams['PATH_TO_PHYSICAL_EDIT'], '/order/physical/edit/#physical_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#", "#/NOBR#"), array("", ""), $arParams["NAME_TEMPLATE"]);
$arParams['ELEMENT_ID'] = isset($arParams['ELEMENT_ID']) ? $arParams['ELEMENT_ID'] : '';

$bRead = $bDelete = $bEdit = false;
$bVarsFromForm = false;

$COrderDirection = new COrderDirection();

if (!empty($arParams['ELEMENT_ID'])) {
    $arEntityAttr = $COrderPerms->GetEntityAttr('DIRECTION', array($arParams['ELEMENT_ID']));
    $bEdit = true;
}

$arFields = null;
if ($bEdit) {
    $arFilter = array(
        'ID' => $arParams['ELEMENT_ID'],
        //'PERMISSION' => 'WRITE'
    );
    $res = COrderDirection::GetListEx(array(), $arFilter);
    $arFields = $res->Fetch();
    if ($arFields === false) {
        $bEdit = false;
    }
} else {
    $arFields = array(
        'ID' => ''
    );

    if (isset($_GET['direction_id'])) {
        $arFields['DIRECTION_ID'] = $_GET['direction_id'];
    }
}

if ($bEdit) {
    $arResult['PERM_EDIT'] = $bEdit = $COrderPerms->CheckEnityAccess('DIRECTION', 'EDIT', $arEntityAttr[$arParams['ELEMENT_ID']]);
    $arResult['PERM_READ'] = $bRead = $COrderPerms->CheckEnityAccess('DIRECTION', 'READ', $arEntityAttr[$arParams['ELEMENT_ID']]);
    $arResult['PERM_DELETE'] = $bDelete = $COrderPerms->CheckEnityAccess('DIRECTION', 'DELETE', $arEntityAttr[$arParams['ELEMENT_ID']]);
} else {
    $bAdd = !$COrderPerms->HavePerm('DIRECTION', BX_ORDER_PERM_NONE, 'ADD') && empty($arParams['ELEMENT_ID']);
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

$isExternal = $bEdit && isset($arFields['ORIGINATOR_ID']) && isset($arFields['ORIGIN_ID']) && intval($arFields['ORIGINATOR_ID']) > 0 && intval($arFields['ORIGIN_ID']) > 0;

$arResult['ELEMENT'] = is_array($arFields) ? $arFields : null;
unset($arFields);


$arResult['FORM_ID'] = !empty($arParams['FORM_ID']) ? $arParams['FORM_ID'] : 'ORDER_DIRECTION_EDIT_V12';
$arResult['GRID_ID'] = 'ORDER_DIRECTION_EDIT_V12';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid()) {

    $bVarsFromForm = true;
    if (isset($_POST['save']) || isset($_POST['saveAndView']) || isset($_POST['saveAndAdd']) || isset($_POST['apply'])) {
        $arSrcElement = $bEdit ? $arResult['ELEMENT'] : array();
        $arFields = array();

        if(isset($_POST['PRIVATE']) && ($_POST['PRIVATE']=='Y' || $_POST['PRIVATE']=='N'))
            $arFields['PRIVATE'] = $_POST['PRIVATE'];
        elseif(isset($arSrcElement['PRIVATE']))
            $arFields['PRIVATE'] = $arSrcElement['PRIVATE'];

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

        if (isset($_POST['TITLE']))
            $arFields['TITLE'] = trim($_POST['TITLE']);
        elseif (isset($arSrcElement['TITLE']))
            $arFields['TITLE'] = $arSrcElement['TITLE'];

        if (isset($_POST[$arResult['FORM_ID'] . '_CHANGE_BTN_PARENT_ID']))
            $arFields['PARENT_ID'] = $_POST[$arResult['FORM_ID'] . '_CHANGE_BTN_PARENT_ID'];
        elseif (isset($arSrcElement['PARENT_ID']))
            $arFields['PARENT_ID'] = $arSrcElement['PARENT_ID'];

        if (isset($_POST[$arResult['FORM_ID'] . '_CHANGE_BTN_MANAGER_ID']))
            $arFields['MANAGER_ID'] = $_POST[$arResult['FORM_ID'] . '_CHANGE_BTN_MANAGER_ID'];
        elseif (isset($arSrcElement['MANAGER_ID']))
            $arFields['MANAGER_ID'] = $arSrcElement['MANAGER_ID'];


        $arFields['BEHAVIOR']='';

        if (isset($_POST['BEHAVIOR'])) {
            $arFields['BEHAVIOR'] = $_POST['BEHAVIOR'];
        } elseif (isset($arSrcElement['BEHAVIOR'])) {
            $arFields['BEHAVIOR'] = $arSrcElement['BEHAVIOR'];
        }

        if($arFields['BEHAVIOR']=='SELECT') {
            if (isset($_POST[$arResult['FORM_ID'] . '_BEHAVIOR_ENTITY_TYPE'])) {
                $arFields['BEHAVIOR'] .= $_POST[$arResult['FORM_ID'] . '_BEHAVIOR_ENTITY_TYPE'].'#';
            } elseif (isset($arSrcElement['BEHAVIOR_ENTITY_TYPE'])) {
                $arFields['BEHAVIOR'] .= $arSrcElement['BEHAVIOR_ENTITY_TYPE'].'#';
            }

            if (isset($_POST[$arResult['FORM_ID'] . '_BEHAVIOR_ENTITY_VALUE'])) {
                $arFields['BEHAVIOR'] .= $_POST[$arResult['FORM_ID'] . '_BEHAVIOR_ENTITY_VALUE'];
            } elseif (isset($arSrcElement['BEHAVIOR_ENTITY_ID'])) {
                $arFields['BEHAVIOR'] .= $arSrcElement['BEHAVIOR_ENTITY_ID'];
            }
        }



        $ID = isset($arResult['ELEMENT']['ID']) ? $arResult['ELEMENT']['ID'] : '';

        if ($arResult['ELEMENT']['ID'] != '') {
            if (!$COrderDirection->Update($ID, $arFields))
                $arResult['ERROR_MESSAGE']=$COrderDirection->LAST_ERROR;
        } else {

            $arFields['ID'] = COrderHelper::GetNewID();
            if (!$COrderDirection->Add($arFields))
                $arResult['ERROR_MESSAGE']=$COrderDirection->LAST_ERROR;

        }


        if (!empty($arResult['ERROR_MESSAGE'])) {
            ShowError($arResult['ERROR_MESSAGE']);
        } else {

            if (isset($_POST['apply'])) {
                //if (COrderDirection::CheckUpdatePermission($ID))
                //{
                LocalRedirect(
                    CComponentEngine::MakePathFromTemplate(
                        $arParams['PATH_TO_DIRECTION_EDIT'],
                        array('direction_id' => $ID)
                    )
                );
                //}
            } elseif (isset($_POST['saveAndAdd'])) {
                LocalRedirect(
                    CComponentEngine::MakePathFromTemplate(
                        $arParams['PATH_TO_DIRECTION_EDIT'],
                        array('direction_id' => 0)
                    )
                );
            }


            // save
            LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_DIRECTION_LIST'], array()));
        }
    }
} elseif (isset($_GET['delete']) && check_bitrix_sessid()) {
    if ($bEdit) {
        if (!$COrderDirection->Delete($arResult['ELEMENT']['ID']))
            $arResult['ERROR_MESSAGE']=$COrderDirection->LAST_ERROR;


        if(!empty($arResult['ERROR_MESSAGE'])) {
            ShowError($arResult['ERROR_MESSAGE']);
        } else {
            LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_DIRECTION_LIST']));
            return;
        }
    } else {
        ShowError(GetMessage('ORDER_DELETE_ERROR'));
        return;
    }
}


$arResult['BACK_URL'] = $arParams['PATH_TO_DIRECTION_LIST'];
$arResult['EDIT'] = $bEdit;

$arResult['FIELDS'] = array();


$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'section_direction_info',
    'name' => GetMessage('ORDER_SECTION_DIRECTION_INFO'),
    'type' => 'section'
);
/*$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'TITLE',
	'name' => GetMessage('ORDER_FIELD_TITLE_DIRECTION'),
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
}
$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'TITLE',
    'name' => GetMessage('ORDER_FIELD_TITLE'),
    'type' => 'text',
    'value' => isset($arResult['ELEMENT']['TITLE']) ? $arResult['ELEMENT']['TITLE'] : '',
    'params' => array('readonly' => "readonly"),
    'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'PARENT_ID',
    'name' => GetMessage('ORDER_FIELD_PARENT_ID'),
    'type' => 'link',
    'componentParams' => array(
        'HREF' => CComponentEngine::makePathFromTemplate($arParams['PATH_TO_DIRECTION_EDIT'], array(
            'direction_id' => $arResult['ELEMENT']['PARENT_ID']
        )),
        'VALUE' => $arResult['ELEMENT']['PARENT_TITLE'],
        //'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
    ),
    'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'MANAGER_ID',
    'name' => GetMessage('ORDER_FIELD_MANAGER_ID'),
    'type' => 'link',
    'componentParams' => array(
        'HREF' => CComponentEngine::makePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'], array(
            'direction_id' => $arResult['ELEMENT']['MANAGER_ID']
        )),
        'VALUE' => $arResult['ELEMENT']['MANAGER_FULL_NAME'],
        //'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
    ),
    'persistent' => true
);

$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'DESCRIPTION',
    'name' => GetMessage('ORDER_FIELD_DESCRIPTION'),
    'type' => 'textarea',
    'params' => array('readonly' => ''),
    'value' => isset($arResult['ELEMENT']['DESCRIPTION']) ? $arResult['ELEMENT']['DESCRIPTION'] : '',
    'persistent' => true
);

$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'PRIVATE',
    'name' => GetMessage('ORDER_FIELD_PRIVATE'),
    'type' => 'checkbox',
    'value' => (isset($arResult['ELEMENT']['PRIVATE']) ? $arResult['ELEMENT']['PRIVATE']:'N'),
    'params' => array('disabled' => ''),
    'persistent' => true
);

$nomenId=isset($arResult['ELEMENT']['DEFAULT_NOMEN_ID']) ? $arResult['ELEMENT']['DEFAULT_NOMEN_ID'] : '';
$behaviorVal=(isset($arResult['ELEMENT']['BEHAVIOR']) ? $arResult['ELEMENT']['BEHAVIOR'] : '');
$behaviorEntType=(isset($arResult['ELEMENT']['BEHAVIOR_ENTITY_TYPE']) ? $arResult['ELEMENT']['BEHAVIOR_ENTITY_TYPE'] : '');
$behaviorEntID=(isset($arResult['ELEMENT']['BEHAVIOR_ENTITY_ID']) ? $arResult['ELEMENT']['BEHAVIOR_ENTITY_ID'] : '');
if($behaviorVal=='SELECT') {
    $behaviorClassName = 'COrder' . Bitrix\Main\Entity\Base::snake2camel($behaviorEntType);
    $behaviorEl = $behaviorClassName::GetById($behaviorEntID);
}
$behaviorItems=COrderHelper::GetEnumList('DIRECTION','BEHAVIOR');
$behaviorParams=$onlyRead ? array('disabled' => "disabled") : null;
ob_start();
?>
<select class="order-item-table-select" id="order-behavior-select" name="BEHAVIOR"<?=$behaviorParams?>><?
    if(is_array($behaviorItems)):
        foreach($behaviorItems as $k=>$v):
            ?><option value="<?=htmlspecialcharsbx($k)?>"<?=($k==$behaviorVal? ' selected':'')?>><?=htmlspecialcharsEx($v)?></option><?
        endforeach;
    endif;
    ?></select>

<div <?=$behaviorVal!='SELECT'?'style="display: none"':''?> id="order-behavior-textarea">
    <?
    $APPLICATION->IncludeComponent('newportal:order.structure.selector',
        '',
        array(
            'ID' => $arResult['FORM_ID'].'_BEHAVIOR_ENTITY',
            'SELECTED' => array(
                'VALUE'=>$behaviorEntID,
                'TITLE'=>$behaviorEl['TITLE'],
                'TYPE'=>$behaviorEntType
            ),
        ),
        false,
        array('HIDE_ICONS' => 'Y')
    );
    ?>
</div>
<script>
    $(document).ready(function() {
        $("#order-behavior-select").change(function(){
            if($(this).val()=='SELECT'){
                $("#order-behavior-textarea").css("display",'block');
            } else {
                $("#order-behavior-textarea").css("display",'none');
            }
        });
    });

</script>
<?
$sValNomen = ob_get_clean();

$arResult['FIELDS']['tab_1'][] = array(
    'id' => 'DEFAULT_NOMEN_ID',
    'name' => GetMessage('ORDER_FIELD_DEFAULT_BEHAVIOR'),
    'type' => 'custom',
    'value' => $sValNomen,
    'persistent' => true
);
/*$icnt = count($arResult['FIELDS']['tab_1']);


if (count($arResult['FIELDS']['tab_1']) == $icnt)
	unset($arResult['FIELDS']['tab_1'][$icnt - 1]);*/


$this->IncludeComponentTemplate();

include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/newportal/order.direction/include/nav.php');
?>
