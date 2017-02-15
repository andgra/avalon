<?php

IncludeModuleLangFile(__FILE__);

class COrderPerms
{
    const PERM_NONE = BX_ORDER_PERM_NONE;
    const PERM_SELF = BX_ORDER_PERM_SELF;
    const PERM_DEPARTMENT = BX_ORDER_PERM_DEPARTMENT;
    const PERM_SUBDEPARTMENT = BX_ORDER_PERM_SUBDEPARTMENT;
    const PERM_OPEN = BX_ORDER_PERM_OPEN;
    const PERM_ALL = BX_ORDER_PERM_ALL;
    const PERM_CONFIG = BX_ORDER_PERM_CONFIG;

    private static $ENTITY_ATTRS = array();
    private static $INSTANCES = array();
    private static $USER_ADMIN_FLAGS = array();
    protected $userId = 0;
    protected $userGuid=0;
    protected $arUserPerms = array();

    //use iternal ID
    function __construct($userId)
    {
        $this->userId = $userId;
        $this->userGuid = COrderHelper::GetGuidById($userId);
        $this->arUserPerms = COrderRole::GetUserPerms($this->userId);
    }

    public static function GetCurrentUserPermissions()
    {
        GLOBAL $USER;
        $userID = $USER->GetId();
        if(!isset(self::$INSTANCES[$userID]))
        {
            self::$INSTANCES[$userID] = new COrderPerms($userID);
        }
        return self::$INSTANCES[$userID];
    }

    //use external ID
    public static function GetUserPermissions($userID)
    {
        $userID=COrderHelper::GetIdByGuid($userID);

        if($userID <= 0)
        {
            GLOBAL $USER;
            $userID = $USER->GetId();
        }

        if(!isset(self::$INSTANCES[$userID]))
        {
            self::$INSTANCES[$userID] = new COrderPerms($userID);
        }
        return self::$INSTANCES[$userID];
    }

    public static function GetCurrentUserID()
    {
        return COrderHelper::GetCurrentUserID();
    }

    //use external ID
    public static function IsAdmin($userID = 0)
    {

        $result = false;
        if($userID <= 0)
        {
            $user = COrderHelper::GetCurrentUser();
            $userID =  $user->GetID();

            if($userID <= 0)
            {
                false;
            }

            if(isset(self::$USER_ADMIN_FLAGS[$userID]))
            {
                return self::$USER_ADMIN_FLAGS[$userID];
            }

            $result = $user->IsAdmin();
            if($result)
            {
                self::$USER_ADMIN_FLAGS[$userID] = true;
                return true;
            }

            try
            {
                if(\Bitrix\Main\ModuleManager::isModuleInstalled('bitrix24')
                    && CModule::IncludeModule('bitrix24'))
                {
                    if(class_exists('CBitrix24')
                        && method_exists('CBitrix24', 'IsPortalAdmin'))
                    {
                        // New style check
                        $result = CBitrix24::IsPortalAdmin($userID);
                    }
                    else
                    {
                        // Check user group 1 ('Portal admins')
                        $arGroups = $user->GetUserGroup($userID);
                        $result = in_array(1, $arGroups);
                    }
                }
            }
            catch(Exception $e)
            {
            }
        }
        else
        {
            $res=COrderStaff::GetByID($userID);
            $userID=$res['SYS_ID'];
            if(isset(self::$USER_ADMIN_FLAGS[$userID]))
            {
                return self::$USER_ADMIN_FLAGS[$userID];
            }

            try
            {
                if(IsModuleInstalled('bitrix24')
                    && CModule::IncludeModule('bitrix24')
                    && class_exists('CBitrix24')
                    && method_exists('CBitrix24', 'IsPortalAdmin'))
                {
                    // Bitrix24 context new style check
                    $result = CBitrix24::IsPortalAdmin($userID);
                }
                else
                {
                    //Check user group 1 ('Admins')
                    $user = new CUser();
                    $arGroups = $user->GetUserGroup($userID);
                    $result = in_array(1, $arGroups);
                }
            }
            catch(Exception $e)
            {
            }
        }
        self::$USER_ADMIN_FLAGS[$userID] = $result;
        return $result;
    }

