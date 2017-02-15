<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $APPLICATION;
global $USER_FIELD_MANAGER;
//use Bitrix\Order\Entity\QuickPanelView;
if (!CModule::IncludeModule('order'))
{
	ShowError(GetMessage('ORDER_MODULE_NOT_INSTALLED'));
	return;
}
$userID = $USER->GetID();
$currentUser = CUser::GetByID($userID);
/*if (!$currentUser || !$currentUser->IsAuthorized())
{
	ShowError(GetMessage('ORDER_ENTITY_QPV_NOT_AUTHORIZED'));
	return;
}*/

$entityTypeName = isset($arParams['ENTITY_TYPE_NAME']) ? $arParams['ENTITY_TYPE_NAME'] : '';
if ($entityTypeName === '')
{
	ShowError(GetMessage('ORDER_ENTITY_QPV_ENTITY_TYPE_NAME_NOT_DEFINED'));
	return;
}

/*use Bitrix\Order\Format\ContactAddressFormatter;
use Bitrix\Order\Format\CompanyAddressFormatter;
use Bitrix\Order\Format\LeadAddressFormatter;
use Bitrix\Order\EntityAddress;*/


$entityTypeID = COrderOwnerType::ResolveID($entityTypeName);
$arResult['ENTITY_TYPE_ID'] = $entityTypeID;
$arResult['ENTITY_TYPE_NAME'] = $entityTypeName;

$entityID = isset($arParams['ENTITY_ID']) ? (int)$arParams['ENTITY_ID'] : 0;
if ($entityID <= 0)
{
	ShowError(GetMessage('ORDER_ENTITY_QPV_ENTITY_ID_NOT_DEFINED'));
	return;
}

$arResult['ENTITY_ID'] = $entityID;

$currentUserPremissions = COrderPerms::GetCurrentUserPermissions();
if(!COrderAuthorizationHelper::CheckReadPermission($entityTypeName, $entityID, $currentUserPremissions))
{
	ShowError(GetMessage('ORDER_ENTITY_QPV_ACCESS_DENIED'));
	return;
}

$entityFields = isset($arParams['~ENTITY_FIELDS']) ? $arParams['~ENTITY_FIELDS'] : null;
if(!is_array($entityFields))
{
	ShowError(GetMessage('ORDER_ENTITY_QPV_ENTITY_FIELDS_NOT_FOUND'));
	return;
}

$canEdit = $arResult['CAN_EDIT'] = COrderAuthorizationHelper::CheckUpdatePermission($entityTypeName, $entityID, $currentUserPremissions);
$userProfilePath = $arResult['PATH_TO_STAFF_EDIT'] = $arParams['PATH_TO_STAFF_EDIT'] = OrderCheckPath('PATH_TO_STAFF_EDIT', $arParams['PATH_TO_STAFF_EDIT'], '/company/personal/user/#staff_id#/');
$nameTemplate = $arResult['NAME_TEMPLATE'] = $arParams['NAME_TEMPLATE'] = empty($arParams['NAME_TEMPLATE']) ? CSite::GetNameFormat(false) : str_replace(array("#NOBR#","#/NOBR#"), array("",""), $arParams["NAME_TEMPLATE"]);
$enableInstantEdit = $arResult['ENABLE_INSTANT_EDIT'] = isset($arParams['ENABLE_INSTANT_EDIT']) ? $arParams['ENABLE_INSTANT_EDIT'] : false;
$arResult['INSTANT_EDITOR_ID'] = isset($arParams['INSTANT_EDITOR_ID']) ? $arParams['INSTANT_EDITOR_ID'] : '';
$arResult['SERVICE_URL'] = isset($arParams['SERVICE_URL']) ? $arParams['SERVICE_URL'] : '';
$arResult['FORM_ID'] = $arParams['FORM_ID'] = isset($arParams['FORM_ID']) ? $arParams['FORM_ID'] : strtolower($entityTypeName).'_'.$entityID;
$arResult['GUID'] = isset($arParams['GUID']) ? $arParams['GUID'] : strtolower($arResult['FORM_ID']).'_qpv';

//CONFIG -->
$config = CUserOptions::GetOption(
	'order.entity.quickpanelview',
	$arResult['GUID'],
	null,
	$currentUser->GetID()
);

$enableDefaultConfig = !is_array($config);
if($enableDefaultConfig)
{
	$config = array('enabled' => 'N', 'expanded' => 'Y', 'fixed' => 'Y');
}
// <-- CONFIG

//$defaultCompanyLogoUrl = SITE_DIR.'bitrix/js/order/images/order-default-company.jpg';
$defaultCompanyLogoUrl = '';
$ufEntityID = '';
$entityData = array();
$entityContext = array(
	'SIP_MANAGER_CONFIG' => array()
);

