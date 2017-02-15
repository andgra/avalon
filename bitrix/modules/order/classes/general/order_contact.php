<?php

IncludeModuleLangFile(__FILE__);

class COrderContact
{
	static public $sUFEntityID = 'ORDER_CONTACT';
	public $LAST_ERROR = '';
	public $cPerms = null;
	protected $bCheckPermission = true;
	const TABLE_ALIAS = 'L';
	protected static $TYPE_NAME = 'CONTACT';

	function __construct($bCheckPermission = true)
	{
		$this->bCheckPermission = $bCheckPermission;
		$this->cPerms = COrderPerms::GetCurrentUserPermissions();
	}
    public static function GetByID($id)
    {
        if(isset($id) && $id!=='') {
            $res=self::GetListEx(array(),array('=ID'=>$id));
            $arRes=$res->Fetch();
        }
        return isset($arRes)?$arRes:false;
    }

    public static function GetFields()
	{
        $createdByJoin = ' LEFT JOIN (b_user U2 JOIN b_uts_user UF2 ON U2.ID=UF2.VALUE_ID) ON L.CREATED_BY_ID = UF2.UF_GUID';
        $createdByJoin .= ' LEFT JOIN b_order_physical UP2 ON UF2.UF_GUID=UP2.ID';
        $modifyByJoin = ' LEFT JOIN (b_user U3 JOIN b_uts_user UF3 ON U3.ID=UF3.VALUE_ID) ON L.MODIFY_BY_ID = UF3.UF_GUID';
        $modifyByJoin .= ' LEFT JOIN b_order_physical UP3 ON UF3.UF_GUID=UP3.ID';
		$physicalJoin = ' LEFT JOIN b_order_physical P ON L.GUID = P.ID';
		$assignedJoin = ' LEFT JOIN b_order_physical P2 ON L.ASSIGNED_ID = P2.ID';
		$agentJoin = ' LEFT JOIN b_order_agent A ON L.AGENT_ID = A.ID LEFT JOIN b_order_physical AP ON A.ID=AP.ID';

		$result = array(
			'ID' => array('FIELD' => 'L.ID', 'TYPE' => 'string'),

			'CREATED_DATE' => array('FIELD' => 'L.DATE_CREATE', 'TYPE' => 'datetime'),
			'CREATED_BY_ID' => array('FIELD' => 'L.CREATED_BY_ID', 'TYPE' => 'int'),
			'CREATED_BY_LOGIN' => array('FIELD' => 'U2.LOGIN', 'TYPE' => 'string', 'FROM' => $createdByJoin),
			'CREATED_BY_FULL_NAME' => array('FIELD' => 'CONCAT (UP2.LAST_NAME, " ", UP2.NAME, " ", UP2.SECOND_NAME)', 'TYPE' => 'string', 'FROM' => $createdByJoin),
			'CREATED_BY_EMAIL' => array('FIELD' => 'UP2.EMAIL', 'TYPE' => 'string', 'FROM' => $createdByJoin),

			'MODIFY_DATE' => array('FIELD' => 'L.DATE_MODIFY', 'TYPE' => 'datetime'),
			'MODIFY_BY_ID' => array('FIELD' => 'L.MODIFY_BY_ID', 'TYPE' => 'int'),
			'MODIFY_BY_LOGIN' => array('FIELD' => 'U3.LOGIN', 'TYPE' => 'string', 'FROM' => $modifyByJoin),
			'MODIFY_BY_FULL_NAME' => array('FIELD' => 'CONCAT (UP3.LAST_NAME, " ", UP3.NAME, " ", UP3.SECOND_NAME)', 'TYPE' => 'string', 'FROM' => $modifyByJoin),
			'MODIFY_BY_EMAIL' => array('FIELD' => 'UP3.EMAIL', 'TYPE' => 'string', 'FROM' => $modifyByJoin),

			'SHARED' => array('FIELD' => 'L.SHARED', 'TYPE' => 'string'),
			'GUID' => array('FIELD' => 'L.GUID', 'TYPE' => 'string'),
			'FULL_NAME' => array('FIELD' => 'CONCAT (P.LAST_NAME, " ",P.NAME, " ",P.SECOND_NAME)', 'TYPE' => 'string', 'FROM' => $physicalJoin),
			'EMAIL' => array('FIELD' => 'P.EMAIL', 'TYPE' => 'string', 'FROM' => $physicalJoin),
			'PHONE' => array('FIELD' => 'P.PHONE', 'TYPE' => 'string', 'FROM' => $physicalJoin),
			'AGENT_ID' => array('FIELD' => 'L.AGENT_ID', 'TYPE' => 'string'),
			'AGENT_TITLE' => array('FIELD' => 'CASE A.LEGAL WHEN "Y" THEN A.TITLE ELSE CONCAT (AP.LAST_NAME, " ", AP.NAME, " ", AP.SECOND_NAME) END', 'TYPE' => 'string','FROM'=>$agentJoin),
			'AGENT_PHONE' => array('FIELD' => 'A.LEGAL_PHONE', 'TYPE' => 'string','FROM'=>$agentJoin),
			'AGENT_EMAIL' => array('FIELD' => 'A.LEGAL_EMAIL', 'TYPE' => 'string','FROM'=>$agentJoin),
			'START_DATE' => array('FIELD' => 'L.START_DATE', 'TYPE' => 'date'),
			'END_DATE' => array('FIELD' => 'L.END_DATE', 'TYPE' => 'date'),
			'ASSIGNED_ID' => array('FIELD' => 'L.ASSIGNED_ID', 'TYPE' => 'string'),
			'ASSIGNED_FULL_NAME' => array('FIELD' => 'CONCAT (P2.LAST_NAME, " ",P2.NAME, " ",P2.SECOND_NAME)', 'TYPE' => 'string', 'FROM' => $assignedJoin),
			'ASSIGNED_EMAIL' => array('FIELD' => 'P2.EMAIL', 'TYPE' => 'string', 'FROM' => $assignedJoin),
			'DESCRIPTION' => array('FIELD' => 'L.DESCRIPTION', 'TYPE' => 'string'),
		);
		return $result;
	}