    static public function IsAccessEnabled($userPerms = null)
    {
        if($userPerms === null || !is_object($userPerms))
        {
            $userPerms = COrderPerms::GetCurrentUserPermissions();
        }

        return !$userPerms->HavePerm('PHYSICAL', self::PERM_NONE)
        || !$userPerms->HavePerm('CONTACT', self::PERM_NONE)
        || !$userPerms->HavePerm('AGENT', self::PERM_NONE)
        || !$userPerms->HavePerm('DIRECTION', self::PERM_NONE)
        || !$userPerms->HavePerm('NOMEN', self::PERM_NONE)
        || !$userPerms->HavePerm('COURSE', self::PERM_NONE)
        || !$userPerms->HavePerm('GROUP', self::PERM_NONE)
        || !$userPerms->HavePerm('FORMED_GROUP', self::PERM_NONE)
        || !$userPerms->HavePerm('APP', self::PERM_NONE)
        || !$userPerms->HavePerm('REG', self::PERM_NONE)
        //|| !$userPerms->HavePerm('STAFF', self::PERM_NONE)
        ;
    }

    public static function IsAuthorized()
    {
        return COrderHelper::GetCurrentUser()->IsAuthorized();
    }

    //Use external ID
    static public function GetUserAttr($iUserID)
    {
        $iUserID=COrderHelper::GetIdByGuid($iUserID);
        static $arResult = array();
        if (!empty($arResult[$iUserID]))
        {
            return $arResult[$iUserID];
        }

        $iUserID = (int) $iUserID;

        $arResult[$iUserID] = array();

        $obRes = CAccess::GetUserCodes($iUserID);
        while($arCode = $obRes->Fetch())
            if (strpos($arCode['ACCESS_CODE'], 'DR') !== 0)
                $arResult[$iUserID][strtoupper($arCode['PROVIDER_ID'])][] = $arCode['ACCESS_CODE'];

        if (!empty($arResult[$iUserID]['INTRANET']) && IsModuleInstalled('intranet'))
        {
            foreach ($arResult[$iUserID]['INTRANET'] as $iDepartment)
            {
                if(substr($iDepartment, 0, 1) === 'D')
                {
                    $arTree = CIntranetUtils::GetDeparmentsTree(substr($iDepartment, 1), true);
                    foreach ($arTree as $iSubDepartment)
                    {
                        $arResult[$iUserID]['SUBINTRANET'][] = 'D'.$iSubDepartment;
                    }
                }
            }
        }

        return $arResult[$iUserID];
    }

    //Use external ID
    static public function BuildUserEntityAttr($guid)
    {
        $userID=COrderHelper::GetIdByGuid($guid);
        $result = array('INTRANET' => array());
        $userID = intval($userID);
        $arUserAttrs = $guid !=="" ? self::GetUserAttr($guid) : array();
        if(!empty($arUserAttrs['INTRANET']))
        {
            //HACK: Removing intranet subordination relations, otherwise staff will get access to boss's entities
            foreach($arUserAttrs['INTRANET'] as $code)
            {
                if(strpos($code, 'IU') !== 0)
                {
                    $result['INTRANET'][] = $code;
                }
            }
            $result['INTRANET'][] = "IU{$userID}";
        }
        return $result;
    }


    static public function GetCurrentUserAttr()
    {
        return self::GetUserAttr(COrderHelper::GetCurrentUserID());
    }

    public function GetUserID()
    {
        return $this->userGuid;
    }

    public function GetUserPerms()
    {
        return $this->arUserPerms;
    }

