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
$arParams['PATH_TO_AGENT_LIST'] = OrderCheckPath('PATH_TO_AGENT_LIST', $arParams['PATH_TO_AGENT_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_AGENT_EDIT'] = OrderCheckPath('PATH_TO_AGENT_EDIT', $arParams['PATH_TO_AGENT_EDIT'], '/order/agent/edit/#agent_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);
$arParams['ELEMENT_ID'] = isset($arParams['ELEMENT_ID']) ? $arParams['ELEMENT_ID'] : '';

$bRead = $bDelete = $bEdit = false;
$bVarsFromForm = false;

$COrderAgent=new COrderAgent();

if (!empty($arParams['ELEMENT_ID'])) {
	$arEntityAttr = $COrderPerms->GetEntityAttr('AGENT', array($arParams['ELEMENT_ID']));
	$bEdit = true;
}

$arFields = null;
if ($bEdit)
{
	$arFilter = array(
		'ID' => $arParams['ELEMENT_ID'],
		//'PERMISSION' => 'WRITE'
	);
	$res = COrderAgent::GetListEx(array(),$arFilter);
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

	if (isset($_GET['agent_id']))
	{
		$arFields['AGENT_ID'] =$_GET['agent_id'];
	}
}

if($bEdit) {
	$arResult['PERM_EDIT']=$bEdit=$COrderPerms->CheckEnityAccess('AGENT', 'EDIT', $arEntityAttr[$arParams['ELEMENT_ID']]);
	$arResult['PERM_READ']=$bRead=$COrderPerms->CheckEnityAccess('AGENT', 'READ', $arEntityAttr[$arParams['ELEMENT_ID']]);
	$arResult['PERM_DELETE']=$bDelete=$COrderPerms->CheckEnityAccess('AGENT', 'DELETE', $arEntityAttr[$arParams['ELEMENT_ID']]);
} else {
	$bAdd=!$COrderPerms->HavePerm('AGENT', BX_ORDER_PERM_NONE, 'ADD') && empty($arParams['ELEMENT_ID']);
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

$isExternal = $bEdit && isset($arFields['ORIGINATOR_ID']) && isset($arFields['ORIGIN_ID']) && $arFields['ORIGINATOR_ID'] !="" && $arFields['ORIGIN_ID'] !="";

$arResult['ELEMENT'] = is_array($arFields) ? $arFields : null;
unset($arFields);



$arResult['FORM_ID'] = !empty($arParams['FORM_ID']) ? $arParams['FORM_ID'] : 'ORDER_AGENT_EDIT_V12';
$arResult['GRID_ID'] = 'ORDER_AGENT_EDIT_V12';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid())
{

	$bVarsFromForm = true;
	if(isset($_POST['save']) || isset($_POST['saveAndView']) || isset($_POST['saveAndAdd']) || isset($_POST['apply']))
	{
		$arSrcElement = $bEdit ? $arResult['ELEMENT'] : array();
		$arFields = array();

		$ID = isset($arResult['ELEMENT']['ID']) ? $arResult['ELEMENT']['ID'] : '';

		if(isset($_POST['LEGAL']) && ($_POST['LEGAL']=='Y' || $_POST['LEGAL']=='N'))
			$arFields['LEGAL'] = $_POST['LEGAL'];
		elseif(isset($arSrcElement['LEGAL']))
			$arFields['LEGAL'] = $arSrcElement['LEGAL'];
		else
			ShowError(GetMessage('ORDER_ERROR_LEGAL'));

		if($arFields['LEGAL']=='Y') {
			if(isset($_POST['TITLE']))
				$arFields['TITLE'] = trim($_POST['TITLE']);
			elseif(isset($arSrcElement['TITLE']))
				$arFields['TITLE'] = $arSrcElement['TITLE'];

			if(isset($_POST['ORDER_PERSON_CONTACT_GUID_VALUE']))
				$arFields['CONTACT_GUID'] = $_POST['ORDER_PERSON_CONTACT_GUID_VALUE'];
			elseif(isset($arSrcElement['CONTACT_GUID']))
				$arFields['CONTACT_GUID'] = $arSrcElement['CONTACT_GUID'];

			if(isset($_POST['CONTACT_START_DATE']))
				$arFields['CONTACT_START_DATE'] = $_POST['CONTACT_START_DATE'];
			elseif(isset($arSrcElement['CONTACT_START_DATE']))
				$arFields['CONTACT_START_DATE'] = $arSrcElement['CONTACT_START_DATE'];

			if(isset($_POST['LEGAL_PHONE']))
				$arFields['LEGAL_PHONE'] = trim($_POST['LEGAL_PHONE']);
			elseif(isset($arSrcElement['LEGAL_PHONE']))
				$arFields['LEGAL_PHONE'] = $arSrcElement['LEGAL_PHONE'];

			if(isset($_POST['LEGAL_EMAIL']))
				$arFields['LEGAL_EMAIL'] = trim($_POST['LEGAL_EMAIL']);
			elseif(isset($arSrcElement['LEGAL_EMAIL']))
				$arFields['LEGAL_EMAIL'] = $arSrcElement['LEGAL_EMAIL'];
		} else {
			if(isset($_POST[$arResult['FORM_ID'].'_CHANGE_BTN_ID']))
				$arFields['ID'] = $_POST[$arResult['FORM_ID'].'_CHANGE_BTN_ID'];
			elseif(isset($arSrcElement['ID']))
				$arFields['ID'] = $arSrcElement['ID'];
		}

		/*if(isset($_POST['DESCRIPTION']))
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
		}*/





		if($arResult['ELEMENT']['ID']!='') {
			if(!$COrderAgent->Update($ID, $arFields))
				$arResult['ERROR_MESSAGE']=$COrderAgent->LAST_ERROR;
		}
		else {
			if($arFields['LEGAL']=='Y')
				$arFields['ID']=COrderHelper::GetLegalID($arFields['TITLE']);
			if(!$COrderAgent->Add($arFields))
				$arResult['ERROR_MESSAGE']=$COrderAgent->LAST_ERROR;

		}



		if (!empty($arResult['ERROR_MESSAGE']))
		{
			ShowError($arResult['ERROR_MESSAGE']);
		}
		else
		{

			if (isset($_POST['apply']))
			{
				//if (COrderAgent::CheckUpdatePermission($ID))
				//{
					LocalRedirect(
						CComponentEngine::MakePathFromTemplate(
							$arParams['PATH_TO_AGENT_EDIT'],
							array('agent_id' => $ID)
						)
					);
				//}
			}
			elseif (isset($_POST['saveAndAdd']))
			{
				LocalRedirect(
					CComponentEngine::MakePathFromTemplate(
						$arParams['PATH_TO_AGENT_EDIT'],
						array('agent_id' => 0)
					)
				);
			}
			

			// save
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_AGENT_LIST'], array()));
		}
	}
}
elseif (isset($_GET['delete']) && check_bitrix_sessid())
{
	if ($bEdit)
	{
		if(!$COrderAgent->Delete($arResult['ELEMENT']['ID']))
			$arResult['ERROR_MESSAGE']=$COrderAgent->LAST_ERROR;


		if(!empty($arResult['ERROR_MESSAGE'])) {
			ShowError($arResult['ERROR_MESSAGE']);
		} else {
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_AGENT_LIST']));
			return;
		}
	}
	else
	{
		ShowError(GetMessage('ORDER_DELETE_ERROR'));
		return;
	}
}

if(isset($_GET['legal']) && ($_GET['legal']=='Y' || $_GET['legal']=='N'))
	$arResult['ELEMENT']['LEGAL']=$_GET['legal'];

$arResult['BACK_URL'] = $arParams['PATH_TO_AGENT_LIST'];
$arResult['EDIT'] = $bEdit;

$arResult['FIELDS'] = array();


$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'section_agent_info',
	'name' => GetMessage('ORDER_SECTION_AGENT_INFO'),
	'type' => 'section'
);
/*$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'TITLE',
	'name' => GetMessage('ORDER_FIELD_TITLE_AGENT'),
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
	/*$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'SHARED',
		'name' => GetMessage('ORDER_FIELD_SHARED'),
		'type' => 'checkbox',
		'value' => (isset($arResult['ELEMENT']['SHARED']) ? $arResult['ELEMENT']['SHARED']:'N'),
		'params' => array('disabled'=>"disabled"),
		'persistent' => true
	);*/
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'LEGAL',
		'name' => GetMessage('ORDER_FIELD_LEGAL'),
		'type' => 'text',
		'value' => isset($arResult['ELEMENT']['LEGAL']) ? GetMessage('ORDER_LEGAL_'.$arResult['ELEMENT']['LEGAL']) : '',
		'params'=>array('readonly'=>'readonly'),
		'persistent' => true
	);
} else {
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'LEGAL',
		'name' => GetMessage('ORDER_FIELD_LEGAL'),
		'type' => 'list',
		'value' => isset($arResult['ELEMENT']['LEGAL']) ? $arResult['ELEMENT']['LEGAL'] : '',
		'items'=>array(
			'N'=>GetMessage('ORDER_LEGAL_N'),
			'Y'=>GetMessage('ORDER_LEGAL_Y')
		),
		'persistent' => true
	);
}

