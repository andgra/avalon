<?php

IncludeModuleLangFile(__FILE__);

class COrderPhysical
{
	static public $sUFEntityID = 'ORDER_PHYSICAL';
	public $LAST_ERROR = '';
	public $cPerms = null;
	protected $bCheckPermission = true;
	const TABLE_ALIAS = 'L';
	protected static $TYPE_NAME = 'PHYSICAL';

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
            'LAST_NAME' => array('FIELD' => 'L.LAST_NAME', 'TYPE' => 'string'),
            'NAME' => array('FIELD' => 'L.NAME', 'TYPE' => 'string'),
			'SECOND_NAME' => array('FIELD' => 'L.SECOND_NAME', 'TYPE' => 'string'),
            'FULL_NAME' => array('FIELD' => 'CONCAT(L.LAST_NAME," ",L.NAME," ",L.SECOND_NAME)', 'TYPE' => 'string'),
			'BDAY' => array('FIELD' => 'L.BDAY', 'TYPE' => 'date'),
			'GENDER' => array('FIELD' => 'L.GENDER', 'TYPE' => 'string'),
            'OUT_ADDRESS' => array('FIELD' => 'L.OUT_ADDRESS', 'TYPE' => 'string'),
			'REG_ADDRESS' => array('FIELD' => 'L.REG_ADDRESS', 'TYPE' => 'string'),
            'LIVE_ADDRESS' => array('FIELD' => 'L.LIVE_ADDRESS', 'TYPE' => 'string'),
			'EMAIL' => array('FIELD' => 'L.EMAIL', 'TYPE' => 'string'),
            'PHONE' => array('FIELD' => 'L.PHONE', 'TYPE' => 'string'),
			'OTHER' => array('FIELD' => 'L.OTHER', 'TYPE' => 'string'),
            'PROF_EDU' => array('FIELD' => 'L.PROF_EDU', 'TYPE' => 'string'),
			'LVL_EDU' => array('FIELD' => 'L.LVL_EDU', 'TYPE' => 'string'),
            'NATION' => array('FIELD' => 'L.NATION', 'TYPE' => 'string'),
			'ZIP_CODE' => array('FIELD' => 'L.ZIP_CODE', 'TYPE' => 'string'),
            'REGION' => array('FIELD' => 'L.REGION', 'TYPE' => 'int'),
			'BPLACE' => array('FIELD' => 'L.BPLACE', 'TYPE' => 'string'),
            'SECOND_EDU' => array('FIELD' => 'L.SECOND_EDU', 'TYPE' => 'string'),
			'CERT_MID_EDU' => array('FIELD' => 'L.CERT_MID_EDU', 'TYPE' => 'string'),
            'SERIAL_DIP' => array('FIELD' => 'L.SERIAL_DIP', 'TYPE' => 'string'),
			'NOM_DIP' => array('FIELD' => 'L.NOM_DIP', 'TYPE' => 'string'),
            'WHO_DIP' => array('FIELD' => 'L.WHO_DIP', 'TYPE' => 'string'),
			'WHEN_DIP' => array('FIELD' => 'L.WHEN_DIP', 'TYPE' => 'string'),
            'END_YEAR' => array('FIELD' => 'L.END_YEAR', 'TYPE' => 'string'),
			'HONORS_DIP' => array('FIELD' => 'L.HONORS_DIP', 'TYPE' => 'string'),
            'ORIGINAL_DIP' => array('FIELD' => 'L.ORIGINAL_DIP', 'TYPE' => 'string'),
			'DESCRIPTION' => array('FIELD' => 'L.DESCRIPTION', 'TYPE' => 'string'),

		);
		return $result;
	}

    public static function GetListEx($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $arOptions = array())
	{
		global $DBType;
		$lb = new COrderEntityListBuilder(
			$DBType,
			'b_order_physical',
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

		if (isset($arFieldsOrig['LAST_NAME']) && isset($arFieldsModif['LAST_NAME'])
			&& $arFieldsOrig['LAST_NAME'] != $arFieldsModif['LAST_NAME'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'LAST_NAME',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_LAST_NAME'),
				'VALUE_OLD' => $arFieldsOrig['LAST_NAME'],
				'VALUE_NEW' => $arFieldsModif['LAST_NAME'],
			);

		if (isset($arFieldsOrig['NAME']) && isset($arFieldsModif['NAME'])
			&& $arFieldsOrig['NAME'] != $arFieldsModif['NAME'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'NAME',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_NAME'),
				'VALUE_OLD' => $arFieldsOrig['NAME'],
				'VALUE_NEW' => $arFieldsModif['NAME'],
			);

		if (isset($arFieldsOrig['SECOND_NAME']) && isset($arFieldsModif['SECOND_NAME'])
			&& $arFieldsOrig['SECOND_NAME'] != $arFieldsModif['SECOND_NAME'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'SECOND_NAME',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_SECOND_NAME'),
				'VALUE_OLD' => $arFieldsOrig['SECOND_NAME'],
				'VALUE_NEW' => $arFieldsModif['SECOND_NAME'],
			);

		if (isset($arFieldsOrig['GENDER']) && isset($arFieldsModif['GENDER'])
			&& $arFieldsOrig['GENDER'] != $arFieldsModif['GENDER'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'GENDER',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_GENDER'),
				'VALUE_OLD' => $arFieldsOrig['GENDER'],
				'VALUE_NEW' => $arFieldsModif['GENDER'],
			);

		if (array_key_exists('BDAY', $arFieldsOrig) && array_key_exists('BDAY', $arFieldsModif) &&
			ConvertTimeStamp(strtotime($arFieldsOrig['BDAY'])) != $arFieldsModif['BDAY']
            && $arFieldsOrig['BDAY'] != $arFieldsModif['BDAY'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'BDAY',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_BDAY'),
				'VALUE_OLD' => $arFieldsOrig['BDAY'],
				'VALUE_NEW' => $arFieldsModif['BDAY'],
			);

		if (isset($arFieldsOrig['OUT_ADDRESS']) && isset($arFieldsModif['OUT_ADDRESS'])
			&& $arFieldsOrig['OUT_ADDRESS'] != $arFieldsModif['OUT_ADDRESS'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'OUT_ADDRESS',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_OUT_ADDRESS'),
				'VALUE_OLD' => $arFieldsOrig['OUT_ADDRESS'],
				'VALUE_NEW' => $arFieldsModif['OUT_ADDRESS'],
			);

		if (isset($arFieldsOrig['REG_ADDRESS']) && isset($arFieldsModif['REG_ADDRESS'])
			&& $arFieldsOrig['REG_ADDRESS'] != $arFieldsModif['REG_ADDRESS'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'REG_ADDRESS',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_REG_ADDRESS'),
				'VALUE_OLD' => $arFieldsOrig['REG_ADDRESS'],
				'VALUE_NEW' => $arFieldsModif['REG_ADDRESS'],
			);

		if (isset($arFieldsOrig['LIVE_ADDRESS']) && isset($arFieldsModif['LIVE_ADDRESS'])
			&& $arFieldsOrig['LIVE_ADDRESS'] != $arFieldsModif['LIVE_ADDRESS'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'LIVE_ADDRESS',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_LIVE_ADDRESS'),
				'VALUE_OLD' => $arFieldsOrig['LIVE_ADDRESS'],
				'VALUE_NEW' => $arFieldsModif['LIVE_ADDRESS'],
			);

		if (isset($arFieldsOrig['EMAIL']) && isset($arFieldsModif['EMAIL'])
			&& $arFieldsOrig['EMAIL'] != $arFieldsModif['EMAIL'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'EMAIL',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_EMAIL'),
				'VALUE_OLD' => $arFieldsOrig['EMAIL'],
				'VALUE_NEW' => $arFieldsModif['EMAIL'],
			);

		if (isset($arFieldsOrig['PHONE']) && isset($arFieldsModif['PHONE'])
			&& $arFieldsOrig['PHONE'] != $arFieldsModif['PHONE'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'PHONE',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_PHONE'),
				'VALUE_OLD' => $arFieldsOrig['PHONE'],
				'VALUE_NEW' => $arFieldsModif['PHONE'],
			);

		if (isset($arFieldsOrig['OTHER']) && isset($arFieldsModif['OTHER'])
			&& $arFieldsOrig['OTHER'] != $arFieldsModif['OTHER'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'OTHER',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_OTHER'),
				'VALUE_OLD' => $arFieldsOrig['OTHER'],
				'VALUE_NEW' => $arFieldsModif['OTHER'],
			);

		if (isset($arFieldsOrig['PROF_EDU']) && isset($arFieldsModif['PROF_EDU'])
			&& $arFieldsOrig['PROF_EDU'] != $arFieldsModif['PROF_EDU'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'PROF_EDU',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_PROF_EDU'),
				'VALUE_OLD' => $arFieldsOrig['PROF_EDU'],
				'VALUE_NEW' => $arFieldsModif['PROF_EDU'],
			);

		if (isset($arFieldsOrig['LVL_EDU']) && isset($arFieldsModif['LVL_EDU'])
			&& $arFieldsOrig['LVL_EDU'] != $arFieldsModif['LVL_EDU'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'LVL_EDU',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_LVL_EDU'),
				'VALUE_OLD' => $arFieldsOrig['LVL_EDU'],
				'VALUE_NEW' => $arFieldsModif['LVL_EDU'],
			);

		if (isset($arFieldsOrig['NATION']) && isset($arFieldsModif['NATION'])
			&& $arFieldsOrig['NATION'] != $arFieldsModif['NATION'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'NATION',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_NATION'),
				'VALUE_OLD' => $arFieldsOrig['NATION'],
				'VALUE_NEW' => $arFieldsModif['NATION'],
			);

		if (isset($arFieldsOrig['ZIP_CODE']) && isset($arFieldsModif['ZIP_CODE'])
			&& $arFieldsOrig['ZIP_CODE'] != $arFieldsModif['ZIP_CODE'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'ZIP_CODE',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_ZIP_CODE'),
				'VALUE_OLD' => $arFieldsOrig['ZIP_CODE'],
				'VALUE_NEW' => $arFieldsModif['ZIP_CODE'],
			);

		if (isset($arFieldsOrig['REGION']) && isset($arFieldsModif['REGION'])
			&& $arFieldsOrig['REGION'] != $arFieldsModif['REGION'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'REGION',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_REGION'),
				'VALUE_OLD' => $arFieldsOrig['REGION'],
				'VALUE_NEW' => $arFieldsModif['REGION'],
			);

		if (isset($arFieldsOrig['BPLACE']) && isset($arFieldsModif['BPLACE'])
			&& $arFieldsOrig['BPLACE'] != $arFieldsModif['BPLACE'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'BPLACE',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_BPLACE'),
				'VALUE_OLD' => $arFieldsOrig['BPLACE'],
				'VALUE_NEW' => $arFieldsModif['BPLACE'],
			);

		if (isset($arFieldsOrig['SECOND_EDU']) && isset($arFieldsModif['SECOND_EDU'])
			&& $arFieldsOrig['SECOND_EDU'] != $arFieldsModif['SECOND_EDU'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'SECOND_EDU',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_SECOND_EDU'),
				'VALUE_OLD' => $arFieldsOrig['SECOND_EDU'],
				'VALUE_NEW' => $arFieldsModif['SECOND_EDU'],
			);

		if (isset($arFieldsOrig['CERT_MID_EDU']) && isset($arFieldsModif['CERT_MID_EDU'])
			&& $arFieldsOrig['CERT_MID_EDU'] != $arFieldsModif['CERT_MID_EDU'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'CERT_MID_EDU',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_CERT_MID_EDU'),
				'VALUE_OLD' => $arFieldsOrig['CERT_MID_EDU'],
				'VALUE_NEW' => $arFieldsModif['CERT_MID_EDU'],
			);

		if (isset($arFieldsOrig['SERIAL_DIP']) && isset($arFieldsModif['SERIAL_DIP'])
			&& $arFieldsOrig['SERIAL_DIP'] != $arFieldsModif['SERIAL_DIP'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'SERIAL_DIP',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_SERIAL_DIP'),
				'VALUE_OLD' => $arFieldsOrig['SERIAL_DIP'],
				'VALUE_NEW' => $arFieldsModif['SERIAL_DIP'],
			);

		if (isset($arFieldsOrig['NOM_DIP']) && isset($arFieldsModif['NOM_DIP'])
			&& $arFieldsOrig['NOM_DIP'] != $arFieldsModif['NOM_DIP'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'NOM_DIP',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_NOM_DIP'),
				'VALUE_OLD' => $arFieldsOrig['NOM_DIP'],
				'VALUE_NEW' => $arFieldsModif['NOM_DIP'],
			);

		if (isset($arFieldsOrig['WHO_DIP']) && isset($arFieldsModif['WHO_DIP'])
			&& $arFieldsOrig['WHO_DIP'] != $arFieldsModif['WHO_DIP'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'WHO_DIP',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_WHO_DIP'),
				'VALUE_OLD' => $arFieldsOrig['WHO_DIP'],
				'VALUE_NEW' => $arFieldsModif['WHO_DIP'],
			);

		if (isset($arFieldsOrig['WHEN_DIP']) && isset($arFieldsModif['WHEN_DIP'])
			&& $arFieldsOrig['WHEN_DIP'] != $arFieldsModif['WHEN_DIP'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'WHEN_DIP',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_WHEN_DIP'),
				'VALUE_OLD' => $arFieldsOrig['WHEN_DIP'],
				'VALUE_NEW' => $arFieldsModif['WHEN_DIP'],
			);

		if (isset($arFieldsOrig['END_YEAR']) && isset($arFieldsModif['END_YEAR'])
			&& $arFieldsOrig['END_YEAR'] != $arFieldsModif['END_YEAR'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'END_YEAR',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_END_YEAR'),
				'VALUE_OLD' => $arFieldsOrig['END_YEAR'],
				'VALUE_NEW' => $arFieldsModif['END_YEAR'],
			);

		if (isset($arFieldsOrig['HONORS_DIP']) && isset($arFieldsModif['HONORS_DIP'])
			&& $arFieldsOrig['HONORS_DIP'] != $arFieldsModif['HONORS_DIP'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'HONORS_DIP',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_HONORS_DIP'),
				'VALUE_OLD' => $arFieldsOrig['HONORS_DIP'],
				'VALUE_NEW' => $arFieldsModif['HONORS_DIP'],
			);

		if (isset($arFieldsOrig['ORIGINAL_DIP']) && isset($arFieldsModif['ORIGINAL_DIP'])
			&& $arFieldsOrig['ORIGINAL_DIP'] != $arFieldsModif['ORIGINAL_DIP'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'ORIGINAL_DIP',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_ORIGINAL_DIP'),
				'VALUE_OLD' => $arFieldsOrig['ORIGINAL_DIP'],
				'VALUE_NEW' => $arFieldsModif['ORIGINAL_DIP'],
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

		if (!isset($arFields['SHARED']) || ($arFields['SHARED'] != 'Y' && $arFields['SHARED'] != 'N'))
			$arFields['SHARED'] = $arRow['SHARED'];

		if (!isset($arFields['PHONE']))
			$arFields['PHONE'] = $arRow['PHONE'];

		if (!isset($arFields['EMAIL']))
			$arFields['EMAIL'] = $arRow['EMAIL'];

		if (!isset($arFields['LAST_NAME']) && !isset($arFields['NAME']) && isset($arFields['FULL_NAME'])
            && count($arName=explode(' ',$arFields['FULL_NAME']))>1) {
            $arFields['LAST_NAME'] = $arName[0];
            $arFields['NAME'] = $arName[1];
            $arFields['SECOND_NAME'] = $arName[2];
        }

		if (!isset($arFields['FULL_NAME']))
			$arFields['FULL_NAME'] = $arRow['FULL_NAME'];

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

			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderPhysicalUpdate');
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
						$this->LAST_ERROR = GetMessage('ORDER_PHYSICAL_UPDATE_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
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
					$arEvent['ENTITY_TYPE'] = 'PHYSICAL';
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
			$sUpdate = $DB->PrepareUpdate('b_order_physical', $arFields);
			if (strlen($sUpdate) > 0)
			{
				$DB->Query("UPDATE b_order_physical SET {$sUpdate} WHERE ID = '{$ID}'", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
				if($arFields['SHARED']=='Y' && $arRow['SHARED']=='Y' && ($arFields['PHONE']!=$arRow['PHONE'] || $arFields['EMAIL']!=$arRow['EMAIL']))
					COrderHelper::ChangeAgentInfo($ID,$arFields['PHONE'],$arFields['EMAIL']);
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
				$afterEvents = GetModuleEvents('order', 'OnAfterOrderPhysicalUpdate');
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

		if (!isset($arFields['SHARED']) || ($arFields['SHARED'] != 'Y' && $arFields['SHARED'] != 'N'))
			$arFields['SHARED'] = 'N';


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
				$sEntityPerm = $userPerms->GetPermType('PHYSICAL', 'ADD');
				if ($sEntityPerm == BX_ORDER_PERM_NONE)
				{
					$this->LAST_ERROR = GetMessage('ORDER_PERMISSION_DENIED');
					$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					return false;
				}
			}


			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderPhysicalAdd');
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
						$this->LAST_ERROR = GetMessage('ORDER_PHYSICAL_CREATION_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
						$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					}
					return false;
				}
			}

			$DB->Add('b_order_physical', $arFields, array(), 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);



			/*if($bUpdateSearch)
			{
				$arFilterTmp = Array('ID' => $ID);
				if (!$this->bCheckPermission)
					$arFilterTmp["CHECK_PERMISSIONS"] = "N";
				CCrmSearch::UpdateSearch($arFilterTmp, 'DEAL', true);
			}*/

			$result = $arFields['ID'];

			COrderEvent::RegisterAddEvent(self::$TYPE_NAME, $result, $iUserId);


			$afterEvents = GetModuleEvents('order', 'OnAfterOrderPhysicalAdd');
			while ($arEvent = $afterEvents->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array(&$arFields));
			}

			if(isset($arFields['ORIGIN_ID']) && $arFields['ORIGIN_ID'] !== '')
			{
				$afterEvents = GetModuleEvents('order', 'OnAfterExternalOrderPhysicalAdd');
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

		if (isset($arFields['LAST_NAME']) && $arFields['LAST_NAME']=='')
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_LAST_NAME')))."<br />\n";

		if (isset($arFields['NAME']) && $arFields['NAME']=='')
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_NAME')))."<br />\n";

		if (!empty($arFields['BDAY']) && !CheckDateTime($arFields['BDAY']))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_BDAY')))."<br />\n";

		if (!empty($arFields['GENDER']) && !in_array($arFields['GENDER'],array(GetMessage('GENDER_MALE'),GetMessage('GENDER_FEMALE'),'')))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_GENDER')))."<br />\n";

		/*if (!empty($arFields['EMAIL']) && !filter_var($arFields['EMAIL'],FILTER_VALIDATE_EMAIL))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_EMAIL')))."<br />\n";*/

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
			$sEntityPerm = $this->cPerms->GetPermType('PHYSICAL', 'DELETE');
			if ($sEntityPerm == BX_ORDER_PERM_NONE)
				return false;
			/*else if ($sEntityPerm == BX_CRM_PERM_SELF)
				$sWherePerm = " AND ASSIGNED_BY_ID = {$iUserId}";
			else if ($sEntityPerm == BX_CRM_PERM_OPEN)
				$sWherePerm = " AND (OPENED = 'Y' OR ASSIGNED_BY_ID = {$iUserId})";*/
		}

		$APPLICATION->ResetException();
		$events = GetModuleEvents('order', 'OnBeforeOrderPhysicalDelete');
		while ($arEvent = $events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if ($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}



		$dbRes = $DB->Query("DELETE FROM b_order_physical WHERE ID = '{$ID}'{$sWherePerm}", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
		if (is_object($dbRes) && $dbRes->AffectedRowsCount() > 0)
		{
			/*CCrmSearch::DeleteSearch('DEAL', $ID);

			$DB->Query("DELETE FROM b_crm_entity_perms WHERE ENTITY='DEAL' AND ENTITY_ID = $ID", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
			$GLOBALS['USER_FIELD_MANAGER']->Delete(self::$sUFEntityID, $ID);
			$CCrmFieldMulti = new CCrmFieldMulti();
			$CCrmFieldMulti->DeleteByElement('DEAL', $ID);*/
			$COrderEvent = new COrderEvent();
			$COrderEvent->DeleteByElement('PHYSICAL', $ID);




			//if(Bitrix\Crm\Settings\HistorySettings::getCurrent()->isDealDeletionEventEnabled())
			COrderEvent::RegisterDeleteEvent(BX_ORDER_PHYSICAL, $ID, $iUserId, array('FIELDS' => $arFields));

			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				$GLOBALS["CACHE_MANAGER"]->ClearByTag("order_entity_name_".BX_ORDER_PHYSICAL_ID."_".$ID);
			}

			$afterEvents = GetModuleEvents('order', 'OnAfterOrderPhysicalDelete');
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