if(!function_exists('__OrderQuickPanelViewPrepareResponsible'))
{
	function __OrderQuickPanelViewPrepareResponsible($entityFields, $userProfilePath, $nameTemplate, $enableEdit, $editorID, $serviveUrl, $key = '', $useTildeKey = true)
	{
		if($key === '')
		{
			$key = 'ASSIGNED_BY';
		}

		$map = array(
			'ID' => ($useTildeKey ? '~' : '').$key.'_ID',
			'FORMATTED_NAME' => ($useTildeKey ? '~' : '').$key.'_FORMATTED_NAME',
			'LOGIN' => ($useTildeKey ? '~' : '').$key.'_LOGIN',
			'NAME' => ($useTildeKey ? '~' : '').$key.'_NAME',
			'LAST_NAME' => ($useTildeKey ? '~' : '').$key.'_LAST_NAME',
			'SECOND_NAME' => ($useTildeKey ? '~' : '').$key.'_SECOND_NAME',
			'PERSONAL_PHOTO' => ($useTildeKey ? '~' : '').$key.'_PERSONAL_PHOTO',
			'WORK_POSITION' => ($useTildeKey ? '~' : '').$key.'_WORK_POSITION'
		);

		$userID = isset($entityFields[$map['ID']]) ? $entityFields[$map['ID']] : 0;
		$formattedName = isset($entityFields[$map['FORMATTED_NAME']]) ? $entityFields[$map['FORMATTED_NAME']] : '';
		if($formattedName === '')
		{
			$formattedName = CUser::FormatName(
				$nameTemplate,
				array(
					'LOGIN' => isset($entityFields[$map['LOGIN']]) ? $entityFields[$map['LOGIN']] : '',
					'NAME' => isset($entityFields[$map['NAME']]) ? $entityFields[$map['NAME']] : '',
					'LAST_NAME' => isset($entityFields[$map['LAST_NAME']]) ? $entityFields[$map['LAST_NAME']] : '',
					'SECOND_NAME' => isset($entityFields[$map['SECOND_NAME']]) ? $entityFields[$map['SECOND_NAME']] : ''
				),
				true, false
			);
		}

		$photoID = isset($entityFields[$map['PERSONAL_PHOTO']]) ? $entityFields[$map['PERSONAL_PHOTO']] : 0;
		$photoUrl = '';
		if($photoID > 0)
		{
			$file = new CFile();
			$fileInfo = $file->ResizeImageGet(
				$photoID,
				array('width' => 38, 'height'=> 38),
				BX_RESIZE_IMAGE_EXACT
			);
			if(is_array($fileInfo) && isset($fileInfo['src']))
			{
				$photoUrl = $fileInfo['src'];
			}
		}

		return array(
			'type' => 'responsible',
			'enableCaption' => false,
			'editable' => $enableEdit,
			'data' => array(
				'fieldID' => $useTildeKey ? substr($map['ID'], 1) : $map['ID'],
				'userID' => $userID,
				'name' => $formattedName,
				'photoID' => $photoID,
				'photoUrl' => $photoUrl,
				'position' => isset($entityFields[$map['WORK_POSITION']]) ? $entityFields[$map['WORK_POSITION']] : '',
				'profileUrlTemplate' => $userProfilePath,
				'profileUrl' => CComponentEngine::makePathFromTemplate($userProfilePath, array('staff_id' => $userID)),
				'editorID' => $editorID,
				'serviceUrl' => $serviveUrl,
				'userInfoProviderID' => md5($serviveUrl)
			)
		);
	}
}
if(!function_exists('__OrderQuickPanelViewPrepareClientInfo'))
{
	function __OrderQuickPanelViewPrepareClientInfo($entityTypeName, &$entityContext)
	{
		if($entityTypeName === COrderOwnerType::CompanyName)
		{
			if(!isset($entityContext['SIP_MANAGER_CONFIG'][COrderOwnerType::CompanyName]))
			{
				$entityContext['SIP_MANAGER_CONFIG'][COrderOwnerType::CompanyName] = array(
					'ENTITY_TYPE' => COrderOwnerType::CompanyName,
					'SERVICE_URL' => SITE_DIR.'bitrix/components/bitrix/order.company.show/ajax.php?' . bitrix_sessid_get()
				);
			}

			$entityInfo = $entityContext['COMPANY_INFO'];
			$fieldData = array(
				'type' => 'client',
				'enableCaption' => false,
				'data' => array(
					'ENTITY_TYPE_NAME' => COrderOwnerType::CompanyName,
					'NAME' => $entityInfo['TITLE'],
					'DESCRIPTION' => '',
					'SHOW_URL' => $entityInfo['SHOW_URL'],
					'IMAGE_URL' => $entityInfo['IMAGE_URL']
				)
			);
		}
		else
		{
			if(!isset($entityContext['SIP_MANAGER_CONFIG'][COrderOwnerType::ContactName]))
			{
				$entityContext['SIP_MANAGER_CONFIG'][COrderOwnerType::ContactName] = array(
					'ENTITY_TYPE' => COrderOwnerType::ContactName,
					'SERVICE_URL' => SITE_DIR.'bitrix/components/bitrix/order.contact.show/ajax.php?' . bitrix_sessid_get()
				);
			}

			$entityInfo = $entityContext['CONTACT_INFO'];
			$fieldData = array(
				'type' => 'client',
				'enableCaption' => false,
				'data' => array(
					'ENTITY_TYPE_NAME' => COrderOwnerType::ContactName,
					'NAME' => $entityInfo['FORMATTED_NAME'],
					'DESCRIPTION' => $entityInfo['POST'],
					'SHOW_URL' => $entityInfo['SHOW_URL'],
					'IMAGE_URL' => $entityInfo['IMAGE_URL']
				)
			);
		}

		$entityID = $entityInfo['ID'];
		if(isset($entityInfo['FM']))
		{
			if(isset($entityInfo['FM']['PHONE']))
			{
				$fieldData['data']['PHONE'] = __OrderQuickPanelViewPrepareMultiFields(
					$entityInfo['FM']['PHONE'],
					$entityTypeName,
					$entityID,
					'PHONE'
				);
			}
			if(isset($entityInfo['FM']['EMAIL']))
			{
				$fieldData['data']['EMAIL'] = __OrderQuickPanelViewPrepareMultiFields(
					$entityInfo['FM']['EMAIL'],
					$entityTypeName,
					$entityID,
					'EMAIL'
				);
			}
		}

		return $fieldData;
	}
}
if(!function_exists('__OrderQuickPanelViewLoadMultiFields'))
{
	function __OrderQuickPanelViewLoadMultiFields($entityTypeName, $entityID)
	{
		$dbResult = COrderFieldMulti::GetList(
			array('ID' => 'asc'),
			array('ENTITY_ID' => $entityTypeName, 'ELEMENT_ID' => $entityID)
		);

		$result = array();
		while($arMultiFields = $dbResult->Fetch())
		{
			$result[$arMultiFields['TYPE_ID']][$arMultiFields['ID']] = array('VALUE' => $arMultiFields['VALUE'], 'VALUE_TYPE' => $arMultiFields['VALUE_TYPE']);
		}
		return $result;
	}
}
if(!function_exists('__OrderQuickPanelViewPrepareMultiFields'))
{
	function __OrderQuickPanelViewPrepareMultiFields(array $multiFields, $entityTypeName, $entityID, $typeID)
	{
		if(empty($multiFields))
		{
			return null;
		}

		$arEntityTypeInfos = COrderFieldMulti::GetEntityTypeInfos();
		$arEntityTypes = COrderFieldMulti::GetEntityTypes();
		$sipConfig =  array(
			'STUB' => GetMessage('ORDER_ENTITY_QPV_MULTI_FIELD_NOT_ASSIGNED'),
			'ENABLE_SIP' => true,
			'SIP_PARAMS' => array(
				'ENTITY_TYPE' => 'ORDER_'.$entityTypeName,
				'ENTITY_ID' => $entityID)
		);

		$typeInfo = isset($arEntityTypeInfos[$typeID]) ? $arEntityTypeInfos[$typeID] : array();
		$result = array(
			'type' => 'multiField',
			'caption' => isset($typeInfo['NAME']) ? $typeInfo['NAME'] : $typeID,
			'data' => array('type'=> $typeID, 'items'=> array())
		);
		foreach($multiFields as $multiField)
		{
			$value = isset($multiField['VALUE']) ? $multiField['VALUE'] : '';
			$valueType = isset($multiField['VALUE_TYPE']) ? $multiField['VALUE_TYPE'] : '';

			$entityType = $arEntityTypes[$typeID];
			$valueTypeInfo = isset($entityType[$valueType]) ? $entityType[$valueType] : null;

			$params = array('VALUE' => $value, 'VALUE_TYPE_ID' => $valueType, 'VALUE_TYPE' => $valueTypeInfo);
			$result['data']['items'][] = COrderViewHelper::PrepareMultiFieldValueItemData($typeID, $params, $sipConfig);
		}
		return $result;
	}
}
if(!function_exists('__OrderQuickPanelViewPrepareContactInfo'))
{
	function __OrderQuickPanelViewPrepareContactInfo($entityFields, &$entityContext, $key = '', $useTildeKey = true)
	{
		if($key === '')
		{
			$key = 'CONTACT';
		}

		$map = array(
			'ID' => ($useTildeKey ? '~' : '').$key.'_ID',
			'FORMATTED_NAME' => ($useTildeKey ? '~' : '').$key.'_FORMATTED_NAME',
			'POST' => ($useTildeKey ? '~' : '').$key.'_POST',
			'PHOTO' => ($useTildeKey ? '~' : '').$key.'_PHOTO',
		);

		$entityContext['CONTACT_INFO'] = array(
			'ID' => isset($entityFields[$map['ID']]) ? (int)$entityFields[$map['ID']] : 0,
			'FORMATTED_NAME' => ''
		);
		if($entityContext['CONTACT_INFO']['ID'] > 0 && isset($entityFields[$map['FORMATTED_NAME']]))
		{
			$entityContext['CONTACT_INFO']['FORMATTED_NAME'] = $entityFields[$map['FORMATTED_NAME']];
			$entityContext['CONTACT_INFO']['POST'] = isset($entityFields[$map['POST']]) ? $entityFields[$map['POST']] : '';

			$entityContext['CONTACT_INFO']['SHOW_URL'] = COrderOwnerType::GetShowUrl(
				COrderOwnerType::Contact,
				$entityContext['CONTACT_INFO']['ID'],
				false
			);

			if(isset($entityFields[$map['PHOTO']]))
			{
				$file = new CFile();
				$fileInfo = $file->ResizeImageGet(
					$entityFields[$map['PHOTO']],
					array('width' => 38, 'height' => 38),
					BX_RESIZE_IMAGE_EXACT
				);

				$entityContext['CONTACT_INFO']['IMAGE_URL'] = is_array($fileInfo) && isset($fileInfo['src']) ? $fileInfo['src'] : '';
			}
			else
			{
				$entityContext['CONTACT_INFO']['IMAGE_URL'] = '';
			}

			$entityContext['CONTACT_INFO']['FM'] = __OrderQuickPanelViewLoadMultiFields(COrderOwnerType::ContactName, $entityContext['CONTACT_INFO']['ID']);
			$entityContext['CONTACT_INFO']['MULTI_FIELDS_OPTIONS'] = array(
				'ENABLE_SIP' => true,
				'SIP_PARAMS' => array(
					'ENTITY_TYPE' => 'ORDER_'.COrderOwnerType::ContactName,
					'ENTITY_ID' => $entityContext['CONTACT_INFO']['ID']
				)
			);
		}
	}
}
if(!function_exists('__OrderQuickPanelViewPrepareCompanyInfo'))
{
	function __OrderQuickPanelViewPrepareCompanyInfo($entityFields, &$entityContext, $key = '', $useTildeKey = true)
	{
		if($key === '')
		{
			$key = 'COMPANY';
		}

		$map = array(
			'ID' => ($useTildeKey ? '~' : '').$key.'_ID',
			'TITLE' => ($useTildeKey ? '~' : '').$key.'_TITLE',
			'LOGO' => ($useTildeKey ? '~' : '').$key.'_LOGO',
		);

		$entityContext['COMPANY_INFO'] = array(
			'ID' => isset($entityFields[$map['ID']]) ? (int)$entityFields[$map['ID']] : 0,
			'TITLE' => ''
		);
		if($entityContext['COMPANY_INFO']['ID'] > 0 && isset($entityFields[$map['TITLE']]))
		{
			$entityContext['COMPANY_INFO']['TITLE'] = $entityFields[$map['TITLE']];
			$entityContext['COMPANY_INFO']['SHOW_URL'] = COrderOwnerType::GetShowUrl(
				COrderOwnerType::Company,
				$entityContext['COMPANY_INFO']['ID'],
				false
			);

			if(isset($entityFields[$map['LOGO']]))
			{
				$file = new CFile();
				$fileInfo = $file->ResizeImageGet(
					$entityFields[$map['LOGO']],
					array('width' => 48, 'height' => 31),
					BX_RESIZE_IMAGE_PROPORTIONAL
				);

				$entityContext['COMPANY_INFO']['IMAGE_URL'] = is_array($fileInfo) && isset($fileInfo['src']) ? $fileInfo['src'] : '';
			}
			else
			{
				$entityContext['COMPANY_INFO']['IMAGE_URL'] = '';
			}

			$entityContext['COMPANY_INFO']['FM'] = __OrderQuickPanelViewLoadMultiFields(COrderOwnerType::CompanyName, $entityContext['COMPANY_INFO']['ID']);
			$entityContext['COMPANY_INFO']['MULTI_FIELDS_OPTIONS'] = array(
				'ENABLE_SIP' => true,
				'SIP_PARAMS' => array(
					'ENTITY_TYPE' => 'ORDER_'.COrderOwnerType::CompanyName,
					'ENTITY_ID' => $entityContext['COMPANY_INFO']['ID']
				)
			);
		}
	}
}
if(!function_exists('__OrderQuickPanelViewPrepareStatusEnumeration'))
{
	function __OrderQuickPanelViewPrepareStatusEnumeration($statusTypeID, $statusID, $editable, &$entityContext)
	{
		$sourceItems = COrderStatus::GetStatusList($statusTypeID);
		$items = array();
		$text = '';
		foreach($sourceItems as $k => $v)
		{
			if(!is_string($k))
			{
				$k = (string)$k;
			}
			$items[] = array('ID' => $k, 'VALUE' => $v);
			if($text === '' && $statusID !== '' && $statusID === $k)
			{
				$text = $v;
			}
		}

		return array(
			'type' => 'enumeration',
			'editable'=> $editable,
			'data' => array(
				'value' => $statusID,
				'text' => $text,
				'items' => $items
			)
		);
	}
}
if(!function_exists('__OrderQuickPanelViewPrepareCurrencyEnumeration'))
{
	function __OrderQuickPanelViewPrepareCurrencyEnumeration($currencyID, $editable, &$entityContext)
	{
		$items = COrderCurrencyHelper::PrepareListItems();
		return array(
			'type' => 'enumeration',
			'editable'=> $editable,
			'data' => array(
				'value' => $currencyID,
				'text' => $currencyID !== '' && isset($items[$currencyID]) ? $items[$currencyID] : '',
				'items' => $items
			)
		);
	}
}
if(!function_exists('__OrderQuickPanelViewPrepareMoney'))
{
	function __OrderQuickPanelViewPrepareMoney($sum, $currencyID, $editable, $serviceUrl, &$entityContext)
	{
		$formattedSum = COrderCurrency::MoneyToString($sum, $currencyID, '#');
		$formattedSumWithCurrency = COrderCurrency::MoneyToString($sum, $currencyID, '');
		return array(
			'type' => 'money',
			'editable'=> $editable,
			'data' => array(
				'currencyId' => $currencyID,
				'value' => $sum,
				'text' => $formattedSum,
				'formatted_sum' => $formattedSum,
				'formatted_sum_with_currency' => $formattedSumWithCurrency,
				'serviceUrl' => $serviceUrl
			)
		);
	}
}

