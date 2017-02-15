<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule('order'))
{
	ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
	return;
}

//CModule::IncludeModule('fileman');


global $USER_FIELD_MANAGER, $USER;

$userID = COrderHelper::GetCurrentUserID();

$COrderPerms = COrderPerms::GetCurrentUserPermissions();

$arResult['CURRENT_USER_ID'] = $userID;
$arParams['PATH_TO_GROUP_LIST'] = OrderCheckPath('PATH_TO_GROUP_LIST', $arParams['PATH_TO_GROUP_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_GROUP_EDIT'] = OrderCheckPath('PATH_TO_GROUP_EDIT', $arParams['PATH_TO_GROUP_EDIT'], '/order/group/edit/#group_id#');
$arParams['PATH_TO_NOMEN_EDIT'] = OrderCheckPath('PATH_TO_NOMEN_EDIT', $arParams['PATH_TO_NOMEN_EDIT'], '/order/nomen/edit/#nomen_id#');
$arParams['PATH_TO_PHYSICAL_EDIT'] = OrderCheckPath('PATH_TO_PHYSICAL_EDIT', $arParams['PATH_TO_PHYSICAL_EDIT'], '/order/physical/edit/#physical_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);
$arParams['ELEMENT_ID'] = isset($arParams['ELEMENT_ID']) ? $arParams['ELEMENT_ID'] : '';

$bRead = $bDelete = $bEdit = false;
$bVarsFromForm = false;

$COrderGroup=new COrderGroup();

if (!empty($arParams['ELEMENT_ID'])) {
	$arEntityAttr = $COrderPerms->GetEntityAttr('GROUP', array($arParams['ELEMENT_ID']));
	$bEdit = true;
}


$arFields = null;
if ($bEdit)
{
	$arFilter = array(
		'ID' => $arParams['ELEMENT_ID'],
		//'PERMISSION' => 'WRITE'
	);
	$res = COrderGroup::GetListEx(array(),$arFilter);
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


	if (isset($_GET['group_id']))
	{
		$arFields['GROUP_ID'] =$_GET['group_id'];
	}
}




if($bEdit) {
	$arResult['PERM_EDIT']=$bEdit=$COrderPerms->CheckEnityAccess('GROUP', 'EDIT', $arEntityAttr[$arParams['ELEMENT_ID']]);
	$arResult['PERM_READ']=$bRead=$COrderPerms->CheckEnityAccess('GROUP', 'READ', $arEntityAttr[$arParams['ELEMENT_ID']]);
	$arResult['PERM_DELETE']=$bDelete=$COrderPerms->CheckEnityAccess('GROUP', 'DELETE', $arEntityAttr[$arParams['ELEMENT_ID']]);
} else {
	$bAdd=!$COrderPerms->HavePerm('GROUP', BX_ORDER_PERM_NONE, 'ADD') && empty($arParams['ELEMENT_ID']);
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



$arResult['FORM_ID'] = !empty($arParams['FORM_ID']) ? $arParams['FORM_ID'] : 'ORDER_GROUP_EDIT_V12';
$arResult['GRID_ID'] = 'ORDER_GROUP_EDIT_V12';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid())
{

	$bVarsFromForm = true;
	if(isset($_POST['save']) || isset($_POST['saveAndView']) || isset($_POST['saveAndAdd']) || isset($_POST['apply']))
	{
		$arSrcElement = $bEdit ? $arResult['ELEMENT'] : array();
		$arFields = array();



		if(isset($_POST['PRIVATE']) && ($_POST['PRIVATE']=='Y' || $_POST['PRIVATE']=='N'))
			$arFields['PRIVATE'] = $_POST['PRIVATE'];
		elseif(isset($arSrcElement['PRIVATE']))
			$arFields['PRIVATE'] = $arSrcElement['PRIVATE'];


		if(isset($_POST['TITLE']))
			$arFields['TITLE'] = trim($_POST['TITLE']);
		elseif(isset($arSrcElement['TITLE']))
			$arFields['TITLE'] = $arSrcElement['TITLE'];

		if(isset($_POST[$arResult['FORM_ID'].'_CHANGE_BTN_NOMEN_ID']))
			$arFields['NOMEN_ID'] = $_POST[$arResult['FORM_ID'].'_CHANGE_BTN_NOMEN_ID'];
		elseif(isset($arSrcElement['NOMEN_ID']))
			$arFields['NOMEN_ID'] = $arSrcElement['NOMEN_ID'];



		$ID = isset($arResult['ELEMENT']['ID']) ? $arResult['ELEMENT']['ID'] : '';

		if($arResult['ELEMENT']['ID']!='') {
			if(!$COrderGroup->Update($ID, $arFields))
				$arResult['ERROR_MESSAGE']=$COrderGroup->LAST_ERROR;
		}
		else {

			$arFields['ID']=COrderHelper::GetNewID();
			if(!$COrderGroup->Add($arFields))
				$arResult['ERROR_MESSAGE']=$COrderGroup->LAST_ERROR;

		}




		if (!empty($arResult['ERROR_MESSAGE']))
		{
			ShowError($arResult['ERROR_MESSAGE']);
		}
		else
		{

			if (isset($_POST['apply']))
			{
				//if (COrderGroup::CheckUpdatePermission($ID))
				//{
					LocalRedirect(
						CComponentEngine::MakePathFromTemplate(
							$arParams['PATH_TO_GROUP_EDIT'],
							array('group_id' => $ID)
						)
					);
				//}
			}
			elseif (isset($_POST['saveAndAdd']))
			{
				LocalRedirect(
					CComponentEngine::MakePathFromTemplate(
						$arParams['PATH_TO_GROUP_EDIT'],
						array('group_id' => 0)
					)
				);
			}
			

			// save
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_GROUP_LIST'], array()));
		}
	}
}
elseif (isset($_GET['delete']) && check_bitrix_sessid())
{
	if ($bEdit)
	{
		if(!$COrderGroup->Delete($arResult['ELEMENT']['ID']))
			$arResult['ERROR_MESSAGE']=$COrderGroup->LAST_ERROR;


		if(!empty($arResult['ERROR_MESSAGE'])) {
			ShowError($arResult['ERROR_MESSAGE']);
		} else {
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_GROUP_LIST']));
			return;
		}
	}
	else
	{
		ShowError(GetMessage('ORDER_DELETE_ERROR'));
		return;
	}
}



$arResult['BACK_URL'] = $arParams['PATH_TO_GROUP_LIST'];
$arResult['EDIT'] = $bEdit;

$arResult['FIELDS'] = array();


$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'section_group_info',
	'name' => GetMessage('ORDER_SECTION_GROUP_INFO'),
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
	'id' => 'TITLE',
	'name' => GetMessage('ORDER_FIELD_TITLE'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['TITLE']) ? $arResult['ELEMENT']['TITLE'] : '',
	'params' => array('readonly' => "readonly"),
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
	'id' => 'PRIVATE',
	'name' => GetMessage('ORDER_FIELD_PRIVATE'),
	'type' => 'checkbox',
	'value' => (isset($arResult['ELEMENT']['PRIVATE']) ? $arResult['ELEMENT']['PRIVATE']:'N'),
	'persistent' => true
);

/*$icnt = count($arResult['FIELDS']['tab_1']);


if (count($arResult['FIELDS']['tab_1']) == $icnt)
	unset($arResult['FIELDS']['tab_1'][$icnt - 1]);*/


$this->IncludeComponentTemplate();

include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/newportal/order.group/include/nav.php');
?>
