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
$arParams['PATH_TO_COURSE_LIST'] = OrderCheckPath('PATH_TO_COURSE_LIST', $arParams['PATH_TO_COURSE_LIST'], $APPLICATION->GetCurPage());
$arParams['PATH_TO_COURSE_EDIT'] = OrderCheckPath('PATH_TO_COURSE_EDIT', $arParams['PATH_TO_COURSE_EDIT'], '/order/course/edit/#course_id#');
$arParams['PATH_TO_NOMEN_EDIT'] = OrderCheckPath('PATH_TO_NOMEN_EDIT', $arParams['PATH_TO_NOMEN_EDIT'], '/order/nomen/edit/#nomen_id#');
$arParams['PATH_TO_PHYSICAL_EDIT'] = OrderCheckPath('PATH_TO_PHYSICAL_EDIT', $arParams['PATH_TO_PHYSICAL_EDIT'], '/order/physical/edit/#physical_id#');
$arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);
$arParams['ELEMENT_ID'] = isset($arParams['ELEMENT_ID']) ? $arParams['ELEMENT_ID'] : '';

$bRead = $bDelete = $bEdit = false;
$bVarsFromForm = false;

$COrderCourse=new COrderCourse();

if (!empty($arParams['ELEMENT_ID'])) {
	$arEntityAttr = $COrderPerms->GetEntityAttr('COURSE', array($arParams['ELEMENT_ID']));
	$bEdit = true;
}

$arFields = null;
if ($bEdit)
{
	$arFilter = array(
		'ID' => $arParams['ELEMENT_ID'],
		//'PERMISSION' => 'WRITE'
	);
	$res = COrderCourse::GetListEx(array(),$arFilter);
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

	if (isset($_GET['course_id']))
	{
		$arFields['COURSE_ID'] =$_GET['course_id'];
	}
}

if($bEdit) {
	$arResult['PERM_EDIT']=$bEdit=$COrderPerms->CheckEnityAccess('COURSE', 'EDIT', $arEntityAttr[$arParams['ELEMENT_ID']]);
	$arResult['PERM_READ']=$bRead=$COrderPerms->CheckEnityAccess('COURSE', 'READ', $arEntityAttr[$arParams['ELEMENT_ID']]);
	$arResult['PERM_DELETE']=$bDelete=$COrderPerms->CheckEnityAccess('COURSE', 'DELETE', $arEntityAttr[$arParams['ELEMENT_ID']]);
} else {
	$bAdd=!$COrderPerms->HavePerm('COURSE', BX_ORDER_PERM_NONE, 'ADD') && empty($arParams['ELEMENT_ID']);
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



$arResult['FORM_ID'] = !empty($arParams['FORM_ID']) ? $arParams['FORM_ID'] : 'ORDER_COURSE_EDIT_V12';
$arResult['GRID_ID'] = 'ORDER_COURSE_EDIT_V12';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid())
{

	$bVarsFromForm = true;
	if(isset($_POST['save']) || isset($_POST['saveAndView']) || isset($_POST['saveAndAdd']) || isset($_POST['apply']))
	{
		$arSrcElement = $bEdit ? $arResult['ELEMENT'] : array();
		$arFields = array();


		if(isset($_POST['ANNOTATION']))
		{
			$comments = isset($_POST['ANNOTATION']) ? trim($_POST['ANNOTATION']) : '';
			if($comments !== '' && strpos($comments, '<') !== false)
			{
				$sanitizer = new CBXSanitizer();
				$sanitizer->ApplyDoubleEncode(false);
				$sanitizer->SetLevel(CBXSanitizer::SECURE_LEVEL_MIDDLE);
				//Crutch for for Chrome line break behaviour in HTML editor.
				$sanitizer->AddTags(array('div' => array()));
				$comments = $sanitizer->SanitizeHtml($comments);
			}
			$arFields['ANNOTATION'] = $comments;
		}

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

		if(isset($_POST['COURSE_PROG']))
		{
			$comments = isset($_POST['COURSE_PROG']) ? trim($_POST['COURSE_PROG']) : '';
			if($comments !== '' && strpos($comments, '<') !== false)
			{
				$sanitizer = new CBXSanitizer();
				$sanitizer->ApplyDoubleEncode(false);
				$sanitizer->SetLevel(CBXSanitizer::SECURE_LEVEL_MIDDLE);
				//Crutch for for Chrome line break behaviour in HTML editor.
				$sanitizer->AddTags(array('div' => array()));
				$comments = $sanitizer->SanitizeHtml($comments);
			}
			$arFields['COURSE_PROG'] = $comments;
		}

		if(isset($_POST['TITLE']))
			$arFields['TITLE'] = trim($_POST['TITLE']);
		elseif(isset($arSrcElement['TITLE']))
			$arFields['TITLE'] = $arSrcElement['TITLE'];

		if(isset($_POST[$arResult['FORM_ID'].'_CHANGE_BTN_PREV_COURSE']))
			$arFields['PREV_COURSE'] = $_POST[$arResult['FORM_ID'].'_CHANGE_BTN_PREV_COURSE'];
		elseif(isset($arSrcElement['PREV_COURSE']))
			$arFields['PREV_COURSE'] = $arSrcElement['PREV_COURSE'];

		if(isset($_POST['DURATION']))
			$arFields['DURATION'] = $_POST['DURATION'];
		elseif(isset($arSrcElement['DURATION']))
			$arFields['DURATION'] = $arSrcElement['DURATION'];



		$ID = isset($arResult['ELEMENT']['ID']) ? $arResult['ELEMENT']['ID'] : '';

		if($arResult['ELEMENT']['ID']!='') {
			if(!$COrderCourse->Update($ID, $arFields))
				$arResult['ERROR_MESSAGE']=$COrderCourse->LAST_ERROR;
		}
		else {

			$arFields['ID']=COrderHelper::GetNewID();
			if(!$COrderCourse->Add($arFields))
				$arResult['ERROR_MESSAGE']=$COrderCourse->LAST_ERROR;

		}




		if (!empty($arResult['ERROR_MESSAGE']))
		{
			ShowError($arResult['ERROR_MESSAGE']);
		}
		else
		{

			if (isset($_POST['apply']))
			{
				//if (COrderCourse::CheckUpdatePermission($ID))
				//{
					LocalRedirect(
						CComponentEngine::MakePathFromTemplate(
							$arParams['PATH_TO_COURSE_EDIT'],
							array('course_id' => $ID)
						)
					);
				//}
			}
			elseif (isset($_POST['saveAndAdd']))
			{
				LocalRedirect(
					CComponentEngine::MakePathFromTemplate(
						$arParams['PATH_TO_COURSE_EDIT'],
						array('course_id' => 0)
					)
				);
			}
			

			// save
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_COURSE_LIST'], array()));
		}
	}
}
elseif (isset($_GET['delete']) && check_bitrix_sessid())
{
	if ($bEdit)
	{
		if(!$COrderCourse->Delete($arResult['ELEMENT']['ID']))
			$arResult['ERROR_MESSAGE']=$COrderCourse->LAST_ERROR;


		if(!empty($arResult['ERROR_MESSAGE'])) {
			ShowError($arResult['ERROR_MESSAGE']);
		} else {
			LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_COURSE_LIST']));
			return;
		}
	}
	else
	{
		ShowError(GetMessage('ORDER_DELETE_ERROR'));
		return;
	}
}



