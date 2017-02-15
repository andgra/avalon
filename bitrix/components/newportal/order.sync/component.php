<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

/** @global CMain $APPLICATION */
global $APPLICATION;

$syncEnabled = COption::GetOptionString('order', 'order_sync_enable', 'N');
$syncEnabled = ($syncEnabled === 'Y');
$arResult['ORDER_SYNC_ENABLED'] = ($syncEnabled) ? 'Y' : 'N';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ORDER_SYNC_ENABLE']) && check_bitrix_sessid())
{
	$APPLICATION->RestartBuffer();
	header('Content-type: application/x-www-form-urlencoded; charset=UTF-8');
	$errNumber = 0;
	CUtil::JSPostUnescape();
	$syncEnabled = ($_POST['ORDER_SYNC_ENABLE'] === 'Y');
	COption::SetOptionString('order', 'order_sync_enable', ($syncEnabled) ? 'Y' : 'N');
	$result = array('ERROR' => $errNumber);
	if ($errNumber === 0)
		$result['CHECKED'] = $syncEnabled ? 'Y' : 'N';
	echo CUtil::PhpToJSObject($result);
	exit();
}

if (!CModule::IncludeModule('order'))
{
	ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
	return;
}


global $APPLICATION, $USER;
$COrderPerms = COrderPerms::GetCurrentUserPermissions();
if ($COrderPerms->HavePerm('SYNC', BX_ORDER_PERM_NONE, 'READ'))
{
	ShowError(GetMessage('ORDER_PERMISSION_DENIED'));
	return;
}


$componentPage = '';
$arDefaultUrlTemplates404 = array(
	'index' => '',
	'all' => 'all/',
);

if ($arParams['SEF_MODE'] === 'Y')
{
	$arDefaultVariableAliases404 = array();
	$arComponentVariables = array();
	$arVariables = array();
	$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams['SEF_URL_TEMPLATES']);
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams['VARIABLE_ALIASES']);
	$componentPage = CComponentEngine::ParseComponentPath($arParams['SEF_FOLDER'], $arUrlTemplates, $arVariables);

	if (!(is_string($componentPage) && isset($componentPage[0]) && isset($arDefaultUrlTemplates404[$componentPage])) || $this->__templateName === 'free')
	{
		$componentPage = 'index';
	}

	CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

	foreach ($arUrlTemplates as $url => $value)
	{
		$key = 'PATH_TO_SYNC_'.strtoupper($url);
		$arResult[$key] = isset($arParams[$key][0]) ? $arParams[$key] : $arParams['SEF_FOLDER'].$value;
	}
}
else
{
	$arComponentVariables = array();
	$arDefaultVariableAliases = array();
	$arVariables = array();
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams['VARIABLE_ALIASES']);
	CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

	$componentPage = 'index';
	if ($this->__templateName !== 'free')
	{
		if (isset($_REQUEST['all']))
		{
			$componentPage = 'all';
		}
	}

	$curPage = $APPLICATION->GetCurPage();

	$arResult['PATH_TO_SYNC_INDEX'] = $curPage;
	$arResult['PATH_TO_SYNC_ALL'] = $curPage.'?all';
}

$arResult =
	array_merge(
		array(
			'VARIABLES' => $arVariables,
			'ALIASES' => $arParams['SEF_MODE'] == 'Y' ? array(): $arVariableAliases
		),
		$arResult
	);
if($componentPage=='index') {
	$arResult['ENTITY_TITLE']=COrderEntitySelectorHelper::PrepareEntityTitles();
}

$this->IncludeComponentTemplate($componentPage);
