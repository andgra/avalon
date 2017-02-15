<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule('order'))
{
	ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
	return;
}

//CModule::IncludeModule('fileman');
if (!function_exists('ProcessRegs')) {
	function ProcessRegs($formedGroupID,$gridID)
	{
		global $_POST;
		if (isset($_POST['FIELDS']) && is_array($_POST['FIELDS'])) {
			$COrderReg = new COrderReg();
			foreach ($_POST['FIELDS'] as $regID => $regFields) {
				if (isset($_POST['ORDER_PERSON_PHYSICAL_ID_' . $regID . '_VALUE'])) {
					$regFields['PHYSICAL_ID'] = $_POST['ORDER_PERSON_PHYSICAL_ID_' . $regID . '_VALUE'];
					if (isset($_POST[$regID . '_FLAG']) && $_POST[$regID . '_FLAG'] == 'NEW') {
						$regFields['ID'] = COrderHelper::GetNewID();
						$regFields['APP_ID'] = $_POST[$gridID.'_CHANGE_BTN_APP_ID_' . $regID];
						$regFields['SHARED'] = 'N';
						$regFields['ENTITY_ID'] = $formedGroupID;
						$regFields['ENTITY_TYPE'] = 'formed_group';
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
		} elseif ($_POST['action_button_' . $gridID] == 'delete') {
			$COrderReg = new COrderReg();
			foreach ($_POST['ID'] as $regID) {
				if (!$COrderReg->Delete($regID)) {
					ShowError($COrderReg->LAST_ERROR);
					return false;
				}
			}
		}
		return true;
	}
}

global $USER_FIELD_MANAGER, $USER;

$userID = COrderHelper::GetCurrentUserID();

$COrderPerms = COrderPerms::GetCurrentUserPermissions();

$arResult['CURRENT_USER_ID'] = $userID;
$arParams['PATH_TO_FORMED_GROUP_LIST'] = OrderCheckPath('PATH_TO_FORMED_GROUP_LIST', $arParams['PATH_TO_FORMED_GROUP_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_FORMED_GROUP_EDIT'] = OrderCheckPath('PATH_TO_FORMED_GROUP_EDIT', $arParams['PATH_TO_FORMED_GROUP_EDIT'], '/order/formed_group/edit/#formed_group_id#');
$arParams['PATH_TO_GROUP_EDIT'] = OrderCheckPath('PATH_TO_GROUP_EDIT', $arParams['PATH_TO_GROUP_EDIT'], '/order/group/edit/#group_id#');
$arParams['PATH_TO_NOMEN_EDIT'] = OrderCheckPath('PATH_TO_NOMEN_EDIT', $arParams['PATH_TO_NOMEN_EDIT'], '/order/nomen/edit/#nomen_id#');
$arParams['PATH_TO_PHYSICAL_EDIT'] = OrderCheckPath('PATH_TO_PHYSICAL_EDIT', $arParams['PATH_TO_PHYSICAL_EDIT'], '/order/physical/edit/#physical_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);
$arParams['ELEMENT_ID'] = isset($arParams['ELEMENT_ID']) ? $arParams['ELEMENT_ID'] : '';

$bRead = $bDelete = $bEdit = false;
$bVarsFromForm = false;

$COrderFormedGroup=new COrderFormedGroup();

if (!empty($arParams['ELEMENT_ID'])) {
	$arEntityAttr = $COrderPerms->GetEntityAttr('FORMED_GROUP', array($arParams['ELEMENT_ID']));
	$bEdit = true;
}

$arFields = null;
if ($bEdit)
{
	$arFilter = array(
		'ID' => $arParams['ELEMENT_ID'],
		//'PERMISSION' => 'WRITE'
	);
	$res = COrderFormedGroup::GetListEx(array(),$arFilter);
	$arFields=$res->Fetch();
	if ($arFields === false)
	{
		$bEdit = false;
	}
}
else
{
	$arFields = array(
		'ID' => ''
	);

	if (isset($_GET['formed_group_id']))
	{
		$arFields['FORMED_GROUP_ID'] =$_GET['formed_group_id'];
	}
}



if($bEdit) {
	$arResult['PERM_EDIT']=$bEdit=$COrderPerms->CheckEnityAccess('FORMED_GROUP', 'EDIT', $arEntityAttr[$arParams['ELEMENT_ID']]);
	$arResult['PERM_READ']=$bRead=$COrderPerms->CheckEnityAccess('FORMED_GROUP', 'READ', $arEntityAttr[$arParams['ELEMENT_ID']]);
	$arResult['PERM_DELETE']=$bDelete=$COrderPerms->CheckEnityAccess('FORMED_GROUP', 'DELETE', $arEntityAttr[$arParams['ELEMENT_ID']]);
} else {
	$bAdd=!$COrderPerms->HavePerm('FORMED_GROUP', BX_ORDER_PERM_NONE, 'ADD') && empty($arParams['ELEMENT_ID']);
}
$isPermitted = $bEdit || $bRead || $bAdd;
$onlyRead=$bRead && !$bEdit;


if(!$isPermitted)
{
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



$arResult['FORM_ID'] = !empty($arParams['FORM_ID']) ? $arParams['FORM_ID'] : 'ORDER_FORMED_GROUP_EDIT_V12';
$arResult['GRID_ID'] = 'ORDER_FORMED_GROUP_EDIT_V12';


do{
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid())
	{

		$bVarsFromForm = true;
		$DB->StartTransaction();
		if(isset($_POST['save']) || isset($_POST['saveAndView']) || isset($_POST['saveAndAdd']) || isset($_POST['apply']))
		{
			$arSrcElement = $bEdit ? $arResult['ELEMENT'] : array();
			$arFields = array();



			/*if(isset($_POST[$arResult['FORM_ID'].'_CHANGE_BTN_GROUP_ID']))
				$arFields['GROUP_ID'] = $_POST[$arResult['FORM_ID'].'_CHANGE_BTN_GROUP_ID'];
			elseif(isset($arSrcElement['GROUP_ID']))
				$arFields['GROUP_ID'] = $arSrcElement['GROUP_ID'];

			if(isset($_POST['DATE_START']))
				$arFields['DATE_START'] = trim($_POST['DATE_START']);
			elseif(isset($arSrcElement['DATE_START']))
				$arFields['DATE_START'] = $arSrcElement['DATE_START'];

			if(isset($_POST['DATE_END']))
				$arFields['DATE_END'] = trim($_POST['DATE_END']);
			elseif(isset($arSrcElement['DATE_END']))
				$arFields['DATE_END'] = $arSrcElement['DATE_END'];

			if(isset($_POST['MAX']))
				$arFields['MAX'] = trim($_POST['MAX']);
			elseif(isset($arSrcElement['MAX']))
				$arFields['MAX'] = $arSrcElement['MAX'];*/
			$arFields=$arSrcElement;



			$ID = isset($arResult['ELEMENT']['ID']) ? $arResult['ELEMENT']['ID'] : '';

			if($arResult['ELEMENT']['ID']!='') {
				if(!ProcessRegs($ID,$arResult['GRID_ID'])) {
					$DB->Rollback();
					break;
				}
				if(!$COrderFormedGroup->Update($ID, $arFields)) {
					ShowError($COrderFormedGroup->LAST_ERROR);
					$DB->Rollback();
					break;
				}

			}
			else {
				ShowError(GetMessage('ORDER_ADD_DENIED'));
				break;

				/*$arFields['ID']=COrderHelper::GetNewID();
				if(!$COrderFormedGroup->Add($arFields))
					$arResult['ERROR_MESSAGE']=$COrderFormedGroup->LAST_ERROR;*/

			}



			$DB->Commit();

			if (isset($_POST['apply']))
			{
				//if (COrderFormedGroup::CheckUpdatePermission($ID))
				//{
					LocalRedirect(
						CComponentEngine::MakePathFromTemplate(
							$arParams['PATH_TO_FORMED_GROUP_EDIT'],
							array('formed_group_id' => $ID)
						)
					);
				//}
			}
			elseif (isset($_POST['saveAndAdd']))
			{
				LocalRedirect(
					CComponentEngine::MakePathFromTemplate(
						$arParams['PATH_TO_FORMED_GROUP_EDIT'],
						array('formed_group_id' => 0)
					)
				);
			}


			// save
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_FORMED_GROUP_LIST'], array()));

		}

	}
	elseif (isset($_GET['delete']) && check_bitrix_sessid())
	{
		if ($bDelete) {
			$DB->StartTransaction();
			if(!$COrderFormedGroup->Delete($arResult['ELEMENT']['ID'])) {

				ShowError($COrderFormedGroup->LAST_ERROR);
				$DB->Rollback();
				break;
			}


			$DB->Commit();
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_FORMED_GROUP_LIST']));
			return;
		} else {
			ShowError(GetMessage('ORDER_DELETE_ERROR'));
			break;
		}
	}

} while(false);



$arResult['BACK_URL'] = $arParams['PATH_TO_FORMED_GROUP_LIST'];
$arResult['EDIT'] = $bEdit;

$arResult['FIELDS'] = array();


$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'section_formed_group_info',
	'name' => GetMessage('ORDER_SECTION_FORMED_GROUP_INFO'),
	'type' => 'section'
);



if($arResult['ELEMENT']['ID']!='') {
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
	'id' => 'GROUP_ID',
	'name' => GetMessage('ORDER_FIELD_GROUP_ID'),
	'type' => 'link',
	'componentParams' => array(
		'HREF' => CComponentEngine::makePathFromTemplate($arParams['PATH_TO_GROUP_EDIT'], array(
			'group_id' => $arResult['ELEMENT']['GROUP_ID']
		)),
		'VALUE' => $arResult['ELEMENT']['GROUP_TITLE'],
		//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
	),
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'NOMEN_ID',
	'name' => GetMessage('ORDER_FIELD_NOMEN_ID'),
	'type' => 'link',
	'componentParams' => array(
		'HREF' => CComponentEngine::makePathFromTemplate($arParams['PATH_TO_NOMEN_EDIT'], array(
			'nomen_id' => $arResult['ELEMENT']['NOMEN_ID']
		)),
		'VALUE' => $arResult['ELEMENT']['NOMEN_TITLE'],
		//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
	),
	'persistent' => true
);

$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'DATE_START',
	'name' => GetMessage('ORDER_FIELD_DATE_START'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['DATE_START']) ? $arResult['ELEMENT']['DATE_START'] : '',
	'params' => array('readonly' => "readonly"),
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'DATE_END',
	'name' => GetMessage('ORDER_FIELD_DATE_END'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['DATE_END']) ? $arResult['ELEMENT']['DATE_END'] : '',
	'params' => array('readonly' => "readonly"),
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'ENROLLED',
	'name' => GetMessage('ORDER_FIELD_ENROLLED'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['ENROLLED']) ? $arResult['ELEMENT']['ENROLLED'] : '',
	'params' => array('readonly' => "readonly"),
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'FREE',
	'name' => GetMessage('ORDER_FIELD_FREE'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['FREE']) ? $arResult['ELEMENT']['FREE'] : '',
	'params' => array('readonly' => "readonly"),
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'MAX',
	'name' => GetMessage('ORDER_FIELD_MAX'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['MAX']) ? $arResult['ELEMENT']['MAX'] : '',
	'params' => array('readonly' => "readonly"),
	'persistent' => true
);

$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'PRIVATE',
	'name' => GetMessage('ORDER_FIELD_PRIVATE'),
	'type' => 'checkbox',
	'value' => (isset($arResult['ELEMENT']['PRIVATE']) ? $arResult['ELEMENT']['PRIVATE']:'N'),
	'params' => array('disabled' => "disabled"),
	'persistent' => true
);
/*$icnt = count($arResult['FIELDS']['tab_1']);


if (count($arResult['FIELDS']['tab_1']) == $icnt)
	unset($arResult['FIELDS']['tab_1'][$icnt - 1]);*/


$this->IncludeComponentTemplate();

include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/newportal/order.formed_group/include/nav.php');
?>
