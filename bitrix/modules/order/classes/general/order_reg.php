<?php

IncludeModuleLangFile(__FILE__);

class COrderReg
{
	static public $sUFEntityID = 'ORDER_REG';
	public $LAST_ERROR = '';
	public $cPerms = null;
	protected $bCheckPermission = true;
	const TABLE_ALIAS = 'L';
	protected static $TYPE_NAME = 'REG';

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
        $physicalJoin = ' LEFT JOIN b_order_physical P ON L.PHYSICAL_ID=P.ID';
        $appJoin = ' LEFT JOIN b_order_app A ON A.ID=L.APP_ID';
        $appJoin .= ' LEFT JOIN (b_user U4 JOIN b_uts_user UF4 ON U4.ID=UF4.VALUE_ID) ON A.ASSIGNED_ID = UF4.UF_GUID';
        $appJoin .= ' LEFT JOIN b_order_physical UP4 ON UF4.UF_GUID=UP4.ID';
        $appJoin .= ' LEFT JOIN b_order_agent AG ON A.AGENT_ID=AG.ID';
		$directionJoin = ' LEFT JOIN (
			SELECT CASE ENTITY_TYPE
				WHEN "DIRECTION" THEN L.ENTITY_ID
				WHEN "NOMEN" THEN (SELECT MAX(E.DIRECTION_ID) FROM b_order_nomen E WHERE E.ID=L.ENTITY_ID)
				WHEN "FORMED_GROUP" THEN (SELECT MAX(N.DIRECTION_ID) FROM b_order_formed_group FG LEFT JOIN b_order_group G ON FG.GROUP_ID=G.ID
					LEFT JOIN b_order_nomen N ON G.NOMEN_ID=N.ID WHERE FG.ID=L.ENTITY_ID)
				WHEN "GROUP" THEN (SELECT MAX(N.DIRECTION_ID) FROM b_order_group E LEFT JOIN b_order_nomen N ON E.NOMEN_ID=N.ID WHERE E.ID=L.ENTITY_ID) END DIRECTION_ID
			FROM b_order_reg';

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

