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
$arParams['PATH_TO_CONTACT_LIST'] = OrderCheckPath('PATH_TO_CONTACT_LIST', $arParams['PATH_TO_CONTACT_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_CONTACT_EDIT'] = OrderCheckPath('PATH_TO_CONTACT_EDIT', $arParams['PATH_TO_CONTACT_EDIT'], '/order/contact/edit/#contact_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);
$arParams['ELEMENT_ID'] = isset($arParams['ELEMENT_ID']) ? $arParams['ELEMENT_ID'] : '';

$bRead = $bDelete = $bEdit = false;
$bVarsFromForm = false;

$COrderContact=new COrderContact();

if (!empty($arParams['ELEMENT_ID'])) {
	$arEntityAttr = $COrderPerms->GetEntityAttr('CONTACT', array($arParams['ELEMENT_ID']));
	$bEdit = true;
}

$arFields = null;
if ($bEdit)
{
	$arFilter = array(
		'ID' => $arParams['ELEMENT_ID'],
		//'PERMISSION' => 'WRITE'
	);
	$res = COrderContact::GetListEx(array(),$arFilter);
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

	if (isset($_GET['contact_id']))
	{
		$arFields['CONTACT_ID'] =$_GET['contact_id'];
	}
}

if($bEdit) {
	$arResult['PERM_EDIT']=$bEdit=$COrderPerms->CheckEnityAccess('CONTACT', 'EDIT', $arEntityAttr[$arParams['ELEMENT_ID']]);
	$arResult['PERM_READ']=$bRead=$COrderPerms->CheckEnityAccess('CONTACT', 'READ', $arEntityAttr[$arParams['ELEMENT_ID']]);
	$arResult['PERM_DELETE']=$bDelete=$COrderPerms->CheckEnityAccess('CONTACT', 'DELETE', $arEntityAttr[$arParams['ELEMENT_ID']]);
} else {
	$bAdd=!$COrderPerms->HavePerm('CONTACT', BX_ORDER_PERM_NONE, 'ADD') && empty($arParams['ELEMENT_ID']);
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



$arResult['FORM_ID'] = !empty($arParams['FORM_ID']) ? $arParams['FORM_ID'] : 'ORDER_CONTACT_EDIT_V12';
$arResult['GRID_ID'] = 'ORDER_CONTACT_EDIT_V12';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid())
{

	$bVarsFromForm = true;
	if(isset($_POST['save']) || isset($_POST['saveAndView']) || isset($_POST['saveAndAdd']) || isset($_POST['apply']))
	{
		$arSrcElement = $bEdit ? $arResult['ELEMENT'] : array();
		$arFields = array();



		if(isset($_POST['DESCRIPTION']))
		{
			$comments = isset($_POST['DESCRIPTION']) ? trim($_POST['DESCRIPTION']) : '';
			if($comments !== '' && strpos($comments, '<') !== false)
			{
				$sanitizer = new CBXSanitizer();
				$sanitizer->ApplyDoubleEncode(false);
				$sanitizer->SetLevel(CBXSanitizer::SECURE_LEVEL_MIDDLE);
				//Crutch for for Chrome line break behaviour in HTML editor.
				$sanitizer->AddTags(array('div' => array()));
				$comments = $sanitizer->SanitizeHtml($comments);
			}
			$arFields['DESCRIPTION'] = $comments;
		}

		if(isset($_POST[$arResult['FORM_ID'].'_CHANGE_BTN_GUID']))
			$arFields['GUID'] = $_POST[$arResult['FORM_ID'].'_CHANGE_BTN_GUID'];
		elseif(isset($arSrcElement['GUID']))
			$arFields['GUID'] = $arSrcElement['GUID'];

		if(isset($_POST[$arResult['FORM_ID'].'_CHANGE_BTN_AGENT_ID']))
			$arFields['AGENT_ID'] = $_POST[$arResult['FORM_ID'].'_CHANGE_BTN_AGENT_ID'];
		elseif(isset($arSrcElement['AGENT_ID']))
			$arFields['AGENT_ID'] = $arSrcElement['AGENT_ID'];

		if(isset($_POST['START_DATE']))
			$arFields['START_DATE'] = trim($_POST['START_DATE']);
		elseif(isset($arSrcElement['START_DATE']))
			$arFields['START_DATE'] = $arSrcElement['START_DATE'];

		if(isset($_POST['END_DATE']))
			$arFields['END_DATE'] = trim($_POST['END_DATE']);
		elseif(isset($arSrcElement['END_DATE']))
			$arFields['END_DATE'] = $arSrcElement['END_DATE'];

		if(isset($_POST[$arResult['FORM_ID'].'_CHANGE_BTN_ASSIGNED_ID']))
			$arFields['ASSIGNED_ID'] = $_POST[$arResult['FORM_ID'].'_CHANGE_BTN_ASSIGNED_ID'];
		elseif(isset($arSrcElement['ASSIGNED_ID']))
			$arFields['ASSIGNED_ID'] = $arSrcElement['ASSIGNED_ID'];


		$ID = isset($arResult['ELEMENT']['ID']) ? $arResult['ELEMENT']['ID'] : '';

		if($arResult['ELEMENT']['ID']!='') {
			if(!$COrderContact->Update($ID, $arFields))
				$arResult['ERROR_MESSAGE']=$COrderContact->LAST_ERROR;
		}
		else {

			$arFields['SHARED'] = 'N';
			$arFields['ID']=COrderHelper::GetNewID();
			if(!$COrderContact->Add($arFields))
				$arResult['ERROR_MESSAGE']=$COrderContact->LAST_ERROR;

		}




		if (!empty($arResult['ERROR_MESSAGE']))
		{
			ShowError($arResult['ERROR_MESSAGE']);
		}
		else
		{

			if (isset($_POST['apply']))
			{
				//if (COrderContact::CheckUpdatePermission($ID))
				//{
					LocalRedirect(
						CComponentEngine::MakePathFromTemplate(
							$arParams['PATH_TO_CONTACT_EDIT'],
							array('contact_id' => $ID)
						)
					);
				//}
			}
			elseif (isset($_POST['saveAndAdd']))
			{
				LocalRedirect(
					CComponentEngine::MakePathFromTemplate(
						$arParams['PATH_TO_CONTACT_EDIT'],
						array('contact_id' => 0)
					)
				);
			}
			

			// save
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_CONTACT_LIST'], array()));
		}
	}
}
elseif (isset($_GET['delete']) && check_bitrix_sessid())
{
	if ($bEdit)
	{
		if(!$COrderContact->Delete($arResult['ELEMENT']['ID']))
			$arResult['ERROR_MESSAGE']=$COrderContact->LAST_ERROR;


		if(!empty($arResult['ERROR_MESSAGE'])) {
			ShowError($arResult['ERROR_MESSAGE']);
		} else {
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_CONTACT_LIST']));
			return;
		}
	}
	else
	{
		ShowError(GetMessage('ORDER_DELETE_ERROR'));
		return;
	}
}


$arResult['BACK_URL'] = $arParams['PATH_TO_CONTACT_LIST'];
$arResult['EDIT'] = $bEdit;

$arResult['FIELDS'] = array();


$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'section_contact_info',
	'name' => GetMessage('ORDER_SECTION_CONTACT_INFO'),
	'type' => 'section'
);
/*$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'TITLE',
	'name' => GetMessage('ORDER_FIELD_TITLE_CONTACT'),
	'params' => array('size' => 50),
	'value' => isset($arResult['ELEMENT']['~TITLE']) ? $arResult['ELEMENT']['~TITLE'] : '',
	'type' => 'text'
);*/



if($arResult['ELEMENT']['ID']!='') {
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'ID',
		'name' => GetMessage('ORDER_FIELD_ID'),
		'type' => 'text',
		'VALUE' => $arResult['ELEMENT']['ID'],
		'params' => array('readonly' => "readonly"),
		'persistent' => true
	);
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'SHARED',
		'name' => GetMessage('ORDER_FIELD_SHARED'),
		'type' => 'checkbox',
		'value' => (isset($arResult['ELEMENT']['SHARED']) ? $arResult['ELEMENT']['SHARED']:'N'),
		'params' => array('disabled'=>"disabled"),
		'persistent' => true
	);
}


