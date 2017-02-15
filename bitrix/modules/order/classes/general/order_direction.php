<?php

IncludeModuleLangFile(__FILE__);

class COrderDirection
{
	static public $sUFEntityID = 'ORDER_DIRECTION';
	public $LAST_ERROR = '';
	public $cPerms = null;
	protected $bCheckPermission = true;
	const TABLE_ALIAS = 'L';
	protected static $TYPE_NAME = 'DIRECTION';

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
		$managerJoin = ' LEFT JOIN b_order_physical P ON L.MANAGER_ID = P.ID';
		$parentJoin = ' LEFT JOIN b_order_direction D ON L.PARENT_ID = D.ID';
		$nomenJoin = ' LEFT JOIN b_order_nomen N ON L.DEFAULT_NOMEN_ID = N.ID';
		$rootJoin = ' LEFT JOIN b_order_direction R ON L.ROOT_ID = R.ID';

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

			'PARENT_ID' => array('FIELD' => 'L.PARENT_ID', 'TYPE' => 'string'),
			'PARENT_TITLE' => array('FIELD' => 'D.TITLE', 'TYPE' => 'string', 'FROM' => $parentJoin),

			'ROOT_ID' => array('FIELD' => 'L.ROOT_ID', 'TYPE' => 'string'),
			'ROOT_TITLE' => array('FIELD' => 'D.TITLE', 'TYPE' => 'string', 'FROM' => $rootJoin),

			'MANAGER_ID' => array('FIELD' => 'L.MANAGER_ID', 'TYPE' => 'string'),
			'MANAGER_FULL_NAME' => array('FIELD' => 'CONCAT (P.LAST_NAME, " ", P.NAME, " ", P.SECOND_NAME)', 'TYPE' => 'string', 'FROM' => $managerJoin),
			'MANAGER_EMAIL' => array('FIELD' => 'P.EMAIL', 'TYPE' => 'string', 'FROM' => $managerJoin),

			'DEFAULT_NOMEN_ID' => array('FIELD' => 'L.DEFAULT_NOMEN_ID', 'TYPE' => 'string'),
			'DEFAULT_NOMEN_TITLE' => array('FIELD' => 'N.TITLE', 'TYPE' => 'string', 'FROM' => $nomenJoin),

			'DESCRIPTION' => array('FIELD' => 'L.DESCRIPTION', 'TYPE' => 'string'),
			'PRIVATE' => array('FIELD' => 'CASE WHEN L.PRIVATE<>"Y" THEN "N" ELSE "Y" END', 'TYPE' => 'string'),

