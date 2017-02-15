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
//$COrderPerms = COrderPerms::GetUserPermissions(32750);

$arResult['CURRENT_USER_ID'] = $userID;
$arParams['PATH_TO_REG_LIST'] = OrderCheckPath('PATH_TO_REG_LIST', $arParams['PATH_TO_REG_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_REG_EDIT'] = OrderCheckPath('PATH_TO_REG_EDIT', $arParams['PATH_TO_REG_EDIT'], '/order/reg/edit/#reg_id#');
$arParams['PATH_TO_APP_EDIT'] = OrderCheckPath('PATH_TO_APP_EDIT', $arParams['PATH_TO_APP_EDIT'], '/order/app/edit/#app_id#');
$arParams['PATH_TO_CONTACT_EDIT'] = OrderCheckPath('PATH_TO_CONTACT_EDIT', $arParams['PATH_TO_CONTACT_EDIT'], '/order/contact/edit/#contact_id#');
$arParams['PATH_TO_GROUP_EDIT'] = OrderCheckPath('PATH_TO_GROUP_EDIT', $arParams['PATH_TO_GROUP_EDIT'], '/order/group/edit/#group_id#');
$arParams['PATH_TO_DIRECTION_EDIT'] = OrderCheckPath('PATH_TO_DIRECTION_EDIT', $arParams['PATH_TO_DIRECTION_EDIT'], '/order/direction/edit/#direction_id#');
$arParams['PATH_TO_NOMEN_EDIT'] = OrderCheckPath('PATH_TO_NOMEN_EDIT', $arParams['PATH_TO_NOMEN_EDIT'], '/order/nomen/edit/#nomen_id#');
$arParams['PATH_TO_COURSE_EDIT'] = OrderCheckPath('PATH_TO_COURSE_EDIT', $arParams['PATH_TO_COURSE_EDIT'], '/order/course/edit/#course_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);
$arParams['ELEMENT_ID'] = isset($arParams['ELEMENT_ID']) ? (int)$arParams['ELEMENT_ID'] : 0;

$bRead = $bDelete = $bEdit = false;
$bVarsFromForm = false;

$COrderReg=new COrderReg();

if (!empty($arParams['ELEMENT_ID'])) {
	$arEntityAttr = $COrderPerms->GetEntityAttr('REG', array($arParams['ELEMENT_ID']));
	$bEdit = true;
}
$tree=COrderDirection::GetTree();

$arFields = null;
if ($bEdit)
{
	$arFilter = array(
		'ID' => $arParams['ELEMENT_ID'],
		//'PERMISSION' => 'WRITE'
	);
	$res = COrderReg::GetListEx(array(),$arFilter);
	$arFields=$res->Fetch();
	if ($arFields === false)
	{
		$bEdit = false;
	} else $pDir=COrderReg::GetDirection($arFields,$tree);
}
else
{
	$arFields = array(
		'ID' => 0
	);

	if (isset($_GET['physical_id']))
	{
		$arFields['PHYSICAL_ID'] = intval($_GET['physical_id']);
	}
}

if($bEdit) {
	$bAssigned=(isset($arFields['ASSIGNED_ID']) && $arFields['ASSIGNED_ID']!='')?in_array($arFields['ASSIGNED_ID'],CAccess::GetUserCodesArray($USER->GetID())):false;
	$arEntityAttrTemp=is_array($arEntityAttr[$arParams['ELEMENT_ID']])?
		array_merge($arEntityAttr[$arParams['ELEMENT_ID']],array('STATUS'.$arFields['STATUS'],$pDir)):
		array('STATUS'.$arFields['STATUS'],$pDir);
	$arResult['PERM_EDIT']=$bEdit=$bAssigned || $COrderPerms->CheckEnityAccess('REG', 'EDIT', $arEntityAttrTemp);
	$arResult['PERM_READ']=$bRead=$bAssigned || $COrderPerms->CheckEnityAccess('REG', 'READ', $arEntityAttrTemp);
	$arResult['PERM_DELETE']=$bDelete=$bAssigned || $COrderPerms->CheckEnityAccess('REG', 'DELETE', $arEntityAttrTemp);
} else {
	$bAdd=!$COrderPerms->HavePerm('REG', BX_ORDER_PERM_NONE, 'ADD') && empty($arParams['ELEMENT_ID']);
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



$isExternal = ($bEdit || $bRead) && isset($arFields['ORIGINATOR_ID']) && isset($arFields['ORIGIN_ID']) && intval($arFields['ORIGINATOR_ID']) > 0 && intval($arFields['ORIGIN_ID']) > 0;

$arResult['ELEMENT'] = is_array($arFields) ? $arFields : null;
unset($arFields);



$arResult['FORM_ID'] = !empty($arParams['FORM_ID']) ? $arParams['FORM_ID'] : 'ORDER_REG_EDIT_V12';
$arResult['GRID_ID'] = 'ORDER_REG_EDIT_V12';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid())
{

	$bVarsFromForm = true;
	if(isset($_POST['save']) || isset($_POST['saveAndView']) || isset($_POST['saveAndAdd']) || isset($_POST['apply']))
	{
		$arSrcElement = ($bEdit || $bRead) ? $arResult['ELEMENT'] : array();
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

		if(isset($_POST['ORDER_REG_EDIT_V12_CHANGE_BTN_PHYSICAL_ID']))
		{
			$arFields['PHYSICAL_ID'] = $_POST['ORDER_REG_EDIT_V12_CHANGE_BTN_PHYSICAL_ID'];
		}
		elseif(isset($arSrcElement['PHYSICAL_ID']))
		{
			$arFields['PHYSICAL_ID'] = $arSrcElement['PHYSICAL_ID'];
		}

		if(isset($_POST['PAST']))
		{
			$arFields['PAST'] = trim($_POST['PAST']);
		}
		elseif(isset($arSrcElement['PAST']))
		{
			$arFields['PAST'] = $arSrcElement['PAST'];
		}

		if (isset($_POST['PERIOD'])) {
			$arFields['PERIOD'] = trim($_POST['PERIOD']);
		} elseif (isset($arSrcElement['PERIOD'])) {
			$arFields['PERIOD'] = $arSrcElement['PERIOD'];
		}

		if(isset($_POST['STATUS']))
		{
			$arFields['STATUS'] = trim($_POST['STATUS']);
		}
		elseif(isset($arSrcElement['STATUS']))
		{
			$arFields['STATUS'] = $arSrcElement['STATUS'];
		}

		if(isset($_POST['ORDER_REG_EDIT_V12_CHANGE_BTN_ENTITY_ID']))
		{
			$arFields['ENTITY_ID'] = $_POST['ORDER_REG_EDIT_V12_CHANGE_BTN_ENTITY_ID'];
		}
		elseif(isset($arSrcElement['ENTITY_ID']))
		{
			$arFields['ENTITY_ID'] = $arSrcElement['ENTITY_ID'];
		}

		if(isset($_POST['ORDER_REG_EDIT_V12_CHANGE_BTN_ENTITY_ID_ENTITY_TYPE']))
		{
			$arFields['ENTITY_TYPE'] = strtoupper($_POST['ORDER_REG_EDIT_V12_CHANGE_BTN_ENTITY_ID_ENTITY_TYPE']);
		}
		elseif(isset($arSrcElement['ENTITY_TYPE']))
		{
			$arFields['ENTITY_TYPE'] = strtoupper($arSrcElement['ENTITY_TYPE']);
		}

		$ID = isset($arResult['ELEMENT']['ID']) ? $arResult['ELEMENT']['ID'] : '';

		if($ID!='') {
			if(!$COrderReg->Update($ID, $arFields))
				$arResult['ERROR_MESSAGE']=$COrderReg->LAST_ERROR;
		}
		else {
			if(isset($_POST['ORDER_REG_EDIT_V12_CHANGE_BTN_APP_ID']))
			{
				$arFields['APP_ID'] = $_POST['ORDER_REG_EDIT_V12_CHANGE_BTN_APP_ID'];
			}
			elseif(isset($arSrcElement['APP_ID']))
			{
				$arFields['APP_ID'] = $arSrcElement['APP_ID'];
			}

			if(isset($_POST['SHARED']))
			{
				$arFields['SHARED'] = trim($_POST['SHARED']);
			}
			elseif(isset($arSrcElement['SHARED']))
			{
				$arFields['SHARED'] = $arSrcElement['SHARED'];
			}

			$arFields['ID']=COrderHelper::GetNewID();
			if(!$COrderReg->Add($arFields))
				$arResult['ERROR_MESSAGE']=$COrderReg->LAST_ERROR;

		}




		if (!empty($arResult['ERROR_MESSAGE']))
		{
			ShowError($arResult['ERROR_MESSAGE']);
		}
		else
		{

			if (isset($_POST['apply']))
			{
				//if (COrderReg::CheckUpdatePermission($ID))
				//{
					LocalRedirect(
						CComponentEngine::MakePathFromTemplate(
							$arParams['PATH_TO_REG_EDIT'],
							array('reg_id' => $ID)
						)
					);
				//}
			}
			elseif (isset($_POST['saveAndAdd']))
			{
				LocalRedirect(
					CComponentEngine::MakePathFromTemplate(
						$arParams['PATH_TO_REG_EDIT'],
						array('reg_id' => 0)
					)
				);
			}
			

			// save
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_REG_LIST'], array()));
		}
	}
}
elseif (isset($_GET['delete']) && check_bitrix_sessid())
{
	if ($bDelete)
	{
		if(!$COrderReg->Delete($arResult['ELEMENT']['ID']))
			$arResult['ERROR_MESSAGE']=$COrderReg->LAST_ERROR;


		if(!empty($arResult['ERROR_MESSAGE'])) {
			ShowError($arResult['ERROR_MESSAGE']);
		} else {
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_REG_LIST']));
			return;
		}
	}
	else
	{
		ShowError(GetMessage('ORDER_DELETE_ERROR'));
		return;
	}
}

$arResult['BACK_URL'] = $arParams['PATH_TO_REG_LIST'];
$arResult['STATUS_LIST']=COrderHelper::GetEnumList('REG',"STATUS");
$arResult['PAST_LIST']=COrderHelper::GetEnumList('REG',"PAST");
$arResult['EDIT'] = $bEdit;

$arResult['FIELDS'] = array();


$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'section_reg_info',
	'name' => GetMessage('ORDER_SECTION_REG_INFO'),
	'type' => 'section'
);
/*$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'TITLE',
	'name' => GetMessage('ORDER_FIELD_TITLE_REG'),
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
		'params' => array('readonly'=>"readonly"),
		'persistent' => true
	);
	if($arResult['ELEMENT']['SHARED']=='N') {
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'APP_ID',
			'name' => GetMessage('ORDER_FIELD_APP_ID'),
			'type' => 'link',
			'componentParams' => array(
				'HREF' => CComponentEngine::makePathFromTemplate($arParams['PATH_TO_APP_EDIT'], array(
					'app_id' => $arResult['ELEMENT']['APP_ID']
				)),
				'VALUE' => '[' . $arResult['ELEMENT']['APP_ID'] . ']',
				//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
			),
			//'params' => array('readonly'=>"readonly"),
			'persistent' => true
		);
	}
}
else {
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'APP_ID',
		'name' => GetMessage('ORDER_FIELD_APP_ID'),
		'type' => 'order_entity_selector',
		'componentParams' => array(
			'ENTITY_TYPE' => 'APP',
			'INPUT_NAME' => 'APP_ID',
			'NEW_INPUT_NAME' => 'NEW_APP_ID',
			'INPUT_VALUE' => '',
			'FORM_NAME' => $arResult['FORM_ID'],
			'MULTIPLE' => 'N',
			//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
		),
		'persistent' => true
	);
}


if($arResult['ELEMENT']['SHARED']=='Y' || $onlyRead) {
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'PHYSICAL_ID',
		'name' => GetMessage('ORDER_FIELD_PHYSICAL_ID'),
		'type' => 'link',
		'componentParams' => array(
			'HREF' => CComponentEngine::makePathFromTemplate($arParams['PATH_TO_PHYSICAL_EDIT'],array(
				'physical_id' => $arResult['ELEMENT']['PHYSICAL_ID']
			)),
			'VALUE' => $arResult['ELEMENT']['PHYSICAL_FULL_NAME'],
			//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
		),
		//'params' => array('readonly'=>"readonly"),
		'persistent' => true
	);
}
else {
	if($arResult['ELEMENT']['ID']!='' && $arResult['ELEMENT']['PHYSICAL_ID']!=='') {
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'PHYSICAL_ID',
			'name' => GetMessage('ORDER_FIELD_PHYSICAL_ID'),
			'type' => 'order_entity_selector',
			'componentParams' => array(
				'ENTITY_TYPE' => 'PHYSICAL',
				'INPUT_NAME' => 'PHYSICAL_ID',
				'INPUT_VALUE' => isset($arResult['ELEMENT']['PHYSICAL_ID']) ? $arResult['ELEMENT']['PHYSICAL_ID'] : '',
				'FORM_NAME' => $arResult['FORM_ID'],
				'MULTIPLE' => 'N',
				//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
			),
			'persistent' => true
		);
	}
	else {
		$arResult['FIELDS']['tab_1'][] = array(
			'id' => 'PHYSICAL_ID',
			'name' => GetMessage('ORDER_FIELD_PHYSICAL_ID'),
			'type' => 'order_entity_add',
			'componentParams' => array(
				'ENTITY_TYPE' => 'PHYSICAL',
				'INPUT_NAME' => 'PHYSICAL_ID',
				'NEW_INPUT_NAME' => 'NEW_PHYSICAL_ID',
				'INPUT_VALUE' => array(
					'FULL_NAME'=>$arResult['ELEMENT']['PHYSICAL_FULL_NAME'],
				),
				'FORM_NAME' => $arResult['FORM_ID'],
				'MULTIPLE' => 'N',
				//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
			),
			'persistent' => true
		);
	}
}

if($arResult['ELEMENT']['SHARED']=='Y' || $onlyRead) {
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'ENTITY_ID',
		'name' => GetMessage('ORDER_FIELD_ENTITY_ID'),
		'type' => 'link',
		'componentParams' => array(
			'HREF' => CComponentEngine::makePathFromTemplate($arParams['PATH_TO_'.$arResult['ELEMENT']['ENTITY_TYPE'].'_EDIT'],array(
				strtolower($arResult['ELEMENT']['ENTITY_TYPE']).'_id' => $arResult['ELEMENT']['ENTITY_ID']
			)),
			'VALUE' => $arResult['ELEMENT']['ENTITY_TITLE'].' | '.GetMessage('ORDER_FIELD_ENTITY_'.$arResult['ELEMENT']['ENTITY_TYPE']),
			//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
		),
		//'params' => array('readonly'=>"readonly"),
		'persistent' => true
	);
}
else {
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'ENTITY_ID',
		'name' => GetMessage('ORDER_FIELD_ENTITY_ID'),
		'type' => 'order_entity_selector',
		'componentParams' => array(
			'ENTITY_TYPE' => array('DIRECTION', 'NOMEN', 'GROUP', 'FORMED_GROUP'),
			'INPUT_NAME' => 'ENTITY_ID',
			'NEW_INPUT_NAME' => 'NEW_ENTITY_ID',
			'INPUT_VALUE' => isset($arResult['ELEMENT']['ENTITY_ID']) ?
				$arResult['ELEMENT']['ENTITY_TYPE'].'#_#'.$arResult['ELEMENT']['ENTITY_ID'] : '',
			'FORM_NAME' => $arResult['FORM_ID'],
			'MULTIPLE' => 'N',
			//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
		),
		'persistent' => true
	);
}

$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'PAST',
	'name' => GetMessage('ORDER_FIELD_PAST'),
	'items' => $arResult['PAST_LIST'],
	'type' => 'checkbox',
	'value' => (isset($arResult['ELEMENT']['PAST']) ? $arResult['ELEMENT']['PAST']:'N'),
	'params' => ($arResult['ELEMENT']['SHARED']=='Y' || $onlyRead)?array('disabled'=>"disabled"):null,
	'persistent' => true
);

$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'STATUS',
	'name' => GetMessage('ORDER_FIELD_STATUS'),
	'items' => $arResult['STATUS_LIST'],
	'type' => 'list',
	'value' => (isset($arResult['ELEMENT']['STATUS']) ? $arResult['ELEMENT']['STATUS'] : ''),
	'params' => ($arResult['ELEMENT']['SHARED']=='Y' || $onlyRead)?array('disabled'=>"disabled"):null,
	'persistent' => true
);


if($arResult['ELEMENT']['PERIOD']!='') {
	$periodClasses = '';
	if (MakeTimeStamp($arResult['ELEMENT']['PERIOD']) <= MakeTimeStamp(ConvertTimeStamp()))
		$periodClasses .= 'order-list-today ';
	if (MakeTimeStamp($arResult['ELEMENT']['PERIOD']) < MakeTimeStamp(ConvertTimeStamp()))
		$periodClasses .= 'order-list-time-expired ';
}
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'PERIOD',
	'name' => GetMessage('ORDER_FIELD_PERIOD'),
	'type' => ($arResult['ELEMENT']['SHARED']=='Y' || $onlyRead)?'label':'date_short',
	'value' => isset($arResult['ELEMENT']['PERIOD']) ? ConvertTimeStamp(MakeTimeStamp($arResult['ELEMENT']['PERIOD']), 'SHORT', SITE_ID) : '',
	'params' => ($arResult['ELEMENT']['SHARED']=='Y' || $onlyRead)?array('readonly'=>"readonly"):null,
	'classes' => ($arResult['ELEMENT']['SHARED']=='Y' || $onlyRead)?'':$periodClasses,
	'persistent' => true
);




if($arResult['ELEMENT']['SHARED']!='Y' && !$onlyRead) {
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
else {
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'DESCRIPTION',
		'name' => GetMessage('ORDER_FIELD_DESCRIPTION'),
		'type' => 'textarea',
		'params' => array('readonly' => ''),
		'value' => isset($arResult['ELEMENT']['DESCRIPTION']) ? $arResult['ELEMENT']['DESCRIPTION'] : '',
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

	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'SHARED',
		'name' => GetMessage('ORDER_FIELD_SHARED'),
		'type' => 'checkbox',
		'value' => (isset($arResult['ELEMENT']['SHARED']) ? $arResult['ELEMENT']['SHARED']:'N'),
		'params' => array('disabled'=>"disabled"),
		'persistent' => true
	);
} else {
	$arResult['FIELDS']['tab_1'][] = array(
		'id' => 'SHARED',
		'type' => 'hidden',
		'value' => 'N',
	);
}

/*$arResult['RESPONSIBLE_SELECTOR_PARAMS'] = array(
	'NAME' => 'order_reg_edit_resonsible',
	'INPUT_NAME' => 'ASSIGNED_BY_ID',
	'SEARCH_INPUT_NAME' => 'ASSIGNED_BY_NAME',
	'NAME_TEMPLATE' => $arParams['NAME_TEMPLATE']
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'ASSIGNED_BY_ID',
	'componentParams' => $arResult['RESPONSIBLE_SELECTOR_PARAMS'],
	'name' => GetMessage('ORDER_FIELD_ASSIGNED_BY_ID'),
	'type' => 'text',//'intranet_user_search',
	'params' => array('readonly'=>"readonly"),
	'value' => isset($arResult['ELEMENT']['ASSIGNED_BY_ID']) ? $arResult['ELEMENT']['ASSIGNED_BY_ID'] : $USER->GetID()
);*/


/*$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'section_contact_info',
	'name' => GetMessage('ORDER_SECTION_CONTACT_INFO2'),
	'type' => 'section'
);*/
/*if (CCrmContact::CheckReadPermission())
{*/


//}

/*if ($bTaxMode)
{
	// CLIENT LOCATION
	$sLocationHtml = '';
	ob_start();

	$locValue = isset($arResult['ELEMENT']['LOCATION_ID']) ? $arResult['ELEMENT']['LOCATION_ID'] : '';
	CSaleLocation::proxySaleAjaxLocationsComponent(
		array(
			'AJAX_CALL' => 'N',
			'COUNTRY_INPUT_NAME' => 'LOC_COUNTRY',
			'REGION_INPUT_NAME' => 'LOC_REGION',
			'CITY_INPUT_NAME' => 'LOC_CITY',
			'CITY_OUT_LOCATION' => 'Y',
			'LOCATION_VALUE' => $locValue,
			'ORDER_PROPS_ID' => 'DEAL_'.$arResult['ELEMENT']['ID'],
			'ONCITYCHANGE' => 'BX.onCustomEvent(\'CrmProductRowSetLocation\', [\'LOC_CITY\']);',
			'SHOW_QUICK_CHOOSE' => 'N',
			//'SIZE1' => $arProperties['SIZE1']
		),
		array(
			"CODE" => $locValue,
			"ID" => "",
			"PROVIDE_LINK_BY" => "code",
			"JS_CALLBACK" => 'CrmProductRowSetLocation'
		),
		'popup'
	);
	$sLocationHtml = ob_get_contents();
	ob_end_clean();
	$locationField = array(
		'id' => 'LOCATION_ID',
		'name' => GetMessage('CRM_DEAL_FIELD_LOCATION_ID'),
		'type' => 'custom',
		'value' =>  $sLocationHtml.
			'<div>
				<span class="bx-crm-edit-content-block-element-name">&nbsp;</span>'.
			'<span class="bx-crm-edit-content-location-description">'.
			GetMessage('CRM_DEAL_FIELD_LOCATION_ID_DESCRIPTION').
			'</span>'.
			'</div>',
		'required' => true
	);
	$arResult['FIELDS']['tab_1'][] = $locationField;
	$arResult['FORM_FIELDS_TO_ADD']['LOCATION_ID'] = $locationField;
	unset($locationField);
}*/




/*$icnt = count($arResult['FIELDS']['tab_1']);


if (count($arResult['FIELDS']['tab_1']) == $icnt)
	unset($arResult['FIELDS']['tab_1'][$icnt - 1]);*/


$this->IncludeComponentTemplate();

include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/newportal/order.reg/include/nav.php');
?>
