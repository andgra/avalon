<?php
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC','Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
global $DB, $APPLICATION;
if(!function_exists('__OrderConfigPermsEndResonse'))
{
	function __OrderConfigPermsEndResonse($result)
	{
		$GLOBALS['APPLICATION']->RestartBuffer();
		Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
		if(!empty($result))
		{
			echo CUtil::PhpToJSObject($result);
		}
		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
		die();
	}
}

if (!CModule::IncludeModule('order'))
{
	__OrderConfigPermsEndResonse(array('ERROR' => 'Could not include order module.'));
}


/*
 * ONLY 'POST' METHOD SUPPORTED
 * SUPPORTED ACTIONS:
 * 'REBUILD_ENTITY_ATTRS' - Rebuild entity attributes
 */

$curUser = COrderSecurityHelper::GetCurrentUser();
if (!$curUser || !$curUser->IsAuthorized() || !check_bitrix_sessid() || $_SERVER['REQUEST_METHOD'] != 'POST')
{
	__OrderConfigPermsEndResonse(array('ERROR' => 'Access denied.'));
}

$action = isset($_POST['ACTION']) ? strtoupper($_POST['ACTION']) : '';
if($action === 'REBUILD_ENTITY_ATTRS')
{
	__IncludeLang(dirname(__FILE__).'/lang/'.LANGUAGE_ID.'/'.basename(__FILE__));

	$params = isset($_POST['PARAMS']) && is_array($_POST['PARAMS']) ? $_POST['PARAMS'] : array();
	$entityTypeName = isset($params['ENTITY_TYPE_NAME']) ? $params['ENTITY_TYPE_NAME'] : '';
	if($entityTypeName === '')
	{
		__OrderConfigPermsEndResonse(array('ERROR' => 'Entity type is not specified.'));
	}

	$entityTypeID = COrderOwnerType::ResolveID($entityTypeName);
	if($entityTypeID === COrderOwnerType::Undefined)
	{
		__OrderConfigPermsEndResonse(array('ERROR' => 'Undefined entity type is specified.'));
	}

	if($entityTypeID === COrderOwnerType::Company)
	{
		if(!COrderCompany::CheckUpdatePermission(0))
		{
			__OrderConfigPermsEndResonse(array('ERROR' => 'Access denied.'));
		}

		if(COption::GetOptionString('order', '~ORDER_REBUILD_COMPANY_ATTR', 'N') !== 'Y')
		{
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'NOT_REQUIRED',
					'SUMMARY' => GetMessage('ORDER_CONFIG_PERMS_REBUILD_ATTR_NOT_REQUIRED_SUMMARY')
				)
			);
		}

		$progressData = COption::GetOptionString('order', '~ORDER_REBUILD_COMPANY_ATTR_PROGRESS',  '');
		$progressData = $progressData !== '' ? unserialize($progressData) : array();
		$lastItemID = isset($progressData['LAST_ITEM_ID']) ? intval($progressData['LAST_ITEM_ID']) : 0;
		$processedItemQty = isset($progressData['PROCESSED_ITEMS']) ? intval($progressData['PROCESSED_ITEMS']) : 0;
		$totalItemQty = isset($progressData['TOTAL_ITEMS']) ? intval($progressData['TOTAL_ITEMS']) : 0;
		if($totalItemQty <= 0)
		{
			$totalItemQty = COrderCompany::GetListEx(array(), array('CHECK_PERMISSIONS' => 'N'), array(), false);
		}

		$filter = array('CHECK_PERMISSIONS' => 'N');
		if($lastItemID > 0)
		{
			$filter['>ID'] = $lastItemID;
		}

		$dbResult = COrderCompany::GetListEx(
			array('ID' => 'ASC'),
			$filter,
			false,
			array('nTopCount' => 10),
			array('ID')
		);

		$itemIDs = array();
		$itemQty = 0;
		if(is_object($dbResult))
		{
			while($fields = $dbResult->Fetch())
			{
				$itemIDs[] = intval($fields['ID']);
				$itemQty++;
			}
		}

		if($itemQty > 0)
		{
			COrderCompany::RebuildEntityAccessAttrs($itemIDs);

			$progressData['TOTAL_ITEMS'] = $totalItemQty;
			$processedItemQty += $itemQty;
			$progressData['PROCESSED_ITEMS'] = $processedItemQty;
			$progressData['LAST_ITEM_ID'] = $itemIDs[$itemQty - 1];

			COption::SetOptionString('order', '~ORDER_REBUILD_COMPANY_ATTR_PROGRESS', serialize($progressData));
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'PROGRESS',
					'PROCESSED_ITEMS' => $processedItemQty,
					'TOTAL_ITEMS' => $totalItemQty,
					'SUMMARY' => GetMessage(
						'ORDER_CONFIG_PERMS_REBUILD_ATTR_PROGRESS_SUMMARY',
						array(
							'#PROCESSED_ITEMS#' => $processedItemQty,
							'#TOTAL_ITEMS#' => $totalItemQty
						)
					)
				)
			);
		}
		else
		{
			COption::RemoveOption('order', '~ORDER_REBUILD_COMPANY_ATTR');
			COption::RemoveOption('order', '~ORDER_REBUILD_COMPANY_ATTR_PROGRESS');
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'COMPLETED',
					'PROCESSED_ITEMS' => $processedItemQty,
					'TOTAL_ITEMS' => $totalItemQty,
					'SUMMARY' => GetMessage(
						'ORDER_CONFIG_PERMS_REBUILD_ATTR_COMPLETED_SUMMARY',
						array('#PROCESSED_ITEMS#' => $processedItemQty)
					)
				)
			);
		}
	}
	elseif($entityTypeID === COrderOwnerType::Contact)
	{
		if(!COrderContact::CheckUpdatePermission(0))
		{
			__OrderConfigPermsEndResonse(array('ERROR' => 'Access denied.'));
		}

		if(COption::GetOptionString('order', '~ORDER_REBUILD_CONTACT_ATTR', 'N') !== 'Y')
		{
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'NOT_REQUIRED',
					'SUMMARY' => GetMessage('ORDER_CONFIG_PERMS_REBUILD_ATTR_NOT_REQUIRED_SUMMARY')
				)
			);
		}

		$progressData = COption::GetOptionString('order', '~ORDER_REBUILD_CONTACT_ATTR_PROGRESS',  '');
		$progressData = $progressData !== '' ? unserialize($progressData) : array();
		$lastItemID = isset($progressData['LAST_ITEM_ID']) ? intval($progressData['LAST_ITEM_ID']) : 0;
		$processedItemQty = isset($progressData['PROCESSED_ITEMS']) ? intval($progressData['PROCESSED_ITEMS']) : 0;
		$totalItemQty = isset($progressData['TOTAL_ITEMS']) ? intval($progressData['TOTAL_ITEMS']) : 0;
		if($totalItemQty <= 0)
		{
			$totalItemQty = COrderContact::GetListEx(array(), array('CHECK_PERMISSIONS' => 'N'), array(), false);
		}

		$filter = array('CHECK_PERMISSIONS' => 'N');
		if($lastItemID > 0)
		{
			$filter['>ID'] = $lastItemID;
		}

		$dbResult = COrderContact::GetListEx(
			array('ID' => 'ASC'),
			$filter,
			false,
			array('nTopCount' => 10),
			array('ID')
		);

		$itemIDs = array();
		$itemQty = 0;
		if(is_object($dbResult))
		{
			while($fields = $dbResult->Fetch())
			{
				$itemIDs[] = intval($fields['ID']);
				$itemQty++;
			}
		}

		if($itemQty > 0)
		{
			COrderContact::RebuildEntityAccessAttrs($itemIDs);

			$progressData['TOTAL_ITEMS'] = $totalItemQty;
			$processedItemQty += $itemQty;
			$progressData['PROCESSED_ITEMS'] = $processedItemQty;
			$progressData['LAST_ITEM_ID'] = $itemIDs[$itemQty - 1];

			COption::SetOptionString('order', '~ORDER_REBUILD_CONTACT_ATTR_PROGRESS', serialize($progressData));
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'PROGRESS',
					'PROCESSED_ITEMS' => $processedItemQty,
					'TOTAL_ITEMS' => $totalItemQty,
					'SUMMARY' => GetMessage(
						'ORDER_CONFIG_PERMS_REBUILD_ATTR_PROGRESS_SUMMARY',
						array(
							'#PROCESSED_ITEMS#' => $processedItemQty,
							'#TOTAL_ITEMS#' => $totalItemQty
						)
					)
				)
			);
		}
		else
		{
			COption::RemoveOption('order', '~ORDER_REBUILD_CONTACT_ATTR');
			COption::RemoveOption('order', '~ORDER_REBUILD_CONTACT_ATTR_PROGRESS');
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'COMPLETED',
					'PROCESSED_ITEMS' => $processedItemQty,
					'TOTAL_ITEMS' => $totalItemQty,
					'SUMMARY' => GetMessage(
						'ORDER_CONFIG_PERMS_REBUILD_ATTR_COMPLETED_SUMMARY',
						array('#PROCESSED_ITEMS#' => $processedItemQty)
					)
				)
			);
		}
	}
	elseif($entityTypeID === COrderOwnerType::Deal)
	{
		if(!COrderDeal::CheckUpdatePermission(0))
		{
			__OrderConfigPermsEndResonse(array('ERROR' => 'Access denied.'));
		}

		if(COption::GetOptionString('order', '~ORDER_REBUILD_DEAL_ATTR', 'N') !== 'Y')
		{
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'NOT_REQUIRED',
					'SUMMARY' => GetMessage('ORDER_CONFIG_PERMS_REBUILD_ATTR_NOT_REQUIRED_SUMMARY')
				)
			);
		}

		$progressData = COption::GetOptionString('order', '~ORDER_REBUILD_DEAL_ATTR_PROGRESS',  '');
		$progressData = $progressData !== '' ? unserialize($progressData) : array();
		$lastItemID = isset($progressData['LAST_ITEM_ID']) ? intval($progressData['LAST_ITEM_ID']) : 0;
		$processedItemQty = isset($progressData['PROCESSED_ITEMS']) ? intval($progressData['PROCESSED_ITEMS']) : 0;
		$totalItemQty = isset($progressData['TOTAL_ITEMS']) ? intval($progressData['TOTAL_ITEMS']) : 0;
		if($totalItemQty <= 0)
		{
			$totalItemQty = COrderDeal::GetListEx(array(), array('CHECK_PERMISSIONS' => 'N'), array(), false);
		}

		$filter = array('CHECK_PERMISSIONS' => 'N');
		if($lastItemID > 0)
		{
			$filter['>ID'] = $lastItemID;
		}

		$dbResult = COrderDeal::GetListEx(
			array('ID' => 'ASC'),
			$filter,
			false,
			array('nTopCount' => 10),
			array('ID')
		);

		$itemIDs = array();
		$itemQty = 0;
		if(is_object($dbResult))
		{
			while($fields = $dbResult->Fetch())
			{
				$itemIDs[] = intval($fields['ID']);
				$itemQty++;
			}
		}

		if($itemQty > 0)
		{
			COrderDeal::RebuildEntityAccessAttrs($itemIDs);

			$progressData['TOTAL_ITEMS'] = $totalItemQty;
			$processedItemQty += $itemQty;
			$progressData['PROCESSED_ITEMS'] = $processedItemQty;
			$progressData['LAST_ITEM_ID'] = $itemIDs[$itemQty - 1];

			COption::SetOptionString('order', '~ORDER_REBUILD_DEAL_ATTR_PROGRESS', serialize($progressData));
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'PROGRESS',
					'PROCESSED_ITEMS' => $processedItemQty,
					'TOTAL_ITEMS' => $totalItemQty,
					'SUMMARY' => GetMessage(
						'ORDER_CONFIG_PERMS_REBUILD_ATTR_PROGRESS_SUMMARY',
						array(
							'#PROCESSED_ITEMS#' => $processedItemQty,
							'#TOTAL_ITEMS#' => $totalItemQty
						)
					)
				)
			);
		}
		else
		{
			COption::RemoveOption('order', '~ORDER_REBUILD_DEAL_ATTR');
			COption::RemoveOption('order', '~ORDER_REBUILD_DEAL_ATTR_PROGRESS');
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'COMPLETED',
					'PROCESSED_ITEMS' => $processedItemQty,
					'TOTAL_ITEMS' => $totalItemQty,
					'SUMMARY' => GetMessage(
						'ORDER_CONFIG_PERMS_REBUILD_ATTR_COMPLETED_SUMMARY',
						array('#PROCESSED_ITEMS#' => $processedItemQty)
					)
				)
			);
		}
	}
	elseif($entityTypeID === COrderOwnerType::Lead)
	{
		if(!COrderLead::CheckUpdatePermission(0))
		{
			__OrderConfigPermsEndResonse(array('ERROR' => 'Access denied.'));
		}

		if(COption::GetOptionString('order', '~ORDER_REBUILD_LEAD_ATTR', 'N') !== 'Y')
		{
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'NOT_REQUIRED',
					'SUMMARY' => GetMessage('ORDER_CONFIG_PERMS_REBUILD_ATTR_NOT_REQUIRED_SUMMARY')
				)
			);
		}

		$progressData = COption::GetOptionString('order', '~ORDER_REBUILD_LEAD_ATTR_PROGRESS',  '');
		$progressData = $progressData !== '' ? unserialize($progressData) : array();
		$lastItemID = isset($progressData['LAST_ITEM_ID']) ? intval($progressData['LAST_ITEM_ID']) : 0;
		$processedItemQty = isset($progressData['PROCESSED_ITEMS']) ? intval($progressData['PROCESSED_ITEMS']) : 0;
		$totalItemQty = isset($progressData['TOTAL_ITEMS']) ? intval($progressData['TOTAL_ITEMS']) : 0;
		if($totalItemQty <= 0)
		{
			$totalItemQty = COrderLead::GetListEx(array(), array('CHECK_PERMISSIONS' => 'N'), array(), false);
		}

		$filter = array('CHECK_PERMISSIONS' => 'N');
		if($lastItemID > 0)
		{
			$filter['>ID'] = $lastItemID;
		}

		$dbResult = COrderLead::GetListEx(
			array('ID' => 'ASC'),
			$filter,
			false,
			array('nTopCount' => 10),
			array('ID')
		);

		$itemIDs = array();
		$itemQty = 0;
		if(is_object($dbResult))
		{
			while($fields = $dbResult->Fetch())
			{
				$itemIDs[] = intval($fields['ID']);
				$itemQty++;
			}
		}

		if($itemQty > 0)
		{
			COrderLead::RebuildEntityAccessAttrs($itemIDs);

			$progressData['TOTAL_ITEMS'] = $totalItemQty;
			$processedItemQty += $itemQty;
			$progressData['PROCESSED_ITEMS'] = $processedItemQty;
			$progressData['LAST_ITEM_ID'] = $itemIDs[$itemQty - 1];

			COption::SetOptionString('order', '~ORDER_REBUILD_LEAD_ATTR_PROGRESS', serialize($progressData));
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'PROGRESS',
					'PROCESSED_ITEMS' => $processedItemQty,
					'TOTAL_ITEMS' => $totalItemQty,
					'SUMMARY' => GetMessage(
						'ORDER_CONFIG_PERMS_REBUILD_ATTR_PROGRESS_SUMMARY',
						array(
							'#PROCESSED_ITEMS#' => $processedItemQty,
							'#TOTAL_ITEMS#' => $totalItemQty
						)
					)
				)
			);
		}
		else
		{
			COption::RemoveOption('order', '~ORDER_REBUILD_LEAD_ATTR');
			COption::RemoveOption('order', '~ORDER_REBUILD_LEAD_ATTR_PROGRESS');
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'COMPLETED',
					'PROCESSED_ITEMS' => $processedItemQty,
					'TOTAL_ITEMS' => $totalItemQty,
					'SUMMARY' => GetMessage(
						'ORDER_CONFIG_PERMS_REBUILD_ATTR_COMPLETED_SUMMARY',
						array('#PROCESSED_ITEMS#' => $processedItemQty)
					)
				)
			);
		}
	}
	elseif($entityTypeID === COrderOwnerType::Quote)
	{
		if(!COrderQuote::CheckUpdatePermission(0))
		{
			__OrderConfigPermsEndResonse(array('ERROR' => 'Access denied.'));
		}

		if(COption::GetOptionString('order', '~ORDER_REBUILD_QUOTE_ATTR', 'N') !== 'Y')
		{
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'NOT_REQUIRED',
					'SUMMARY' => GetMessage('ORDER_CONFIG_PERMS_REBUILD_ATTR_NOT_REQUIRED_SUMMARY')
				)
			);
		}

		$progressData = COption::GetOptionString('order', '~ORDER_REBUILD_QUOTE_ATTR_PROGRESS',  '');
		$progressData = $progressData !== '' ? unserialize($progressData) : array();
		$lastItemID = isset($progressData['LAST_ITEM_ID']) ? intval($progressData['LAST_ITEM_ID']) : 0;
		$processedItemQty = isset($progressData['PROCESSED_ITEMS']) ? intval($progressData['PROCESSED_ITEMS']) : 0;
		$totalItemQty = isset($progressData['TOTAL_ITEMS']) ? intval($progressData['TOTAL_ITEMS']) : 0;
		if($totalItemQty <= 0)
		{
			$totalItemQty = COrderQuote::GetList(array(), array('CHECK_PERMISSIONS' => 'N'), array(), false);
		}

		$filter = array('CHECK_PERMISSIONS' => 'N');
		if($lastItemID > 0)
		{
			$filter['>ID'] = $lastItemID;
		}

		$dbResult = COrderQuote::GetList(
			array('ID' => 'ASC'),
			$filter,
			false,
			array('nTopCount' => 10),
			array('ID')
		);

		$itemIDs = array();
		$itemQty = 0;
		if(is_object($dbResult))
		{
			while($fields = $dbResult->Fetch())
			{
				$itemIDs[] = intval($fields['ID']);
				$itemQty++;
			}
		}

		if($itemQty > 0)
		{
			COrderQuote::RebuildEntityAccessAttrs($itemIDs);

			$progressData['TOTAL_ITEMS'] = $totalItemQty;
			$processedItemQty += $itemQty;
			$progressData['PROCESSED_ITEMS'] = $processedItemQty;
			$progressData['LAST_ITEM_ID'] = $itemIDs[$itemQty - 1];

			COption::SetOptionString('order', '~ORDER_REBUILD_QUOTE_ATTR_PROGRESS', serialize($progressData));
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'PROGRESS',
					'PROCESSED_ITEMS' => $processedItemQty,
					'TOTAL_ITEMS' => $totalItemQty,
					'SUMMARY' => GetMessage(
						'ORDER_CONFIG_PERMS_REBUILD_ATTR_PROGRESS_SUMMARY',
						array(
							'#PROCESSED_ITEMS#' => $processedItemQty,
							'#TOTAL_ITEMS#' => $totalItemQty
						)
					)
				)
			);
		}
		else
		{
			COption::RemoveOption('order', '~ORDER_REBUILD_QUOTE_ATTR');
			COption::RemoveOption('order', '~ORDER_REBUILD_QUOTE_ATTR_PROGRESS');
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'COMPLETED',
					'PROCESSED_ITEMS' => $processedItemQty,
					'TOTAL_ITEMS' => $totalItemQty,
					'SUMMARY' => GetMessage(
						'ORDER_CONFIG_PERMS_REBUILD_ATTR_COMPLETED_SUMMARY',
						array('#PROCESSED_ITEMS#' => $processedItemQty)
					)
				)
			);
		}
	}
	elseif($entityTypeID === COrderOwnerType::Invoice)
	{
		if(!COrderInvoice::CheckUpdatePermission(0))
		{
			__OrderConfigPermsEndResonse(array('ERROR' => 'Access denied.'));
		}

		if(COption::GetOptionString('order', '~ORDER_REBUILD_INVOICE_ATTR', 'N') !== 'Y')
		{
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'NOT_REQUIRED',
					'SUMMARY' => GetMessage('ORDER_CONFIG_PERMS_REBUILD_ATTR_NOT_REQUIRED_SUMMARY')
				)
			);
		}

		$progressData = COption::GetOptionString('order', '~ORDER_REBUILD_INVOICE_ATTR_PROGRESS',  '');
		$progressData = $progressData !== '' ? unserialize($progressData) : array();
		$lastItemID = isset($progressData['LAST_ITEM_ID']) ? intval($progressData['LAST_ITEM_ID']) : 0;
		$processedItemQty = isset($progressData['PROCESSED_ITEMS']) ? intval($progressData['PROCESSED_ITEMS']) : 0;
		$totalItemQty = isset($progressData['TOTAL_ITEMS']) ? intval($progressData['TOTAL_ITEMS']) : 0;
		if($totalItemQty <= 0)
		{
			$totalItemQty = COrderInvoice::GetList(array(), array('CHECK_PERMISSIONS' => 'N'), array(), false);
		}

		$filter = array('CHECK_PERMISSIONS' => 'N');
		if($lastItemID > 0)
		{
			$filter['>ID'] = $lastItemID;
		}

		$dbResult = COrderInvoice::GetList(
			array('ID' => 'ASC'),
			$filter,
			false,
			array('nTopCount' => 10),
			array('ID')
		);

		$itemIDs = array();
		$itemQty = 0;
		if(is_object($dbResult))
		{
			while($fields = $dbResult->Fetch())
			{
				$itemIDs[] = intval($fields['ID']);
				$itemQty++;
			}
		}

		if($itemQty > 0)
		{
			COrderInvoice::RebuildEntityAccessAttrs($itemIDs);

			$progressData['TOTAL_ITEMS'] = $totalItemQty;
			$processedItemQty += $itemQty;
			$progressData['PROCESSED_ITEMS'] = $processedItemQty;
			$progressData['LAST_ITEM_ID'] = $itemIDs[$itemQty - 1];

			COption::SetOptionString('order', '~ORDER_REBUILD_INVOICE_ATTR_PROGRESS', serialize($progressData));
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'PROGRESS',
					'PROCESSED_ITEMS' => $processedItemQty,
					'TOTAL_ITEMS' => $totalItemQty,
					'SUMMARY' => GetMessage(
						'ORDER_CONFIG_PERMS_REBUILD_ATTR_PROGRESS_SUMMARY',
						array(
							'#PROCESSED_ITEMS#' => $processedItemQty,
							'#TOTAL_ITEMS#' => $totalItemQty
						)
					)
				)
			);
		}
		else
		{
			COption::RemoveOption('order', '~ORDER_REBUILD_INVOICE_ATTR');
			COption::RemoveOption('order', '~ORDER_REBUILD_INVOICE_ATTR_PROGRESS');
			__OrderConfigPermsEndResonse(
				array(
					'STATUS' => 'COMPLETED',
					'PROCESSED_ITEMS' => $processedItemQty,
					'TOTAL_ITEMS' => $totalItemQty,
					'SUMMARY' => GetMessage(
						'ORDER_CONFIG_PERMS_REBUILD_ATTR_COMPLETED_SUMMARY',
						array('#PROCESSED_ITEMS#' => $processedItemQty)
					)
				)
			);
		}
	}
	else
	{
		__OrderConfigPermsEndResonse(array('ERROR' => 'Specified entity type is not supported.'));
	}
}