$file = new CFile();
$formOptions = CUserOptions::GetOption('main.interface.form', $arResult['FORM_ID'], array());
$formFieldNames = array();
if(!(isset($formOptions['settings_disabled']) && $formOptions['settings_disabled'] === 'Y') && is_array($formOptions['tabs']))
{
	foreach($formOptions['tabs'] as $tab)
	{
		$tabID = isset($tab['id']) ? $tab['id'] : '';
		if($tabID !== 'tab_1')
		{
			continue;
		}

		$fields = isset($tab['fields']) ? $tab['fields'] : null;
		if(!is_array($fields))
		{
			continue;
		}

		foreach($fields as $field)
		{
			$type = isset($field['type']) ? $field['type'] : '';
			if($type === 'section')
			{
				continue;
			}

			$fieldID = isset($field['id']) ? $field['id'] : '';
			if($fieldID === '')
			{
				continue;
			}

			$fieldName = isset($field['name']) ? $field['name'] : '';
			if($fieldName !== '')
			{
				$formFieldNames[$fieldID] = $fieldName;
			}
		}
	}
}

if($entityTypeID === COrderOwnerType::Contact)
{
	$entityContext['SIP_MANAGER_CONFIG'][COrderOwnerType::ContactName] = array(
		'ENTITY_TYPE' => COrderOwnerType::ContactName,
		'SERVICE_URL' => SITE_DIR.'bitrix/components/bitrix/order.contact.show/ajax.php?'.bitrix_sessid_get()
	);

	$ufEntityID = COrderContact::$sUFEntityID;
	$fieldKeys = array(
		'NAME' => true, 'SECOND_NAME' => true, 'LAST_NAME' => true,
		'BIRTHDATE' => true, 'TYPE_ID' => true,
		'SOURCE_ID' => true, 'SOURCE_DESCRIPTION' => true,
		'COMPANY_ID' => true, 'POST' => true, 'ADDRESS' => true,
		'OPENED' => true, 'EXPORT' => true,
		'ASSIGNED_BY_ID' => true, 'COMMENTS' => true
	);

	if($enableDefaultConfig)
	{
		$config['left'] = 'POST,TYPE_ID,SOURCE_ID';
		$config['center'] = 'PHONE,EMAIL,IM,COMPANY_ID';
		$config['right'] = 'ASSIGNED_BY_ID';
		$config['bottom'] = 'COMMENTS';
	}

	__OrderQuickPanelViewPrepareCompanyInfo($entityFields, $entityContext);

	foreach($entityFields as $k => $v)
	{
		if(!isset($fieldKeys[$k]))
		{
			continue;
		}

		if($k === 'BIRTHDATE')
		{
			$entityData[$k] = array(
				'type' => 'datetime',
				'editable'=> $enableInstantEdit,
				'data' => array(
					'text' => ($v !== null && $v !== '') ? ConvertTimeStamp(MakeTimeStamp($v), 'SHORT', SITE_ID) : '',
					'enableTime' => false
				)
			);
		}
		elseif($k === 'TYPE_ID')
		{
			$entityData[$k] = __OrderQuickPanelViewPrepareStatusEnumeration('CONTACT_TYPE', $v, $enableInstantEdit, $entityContext);
		}
		elseif($k === 'SOURCE_ID')
		{
			$entityData[$k] = __OrderQuickPanelViewPrepareStatusEnumeration('SOURCE', $v, $enableInstantEdit, $entityContext);
		}
		elseif($k === 'COMPANY_ID')
		{
			$entityData['COMPANY_ID'] = __OrderQuickPanelViewPrepareClientInfo(COrderOwnerType::CompanyName, $entityContext);
		}
		elseif($k === 'OPENED' || $k === 'EXPORT')
		{
			$v = ($v !== null && $v !== '') ? strtoupper($v) : 'N';
			$entityData[$k] = array(
				'type' => 'boolean',
				'editable'=> $enableInstantEdit,
				'data' => array('baseType' => 'char', 'value' => $v)
			);
		}
		elseif($k === 'ASSIGNED_BY_ID')
		{
			$entityData['ASSIGNED_BY_ID'] = __OrderQuickPanelViewPrepareResponsible(
				$entityFields,
				$userProfilePath,
				$nameTemplate,
				$enableInstantEdit,
				$arResult['INSTANT_EDITOR_ID'],
				$arResult['SERVICE_URL']
			);
		}
		elseif($k === 'COMMENTS')
		{
			$entityData[$k] = array(
				'type' => 'html',
				'editable'=> $enableInstantEdit,
				'data' => array(
					'html' => $entityFields["~{$k}"],
					'serviceUrl' => $arResult['SERVICE_URL']
				)
			);
		}
		elseif($k === 'ADDRESS')
		{
			$entityData[$k] = array(
				'type' => 'address',
				'editable'=> false,
				'data' => array('lines' => ContactAddressFormatter::prepareLines($entityFields, array('NL2BR' => true)))
			);
		}
		elseif($k === 'SOURCE_DESCRIPTION')
		{
			$entityData[$k] = array(
				'type' => 'text',
				'editable'=> $enableInstantEdit,
				'data' => array('text' => $entityFields["~{$k}"], 'multiline' => true)
			);
		}
		else
		{
			$entityData[$k] = array(
				'type' => 'text',
				'editable'=> $enableInstantEdit,
				'data' => array('text' => $entityFields["~{$k}"])
			);
		}

		$caption = isset($formFieldNames[$k]) ? $formFieldNames[$k] : '';
		if($caption === '')
		{
			$caption = COrderContact::GetFieldCaption($k);
		}
		$entityData[$k]['caption'] = $caption;
	}

	if(isset($entityFields['~PHOTO']))
	{
		$fileInfo = $file->ResizeImageGet(
			$entityFields['~PHOTO'],
			array('width' => 34, 'height' => 34),
			BX_RESIZE_IMAGE_EXACT
		);

		$arResult['HEAD_IMAGE_URL'] = isset($fileInfo['src']) ? $fileInfo['src'] : '';
	}
	else
	{
		$arResult['HEAD_IMAGE_URL'] = '';
	}
}
elseif($entityTypeID === COrderOwnerType::Company)
{
	$entityContext['SIP_MANAGER_CONFIG'][COrderOwnerType::CompanyName] = array(
		'ENTITY_TYPE' => COrderOwnerType::CompanyName,
		'SERVICE_URL' => SITE_DIR.'bitrix/components/bitrix/order.company.show/ajax.php?'.bitrix_sessid_get()
	);

	if($enableDefaultConfig)
	{
		$config['left'] = 'COMPANY_TYPE,INDUSTRY';
		$config['center'] = 'PHONE,EMAIL,WEB';
		$config['right'] = 'ASSIGNED_BY_ID';
		$config['bottom'] = 'COMMENTS';
	}

	$ufEntityID = COrderCompany::$sUFEntityID;
	$fieldKeys = array(
		'TITLE' => true,
		'COMPANY_TYPE' => true, 'INDUSTRY' => true, 'EMPLOYEES' => true,
		'CURRENCY_ID' => true, 'REVENUE' => true,
		'ADDRESS' => true, 'ADDRESS_LEGAL' => true, 'REG_ADDRESS' => true, 'BANKING_DETAILS' => true,
		'OPENED' => true, 'ASSIGNED_BY_ID' => true,
		'COMMENTS' => true
	);

	foreach($entityFields as $k => $v)
	{
		if(!isset($fieldKeys[$k]))
		{
			continue;
		}

		if($k === 'COMPANY_TYPE' || $k === 'INDUSTRY' || $k === 'EMPLOYEES')
		{
			$entityData[$k] = __OrderQuickPanelViewPrepareStatusEnumeration($k, $v, $enableInstantEdit, $entityContext);
		}
		elseif($k === 'CURRENCY_ID')
		{
			$entityData[$k] = __OrderQuickPanelViewPrepareCurrencyEnumeration($v, $enableInstantEdit, $entityContext);
		}
		elseif($k === 'REVENUE')
		{
			$v = isset($entityFields['~REVENUE']) ? $entityFields['~REVENUE'] : 0.0;
			$currencyID = isset($entityFields['~CURRENCY_ID'])
				? $entityFields['~CURRENCY_ID'] : COrderCurrency::GetBaseCurrencyID();

			$entityData[$k] = array(
				'type' => 'money',
				'editable'=> $enableInstantEdit,
				'data' => array(
					'currencyId' => $currencyID,
					'value' => $v,
					'text' => COrderCurrency::MoneyToString($v, $currencyID, '#'),
					'serviceUrl' => $arResult['SERVICE_URL']
				)
			);
		}
		elseif($k === 'OPENED' || $k === 'EXPORT')
		{
			$v = ($v !== null && $v !== '') ? strtoupper($v) : 'N';
			$entityData[$k] = array(
				'type' => 'boolean',
				'editable'=> $enableInstantEdit,
				'data' => array('baseType' => 'char', 'value' => $v)
			);
		}
		elseif($k === 'ASSIGNED_BY_ID')
		{
			$entityData['ASSIGNED_BY_ID'] = __OrderQuickPanelViewPrepareResponsible(
				$entityFields,
				$userProfilePath,
				$nameTemplate,
				$enableInstantEdit,
				$arResult['INSTANT_EDITOR_ID'],
				$arResult['SERVICE_URL']
			);
		}
		elseif($k === 'COMMENTS')
		{
			$entityData[$k] = array(
				'type' => 'html',
				'editable'=> $enableInstantEdit,
				'data' => array(
					'html' => $entityFields["~{$k}"],
					'serviceUrl' => $arResult['SERVICE_URL']
				)
			);
		}
		elseif($k === 'ADDRESS')
		{
			$entityData[$k] = array(
				'type' => 'address',
				'editable'=> false,
				'data' => array('lines' => CompanyAddressFormatter::prepareLines(
					$entityFields, array('TYPE_ID' => EntityAddress::Primary, 'NL2BR' => true))
				)
			);
		}
		elseif($k === 'ADDRESS_LEGAL' || $k === 'REG_ADDRESS')
		{
			$entityData[$k] = array(
				'type' => 'address',
				'editable'=> false,
				'data' => array('lines' => CompanyAddressFormatter::prepareLines(
					$entityFields, array('TYPE_ID' => EntityAddress::Registered, 'NL2BR' => true))
				)
			);
		}
		elseif($k === 'BANKING_DETAILS')
		{
			$entityData[$k] = array(
				'type' => 'text',
				'editable'=> $enableInstantEdit,
				'data' => array('text' => $entityFields["~{$k}"], 'multiline' => true)
			);
		}
		else
		{
			$entityData[$k] = array(
				'type' => 'text',
				'editable'=> $enableInstantEdit,
				'data' => array('text' => $entityFields["~{$k}"])
			);
		}

		$caption = isset($formFieldNames[$k]) ? $formFieldNames[$k] : '';
		if($caption === '')
		{
			$caption = COrderCompany::GetFieldCaption($k);
		}
		$entityData[$k]['caption'] = $caption;
	}

	if(isset($entityFields['~LOGO']))
	{
		$fileInfo = $file->ResizeImageGet(
			$entityFields['~LOGO'],
			array('width' => 79, 'height' => 33),
			BX_RESIZE_IMAGE_PROPORTIONAL_ALT
		);

		$arResult['HEAD_IMAGE_URL'] = isset($fileInfo['src']) ? $fileInfo['src'] : $defaultCompanyLogoUrl;
	}
	else
	{
		$arResult['HEAD_IMAGE_URL'] = $defaultCompanyLogoUrl;
	}

	$arResult['HEAD_TITLE'] = isset($entityFields['TITLE']) ? $entityFields['TITLE'] : '';
	$arResult['HEAD_TITLE_FIELD_ID'] = 'TITLE';

}
elseif($entityTypeID === COrderOwnerType::Deal)
{
	if($enableDefaultConfig)
	{
		$config['left'] = 'TYPE_ID,OPPORTUNITY,CURRENCY_ID,PROBABILITY';
		$config['center'] = 'CONTACT_ID,COMPANY_ID';
		$config['right'] = 'BEGINDATE,CLOSEDATE,ASSIGNED_BY_ID';
		$config['bottom'] = 'COMMENTS';
	}

	$ufEntityID = COrderDeal::$sUFEntityID;
	$fieldKeys = array(
		'TITLE' => true, 'STAGE_ID' => true,
		'CURRENCY_ID' => true, 'OPPORTUNITY' => true,
		'TYPE_ID' => true, 'PROBABILITY' => true,
		'BEGINDATE' => true, 'CLOSEDATE' => true,
		'CLOSED' => true, 'OPENED' => true,
		'CONTACT_ID' => true, 'COMPANY_ID' => true,
		'ASSIGNED_BY_ID' => true,
		'COMMENTS' => true
	);

	$arResult['HEAD_PROGRESS_LEGEND'] = isset($entityFields['~STAGE_TEXT']) ? $entityFields['~STAGE_TEXT'] : '';
	$stageText = htmlspecialcharsbx($arResult['HEAD_PROGRESS_LEGEND']);

	$progressHtml = $arResult['HEAD_PROGRESS_BAR'] = COrderViewHelper::RenderProgressControl(
		array(
			'ENTITY_TYPE_NAME' => COrderOwnerType::DealName,
			'REGISTER_SETTINGS' => true,
			'CONTROL_ID' =>  $arResult['GUID'],
			'ENTITY_ID' => $entityFields['~ID'],
			'CURRENT_ID' => $entityFields['~STAGE_ID'],
			'SERVICE_URL' => '/bitrix/components/bitrix/order.deal.list/list.ajax.php',
			'READ_ONLY' => !$canEdit,
			'DISPLAY_LEGEND' => false
		)
	);

	$currencyID = isset($entityFields['~CURRENCY_ID'])
		? $entityFields['~CURRENCY_ID'] : COrderCurrency::GetBaseCurrencyID();
	$arResult['HEAD_FORMATTED_SUM'] = COrderCurrency::MoneyToString(
			isset($entityFields['~OPPORTUNITY']) ? $entityFields['~OPPORTUNITY'] : 0.0, $currencyID
	);
	$arResult['HEAD_SUM_FIELD_ID'] = 'OPPORTUNITY';

	__OrderQuickPanelViewPrepareContactInfo($entityFields, $entityContext);
	__OrderQuickPanelViewPrepareCompanyInfo($entityFields, $entityContext);

	foreach($entityFields as $k => $v)
	{
		if(!isset($fieldKeys[$k]))
		{
			continue;
		}

		if($k === 'TYPE_ID')
		{
			$entityData[$k] = __OrderQuickPanelViewPrepareStatusEnumeration('DEAL_TYPE', $v, $enableInstantEdit, $entityContext);
		}
		elseif($k === 'STAGE_ID')
		{
			$entityData[$k] = array(
				'type' => 'custom',
				'data' => array(
					'html' => "<div class=\"order-detail-stage\"><div class=\"order-detail-stage-name\">{$stageText}</div>{$progressHtml}</div>"
				)
			);
		}
		elseif($k === 'CURRENCY_ID')
		{
			$entityData[$k] = __OrderQuickPanelViewPrepareCurrencyEnumeration($v, $enableInstantEdit, $entityContext);
		}
		elseif($k === 'OPPORTUNITY')
		{
			$entityData[$k] = __OrderQuickPanelViewPrepareMoney(
				isset($entityFields['~OPPORTUNITY']) ? $entityFields['~OPPORTUNITY'] : 0.0,
				isset($entityFields['~CURRENCY_ID']) ? $entityFields['~CURRENCY_ID'] : COrderCurrency::GetBaseCurrencyID(),
				$enableInstantEdit,
				$arResult['SERVICE_URL'],
				$entityContext
			);
		}
		elseif($k === 'BEGINDATE' || $k === 'CLOSEDATE')
		{
			$entityData[$k] = array(
				'type' => 'datetime',
				'editable'=> $enableInstantEdit,
				'data' => array(
					'text' => ($v !== null && $v !== '') ? ConvertTimeStamp(MakeTimeStamp($v), 'SHORT', SITE_ID) : '',
					'enableTime' => false
				)
			);
		}
		elseif($k === 'OPENED' || $k === 'CLOSED')
		{
			$v = ($v !== null && $v !== '') ? strtoupper($v) : 'N';
			$entityData[$k] = array(
				'type' => 'boolean',
				'editable'=> $enableInstantEdit,
				'data' => array('baseType' => 'char', 'value' => $v)
			);
		}
		elseif($k === 'COMPANY_ID')
		{
			$entityData['COMPANY_ID'] = __OrderQuickPanelViewPrepareClientInfo(COrderOwnerType::CompanyName, $entityContext);
		}
		elseif($k === 'CONTACT_ID')
		{
			$entityData['CONTACT_ID'] = __OrderQuickPanelViewPrepareClientInfo(COrderOwnerType::ContactName, $entityContext);
		}
		elseif($k === 'ASSIGNED_BY_ID')
		{
			$entityData['ASSIGNED_BY_ID'] = __OrderQuickPanelViewPrepareResponsible(
				$entityFields,
				$userProfilePath,
				$nameTemplate,
				$enableInstantEdit,
				$arResult['INSTANT_EDITOR_ID'],
				$arResult['SERVICE_URL']
			);
		}
		elseif($k === 'PROBABILITY')
		{
			$entityData[$k] = array(
				'type' => 'text',
				'editable'=> $enableInstantEdit,
				'data' => array(
					'baseType' => 'int',
					'text' => $entityFields["~{$k}"]
				)
			);
		}
		elseif($k === 'COMMENTS')
		{
			$entityData[$k] = array(
				'type' => 'html',
				'editable'=> $enableInstantEdit,
				'data' => array(
					'html' => $entityFields["~{$k}"],
					'serviceUrl' => $arResult['SERVICE_URL']
				)
			);
		}
		else
		{
			$entityData[$k] = array(
				'type' => 'text',
				'editable'=> $enableInstantEdit,
				'data' => array('text' => $entityFields["~{$k}"])
			);
		}

		$caption = isset($formFieldNames[$k]) ? $formFieldNames[$k] : '';
		if($caption === '')
		{
			$caption = COrderDeal::GetFieldCaption($k);
		}
		$entityData[$k]['caption'] = $caption;
	}

	$arResult['HEAD_TITLE'] = isset($entityFields['TITLE']) ? $entityFields['TITLE'] : '';
	$arResult['HEAD_TITLE_FIELD_ID'] = 'TITLE';
}
elseif($entityTypeID === COrderOwnerType::Lead)
{
	$entityContext['SIP_MANAGER_CONFIG'][COrderOwnerType::LeadName] = array(
		'ENTITY_TYPE' => COrderOwnerType::LeadName,
		'SERVICE_URL' => SITE_DIR.'bitrix/components/bitrix/order.lead.show/ajax.php?'.bitrix_sessid_get()
	);

	$ufEntityID = COrderLead::$sUFEntityID;
	$fieldKeys = array(
		'TITLE'=> true, 'COMPANY_TITLE' => true,
		'NAME' => true, 'SECOND_NAME' => true, 'LAST_NAME' => true,
		'STATUS_ID'=> true, 'STATUS_DESCRIPTION'=> true,
		'SOURCE_ID'=> true, 'SOURCE_DESCRIPTION'=> true,
		'CURRENCY_ID' => true, 'OPPORTUNITY' => true,
		'POST' => true, 'ADDRESS' => true,
		'BIRTHDATE' => true,
		'OPENED' => true,
		'ASSIGNED_BY_ID' => true,
		'COMMENTS' => true

	);

	if($enableDefaultConfig)
	{
		$config['left'] = 'SOURCE_ID,SOURCE_DESCRIPTION';
		$config['center'] = 'PHONE,EMAIL,IM';
		$config['right'] = 'ASSIGNED_BY_ID';
		$config['bottom'] = 'COMMENTS';
	}

	$arResult['HEAD_PROGRESS_LEGEND'] = isset($entityFields['~STATUS_TEXT']) ? $entityFields['~STATUS_TEXT'] : '';
	$statusText = htmlspecialcharsbx($arResult['HEAD_PROGRESS_LEGEND']);
	$progressHtml = $arResult['HEAD_PROGRESS_BAR'] = COrderViewHelper::RenderProgressControl(
		array(
			'ENTITY_TYPE_NAME' => COrderOwnerType::LeadName,
			'REGISTER_SETTINGS' => true,
			'CONTROL_ID' =>  $arResult['GUID'],
			'ENTITY_ID' => $entityFields['~ID'],
			'CURRENT_ID' => $entityFields['~STATUS_ID'],
			'SERVICE_URL' => '/bitrix/components/bitrix/order.lead.list/list.ajax.php',
			'LEAD_CONVERT_URL' => isset($entityFields['PATH_TO_LEAD_CONVERT']) ? $entityFields['PATH_TO_LEAD_CONVERT'] : '',
			'READ_ONLY' => !$canEdit,
			'DISPLAY_LEGEND' => false
		)
	);

	$currencyID = isset($entityFields['~CURRENCY_ID'])
		? $entityFields['~CURRENCY_ID'] : COrderCurrency::GetBaseCurrencyID();
	$arResult['HEAD_FORMATTED_SUM'] = COrderCurrency::MoneyToString(
			isset($entityFields['~OPPORTUNITY']) ? $entityFields['~OPPORTUNITY'] : 0.0, $currencyID
	);
	$arResult['HEAD_SUM_FIELD_ID'] = 'OPPORTUNITY';

	foreach($entityFields as $k => $v)
	{
		if(!isset($fieldKeys[$k]))
		{
			continue;
		}

		if($k === 'SOURCE_ID')
		{
			$entityData[$k] = __OrderQuickPanelViewPrepareStatusEnumeration('SOURCE', $v, $enableInstantEdit, $entityContext);
		}
		elseif($k === 'STATUS_ID')
		{
			$entityData[$k] = array(
				'type' => 'custom',
				'data' => array(
					'html' => "<div class=\"order-detail-stage\"><div class=\"order-detail-stage-name\">{$statusText}</div>{$progressHtml}</div>"
				)
			);
		}
		elseif($k === 'CURRENCY_ID')
		{
			$entityData[$k] = __OrderQuickPanelViewPrepareCurrencyEnumeration($v, $enableInstantEdit, $entityContext);
		}
		elseif($k === 'OPPORTUNITY')
		{
			$entityData[$k] = __OrderQuickPanelViewPrepareMoney(
				isset($entityFields['~OPPORTUNITY']) ? $entityFields['~OPPORTUNITY'] : 0.0,
				isset($entityFields['~CURRENCY_ID']) ? $entityFields['~CURRENCY_ID'] : COrderCurrency::GetBaseCurrencyID(),
				$enableInstantEdit,
				$arResult['SERVICE_URL'],
				$entityContext
			);
		}
		elseif($k === 'BIRTHDATE')
		{
			$entityData[$k] = array(
				'type' => 'datetime',
				'editable'=> $enableInstantEdit,
				'data' => array(
					'text' => ($v !== null && $v !== '') ? ConvertTimeStamp(MakeTimeStamp($v), 'SHORT', SITE_ID) : '',
					'enableTime' => false
				)
			);
		}
		elseif($k === 'OPENED')
		{
			$v = ($v !== null && $v !== '') ? strtoupper($v) : 'N';
			$entityData[$k] = array(
				'type' => 'boolean',
				'editable'=> $enableInstantEdit,
				'data' => array('baseType' => 'char', 'value' => $v)
			);
		}
		elseif($k === 'ASSIGNED_BY_ID')
		{
			$entityData['ASSIGNED_BY_ID'] = __OrderQuickPanelViewPrepareResponsible(
				$entityFields,
				$userProfilePath,
				$nameTemplate,
				$enableInstantEdit,
				$arResult['INSTANT_EDITOR_ID'],
				$arResult['SERVICE_URL']
			);
		}
		elseif($k === 'COMMENTS')
		{
			$entityData[$k] = array(
				'type' => 'html',
				'editable'=> $enableInstantEdit,
				'data' => array(
					'html' => $entityFields["~{$k}"],
					'serviceUrl' => $arResult['SERVICE_URL']
				)
			);
		}
		elseif($k === 'ADDRESS')
		{
			$entityData[$k] = array(
				'type' => 'address',
				'editable'=> false,
				'data' => array('lines' => LeadAddressFormatter::prepareLines($entityFields, array('NL2BR' => true)))
			);
		}
		elseif($k === 'STATUS_DESCRIPTION' || $k === 'SOURCE_DESCRIPTION')
		{
			$entityData[$k] = array(
				'type' => 'text',
				'editable'=> $enableInstantEdit,
				'data' => array('text' => $entityFields["~{$k}"], 'multiline' => true)
			);
		}
		else
		{
			$entityData[$k] = array(
				'type' => 'text',
				'editable'=> $enableInstantEdit,
				'data' => array('text' => $entityFields["~{$k}"])
			);
		}

		$caption = isset($formFieldNames[$k]) ? $formFieldNames[$k] : '';
		if($caption === '')
		{
			$caption = COrderLead::GetFieldCaption($k);
		}
		$entityData[$k]['caption'] = $caption;
	}

	$arResult['HEAD_TITLE'] = isset($entityFields['TITLE']) ? $entityFields['TITLE'] : '';
	$arResult['HEAD_TITLE_FIELD_ID'] = 'TITLE';
}
elseif($entityTypeID === COrderOwnerType::Quote)
{
	$entityContext['SIP_MANAGER_CONFIG'][COrderOwnerType::QuoteName] = array(
		'ENTITY_TYPE' => COrderOwnerType::QuoteName,
		'SERVICE_URL' => SITE_DIR.'bitrix/components/bitrix/order.quote.show/ajax.php?'.bitrix_sessid_get()
	);

	if($enableDefaultConfig)
	{
		$config['left'] = 'CLOSEDATE,LEAD_ID,DEAL_ID';
		$config['center'] = 'CONTACT_ID,COMPANY_ID';
		$config['right'] = 'ASSIGNED_BY_ID';
		$config['bottom'] = 'COMMENTS';
	}

	$ufEntityID = COrderQuote::$sUFEntityID;
	$fieldKeys = array(
		'QUOTE_NUMBER' => true, 'TITLE' => true,
		'STATUS_ID' => true,
		'CURRENCY_ID' => true, 'OPPORTUNITY' => true,
		'CONTACT_ID' => true, 'COMPANY_ID' => true, 'LEAD_ID' => true, 'DEAL_ID' => true,
		'CLIENT_PHONE' => true, 'CLIENT_EMAIL' => true,
		'BEGINDATE' => true, 'CLOSEDATE' => true,
		'CLOSED' => true, 'OPENED' => true,
		'ASSIGNED_BY_ID' => true,
		'COMMENTS' => true
	);

	$arResult['HEAD_PROGRESS_LEGEND'] = isset($entityFields['~STATUS_TEXT']) ? $entityFields['~STATUS_TEXT'] : '';
	$statusText = htmlspecialcharsbx($arResult['HEAD_PROGRESS_LEGEND']);
	$progressHtml = $arResult['HEAD_PROGRESS_BAR'] = COrderViewHelper::RenderProgressControl(
		array(
			'ENTITY_TYPE_NAME' => COrderOwnerType::QuoteName,
			'REGISTER_SETTINGS' => true,
			'CONTROL_ID' =>  $arResult['GUID'],
			'ENTITY_ID' => $entityFields['~ID'],
			'CURRENT_ID' => $entityFields['~STATUS_ID'],
			'SERVICE_URL' => '/bitrix/components/bitrix/order.quote.list/list.ajax.php',
			'READ_ONLY' => !$canEdit,
			'DISPLAY_LEGEND' => false
		)
	);

	$currencyID = isset($entityFields['~CURRENCY_ID'])
		? $entityFields['~CURRENCY_ID'] : COrderCurrency::GetBaseCurrencyID();
	$arResult['HEAD_FORMATTED_SUM'] = COrderCurrency::MoneyToString(
			isset($entityFields['~OPPORTUNITY']) ? $entityFields['~OPPORTUNITY'] : 0.0, $currencyID
	);
	$arResult['HEAD_SUM_FIELD_ID'] = 'OPPORTUNITY';

	__OrderQuickPanelViewPrepareContactInfo($entityFields, $entityContext);
	__OrderQuickPanelViewPrepareCompanyInfo($entityFields, $entityContext);

	foreach($entityFields as $k => $v)
		{
			if(!isset($fieldKeys[$k]))
			{
				continue;
			}

			if($k === 'STATUS_ID')
			{
				$entityData[$k] = array(
					'type' => 'custom',
					'data' => array(
						'html' => "<div class=\"order-detail-stage\"><div class=\"order-detail-stage-name\">{$stageText}</div>{$progressHtml}</div>"
					)
				);
			}
			elseif($k === 'CURRENCY_ID')
			{
				$entityData[$k] = __OrderQuickPanelViewPrepareCurrencyEnumeration($v, $enableInstantEdit, $entityContext);
			}
			elseif($k === 'OPPORTUNITY')
			{
				$entityData[$k] = __OrderQuickPanelViewPrepareMoney(
					isset($entityFields['~OPPORTUNITY']) ? $entityFields['~OPPORTUNITY'] : 0.0,
					isset($entityFields['~CURRENCY_ID']) ? $entityFields['~CURRENCY_ID'] : COrderCurrency::GetBaseCurrencyID(),
					$enableInstantEdit,
					$arResult['SERVICE_URL'],
					$entityContext
				);
			}
			elseif($k === 'BEGINDATE' || $k === 'CLOSEDATE')
			{
				$entityData[$k] = array(
					'type' => 'datetime',
					'editable'=> $enableInstantEdit,
					'data' => array(
						'text' => ($v !== null && $v !== '') ? ConvertTimeStamp(MakeTimeStamp($v), 'SHORT', SITE_ID) : '',
						'enableTime' => false
					)
				);
			}
			elseif($k === 'OPENED' || $k === 'CLOSED')
			{
				$v = ($v !== null && $v !== '') ? strtoupper($v) : 'N';
				$entityData[$k] = array(
					'type' => 'boolean',
					'editable'=> $enableInstantEdit,
					'data' => array('baseType' => 'char', 'value' => $v)
				);
			}
			elseif($k === 'LEAD_ID')
			{
				$v = (int)$v;
				if($v > 0)
				{
					$entityData[$k] = array(
						'type' => 'link',
						'data' => array(
							'text' => COrderOwnerType::GetCaption(COrderOwnerType::Lead, $v),
							'url' => COrderOwnerType::GetShowUrl(COrderOwnerType::Lead, $v, true)
						)
					);
				}
				else
				{
					$entityData[$k] = array('type' => 'text', 'data' => array('text' => ''));
				}
			}
			elseif($k === 'DEAL_ID')
			{
				$v = (int)$v;
				if($v > 0)
				{
					$entityData[$k] = array(
						'type' => 'link',
						'data' => array(
							'text' => COrderOwnerType::GetCaption(COrderOwnerType::Deal, $v),
							'url' => COrderOwnerType::GetShowUrl(COrderOwnerType::Deal, $v, true)
						)
					);
				}
				else
				{
					$entityData[$k] = array('type' => 'text', 'data' => array('text' => ''));
				}
			}
			elseif($k === 'COMPANY_ID')
			{
				$entityData['COMPANY_ID'] = __OrderQuickPanelViewPrepareClientInfo(COrderOwnerType::CompanyName, $entityContext);
			}
			elseif($k === 'CONTACT_ID')
			{
				$entityData['CONTACT_ID'] = __OrderQuickPanelViewPrepareClientInfo(COrderOwnerType::ContactName, $entityContext);
			}
			elseif($k === 'ASSIGNED_BY_ID')
			{
				$entityData['ASSIGNED_BY_ID'] = __OrderQuickPanelViewPrepareResponsible(
					$entityFields,
					$userProfilePath,
					$nameTemplate,
					$enableInstantEdit,
					$arResult['INSTANT_EDITOR_ID'],
					$arResult['SERVICE_URL']
				);
			}
			elseif($k === 'CLIENT_PHONE')
			{
				$params = array('VALUE' => $v, 'VALUE_TYPE_ID' => 'WORK');
				$entityData['CLIENT_PHONE'] = array(
					'type' => 'text',
					'data' => array(
						'html' => COrderViewHelper::PrepareMultiFieldHtml(
							'PHONE',
							$params,
							array(
								'ENABLE_SIP' => true,
								'SIP_PARAMS' => array(
									'ENTITY_TYPE' => 'ORDER_'.$entityTypeName,
									'ENTITY_ID' => $entityID)
							)
						)
					)
				);
			}
			elseif($k === 'CLIENT_EMAIL')
			{
				$params = array('VALUE' => $v, 'VALUE_TYPE_ID' => 'WORK');
				$entityData['CLIENT_EMAIL'] = array(
					'type' => 'text',
					'data' => array(
						'html' => COrderViewHelper::PrepareMultiFieldHtml('EMAIL', $params)
					)
				);
			}
			elseif($k === 'COMMENTS')
			{
				$entityData[$k] = array(
					'type' => 'html',
					'editable'=> $enableInstantEdit,
					'data' => array(
						'html' => $entityFields["~{$k}"],
						'serviceUrl' => $arResult['SERVICE_URL']
					)
				);
			}
			else
			{
				$entityData[$k] = array(
					'type' => 'text',
					'editable'=> $enableInstantEdit,
					'data' => array('text' => $entityFields["~{$k}"])
				);
			}

			$caption = isset($formFieldNames[$k]) ? $formFieldNames[$k] : '';
			if($caption === '')
			{
				$caption = COrderQuote::GetFieldCaption($k);
			}
			$entityData[$k]['caption'] = $caption;
		}

	$arResult['HEAD_TITLE'] = isset($entityFields['TITLE']) ? $entityFields['TITLE'] : '';
	$arResult['HEAD_TITLE_FIELD_ID'] = 'TITLE';
}
elseif($entityTypeID === COrderOwnerType::Invoice)
{
	if($enableDefaultConfig)
	{
		$config['left'] = 'DATE_BILL,DATE_PAY_BEFORE,PAY_VOUCHER_DATE,UF_DEAL_ID,UF_QUOTE_ID';
		$config['center'] = 'CLIENT_ID';
		$config['right'] = 'RESPONSIBLE_ID';
		$config['bottom'] = 'COMMENTS';
	}

	$ufEntityID = COrderInvoice::$sUFEntityID;
	$fieldKeys = array(
		'ACCOUNT_NUMBER' => true, 'ORDER_TOPIC' => true,
		'STATUS_ID' => true,
		'PAY_VOUCHER_DATE' => true, 'PAY_VOUCHER_NUM' => true,
		'DATE_BILL' => true, 'DATE_PAY_BEFORE' => true,
		'REASON_MARKED_SUCCESS' => true, 'DATE_MARKED' => true, 'REASON_MARKED' => true,
		'RESPONSIBLE_ID' => true, 'CURRENCY' => true, 'PRICE' => true,
		'UF_CONTACT_ID' => true, 'UF_COMPANY_ID' => true,
		'UF_DEAL_ID' => true, 'UF_QUOTE_ID' => true,
		'PR_LOCATION' => true, 'PAYER_INFO' => true, 'PAY_SYSTEM_ID' => true,
		'COMMENTS' => true
	);

	$arResult['HEAD_PROGRESS_LEGEND'] = isset($entityFields['STATUS_TEXT']) ? $entityFields['STATUS_TEXT'] : '';
	$statusText = htmlspecialcharsbx($arResult['HEAD_PROGRESS_LEGEND']);
	$progressHtml = $arResult['HEAD_PROGRESS_BAR'] = COrderViewHelper::RenderProgressControl(
		array(
			'ENTITY_TYPE_NAME' => COrderOwnerType::InvoiceName,
			'REGISTER_SETTINGS' => true,
			'CONTROL_ID' =>  $arResult['GUID'],
			'ENTITY_ID' => $entityFields['ID'],
			'CURRENT_ID' => $entityFields['STATUS_ID'],
			'SERVICE_URL' => '/bitrix/components/bitrix/order.invoice.list/list.ajax.php',
			'READ_ONLY' => !$canEdit,
			'DISPLAY_LEGEND' => false
		)
	);

	$currencyID = isset($entityFields['CURRENCY'])
		? $entityFields['CURRENCY'] : COrderInvoice::GetCurrencyID();
	$arResult['HEAD_FORMATTED_SUM'] = COrderCurrency::MoneyToString(
			isset($entityFields['PRICE']) ? $entityFields['PRICE'] : 0.0, $currencyID
	);
	$arResult['HEAD_SUM_FIELD_ID'] = 'PRICE';

	__OrderQuickPanelViewPrepareContactInfo($entityFields, $entityContext, 'UF_CONTACT', false);
	__OrderQuickPanelViewPrepareCompanyInfo($entityFields, $entityContext, 'UF_COMPANY', false);

	$isSuccessfullStatus = isset($entityFields['STATUS_SUCCESS']) ? strtoupper($entityFields['STATUS_SUCCESS']) === 'Y' : false;
	$isFailedStatus = isset($entityFields['STATUS_FAILED']) ? strtoupper($entityFields['STATUS_FAILED']) === 'Y' : false;
	foreach($entityFields as $k => $v)
	{
		if(!isset($fieldKeys[$k]))
		{
			continue;
		}

		if($k === 'STATUS_ID')
		{
			$entityData[$k] = array(
				'type' => 'custom',
				'data' => array(
					'html' => "<div class=\"order-detail-stage\"><div class=\"order-detail-stage-name\">{$statusText}</div>{$progressHtml}</div>"
				)
			);
		}
		elseif($k === 'CURRENCY')
		{
			//HACK: EDIT FORM REFERS BY 'CURRENCY_ID'
			$k = 'CURRENCY_ID';
			$entityData[$k] = __OrderQuickPanelViewPrepareCurrencyEnumeration($v, $enableInstantEdit, $entityContext);
		}
		elseif($k === 'PAY_VOUCHER_DATE' || $k === 'DATE_BILL' || $k === 'DATE_PAY_BEFORE' || $k === 'DATE_MARKED')
		{
			$entityData[$k] = array(
				'type' => 'datetime',
				'editable'=> $enableInstantEdit,
				'data' => array(
					'text' => ($v !== null && $v !== '') ? ConvertTimeStamp(MakeTimeStamp($v), 'SHORT', SITE_ID) : '',
					'enableTime' => false
				)
			);
		}
		elseif($k === 'PRICE')
		{
			$entityData[$k] = __OrderQuickPanelViewPrepareMoney(
				isset($entityFields['PRICE']) ? $entityFields['PRICE'] : 0.0,
				$currencyID,
				$enableInstantEdit,
				$arResult['SERVICE_URL'],
				$entityContext
			);
		}
		elseif($k === 'UF_COMPANY_ID')
		{
			if($entityContext['COMPANY_INFO']['ID'] <= 0)
			{
				continue;
			}

			//HACK: EDIT FORM TREAT 'UF_COMPANY_ID' AS 'CLIENT_ID'
			$k = 'CLIENT_ID';
			$entityData[$k] = __OrderQuickPanelViewPrepareClientInfo(COrderOwnerType::CompanyName, $entityContext);
		}
		elseif($k === 'UF_CONTACT_ID')
		{
			if($entityContext['CONTACT_INFO']['ID'] <= 0)
			{
				continue;
			}

			if($entityContext['COMPANY_INFO']['ID'] <= 0)
			{
				//HACK: EDIT FORM TREAT 'UF_CONTACT_ID' AS 'CLIENT_ID'
				$k = 'CLIENT_ID';
			}
			if($entityContext['CONTACT_INFO']['ID'] <= 0)
			{
				$entityData[$k] = array('type' => 'text', 'data' => array('text' => GetMessage('ORDER_ENTITY_QPV_CLIENT_NOT_ASSIGNED')));
			}
			else
			{
				$entityData[$k] = __OrderQuickPanelViewPrepareClientInfo(COrderOwnerType::ContactName, $entityContext);
			}
		}
		elseif($k === 'UF_DEAL_ID')
		{
			$v = (int)$v;
			if($v <= 0)
			{
				$entityData[$k] = array('type' => 'text', 'data' => array('text' => GetMessage('ORDER_ENTITY_QPV_DEAL_NOT_ASSIGNED')));
			}
			else
			{
				$caption = isset($entityFields['UF_DEAL_TITLE']) ? $entityFields['UF_DEAL_TITLE'] : '';
				if($caption === '')
				{
					$caption = COrderOwnerType::GetCaption(COrderOwnerType::Deal, $v);
				}

				$showUrl = isset($entityFields['UF_DEAL_SHOW_URL']) ? $entityFields['UF_DEAL_SHOW_URL'] : '';
				if($showUrl === '')
				{
					$showUrl = COrderOwnerType::GetShowUrl(COrderOwnerType::Deal, $v, true);
				}

				if($showUrl === '')
				{
					$entityData[$k] = array(
						'type' => 'text',
						'data' => array('text' => $caption)
					);
				}
				else
				{
					$entityData[$k] = array(
						'type' => 'link',
						'data' => array('text' => $caption, 'url' => $showUrl)
					);
				}
			}
		}
		elseif($k === 'UF_QUOTE_ID')
		{
			$v = (int)$v;
			if($v <= 0)
			{
				$entityData[$k] = array('type' => 'text', 'data' => array('text' => GetMessage('ORDER_ENTITY_QPV_QUOTE_NOT_ASSIGNED')));
			}
			else
			{
				$caption = isset($entityFields['UF_QUOTE_TITLE']) ? $entityFields['UF_QUOTE_TITLE'] : '';
				if($caption === '')
				{
					$caption = COrderOwnerType::GetCaption(COrderOwnerType::Quote, $v);
				}

				$showUrl = isset($entityFields['UF_QUOTE_SHOW_URL']) ? $entityFields['UF_QUOTE_SHOW_URL'] : '';
				if($showUrl === '')
				{
					$showUrl = COrderOwnerType::GetShowUrl(COrderOwnerType::Quote, $v, true);
				}

				if($showUrl === '')
				{
					$entityData[$k] = array(
						'type' => 'text',
						'data' => array('text' => $caption)
					);
				}
				else
				{
					$entityData[$k] = array(
						'type' => 'link',
						'data' => array('text' => $caption, 'url' => $showUrl)
					);
				}
			}
		}
		elseif($k === 'RESPONSIBLE_ID')
		{
			$entityData['RESPONSIBLE_ID'] = __OrderQuickPanelViewPrepareResponsible(
				$entityFields,
				$userProfilePath,
				$nameTemplate,
				$enableInstantEdit,
				$arResult['INSTANT_EDITOR_ID'],
				$arResult['SERVICE_URL'],
				'RESPONSIBLE',
				false
			);
		}
		elseif($k === 'PR_LOCATION')
		{
			//HACK: EDIT FORM REFERS 'PR_LOCATION' BY 'LOCATION_ID'
			$k = 'LOCATION_ID';
			$entityData[$k] = array(
				'type' => 'text',
				'data' => array('text' => $v > 0 ? COrderLocations::getLocationString($v) : GetMessage('ORDER_ENTITY_QPV_LOCATION_NOT_ASSIGNED'))
			);
		}
		elseif($k === 'PAY_SYSTEM_ID')
		{
			$entityData[$k] = array(
				'type' => 'text',
				'data' => array('text' => isset($entityFields['PAY_SYSTEM_NAME']) ? $entityFields['PAY_SYSTEM_NAME'] : GetMessage('ORDER_ENTITY_QPV_PAY_SYSTEM_NOT_ASSIGNED'))
			);
		}
		elseif($k === 'COMMENTS')
		{
			$entityData[$k] = array(
				'type' => 'html',
				'editable'=> $enableInstantEdit,
				'data' => array(
					'html' => $entityFields[$k],
					'serviceUrl' => $arResult['SERVICE_URL']
				)
			);
		}
		else
		{
			$entityData[$k] = array(
				'type' => 'text',
				'editable'=> $enableInstantEdit,
				'data' => array('text' => $entityFields[$k])
			);
		}

		if($k === 'PAY_VOUCHER_DATE' || $k === 'PAY_VOUCHER_NUM' || $k == 'REASON_MARKED_SUCCESS')
		{
			$entityData[$k]['visible'] = $isSuccessfullStatus;
		}
		elseif($k === 'DATE_MARKED' || $k === 'REASON_MARKED')
		{
			$entityData[$k]['visible'] = $isFailedStatus;
		}

		$caption = isset($formFieldNames[$k]) ? $formFieldNames[$k] : '';
		if($caption === '')
		{
			$caption = COrderInvoice::GetFieldCaption($k);
		}
		$entityData[$k]['caption'] = $caption;
	}

	$arResult['HEAD_TITLE'] = isset($entityFields['ORDER_TOPIC']) ? htmlspecialcharsbx($entityFields['ORDER_TOPIC']) : '';
	$arResult['HEAD_TITLE_FIELD_ID'] = 'ORDER_TOPIC';
}
else
{
	ShowError(GetMessage('ORDER_ENTITY_QPV_ENTITY_TYPE_NAME_NOT_SUPPORTED'));
	return;
}

