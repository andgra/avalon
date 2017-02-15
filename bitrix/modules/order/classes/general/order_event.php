<?php

IncludeModuleLangFile(__FILE__);
use \Bitrix\Main\Type\Date;
use \Bitrix\Main\Type\DateTime;
use \Bitrix\Crm\Settings\HistorySettings;

class COrderEvent
{
	protected $cdb = null;
	protected $currentUserID = '';


	/** @var array  */
	function __construct()
	{
		global $DB;
		$this->cdb = $DB;

		$this->currentUserID = COrderHelper::GetCurrentUserID();
	}
	public function Add($arFields, $bPermCheck = true)
	{
		$err_mess = (self::err_mess()).'<br />Function: Add<br />Line: ';
		$db_events = GetModuleEvents('order', 'OnBeforeOrderAddEvent');
		while($arEvent = $db_events->Fetch())
			$arFields = ExecuteModuleEventEx($arEvent, array($arFields));

		if (isset($arFields['ENTITY']) && is_array($arFields['ENTITY']))
		{
			foreach($arFields['ENTITY'] as $key => $arEntity)
				if (!(isset($arEntity['ENTITY_TYPE']) && isset($arEntity['ENTITY_ID'])))
					unset($arEntity['ENTITY'][$key]);
		}
		else if (isset($arFields['ENTITY_TYPE']) && isset($arFields['ENTITY_ID']))
		{
			$arFields['ENTITY'] = array(
				array(
					'ENTITY_TYPE' => $arFields['ENTITY_TYPE'],
					'ENTITY_ID' => $arFields['ENTITY_ID'],
					'ENTITY_FIELD' => isset($arFields['ENTITY_FIELD']) ? $arFields['ENTITY_FIELD'] : ''
				)
			);
		}
		else
			return false;



		if (!$this->CheckFields($arFields))
			return false;




		$arFields_i = Array(
			'ID' 		    => isset($arFields['ID'])? $arFields['ID']: '',
			'ASSIGNED_BY_ID'=> isset($arFields['USER_ID']) ? $arFields['USER_ID'] : $this->currentUserID,
			'CREATED_BY_ID'	=> isset($arFields['USER_ID']) ? $arFields['USER_ID'] : $this->currentUserID,
			'EVENT_NAME' 	=> $arFields['EVENT_NAME'],
			'VALUE_OLD'     => isset($arFields['VALUE_OLD'])? $arFields['VALUE_OLD']: '',
			'VALUE_NEW'     => isset($arFields['VALUE_NEW'])? $arFields['VALUE_NEW']: '',
			'ENTITY_TYPE'   => isset($arFields['ENTITY_TYPE'])? $arFields['ENTITY_TYPE']: '',
			'ENTITY_ID'     => isset($arFields['ENTITY_ID'])? $arFields['ENTITY_ID']: '',
			'ENTITY_FIELD'  => isset($arFields['ENTITY_FIELD'])? $arFields['ENTITY_FIELD']: '',
		);

		if (isset($arFields['DATE_CREATE']) && !empty($arFields['DATE_CREATE']))
			$arFields_i['DATE_CREATE'] = $arFields['DATE_CREATE'];
		else
			$arFields_i['~DATE_CREATE'] = $this->cdb->GetNowFunction();

		$EVENT_ID = $this->cdb->Add('b_order_event', $arFields_i, array(), 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);

		$db_events = GetModuleEvents('order', 'OnAfterOrderAddEvent');
		while($arEvent = $db_events->Fetch())
			ExecuteModuleEventEx($arEvent, array($EVENT_ID, $arFields));

		return $EVENT_ID;
	}
	public function Share($srcEntity, $dstEntities, $typeName)
	{
		$typeName = strtoupper(strval($typeName));
		if($typeName === '')
		{
			return;
		}

		global $DB;
		$srcEntityType = isset($srcEntity['ENTITY_TYPE']) ? $DB->ForSql($srcEntity['ENTITY_TYPE']) : '';
		$srcEntityID = isset($srcEntity['ENTITY_ID']) ? $srcEntity['ENTITY_ID'] : '';

		if($srcEntityType === '' || $srcEntityID == '')
		{
			return;
		}

		$dbResult = null;
		if($typeName === 'MESSAGE')
		{
			$dbResult = $DB->Query("SELECT ID FROM b_crm_event WHERE ID IN (SELECT EVENT_ID FROM b_crm_event_relations WHERE ENTITY_TYPE = '{$srcEntityType}' AND ENTITY_ID = {$srcEntityID}) AND (EVENT_TYPE = 2 OR (EVENT_TYPE = 0 AND EVENT_ID = 'MESSAGE'))");
		}

		if($dbResult)
		{
			while($arResult = $dbResult->Fetch())
			{
				self::AddRelation($arResult['ID'], $dstEntities, false);
			}
		}
	}