$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'GUID',
	'name' => GetMessage('ORDER_FIELD_GUID'),
	'type' => 'order_person_selector',
	'componentParams' => array(
		'TYPE' => 'physical',
		'ID' => 'GUID',
		'SELECTED' => array(
			'ID' => $arResult['ELEMENT']['GUID'],
			'FULL_NAME' => $arResult['ELEMENT']['FULL_NAME'],
			'PHONE' => $arResult['ELEMENT']['PHONE'],
			'EMAIL' => $arResult['ELEMENT']['EMAIL'],
		),
		'READONLY'=>$arResult['ELEMENT']['SHARED']=='Y'
	),
	'persistent' => true
);

$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'AGENT_ID',
	'name' => GetMessage('ORDER_FIELD_AGENT_ID'),
	'type' => 'order_person_selector',
	'componentParams' => array(
		'TYPE' => 'agent',
		'ID' => 'AGENT_ID',
		'SELECTED' => array(
			'ID' => $arResult['ELEMENT']['AGENT_ID'],
			'LEGAL' => 'Y',
			'TITLE' => $arResult['ELEMENT']['AGENT_TITLE'],
			'PHONE' => $arResult['ELEMENT']['AGENT_PHONE'],
			'EMAIL' => $arResult['ELEMENT']['AGENT_EMAIL'],
		),
		'READONLY'=>$arResult['ELEMENT']['ID']!=''
	),
	'persistent' => true
);