if($entityTypeID !== COrderOwnerType::Deal && $entityTypeID !== COrderOwnerType::Invoice && $entityTypeID !== COrderOwnerType::Quote)
{
	if(!(isset($entityFields['FM']) && is_array($entityFields['FM'])))
	{
		$entityFields['FM'] = __OrderQuickPanelViewLoadMultiFields($entityTypeName, $entityID);
	}
	if(isset($entityFields['FM']) && is_array($entityFields['FM']) && empty($entityFields['FM']))
	{
		$entityFields['FM']['PHONE']['n0'] = array('VALUE' => '', 'VALUE_TYPE' => 'WORK');
		$entityFields['FM']['EMAIL']['n0'] = array('VALUE' => '', 'VALUE_TYPE' => 'WORK');
	}
}
if(isset($entityFields['FM']))
{
	$entityContext['MULTI_FIELDS_OPTIONS'] = array(
		'STUB' => GetMessage('ORDER_ENTITY_QPV_MULTI_FIELD_NOT_ASSIGNED'),
		'ENABLE_SIP' => true,
		'SIP_PARAMS' => array(
			'ENTITY_TYPE' => 'ORDER_'.$entityTypeName,
			'ENTITY_ID' => $entityID)
	);
	foreach($entityFields['FM'] as $typeID => $multiFields)
	{
		$entityData[$typeID] = __OrderQuickPanelViewPrepareMultiFields($multiFields, $entityTypeName, $entityID, $typeID);
	}
}