	public function CheckFields($arFields)
	{
		$aMsg = array();

		if(!is_set($arFields, 'EVENT_NAME') || trim($arFields['EVENT_NAME'])=='')
			$aMsg[] = array('id'=>'EVENT_NAME', 'text'=>GetMessage('ORDER_EVENT_ERR_ENTITY_NAME'));

		if(isset($arFields['DATE_CREATE'])
			&& !empty($arFields['DATE_CREATE'])
			&& !CheckDateTime($arFields['DATE_CREATE'], FORMAT_DATETIME))
		{
			$aMsg[] = array('id'=>'EVENT_DATE', 'text'=>GetMessage('ORDER_EVENT_ERR_ENTITY_DATE_NOT_VALID'));
		}

		if(!empty($aMsg))
		{
			$e = new CAdminException($aMsg);
			$GLOBALS['APPLICATION']->ThrowException($e);
			return false;
		}

		return true;
	}
	public static function GetFields()
	{
		$createdByJoin = ' LEFT JOIN (b_user U JOIN b_uts_user UF ON U.ID=UF.VALUE_ID) ON L.CREATED_BY_ID = UF.UF_GUID';
		$createdByJoin .= ' LEFT JOIN b_order_physical UP ON UF.UF_GUID=UP.ID';

		$result = array(
			'ID' => array('FIELD' => 'L.ID', 'TYPE' => 'string'),

			'ENTITY_TYPE' => array('FIELD' => 'L.ENTITY_TYPE', 'TYPE' => 'string'),
			'ENTITY_ID' => array('FIELD' => 'L.ENTITY_ID', 'TYPE' => 'string'),
			'ENTITY_FIELD' => array('FIELD' => 'L.ENTITY_FIELD', 'TYPE' => 'string'),

			'EVENT_NAME' => array('FIELD' => 'L.EVENT_NAME', 'TYPE' => 'string'),
			'VALUE_OLD' => array('FIELD' => 'L.VALUE_OLD', 'TYPE' => 'string'),
			'VALUE_NEW' => array('FIELD' => 'L.VALUE_NEW', 'TYPE' => 'string'),

			'CREATED_BY_ID' => array('FIELD' => 'L.CREATED_BY_ID', 'TYPE' => 'string'),
			'CREATED_BY_LOGIN' => array('FIELD' => 'U.LOGIN', 'TYPE' => 'string', 'FROM'=> $createdByJoin),
			'CREATED_BY_NAME' => array('FIELD' => 'U.NAME', 'TYPE' => 'string', 'FROM'=> $createdByJoin),
			'CREATED_BY_LAST_NAME' => array('FIELD' => 'U.LAST_NAME', 'TYPE' => 'string', 'FROM'=> $createdByJoin),
			'CREATED_BY_SECOND_NAME' => array('FIELD' => 'U.SECOND_NAME', 'TYPE' => 'string', 'FROM'=> $createdByJoin),

			'DATE_CREATE' => array('FIELD' => 'L.DATE_CREATE', 'TYPE' => 'datetime'),
		);
		return $result;
	}

