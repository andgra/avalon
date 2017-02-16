<?php

IncludeModuleLangFile(__FILE__);

class COrderCourse
{
	static public $sUFEntityID = 'ORDER_COURSE';
	public $LAST_ERROR = '';
	public $cPerms = null;
	protected $bCheckPermission = true;
	//const TABLE_ALIAS = 'L';
	protected static $TYPE_NAME = 'COURSE';

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
		//$assignedByJoin = 'LEFT JOIN b_user U ON L.ASSIGNED_BY_ID = U.ID';
        $createdByJoin = ' LEFT JOIN (b_user U2 JOIN b_uts_user UF2 ON U2.ID=UF2.VALUE_ID) ON L.CREATED_BY_ID = UF2.UF_GUID';
        $createdByJoin .= ' LEFT JOIN b_order_physical UP2 ON UF2.UF_GUID=UP2.ID';
        $modifyByJoin = ' LEFT JOIN (b_user U3 JOIN b_uts_user UF3 ON U3.ID=UF3.VALUE_ID) ON L.MODIFY_BY_ID = UF3.UF_GUID';
        $modifyByJoin .= ' LEFT JOIN b_order_physical UP3 ON UF3.UF_GUID=UP3.ID';
		$prevCourseJoin = ' LEFT JOIN b_order_course PC ON PC.ID=L.PREV_COURSE';
		$nextCourseJoin = ' LEFT JOIN b_order_course NC ON NC.PREV_COURSE=L.ID';

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