$arResult['BACK_URL'] = $arParams['PATH_TO_COURSE_LIST'];
$arResult['EDIT'] = $bEdit;

$arResult['FIELDS'] = array();


$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'section_course_info',
	'name' => GetMessage('ORDER_SECTION_COURSE_INFO'),
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
	'id' => 'ANNOTATION',
	'name' => GetMessage('ORDER_FIELD_ANNOTATION'),
	'type' => 'textarea',
	'params' => array('readonly' => ''),
	'value' => isset($arResult['ELEMENT']['ANNOTATION']) ? $arResult['ELEMENT']['ANNOTATION'] : '',
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
	'id' => 'COURSE_PROG',
	'name' => GetMessage('ORDER_FIELD_COURSE_PROG'),
	'type' => 'textarea',
	'params' => array('readonly' => ''),
	'value' => isset($arResult['ELEMENT']['COURSE_PROG']) ? $arResult['ELEMENT']['COURSE_PROG'] : '',
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'DURATION',
	'name' => GetMessage('ORDER_FIELD_DURATION'),
	'type' => 'text',
	'value' => isset($arResult['ELEMENT']['DURATION']) ? $arResult['ELEMENT']['DURATION'] : '',
	'params' => array('readonly' => "readonly"),
	'persistent' => true
);

$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'PREV_COURSE',
	'name' => GetMessage('ORDER_FIELD_PREV_COURSE'),
	'type' => 'link',
	'componentParams' => array(
		'HREF' => CComponentEngine::makePathFromTemplate($arParams['PATH_TO_COURSE_EDIT'], array(
			'course_id' => $arResult['ELEMENT']['PREV_COURSE']
		)),
		'VALUE' => $arResult['ELEMENT']['PREV_COURSE_TITLE'],
		//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
	),
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'NEXT_COURSE',
	'name' => GetMessage('ORDER_FIELD_NEXT_COURSE'),
	'type' => 'link',
	'componentParams' => array(
		'HREF' => CComponentEngine::makePathFromTemplate($arParams['PATH_TO_COURSE_EDIT'], array(
			'course_id' => $arResult['ELEMENT']['NEXT_COURSE']
		)),
		'VALUE' => $arResult['ELEMENT']['NEXT_COURSE_TITLE'],
		//'NAME_TEMPLATE' => \Bitrix\Crm\Format\PersonNameFormatter::getFormat()
	),
	'persistent' => true
);
$res=COrderNomen::GetListEx(array(),array(),false,false,array('ID','TITLE'));
while($el=$res->Fetch()) {
	$arResult["NOMEN_LIST"][$el['ID']]=$el;
}

