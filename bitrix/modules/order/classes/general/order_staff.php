<?php

IncludeModuleLangFile(__FILE__);

class COrderStaff
{
    static public $sUFEntityID = 'ORDER_STAFF';
    public $LAST_ERROR = '';
    public $cPerms = null;
    protected $bCheckPermission = true;
    //const TABLE_ALIAS = 'L';
    protected static $TYPE_NAME = 'STAFF';

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
        /*$createdByJoin = ' LEFT JOIN (b_user U2 JOIN b_uts_user UF2 ON U2.ID=UF2.VALUE_ID) ON L.CREATED_BY_ID = UF2.UF_GUID';
        $createdByJoin .= ' LEFT JOIN b_order_physical UP2 ON UF2.UF_GUID=UP2.ID';
        $modifyByJoin = ' LEFT JOIN (b_user U3 JOIN b_uts_user UF3 ON U3.ID=UF3.VALUE_ID) ON L.MODIFY_BY_ID = UF3.UF_GUID';
        $modifyByJoin .= ' LEFT JOIN b_order_physical UP3 ON UF3.UF_GUID=UP3.ID';*/
        $physicalJoin = ' JOIN (b_order_physical P JOIN b_uts_user UF ON P.ID=UF.UF_GUID) ON L.ID = UF.VALUE_ID';