			'BEHAVIOR' => array('FIELD' => 'CASE WHEN LEFT(L.BEHAVIOR, 6)="SELECT" THEN "SELECT" ELSE L.BEHAVIOR END', 'TYPE' => 'string'),
			'BEHAVIOR_ENTITY_TYPE' => array('FIELD' => 'CASE WHEN LEFT(L.BEHAVIOR, 6)="SELECT" THEN LOWER(SUBSTRING(L.BEHAVIOR,7,INSTR(L.BEHAVIOR, "#")-7)) ELSE "" END', 'TYPE' => 'string'),
			'BEHAVIOR_ENTITY_ID' => array('FIELD' => 'CASE WHEN LEFT(L.BEHAVIOR, 6)="SELECT" THEN SUBSTRING(L.BEHAVIOR,INSTR(L.BEHAVIOR, "#")+1) ELSE "" END', 'TYPE' => 'string'),
		);
		return $result;
	}

    public static function GetListEx($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $arOptions = array())
	{
		global $DBType;
		$lb = new COrderEntityListBuilder(
			$DBType,
			'b_order_direction',
			'L',
			self::GetFields()
		);
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

		if (isset($arFieldsOrig['PARENT_ID']) && isset($arFieldsModif['PARENT_ID'])
			&& $arFieldsOrig['PARENT_ID'] != $arFieldsModif['PARENT_ID'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'PARENT_ID',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_PARENT_ID'),
				'VALUE_OLD' => $arFieldsOrig['PARENT_ID'],
				'VALUE_NEW' => $arFieldsModif['PARENT_ID'],
			);

		if (isset($arFieldsOrig['MANAGER_ID']) && isset($arFieldsModif['MANAGER_ID'])
			&& $arFieldsOrig['MANAGER_ID'] != $arFieldsModif['MANAGER_ID'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'MANAGER_ID',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_MANAGER_ID'),
				'VALUE_OLD' => $arFieldsOrig['MANAGER_ID'],
				'VALUE_NEW' => $arFieldsModif['MANAGER_ID'],
			);

		if (isset($arFieldsOrig['DESCRIPTION']) && isset($arFieldsModif['DESCRIPTION'])
			&& $arFieldsOrig['DESCRIPTION'] != $arFieldsModif['DESCRIPTION'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'DESCRIPTION',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_DESCRIPTION'),
				'VALUE_OLD' => $arFieldsOrig['DESCRIPTION'],
				'VALUE_NEW' => $arFieldsModif['DESCRIPTION'],
			);

		if (isset($arFieldsOrig['PRIVATE']) && isset($arFieldsModif['PRIVATE'])
			&& $arFieldsOrig['PRIVATE'] != $arFieldsModif['PRIVATE'])
			$arMsg[] = Array(
				'ENTITY_FIELD' => 'PRIVATE',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_PRIVATE'),
				'VALUE_OLD' => $arFieldsOrig['PRIVATE'],
				'VALUE_NEW' => $arFieldsModif['PRIVATE'],
			);

		if (isset($arFieldsOrig['BEHAVIOR']) && isset($arFieldsModif['BEHAVIOR'])
			&& $arFieldsOrig['BEHAVIOR'] != $arFieldsModif['BEHAVIOR']) {

			if ($arFieldsOrig['BEHAVIOR']=='SELECT') {
				$arFieldsOrig['BEHAVIOR'] .= $arFieldsOrig['BEHAVIOR_ENTITY_TYPE'].'#';
				$arFieldsOrig['BEHAVIOR'] .= $arFieldsOrig['BEHAVIOR_ENTITY_ID'];
			}

			$arMsg[] = Array(
				'ENTITY_FIELD' => 'BEHAVIOR',
				'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_BEHAVIOR'),
				'VALUE_OLD' => $arFieldsOrig['BEHAVIOR'],
				'VALUE_NEW' => $arFieldsModif['BEHAVIOR'],
			);
		}


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

		if (isset($arFields['ROOT_ID']))
		{
			unset($arFields['ROOT_ID']);
		}

		$arFields['~DATE_MODIFY'] = $DB->CurrentTimeFunction();

		//Scavenging

		if (!isset($arFields['MODIFY_BY_ID']) || $arFields['MODIFY_BY_ID'] =='')
			$arFields['MODIFY_BY_ID'] = $iUserId;
        else
            $iUserId = $arFields['MODIFY_BY_ID'];


		//$assignedByID = isset($arFields['ASSIGNED_BY_ID']) ? $arFields['ASSIGNED_BY_ID'] : $arRow['ASSIGNED_BY_ID'];

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

			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderDirectionUpdate');
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
						$this->LAST_ERROR = GetMessage('ORDER_DIRECTION_UPDATE_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
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
					$arEvent['ENTITY_TYPE'] = 'DIRECTION';
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

			if(isset($arFields['PARENT_ID'])) {
				$rootID=self::GetSuperParent($arFields['PARENT_ID']);
				if($rootID==false)
					$rootID=$ID;
				$arFields['ROOT_ID']=$rootID;
			}
			$sUpdate = $DB->PrepareUpdate('b_order_direction', $arFields);
			if (strlen($sUpdate) > 0)
			{
				$DB->Query("UPDATE b_order_direction SET {$sUpdate} WHERE ID = '{$ID}'", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
				$bResult = true;
				if(isset($rootID)) {
					$children = self::GetChildren($ID);
					foreach ($children as $childID => $child) {
						$this->SetRootId($childID, $rootID);
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
				$afterEvents = GetModuleEvents('order', 'OnAfterOrderDirectionUpdate');
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

		if (isset($arFields['ROOT_ID']))
			unset($arFields['ROOT_ID']);

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
				$sEntityPerm = $userPerms->GetPermType('DIRECTION', 'ADD');
				if ($sEntityPerm == BX_ORDER_PERM_NONE)
				{
					$this->LAST_ERROR = GetMessage('ORDER_PERMISSION_DENIED');
					$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					return false;
				}
			}


			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderDirectionAdd');
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
						$this->LAST_ERROR = GetMessage('ORDER_DIRECTION_CREATION_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
						$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					}
					return false;
				}
			}
			if(!isset($arFields['PARENT_ID'])) {
				$arFields['PARENT_ID']='';
			}
			$rootID=self::GetSuperParent($arFields['PARENT_ID']);
			if($rootID==false)
				$rootID=$arFields['ID'];
			$arFields['ROOT_ID']=$rootID;

			$DB->Add('b_order_direction', $arFields, array(), 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);

			$result = $arFields['ID'];

			if(isset($rootID)) {
				$children = self::GetChildren($result);
				foreach ($children as $childID => $child) {
					$this->SetRootId($childID, $rootID);
				}
			}
			COrderEvent::RegisterAddEvent(self::$TYPE_NAME, $result, $iUserId);

			/*if($bUpdateSearch)
			{
				$arFilterTmp = Array('ID' => $ID);
				if (!$this->bCheckPermission)
					$arFilterTmp["CHECK_PERMISSIONS"] = "N";
				CCrmSearch::UpdateSearch($arFilterTmp, 'DEAL', true);
			}*/




			$afterEvents = GetModuleEvents('order', 'OnAfterOrderDirectionAdd');
			while ($arEvent = $afterEvents->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array(&$arFields));
			}

			if(isset($arFields['ORIGIN_ID']) && $arFields['ORIGIN_ID'] !== '')
			{
				$afterEvents = GetModuleEvents('order', 'OnAfterExternalOrderDirectionAdd');
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

		if (!empty($arFields['PRIVATE']) && ($arFields['PRIVATE']!='Y' && $arFields['PRIVATE']!='N'))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_PRIVATE')))."<br />\n";

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
			$sEntityPerm = $this->cPerms->GetPermType('DIRECTION', 'DELETE');
			if ($sEntityPerm == BX_ORDER_PERM_NONE)
				return false;
			/*else if ($sEntityPerm == BX_CRM_PERM_SELF)
				$sWherePerm = " AND ASSIGNED_BY_ID = {$iUserId}";
			else if ($sEntityPerm == BX_CRM_PERM_OPEN)
				$sWherePerm = " AND (OPENED = 'Y' OR ASSIGNED_BY_ID = {$iUserId})";*/
		}

		$APPLICATION->ResetException();
		$events = GetModuleEvents('order', 'OnBeforeOrderDirectionDelete');
		while ($arEvent = $events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if ($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}



		$dbRes = $DB->Query("DELETE FROM b_order_direction WHERE ID = '{$ID}'{$sWherePerm}", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
		if (is_object($dbRes) && $dbRes->AffectedRowsCount() > 0)
		{
			/*CCrmSearch::DeleteSearch('DEAL', $ID);

			$DB->Query("DELETE FROM b_crm_entity_perms WHERE ENTITY='DEAL' AND ENTITY_ID = $ID", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
			$GLOBALS['USER_FIELD_MANAGER']->Delete(self::$sUFEntityID, $ID);
			$CCrmFieldMulti = new CCrmFieldMulti();
			$CCrmFieldMulti->DeleteByElement('DEAL', $ID);*/
			$COrderEvent = new COrderEvent();
			$COrderEvent->DeleteByElement('DIRECTION', $ID);




			//if(Bitrix\Crm\Settings\HistorySettings::getCurrent()->isDealDeletionEventEnabled())
			COrderEvent::RegisterDeleteEvent(BX_ORDER_DIRECTION, $ID, $iUserId, array('FIELDS' => $arFields));

			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				$GLOBALS["CACHE_MANAGER"]->ClearByTag("order_entity_name_".BX_ORDER_DIRECTION_ID."_".$ID);
			}

			$afterEvents = GetModuleEvents('order', 'OnAfterOrderDirectionDelete');
			while ($arEvent = $afterEvents->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array($ID));
			}
		}
		return true;
	}

	private function SetRootId($ID,$rootID)
	{
		global $DB;
		$arFields=array('ROOT_ID'=>$rootID);
		$sUpdate = $DB->PrepareUpdate('b_order_direction', $arFields);
		$DB->Query("UPDATE b_order_direction SET {$sUpdate} WHERE ID = '{$ID}'", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);

	}

    public static function GetTreeMenu($arr=null,$parentID=0,$depth=1) {
        if(is_null($arr)) {
            $res = self::GetListEx(array(), array('!ID' => '000000000'));
            while($el=$res->Fetch()) {
                $arr[$el['ID']]=$el;
            }
        }
		$structure=array();
        foreach($arr as $ID=>$el)
        {
            if($el['PARENT_ID']==$parentID){
                $childs=self::GetTreeMenu($arr,$ID,$depth+1);
                if($childs==null)
                {
                    $parent=false;
                    $childs=array();
                }
                else
                {
                    $parent=true;
                }
				$el['DEPTH_LEVEL']=$depth;
				$el['IS_PARENT']=$parent;
				$structure[]=$el;
                $structure=array_merge($structure, $childs);
            }
        }
        return  $structure;
    }

	public static function GetTree($arr=null,$parentID=0) {
		if(is_null($arr)) {
			$res = self::GetListEx(array(), array('!ID' => '000000000'));
			while($el=$res->Fetch()) {
				$arr[$el['ID']]=$el;
			}
		}
		$arDirs=array();
		foreach($arr as $ID=>$el)
		{
			if($el['PARENT_ID']==$parentID){
				$childs=self::GetTree($arr,$ID);
				$arDirs[$ID]=$el;
				if(isset($el['CHILD_NOMENS'])) {
					$arDirs[$ID]['CHILD_NOMENS']=$el['CHILD_NOMENS'];
				}
				if(!is_null($childs)) {
					$arDirs[$ID]['CHILD_DIRECTIONS']=$childs;
				}
			}
		}
		return  $arDirs;
	}

	public static function GetSuperParent($needle,$tree=null) {
		if($needle=='') {
			return false;
		}
		if($tree==null) {
			$tree=self::GetTree();
		}
		$found=false;
		foreach($tree as $num=>$el) {
			if($el['ID']==$needle) {
				return $el['PARENT_ID']==''?$el['ID']:true;
			} elseif(!empty($el['CHILD_DIRECTIONS'])) {
				$found=self::GetSuperParent($needle,$el['CHILD_DIRECTIONS']);
			}
			if($found) {
				return $el['PARENT_ID']==''?$el['ID']:true;
			}
		}
		return false;
	}



	public static function GetChildren($parentID=null,$arr=null) {
		if(is_null($arr)) {
			$res = self::GetListEx(array(), array('!ID' => '000000000'));
			while($el=$res->Fetch()) {
				$arr[$el['ID']]=$el;
			}
		}
		$structure=array();
		foreach($arr as $ID=>$el)
		{
			if($el['PARENT_ID']==$parentID){
				$childs=self::GetChildren($ID,$arr);
				if($childs==null)
				{
					$childs=array();
				}
				$structure[$ID]=$el;
				$structure=array_merge($structure, $childs);
			}
		}
		return $structure;
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