			'TITLE' => array('FIELD' => 'L.TITLE', 'TYPE' => 'string'),
			'ANNOTATION' => array('FIELD' => 'L.ANNOTATION', 'TYPE' => 'string'),
			'DESCRIPTION' => array('FIELD' => 'L.DESCRIPTION', 'TYPE' => 'string'),
			'COURSE_PROG' => array('FIELD' => 'L.COURSE_PROG', 'TYPE' => 'string'),
			'DURATION' => array('FIELD' => 'L.DURATION', 'TYPE' => 'string'),
			'PREV_COURSE' => array('FIELD' => 'L.PREV_COURSE', 'TYPE' => 'string'),
			'PREV_COURSE_TITLE' => array('FIELD' => 'PC.TITLE', 'TYPE' => 'string', 'FROM' => $prevCourseJoin),
			'NEXT_COURSE' => array('FIELD' => 'NC.ID', 'TYPE' => 'string'),
			'NEXT_COURSE_TITLE' => array('FIELD' => 'NC.TITLE', 'TYPE' => 'string', 'FROM' => $nextCourseJoin),
			'EXAM' => array('FIELD' => 'L.EXAM', 'TYPE' => 'string'),
			'LITER' => array('FIELD' => 'L.LITER', 'TYPE' => 'string'),
			'DOC' => array('FIELD' => 'L.DOC', 'TYPE' => 'string'),
			'NOMEN' => array('FIELD' => 'L.NOMEN', 'TYPE' => 'string'),
			'TEACHER' => array('FIELD' => 'L.TEACHER', 'TYPE' => 'string'),

		);
		return $result;
	}

    public static function GetListEx($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $arOptions = array())
	{
		global $DBType;
		$lb = new COrderEntityListBuilder(
			$DBType,
			'b_order_course',
			'L',
			self::GetFields()
		);
		//HACK:: override user fields data for unserialize file IDs
		$lb->SetUserFields(array('EXAM' => array('MULTIPLE' => 'Y'),
            'LITER' => array('MULTIPLE' => 'Y'),'DOC' => array('MULTIPLE' => 'Y'),
            'NOMEN' => array('MULTIPLE' => 'Y'),'TEACHER' => array('MULTIPLE' => 'Y')));
		return $lb->Prepare($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields, $arOptions);
	}

    public static function CompareFields($arFieldsOrig, $arFieldsModif) {
		$arMsg = Array();

		if (isset($arFieldsOrig['TITLE']) && isset($arFieldsModif['TITLE'])
			&& $arFieldsOrig['TITLE'] != $arFieldsModif['TITLE'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'TITLE',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_TITLE'),
				'VALUE_OLD' => $arFieldsOrig['TITLE'],
				'VALUE_NEW' => $arFieldsModif['TITLE'],
			);

		if (isset($arFieldsOrig['ANNOTATION']) && isset($arFieldsModif['ANNOTATION'])
			&& $arFieldsOrig['ANNOTATION'] != $arFieldsModif['ANNOTATION'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'ANNOTATION',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_ANNOTATION'),
				'VALUE_OLD' => $arFieldsOrig['ANNOTATION'],
				'VALUE_NEW' => $arFieldsModif['ANNOTATION'],
			);

		if (isset($arFieldsOrig['DESCRIPTION']) && isset($arFieldsModif['DESCRIPTION'])
			&& $arFieldsOrig['DESCRIPTION'] != $arFieldsModif['DESCRIPTION'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'DESCRIPTION',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_DESCRIPTION'),
				'VALUE_OLD' => $arFieldsOrig['DESCRIPTION'],
				'VALUE_NEW' => $arFieldsModif['DESCRIPTION'],
			);

		if (isset($arFieldsOrig['COURSE_PROG']) && isset($arFieldsModif['COURSE_PROG'])
			&& $arFieldsOrig['COURSE_PROG'] != $arFieldsModif['COURSE_PROG'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'COURSE_PROG',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_COURSE_PROG'),
				'VALUE_OLD' => $arFieldsOrig['COURSE_PROG'],
				'VALUE_NEW' => $arFieldsModif['COURSE_PROG'],
			);

		if (isset($arFieldsOrig['DURATION']) && isset($arFieldsModif['DURATION'])
			&& $arFieldsOrig['DURATION'] != $arFieldsModif['DURATION'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'DURATION',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_DURATION'),
				'VALUE_OLD' => $arFieldsOrig['DURATION'],
				'VALUE_NEW' => $arFieldsModif['DURATION'],
			);

		if (isset($arFieldsOrig['PREV_COURSE']) && isset($arFieldsModif['PREV_COURSE'])
			&& $arFieldsOrig['PREV_COURSE'] != $arFieldsModif['PREV_COURSE'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'PREV_COURSE',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_PREV_COURSE'),
				'VALUE_OLD' => $arFieldsOrig['PREV_COURSE'],
				'VALUE_NEW' => $arFieldsModif['PREV_COURSE'],
			);

		if (isset($arFieldsOrig['EXAM']) && isset($arFieldsModif['EXAM'])
			&& $arFieldsOrig['EXAM'] != $arFieldsModif['EXAM']
			&& unserialize($arFieldsOrig['EXAM']) != unserialize($arFieldsModif['EXAM']))
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'EXAM',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_EXAM'),
				'VALUE_OLD' => $arFieldsOrig['EXAM'],
				'VALUE_NEW' => $arFieldsModif['EXAM'],
			);

		if (isset($arFieldsOrig['LITER']) && isset($arFieldsModif['LITER'])
			&& $arFieldsOrig['LITER'] != $arFieldsModif['LITER']
			&& unserialize($arFieldsOrig['LITER']) != unserialize($arFieldsModif['LITER']))
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'LITER',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_LITER'),
				'VALUE_OLD' => $arFieldsOrig['LITER'],
				'VALUE_NEW' => $arFieldsModif['LITER'],
			);

		if (isset($arFieldsOrig['DOC']) && isset($arFieldsModif['DOC'])
			&& $arFieldsOrig['DOC'] != $arFieldsModif['DOC']
			&& unserialize($arFieldsOrig['DOC']) != unserialize($arFieldsModif['DOC']))
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'DOC',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_DOC'),
				'VALUE_OLD' => $arFieldsOrig['DOC'],
				'VALUE_NEW' => $arFieldsModif['DOC'],
			);

		if (isset($arFieldsOrig['NOMEN']) && isset($arFieldsModif['NOMEN'])
			&& $arFieldsOrig['NOMEN'] != $arFieldsModif['NOMEN']
			&& unserialize($arFieldsOrig['NOMEN']) != unserialize($arFieldsModif['NOMEN']))
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'NOMEN',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_NOMEN'),
				'VALUE_OLD' => $arFieldsOrig['NOMEN'],
				'VALUE_NEW' => $arFieldsModif['NOMEN'],
			);

		if (isset($arFieldsOrig['TEACHER']) && isset($arFieldsModif['TEACHER'])
			&& $arFieldsOrig['TEACHER'] != $arFieldsModif['TEACHER']
			&& unserialize($arFieldsOrig['TEACHER']) != unserialize($arFieldsModif['TEACHER']))
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'TEACHER',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_TEACHER'),
				'VALUE_OLD' => $arFieldsOrig['TEACHER'],
				'VALUE_NEW' => $arFieldsModif['TEACHER'],
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

		if (!isset($arFields['MODIFY_BY_ID']) || $arFields['MODIFY_BY_ID']=='')
			$arFields['MODIFY_BY_ID'] = $iUserId;
        else
            $iUserId = $arFields['MODIFY_BY_ID'];

        if(isset($arFields['EXAM']) && !is_array(unserialize($arFields['EXAM'])))
            $arFields['EXAM']=serialize(array());

        if(isset($arFields['LITER']) && !is_array(unserialize($arFields['LITER'])))
            $arFields['LITER']=serialize(array());

        if(isset($arFields['DOC']) && !is_array(unserialize($arFields['DOC'])))
            $arFields['DOC']=serialize(array());

        if(isset($arFields['NOMEN']) && !is_array(unserialize($arFields['NOMEN'])))
            $arFields['NOMEN']=serialize(array());

        if(isset($arFields['TEACHER']) && !is_array(unserialize($arFields['TEACHER'])))
            $arFields['TEACHER']=serialize(array());


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

			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderCourseUpdate');
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
						$this->LAST_ERROR = GetMessage('ORDER_COURSE_UPDATE_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
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
					$arEvent['ENTITY_TYPE'] = 'COURSE';
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
			$sUpdate = $DB->PrepareUpdate('b_order_course', $arFields);
			if (strlen($sUpdate) > 0)
			{
				$DB->Query("UPDATE b_order_course SET {$sUpdate} WHERE ID = '{$ID}'", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
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
				$afterEvents = GetModuleEvents('order', 'OnAfterOrderCourseUpdate');
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

        if(isset($arFields['EXAM']) && !is_array(unserialize($arFields['EXAM'])))
            $arFields['EXAM']=serialize(array());

        if(isset($arFields['LITER']) && !is_array(unserialize($arFields['LITER'])))
            $arFields['LITER']=serialize(array());

        if(isset($arFields['DOC']) && !is_array(unserialize($arFields['DOC'])))
            $arFields['DOC']=serialize(array());

        if(isset($arFields['NOMEN']) && !is_array(unserialize($arFields['NOMEN'])))
            $arFields['NOMEN']=serialize(array());

        if(isset($arFields['TEACHER']) && !is_array(unserialize($arFields['TEACHER'])))
            $arFields['TEACHER']=serialize(array());


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
				$sEntityPerm = $userPerms->GetPermType('COURSE', 'ADD');
				if ($sEntityPerm == BX_ORDER_PERM_NONE)
				{
					$this->LAST_ERROR = GetMessage('ORDER_PERMISSION_DENIED');
					$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					return false;
				}
			}


			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderCourseAdd');
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
						$this->LAST_ERROR = GetMessage('ORDER_COURSE_CREATION_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
						$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					}
					return false;
				}
			}

			$DB->Add('b_order_course', $arFields, array(), 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);



			/*if($bUpdateSearch)
			{
				$arFilterTmp = Array('ID' => $ID);
				if (!$this->bCheckPermission)
					$arFilterTmp["CHECK_PERMISSIONS"] = "N";
				CCrmSearch::UpdateSearch($arFilterTmp, 'DEAL', true);
			}*/

			$result = $arFields['ID'];

			COrderEvent::RegisterAddEvent(self::$TYPE_NAME, $result, $iUserId);


			$afterEvents = GetModuleEvents('order', 'OnAfterOrderCourseAdd');
			while ($arEvent = $afterEvents->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array(&$arFields));
			}

			if(isset($arFields['ORIGIN_ID']) && $arFields['ORIGIN_ID'] !== '')
			{
				$afterEvents = GetModuleEvents('order', 'OnAfterExternalOrderCourseAdd');
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

		if (isset($arFields['TITLE']) && $arFields['TITLE']=='')
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_TITLE')))."<br />\n";

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
			$sEntityPerm = $this->cPerms->GetPermType('COURSE', 'DELETE');
			if ($sEntityPerm == BX_ORDER_PERM_NONE)
				return false;
			/*else if ($sEntityPerm == BX_CRM_PERM_SELF)
				$sWherePerm = " AND ASSIGNED_BY_ID = {$iUserId}";
			else if ($sEntityPerm == BX_CRM_PERM_OPEN)
				$sWherePerm = " AND (OPENED = 'Y' OR ASSIGNED_BY_ID = {$iUserId})";*/
		}

		$APPLICATION->ResetException();
		$events = GetModuleEvents('order', 'OnBeforeOrderCourseDelete');
		while ($arEvent = $events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if ($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}



		$dbRes = $DB->Query("DELETE FROM b_order_course WHERE ID = '{$ID}'{$sWherePerm}", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
		if (is_object($dbRes) && $dbRes->AffectedRowsCount() > 0)
		{
			/*CCrmSearch::DeleteSearch('DEAL', $ID);

			$DB->Query("DELETE FROM b_crm_entity_perms WHERE ENTITY='DEAL' AND ENTITY_ID = $ID", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
			$GLOBALS['USER_FIELD_MANAGER']->Delete(self::$sUFEntityID, $ID);
			$CCrmFieldMulti = new CCrmFieldMulti();
			$CCrmFieldMulti->DeleteByElement('DEAL', $ID);*/
			$COrderEvent = new COrderEvent();
			$COrderEvent->DeleteByElement('COURSE', $ID);




			//if(Bitrix\Crm\Settings\HistorySettings::getCurrent()->isDealDeletionEventEnabled())
			COrderEvent::RegisterDeleteEvent(BX_ORDER_COURSE, $ID, $iUserId, array('FIELDS' => $arFields));

			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				$GLOBALS["CACHE_MANAGER"]->ClearByTag("order_entity_name_".BX_ORDER_COURSE_ID."_".$ID);
			}

			$afterEvents = GetModuleEvents('order', 'OnAfterOrderCourseDelete');
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
}