        $result = array(
            'ID' => array('FIELD' => 'P.ID', 'TYPE' => 'string','FROM'=>$physicalJoin),

            /*'CREATED_DATE' => array('FIELD' => 'L.DATE_CREATE', 'TYPE' => 'datetime'),
            'CREATED_BY_ID' => array('FIELD' => 'L.CREATED_BY_ID', 'TYPE' => 'string'),
            'CREATED_BY_LOGIN' => array('FIELD' => 'U2.LOGIN', 'TYPE' => 'string', 'FROM' => $createdByJoin),
            'CREATED_BY_FULL_NAME' => array('FIELD' => 'CONCAT (UP2.LAST_NAME, " ", UP2.NAME, " ", UP2.SECOND_NAME)', 'TYPE' => 'string', 'FROM' => $createdByJoin),
            'CREATED_BY_EMAIL' => array('FIELD' => 'UP2.EMAIL', 'TYPE' => 'string', 'FROM' => $createdByJoin),

            'MODIFY_DATE' => array('FIELD' => 'L.DATE_MODIFY', 'TYPE' => 'datetime'),
            'MODIFY_BY_ID' => array('FIELD' => 'L.MODIFY_BY_ID', 'TYPE' => 'string'),
            'MODIFY_BY_LOGIN' => array('FIELD' => 'U3.LOGIN', 'TYPE' => 'string', 'FROM' => $modifyByJoin),
            'MODIFY_BY_FULL_NAME' => array('FIELD' => 'CONCAT (UP3.LAST_NAME, " ", UP3.NAME, " ", UP3.SECOND_NAME)', 'TYPE' => 'string', 'FROM' => $modifyByJoin),
            'MODIFY_BY_EMAIL' => array('FIELD' => 'UP3.EMAIL', 'TYPE' => 'string', 'FROM' => $modifyByJoin),*/

            'LOGIN' => array('FIELD' => 'LOWER(L.LOGIN)', 'TYPE' => 'string'),
            'LAST_NAME' => array('FIELD' => 'P.LAST_NAME', 'TYPE' => 'string','FROM'=>$physicalJoin),
            'NAME' => array('FIELD' => 'P.NAME', 'TYPE' => 'string','FROM'=>$physicalJoin),
            'SECOND_NAME' => array('FIELD' => 'P.SECOND_NAME', 'TYPE' => 'string','FROM'=>$physicalJoin),
            'FULL_NAME' => array('FIELD' => 'CONCAT (P.LAST_NAME, " ", P.NAME, " ", P.SECOND_NAME)', 'TYPE' => 'string','FROM'=>$physicalJoin),
            'EMAIL' => array('FIELD' => 'P.EMAIL', 'TYPE' => 'string','FROM'=>$physicalJoin),
            'PHONE' => array('FIELD' => 'P.PHONE', 'TYPE' => 'string','FROM'=>$physicalJoin),
            'EMPLOYMENT' => array('FIELD' => 'UF.UF_EMPLOYMENT', 'TYPE' => 'string','FROM'=>$physicalJoin),
            'DEPARTMENT' => array('FIELD' => 'UF.UF_DEPARTMENT', 'TYPE' => 'string','FROM'=>$physicalJoin),
            'SYS_ID' => array('FIELD' => 'L.ID', 'TYPE' => 'string'),

        );
        return $result;
    }

    public static function GetListEx($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $arOptions = array())
    {
        global $DBType;
        $lb = new COrderEntityListBuilder(
            $DBType,
            'b_user',
            'L',
            self::GetFields()
        );
        return $lb->Prepare($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields, $arOptions);
    }

    public static function CompareFields($arFieldsOrig, $arFieldsModif, $bCheckPerms = true) {
        $arMsg = Array();

        /*if (isset($arFieldsOrig['TITLE']) && isset($arFieldsModif['TITLE'])
            && $arFieldsOrig['TITLE'] != $arFieldsModif['TITLE'])
            $arMsg[] = Array(
                'ENTITY_FIELD' => 'TITLE',
                'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_TITLE'),
                'VALUE_OLD' => $arFieldsOrig['TITLE'],
                'VALUE_NEW' => $arFieldsModif['TITLE'],
            );*/


        return $arMsg;
    }

    static function check_props($arEl,$props) {
        $uSuccess=false;

        if(isset($props['LOGIN']) && strlen(trim($props['LOGIN']))>2
            && $props['LOGIN']!=$arEl['LOGIN']) {
            $uSuccess = true;
            $arEl['LOGIN'] = $props['LOGIN'];
        }

        if(isset($props['GROUP']) && $props['GROUP']!=$arEl['GROUP']) {
            $uSuccess = true;
            $arEl['GROUP'] = $props['GROUP'];
        }
        $arEl['WORK_POSITION']=$arEl['GROUP'];
        unset($arEl['GROUP']);

        if(isset($props['EMPLOYMENT']) && $props['EMPLOYMENT']!=$arEl['EMPLOYMENT']) {
            $uSuccess = true;
            $arEl['EMPLOYMENT'] = $props['EMPLOYMENT'];
        }
        $arEl['UF_EMPLOYMENT']=$arEl['EMPLOYMENT'];
        unset($arEl['EMPLOYMENT']);

        if(isset($props['ACTIVE']) && ($props['ACTIVE']=='Y' || $props['ACTIVE']=='N')
            && $props['ACTIVE']!=$arEl['ACTIVE']) {
            $uSuccess = true;
            $arEl['ACTIVE'] = $props['ACTIVE'];
        }

        if(isset($props['PASSWORD']) && strlen(trim($props['PASSWORD']))>4
            && $props['PASSWORD']==$props['CONFIRM_PASSWORD']) {
            if(strlen($arEl["PASSWORD"]) > 32) {
                $salt = substr($arEl["PASSWORD"], 0, strlen($arEl["PASSWORD"]) - 32);
                $db_password = substr($arEl["PASSWORD"], -32);
            } else {
                $salt = "";
                $db_password = $arEl["PASSWORD"];
            }

            $user_password =  md5($salt.$props["PASSWORD"]);
            if($user_password!=$db_password) {
                $uSuccess = true;
                $arEl['PASSWORD'] = $props['PASSWORD'];
                $arEl['CONFIRM_PASSWORD'] = $props['CONFIRM_PASSWORD'];
            } else unset($arEl['PASSWORD']);
        } else unset($arEl['PASSWORD']);


        $arEl['UF_GUID']=$arEl['ID'];
        unset($arEl['ID']);

        if(!$uSuccess)
            return false;
        else
            return $arEl;
    }

    public function Update($ID, array &$arFields, $bCompare = true,  $options = array()) {
        global $DB,$USER;

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

        if (isset($arFields['DEPARTMENT']))
        {
            $arFields['UF_DEPARTMENT']=$arFields['DEPARTMENT'];
            unset($arFields['DEPARTMENT']);
        }

        if (isset($arFields['EMPLOYMENT']))
        {
            $arFields['UF_EMPLOYMENT']=$arFields['EMPLOYMENT'];
            unset($arFields['EMPLOYMENT']);
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
            $bResult=$USER->Update(COrderHelper::GetIdByGuid($ID),$arFields);


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

    /*public function Add($props) {
        $id=$props['ID'];
        if(!isset($id) || self::GetByID($id,true)) {
            return false;
        }
        $arEl=array(
            'ID' => $id,
            'LOGIN' => '',
            'GROUP' => '',
            'EMPLOYMENT' => '',
            'ACTIVE' => 'Y',
            'PASSWORD' => '',
            'EMAIL' => 'example@portal.ru',
        );

        $arLoad=self::check_props($arEl,$props);

        if($arLoad) {
            $user = new CUser;
            if ($user->Add($arLoad))
                return $id;
        }
        return false;
    }*/

    public function CheckFields(&$arFields)
    {
        $this->LAST_ERROR = '';

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
            $sEntityPerm = $this->cPerms->GetPermType('STAFF', 'DELETE');
            if ($sEntityPerm == BX_ORDER_PERM_NONE)
                return false;
            /*else if ($sEntityPerm == BX_CRM_PERM_SELF)
                $sWherePerm = " AND ASSIGNED_BY_ID = {$iUserId}";
            else if ($sEntityPerm == BX_CRM_PERM_OPEN)
                $sWherePerm = " AND (OPENED = 'Y' OR ASSIGNED_BY_ID = {$iUserId})";*/
        }

        $APPLICATION->ResetException();
        $events = GetModuleEvents('order', 'OnBeforeOrderStaffDelete');
        while ($arEvent = $events->Fetch())
            if (ExecuteModuleEventEx($arEvent, array($ID))===false)
            {
                $err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
                if ($ex = $APPLICATION->GetException())
                    $err .= ': '.$ex->GetString();
                $APPLICATION->throwException($err);
                return false;
            }



        $dbRes = $USER->Delete(COrderHelper::GetIdByGuid($ID));
        if (is_object($dbRes) && $dbRes->AffectedRowsCount() > 0)
        {
            /*CCrmSearch::DeleteSearch('DEAL', $ID);

            $DB->Query("DELETE FROM b_crm_entity_perms WHERE ENTITY='DEAL' AND ENTITY_ID = $ID", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
            $GLOBALS['USER_FIELD_MANAGER']->Delete(self::$sUFEntityID, $ID);
            $CCrmFieldMulti = new CCrmFieldMulti();
            $CCrmFieldMulti->DeleteByElement('DEAL', $ID);*/
            $COrderEvent = new COrderEvent();
            $COrderEvent->DeleteByElement('STAFF', $ID);




            //if(Bitrix\Crm\Settings\HistorySettings::getCurrent()->isDealDeletionEventEnabled())
            COrderEvent::RegisterDeleteEvent(BX_ORDER_STAFF, $ID, $iUserId, array('FIELDS' => $arFields));

            if(defined("BX_COMP_MANAGED_CACHE"))
            {
                $GLOBALS["CACHE_MANAGER"]->ClearByTag("order_entity_name_".BX_ORDER_STAFF_ID."_".$ID);
            }

            $afterEvents = GetModuleEvents('order', 'OnAfterOrderStaffDelete');
            while ($arEvent = $afterEvents->Fetch())
            {
                ExecuteModuleEventEx($arEvent, array($ID));
            }
        }
        return true;
    }

    public static function GetDepartments($arFilter=array()) {
        if(!is_array($arFilter))
            $arFilter=array();
        $arFilter = array_merge($arFilter,array(
            'IBLOCK_CODE' => 'departments',
            'GLOBAL_ACTIVE' => 'Y'
        ));
        $res = CIBlockSection::GetList(
            array(),
            $arFilter
        );
        while($el=$res->Fetch()) {
            $arRes[$el['ID']]=$el;
        }
        return $arRes;
    }
}