	// GetList with navigation support
	public static function GetListEx($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $arOptions = array())
	{
		if (isset($arFilter['ENTITY']))
		{
			if(is_string($arFilter['ENTITY']) && $arFilter['ENTITY'] !== '')
			{
				$ary = explode('_', $arFilter['ENTITY']);
				if(count($ary) === 2)
				{
					$arFilter['ENTITY_TYPE'] = COrderHelper::GetLongEntityType($ary[0]);
					$arFilter['ENTITY_ID'] = $ary[1];
				}
			}
			unset($arFilter['ENTITY']);
		}

		global $DBType;
		$lb = new CCrmEntityListBuilder(
			$DBType,
			'b_order_event',
			'L',
			self::GetFields()
		);
		return $lb->Prepare($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields, $arOptions);
	}
	/*public static function GetList($arSort=array(), $arFilter=Array(), $nPageTop = false)
	{
		global $DB, $USER;
		$currentUser = (isset($USER) && ((get_class($USER) === 'CUser') || ($USER instanceof CUser))) ? $USER : (new CUser());

		$arSqlSearch = Array();
		$strSqlSearch = "";
		$err_mess = (self::err_mess()).'<br />Function: GetList<br />Line: ';

		if (isset($arFilter['ENTITY']))
		{
			$ar = explode('_', $arFilter['ENTITY']);
			$arFilter['ENTITY_TYPE'] = CUserTypeCrm::GetLongEntityType($ar[0]);
			$arFilter['ENTITY_ID'] = intval($ar[1]);
			unset($arFilter['ENTITY']);
		}

		// permission check
		$strPermission = "";
		if (!$currentUser->IsAdmin())
		{
			$CCrmPerms = new CCrmPerms($currentUser->GetID());
			$arUserAttr = array();
			$arEntity = array();
			if (empty($arFilter['ENTITY_TYPE']))
				$arEntity = array('LEAD', 'DEAL', 'CONTACT', 'COMPANY', 'QUOTE');
			else if (is_array($arFilter['ENTITY_TYPE']))
				$arEntity = $arFilter['ENTITY_TYPE'];
			else
				$arEntity = array($arFilter['ENTITY_TYPE']);

			$arInEntity = array();
			foreach ($arEntity as $sEntityType)
			{
				$arEntityAttr = $CCrmPerms->GetUserAttrForSelectEntity($sEntityType, 'READ');
				$arUserAttr[$sEntityType] = $arEntityAttr;
			}

			if (empty($arUserAttr))
			{
				$CDBResult = new CDBResult();
				$CDBResult->InitFromArray(array());
				return $CDBResult;
			}

			$arUserPerm = array();
			foreach ($arUserAttr as $sEntityType => $_arAttrs)
			{
				if (isset($_arAttrs[0]) && is_array($_arAttrs[0]) && empty($_arAttrs[0]))
				{
					$arInEntity[] = $sEntityType;
					continue;
				}
				foreach ($_arAttrs as $_arAttr)
				{
					if (empty($_arAttr))
						continue;
					$_icnt = count($_arAttr);
					$_idcnt = -1;
					foreach ($_arAttr as $sAttr)
						if ($sAttr[0] == 'D')
							$_idcnt++;
					if ($_icnt == 1 && ($_idcnt == 1 || $_idcnt == -1))
						$_idcnt = 0;

					$arUserPerm[] = "(P.ENTITY = '$sEntityType' AND SUM(CASE WHEN P.ATTR = '".implode("' or P.ATTR = '", $_arAttr)."' THEN 1 ELSE 0 END) = ".($_icnt - $_idcnt).')';
				}
			}

			$arPermission = array();
			if (!empty($arInEntity))
				$arPermission[] = " CER.ENTITY_TYPE IN ('".implode("','", $arInEntity)."')";

			if (!empty($arUserPerm))
				$arPermission[] = "
						EXISTS(
							SELECT 1
							FROM b_crm_entity_perms P
							WHERE P.ENTITY = CER.ENTITY_TYPE AND CER.ENTITY_ID = P.ENTITY_ID
							GROUP BY P.ENTITY, P.ENTITY_ID
							HAVING ".implode(" \n\t\t\t\t\t\t\t\tOR ", $arUserPerm)."
						)";
			if (!empty($arPermission))
				$strPermission = 'AND ('.implode(' OR ', $arPermission).')';
		}

		$sOrder = '';
		foreach($arSort as $key => $val)
		{
			$ord = (strtoupper($val) <> 'ASC'? 'DESC':'ASC');
			switch (strtoupper($key))
			{
				case 'ID':	$sOrder .= ', CER.ID '.$ord; break;
				case 'CREATED_BY_ID':	$sOrder .= ', CE.CREATED_BY_ID '.$ord; break;
				case 'EVENT_TYPE':	$sOrder .= ', CE.EVENT_TYPE '.$ord; break;
				case 'ENTITY_TYPE':	$sOrder .= ', CER.ENTITY_TYPE '.$ord; break;
				case 'ENTITY_ID':	$sOrder .= ', CER.ENTITY_ID '.$ord; break;
				case 'EVENT_ID':	$sOrder .= ', CE.EVENT_ID '.$ord; break;
				case 'DATE_CREATE':	$sOrder .= ', CE.DATE_CREATE '.$ord; break;
				case 'EVENT_NAME':	$sOrder .= ', CE.EVENT_NAME 	 '.$ord; break;
				case 'ENTITY_FIELD':	$sOrder .= ', CER.ENTITY_FIELD 	 '.$ord; break;
			}
		}

		if (strlen($sOrder)<=0)
			$sOrder = 'CER.ID DESC';

		$strSqlOrder = ' ORDER BY '.TrimEx($sOrder,',');

		// where
		$arWhereFields = array(
			'ID' => array(
				'TABLE_ALIAS' => 'CER',
				'FIELD_NAME' => 'CER.ID',
				'FIELD_TYPE' => 'int',
				'JOIN' => false
			),
			'ENTITY_TYPE' => array(
				'TABLE_ALIAS' => 'CER',
				'FIELD_NAME' => 'CER.ENTITY_TYPE',
				'FIELD_TYPE' => 'string',
				'JOIN' => false
			),
			'EVENT_REL_ID' => array(
				'TABLE_ALIAS' => 'CER',
				'FIELD_NAME' => 'CER.EVENT_ID',
				'FIELD_TYPE' => 'string',
				'JOIN' => false
			),
			'EVENT_ID' => array(
				'TABLE_ALIAS' => 'CE',
				'FIELD_NAME' => 'CE.EVENT_ID',
				'FIELD_TYPE' => 'string',
				'JOIN' => false
			),
			'CREATED_BY_ID' => array(
				'TABLE_ALIAS' => 'CE',
				'FIELD_NAME' => 'CE.CREATED_BY_ID',
				'FIELD_TYPE' => 'int',
				'JOIN' => false
			),
			'ASSIGNED_BY_ID' => array(
				'TABLE_ALIAS' => 'CER',
				'FIELD_NAME' => 'CER.ASSIGNED_BY_ID',
				'FIELD_TYPE' => 'int',
				'JOIN' => false
			),
			'EVENT_TYPE' => array(
				'TABLE_ALIAS' => 'CE',
				'FIELD_NAME' => 'CE.EVENT_TYPE',
				'FIELD_TYPE' => 'string',
				'JOIN' => false
			),
			'EVENT_DESC' => array(
				'TABLE_ALIAS' => 'CE',
				'FIELD_NAME' => 'CE.EVENT_TEXT_1',
				'FIELD_TYPE' => 'string',
				'JOIN' => false
			),
			'ENTITY_ID' => array(
				'TABLE_ALIAS' => 'CER',
				'FIELD_NAME' => 'CER.ENTITY_ID',
				'FIELD_TYPE' => 'int',
				'JOIN' => false
			),
			'ENTITY_FIELD' => array(
				'TABLE_ALIAS' => 'CER',
				'FIELD_NAME' => 'CER.ENTITY_FIELD',
				'FIELD_TYPE' => 'string',
				'JOIN' => false
			),
			'DATE_CREATE' => array(
				'TABLE_ALIAS' => 'CE',
				'FIELD_NAME' => 'CE.DATE_CREATE',
				'FIELD_TYPE' => 'datetime',
				'JOIN' => false
			)
		);


		$obQueryWhere = new CSQLWhere();
		$obQueryWhere->SetFields($arWhereFields);
		if (!is_array($arFilter))
			$arFilter = array();
		$sQueryWhereFields = $obQueryWhere->GetQuery($arFilter);

		if (!empty($sQueryWhereFields))
			$strSqlSearch .= "\n\t\t\t\tAND ($sQueryWhereFields) ";

		$strSql = "
			SELECT
				CER.ID,
				CER.ENTITY_TYPE,
				CER.ENTITY_ID,
				CER.ENTITY_FIELD,
				".$DB->DateToCharFunction('CE.DATE_CREATE')." DATE_CREATE,
				CER.EVENT_ID,
				CE.EVENT_NAME,
				CE.EVENT_TYPE,
				CE.EVENT_TEXT_1,
				CE.EVENT_TEXT_2,
				CE.FILES,
				CE.CREATED_BY_ID,
				U.LOGIN as CREATED_BY_LOGIN,
				U.NAME as CREATED_BY_NAME,
				U.LAST_NAME as CREATED_BY_LAST_NAME,
				U.SECOND_NAME as CREATED_BY_SECOND_NAME
			FROM
				b_crm_event_relations CER,
				b_crm_event CE LEFT JOIN b_user U ON CE.CREATED_BY_ID = U.ID
			WHERE
				CER.EVENT_ID = CE.ID
				$strSqlSearch
				$strPermission
				$strSqlOrder";

		if ($nPageTop !== false)
		{
			$nPageTop = (int) $nPageTop;
			$strSql = $DB->TopSql($strSql, $nPageTop);
		}

		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		$res->SetUserFields(array('FILES' => array('MULTIPLE' => 'Y')));
		return $res;
	}*/
	public function DeleteByElement($entityId, $elementId)
	{
		$err_mess = (self::err_mess()).'<br>Function: DeleteByElement<br>Line: ';


		$db_events = GetModuleEvents('order', 'OnBeforeOrderEventDeleteByElement');
		while($arEvent = $db_events->Fetch())
			ExecuteModuleEventEx($arEvent, array($entityId, $elementId));

		if ($entityId == '' || $elementId == 0)
			return false;


		$res = $this->cdb->Query("DELETE FROM b_order_event WHERE 
          ENTITY_TYPE = '".$this->cdb->ForSql($entityId)."' AND ENTITY_ID = '$elementId'", false, $err_mess.__LINE__);

		return $res;
	}
	public function Delete($ID)
	{
		$err_mess = (self::err_mess()).'<br>Function: Delete<br>Line: ';

		$ID = IntVal($ID);

		$db_events = GetModuleEvents('order', 'OnBeforeOrderEventDelete');
		while($arEvent = $db_events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID));

		// if not admin - delete only self items
		if (!CCrmPerms::IsAdmin()) {
            $res = $this->cdb->Query("DELETE FROM b_order_event WHERE ID = '$ID' AND CREATED_BY_ID='$this->$this->currentUserID'", false, $err_mess.__LINE__);
		} else {
            $res = $this->cdb->Query("DELETE FROM b_order_event WHERE ID = '$ID'", false, $err_mess.__LINE__);
        }


		return $res;
	}

