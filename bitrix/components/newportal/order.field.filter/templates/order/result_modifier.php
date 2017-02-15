<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("order"))
	return;

global $USER;
$COrderPerms = COrderPerms::GetCurrentUserPermissions();
$arSupportedTypes = array(); // all entity types are defined in settings
$arSettings = $arParams['arUserField']['SETTINGS'];
if (isset($arSettings['PHYSICAL']) && $arSettings['PHYSICAL'] === 'Y')
{
	$arSupportedTypes[] = 'PHYSICAL';
}
if (isset($arSettings['CONTACT']) && $arSettings['CONTACT'] === 'Y')
{
	$arSupportedTypes[] = 'CONTACT';
}
if (isset($arSettings['AGENT']) && $arSettings['AGENT'] === 'Y')
{
	$arSupportedTypes[] = 'AGENT';
}
if (isset($arSettings['DIRECTION']) && $arSettings['DIRECTION'] === 'Y')
{
	$arSupportedTypes[] = 'DIRECTION';
}
if (isset($arSettings['NOMEN']) && $arSettings['NOMEN'] === 'Y')
{
	$arSupportedTypes[] = 'NOMEN';
}
if (isset($arSettings['COURSE']) && $arSettings['COURSE'] === 'Y')
{
	$arSupportedTypes[] ='COURSE';
}
if (isset($arSettings['GROUP']) && $arSettings['GROUP'] === 'Y')
{
	$arSupportedTypes[] = 'GROUP';
}
if (isset($arSettings['FORMED_GROUP']) && $arSettings['FORMED_GROUP'] === 'Y')
{
	$arSupportedTypes[] = 'FORMED_GROUP';
}
if (isset($arSettings['REG']) && $arSettings['REG'] === 'Y')
{
	$arSupportedTypes[] = 'REG';
}
if (isset($arSettings['APP']) && $arSettings['APP'] === 'Y')
{
	$arSupportedTypes[] = 'APP';
}
if (isset($arSettings['STAFF']) && $arSettings['STAFF'] === 'Y')
{
	$arSupportedTypes[] = 'STAFF';
}
/*if (isset($arSettings['LEAD']) && $arSettings['LEAD'] === 'Y')
{
	$arSupportedTypes[] = 'LEAD';
}*/
$arParams['ENTITY_TYPE'] = array(); // only entity types are allowed for current user
foreach($arSupportedTypes as $supportedType)
{
	if(!$COrderPerms->HavePerm($supportedType, BX_ORDER_PERM_NONE, 'READ')) {
		$arParams['ENTITY_TYPE'][] = $supportedType;
	}
}

$arResult['MULTIPLE'] = $arParams['arUserField']['MULTIPLE'];
if (!is_array($arResult['VALUE']))
	$arResult['VALUE'] = array(htmlspecialcharsBack(htmlspecialcharsBack($arResult['VALUE'])));
else
{
	$ar = array();
	foreach ($arResult['VALUE'] as $key=> $value)
		if (!empty($value))
			$ar[$key] = htmlspecialcharsBack(htmlspecialcharsBack($value));
	$arResult['VALUE'] = $ar;
}

$arResult['SELECTED'] = array();
foreach ($arResult['VALUE'] as $key => $value)
{
	/*if (empty($value) || ($key!='ID' && preg_match('/^\d+$/',$key) != 1))
	{
		continue;
	}*/


	// Try to get raw entity ID
	$ary = explode('#_#', $value);
	if(count($ary) > 1)	{
		$value = $ary[1];
		$type=$ary[0];
	} else {
		$type=$arParams['ENTITY_TYPE'][0];
	}
	$arResult['SELECTED'][strtolower($type.'#_#'.$value)] = $type.'#_#'.$value;

}

$arResult['ELEMENT'] = array();

$arResult['ELEMENT'] =COrderEntitySelectorHelper::PreparePopupItems($arParams['ENTITY_TYPE'],array());

foreach($arResult['ELEMENT'] as &$el) {
	if (isset($arResult['SELECTED'][strtolower($el['type'].'#_#'.$el['id'])]))
	{
		unset($arResult['SELECTED'][strtolower($el['type'].'#_#'.$el['id'])]);
		$el['selected'] = 'Y';
	}
	else
		$el['selected'] = 'N';
}
unset($el);
foreach($arParams['ENTITY_TYPE'] as $ent) {
	$arResult['ENTITY_TYPE'][]=strtolower($ent);
}

?>