$res=COrderPhysical::GetListEx(array(),array(),false,false,array('ID','FULL_NAME'));
while($el=$res->Fetch()) {
	$arResult["PHYSICAL_LIST"][$el['ID']]=$el;
}
$arResult['EXAM']=unserialize($arResult['ELEMENT']['EXAM']);
$arResult['LITER']=unserialize($arResult['ELEMENT']['LITER']);
$arResult['DOC']=unserialize($arResult['ELEMENT']['DOC']);
$arResult['NOMEN']=unserialize($arResult['ELEMENT']['NOMEN']);
$arResult['TEACHER']=unserialize($arResult['ELEMENT']['TEACHER']);
$thead='<table class="order-edit-field-table">';
$valExam='<tr><th>'.GetMessage('ORDER_COURSE_EXAM_TITLE').'</th><th width="30%">'.GetMessage('ORDER_COURSE_EXAM_MARK').'</th></tr>';
foreach($arResult['EXAM'] as $num=>$item) {
	$valExam.='<tr><td>'.$item['EXAM_TITLE'].'</td><td>'.$item['EXAM_MARK'].'</td></tr>';
}
$valLiter='<tr><th>'.GetMessage('ORDER_COURSE_LITER_ID').'</th></tr>';
foreach($arResult['LITER'] as $num=>$item) {
	$valLiter.='<tr><td>'.$item['LITER_ID'].'</td></tr>';
}
$valDoc='<tr><th>'.GetMessage('ORDER_COURSE_DOC_TITLE').'</th></tr>';
foreach($arResult['DOC'] as $num=>$item) {
	$valDoc.='<tr><td>'.$item['DOC_TITLE'].'</td></tr>';
}
$valNomen='<tr><th width="25%">'.GetMessage('ORDER_COURSE_NOMEN_ID').'</th><th>'.GetMessage('ORDER_COURSE_NOMEN_TITLE').'</th></tr>';
foreach($arResult['NOMEN'] as $num=>$item) {
	$valNomen.='<tr><td>'.$item['NOMEN_ID'].'</td>';
	$valNomen.='<td><a href="/order/nomen/'.$item['NOMEN_ID'].'/">'.$arResult['NOMEN_LIST'][$item['NOMEN_ID']]['TITLE'].'</a></td></tr>';
}
$valTeacher='<tr><th width="25%">'.GetMessage('ORDER_COURSE_TEACHER_ID').'</th><th>'.GetMessage('ORDER_COURSE_TEACHER_FULL_NAME').'</th></tr>';
foreach($arResult['TEACHER'] as $num=>$item) {
	$valTeacher.='<tr><td>'.$item['TEACHER_ID'].'</td>';
	$valTeacher.='<td><a href="/order/physical/'.$item['TEACHER_ID'].'/">'.$arResult['PHYSICAL_LIST'][$item['TEACHER_ID']]['FULL_NAME'].'</a></td></tr>';
}
$tfoot='</table>';
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'EXAM',
	'name' => GetMessage('ORDER_FIELD_EXAM'),
	'type' => 'custom',
	'value' => $thead.$valExam.$tfoot,
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'LITER',
	'name' => GetMessage('ORDER_FIELD_LITER'),
	'type' => 'custom',
	'value' => $thead.$valLiter.$tfoot,
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'DOC',
	'name' => GetMessage('ORDER_FIELD_DOC'),
	'type' => 'custom',
	'value' => $thead.$valDoc.$tfoot,
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'NOMEN',
	'name' => GetMessage('ORDER_FIELD_NOMEN'),
	'type' => 'custom',
	'value' => $thead.$valNomen.$tfoot,
	'persistent' => true
);
$arResult['FIELDS']['tab_1'][] = array(
	'id' => 'TEACHER',
	'name' => GetMessage('ORDER_FIELD_TEACHER'),
	'type' => 'custom',
	'value' => $thead.$valTeacher.$tfoot,
	'persistent' => true
);
/*$icnt = count($arResult['FIELDS']['tab_1']);


if (count($arResult['FIELDS']['tab_1']) == $icnt)
	unset($arResult['FIELDS']['tab_1'][$icnt - 1]);*/


$this->IncludeComponentTemplate();

include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/newportal/order.course/include/nav.php');
?>
