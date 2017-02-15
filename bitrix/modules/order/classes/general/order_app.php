<?php

IncludeModuleLangFile(__FILE__);

class COrderApp
{
	static public $sUFEntityID = 'ORDER_APP';
	public $LAST_ERROR = '';
	public $cPerms = null;
	protected $bCheckPermission = true;
	const TABLE_ALIAS = 'L';
	protected static $TYPE_NAME = 'APP';

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
        $assignedJoin = ' LEFT JOIN (b_user U4 JOIN b_uts_user UF4 ON U4.ID=UF4.VALUE_ID) ON L.ASSIGNED_ID = UF4.UF_GUID';
        $assignedJoin .= ' LEFT JOIN b_order_physical UP4 ON UF4.UF_GUID=UP4.ID';
        $agentJoin = ' LEFT JOIN b_order_agent A ON L.AGENT_ID=A.ID';
		$agentJoin .= ' LEFT JOIN b_order_contact C ON (A.ID = C.AGENT_ID AND A.LEGAL="Y" AND (C.END_DATE IS NULL OR C.END_DATE>now()))';
		$agentJoin .= ' LEFT JOIN b_order_physical CP ON CP.ID=C.GUID';
		$agentJoin .= ' LEFT JOIN b_order_physical AP ON (AP.ID=A.ID AND A.LEGAL="N")';
		$rootDirectionJoin = ' LEFT JOIN (
			SELECT GROUP_CONCAT(CONCAT("#",T.ROOT_ID,"#") ORDER BY ROOT_ID SEPARATOR ";") ROOT_ID,APP_ID
			FROM (
				SELECT DISTINCT(CASE UPPER(ENTITY_TYPE)
					WHEN "DIRECTION" THEN (SELECT MAX(D.ROOT_ID) FROM b_order_direction D
						WHERE D.ID=R1.ENTITY_ID)
					WHEN "NOMEN" THEN (SELECT MAX(D.ROOT_ID) FROM b_order_nomen E
						LEFT JOIN b_order_direction D ON E.DIRECTION_ID=D.ID
						WHERE E.ID=R1.ENTITY_ID)
					WHEN "GROUP" THEN (SELECT MAX(D.ROOT_ID) FROM b_order_group E
						LEFT JOIN b_order_nomen N ON E.NOMEN_ID=N.ID
						LEFT JOIN b_order_direction D ON N.DIRECTION_ID=D.ID
						WHERE E.ID=R1.ENTITY_ID)
					WHEN "FORMED_GROUP" THEN (SELECT MAX(D.ROOT_ID) FROM b_order_formed_group FG
						LEFT JOIN b_order_group G ON FG.GROUP_ID=G.ID
						LEFT JOIN b_order_nomen N ON G.NOMEN_ID=N.ID
						LEFT JOIN b_order_direction D ON N.DIRECTION_ID=D.ID
						WHERE FG.ID=R1.ENTITY_ID)
					END) ROOT_ID,
					APP_ID
				FROM b_order_reg R1
				WHERE APP_ID<>0
			) T GROUP BY T.APP_ID
		) RD ON RD.APP_ID=L.ID';
		$readyJoin = ' LEFT JOIN (
			SELECT MIN(CASE WHEN (LOWER(ENTITY_TYPE)="formed_group" AND PHYSICAL_ID<>"") THEN 1 ELSE 0 END) MAY_TO_READY_REG,
				APP_ID, MIN(PERIOD) MIN_PERIOD
			FROM b_order_reg
			GROUP BY APP_ID
		) R2 ON R2.APP_ID=L.ID';

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

			'AGENT_ID' => array('FIELD' => 'L.AGENT_ID', 'TYPE' => 'string'),
			'AGENT_LEGAL' => array('FIELD' => 'CASE L.AGENT_ID WHEN "" THEN L.AGENT_LEGAL '.
                'ELSE A.LEGAL END', 'TYPE' => 'string', 'FROM' => $agentJoin),
			'AGENT_TITLE' => array('FIELD' => 'CASE L.AGENT_ID WHEN "" THEN L.AGENT_TITLE '.
                'ELSE (CASE A.LEGAL WHEN "Y" THEN A.TITLE ELSE CONCAT(AP.LAST_NAME, " ", AP.NAME, " ", AP.SECOND_NAME) END) END', 'TYPE' => 'string', 'FROM' => $agentJoin),
			'AGENT_PHONE' => array('FIELD' => 'CASE L.AGENT_ID WHEN "" THEN L.AGENT_PHONE '.
                'ELSE (CASE A.LEGAL WHEN "Y" THEN A.LEGAL_PHONE ELSE AP.PHONE END) END', 'TYPE' => 'string', 'FROM' => $agentJoin),
			'AGENT_EMAIL' => array('FIELD' => 'CASE L.AGENT_ID WHEN "" THEN L.AGENT_EMAIL '.
                'ELSE (CASE A.LEGAL WHEN "Y" THEN A.LEGAL_EMAIL ELSE AP.EMAIL END) END', 'TYPE' => 'string', 'FROM' => $agentJoin),
			'CONTACT_ID' => array('FIELD' => 'CASE L.AGENT_ID WHEN "" THEN "" ELSE C.ID END', 'TYPE' => 'string', 'FROM' => $agentJoin),
			'CONTACT_GUID' => array('FIELD' => 'CASE L.AGENT_ID WHEN "" THEN "" ELSE C.GUID END', 'TYPE' => 'string', 'FROM' => $agentJoin),
			'CONTACT_FULL_NAME' => array('FIELD' => 'CASE L.AGENT_ID WHEN "" THEN L.CONTACT_FULL_NAME '.
                'ELSE CONCAT(CP.LAST_NAME, " ", CP.NAME, " ", CP.SECOND_NAME) END', 'TYPE' => 'string', 'FROM' => $agentJoin),
			'CONTACT_PHONE' => array('FIELD' => 'CASE L.AGENT_ID WHEN "" THEN L.CONTACT_PHONE '.
                'ELSE CP.PHONE END', 'TYPE' => 'string', 'FROM' => $agentJoin),
			'CONTACT_EMAIL' => array('FIELD' => 'CASE L.AGENT_ID WHEN "" THEN L.CONTACT_EMAIL '.
                'ELSE CP.EMAIL END', 'TYPE' => 'string', 'FROM' => $agentJoin),
			'STATUS' => array('FIELD' => 'CASE WHEN LEFT(L.STATUS, 9)="CONVERTED" THEN "CONVERTED" ELSE L.STATUS END', 'TYPE' => 'string'),
			'STATUS_TEXT' => array('FIELD' => 'CASE WHEN LEFT(L.STATUS, 9)="CONVERTED" THEN SUBSTRING(L.STATUS,10) ELSE "" END', 'TYPE' => 'string'),
			'ASSIGNED_ID' => array('FIELD' => 'L.ASSIGNED_ID', 'TYPE' => 'string'),
			'ASSIGNED_LOGIN' => array('FIELD' => 'U4.LOGIN', 'TYPE' => 'string', 'FROM' => $assignedJoin),
			'ASSIGNED_FULL_NAME' => array('FIELD' => 'CONCAT (UP4.LAST_NAME, " ", UP4.NAME, " ", UP4.SECOND_NAME)', 'TYPE' => 'string', 'FROM' => $assignedJoin),
			'ASSIGNED_EMAIL' => array('FIELD' => 'UP4.EMAIL', 'TYPE' => 'string', 'FROM' => $assignedJoin),
			'SOURCE' => array('FIELD' => 'CASE WHEN LEFT(L.SOURCE, 5)="OTHER" THEN "OTHER" ELSE L.SOURCE END', 'TYPE' => 'string'),
			'SOURCE_TEXT' => array('FIELD' => 'CASE WHEN LEFT(L.SOURCE, 5)="OTHER" THEN SUBSTRING(L.SOURCE,6) ELSE "" END', 'TYPE' => 'string'),
			'PAST' => array('FIELD' => 'L.PAST', 'TYPE' => 'string'),
			'PERIOD' => array('FIELD' => 'R2.MIN_PERIOD', 'TYPE' => 'date', 'FROM' => $readyJoin),
			'EXPIRED' => array('FIELD' => 'CASE WHEN R2.MIN_PERIOD<NOW() THEN "Y" ELSE "N" END', 'TYPE' => 'string', 'FROM' => $readyJoin),
			'ATTENTION' => array('FIELD' => 'CASE WHEN R2.MIN_PERIOD<DATE_ADD(NOW(), INTERVAL 1 DAY) THEN "Y" ELSE "N" END', 'TYPE' => 'string', 'FROM' => $readyJoin),
			'DESCRIPTION' => array('FIELD' => 'L.DESCRIPTION', 'TYPE' => 'string'),
			'HAND_MADE' => array('FIELD' => 'L.HAND_MADE', 'TYPE' => 'string'),
			'ROOT_ID' => array('FIELD' => 'RD.ROOT_ID', 'TYPE' => 'string', 'FROM' => $rootDirectionJoin),
			'MAY_TO_READY' => array('FIELD' => 'CASE WHEN (R2.MAY_TO_READY_REG="1" AND L.AGENT_ID<>"") THEN 1 ELSE 0 END', 'TYPE' => 'string', 'FROM' => $readyJoin),

		);
		return $result;
	}

    public static function GetListEx($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $arOptions = array())
	{
		global $DBType;
		$lb = new COrderEntityListBuilder(
			$DBType,
			'b_order_app',
			'L',
			self::GetFields()
		);
		return $lb->Prepare($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields, $arOptions);
	}

    public static function CompareFields($arFieldsOrig, $arFieldsModif, $bCheckPerms = true) {
		$arMsg = Array();

		$aID=isset($arFieldsOrig['AGENT_ID'])?$arFieldsOrig['AGENT_ID']:false;
		if (isset($arFieldsOrig['AGENT_ID']) && isset($arFieldsModif['AGENT_ID'])
			&& $arFieldsOrig['AGENT_ID'] != $arFieldsModif['AGENT_ID']) {
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'AGENT_ID',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_AGENT_ID'),
				'VALUE_OLD' => $arFieldsOrig['AGENT_ID'],
				'VALUE_NEW' => $arFieldsModif['AGENT_ID'],
			);
			$aID=$arFieldsModif['AGENT_ID'];
		}

        if($aID==='') {
            if(isset($arFieldsOrig['AGENT_LEGAL']) && isset($arFieldsModif['AGENT_LEGAL'])
			&& $arFieldsOrig['AGENT_LEGAL'] != $arFieldsModif['AGENT_LEGAL'])
				$arMsg[] = Array(
					'ENTITY_FIELD' => 'AGENT_LEGAL',
					'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_AGENT_LEGAL'),
					'VALUE_OLD' => $arFieldsOrig['AGENT_LEGAL'],
					'VALUE_NEW' => $arFieldsModif['AGENT_LEGAL'],
				);

            if(isset($arFieldsOrig['AGENT_TITLE']) && isset($arFieldsModif['AGENT_TITLE'])
			&& $arFieldsOrig['AGENT_TITLE'] != $arFieldsModif['AGENT_TITLE'])
				$arMsg[] = Array(
					'ENTITY_FIELD' => 'AGENT_TITLE',
					'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_AGENT_TITLE'),
					'VALUE_OLD' => $arFieldsOrig['AGENT_TITLE'],
					'VALUE_NEW' => $arFieldsModif['AGENT_TITLE'],
				);

            if(isset($arFieldsOrig['AGENT_PHONE']) && isset($arFieldsModif['AGENT_PHONE'])
			&& $arFieldsOrig['AGENT_PHONE'] != $arFieldsModif['AGENT_PHONE'])
				$arMsg[] = Array(
					'ENTITY_FIELD' => 'AGENT_PHONE',
					'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_AGENT_PHONE'),
					'VALUE_OLD' => $arFieldsOrig['AGENT_PHONE'],
					'VALUE_NEW' => $arFieldsModif['AGENT_PHONE'],
				);

            if(isset($arFieldsOrig['AGENT_EMAIL']) && isset($arFieldsModif['AGENT_EMAIL'])
			&& $arFieldsOrig['AGENT_EMAIL'] != $arFieldsModif['AGENT_EMAIL'])
				$arMsg[] = Array(
					'ENTITY_FIELD' => 'AGENT_EMAIL',
					'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_AGENT_EMAIL'),
					'VALUE_OLD' => $arFieldsOrig['AGENT_EMAIL'],
					'VALUE_NEW' => $arFieldsModif['AGENT_EMAIL'],
				);

            if(isset($arFieldsOrig['CONTACT_FULL_NAME']) && isset($arFieldsModif['CONTACT_FULL_NAME'])
			&& $arFieldsOrig['CONTACT_FULL_NAME'] != $arFieldsModif['CONTACT_FULL_NAME'])
				$arMsg[] = Array(
					'ENTITY_FIELD' => 'CONTACT_FULL_NAME',
					'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_CONTACT_FULL_NAME'),
					'VALUE_OLD' => $arFieldsOrig['CONTACT_FULL_NAME'],
					'VALUE_NEW' => $arFieldsModif['CONTACT_FULL_NAME'],
				);

            if(isset($arFieldsOrig['CONTACT_PHONE']) && isset($arFieldsModif['CONTACT_PHONE'])
			&& $arFieldsOrig['CONTACT_PHONE'] != $arFieldsModif['CONTACT_PHONE'])
				$arMsg[] = Array(
					'ENTITY_FIELD' => 'CONTACT_PHONE',
					'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_CONTACT_PHONE'),
					'VALUE_OLD' => $arFieldsOrig['CONTACT_PHONE'],
					'VALUE_NEW' => $arFieldsModif['CONTACT_PHONE'],
				);

            if(isset($arFieldsOrig['CONTACT_EMAIL']) && isset($arFieldsModif['CONTACT_EMAIL'])
			&& $arFieldsOrig['CONTACT_EMAIL'] != $arFieldsModif['CONTACT_EMAIL'])
				$arMsg[] = Array(
					'ENTITY_FIELD' => 'CONTACT_EMAIL',
					'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_CONTACT_EMAIL'),
					'VALUE_OLD' => $arFieldsOrig['CONTACT_EMAIL'],
					'VALUE_NEW' => $arFieldsModif['CONTACT_EMAIL'],
				);
        }

		if (isset($arFieldsOrig['STATUS']) && isset($arFieldsModif['STATUS'])
			&& $arFieldsOrig['STATUS'] != $arFieldsModif['STATUS']) {

			if ($arFieldsOrig['STATUS']=='CONVERTED')
				$arFieldsOrig['STATUS'].=$arFieldsOrig['STATUS_TEXT'];

			$arMsg[] = Array(
				'ENTITY_FIELD' => 'STATUS',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_STATUS'),
				'VALUE_OLD' => $arFieldsOrig['STATUS'],
				'VALUE_NEW' => $arFieldsModif['STATUS'],
			);
		}

		if (isset($arFieldsOrig['ASSIGNED_ID']) && isset($arFieldsModif['ASSIGNED_ID'])
			&& $arFieldsOrig['ASSIGNED_ID'] != $arFieldsModif['ASSIGNED_ID'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'ASSIGNED_ID',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_ASSIGNED_ID'),
				'VALUE_OLD' => $arFieldsOrig['ASSIGNED_ID'],
				'VALUE_NEW' => $arFieldsModif['ASSIGNED_ID'],
			);

		if (isset($arFieldsOrig['SOURCE']) && isset($arFieldsModif['SOURCE'])) {
			if ($arFieldsOrig['SOURCE']=='OTHER')
				$arFieldsOrig['SOURCE'].=$arFieldsOrig['SOURCE_TEXT'];

			if($arFieldsOrig['SOURCE'] != $arFieldsModif['SOURCE'])
				$arMsg[] = Array(
					'ENTITY_FIELD' => 'SOURCE',
					'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_SOURCE'),
					'VALUE_OLD' => $arFieldsOrig['SOURCE'],
					'VALUE_NEW' => $arFieldsModif['SOURCE'],
				);
		}

		if (isset($arFieldsOrig['PAST']) && isset($arFieldsModif['PAST'])
			&& $arFieldsOrig['PAST'] != $arFieldsModif['PAST'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'PAST',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_PAST'),
				'VALUE_OLD' => $arFieldsOrig['PAST'],
				'VALUE_NEW' => $arFieldsModif['PAST'],
			);

		if (isset($arFieldsOrig['DESCRIPTION']) && isset($arFieldsModif['DESCRIPTION'])
			&& $arFieldsOrig['DESCRIPTION'] != $arFieldsModif['DESCRIPTION'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'DESCRIPTION',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_DESCRIPTION'),
				'VALUE_OLD' => $arFieldsOrig['DESCRIPTION'],
				'VALUE_NEW' => $arFieldsModif['DESCRIPTION'],
			);


		if (isset($arFieldsOrig['HAND_MADE']) && isset($arFieldsModif['HAND_MADE'])
			&& $arFieldsOrig['HAND_MADE'] != $arFieldsModif['HAND_MADE'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'HAND_MADE',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_HAND_MADE'),
				'VALUE_OLD' => $arFieldsOrig['HAND_MADE'],
				'VALUE_NEW' => $arFieldsModif['HAND_MADE'],
			);


		return $arMsg;
	}

	public function Update($ID, array &$arFields, $bCompare = true, $options = array()) {
        global $DB,$USER;
		$this->LAST_ERROR = '';

		if(!is_array($options))
		{
			$options = array();
		}

		$arFilterTmp = array('ID' => $ID);
		if (!$this->bCheckPermission)
			$arFilterTmp['CHECK_PERMISSIONS'] = 'N';

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

		if (!isset($arFields['MODIFY_BY_ID']) || $arFields['MODIFY_BY_ID'] <= 0)
			$arFields['MODIFY_BY_ID'] = $iUserId;
		else
			$iUserId = $arFields['MODIFY_BY_ID'];



		$assignedID = isset($arFields['ASSIGNED_ID']) ? $arFields['ASSIGNED_ID'] : $arRow['ASSIGNED_ID'];

		$bResult = false;
		if (!$this->CheckFields($arFields, $ID, $options))
		{
			$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
		}
		else
		{
			if($this->bCheckPermission && (!COrderAuthorizationHelper::CheckUpdatePermission(self::$TYPE_NAME, $ID, $this->cPerms,null,array('STATUS',$arFields['STATUS']))
					&& !in_array($assignedID,CAccess::GetUserCodesArray($USER->GetID()))))
			{
				$this->LAST_ERROR = GetMessage('ORDER_PERMISSION_DENIED');
				$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
				return false;
			}

			if(!isset($arFields['ID']))
			{
				$arFields['ID'] = $ID;
			}

			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderAppUpdate');
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
						$this->LAST_ERROR = GetMessage('ORDER_APP_UPDATE_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
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
					$arEvent['ENTITY_TYPE'] = 'APP';
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
			$sUpdate = $DB->PrepareUpdate('b_order_app', $arFields);
			if (strlen($sUpdate) > 0)
			{
				$DB->Query("UPDATE b_order_app SET {$sUpdate} WHERE ID = '{$ID}'", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
				$bResult = true;

				if($bResult && (isset($arFields['AGENT_LEGAL']) && $arFields['AGENT_LEGAL'] != 'Y' || !isset($arFields['AGENT_LEGAL']) && $arRow['AGENT_LEGAL']!='Y') && $arFields['AGENT_ID']!='' && $arRow['AGENT_ID']=='') {
					$COrderPhysical=new COrderPhysical();
					$phys=COrderPhysical::GetByID($arFields['AGENT_ID']);
					$arPhysical=array(
					);
					if($arFields['AGENT_PHONE']!='') {
						$arPhysical['PHONE'] = $arFields['AGENT_PHONE'];
					} else {
						$arPhysical['PHONE'] = $phys['PHONE'];
					}
					if($arFields['AGENT_EMAIL']!='') {
						$arPhysical['EMAIL'] = $arFields['AGENT_EMAIL'];
					} else {
						$arPhysical['EMAIL'] = $phys['EMAIL'];
					}
					if (!$COrderPhysical->Update($arFields['AGENT_ID'],$arPhysical)) {
						$this->LAST_ERROR=$COrderPhysical->LAST_ERROR;
						$bResult = false;
					} else {
						COrderHelper::ChangeAgentInfo($ID, $arFields['AGENT_PHONE'], $arFields['AGENT_EMAIL']);
					}
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
				$afterEvents = GetModuleEvents('order', 'OnAfterOrderAppUpdate');
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
			if (!isset($arFields['STATUS']))
				$arFields['STATUS'] = 'NEW';


			if($this->bCheckPermission)
			{
				$userPerms =  $iUserId == COrderPerms::GetCurrentUserID() ? $this->cPerms : COrderPerms::GetUserPermissions($iUserId);
				$sEntityPerm = $userPerms->GetPermType('APP', 'ADD');
				if ($sEntityPerm == BX_ORDER_PERM_NONE)
				{
					$this->LAST_ERROR = GetMessage('ORDER_PERMISSION_DENIED');
					$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					return false;
				}
			}










			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderAppAdd');
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
						$this->LAST_ERROR = GetMessage('ORDER_APP_CREATION_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
						$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					}
					return false;
				}
			}

			$DB->Add('b_order_app', $arFields, array(), 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);



			/*if($bUpdateSearch)
			{
				$arFilterTmp = Array('ID' => $ID);
				if (!$this->bCheckPermission)
					$arFilterTmp["CHECK_PERMISSIONS"] = "N";
				CCrmSearch::UpdateSearch($arFilterTmp, 'DEAL', true);
			}*/

			$result = $arFields['ID'];

			COrderEvent::RegisterAddEvent(self::$TYPE_NAME, $result, $iUserId);


			$afterEvents = GetModuleEvents('order', 'OnAfterOrderAppAdd');
			while ($arEvent = $afterEvents->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array(&$arFields));
			}

			if(isset($arFields['ORIGIN_ID']) && $arFields['ORIGIN_ID'] !== '')
			{
				$afterEvents = GetModuleEvents('order', 'OnAfterExternalOrderAppAdd');
				while ($arEvent = $afterEvents->Fetch())
				{
					ExecuteModuleEventEx($arEvent, array(&$arFields));
				}
			}
		}

		return $result;
	}

	public function CheckFields(&$arFields,$ID='')
	{
		$this->LAST_ERROR = '';

		if (isset($arFields['ID']) && $arFields['ID']=='')
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_IS_MISSING', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_ID')))."<br />\n";

		if (!empty($arFields['AGENT_EMAIL']) && !filter_var($arFields['AGENT_EMAIL'],FILTER_VALIDATE_EMAIL))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_AGENT_EMAIL')))."<br />\n";

		if (!empty($arFields['CONTACT_EMAIL']) && !filter_var($arFields['CONTACT_EMAIL'],FILTER_VALIDATE_EMAIL))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_CONTACT_EMAIL')))."<br />\n";

		if (!empty($arFields['PAST']) && ($arFields['PAST']!='Y' && $arFields['PAST']!='N'))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_PAST')))."<br />\n";

		if (!empty($arFields['HAND_MADE']) && ($arFields['HAND_MADE']!='Y' && $arFields['HAND_MADE']!='N'))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_HAND_MADE')))."<br />\n";

        $arEnum=COrderHelper::GetEnumList('APP','STATUS');
		if (!empty($arFields['STATUS']) && !array_key_exists($arFields['STATUS'],$arEnum))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_STATUS')))."<br />\n";


		if (!empty($arFields['STATUS']) && $arFields['STATUS']=='READY') {
			$res=COrderReg::GetListEx(array(),array('APP_ID'=>$ID));
			$bReg=true;
			$cReg=0;
			while($el=$res->Fetch()) {
				$cReg++;
				if(strtolower($el['ENTITY_TYPE'])!='formed_group' || $el['PHYSICAL_ID']=='' || $el['STATUS']=='NEW'
					|| $el['STATUS']=='EXPELLED' || $el['STATUS']=='EXPIRED' || $el['STATUS']=='INTERRUPTED'
				) {
					$bReg=false;
					break;
				}
			}
			if ($arFields['AGENT_ID'] != '') {
				$arAgent = COrderAgent::GetByID($arFields['AGENT_ID']);
				if ($cReg == 0 || ($arAgent['LEGAL'] != 'Y' && $bReg==false))
					$this->LAST_ERROR .= GetMessage('ORDER_ERROR_STATUS_NOT_PERMITTED',
							array('%REASON%' => GetMessage('ORDER_ERROR_REASON_REG') )) . "<br />\n";
			} else {
				$this->LAST_ERROR .= GetMessage('ORDER_ERROR_STATUS_NOT_PERMITTED',
						array('%REASON%' => GetMessage('ORDER_ERROR_REASON_APP') )) . "<br />\n";
			}
		}

		if (strlen($this->LAST_ERROR) > 0)
			return false;

		return true;
	}

    public function Delete($ID)
	{
		global $DB, $APPLICATION,$USER;

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
			$sEntityPerm = $this->cPerms->GetPermType('APP', 'DELETE');
			if ($sEntityPerm == BX_ORDER_PERM_NONE && !in_array($arFields['ASSIGNED_ID'],CAccess::GetUserCodesArray($USER->GetID())))
				return false;
			/*else if ($sEntityPerm == BX_CRM_PERM_SELF)
				$sWherePerm = " AND ASSIGNED_BY_ID = {$iUserId}";
			else if ($sEntityPerm == BX_CRM_PERM_OPEN)
				$sWherePerm = " AND (OPENED = 'Y' OR ASSIGNED_BY_ID = {$iUserId})";*/
		}

		$APPLICATION->ResetException();
		$events = GetModuleEvents('order', 'OnBeforeOrderAppDelete');
		while ($arEvent = $events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if ($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}



		$dbRes = $DB->Query("DELETE FROM b_order_app WHERE ID = '{$ID}'{$sWherePerm}", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
		if (is_object($dbRes) && $dbRes->AffectedRowsCount() > 0)
		{
			/*CCrmSearch::DeleteSearch('DEAL', $ID);

			$DB->Query("DELETE FROM b_crm_entity_perms WHERE ENTITY='DEAL' AND ENTITY_ID = $ID", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
			$GLOBALS['USER_FIELD_MANAGER']->Delete(self::$sUFEntityID, $ID);
			$CCrmFieldMulti = new CCrmFieldMulti();
			$CCrmFieldMulti->DeleteByElement('DEAL', $ID);*/

			$COrderEvent = new COrderEvent();
			$COrderEvent->DeleteByElement('APP', $ID);

			$COrderReg=new COrderReg($this->bCheckPermission);
			$res=COrderReg::GetListEx(array(),array('APP_ID'=>$ID));
			while($el=$res->Fetch()) {
				$COrderReg->Delete($el['ID']);
			}



			//if(Bitrix\Crm\Settings\HistorySettings::getCurrent()->isDealDeletionEventEnabled())
				COrderEvent::RegisterDeleteEvent(BX_ORDER_APP, $ID, $iUserId, array('FIELDS' => $arFields));

			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				$GLOBALS["CACHE_MANAGER"]->ClearByTag("order_entity_name_".BX_ORDER_APP_ID."_".$ID);
			}

			$afterEvents = GetModuleEvents('order', 'OnAfterOrderAppDelete');
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

        $arUserAttr = COrderPerms::BuildUserEntityAttr($userID);
        return array_merge($arResult, $arUserAttr['INTRANET']);
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
            $assignedByID = isset($fields['ASSIGNED_ID']) ? $fields['ASSIGNED_ID'] : '';
            if($assignedByID =='')
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
            COrderPerms::UpdateEntityAttr('APP', $ID, $entityAttrs);
        }
    }

    public static function CheckCreatePermission($userPermissions = null)
    {
        return COrderAuthorizationHelper::CheckCreatePermission(self::$TYPE_NAME, $userPermissions);
    }

    public static function CheckUpdatePermission($ID, $userPermissions = null)
    {
		$app=self::GetByID($ID);
		$pDir=self::GetDirections($app);
        return COrderAuthorizationHelper::CheckUpdatePermission(self::$TYPE_NAME, $ID, $userPermissions,array($ID=>array_merge(array('STATUS'.$app['STATUS']),$pDir)));
    }

    public static function CheckDeletePermission($ID, $userPermissions = null)
    {
		$app=self::GetByID($ID);
		$pDir=self::GetDirections($app);
        return COrderAuthorizationHelper::CheckDeletePermission(self::$TYPE_NAME, $ID, $userPermissions,array($ID=>array_merge(array('STATUS'.$app['STATUS']),$pDir)));
    }

    public static function CheckReadPermission($ID = '', $userPermissions = null)
    {
		if($ID!='') {
			$app = self::GetByID($ID);
			$pDir = self::GetDirections($app);
			return COrderAuthorizationHelper::CheckReadPermission(self::$TYPE_NAME, $ID, $userPermissions,array($ID=>array_merge(array('STATUS'.$app['STATUS']),$pDir)));
		}
		return COrderAuthorizationHelper::CheckReadPermission(self::$TYPE_NAME, $ID, $userPermissions);
    }



	public static function GetDirections($app) {
		if(!isset($app['ROOT_ID']))
			$app=self::GetByID($app['ID']);
		$pDir=explode(';',$app['ROOT_ID']);
		foreach($pDir as &$d) {
			$d='DIRECTION'.str_replace("#","",$d);
		}
		unset($d);
		return $pDir;
	}

	public static function SetAssigned($appID) {
		$app=self::GetByID($appID);
		$dir=explode(';',$app['ROOT_ID']);
		$assignedID='D54';
		$departments=COrderHelper::GetRootDirectionList();
		foreach($dir as $directionID) {
			if ($assignedID == 'D54')
				$assignedID = $departments['#'.$directionID.'#'];
			else {
				$assignedID = 'D54';
				break;
			}
		}
		return $assignedID;
	}
}