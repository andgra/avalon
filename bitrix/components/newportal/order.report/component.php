<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule('order'))
{
	ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
	return;
}


$arDefaultUrlTemplates404 = array(
	'index' => 'index.php',
	'report' => 'report/',
	'construct' => 'construct/#report_id#/#action#/',
	'view' => 'view/#report_id#/'
);

$arDefaultVariableAliases404 = array(

);
$arDefaultVariableAliases = array();
$componentPage = '';
$arComponentVariables = array('report_id', 'action');

$arResult['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);

if ($arParams['SEF_MODE'] == 'Y')
{
	$arVariables = array();
	$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams['SEF_URL_TEMPLATES']);
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams['VARIABLE_ALIASES']);
	$componentPage = CComponentEngine::ParseComponentPath($arParams['SEF_FOLDER'], $arUrlTemplates, $arVariables);

	if (empty($componentPage) || (!array_key_exists($componentPage, $arDefaultUrlTemplates404)))
		$componentPage = 'index';

	CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

	foreach ($arUrlTemplates as $url => $value)
	{
		if (strlen($arParams['PATH_TO_REPORT_'.strToUpper($url)]) <= 0)
			$arResult['PATH_TO_REPORT_'.strToUpper($url)] = $arParams['SEF_FOLDER'].$value;
		else
			$arResult['PATH_TO_REPORT_'.strToUpper($url)] = $arParams['PATH_TO_'.strToUpper($url)];
	}
}
else
{
	$arComponentVariables[] = $arParams['VARIABLE_ALIASES']['report_id'];
	$arComponentVariables[] = $arParams['VARIABLE_ALIASES']['action'];

	$arVariables = array();
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams['VARIABLE_ALIASES']);
	CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

	$componentPage = 'index';
	if (isset($_REQUEST['report']))
		$componentPage = 'report';
	else if (isset($_REQUEST['construct']))
		$componentPage = 'construct';
	else if (isset($_REQUEST['view']))
		$componentPage = 'view';
	$arResult['PATH_TO_REPORT_REPORT'] = $APPLICATION->GetCurPage();
	$arResult['PATH_TO_REPORT_CONSTRUCT'] = $APPLICATION->GetCurPage()."?report_id=#report_id#&action=#action#";
	$arResult['PATH_TO_REPORT_VIEW'] = $APPLICATION->GetCurPage()."?report_id=#report_id#";
}

$arResult = array_merge(
	array(
		'VARIABLES' => $arVariables,
		'ALIASES' => $arParams['SEF_MODE'] == 'Y'? array(): $arVariableAliases,
		'ELEMENT_ID' => $arParams['ELEMENT_ID'],
		'PATH_TO_APP_EDIT' => $arParams['PATH_TO_APP_EDIT'],
		'PATH_TO_REG_EDIT' => $arParams['PATH_TO_REG_EDIT'],
		'PATH_TO_PHYSICAL_EDIT' => $arParams['PATH_TO_PHYSICAL_EDIT'],
		'PATH_TO_CONTACT_EDIT' => $arParams['PATH_TO_CONTACT_EDIT'],
		'PATH_TO_AGENT_EDIT' => $arParams['PATH_TO_AGENT_EDIT'],
		'PATH_TO_DIRECTION_EDIT' => $arParams['PATH_TO_DIRECTION_EDIT'],
		'PATH_TO_NOMEN_EDIT' => $arParams['PATH_TO_NOMEN_EDIT'],
		'PATH_TO_COURSE_EDIT' => $arParams['PATH_TO_COURSE_EDIT'],
		'PATH_TO_GROUP_EDIT' => $arParams['PATH_TO_GROUP_EDIT'],
		'PATH_TO_FORMED_GROUP_EDIT' => $arParams['PATH_TO_FORMED_GROUP_EDIT'],
		'PATH_TO_STAFF_EDIT' => $arParams['PATH_TO_STAFF_EDIT'],
	),
	$arResult
);

$this->IncludeComponentTemplate($componentPage);

?>