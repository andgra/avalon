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
$arParams['PATH_TO_PHYSICAL_LIST'] = OrderCheckPath('PATH_TO_PHYSICAL_LIST', $arParams['PATH_TO_PHYSICAL_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_PHYSICAL_EDIT'] = OrderCheckPath('PATH_TO_PHYSICAL_EDIT', $arParams['PATH_TO_PHYSICAL_EDIT'], '/order/physical/edit/#physical_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);
$arParams['ELEMENT_ID'] = isset($arParams['ELEMENT_ID']) ? $arParams['ELEMENT_ID'] : '';

$bRead = $bDelete = $bEdit = false;
$bVarsFromForm = false;

$COrderPhysical=new COrderPhysical();

if (!empty($arParams['ELEMENT_ID'])) {
	$arEntityAttr = $COrderPerms->GetEntityAttr('PHYSICAL', array($arParams['ELEMENT_ID']));
	$bEdit = true;
}

$arFields = null;
if ($bEdit)
{
	$arFilter = array(
		'ID' => $arParams['ELEMENT_ID'],
		//'PERMISSION' => 'WRITE'
	);
	$res = COrderPhysical::GetListEx(array(),$arFilter);
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

	if (isset($_GET['physical_id']))
	{
		$arFields['PHYSICAL_ID'] =$_GET['physical_id'];
	}
}

if($bEdit) {
	$arResult['PERM_EDIT']=$bEdit=$COrderPerms->CheckEnityAccess('PHYSICAL', 'EDIT', $arEntityAttr[$arParams['ELEMENT_ID']]);
	$arResult['PERM_READ']=$bRead=$COrderPerms->CheckEnityAccess('PHYSICAL', 'READ', $arEntityAttr[$arParams['ELEMENT_ID']]);
	$arResult['PERM_DELETE']=$bDelete=$COrderPerms->CheckEnityAccess('PHYSICAL', 'DELETE', $arEntityAttr[$arParams['ELEMENT_ID']]);
} else {
	$bAdd=!$COrderPerms->HavePerm('PHYSICAL', BX_ORDER_PERM_NONE, 'ADD') && empty($arParams['ELEMENT_ID']);
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



$arResult['FORM_ID'] = !empty($arParams['FORM_ID']) ? $arParams['FORM_ID'] : 'ORDER_PHYSICAL_EDIT_V12';
$arResult['GRID_ID'] = 'ORDER_PHYSICAL_EDIT_V12';


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


		if(isset($_POST['LAST_NAME']))
		{
			$arFields['LAST_NAME'] = trim($_POST['LAST_NAME']);
		}
		elseif(isset($arSrcElement['LAST_NAME']))
		{
			$arFields['LAST_NAME'] = $arSrcElement['LAST_NAME'];
		}

		if(isset($_POST['NAME']))
		{
			$arFields['NAME'] = trim($_POST['NAME']);
		}
		elseif(isset($arSrcElement['NAME']))
		{
			$arFields['NAME'] = $arSrcElement['NAME'];
		}

		if(isset($_POST['SECOND_NAME']))
		{
			$arFields['SECOND_NAME'] = $_POST['SECOND_NAME'];
		}
		elseif(isset($arSrcElement['SECOND_NAME']))
		{
			$arFields['SECOND_NAME'] = $arSrcElement['SECOND_NAME'];
		}

		if(isset($_POST['GENDER']))
		{
			$arFields['GENDER'] = $_POST['GENDER'];
		}
		elseif(isset($arSrcElement['GENDER']))
		{
			$arFields['GENDER'] = $arSrcElement['GENDER'];
		}

		if(isset($_POST['BDAY']))
		{
			$arFields['BDAY'] = $_POST['BDAY'];
		}
		elseif(isset($arSrcElement['BDAY']))
		{
			$arFields['BDAY'] = $arSrcElement['BDAY'];
		}

		if(isset($_POST['OUT_ADDRESS']))
		{
			$arFields['OUT_ADDRESS'] = $_POST['OUT_ADDRESS'];
		}
		elseif(isset($arSrcElement['OUT_ADDRESS']))
		{
			$arFields['OUT_ADDRESS'] = $arSrcElement['OUT_ADDRESS'];
		}

		if(isset($_POST['PHYSICAL_ADDRESS']))
		{
			$arFields['PHYSICAL_ADDRESS'] = $_POST['PHYSICAL_ADDRESS'];
		}
		elseif(isset($arSrcElement['PHYSICAL_ADDRESS']))
		{
			$arFields['PHYSICAL_ADDRESS'] = $arSrcElement['PHYSICAL_ADDRESS'];
		}

		if(isset($_POST['LIVE_ADDRESS']))
		{
			$arFields['LIVE_ADDRESS'] = $_POST['LIVE_ADDRESS'];
		}
		elseif(isset($arSrcElement['LIVE_ADDRESS']))
		{
			$arFields['LIVE_ADDRESS'] = $arSrcElement['LIVE_ADDRESS'];
		}

		if(isset($_POST['EMAIL']))
		{
			$arFields['EMAIL'] = $_POST['EMAIL'];
		}
		elseif(isset($arSrcElement['EMAIL']))
		{
			$arFields['EMAIL'] = $arSrcElement['EMAIL'];
		}

		if(isset($_POST['PHONE']))
		{
			$arFields['PHONE'] = $_POST['PHONE'];
		}
		elseif(isset($arSrcElement['PHONE']))
		{
			$arFields['PHONE'] = $arSrcElement['PHONE'];
		}

		if(isset($_POST['OTHER']))
		{
			$arFields['OTHER'] = $_POST['OTHER'];
		}
		elseif(isset($arSrcElement['OTHER']))
		{
			$arFields['OTHER'] = $arSrcElement['OTHER'];
		}

		if(isset($_POST['PROF_EDU']))
		{
			$arFields['PROF_EDU'] = $_POST['PROF_EDU'];
		}
		elseif(isset($arSrcElement['PROF_EDU']))
		{
			$arFields['PROF_EDU'] = $arSrcElement['PROF_EDU'];
		}

		if(isset($_POST['LVL_EDU']))
		{
			$arFields['LVL_EDU'] = $_POST['LVL_EDU'];
		}
		elseif(isset($arSrcElement['LVL_EDU']))
		{
			$arFields['LVL_EDU'] = $arSrcElement['LVL_EDU'];
		}

		if(isset($_POST['NATION']))
		{
			$arFields['NATION'] = $_POST['NATION'];
		}
		elseif(isset($arSrcElement['NATION']))
		{
			$arFields['NATION'] = $arSrcElement['NATION'];
		}

		if(isset($_POST['ZIP_CODE']))
		{
			$arFields['ZIP_CODE'] = $_POST['ZIP_CODE'];
		}
		elseif(isset($arSrcElement['ZIP_CODE']))
		{
			$arFields['ZIP_CODE'] = $arSrcElement['ZIP_CODE'];
		}

		if(isset($_POST['PHYSICALION']))
			$arFields['PHYSICALION'] = $_POST['PHYSICALION'];
		elseif(isset($arSrcElement['PHYSICALION']))
			$arFields['PHYSICALION'] = $arSrcElement['PHYSICALION'];

		if(isset($_POST['BPLACE']))
			$arFields['BPLACE'] = $_POST['BPLACE'];
		elseif(isset($arSrcElement['BPLACE']))
			$arFields['BPLACE'] = $arSrcElement['BPLACE'];

		if(isset($_POST['SECOND_EDU']))
			$arFields['SECOND_EDU'] = $_POST['SECOND_EDU'];
		elseif(isset($arSrcElement['SECOND_EDU']))
			$arFields['SECOND_EDU'] = $arSrcElement['SECOND_EDU'];

		if(isset($_POST['CERT_MID_EDU']))
			$arFields['CERT_MID_EDU'] = $_POST['CERT_MID_EDU'];
		elseif(isset($arSrcElement['CERT_MID_EDU']))
			$arFields['CERT_MID_EDU'] = $arSrcElement['CERT_MID_EDU'];

		if(isset($_POST['SERIAL_DIP']))
			$arFields['SERIAL_DIP'] = $_POST['SERIAL_DIP'];
		elseif(isset($arSrcElement['SERIAL_DIP']))
			$arFields['SERIAL_DIP'] = $arSrcElement['SERIAL_DIP'];

		if(isset($_POST['NOM_DIP']))
			$arFields['NOM_DIP'] = $_POST['NOM_DIP'];
		elseif(isset($arSrcElement['NOM_DIP']))
			$arFields['NOM_DIP'] = $arSrcElement['NOM_DIP'];

		if(isset($_POST['WHO_DIP']))
			$arFields['WHO_DIP'] = $_POST['WHO_DIP'];
		elseif(isset($arSrcElement['WHO_DIP']))
			$arFields['WHO_DIP'] = $arSrcElement['WHO_DIP'];

		if(isset($_POST['WHEN_DIP']))
			$arFields['WHEN_DIP'] = $_POST['WHEN_DIP'];
		elseif(isset($arSrcElement['WHEN_DIP']))
			$arFields['WHEN_DIP'] = $arSrcElement['WHEN_DIP'];

		if(isset($_POST['END_YEAR']))
			$arFields['END_YEAR'] = $_POST['END_YEAR'];
		elseif(isset($arSrcElement['END_YEAR']))
			$arFields['END_YEAR'] = $arSrcElement['END_YEAR'];

		if(isset($_POST['HONORS_DIP']))
			$arFields['HONORS_DIP'] = $_POST['HONORS_DIP'];
		elseif(isset($arSrcElement['HONORS_DIP']))
			$arFields['HONORS_DIP'] = $arSrcElement['HONORS_DIP'];

		if(isset($_POST['ORIGINAL_DIP']))
			$arFields['ORIGINAL_DIP'] = $_POST['ORIGINAL_DIP'];
		elseif(isset($arSrcElement['ORIGINAL_DIP']))
			$arFields['ORIGINAL_DIP'] = $arSrcElement['ORIGINAL_DIP'];



		$ID = isset($arResult['ELEMENT']['ID']) ? $arResult['ELEMENT']['ID'] : '';

		if($arResult['ELEMENT']['ID']!='') {
			if(!$COrderPhysical->Update($ID, $arFields))
				$arResult['ERROR_MESSAGE']=$COrderPhysical->LAST_ERROR;
		}
		else {

			$arFields['SHARED'] = 'N';
			$arFields['ID']=COrderHelper::GetGUID($arFields['LAST_NAME'].' '.$arFields['NAME'].' '.$arFields['SECOND_NAME']);
			if(!$COrderPhysical->Add($arFields))
				$arResult['ERROR_MESSAGE']=$COrderPhysical->LAST_ERROR;

		}




		if (!empty($arResult['ERROR_MESSAGE']))
		{
			ShowError($arResult['ERROR_MESSAGE']);
		}
		else
		{

			if (isset($_POST['apply']))
			{
				//if (COrderPhysical::CheckUpdatePermission($ID))
				//{
					LocalRedirect(
						CComponentEngine::MakePathFromTemplate(
							$arParams['PATH_TO_PHYSICAL_EDIT'],
							array('physical_id' => $ID)
						)
					);
				//}
			}
			elseif (isset($_POST['saveAndAdd']))
			{
				LocalRedirect(
					CComponentEngine::MakePathFromTemplate(
						$arParams['PATH_TO_PHYSICAL_EDIT'],
						array('physical_id' => 0)
					)
				);
			}
			

			// save
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_LIST'], array()));
		}
	}
}
elseif (isset($_GET['delete']) && check_bitrix_sessid())
{
	if ($bEdit)
	{
		if(!$COrderPhysical->Delete($arResult['ELEMENT']['ID']))
			$arResult['ERROR_MESSAGE']=$COrderPhysical->LAST_ERROR;


		if(!empty($arResult['ERROR_MESSAGE'])) {
			ShowError($arResult['ERROR_MESSAGE']);
		} else {
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_PHYSICAL_LIST']));
			return;
		}
	}
	else
	{
		ShowError(GetMessage('ORDER_DELETE_ERROR'));
		return;
	}
}

$arResult['GENDER_LIST']=array(
	GetMessage('ORDER_GENDER_LIST_MALE')=>GetMessage('ORDER_GENDER_LIST_MALE'),
	GetMessage('ORDER_GENDER_LIST_FEMALE')=>GetMessage('ORDER_GENDER_LIST_FEMALE')
);

$arResult['BACK_URL'] = $arParams['PATH_TO_PHYSICAL_LIST'];
$arResult['EDIT'] = $bEdit;

$arResult['FIELDS'] = array();


$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'section_physical_info',
	'name' => GetMessage('ORDER_SECTION_PHYSICAL_INFO'),
	'type' => 'section'
);
/*$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'TITLE',
	'name' => GetMessage('ORDER_FIELD_TITLE_PHYSICAL'),
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
	'id' => 'LAST_NAME',
	'name' => GetMessage('ORDER_FIELD_LAST_NAME'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['LAST_NAME']) ? $arResult['ELEMENT']['LAST_NAME'] : '',
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'NAME',
	'name' => GetMessage('ORDER_FIELD_NAME'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['NAME']) ? $arResult['ELEMENT']['NAME'] : '',
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'SECOND_NAME',
	'name' => GetMessage('ORDER_FIELD_SECOND_NAME'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['SECOND_NAME']) ? $arResult['ELEMENT']['SECOND_NAME'] : '',
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'GENDER',
	'name' => GetMessage('ORDER_FIELD_GENDER'),
	'items' => $arResult['GENDER_LIST'],
	'type' => 'list',
	'value' => isset($arResult['ELEMENT']['GENDER']) ? $arResult['ELEMENT']['GENDER'] : '',
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'BDAY',
	'name' => GetMessage('ORDER_FIELD_BDAY'),
	'type' => 'date',
	'value' => isset($arResult['ELEMENT']['BDAY']) ? $arResult['ELEMENT']['BDAY'] : '',
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'EMAIL',
	'name' => GetMessage('ORDER_FIELD_EMAIL'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['EMAIL']) ? $arResult['ELEMENT']['EMAIL'] : '',
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'PHONE',
	'name' => GetMessage('ORDER_FIELD_PHONE'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['PHONE']) ? $arResult['ELEMENT']['PHONE'] : '',
	'persistent' => true
);



if($arResult['ELEMENT']['SHARED']=='Y') {
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'DESCRIPTION',
		'name' => GetMessage('ORDER_FIELD_DESCRIPTION'),
		'type' => 'textarea',
		'params' => array('readonly' => ''),
		'value' => isset($arResult['ELEMENT']['DESCRIPTION']) ? $arResult['ELEMENT']['DESCRIPTION'] : '',
		'persistent' => true
	);
}
else {
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

$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'section_additional',
	'name' => GetMessage('ORDER_SECTION_ADDITIONAL'),
	'type' => 'section',
	'persistent' => false,

);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'OUT_ADDRESS',
	'name' => GetMessage('ORDER_FIELD_OUT_ADDRESS'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['OUT_ADDRESS']) ? $arResult['ELEMENT']['OUT_ADDRESS'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'PHYSICAL_ADDRESS',
	'name' => GetMessage('ORDER_FIELD_PHYSICAL_ADDRESS'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['PHYSICAL_ADDRESS']) ? $arResult['ELEMENT']['PHYSICAL_ADDRESS'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'LIVE_ADDRESS',
	'name' => GetMessage('ORDER_FIELD_LIVE_ADDRESS'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['LIVE_ADDRESS']) ? $arResult['ELEMENT']['LIVE_ADDRESS'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'OTHER',
	'name' => GetMessage('ORDER_FIELD_OTHER'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['OTHER']) ? $arResult['ELEMENT']['OTHER'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'PROF_EDU',
	'name' => GetMessage('ORDER_FIELD_PROF_EDU'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['PROF_EDU']) ? $arResult['ELEMENT']['PROF_EDU'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'LVL_EDU',
	'name' => GetMessage('ORDER_FIELD_LVL_EDU'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['LVL_EDU']) ? $arResult['ELEMENT']['LVL_EDU'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'NATION',
	'name' => GetMessage('ORDER_FIELD_NATION'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['NATION']) ? $arResult['ELEMENT']['NATION'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'ZIP_CODE',
	'name' => GetMessage('ORDER_FIELD_ZIP_CODE'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['ZIP_CODE']) ? $arResult['ELEMENT']['ZIP_CODE'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'PHYSICALION',
	'name' => GetMessage('ORDER_FIELD_PHYSICALION'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['PHYSICALION']) ? $arResult['ELEMENT']['PHYSICALION'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'BPLACE',
	'name' => GetMessage('ORDER_FIELD_BPLACE'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['BPLACE']) ? $arResult['ELEMENT']['BPLACE'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'SECOND_EDU',
	'name' => GetMessage('ORDER_FIELD_SECOND_EDU'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['SECOND_EDU']) ? $arResult['ELEMENT']['SECOND_EDU'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'CERT_MID_EDU',
	'name' => GetMessage('ORDER_FIELD_CERT_MID_EDU'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['CERT_MID_EDU']) ? $arResult['ELEMENT']['CERT_MID_EDU'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'SERIAL_DIP',
	'name' => GetMessage('ORDER_FIELD_SERIAL_DIP'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['SERIAL_DIP']) ? $arResult['ELEMENT']['SERIAL_DIP'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'NOM_DIP',
	'name' => GetMessage('ORDER_FIELD_NOM_DIP'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['NOM_DIP']) ? $arResult['ELEMENT']['NOM_DIP'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'WHO_DIP',
	'name' => GetMessage('ORDER_FIELD_WHO_DIP'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['WHO_DIP']) ? $arResult['ELEMENT']['WHO_DIP'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'WHEN_DIP',
	'name' => GetMessage('ORDER_FIELD_WHEN_DIP'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['WHEN_DIP']) ? $arResult['ELEMENT']['WHEN_DIP'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'END_YEAR',
	'name' => GetMessage('ORDER_FIELD_END_YEAR'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['END_YEAR']) ? $arResult['ELEMENT']['END_YEAR'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'HONORS_DIP',
	'name' => GetMessage('ORDER_FIELD_HONORS_DIP'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['HONORS_DIP']) ? $arResult['ELEMENT']['HONORS_DIP'] : '',
	'persistent' => false
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'ORIGINAL_DIP',
	'name' => GetMessage('ORDER_FIELD_ORIGINAL_DIP'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['ORIGINAL_DIP']) ? $arResult['ELEMENT']['ORIGINAL_DIP'] : '',
	'persistent' => false
);

if($arResult['ELEMENT']['ID']!='' && $arResult['ELEMENT']['SHARED']=='Y') {
	foreach($arResult['FIELDS']['tab_1'] as $fID=>$field) {
		if (!in_array($field['id'], array('ID', 'SHARED', 'DESCRIPTION',
			'MODIFY_DATE', 'MODIFY_BY', 'section_physical_info', 'section_additional'))
		)
			$arResult['FIELDS']['tab_1'][$fID]['params'] = array('readonly' => '');
		if (in_array($field['id'], array('GENDER', 'BDAY'))) {
			$arResult['FIELDS']['tab_1'][$fID]['type'] = 'text';
		}
	}
}
/*$icnt = count($arResult['FIELDS']['tab_1']);


if (count($arResult['FIELDS']['tab_1']) == $icnt)
	unset($arResult['FIELDS']['tab_1'][$icnt - 1]);*/


$this->IncludeComponentTemplate();

include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/newportal/order.physical/include/nav.php');
?>
