<?if(!Defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

CUtil::InitJSCore();

$arParams['MULTIPLE'] = $arParams['MULTIPLE'] == 'N' ? 'N' : 'Y';

$arParams['INPUT_NAME'] = preg_match('/^[a-zA-Z0-9_]+$/', $arParams['INPUT_NAME']) ? $arParams['INPUT_NAME'] : false;

if (isset($arParams['INPUT_VALUE']))
{
	if (!is_array($arParams['INPUT_VALUE']))
		$arParams['INPUT_VALUE'] = explode(',', $arParams['INPUT_VALUE']);

	if (is_array($arParams['ENTITY_TYPE']))
	{
		$arSettings = Array(
			'PHYSICAL' => in_array('PHYSICAL', $arParams['ENTITY_TYPE'])? 'Y': 'N',
			'CONTACT' => in_array('CONTACT', $arParams['ENTITY_TYPE'])? 'Y': 'N',
			'AGENT' => in_array('AGENT', $arParams['ENTITY_TYPE'])? 'Y': 'N',
			'DIRECTION' => in_array('DIRECTION', $arParams['ENTITY_TYPE'])? 'Y': 'N',
			'NOMEN' => in_array('NOMEN', $arParams['ENTITY_TYPE'])? 'Y': 'N',
			'COURSE' => in_array('COURSE', $arParams['ENTITY_TYPE'])? 'Y': 'N',
			'GROUP' => in_array('GROUP', $arParams['ENTITY_TYPE'])? 'Y': 'N',
			'FORMED_GROUP' => in_array('FORMED_GROUP', $arParams['ENTITY_TYPE'])? 'Y': 'N',
			'REG' => in_array('REG', $arParams['ENTITY_TYPE'])? 'Y': 'N',
			'APP' => in_array('APP', $arParams['ENTITY_TYPE'])? 'Y': 'N',
			'STAFF' => in_array('STAFF', $arParams['ENTITY_TYPE'])? 'Y': 'N',
		);
	}
	else
	{
		$arSettings = Array(
			'PHYSICAL' => $arParams['ENTITY_TYPE'] == 'PHYSICAL'? 'Y': 'N',
			'CONTACT' => $arParams['ENTITY_TYPE'] == 'CONTACT'? 'Y': 'N',
			'AGENT' => $arParams['ENTITY_TYPE'] == 'AGENT'? 'Y': 'N',
			'DIRECTION' => $arParams['ENTITY_TYPE'] == 'DIRECTION'? 'Y': 'N',
			'NOMEN' => $arParams['ENTITY_TYPE'] == 'NOMEN'? 'Y': 'N',
			'COURSE' => $arParams['ENTITY_TYPE'] == 'COURSE'? 'Y': 'N',
			'GROUP' => $arParams['ENTITY_TYPE'] == 'GROUP'? 'Y': 'N',
			'FORMED_GROUP' => $arParams['ENTITY_TYPE'] == 'FORMED_GROUP'? 'Y': 'N',
			'REG' => $arParams['ENTITY_TYPE'] == 'REG'? 'Y': 'N',
			'APP' => $arParams['ENTITY_TYPE'] == 'APP'? 'Y': 'N',
			'STAFF' => $arParams['ENTITY_TYPE'] == 'STAFF'? 'Y': 'N',
		);
	}

	$arUserField = Array(
		'USER_TYPE' => 'order',
		'FIELD_NAME' => $arParams['INPUT_NAME'],
		'MULTIPLE' => $arParams['MULTIPLE'],
		'SETTINGS' => $arSettings,
		'VALUE' => $arParams['INPUT_VALUE'],
	);

	if (isset($arParams['FILTER']) && $arParams['FILTER'] == true)
	{
		$APPLICATION->IncludeComponent(
			'newportal:order.field.filter',
			'order',
			array(
				'arUserField' => $arUserField,
				'bVarsFromForm' => false,
				'form_name' => 'filter_'.$arParams['FORM_NAME']
			),
			false,
			array('HIDE_ICONS' => true)
		);
	}
	else
	{
		$APPLICATION->IncludeComponent(
			'newportal:system.field.edit',
			'order',
			array(
				'arUserField' => $arUserField,
				'bVarsFromForm' => false,
				'form_name' => isset($arParams['FORM_NAME']) && strlen($arParams['FORM_NAME']) > 0 ? 'form_'.$arParams['FORM_NAME'] : '',
			),
			false,
			array('HIDE_ICONS' => 'Y')
		);
	}
}
?>