if($ufEntityID !== '')
{
	$arUserFields = $USER_FIELD_MANAGER->GetUserFields($ufEntityID, $entityID, LANGUAGE_ID);

	// remove invoice reserved fields
	if ($ufEntityID === COrderInvoice::GetUserFieldEntityID())
		foreach (COrderInvoice::GetUserFieldsReserved() as $ufId)
			if (isset($arUserFields[$ufId]))
				unset($arUserFields[$ufId]);

	foreach($arUserFields as $fieldName => &$arUserField)
	{
		$editable = $enableInstantEdit && isset($arUserField['EDIT_IN_LIST']) && $arUserField['EDIT_IN_LIST'] === 'Y';
		if($arUserField['MULTIPLE'] === 'Y')
		{
			continue;
		}

		$userTypeID = $arUserField['USER_TYPE']['USER_TYPE_ID'];
		$value = isset($arUserField['VALUE']) ? $arUserField['VALUE'] : '';
		$caption = isset($formFieldNames[$fieldName]) ? $formFieldNames[$fieldName] : '';
		if($caption === '')
		{
			$caption = isset($arUserField['EDIT_FORM_LABEL']) ? $arUserField['EDIT_FORM_LABEL'] : $fieldName;
		}

		if($userTypeID === 'string' || $userTypeID === 'integer' || $userTypeID === 'double' || $userTypeID === 'datetime')
		{
			$entityData[$fieldName] = array(
				'type' => $userTypeID === 'datetime' ? 'datetime' : 'text',
				'editable'=> $editable,
				'caption' => $caption,
				'data' => array('text' => $value, 'multiline' => $userTypeID === 'string')
			);
		}
		elseif($userTypeID === 'enumeration')
		{
			$text = "";
			$enums = array();
			$enumEntity = new CUserFieldEnum();
			$dbResultEnum = $enumEntity->GetList(array('SORT'=>'ASC'), array('USER_FIELD_ID' => $arUserField['ID']));
			while ($enum = $dbResultEnum->Fetch())
			{
				$enums[] = array('ID' => $enum['ID'], 'VALUE' => $enum['VALUE']);

				if($text === '' && $value !== '' && $value === $enum['ID'])
				{
					$text = $enum['VALUE'];
				}
			}

			$entityData[$fieldName] = array(
				'type' => 'enumeration',
				'editable'=> $editable,
				'caption' => $caption,
				'data' => array(
					'value' => $value,
					'text' => $text,
					'items' => $enums
				)
			);
		}
		elseif($userTypeID === 'boolean')
		{
			$entityData[$fieldName] = array(
				'type' => 'boolean',
				'editable'=> $editable,
				'caption' => $caption,
				'data' => array('baseType' => 'int', 'value' => $value)
			);
		}
	}
	unset($arUserField);
}

$arResult['ENTITY_DATA'] = $entityData;
$arResult['ENTITY_FIELDS'] = $entityFields;
$arResult['CAN_EDIT_OTHER_SETTINGS'] = COrderAuthorizationHelper::CanEditOtherSettings();
$arResult['ENTITY_CONTEXT'] = $entityContext;
$arResult['CONFIG'] = $config;

$this->IncludeComponentTemplate();