    public function HavePerm($permEntity, $permAttr, $permType = 'READ')
    {
        // HACK: only for product and currency support
        $permType = strtoupper($permType);
        if ($permEntity == 'CONFIG' && $permAttr == self::PERM_CONFIG && $permType == 'READ')
        {
            return true;
        }

        // HACK: Compatibility with CONFIG rights
        if ($permEntity == 'CONFIG')
            $permType = 'WRITE';

        if(self::IsAdmin($this->userGuid))
        {
            return $permAttr != self::PERM_NONE;
        }

        if (!isset($this->arUserPerms[$permEntity][$permType]))
            return $permAttr == self::PERM_NONE;

        $icnt = count($this->arUserPerms[$permEntity][$permType]);
        if ($icnt > 1 && $this->arUserPerms[$permEntity][$permType]['-'] == self::PERM_NONE)
        {
            foreach ($this->arUserPerms[$permEntity][$permType] as $sField => $arFieldValue)
            {
                if ($sField == '-')
                    continue ;
                $sPrevPerm = $permAttr;
                foreach ($arFieldValue as $fieldValue => $sAttr)
                    if ($sAttr > $permAttr)
                        return $sAttr == self::PERM_NONE;
                return $permAttr == self::PERM_NONE;
            }
        }

        if ($permAttr == self::PERM_NONE)
            return $this->arUserPerms[$permEntity][$permType]['-'] == self::PERM_NONE;

        if ($this->arUserPerms[$permEntity][$permType]['-'] >= $permAttr)
            return true;

        return false;
    }

    public function GetPermType($permEntity, $permType = 'READ', $arEntityAttr = array())
    {
        if (self::IsAdmin($this->userGuid))
            return self::PERM_ALL;

        if (!isset($this->arUserPerms[$permEntity][$permType]))
            return self::PERM_NONE;

        $icnt = count($this->arUserPerms[$permEntity][$permType]);

        if ($icnt == 1 && isset($this->arUserPerms[$permEntity][$permType]['-']))
            return $this->arUserPerms[$permEntity][$permType]['-'];
        else if ($icnt > 1)
        {
            foreach ($this->arUserPerms[$permEntity][$permType] as $sField => $arFieldValue)
            {
                if ($sField == '-')
                    continue ;
                foreach ($arFieldValue as $fieldValue => $sAttr)
                {
                    if (in_array($sField.$fieldValue, $arEntityAttr))
                        return $sAttr;
                }
            }
            return $this->arUserPerms[$permEntity][$permType]['-'];
        }
        else
            return self::PERM_NONE;
    }