	static public function Rebind($entityTypeID, $srcEntityID, $dstEntityID)
	{
		$entityTypeName = CCrmOwnerType::ResolveName($entityTypeID);

		$sql = "SELECT R.EVENT_ID FROM b_crm_event_relations R
		INNER JOIN b_crm_event E ON R.EVENT_ID = E.ID
			AND R.ENTITY_TYPE = '{$entityTypeName}'
			AND R.ENTITY_ID = {$srcEntityID}
			AND E.EVENT_TYPE IN (0, 2)";

		global $DB;
		$err_mess = (self::err_mess()).'<br>Function: Rebind<br>Line: ';
		$dbResult = $DB->Query($sql, false, $err_mess.__LINE__);
		if(!is_object($dbResult))
		{
			return;
		}

		$IDs = array();
		while($fields = $dbResult->Fetch())
		{
			if(isset($fields['EVENT_ID']))
			{
				$IDs[] = $fields['EVENT_ID'];
			}
		}

		if(!empty($IDs))
		{
			$sql = 'UPDATE b_crm_event_relations SET ENTITY_ID = '.$dstEntityID.' WHERE EVENT_ID IN('.implode(',', $IDs).')';
			$DB->Query($sql, false, $err_mess.__LINE__);
		}
	}
	/*static public function RegisterViewEvent($entityTypeID, $entityID, $userID = 0)
	{
		if($userID <= 0)
		{
			$userID = CCrmSecurityHelper::GetCurrentUserID();
			if($userID <= 0)
			{
				return false;
			}
		}

		$eventType = CCrmEvent::TYPE_VIEW;
		$timestamp = time() + CTimeZone::GetOffset();
		$entityTypeName = CCrmOwnerType::ResolveName($entityTypeID);

		//Try to find last event
		global $DB;
		$dbResult = $DB->Query("
			SELECT MAX({$DB->DateToCharFunction('e.DATE_CREATE', 'FULL')}) DATE_CREATE
				FROM b_crm_event e INNER JOIN b_crm_event_relations r ON
					e.ID = r.EVENT_ID
					AND r.ENTITY_TYPE = '{$entityTypeName}'
					AND r.ENTITY_ID = {$entityID}
					AND e.EVENT_TYPE = {$eventType}
					AND e.CREATED_BY_ID = {$userID}");

		$ary = is_object($dbResult) ? $dbResult->Fetch() : null;
		if(is_array($ary))
		{
			$str = isset($ary['DATE_CREATE']) ? $ary['DATE_CREATE'] : '';
			if($str !== '')
			{
				//Event grouping interval in seconds
				$interval = HistorySettings::getCurrent()->getViewEventGroupingInterval() * 60;
				if($interval >= ($timestamp - MakeTimeStamp($str, FORMAT_DATETIME)))
				{
					return false;
				}
			}
		}

		$entity = new CCrmEvent();
		$entity->Add(
			array(
				'USER_ID' => $userID,
				'ENTITY_ID' => $entityID,
				'ENTITY_TYPE' => $entityTypeName,
				'EVENT_TYPE' => $eventType,
				'EVENT_NAME' => CCrmEvent::GetEventTypeName($eventType),
				'DATE_CREATE' => ConvertTimeStamp($timestamp, 'FULL', SITE_ID)
			),
			false
		);

		return true;
	}*/
	/*static public function RegisterExportEvent($entityTypeID, $entityID, $userID = 0)
	{
		if($userID <= 0)
		{
			$userID = CCrmSecurityHelper::GetCurrentUserID();
			if($userID <= 0)
			{
				return false;
			}
		}

		$eventType = CCrmEvent::TYPE_EXPORT;
		$timestamp = time() + CTimeZone::GetOffset();
		$entityTypeName = CCrmOwnerType::ResolveName($entityTypeID);

		$entity = new CCrmEvent();
		$entity->Add(
			array(
				'USER_ID' => $userID,
				'ENTITY_ID' => $entityID,
				'ENTITY_TYPE' => $entityTypeName,
				'EVENT_TYPE' => $eventType,
				'EVENT_NAME' => CCrmEvent::GetEventTypeName($eventType),
				'DATE_CREATE' => ConvertTimeStamp($timestamp, 'FULL', SITE_ID)
			),
			false
		);

		return true;
	}*/
	static public function RegisterDeleteEvent($entityType, $entityID, $userID = 0)
	{
		if($userID == "")
		{
			$userID = COrderHelper::GetCurrentUserID();
			if($userID == "")
			{
				return false;
			}
		}

		$timestamp = time() + CTimeZone::GetOffset();

		$entity = new COrderEvent();
		return (
			$entity->Add(
				array(
					'USER_ID' => $userID,
					'ENTITY_ID' => $entityID,
					'ENTITY_TYPE' => $entityType,
					'EVENT_NAME' => GetMessage('ORDER_DELETE_ENTITY'),
					'DATE_CREATE' => ConvertTimeStamp($timestamp, 'FULL', SITE_ID),
				),
				false
			)
		);
	}
	static public function RegisterAddEvent($entityType, $entityID, $userID = 0)
	{
		if($userID == "")
		{
			$userID = COrderHelper::GetCurrentUserID();
			if($userID == "")
			{
				return false;
			}
		}

		$timestamp = time() + CTimeZone::GetOffset();

		$entity = new COrderEvent();
		return (
			$entity->Add(
				array(
					'USER_ID' => $userID,
					'ENTITY_ID' => $entityID,
					'ENTITY_TYPE' => $entityType,
					'EVENT_NAME' => GetMessage('ORDER_ADD_ENTITY'),
					'DATE_CREATE' => ConvertTimeStamp($timestamp, 'FULL', SITE_ID),
				),
				false
			)
		);
	}
	private static function err_mess()
	{
		return '<br />Class: COrderEvent<br />File: '.__FILE__;
	}
}

?>