			'SHARED' => array('FIELD' => 'L.SHARED', 'TYPE' => 'string'),
			'APP_ID' => array('FIELD' => 'L.APP_ID', 'TYPE' => 'string'),
			'ASSIGNED_ID' => array('FIELD' => 'A.ASSIGNED_ID', 'TYPE' => 'string', 'FROM' => $appJoin),
			'ASSIGNED_LOGIN' => array('FIELD' => 'U4.LOGIN', 'TYPE' => 'string', 'FROM' => $appJoin),
			'ASSIGNED_FULL_NAME' => array('FIELD' => 'CONCAT (UP4.LAST_NAME, " ", UP4.NAME, " ", UP4.SECOND_NAME)', 'TYPE' => 'string', 'FROM' => $appJoin),
			'ASSIGNED_EMAIL' => array('FIELD' => 'UP4.EMAIL', 'TYPE' => 'string', 'FROM' => $appJoin),
			'ENTITY_TYPE' => array('FIELD' => 'L.ENTITY_TYPE', 'TYPE' => 'string'),
			'ENTITY_ID' => array('FIELD' => 'L.ENTITY_ID', 'TYPE' => 'string'),
			'ENTITY_TITLE' => array('FIELD' => 'CASE ENTITY_TYPE '.
			    ' WHEN "DIRECTION" THEN (SELECT MAX(E.TITLE) FROM b_order_direction E WHERE E.ID=L.ENTITY_ID)'.
			    ' WHEN "NOMEN" THEN (SELECT MAX(E.TITLE) FROM b_order_nomen E WHERE E.ID=L.ENTITY_ID)'.
			    ' WHEN "FORMED_GROUP" THEN (SELECT MAX(CONCAT(E1.TITLE," (",DATE_FORMAT(E.DATE_START,"%d.%m.%Y"),"-",DATE_FORMAT(E.DATE_END,"%d.%m.%Y"),")")) FROM b_order_formed_group E'.
                ' INNER JOIN b_order_group E1 ON E.GROUP_ID=E1.ID WHERE E.ID=L.ENTITY_ID)'.
                ' WHEN "GROUP" THEN (SELECT MAX(E.TITLE) FROM b_order_group E WHERE E.ID=L.ENTITY_ID) END', 'TYPE' => 'string'),
			'PHYSICAL_ID' => array('FIELD' => 'L.PHYSICAL_ID', 'TYPE' => 'string'),
			'PHYSICAL_FULL_NAME' => array('FIELD' => 'CASE L.PHYSICAL_ID WHEN "" THEN L.PHYSICAL_FULL_NAME '.
                'ELSE CONCAT (P.LAST_NAME, " ", P.NAME, " ", P.SECOND_NAME) END', 'TYPE' => 'string', 'FROM' => $physicalJoin),
			'PAST' => array('FIELD' => 'L.PAST', 'TYPE' => 'string'),
			'STATUS' => array('FIELD' => 'L.STATUS', 'TYPE' => 'string'),
			'APP_STATUS' => array('FIELD' => 'CASE WHEN LEFT(A.STATUS, 9)="CONVERTED" THEN "CONVERTED" ELSE A.STATUS END', 'TYPE' => 'string', 'FROM' => $appJoin),
			'APP_STATUS_TEXT' => array('FIELD' => 'CASE WHEN LEFT(A.STATUS, 9)="CONVERTED" THEN SUBSTRING(A.STATUS,10) ELSE "" END', 'TYPE' => 'string', 'FROM' => $appJoin),
			'APP_LEGAL' => array('FIELD' => 'CASE WHEN A.AGENT_ID="" THEN A.AGENT_LEGAL ELSE AG.LEGAL END', 'TYPE' => 'string', 'FROM' => $appJoin),
			'APP_REG_COUNT' => array('FIELD' => '(SELECT COUNT(*) FROM b_order_reg AR WHERE AR.APP_ID=L.APP_ID)', 'TYPE' => 'string'),
			'PERIOD' => array('FIELD' => 'L.PERIOD', 'TYPE' => 'date'),
			'EXPIRED' => array('FIELD' => 'CASE WHEN L.PERIOD<NOW() THEN "Y" ELSE "N" END', 'TYPE' => 'string'),
			'ATTENTION' => array('FIELD' => 'CASE WHEN L.PERIOD<DATE_ADD(NOW(), INTERVAL 1 DAY) THEN "Y" ELSE "N" END', 'TYPE' => 'string'),
			'DESCRIPTION' => array('FIELD' => 'L.DESCRIPTION', 'TYPE' => 'string'),
			'DIRECTION' => array('FIELD' => 'CASE ENTITY_TYPE
				WHEN "DIRECTION" THEN L.ENTITY_ID
				WHEN "NOMEN" THEN (SELECT MAX(E.DIRECTION_ID) FROM b_order_nomen E WHERE E.ID=L.ENTITY_ID)
				WHEN "FORMED_GROUP" THEN (SELECT MAX(N.DIRECTION_ID) FROM b_order_formed_group FG LEFT JOIN b_order_group G ON FG.GROUP_ID=G.ID
					LEFT JOIN b_order_nomen N ON G.NOMEN_ID=N.ID WHERE FG.ID=L.ENTITY_ID)
				WHEN "GROUP" THEN (SELECT MAX(N.DIRECTION_ID) FROM b_order_group E LEFT JOIN b_order_nomen N ON E.NOMEN_ID=N.ID WHERE E.ID=L.ENTITY_ID) END', 'TYPE' => 'string'),

		);
		return $result;
	}

    public static function GetListEx($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $arOptions = array())
	{
		global $DBType;
		$lb = new COrderEntityListBuilder(
			$DBType,
			'b_order_reg',
			'L',
			self::GetFields()
		);
		return $lb->Prepare($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields, $arOptions);
	}

    public static function CompareFields($arFieldsOrig, $arFieldsModif, $bCheckPerms = true) {
		$arMsg = Array();

		if (isset($arFieldsOrig['SHARED']) && isset($arFieldsModif['SHARED'])
			&& $arFieldsOrig['SHARED'] != $arFieldsModif['SHARED'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'SHARED',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_SHARED'),
				'VALUE_OLD' => $arFieldsOrig['SHARED'],
				'VALUE_NEW' => $arFieldsModif['SHARED'],
			);

		if (isset($arFieldsOrig['PAST']) && isset($arFieldsModif['PAST'])
			&& $arFieldsOrig['PAST'] != $arFieldsModif['PAST'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'PAST',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_PAST'),
				'VALUE_OLD' => $arFieldsOrig['PAST'],
				'VALUE_NEW' => $arFieldsModif['PAST'],
			);

		if (isset($arFieldsOrig['STATUS']) && isset($arFieldsModif['STATUS'])
			&& $arFieldsOrig['STATUS'] != $arFieldsModif['STATUS'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'STATUS',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_STATUS'),
				'VALUE_OLD' => $arFieldsOrig['STATUS'],
				'VALUE_NEW' => $arFieldsModif['STATUS'],
			);

		if (isset($arFieldsOrig['ENTITY_TYPE']) && isset($arFieldsModif['ENTITY_TYPE'])
			&& isset($arFieldsOrig['ENTITY_ID']) && isset($arFieldsModif['ENTITY_ID'])
			&& ($arFieldsOrig['ENTITY_ID'] != $arFieldsModif['ENTITY_ID']
				|| 	strtolower($arFieldsOrig['ENTITY_TYPE']) != strtolower($arFieldsModif['ENTITY_TYPE'])))
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'ENTITY',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_ENTITY'),
				'VALUE_OLD' => strtolower($arFieldsOrig['ENTITY_TYPE']).'#_#'.$arFieldsOrig['ENTITY_ID'],
				'VALUE_NEW' => strtolower($arFieldsModif['ENTITY_TYPE']).'#_#'.$arFieldsModif['ENTITY_ID'],
			);


		$pID=isset($arFieldsOrig['PHYSICAL_ID'])?$arFieldsOrig['PHYSICAL_ID']:false;
		if (isset($arFieldsOrig['PHYSICAL_ID']) && isset($arFieldsModif['PHYSICAL_ID'])
			&& $arFieldsOrig['PHYSICAL_ID'] != $arFieldsModif['PHYSICAL_ID']) {
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'PHYSICAL_ID',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_PHYSICAL_ID'),
				'VALUE_OLD' => $arFieldsOrig['PHYSICAL_ID'],
				'VALUE_NEW' => $arFieldsModif['PHYSICAL_ID'],
			);
			$pID=$arFieldsModif['PHYSICAL_ID'];
		}

        if($pID==='') {
            if(isset($arFieldsOrig['PHYSICAL_FULL_NAME']) && isset($arFieldsModif['PHYSICAL_FULL_NAME'])
			&& $arFieldsOrig['PHYSICAL_FULL_NAME'] != $arFieldsModif['PHYSICAL_FULL_NAME'])
				$arMsg[] = Array(
					'ENTITY_FIELD' => 'PHYSICAL_FULL_NAME',
					'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_PHYSICAL_FULL_NAME'),
					'VALUE_OLD' => $arFieldsOrig['PHYSICAL_FULL_NAME'],
					'VALUE_NEW' => $arFieldsModif['PHYSICAL_FULL_NAME'],
				);
        }

		if (array_key_exists('PERIOD', $arFieldsOrig) && array_key_exists('PERIOD', $arFieldsModif) &&
			ConvertTimeStamp(strtotime($arFieldsOrig['PERIOD'])) != $arFieldsModif['PERIOD'] && $arFieldsOrig['PERIOD'] != $arFieldsModif['PERIOD'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'PERIOD',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_PERIOD'),
				'VALUE_OLD' => $arFieldsOrig['PERIOD'],
				'VALUE_NEW' => $arFieldsModif['PERIOD'],
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

	public function Update($ID, array &$arFields, $bCompare = true, $bUpdateSearch = true, $options = array()) {
        global $DB, $USER;

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




		$assignedID = isset($arFields['ASSIGNED_ID']) ? $arFields['ASSIGNED_ID'] : $arRow['ASSIGNED_ID'];

		$bResult = false;
		$arFields['APP_ID']=isset($arFields['APP_ID'])?$arFields['APP_ID']:$arRow['APP_ID'];
		$app=COrderApp::GetByID($arFields['APP_ID']);
		if (!$this->CheckFields($arFields, $app))
		{
			$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
		}
		else
		{
			if($this->bCheckPermission && !COrderAuthorizationHelper::CheckUpdatePermission(self::$TYPE_NAME, $ID, $this->cPerms)
				&& !in_array($assignedID,CAccess::GetUserCodesArray($USER->GetID())))
			{
				$this->LAST_ERROR = GetMessage('ORDER_PERMISSION_DENIED');
				$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
				return false;
			}

			if(!isset($arFields['ID']))
			{
				$arFields['ID'] = $ID;
			}

			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderRegUpdate');
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
						$this->LAST_ERROR = GetMessage('ORDER_REG_UPDATE_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
						$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					}
					return false;
				}
			}

			$arAttr = array();
			$arAttr['STATUS'] = !empty($arFields['STATUS']) ? $arFields['STATUS'] : $arRow['STATUS'];
			//$arAttr['OPENED'] = !empty($arFields['OPENED']) ? $arFields['OPENED'] : $arRow['OPENED'];
			//$arEntityAttr = self::BuildEntityAttr($assignedByID, $arAttr);
			//$sEntityPerm = $this->cPerms->GetPermType('REG', 'WRITE', $arEntityAttr);
			//$this->PrepareEntityAttrs($arEntityAttr, $sEntityPerm);
			//Prevent 'OPENED' field change by user restricted by BX_CRM_PERM_OPEN permission
			//if($sEntityPerm === BX_ORDER_PERM_OPEN && isset($arFields['OPENED']) && $arFields['OPENED'] !== 'Y' && $assignedByID !== $iUserId)
				//$arFields['OPENED'] = 'Y';


			//if (isset($arFields['ASSIGNED_BY_ID']) && $arRow['ASSIGNED_BY_ID'] != $arFields['ASSIGNED_BY_ID'])
				//CCrmEvent::SetAssignedByElement($arFields['ASSIGNED_BY_ID'], 'DEAL', $ID);

			$sonetEventData = array();
			if ($bCompare)
			{
				$arEvents = self::CompareFields($arRow, $arFields, $this->bCheckPermission);
				foreach($arEvents as $arEvent)
				{
					$arEvent['ENTITY_TYPE'] = 'REG';
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
			$sUpdate = $DB->PrepareUpdate('b_order_reg', $arFields);
			if (strlen($sUpdate) > 0)
			{
				$DB->Query("UPDATE b_order_reg SET {$sUpdate} WHERE ID = '{$ID}'", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
				$bResult = true;

				if($app) {
					$arEvent = Array(
						'ENTITY_FIELD' => 'REG',
						'EVENT_NAME' => GetMessage('ORDER_FIELD_APP_EVENT_UPDATE'),
						'VALUE_OLD' => '',
						'VALUE_NEW' => $ID,
					);
					$arEvent['ENTITY_TYPE'] = 'APP';
					$arEvent['ENTITY_ID'] = $arFields['APP_ID'];
					//$arEvent['EVENT_TYPE'] = 1;

					if ($iUserId != '') {
						$arEvent['USER_ID'] = $iUserId;
					} else if (isset($arFields['MODIFY_BY_ID']) && $arFields['MODIFY_BY_ID'] != '') {
						$arEvent['USER_ID'] = $arFields['MODIFY_BY_ID'];
					}

					$COrderEvent = new COrderEvent();
					$COrderEvent->Add($arEvent, $this->bCheckPermission);

				}
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
				$afterEvents = GetModuleEvents('order', 'OnAfterOrderRegUpdate');
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


		$arFields['APP_ID']=isset($arFields['APP_ID'])?$arFields['APP_ID']:'0';
		$app=COrderApp::GetByID($arFields['APP_ID']);
		if (!$this->CheckFields($arFields, $app))
		{
			$result = false;
			$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
		}
		else
		{
			if (!isset($arFields['STATUS']))
				$arFields['STATUS'] = 'NEW';


			if($this->bCheckPermission)
			{
				$userPerms =  $iUserId == COrderPerms::GetCurrentUserID() ? $this->cPerms : COrderPerms::GetUserPermissions($iUserId);
				$sEntityPerm = $userPerms->GetPermType('REG', 'ADD');
				if ($sEntityPerm == BX_ORDER_PERM_NONE)
				{
					$this->LAST_ERROR = GetMessage('ORDER_PERMISSION_DENIED');
					$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					return false;
				}
			}





			$now = ConvertTimeStamp(AddToTimeStamp(array("DD" => 1),MakeTimeStamp(time())), 'SHORT', SITE_ID);
			$arFields['PERIOD'] = isset($arFields['PERIOD']) ? $arFields['PERIOD'] : $now;


			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderRegAdd');
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
						$this->LAST_ERROR = GetMessage('ORDER_REG_CREATION_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
						$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					}
					return false;
				}
			}

			$DB->Add('b_order_reg', $arFields, array(), 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);



			/*if($bUpdateSearch)
			{
				$arFilterTmp = Array('ID' => $ID);
				if (!$this->bCheckPermission)
					$arFilterTmp["CHECK_PERMISSIONS"] = "N";
				CCrmSearch::UpdateSearch($arFilterTmp, 'DEAL', true);
			}*/

			$result = $arFields['ID'];

			COrderEvent::RegisterAddEvent(self::$TYPE_NAME, $result, $iUserId);

			if($app) {
				$arEvent = Array(
					'ENTITY_FIELD' => 'REG',
					'EVENT_NAME' => GetMessage('ORDER_FIELD_APP_EVENT_ADD'),
					'VALUE_OLD' => '',
					'VALUE_NEW' => $result,
				);
				$arEvent['ENTITY_TYPE'] = 'APP';
				$arEvent['ENTITY_ID'] = $arFields['APP_ID'];
				//$arEvent['EVENT_TYPE'] = 1;

				if ($iUserId != '') {
					$arEvent['USER_ID'] = $iUserId;
				} else if (isset($arFields['MODIFY_BY_ID']) && $arFields['MODIFY_BY_ID'] != '') {
					$arEvent['USER_ID'] = $arFields['MODIFY_BY_ID'];
				}

				$COrderEvent = new COrderEvent();
				$COrderEvent->Add($arEvent, $this->bCheckPermission);

			}

			$afterEvents = GetModuleEvents('order', 'OnAfterOrderRegAdd');
			while ($arEvent = $afterEvents->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array(&$arFields));
			}

			if(isset($arFields['ORIGIN_ID']) && $arFields['ORIGIN_ID'] !== '')
			{
				$afterEvents = GetModuleEvents('order', 'OnAfterExternalOrderRegAdd');
				while ($arEvent = $afterEvents->Fetch())
				{
					ExecuteModuleEventEx($arEvent, array(&$arFields));
				}
			}
		}

		return $result;
	}

	public function CheckFields(&$arFields,$app)
	{
		$this->LAST_ERROR = '';

		if (!empty($arFields['SHARED']) && ($arFields['SHARED']!='Y' && $arFields['SHARED']!='N'))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_SHARED')))."<br />\n";

		if (!empty($arFields['PAST']) && ($arFields['PAST']!='Y' && $arFields['PAST']!='N'))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_PAST')))."<br />\n";


        $arEnum=COrderHelper::GetEnumList('REG','STATUS');
		if (!empty($arFields['STATUS']) && !array_key_exists($arFields['STATUS'],$arEnum))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_STATUS')))."<br />\n";

		if (!empty($arFields['PERIOD']) && !CheckDateTime($arFields['PERIOD']))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_PERIOD')))."<br />\n";

		if (!empty($app) && $app['STATUS']=='READY' && $app['AGENT_LEGAL']!='Y' &&
			(strtolower($arFields['ENTITY_TYPE'])!='formed_group'
				|| $arFields['STATUS']=='INTERRUPTED' || $arFields['STATUS']=='NEW'
				|| $arFields['STATUS']=='EXPELLED' || $arFields['STATUS']=='EXPIRED'
				|| $arFields['PHYSICAL_ID']=='')
		) {

			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_APP_STATUS_READY') . "<br />\n";
		}

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
			$sEntityPerm = $this->cPerms->GetPermType('REG', 'DELETE');
			if ($sEntityPerm == BX_ORDER_PERM_NONE)
				return false;
			/*else if ($sEntityPerm == BX_CRM_PERM_SELF)
				$sWherePerm = " AND ASSIGNED_BY_ID = {$iUserId}";
			else if ($sEntityPerm == BX_CRM_PERM_OPEN)
				$sWherePerm = " AND (OPENED = 'Y' OR ASSIGNED_BY_ID = {$iUserId})";*/
		}

		$APPLICATION->ResetException();
		$events = GetModuleEvents('order', 'OnBeforeOrderRegDelete');
		while ($arEvent = $events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if ($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}



		$dbRes = $DB->Query("DELETE FROM b_order_reg WHERE ID = '{$ID}'{$sWherePerm}", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
		if (is_object($dbRes) && $dbRes->AffectedRowsCount() > 0)
		{
			/*CCrmSearch::DeleteSearch('DEAL', $ID);

			$DB->Query("DELETE FROM b_crm_entity_perms WHERE ENTITY='DEAL' AND ENTITY_ID = $ID", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
			$GLOBALS['USER_FIELD_MANAGER']->Delete(self::$sUFEntityID, $ID);
			$CCrmFieldMulti = new CCrmFieldMulti();
			$CCrmFieldMulti->DeleteByElement('DEAL', $ID);*/
			$COrderEvent = new COrderEvent();
			$COrderEvent->DeleteByElement('REG', $ID);

			if(COrderApp::GetByID($arFields['APP_ID'])) {
				$arEvent = Array(
					'ENTITY_FIELD' => 'REG',
					'EVENT_NAME' => GetMessage('ORDER_FIELD_APP_EVENT_DELETE'),
					'VALUE_OLD' => '',
					'VALUE_NEW' => $ID,
				);
				$arEvent['ENTITY_TYPE'] = 'APP';
				$arEvent['ENTITY_ID'] = $arFields['APP_ID'];
				//$arEvent['EVENT_TYPE'] = 1;

				if ($iUserId != '') {
					$arEvent['USER_ID'] = $iUserId;
				} else if (isset($arFields['MODIFY_BY_ID']) && $arFields['MODIFY_BY_ID'] != '') {
					$arEvent['USER_ID'] = $arFields['MODIFY_BY_ID'];
				}

				$COrderEvent = new COrderEvent();
				$COrderEvent->Add($arEvent, $this->bCheckPermission);

			}

			//if(Bitrix\Crm\Settings\HistorySettings::getCurrent()->isDealDeletionEventEnabled())
			COrderEvent::RegisterDeleteEvent(BX_ORDER_REG, $ID, $iUserId, array('FIELDS' => $arFields));

			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				$GLOBALS["CACHE_MANAGER"]->ClearByTag("order_entity_name_".BX_ORDER_REG_ID."_".$ID);
			}

			$afterEvents = GetModuleEvents('order', 'OnAfterOrderRegDelete');
			while ($arEvent = $afterEvents->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array($ID));
			}
		}
		return true;
	}

    static public function BuildEntityAttr($userID, $arAttr = array())
    {
        $userID = (int)$userID;
        $arResult = array("U{$userID}");
        /*if(isset($arAttr['OPENED']) && $arAttr['OPENED'] == 'Y')
        {
            $arResult[] = 'O';
        }

        $stageID = isset($arAttr['STAGE_ID']) ? $arAttr['STAGE_ID'] : '';
        if($stageID !== '')
        {
            $arResult[] = "STAGE_ID{$stageID}";
        }*/

        $arStaffAttr = COrderPerms::BuildUserEntityAttr($userID);
        return array_merge($arResult, $arStaffAttr['INTRANET']);
    }

    static public function RebuildEntityAccessAttrs($IDs)
    {
        if(!is_array($IDs))
        {
            $IDs = array($IDs);
        }

        $arRes = self::GetList(
            array(),
            array('ID' => $IDs),
            array('ID', 'ASSIGNED_ID'/*,'OPENED','STAGE_ID'*/)
        );

        if(!is_array($arRes))
        {
            return false;
        }

        foreach($arRes as $fields)
        {
            $ID = intval($fields['ID']);
            $assignedByID = isset($fields['ASSIGNED_ID']) ? intval($fields['ASSIGNED_ID']) : 0;
            if($assignedByID <= 0)
            {
                continue;
            }

            $attrs = array();
            /*if(isset($fields['OPENED']))
            {
                $attrs['OPENED'] = $fields['OPENED'];
            }

            if(isset($fields['STAGE_ID']))
            {
                $attrs['STAGE_ID'] = $fields['STAGE_ID'];
            }*/

            $entityAttrs = self::BuildEntityAttr($assignedByID, $attrs);
            COrderPerms::UpdateEntityAttr('REG', $ID, $entityAttrs);
        }
    }

    public static function CheckCreatePermission($userPermissions = null)
    {
        return COrderAuthorizationHelper::CheckCreatePermission(self::$TYPE_NAME, $userPermissions);
    }

    public static function CheckUpdatePermission($ID, $userPermissions = null)
    {
		$reg=self::GetByID($ID);
		$pDir=self::GetDirection($reg);
        return COrderAuthorizationHelper::CheckUpdatePermission(self::$TYPE_NAME, $ID, $userPermissions,array($ID=>array('STATUS'.$reg['STATUS'],$pDir)));
    }

    public static function CheckDeletePermission($ID, $userPermissions = null)
    {
		$reg=self::GetByID($ID);
		$pDir=self::GetDirection($reg);
        return COrderAuthorizationHelper::CheckDeletePermission(self::$TYPE_NAME, $ID, $userPermissions,array($ID=>array('STATUS'.$reg['STATUS'],$pDir)));
    }

    public static function CheckReadPermission($ID = '', $userPermissions = null)
    {
		if($ID!='') {
			$reg=self::GetByID($ID);
			$pDir=self::GetDirection($reg);
			return COrderAuthorizationHelper::CheckReadPermission(self::$TYPE_NAME, $ID, $userPermissions, array($ID=>array('STATUS' . $reg['STATUS'], $pDir)));
		}
        return COrderAuthorizationHelper::CheckReadPermission(self::$TYPE_NAME, $ID, $userPermissions);
    }

	public static function GetDirection($reg,$tree=false) {
		if(!isset($reg['DIRECTION']))
			$reg=self::GetByID($reg['ID']);
		if($tree==false)
			$tree=COrderDirection::GetTree();
		foreach($tree as $branch) {
			if($branch['DEPTH_LEVEL']==1)
				$top=$branch['ID'];
			if($branch['ID']==$reg['DIRECTION']) {
				$dir='DIRECTION'.$top;
				break;
			}
		}
		return $dir;
	}
}