    public static function GetEntityGroup($permEntity, $permAttr = self::PERM_NONE, $permType = 'READ')
    {

        global $DB;

        $arResult = array();
        $arRole = CCrmRole::GetRoleByAttr($permEntity, $permAttr, $permType);

        if (!empty($arRole))
        {
            $sSql = 'SELECT RELATION FROM b_order_role_relation WHERE RELATION LIKE \'G%\' AND ROLE_ID IN ('.implode(',', $arRole).')';
            $res = $DB->Query($sSql, false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
            while($row = $res->Fetch())
                $arResult[] = substr($row['RELATION'], 1);
        }
        return $arResult;
    }

    public static function GetEntityRelations($permEntity, $permAttr = self::PERM_NONE, $permType = 'READ')
    {
        global $DB;

        $arResult = array();
        $arRole = CCrmRole::GetRoleByAttr($permEntity, $permAttr, $permType);

        if (!empty($arRole))
        {
            $sSql = 'SELECT RELATION FROM b_order_role_relation WHERE ROLE_ID IN ('.implode(',', $arRole).')';
            $res = $DB->Query($sSql, false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
            while($row = $res->Fetch())
                $arResult[] = $row['RELATION'];
        }
        return $arResult;
    }

    public function CheckEnityAccess($permEntity, $permType, $arEntityAttr)
    {
        if (!is_array($arEntityAttr))
            $arEntityAttr = array();

        $enableCummulativeMode = COption::GetOptionString('order', 'enable_permission_cumulative_mode', 'Y') === 'Y';

        $permAttr = $this->GetPermType($permEntity, $permType, $arEntityAttr);
        if ($permAttr == self::PERM_NONE)
        {
            return false;
        }
        if ($permAttr == self::PERM_ALL)
        {
            return true;
        }
        if ($permAttr == self::PERM_OPEN)
        {
            if((in_array('O', $arEntityAttr) || in_array('U'.$this->userId, $arEntityAttr)))
            {
                return true;
            }

            //For backward compatibility (is not comulative mode)
            if(!$enableCummulativeMode)
            {
                return false;
            }
        }
        if ($permAttr >= self::PERM_SELF && in_array('U'.$this->userId, $arEntityAttr))
        {
            return true;
        }

        $arAttr = self::GetUserAttr($this->userGuid);

        if ($permAttr >= self::PERM_DEPARTMENT && is_array($arAttr['INTRANET']))
        {
            // PERM_OPEN: user may access to not opened entities in his department
            foreach ($arAttr['INTRANET'] as $iDepartment)
            {
                if (in_array($iDepartment, $arEntityAttr))
                {
                    return true;
                }
            }
        }
        if ($permAttr >= self::PERM_SUBDEPARTMENT && is_array($arAttr['SUBINTRANET']))
        {
            // PERM_OPEN: user may access to not opened entities in his intranet
            foreach ($arAttr['SUBINTRANET'] as $iDepartment)
            {
                if (in_array($iDepartment, $arEntityAttr))
                {
                    return true;
                }
            }
        }
        return false;
    }

    public function GetUserAttrForSelectEntity($permEntity, $permType, $bForcePermAll = false)
    {
        $arResult = array();
        if (!isset($this->arUserPerms[$permEntity][$permType]))
            return $arResult;

        $arAttr = self::GetUserAttr($this->userGuid);

        $sDefAttr = $this->arUserPerms[$permEntity][$permType]['-'];
        foreach ($this->arUserPerms[$permEntity][$permType] as $sField => $arFieldValue)
        {
            if ($sField === '-' && count($this->arUserPerms[$permEntity][$permType]) == 1)
            {
                $_arResult = array();
                $sAttr = $sDefAttr;
                if ($sAttr == self::PERM_NONE)
                {
                    continue;
                }
                if ($sAttr == self::PERM_OPEN)
                {
                    $_arResult[] = 'O';
                }
                else if ($sAttr != self::PERM_ALL || $bForcePermAll)
                {
                    if ($sAttr >= self::PERM_SELF)
                    {
                        foreach ($arAttr['USER'] as $iUser)
                        {
                            $arResult[] = array($iUser);
                        }
                    }
                    if ($sAttr >= self::PERM_DEPARTMENT && isset($arAttr['INTRANET']))
                    {
                        foreach ($arAttr['INTRANET'] as $iDepartment)
                        {
                            //HACK: SKIP IU code it is not required for this method
                            if(strlen($iDepartment) > 0 && substr($iDepartment, 0, 2) === 'IU')
                            {
                                continue;
                            }

                            if(!in_array($iDepartment, $_arResult))
                            {
                                $_arResult[] = $iDepartment;
                            }
                        }
                    }
                    if ($sAttr >= self::PERM_SUBDEPARTMENT && isset($arAttr['SUBINTRANET']))
                    {
                        foreach ($arAttr['SUBINTRANET'] as $iDepartment)
                        {
                            if(strlen($iDepartment) > 0 && substr($iDepartment, 0, 2) === 'IU')
                            {
                                continue;
                            }

                            if(!in_array($iDepartment, $_arResult))
                            {
                                $_arResult[] = $iDepartment;
                            }
                        }
                    }
                }
                else //self::PERM_ALL
                {
                    $arResult[] = array();
                }

                if(!empty($_arResult))
                {
                    $arResult[] = $_arResult;
                }
            }
            else
            {
                $arList = array();
                if ($permEntity == 'APP' && $sField == 'STATUS')
                {
                    $arList = COrderHelper::GetEnumList('APP',"STATUS");
                }
                else if ($permEntity == 'REG' && $sField == 'STATUS')
                {
                    $arList = COrderHelper::GetEnumList('REG',"STATUS");
                }
                else if ($permEntity == 'APP' && $sField == 'DIRECTION')
                {
                    $arList = array(
                        '000000001'=>'000000001',
                        '000000002'=>'000000002',
                        '000000003'=>'000000003',
                        '000000017'=>'000000017',
                    );
                }
                else if ($permEntity == 'REG' && $sField == 'DIRECTION')
                {
                    $arList = array(
                        '000000001'=>'000000001',
                        '000000002'=>'000000002',
                        '000000003'=>'000000003',
                        '000000017'=>'000000017',
                    );
                }

                foreach ($arList as $fieldValue => $sTitle)
                {
                    $_arResult = array();
                    $sAttr = $sDefAttr;
                    if (isset($this->arUserPerms[$permEntity][$permType][$sField][$fieldValue]))
                    {
                        $sAttr = $this->arUserPerms[$permEntity][$permType][$sField][$fieldValue];
                    }
                    if ($sAttr == self::PERM_NONE)
                    {
                        continue;
                    }
                    //$_arResult[] = $sField.$fieldValue;
                    if ($sAttr == self::PERM_OPEN)
                    {
                        $_arResult[] = 'O';
                    }
                    else if ($sAttr != self::PERM_ALL)
                    {
                        if ($sAttr >= self::PERM_SELF)
                        {
                            foreach ($arAttr['USER'] as $iUser)
                            {
                                $arResult[] = array($sField.$fieldValue, $iUser);
                            }
                        }
                        if ($sAttr >= self::PERM_DEPARTMENT && isset($arAttr['INTRANET']))
                        {
                            foreach ($arAttr['INTRANET'] as $iDepartment)
                            {
                                if(strlen($iDepartment) > 2 && substr($iDepartment, 0, 2) === 'IU')
                                {
                                    continue;
                                }

                                if(!in_array($iDepartment, $_arResult))
                                {
                                    $_arResult[] = $iDepartment;
                                }
                            }
                        }
                        if ($sAttr >= self::PERM_SUBDEPARTMENT && isset($arAttr['SUBINTRANET']))
                        {
                            foreach ($arAttr['SUBINTRANET'] as $iDepartment)
                            {
                                if(strlen($iDepartment) > 2 && substr($iDepartment, 0, 2) === 'IU')
                                {
                                    continue;
                                }

                                if(!in_array($iDepartment, $_arResult))
                                {
                                    $_arResult[] = $iDepartment;
                                }
                            }
                        }
                    }
                    else //self::PERM_ALL
                    {
                        $arResult[] = array($sField.$fieldValue);
                    }

                    if(!empty($_arResult))
                    {
                        $arResult[] = array_merge(array($sField.$fieldValue), $_arResult);
                    }
                }
            }
        }

        return $arResult;
    }

    static private function RegisterPermissionSet(&$items, $newItem)
    {
        $qty = count($items);
        if($qty === 0)
        {
            $items[] = $newItem;
            return $newItem;
        }

        $user = $newItem['USER'];
        $openedOnly = $newItem['OPENED_ONLY'];
        $departments = $newItem['DEPARTMENTS'];
        $departmentQty = count($departments);
        for($i = 0; $i < $qty; $i++)
        {
            if($user === $items[$i]['USER']
                && $openedOnly === $items[$i]['OPENED_ONLY']
                && $departmentQty === count($items[$i]['DEPARTMENTS'])
                && ($departmentQty === 0 || count(array_diff($departments, $items[$i]['DEPARTMENTS'])) === 0))
            {
                $items[$i]['SCOPES'] = array_merge($items[$i]['SCOPES'], $newItem['SCOPES']);
                return $items[$i];
            }
        }

        $items[] = $newItem;
        return $newItem;
    }

    static public function BuildSql($enityType, $sAliasPrefix, $mPermType, $arOptions = array())
    {
        $perms = null;

        if(!is_array($arOptions))
        {
            $arOptions = array();
        }

        if(isset($arOptions['PERMS']))
        {
            $perms = $arOptions['PERMS'];
        }

        if(!is_object($perms))
        {
            // Process current user permissions
            if (self::IsAdmin(0))
            {
                return '';
            }

            $perms = self::GetCurrentUserPermissions();
        }
        elseif(self::IsAdmin($perms->GetUserID()))
        {
            return '';
        }

        $arUserAttr = array();
        $arPermType = is_array($mPermType) ? $mPermType : array($mPermType);
        foreach ($arPermType as $sPermType)
        {
            $arUserAttr = array_merge($arUserAttr, $perms->GetUserAttrForSelectEntity($enityType, $sPermType));
        }

        if (empty($arUserAttr))
        {
            // Access denied
            return false;
        }

        $scopeRegex1 = '';
        $scopeRegex2 = '';
        if($enityType === 'APP' || $enityType === 'REG')
        {
            $scopeRegex1 = '/^STATUS[0-9A-Za-z\_\-]+$/';
            $scopeRegex2 = '/^DIRECTION[0-9A-Za-z\_\-]+$/';
        }

        $allAttrs = self::GetUserAttr($perms->GetUserID());
        $permissionSets = array();
        foreach ($arUserAttr as &$attrs)
        {
            if (empty($attrs))
            {
                continue;
            }

            $permissionSet = array(
                'USER' => '',
                'DEPARTMENTS' => array(),
                'OPENED_ONLY' => '',
                'SCOPES' => array()
            );

            $qty = count($attrs);
            for($i = 0; $i < $qty; $i++)
            {
                $attr = $attrs[$i];

                if(($scopeRegex1 !== '' && preg_match($scopeRegex1, $attr)) || ($scopeRegex2 !== '' && preg_match($scopeRegex2, $attr)))
                {
                    $permissionSet['SCOPES'][] = "'{$attr}'";
                }
                elseif($attr === 'O')
                {
                    $permissionSet['OPENED_ONLY'] = "'{$attr}'";
                }
                elseif(preg_match('/^U\d+$/', $attr))
                {
                    $permissionSet['USER'] = "'{$attr}'";
                }
                elseif(preg_match('/^D\d+$/', $attr))
                {
                    $permissionSet['DEPARTMENTS'][] = "'{$attr}'";
                }
            }

            if(empty($permissionSet['SCOPES']))
            {
                if($permissionSet['OPENED_ONLY'] !== '')
                {
                    //HACK: for OPENED ONLY mode - allow user own entities too.
                    $userAttr = isset($allAttrs['USER']) && is_array($allAttrs['USER']) && !empty($allAttrs['USER']) ? $allAttrs['USER'][0] : '';
                    if($userAttr !== '')
                    {
                        $permissionSets[] = array(
                            'USER' => "'{$userAttr}'",
                            'DEPARTMENTS' => array(),
                            'OPENED_ONLY' => '',
                            'SCOPES' => array()
                        );
                    }
                }

                $permissionSets[] = &$permissionSet;
                unset($permissionSet);
            }
            else
            {
                $permissionSet = self::RegisterPermissionSet($permissionSets, $permissionSet);
                if($permissionSet['OPENED_ONLY'] !== '')
                {
                    //HACK: for OPENED ONLY mode - allow user own entities too.
                    $userAttr = isset($allAttrs['USER']) && is_array($allAttrs['USER']) && !empty($allAttrs['USER']) ? $allAttrs['USER'][0] : '';
                    if($userAttr !== '')
                    {
                        self::RegisterPermissionSet(
                            $permissionSets,
                            array(
                                'USER' => "'{$userAttr}'",
                                'DEPARTMENTS' => array(),
                                'OPENED_ONLY' => '',
                                'SCOPES' => $permissionSet['SCOPES']
                            )
                        );
                    }
                }
            }
        }
        unset($attrs);

        $isRestricted = false;
        $subQueries = array();
        foreach($permissionSets as &$permissionSet)
        {
            $scopes = $permissionSet['SCOPES'];
            $scopeQty = count($scopes);
            if($scopeQty === 0)
            {
                $restrictionSql = '';
                if($permissionSet['OPENED_ONLY'] !== '')
                {
                    $attr = $permissionSet['OPENED_ONLY'];
                    $restrictionSql = "{$sAliasPrefix}P.ATTR = {$attr}";
                }
                elseif($permissionSet['USER'] !== '')
                {
                    $attr = $permissionSet['USER'];
                    $restrictionSql = "{$sAliasPrefix}P.ATTR = {$attr}";
                }
                elseif(!empty($permissionSet['DEPARTMENTS']))
                {
                    $departments = $permissionSet['DEPARTMENTS'];
                    $restrictionSql = count($departments) > 1
                        ? $sAliasPrefix.'P.ATTR IN('.implode(', ', $departments).')'
                        : $sAliasPrefix.'P.ATTR = '.$departments[0];
                }

                if($restrictionSql !== '')
                {
                    $subQueries[] = "SELECT {$sAliasPrefix}P.ENTITY_ID FROM b_order_entity_perms {$sAliasPrefix}P WHERE {$sAliasPrefix}P.ENTITY = '{$enityType}' AND {$restrictionSql}";

                    if(!$isRestricted)
                    {
                        $isRestricted = true;
                    }
                }
            }
            else
            {
                $scopeSql = $scopeQty > 1
                    ? $sAliasPrefix.'P2.ATTR IN ('.implode(', ', $scopes).')'
                    : $sAliasPrefix.'P2.ATTR = '.$scopes[0];

                $restrictionSql = '';
                if($permissionSet['OPENED_ONLY'] !== '')
                {
                    $attr = $permissionSet['OPENED_ONLY'];
                    $restrictionSql = "{$sAliasPrefix}P1.ATTR = {$attr}";
                }
                elseif($permissionSet['USER'] !== '')
                {
                    $attr = $permissionSet['USER'];
                    $restrictionSql = "{$sAliasPrefix}P1.ATTR = {$attr}";
                }
                elseif(!empty($permissionSet['DEPARTMENTS']))
                {
                    $departments = $permissionSet['DEPARTMENTS'];
                    $restrictionSql = count($departments) > 1
                        ? $sAliasPrefix.'P1.ATTR IN('.implode(', ', $departments).')'
                        : $sAliasPrefix.'P1.ATTR = '.$departments[0];
                }

                if($restrictionSql !== '')
                {
                    $subQueries[] = "SELECT {$sAliasPrefix}P2.ENTITY_ID FROM b_order_entity_perms {$sAliasPrefix}P1 INNER JOIN b_order_entity_perms {$sAliasPrefix}P2 ON {$sAliasPrefix}P1.ENTITY = '{$enityType}' AND {$sAliasPrefix}P2.ENTITY = '{$enityType}' AND {$sAliasPrefix}P1.ENTITY_ID = {$sAliasPrefix}P2.ENTITY_ID AND {$restrictionSql} AND {$scopeSql}";

                    if(!$isRestricted)
                    {
                        $isRestricted = true;
                    }
                }
                else
                {
                    $subQueries[] = "SELECT {$sAliasPrefix}P2.ENTITY_ID FROM b_order_entity_perms {$sAliasPrefix}P1 INNER JOIN b_order_entity_perms {$sAliasPrefix}P2 ON {$sAliasPrefix}P1.ENTITY = '{$enityType}' AND {$sAliasPrefix}P2.ENTITY = '{$enityType}' AND {$sAliasPrefix}P1.ENTITY_ID = {$sAliasPrefix}P2.ENTITY_ID AND {$scopeSql}";
                }
            }
        }
        unset($permissionSet);

        if(!$isRestricted/*|| empty($subQueries)*/)
        {
            return '';
        }

        $sqlUnion = isset($arOptions['PERMISSION_SQL_UNION']) && $arOptions['PERMISSION_SQL_UNION'] === 'DISTINCT' ? 'DISTINCT' : 'ALL';
        $subQuerySql = implode($sqlUnion === 'DISTINCT' ? ' UNION ' : ' UNION ALL ', $subQueries);
        //BAD SOLUTION IF USER HAVE A LOT OF RECORDS IN B_CRM_ENTITY_PERMS TABLE.
        //$subQuerySql = "SELECT {$sAliasPrefix}PX.ENTITY_ID FROM({$subQuerySql}) {$sAliasPrefix}PX ORDER BY {$sAliasPrefix}PX.ENTITY_ID ASC";

        if(isset($arOptions['RAW_QUERY']) && $arOptions['RAW_QUERY'] === true)
        {
            return $subQuerySql;
        }

        $identityCol = 'ID';
        if(is_array($arOptions)
            && isset($arOptions['IDENTITY_COLUMN'])
            && is_string($arOptions['IDENTITY_COLUMN'])
            && $arOptions['IDENTITY_COLUMN'] !== '')
        {
            $identityCol = $arOptions['IDENTITY_COLUMN'];
        }

        $sqlType = isset($arOptions['PERMISSION_SQL_TYPE']) && $arOptions['PERMISSION_SQL_TYPE'] === 'FROM' ? 'FROM' : 'WHERE';
        if($sqlType === 'WHERE')
        {
            return "{$sAliasPrefix}.{$identityCol} IN ({$subQuerySql})";
        }

        return "INNER JOIN ({$subQuerySql}) {$sAliasPrefix}GP ON {$sAliasPrefix}.{$identityCol} = {$sAliasPrefix}GP.ENTITY_ID";
    }

    static public function GetEntityAttr($permEntity, $arIDs)
    {
        if (!is_array($arIDs))
        {
            $arIDs = array($arIDs);
        }

        $effectiveEntityIDs = array();
        foreach ($arIDs as $entityID)
        {
            if($entityID > 0)
            {
                $effectiveEntityIDs[] = $entityID;
            }
        }

        $arResult = array();
        $entityPrefix = strtoupper($permEntity);
        $missedEntityIDs = array();
        foreach($effectiveEntityIDs as $entityID)
        {
            $entityKey = "{$entityPrefix}_{$entityID}";
            if(isset(self::$ENTITY_ATTRS[$entityKey]))
            {
                $arResult[$entityID] = self::$ENTITY_ATTRS[$entityKey];
            }
            else
            {
                $missedEntityIDs[] = $entityID;
            }
        }

        if(empty($missedEntityIDs))
        {
            return $arResult;
        }

        global $DB;
        $sqlIDs = implode(',', $missedEntityIDs);
        $obRes = $DB->Query(
            "SELECT ENTITY_ID, ATTR FROM b_order_entity_perms WHERE ENTITY = '{$DB->ForSql($permEntity)}' AND ENTITY_ID IN('{$sqlIDs}')",
            false,
            'FILE: '.__FILE__.'<br /> LINE: '.__LINE__
        );

        while($arRow = $obRes->Fetch())
        {
            $entityID = $arRow['ENTITY_ID'];
            $entityAttr = $arRow['ATTR'];
            $arResult[$entityID][] = $entityAttr;

            $entityKey = "{$entityPrefix}_{$entityID}";
            if(!isset(self::$ENTITY_ATTRS[$entityKey]))
            {
                self::$ENTITY_ATTRS[$entityKey] = array();
            }
            self::$ENTITY_ATTRS[$entityKey][] = $entityAttr;
        }
        return $arResult;
    }
    static public function UpdateEntityAttr($entityType, $entityID, $arAttrs = array())
    {
        global $DB;
        $entityID = intval($entityID);
        $entityType = strtoupper($entityType);

        if(!is_array($arAttrs))
        {
            $arAttrs = array();
        }

        /*if(!is_array($arOptions))
        {
            $arOptions = array();
        }*/

        $key = "{$entityType}_{$entityID}";
        if(isset(self::$ENTITY_ATTRS[$key]))
        {
            unset(self::$ENTITY_ATTRS[$key]);
        }

        $entityType = $DB->ForSql($entityType);
        $sQuery = "DELETE FROM b_order_entity_perms WHERE ENTITY = '{$entityType}' AND ENTITY_ID = {$entityID}";
        $DB->Query($sQuery, false, $sQuery.'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);

        if (!empty($arAttrs))
        {
            foreach ($arAttrs as $sAttr)
            {
                $sQuery = "INSERT INTO b_order_entity_perms(ENTITY, ENTITY_ID, ATTR) VALUES ('{$entityType}', {$entityID}, '".$DB->ForSql($sAttr)."')";
                $DB->Query($sQuery, false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
            }
        }
    }
}