if($arResult['ELEMENT']['SHARED']=='Y') {
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'START_DATE',
		'name' => GetMessage('ORDER_FIELD_START_DATE'),
		'type' => 'text',
		'value' => isset($arResult['ELEMENT']['START_DATE']) ? $arResult['ELEMENT']['START_DATE'] : '',
		'params'=>array('readonly'=>'readonly'),
		'persistent' => true
	);
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'END_DATE',
		'name' => GetMessage('ORDER_FIELD_END_DATE'),
		'type' => 'text',
		'value' => isset($arResult['ELEMENT']['END_DATE']) ? $arResult['ELEMENT']['END_DATE'] : '',
		'params'=>array('readonly'=>'readonly'),
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
} else {
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'START_DATE',
		'name' => GetMessage('ORDER_FIELD_START_DATE'),
		'type' => 'date',
		'value' => isset($arResult['ELEMENT']['START_DATE']) ? $arResult['ELEMENT']['START_DATE'] : '',
		'persistent' => true
	);
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'END_DATE',
		'name' => GetMessage('ORDER_FIELD_END_DATE'),
		'type' => 'date',
		'value' => isset($arResult['ELEMENT']['END_DATE']) ? $arResult['ELEMENT']['END_DATE'] : '',
		'persistent' => true
	);
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
}



if($arResult['ELEMENT']['ID']!='') {
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'MODIFY_DATE',
		'name' => GetMessage('ORDER_FIELD_MODIFY_DATE'),
		'type' => 'text',
		'params' => array('readonly' => ''),
		'value' => isset($arResult['ELEMENT']['MODIFY_DATE']) ? ConvertTimeStamp(MakeTimeStamp($arResult['ELEMENT']['MODIFY_DATE']), 'FULL', SITE_ID) : '',
		'persistent' => true
	);

	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'MODIFY_BY',
		'name' => GetMessage('ORDER_FIELD_MODIFY_BY_ID'),
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

include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/newportal/order.contact/include/nav.php');
?>