if($arResult['ELEMENT']['LEGAL']=='Y') {
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'TITLE',
		'name' => GetMessage('ORDER_FIELD_TITLE'),
		'type' => 'text',
		'value' => isset($arResult['ELEMENT']['TITLE']) ? $arResult['ELEMENT']['TITLE'] : '',
		'params'=>false,
		'persistent' => true
	);
	if($arResult['ELEMENT']['ID']!='') {
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'FULL_TITLE',
			'name' => GetMessage('ORDER_FIELD_FULL_TITLE'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['FULL_TITLE']) ? $arResult['ELEMENT']['FULL_TITLE'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'INN',
			'name' => GetMessage('ORDER_FIELD_INN'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['INN']) ? $arResult['ELEMENT']['INN'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'KPP',
			'name' => GetMessage('ORDER_FIELD_KPP'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['KPP']) ? $arResult['ELEMENT']['KPP'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'CODE_PO',
			'name' => GetMessage('ORDER_FIELD_CODE_PO'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['CODE_PO']) ? $arResult['ELEMENT']['CODE_PO'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);

		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'CONTACT_GUID',
			'name' => GetMessage('ORDER_FIELD_CONTACT_FULL_NAME'),
			'type' => 'order_person_selector',
			'componentParams' => array(
				'TYPE' => 'physical',
				'ID' => 'CONTACT_GUID',
				'SELECTED' => array(
					'ID' => $arResult['ELEMENT']['CONTACT_GUID'],
					'FULL_NAME' => $arResult['ELEMENT']['CONTACT_FULL_NAME'],
					'PHONE' => $arResult['ELEMENT']['CONTACT_PHONE'],
					'EMAIL' => $arResult['ELEMENT']['CONTACT_EMAIL'],
				),
				'READONLY'=>$arResult['ELEMENT']['CONTACT_SHARED']=='Y',
				'SHOW'=>array(
					'TYPE'=>'contact',
					'ID'=>$arResult['ELEMENT']['CONTACT_ID']
				)
			),
			'persistent' => true
		);

		/*$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'CONTACT_PHONE',
			'name' => GetMessage('ORDER_FIELD_CONTACT_PHONE'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['CONTACT_PHONE']) ? $arResult['ELEMENT']['CONTACT_PHONE'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'CONTACT_EMAIL',
			'name' => GetMessage('ORDER_FIELD_CONTACT_EMAIL'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['CONTACT_EMAIL']) ? $arResult['ELEMENT']['CONTACT_EMAIL'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);*/
	} else {
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'CONTACT_GUID',
			'name' => GetMessage('ORDER_FIELD_CONTACT_FULL_NAME'),
			'type' => 'order_entity_add',
			'componentParams' => array(
				'ENTITY_TYPE' => 'PHYSICAL',
				'INPUT_NAME' => 'CONTACT_GUID',
				'NEW_INPUT_NAME' => 'NEW_CONTACT_GUID',
				'INPUT_VALUE' => '',
				'FORM_NAME' => $arResult['FORM_ID'],
				'MULTIPLE' => 'N',
				//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
			),
			'persistent' => true
		);
	}
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'CONTACT_START_DATE',
		'type' => 'hidden',
		'value' => isset($arResult['ELEMENT']['CONTACT_START_DATE']) ? $arResult['ELEMENT']['CONTACT_START_DATE'] : '',
	);
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'LEGAL_PHONE',
		'name' => GetMessage('ORDER_FIELD_PHONE'),
		'type' => 'text',
		'value' => isset($arResult['ELEMENT']['LEGAL_PHONE']) ? $arResult['ELEMENT']['LEGAL_PHONE'] : '',
		'persistent' => true
	);
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'LEGAL_EMAIL',
		'name' => GetMessage('ORDER_FIELD_EMAIL'),
		'type' => 'text',
		'value' => isset($arResult['ELEMENT']['LEGAL_EMAIL']) ? $arResult['ELEMENT']['LEGAL_EMAIL'] : '',
		'persistent' => true
	);
	if($arResult['ELEMENT']['ID']!='') {
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'LEGAL_SHIP_ADDRESS',
			'name' => GetMessage('ORDER_FIELD_LEGAL_SHIP_ADDRESS'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['LEGAL_SHIP_ADDRESS']) ? $arResult['ELEMENT']['LEGAL_SHIP_ADDRESS'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'LEGAL_MAIL_ADDRESS',
			'name' => GetMessage('ORDER_FIELD_LEGAL_MAIL_ADDRESS'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['LEGAL_MAIL_ADDRESS']) ? $arResult['ELEMENT']['LEGAL_MAIL_ADDRESS'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'LEGAL_FAX',
			'name' => GetMessage('ORDER_FIELD_LEGAL_FAX'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['LEGAL_FAX']) ? $arResult['ELEMENT']['LEGAL_FAX'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'LEGAL_OTHER',
			'name' => GetMessage('ORDER_FIELD_LEGAL_OTHER'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['LEGAL_OTHER']) ? $arResult['ELEMENT']['LEGAL_OTHER'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'FACT_ADDRESS',
			'name' => GetMessage('ORDER_FIELD_FACT_ADDRESS'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['FACT_ADDRESS']) ? $arResult['ELEMENT']['FACT_ADDRESS'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'LEGAL_ADDRESS',
			'name' => GetMessage('ORDER_FIELD_LEGAL_ADDRESS'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['LEGAL_ADDRESS']) ? $arResult['ELEMENT']['LEGAL_ADDRESS'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);
		/*$arResult['FIELDS']['tab_1'][] = array(
            'id' => 'DESCRIPTION',
            'name' => GetMessage('ORDER_FIELD_DESCRIPTION'),
            'type' => 'textarea',
            'params' => array('readonly' => ''),
            'value' => isset($arResult['ELEMENT']['DESCRIPTION']) ? $arResult['ELEMENT']['DESCRIPTION'] : '',
            'persistent' => true
        );*/
	}
} else {
	if($arResult['ELEMENT']['ID']=='') {
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'ID',
			'name' => GetMessage('ORDER_FIELD_GUID'),
			'type' => 'order_entity_selector',
			'componentParams' => array(
				'ENTITY_TYPE' => 'PHYSICAL',
				'INPUT_NAME' => 'ID',
				'NEW_INPUT_NAME' => 'NEW_ID',
				'INPUT_VALUE' => isset($arResult['ELEMENT']['ID']) ? $arResult['ELEMENT']['ID'] : '',
				'FORM_NAME' => $arResult['FORM_ID'],
				'MULTIPLE' => 'N',
				//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
			),
			'persistent' => true
		);
	} else {
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'ID',
			'name' => GetMessage('ORDER_FIELD_GUID'),
			'type' => 'link',
			'componentParams' => array(
				'HREF' => CComponentEngine::makePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'], array(
					'physical_id' => $arResult['ELEMENT']['ID']
				)),
				'VALUE' => $arResult['ELEMENT']['TITLE'],
				//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
			),
			'persistent' => true
		);
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'INN',
			'name' => GetMessage('ORDER_FIELD_INN'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['INN']) ? $arResult['ELEMENT']['INN'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'KPP',
			'name' => GetMessage('ORDER_FIELD_KPP'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['KPP']) ? $arResult['ELEMENT']['KPP'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);


		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'PHONE',
			'name' => GetMessage('ORDER_FIELD_PHONE'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['PHONE']) ? $arResult['ELEMENT']['PHONE'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'EMAIL',
			'name' => GetMessage('ORDER_FIELD_EMAIL'),
			'type' => 'text',
			'value' => isset($arResult['ELEMENT']['EMAIL']) ? $arResult['ELEMENT']['EMAIL'] : '',
			'params' => array('readonly' => 'readonly'),
			'persistent' => true
		);
	}
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

include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/newportal/order.agent/include/nav.php');
?>