    public static function GetListEx($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $arOptions = array())
	{
		global $DBType;
		$lb = new COrderEntityListBuilder(
			$DBType,
			'b_order_contact',
			'L',
			self::GetFields()
		);
		return $lb->Prepare($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields, $arOptions);
	}
	
    public static function CompareFields($arFieldsOrig, $arFieldsModif) {
		$arMsg = Array();

		if (isset($arFieldsOrig['SHARED']) && isset($arFieldsModif['SHARED'])
			&& $arFieldsOrig['SHARED'] != $arFieldsModif['SHARED'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'SHARED',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_SHARED'),
				'VALUE_OLD' => $arFieldsOrig['SHARED'],
				'VALUE_NEW' => $arFieldsModif['SHARED'],
			);

		if (array_key_exists('START_DATE', $arFieldsOrig) && array_key_exists('START_DATE', $arFieldsModif) &&
			ConvertTimeStamp(strtotime($arFieldsOrig['START_DATE'])) != $arFieldsModif['START_DATE']
            && $arFieldsOrig['START_DATE'] != $arFieldsModif['START_DATE'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'START_DATE',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_START_DATE'),
				'VALUE_OLD' => $arFieldsOrig['START_DATE'],
				'VALUE_NEW' => $arFieldsModif['START_DATE'],
			);

		if (array_key_exists('END_DATE', $arFieldsOrig) && array_key_exists('END_DATE', $arFieldsModif) &&
			ConvertTimeStamp(strtotime($arFieldsOrig['END_DATE'])) != $arFieldsModif['END_DATE']
            && $arFieldsOrig['END_DATE'] != $arFieldsModif['END_DATE'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'END_DATE',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_END_DATE'),
				'VALUE_OLD' => $arFieldsOrig['END_DATE'],
				'VALUE_NEW' => $arFieldsModif['END_DATE'],
			);

		if (isset($arFieldsOrig['ASSIGNED_ID']) && isset($arFieldsModif['ASSIGNED_ID'])
			&& $arFieldsOrig['ASSIGNED_ID'] != $arFieldsModif['ASSIGNED_ID'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'ASSIGNED_ID',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_ASSIGNED_ID'),
				'VALUE_OLD' => $arFieldsOrig['ASSIGNED_ID'],
				'VALUE_NEW' => $arFieldsModif['ASSIGNED_ID'],
			);

		if (isset($arFieldsOrig['GUID']) && isset($arFieldsModif['GUID'])
			&& $arFieldsOrig['GUID'] != $arFieldsModif['GUID'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'GUID',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_GUID'),
				'VALUE_OLD' => $arFieldsOrig['GUID'],
				'VALUE_NEW' => $arFieldsModif['GUID'],
			);

		if (isset($arFieldsOrig['AGENT_ID']) && isset($arFieldsModif['AGENT_ID'])
			&& $arFieldsOrig['AGENT_ID'] != $arFieldsModif['AGENT_ID'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'AGENT_ID',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_AGENT_ID'),
				'VALUE_OLD' => $arFieldsOrig['AGENT_ID'],
				'VALUE_NEW' => $arFieldsModif['AGENT_ID'],
			);

		if (isset($arFieldsOrig['DESCRIPTION']) && isset($arFieldsModif['DESCRIPTION'])
			&& $arFieldsOrig['DESCRIPTION'] != $arFieldsModif['DESCRIPTION'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'DESCRIPTION',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_DESCRIPTION'),
				'VALUE_OLD' => $arFieldsOrig['DESCRIPTION'],
				'VALUE_NEW' => $arFieldsModif['DESCRIPTION'],
			);


		return $arMsg;
	}

	public function Update($ID, array &$arFields, $bCompare = true,  $options = array()) {
        global $DB;

		$this->LAST_ERROR = '';

		if(!is_array($options))
		{
			$options = array();
		}

		$arFilterTmp = array('ID' => $ID);
		/*if (!$this->bCheckPermission)
			$arFilterTmp['CHECK_PERMISSIONS'] = 'N';*/

		$obRes = self::GetListEx(array(), $arFilterTmp);
		if (!($arRow = $obRes->Fetch()))
			return false;

		$iUserId = COrderHelper::GetCurrentUserID();

		if (isset($arFields['DATE_CREATE']))
		{
			unset($arFields['DATE_CREATE']);
		}

		if (isset($arFields['DATE_MODIFY']))
		{
			unset($arFields['DATE_MODIFY']);
		}

		$arFields['~DATE_MODIFY'] = $DB->CurrentTimeFunction();

		//Scavenging

        if (!isset($arFields['MODIFY_BY_ID']) || $arFields['MODIFY_BY_ID'] == '')
            $arFields['MODIFY_BY_ID'] = $iUserId;
        else
            $iUserId = $arFields['MODIFY_BY_ID'];


		$bResult = false;
		if (!$this->CheckFields($arFields, $ID, $options))
		{
			$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
		}
		else
		{
			if($this->bCheckPermission && !COrderAuthorizationHelper::CheckUpdatePermission(self::$TYPE_NAME, $ID, $this->cPerms))
			{
				$this->LAST_ERROR = GetMessage('ORDER_PERMISSION_DENIED');
				$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
				return false;
			}

			if(!isset($arFields['ID']))
			{
				$arFields['ID'] = $ID;
			}

			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderContactUpdate');
			while ($arEvent = $beforeEvents->Fetch())
			{
				if(ExecuteModuleEventEx($arEvent, array(&$arFields)) === false)
				{
					if(isset($arFields['RESULT_MESSAGE']))
					{
						$this->LAST_ERROR = $arFields['RESULT_MESSAGE'];
					}
					else
					{
						$this->LAST_ERROR = GetMessage('ORDER_CONTACT_UPDATE_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
						$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					}
					return false;
				}
			}
            
			if ($bCompare)
			{
				$arEvents = self::CompareFields($arRow, $arFields, $this->bCheckPermission);
				foreach($arEvents as $arEvent)
				{
					$arEvent['ENTITY_TYPE'] = 'CONTACT';
					$arEvent['ENTITY_ID'] = $ID;
					//$arEvent['EVENT_TYPE'] = 1;

					if(!isset($arEvent['USER_ID']))
					{
						if($iUserId!='')
						{
							$arEvent['USER_ID'] = $iUserId;
						}
						else if(isset($arFields['MODIFY_BY_ID']) && $arFields['MODIFY_BY_ID']!='')
						{
							$arEvent['USER_ID'] = $arFields['MODIFY_BY_ID'];
						}
					}

					$COrderEvent = new COrderEvent();
					$COrderEvent->Add($arEvent, $this->bCheckPermission);
				}
			}



			unset($arFields['ID']);
			$sUpdate = $DB->PrepareUpdate('b_order_contact', $arFields);
			if (strlen($sUpdate) > 0)
			{
				$DB->Query("UPDATE b_order_contact SET {$sUpdate} WHERE ID = '{$ID}'", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
				$bResult = true;
			}

			
			/*if(defined("BX_COMP_MANAGED_CACHE"))
			{
				static $arNameFields = array("TITLE");
				$bClear = false;
				foreach($arNameFields as $val)
				{
					if(isset($arFields[$val]))
					{
						$bClear = true;
						break;
					}
				}
				if ($bClear)
				{
					$GLOBALS["CACHE_MANAGER"]->ClearByTag("order_entity_name_".BX_ORDER_REG_ID."_".$ID);
				}
			}*/

			//CCrmPerms::UpdateEntityAttr('DEAL', $ID, $arEntityAttr);



			/*if($bUpdateSearch)
			{
				$arFilterTmp = Array('ID' => $ID);
				if (!$this->bCheckPermission)
					$arFilterTmp['CHECK_PERMISSIONS'] = 'N';
				CCrmSearch::UpdateSearch($arFilterTmp, 'DEAL', true);
			}*/

			$arFields['ID'] = $ID;

			/*if (isset($arFields['FM']) && is_array($arFields['FM']))
			{
				$CCrmFieldMulti = new CCrmFieldMulti();
				$CCrmFieldMulti->SetFields('DEAL', $ID, $arFields['FM']);
			}*/


			if($bResult)
			{
				$afterEvents = GetModuleEvents('order', 'OnAfterOrderContactUpdate');
				while ($arEvent = $afterEvents->Fetch())
					ExecuteModuleEventEx($arEvent, array(&$arFields));
			}

		}
		return $bResult;
    }

    public function Add(array &$arFields, $options = array())
	{
		global $DB;

		if(!is_array($options))
		{
			$options = array();
		}

		$this->LAST_ERROR = '';
		$iUserId = COrderHelper::GetCurrentUserID();


		if (isset($arFields['DATE_CREATE']))
			unset($arFields['DATE_CREATE']);
		$arFields['~DATE_CREATE'] = $DB->CurrentTimeFunction();
		$arFields['~DATE_MODIFY'] = $DB->CurrentTimeFunction();


        //Scavenging

        if (!isset($arFields['MODIFY_BY_ID']) || $arFields['MODIFY_BY_ID'] == '')
            $arFields['MODIFY_BY_ID'] = $iUserId;
        else
            $iUserId = $arFields['MODIFY_BY_ID'];
        if (!isset($arFields['CREATED_BY_ID']) || $arFields['CREATED_BY_ID'] == '')
            $arFields['CREATED_BY_ID'] = $iUserId;


		if (!$this->CheckFields($arFields, false, $options))
		{
			$result = false;
			$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
		}
		else
		{


			if($this->bCheckPermission)
			{
				$userPerms =  $iUserId == COrderPerms::GetCurrentUserID() ? $this->cPerms : COrderPerms::GetUserPermissions($iUserId);
				$sEntityPerm = $userPerms->GetPermType('CONTACT', 'ADD');
				if ($sEntityPerm == BX_ORDER_PERM_NONE)
				{
					$this->LAST_ERROR = GetMessage('ORDER_PERMISSION_DENIED');
					$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					return false;
				}
			}


			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderContactAdd');
			while ($arEvent = $beforeEvents->Fetch())
			{
				if(ExecuteModuleEventEx($arEvent, array(&$arFields)) === false)
				{
					if(isset($arFields['RESULT_MESSAGE']))
					{
						$this->LAST_ERROR = $arFields['RESULT_MESSAGE'];
					}
					else
					{
						$this->LAST_ERROR = GetMessage('ORDER_CONTACT_CREATION_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
						$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					}
					return false;
				}
			}

			$DB->Add('b_order_contact', $arFields, array(), 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);



			/*if($bUpdateSearch)
			{
				$arFilterTmp = Array('ID' => $ID);
				if (!$this->bCheckPermission)
					$arFilterTmp["CHECK_PERMISSIONS"] = "N";
				CCrmSearch::UpdateSearch($arFilterTmp, 'DEAL', true);
			}*/

			$result = $arFields['ID'];

			COrderEvent::RegisterAddEvent(self::$TYPE_NAME, $result, $iUserId);


			$afterEvents = GetModuleEvents('order', 'OnAfterOrderContactAdd');
			while ($arEvent = $afterEvents->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array(&$arFields));
			}

			if(isset($arFields['ORIGIN_ID']) && $arFields['ORIGIN_ID'] !== '')
			{
				$afterEvents = GetModuleEvents('order', 'OnAfterExternalOrderContactAdd');
				while ($arEvent = $afterEvents->Fetch())
				{
					ExecuteModuleEventEx($arEvent, array(&$arFields));
				}
			}
		}

		return $result;
	}

	public function CheckFields(&$arFields)
	{
		$this->LAST_ERROR = '';

		if (!empty($arFields['SHARED']) && ($arFields['SHARED']!='Y' && $arFields['SHARED']!='N'))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_SHARED')))."<br />\n";

		if (isset($arFields['GUID']) && $arFields['GUID']=='')
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_GUID')))."<br />\n";

		if (isset($arFields['AGENT_ID']) && $arFields['AGENT_ID']=='')
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_AGENT_ID')))."<br />\n";

		if (!empty($arFields['START_DATE']) && !CheckDateTime($arFields['START_DATE']))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_START_DATE')))."<br />\n";

		if (!empty($arFields['END_DATE']) && !CheckDateTime($arFields['END_DATE']))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_END_DATE')))."<br />\n";

		if (strlen($this->LAST_ERROR) > 0)
			return false;

		return true;
	}

    public function Delete($ID)
	{
		global $DB, $APPLICATION;

		$iUserId = COrderHelper::GetCurrentUserID();


		$dbResult = self::GetListEx(
			array(),
			array('=ID' => $ID, 'CHECK_PERMISSIONS' => 'N')
		);
		$arFields = is_object($dbResult) ? $dbResult->Fetch() : null;
		if(!is_array($arFields))
		{
            $this->LAST_ERROR.=GetMessage('ORDER_ERROR_NOT_FOUND').$ID."<br />\n";
			return false;
		}

		$sWherePerm = '';
		if ($this->bCheckPermission)
		{
			$sEntityPerm = $this->cPerms->GetPermType('CONTACT', 'DELETE');
			if ($sEntityPerm == BX_ORDER_PERM_NONE)
				return false;
			/*else if ($sEntityPerm == BX_CRM_PERM_SELF)
				$sWherePerm = " AND ASSIGNED_BY_ID = {$iUserId}";
			else if ($sEntityPerm == BX_CRM_PERM_OPEN)
				$sWherePerm = " AND (OPENED = 'Y' OR ASSIGNED_BY_ID = {$iUserId})";*/
		}

		$APPLICATION->ResetException();
		$events = GetModuleEvents('order', 'OnBeforeOrderContactDelete');
		while ($arEvent = $events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if ($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}



		$dbRes = $DB->Query("DELETE FROM b_order_contact WHERE ID = '{$ID}'{$sWherePerm}", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
		if (is_object($dbRes) && $dbRes->AffectedRowsCount() > 0)
		{
			/*CCrmSearch::DeleteSearch('DEAL', $ID);

			$DB->Query("DELETE FROM b_crm_entity_perms WHERE ENTITY='DEAL' AND ENTITY_ID = $ID", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
			$GLOBALS['USER_FIELD_MANAGER']->Delete(self::$sUFEntityID, $ID);
			$CCrmFieldMulti = new CCrmFieldMulti();
			$CCrmFieldMulti->DeleteByElement('DEAL', $ID);*/
			$COrderEvent = new COrderEvent();
			$COrderEvent->DeleteByElement('CONTACT', $ID);




			//if(Bitrix\Crm\Settings\HistorySettings::getCurrent()->isDealDeletionEventEnabled())
			COrderEvent::RegisterDeleteEvent(BX_ORDER_CONTACT, $ID, $iUserId, array('FIELDS' => $arFields));

			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				$GLOBALS["CACHE_MANAGER"]->ClearByTag("order_entity_name_".BX_ORDER_CONTACT_ID."_".$ID);
			}

			$afterEvents = GetModuleEvents('order', 'OnAfterOrderContactDelete');
			while ($arEvent = $afterEvents->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array($ID));
			}
		}
		return true;
	}

    public static function CheckCreatePermission($userPermissions = null)
    {
        return COrderAuthorizationHelper::CheckCreatePermission(self::$TYPE_NAME, $userPermissions);
    }

    public static function CheckUpdatePermission($ID, $userPermissions = null)
    {
        $el=self::GetByID($ID);
        return $el['SHARED']=='N' && COrderAuthorizationHelper::CheckUpdatePermission(self::$TYPE_NAME, $ID, $userPermissions);
    }

    public static function CheckDeletePermission($ID, $userPermissions = null)
    {
        $el=self::GetByID($ID);
        return $el['SHARED']=='N' && COrderAuthorizationHelper::CheckDeletePermission(self::$TYPE_NAME, $ID, $userPermissions);
    }

    public static function CheckReadPermission($ID = 0, $userPermissions = null)
    {
        return COrderAuthorizationHelper::CheckReadPermission(self::$TYPE_NAME, $ID, $userPermissions);
    }
}