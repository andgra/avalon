<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();


global $APPLICATION;
if (!CModule::IncludeModule('order'))
{
    ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
    return;
};


$arDefaultUrlTemplates404 = array(
    'index' => '',
    'direction_root' => '#direction_root_id#/',
    'direction' => '#direction_root_id#/#direction_id#/',
    'courses' => '#direction_root_id#/#direction_id#/courses/',
    'enroll' => 'enroll/'
);

$arDefaultVariableAliases404 = array(

);
$arDefaultVariableAliases = array();
$componentPage = '';
$arComponentVariables = array('direction_root_id','direction_id');

$arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);


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
        if(strlen($arParams['PATH_TO_'.strToUpper($url)]) <= 0)
            $arResult['PATH_TO_'.strToUpper($url)] = $arParams['SEF_FOLDER'].$value;
        else
            $arResult['PATH_TO_'.strToUpper($url)] = $arParams['PATH_TO_'.strToUpper($url)];
    }
}
else
{
    $arComponentVariables[] = $arParams['VARIABLE_ALIASES']['direction_id'];

    $arVariables = array();
    $arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams['VARIABLE_ALIASES']);
    CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

    $componentPage = 'index';
    if (isset($_REQUEST['edit']))
        $componentPage = 'edit';
    else if (isset($_REQUEST['copy']))
        $componentPage = 'edit';

    $arResult['PATH_TO_SELF'] = $APPLICATION->GetCurPage(false);
    //$arResult['PATH_TO_NOMEN'] = $APPLICATION->GetCurPage()."?".$arVariableAliases['direction_id']."=#direction_id#";
}

$arResult = array_merge(
    array(
        'VARIABLES' => $arVariables,
        'ALIASES' => $arParams['SEF_MODE'] == 'Y'? array(): $arVariableAliases,
        'ELEMENT_ID' => $arParams['ELEMENT_ID'],
        //'PATH_TO_BASKET' => '/bitrix/components/newportal/order.app_create.basket/ajax.php?'.bitrix_sessid_get(),
    ),
    $arResult
);
$arResult['NAVIGATION_CONTEXT_ID'] = 'APP_CREATE';
$this->IncludeComponentTemplate($componentPage);
?>