<?php

IncludeModuleLangFile(__FILE__);

class COrderFormedGroup
{
	static public $sUFEntityID = 'ORDER_FORMED_GROUP';
	public $LAST_ERROR = '';
	public $cPerms = null;
	protected $bCheckPermission = true;
	//const TABLE_ALIAS = 'L';
	protected static $TYPE_NAME = 'FORMED_GROUP';

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
        $groupJoin = ' LEFT JOIN b_order_group G ON L.GROUP_ID=G.ID';
        $groupJoin .= ' LEFT JOIN b_order_nomen N ON N.ID=G.NOMEN_ID';
        $groupJoin .= ' LEFT JOIN b_order_direction D ON D.ID=N.DIRECTION_ID';

		$result = array(
			'ID' => array('FIELD' => 'L.ID', 'TYPE' => 'string'),

			'CREATED_DATE' => array('FIELD' => 'L.DATE_CREATE', 'TYPE' => 'datetime'),
			'CREATED_BY_ID' => array('FIELD' => 'L.CREATED_BY_ID', 'TYPE' => 'string'),
			'CREATED_BY_LOGIN' => array('FIELD' => 'U2.LOGIN', 'TYPE' => 'string', 'FROM' => $createdByJoin),
			'CREATED_BY_FULL_NAME' => array('FIELD' => 'CONCAT (UP2.LAST_NAME, " ", UP2.NAME, " ", UP2.SECOND_NAME)', 'TYPE' => 'string', 'FROM' => $createdByJoin),
			'CREATED_BY_EMAIL' => array('FIELD' => 'UP2.EMAIL', 'TYPE' => 'string', 'FROM' => $createdByJoin),

			'MODIFY_DATE' => array('FIELD' => 'L.DATE_MODIFY', 'TYPE' => 'datetime'),
			'MODIFY_BY_ID' => array('FIELD' => 'L.MODIFY_BY_ID', 'TYPE' => 'string'),
			'MODIFY_BY_LOGIN' => array('FIELD' => 'U3.LOGIN', 'TYPE' => 'string', 'FROM' => $modifyByJoin),
			'MODIFY_BY_FULL_NAME' => array('FIELD' => 'CONCAT (UP3.LAST_NAME, " ", UP3.NAME, " ", UP3.SECOND_NAME)', 'TYPE' => 'string', 'FROM' => $modifyByJoin),
			'MODIFY_BY_EMAIL' => array('FIELD' => 'UP3.EMAIL', 'TYPE' => 'string', 'FROM' => $modifyByJoin),

			'GROUP_ID' => array('FIELD' => 'L.GROUP_ID', 'TYPE' => 'string'),
			'GROUP_TITLE' => array('FIELD' => 'G.TITLE', 'TYPE' => 'string', 'FROM' => $groupJoin),
			'NOMEN_ID' => array('FIELD' => 'G.NOMEN_ID', 'TYPE' => 'string', 'FROM' => $groupJoin),
			'NOMEN_TITLE' => array('FIELD' => 'N.TITLE', 'TYPE' => 'string', 'FROM' => $groupJoin),
			'DIRECTION_ID' => array('FIELD' => 'N.DIRECTION_ID', 'TYPE' => 'string', 'FROM' => $groupJoin),
			'DIRECTION_TITLE' => array('FIELD' => 'D.TITLE', 'TYPE' => 'string', 'FROM' => $groupJoin),

			'DATE_START' => array('FIELD' => 'L.DATE_START', 'TYPE' => 'date'),
			'DATE_END' => array('FIELD' => 'L.DATE_END', 'TYPE' => 'date'),
			'TITLE' => array('FIELD' => 'CONCAT (G.TITLE," (",DATE_FORMAT(L.DATE_START,"%d.%m.%Y"),"-",DATE_FORMAT(L.DATE_END,"%d.%m.%Y"),")")', 'TYPE' => 'string', 'FROM' => $groupJoin),
			'ENROLLED' => array('FIELD' => '(SELECT COUNT(*) FROM b_order_reg R WHERE R.ENTITY_TYPE="FORMED_GROUP" AND R.ENTITY_ID=L.ID AND (R.STATUS="ENROLLED" OR R.STATUS="ARRANGED" OR R.STATUS="STATEMENT" OR R.STATUS="CONDITIONS"))', 'TYPE' => 'int'),
			'FREE' => array('FIELD' => 'L.MAX-(SELECT COUNT(*) FROM b_order_reg R WHERE R.ENTITY_TYPE="FORMED_GROUP" AND R.ENTITY_ID=L.ID AND (R.STATUS="RESERVED" OR R.STATUS="ENROLLED" OR R.STATUS="ARRANGED" OR R.STATUS="STATEMENT" OR R.STATUS="CONDITIONS"))', 'TYPE' => 'int'),
			'MAX' => array('FIELD' => 'L.MAX', 'TYPE' => 'int'),

			'PRIVATE' => array('FIELD' => 'CASE WHEN G.PRIVATE<>"Y" THEN "N" ELSE "Y" END', 'TYPE' => 'string', 'FROM' => $groupJoin),
		);
		return $result;
	}

    public static function GetListEx($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $arOptions = array())
	{
		global $DBType;
		$lb = new COrderEntityListBuilder(
			$DBType,
			'b_order_formed_group',
			'L',
			self::GetFields()
		);
		return $lb->Prepare($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields, $arOptions);
	}
    
    public static function CompareFields($arFieldsOrig, $arFieldsModif) {
		$arMsg = Array();

		if (isset($arFieldsOrig['GROUP_ID']) && isset($arFieldsModif['GROUP_ID'])
			&& $arFieldsOrig['GROUP_ID'] != $arFieldsModif['GROUP_ID'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'GROUP_ID',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_GROUP_ID'),
				'VALUE_OLD' => $arFieldsOrig['GROUP_ID'],
				'VALUE_NEW' => $arFieldsModif['GROUP_ID'],
			);

		if (array_key_exists('DATE_START', $arFieldsOrig) && array_key_exists('DATE_START', $arFieldsModif) &&
			ConvertTimeStamp(strtotime($arFieldsOrig['DATE_START'])) != $arFieldsModif['DATE_START']
            && $arFieldsOrig['DATE_START'] != $arFieldsModif['DATE_START'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'DATE_START',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_DATE_START'),
				'VALUE_OLD' => $arFieldsOrig['DATE_START'],
				'VALUE_NEW' => $arFieldsModif['DATE_START'],
			);

		if (array_key_exists('DATE_END', $arFieldsOrig) && array_key_exists('DATE_END', $arFieldsModif) &&
			ConvertTimeStamp(strtotime($arFieldsOrig['DATE_END'])) != $arFieldsModif['DATE_END']
            && $arFieldsOrig['DATE_END'] != $arFieldsModif['DATE_END'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'DATE_END',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_DATE_END'),
				'VALUE_OLD' => $arFieldsOrig['DATE_END'],
				'VALUE_NEW' => $arFieldsModif['DATE_END'],
			);

		if (isset($arFieldsOrig['MAX']) && isset($arFieldsModif['MAX'])
			&& $arFieldsOrig['MAX'] != $arFieldsModif['MAX'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'MAX',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_MAX'),
				'VALUE_OLD' => $arFieldsOrig['MAX'],
				'VALUE_NEW' => $arFieldsModif['MAX'],
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

			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderFormedGroupUpdate');
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
						$this->LAST_ERROR = GetMessage('ORDER_FORMED_GROUP_UPDATE_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
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
					$arEvent['ENTITY_TYPE'] = 'FORMED_GROUP';
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
			$sUpdate = $DB->PrepareUpdate('b_order_formed_group', $arFields);
			if (strlen($sUpdate) > 0)
			{
				$DB->Query("UPDATE b_order_formed_group SET {$sUpdate} WHERE ID = '{$ID}'", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
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
				$afterEvents = GetModuleEvents('order', 'OnAfterOrderFormedGroupUpdate');
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
				$sEntityPerm = $userPerms->GetPermType('FORMED_GROUP', 'ADD');
				if ($sEntityPerm == BX_ORDER_PERM_NONE)
				{
					$this->LAST_ERROR = GetMessage('ORDER_PERMISSION_DENIED');
					$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					return false;
				}
			}


			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderFormedGroupAdd');
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
						$this->LAST_ERROR = GetMessage('ORDER_FORMED_GROUP_CREATION_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
						$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					}
					return false;
				}
			}

			$DB->Add('b_order_formed_group', $arFields, array(), 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);



			/*if($bUpdateSearch)
			{
				$arFilterTmp = Array('ID' => $ID);
				if (!$this->bCheckPermission)
					$arFilterTmp["CHECK_PERMISSIONS"] = "N";
				CCrmSearch::UpdateSearch($arFilterTmp, 'DEAL', true);
			}*/

			$result = $arFields['ID'];

			COrderEvent::RegisterAddEvent(self::$TYPE_NAME, $result, $iUserId);


			$afterEvents = GetModuleEvents('order', 'OnAfterOrderFormedGroupAdd');
			while ($arEvent = $afterEvents->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array(&$arFields));
			}

			if(isset($arFields['ORIGIN_ID']) && $arFields['ORIGIN_ID'] !== '')
			{
				$afterEvents = GetModuleEvents('order', 'OnAfterExternalOrderFormedGroupAdd');
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

		if (isset($arFields['GROUP_ID']) && $arFields['GROUP_ID']=='')
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_GROUP_ID')))."<br />\n";

		if (!empty($arFields['DATE_START']) && !CheckDateTime($arFields['DATE_START']))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_DATE_START')))."<br />\n";

		if (!empty($arFields['DATE_END']) && !CheckDateTime($arFields['DATE_END']))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_DATE_END')))."<br />\n";

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
			$this->LAST_ERROR.=GetMessage('ORDER_ERROR_NOT_FOUND').$ID."<br />";
			return false;
		}

		$sWherePerm = '';
		if ($this->bCheckPermission)
		{
			$sEntityPerm = $this->cPerms->GetPermType('FORMED_GROUP', 'DELETE');
			if ($sEntityPerm == BX_ORDER_PERM_NONE)
				return false;
			/*else if ($sEntityPerm == BX_CRM_PERM_SELF)
				$sWherePerm = " AND ASSIGNED_BY_ID = {$iUserId}";
			else if ($sEntityPerm == BX_CRM_PERM_OPEN)
				$sWherePerm = " AND (OPENED = 'Y' OR ASSIGNED_BY_ID = {$iUserId})";*/
		}

		$APPLICATION->ResetException();
		$events = GetModuleEvents('order', 'OnBeforeOrderFormedGroupDelete');
		while ($arEvent = $events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if ($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}



		$dbRes = $DB->Query("DELETE FROM b_order_formed_group WHERE ID = '{$ID}'{$sWherePerm}", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
		if (is_object($dbRes) && $dbRes->AffectedRowsCount() > 0)
		{
			/*CCrmSearch::DeleteSearch('DEAL', $ID);

			$DB->Query("DELETE FROM b_crm_entity_perms WHERE ENTITY='DEAL' AND ENTITY_ID = $ID", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
			$GLOBALS['USER_FIELD_MANAGER']->Delete(self::$sUFEntityID, $ID);
			$CCrmFieldMulti = new CCrmFieldMulti();
			$CCrmFieldMulti->DeleteByElement('DEAL', $ID);*/
			$COrderEvent = new COrderEvent();
			$COrderEvent->DeleteByElement('FORMED_GROUP', $ID);




			//if(Bitrix\Crm\Settings\HistorySettings::getCurrent()->isDealDeletionEventEnabled())
			COrderEvent::RegisterDeleteEvent(BX_ORDER_FORMED_GROUP, $ID, $iUserId, array('FIELDS' => $arFields));

			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				$GLOBALS["CACHE_MANAGER"]->ClearByTag("order_entity_name_".BX_ORDER_FORMED_GROUP_ID."_".$ID);
			}

			$afterEvents = GetModuleEvents('order', 'OnAfterOrderFormedGroupDelete');
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
		return COrderAuthorizationHelper::CheckUpdatePermission(self::$TYPE_NAME, $ID, $userPermissions);
	}

	public static function CheckDeletePermission($ID, $userPermissions = null)
	{
		return COrderAuthorizationHelper::CheckDeletePermission(self::$TYPE_NAME, $ID, $userPermissions);
	}

	public static function CheckReadPermission($ID = 0, $userPermissions = null)
	{
		return COrderAuthorizationHelper::CheckReadPermission(self::$TYPE_NAME, $ID, $userPermissions);
	}

	public static function GetListPopup($arOrder,$arFilter) {

	}
}