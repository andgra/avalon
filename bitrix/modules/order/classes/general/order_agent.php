<?php

IncludeModuleLangFile(__FILE__);

class COrderAgent
{
	static public $sUFEntityID = 'ORDER_AGENT';
	public $LAST_ERROR = '';
	public $cPerms = null;
	protected $bCheckPermission = true;
	const TABLE_ALIAS = 'L';
	protected static $TYPE_NAME = 'AGENT';

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
		$physicalJoin = ' LEFT JOIN b_order_physical P ON L.ID = P.ID';
		$contactJoin = ' LEFT JOIN b_order_contact C ON (L.ID = C.AGENT_ID AND (C.END_DATE>NOW() OR C.END_DATE IS NULL OR C.END_DATE="0000-00-00"))';
		$contactJoin .= ' LEFT JOIN b_order_physical CP ON CP.ID=C.GUID';
        $contactJoin .= ' LEFT JOIN (b_user U4 JOIN b_uts_user UF4 ON U4.ID=UF4.VALUE_ID) ON C.ASSIGNED_ID = UF4.UF_GUID';
        $contactJoin .= ' LEFT JOIN b_order_physical UP4 ON UF4.UF_GUID=UP4.ID';

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

			//'SHARED' => array('FIELD' => 'L.SHARED', 'TYPE' => 'string'),
			'LEGAL' => array('FIELD' => 'CASE WHEN L.LEGAL<>"Y" THEN "N" ELSE "Y" END', 'TYPE' => 'string'),
			'TITLE' => array('FIELD' => 'CASE L.LEGAL WHEN "Y" THEN L.TITLE ELSE CONCAT (P.LAST_NAME, " ", P.NAME, " ", P.SECOND_NAME) END', 'TYPE' => 'string', 'FROM' => $physicalJoin),
			'FULL_TITLE' => array('FIELD' => 'L.FULL_TITLE', 'TYPE' => 'string'),
			'PHONE' => array('FIELD' => 'CASE L.LEGAL WHEN "Y" THEN L.LEGAL_PHONE ELSE P.PHONE END', 'TYPE' => 'string','FROM'=>$physicalJoin),
			'EMAIL' => array('FIELD' => 'CASE L.LEGAL WHEN "Y" THEN L.LEGAL_EMAIL ELSE P.EMAIL END', 'TYPE' => 'string','FROM'=>$physicalJoin),
			'CONTACT_ID' => array('FIELD' => 'C.ID', 'TYPE' => 'string','FROM'=>$contactJoin),
			'CONTACT_SHARED' => array('FIELD' => 'C.SHARED', 'TYPE' => 'string','FROM'=>$contactJoin),
			'CONTACT_GUID' => array('FIELD' => 'C.GUID', 'TYPE' => 'string','FROM'=>$contactJoin),
			'CONTACT_FULL_NAME' => array('FIELD' => 'CONCAT (CP.LAST_NAME, " ", CP.NAME, " ", CP.SECOND_NAME)', 'TYPE' => 'string','FROM'=>$contactJoin),
			'CONTACT_PHONE' => array('FIELD' => 'CP.PHONE', 'TYPE' => 'string','FROM'=>$contactJoin),
			'CONTACT_EMAIL' => array('FIELD' => 'CP.EMAIL', 'TYPE' => 'string','FROM'=>$contactJoin),
			'CONTACT_START_DATE' => array('FIELD' => 'C.START_DATE', 'TYPE' => 'date','FROM'=>$contactJoin),
			'CONTACT_END_DATE' => array('FIELD' => 'C.END_DATE', 'TYPE' => 'date','FROM'=>$contactJoin),
			'CONTACT_ASSIGNED_ID' => array('FIELD' => 'C.ASSIGNED_ID', 'TYPE' => 'string','FROM'=>$contactJoin),
			'CONTACT_ASSIGNED_FULL_NAME' => array('FIELD' => 'CONCAT (UP4.LAST_NAME, " ", UP4.NAME, " ", UP4.SECOND_NAME)', 'TYPE' => 'string','FROM'=>$contactJoin),
			'CONTACT_ASSIGNED_EMAIL' => array('FIELD' => 'UP4.EMAIL', 'TYPE' => 'string','FROM'=>$contactJoin),
			'INN' => array('FIELD' => 'L.INN', 'TYPE' => 'string'),
			'KPP' => array('FIELD' => 'L.KPP', 'TYPE' => 'string'),
			'CODE_PO' => array('FIELD' => 'L.CODE_PO', 'TYPE' => 'string'),
			'LEGAL_PHONE' => array('FIELD' => 'L.LEGAL_PHONE', 'TYPE' => 'string'),
			'LEGAL_EMAIL' => array('FIELD' => 'L.LEGAL_EMAIL', 'TYPE' => 'string'),
			'LEGAL_SHIP_ADDRESS' => array('FIELD' => 'L.LEGAL_SHIP_ADDRESS', 'TYPE' => 'string'),
			'LEGAL_MAIL_ADDRESS' => array('FIELD' => 'L.LEGAL_MAIL_ADDRESS', 'TYPE' => 'string'),
			'LEGAL_FAX' => array('FIELD' => 'L.LEGAL_FAX', 'TYPE' => 'string'),
			'LEGAL_OTHER' => array('FIELD' => 'L.LEGAL_OTHER', 'TYPE' => 'string'),
			'FACT_ADDRESS' => array('FIELD' => 'L.FACT_ADDRESS', 'TYPE' => 'string'),
			'LEGAL_ADDRESS' => array('FIELD' => 'L.LEGAL_ADDRESS', 'TYPE' => 'string'),
			'DESCRIPTION' => array('FIELD' => 'L.DESCRIPTION', 'TYPE' => 'string'),
		);
		return $result;
	}

    public static function GetListEx($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array(), $arOptions = array())
	{
		global $DBType;
		$lb = new COrderEntityListBuilder(
			$DBType,
			'b_order_agent',
			'L',
			self::GetFields()
		);
		return $lb->Prepare($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields, $arOptions);
	}

    static function check_props($arEl,$props) {
        $uSuccess=false;

        if(!isset($props['SHARED']))
            $props['SHARED']='N';

        if(($props['SHARED']=='Y' || $props['SHARED']=='N')
            && $props['SHARED']!=$arEl['SHARED']) {
            $uSuccess = true;
            $arEl['SHARED'] = $props['SHARED'];
        }

        if(!isset($props['CONFIRMED']))
            $props['CONFIRMED']='N';

        if(isset($props['CONFIRMED']) && ($props['CONFIRMED']=='Y' || $props['CONFIRMED']=='N')
            && $props['CONFIRMED']!=$arEl['CONFIRMED']) {
            $uSuccess = true;
            $arEl['CONFIRMED'] = $props['CONFIRMED'];
        }

        if(!isset($props['LEGAL']))
            $props['LEGAL']='N';

        if(isset($props['LEGAL']) && ($props['LEGAL']=='Y' || $props['LEGAL']=='N')
            && $props['LEGAL']!=$arEl['LEGAL'] ) {
            $uSuccess = true;
            $arEl['LEGAL']=$props['LEGAL'];
        }

        if($arEl['LEGAL']=='Y') {
            if(isset($props['TITLE']) && $props['TITLE']!=$arEl['TITLE']) {
                $uSuccess = true;
                $arEl['TITLE']=$props['TITLE'];
            }

            if(isset($props['FULL_TITLE']) && $props['FULL_TITLE']!=$arEl['FULL_TITLE']) {
                $uSuccess = true;
                $arEl['FULL_TITLE']=$props['FULL_TITLE'];
            }

            if(isset($props['CODE_PO']) && preg_match('/^\d*$/', $props['CODE_PO']) == 1 && $props['CODE_PO']!=$arEl['CODE_PO']) {
                $uSuccess = true;
                $arEl['CODE_PO']=$props['CODE_PO'];
            }

            if(isset($props['LEGAL_PHONE']) && $props['LEGAL_PHONE']!=$arEl['LEGAL_PHONE']) {
                $uSuccess = true;
                $arEl['LEGAL_PHONE']=$props['LEGAL_PHONE'];
            }

            if(isset($props['LEGAL_EMAIL']) && (check_email(strtolower($props['LEGAL_EMAIL']),true) || $props['LEGAL_EMAIL']=='')
                && $props['LEGAL_EMAIL']!=$arEl['LEGAL_EMAIL']) {
                $uSuccess = true;
                $arEl['LEGAL_EMAIL']=$props['LEGAL_EMAIL'];
            }

            if(isset($props['LEGAL_SHIP_ADDRESS']) && $props['LEGAL_SHIP_ADDRESS']!=$arEl['LEGAL_SHIP_ADDRESS']) {
                $uSuccess = true;
                $arEl['LEGAL_SHIP_ADDRESS']=$props['LEGAL_SHIP_ADDRESS'];
            }

            if(isset($props['LEGAL_MAIL_ADDRESS']) && $props['LEGAL_MAIL_ADDRESS']!=$arEl['LEGAL_MAIL_ADDRESS']) {
                $uSuccess = true;
                $arEl['LEGAL_MAIL_ADDRESS']=$props['LEGAL_MAIL_ADDRESS'];
            }

            if(isset($props['LEGAL_FAX']) && $props['LEGAL_FAX']!=$arEl['LEGAL_FAX']) {
                $uSuccess = true;
                $arEl['LEGAL_FAX']=$props['LEGAL_FAX'];
            }

            if(isset($props['LEGAL_OTHER']) && $props['LEGAL_OTHER']!=$arEl['LEGAL_OTHER']) {
                $uSuccess = true;
                $arEl['LEGAL_OTHER']=$props['LEGAL_OTHER'];
            }

            if(isset($props['FACT_ADDRESS']) && $props['FACT_ADDRESS']!=$arEl['FACT_ADDRESS']) {
                $uSuccess = true;
                $arEl['FACT_ADDRESS']=$props['FACT_ADDRESS'];
            }

            if(isset($props['LEGAL_ADDRESS']) && $props['LEGAL_ADDRESS']!=$arEl['LEGAL_ADDRESS']) {
                $uSuccess = true;
                $arEl['LEGAL_ADDRESS']=$props['LEGAL_ADDRESS'];
            }
        }


        if(isset($props['INN']) && preg_match('/^\d*$/', $props['INN']) == 1 && $props['INN']!=$arEl['INN']) {
            $uSuccess = true;
            $arEl['INN']=$props['INN'];
        }

        if(isset($props['KPP']) && preg_match('/^\d*$/', $props['KPP']) == 1 && $props['KPP']!=$arEl['KPP']) {
            $uSuccess = true;
            $arEl['KPP']=$props['KPP'];
        }



        if(isset($props['DESCRIPTION']) && $props['DESCRIPTION']!=$arEl['DESCRIPTION']) {
            $uSuccess = true;
            $arEl['DESCRIPTION']=$props['DESCRIPTION'];
        }

        if(!$uSuccess)
            return false;
        else
            return Array(
                "IBLOCK_ID"      => COrderHelper::GetIdByCodeIBlock('AGENT_HIST'),
                "PROPERTY_VALUES"=> $arEl,
                "NAME"           => "agent_hist",
                "ACTIVE"         => "Y"
            );
    }

    public static function CompareFields($arFieldsOrig, $arFieldsModif)
    {
        $arMsg = Array();

        /*if (isset($arFieldsOrig['SHARED']) && isset($arFieldsModif['SHARED'])
            && $arFieldsOrig['SHARED'] != $arFieldsModif['SHARED'])
            $arMsg[] = Array(
                'ENTITY_FIELD' => 'SHARED',
                'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_SHARED'),
                'VALUE_OLD' => $arFieldsOrig['SHARED'],
                'VALUE_NEW' => $arFieldsModif['SHARED'],
            );*/

        if (isset($arFieldsOrig['INN']) && isset($arFieldsModif['INN'])
            && $arFieldsOrig['INN'] != $arFieldsModif['INN'])
            $arMsg[] = Array(
                'ENTITY_FIELD' => 'INN',
                'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_INN'),
                'VALUE_OLD' => $arFieldsOrig['INN'],
                'VALUE_NEW' => $arFieldsModif['INN'],
            );

        if (isset($arFieldsOrig['KPP']) && isset($arFieldsModif['KPP'])
            && $arFieldsOrig['KPP'] != $arFieldsModif['KPP'])
            $arMsg[] = Array(
                'ENTITY_FIELD' => 'KPP',
                'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_KPP'),
                'VALUE_OLD' => $arFieldsOrig['KPP'],
                'VALUE_NEW' => $arFieldsModif['KPP'],
            );

        if (isset($arFieldsOrig['DESCRIPTION']) && isset($arFieldsModif['DESCRIPTION'])
            && $arFieldsOrig['DESCRIPTION'] != $arFieldsModif['DESCRIPTION'])
            $arMsg[] = Array(
                'ENTITY_FIELD' => 'DESCRIPTION',
                'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_DESCRIPTION'),
                'VALUE_OLD' => $arFieldsOrig['DESCRIPTION'],
                'VALUE_NEW' => $arFieldsModif['DESCRIPTION'],
            );

        $bLegal=$arFieldsOrig['LEGAL'] == 'Y';
        if (isset($arFieldsOrig['LEGAL']) && isset($arFieldsModif['LEGAL'])
            && $arFieldsOrig['LEGAL'] != $arFieldsModif['LEGAL']
            && ($arFieldsModif['LEGAL'] == 'Y' || $arFieldsModif['LEGAL'] == 'N')) {
            $bLegal=$arFieldsModif['LEGAL'] == 'Y';
            $arMsg[] = Array(
                'ENTITY_FIELD' => 'LEGAL',
                'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_LEGAL'),
                'VALUE_OLD' => $arFieldsOrig['LEGAL'],
                'VALUE_NEW' => $arFieldsModif['LEGAL'],
            );
        }

        if($bLegal) {
            if (isset($arFieldsOrig['TITLE']) && isset($arFieldsModif['TITLE'])
                && $arFieldsOrig['TITLE'] != $arFieldsModif['TITLE']
            )
                $arMsg[] = Array(
                    'ENTITY_FIELD' => 'TITLE',
                    'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_TITLE'),
                    'VALUE_OLD' => $arFieldsOrig['TITLE'],
                    'VALUE_NEW' => $arFieldsModif['TITLE'],
                );

            if (isset($arFieldsOrig['FULL_TITLE']) && isset($arFieldsModif['FULL_TITLE'])
                && $arFieldsOrig['FULL_TITLE'] != $arFieldsModif['FULL_TITLE'])
                $arMsg[] = Array(
                    'ENTITY_FIELD' => 'FULL_TITLE',
                    'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_FULL_TITLE'),
                    'VALUE_OLD' => $arFieldsOrig['FULL_TITLE'],
                    'VALUE_NEW' => $arFieldsModif['FULL_TITLE'],
                );

            if (isset($arFieldsOrig['CODE_PO']) && isset($arFieldsModif['CODE_PO'])
                && $arFieldsOrig['CODE_PO'] != $arFieldsModif['CODE_PO'])
                $arMsg[] = Array(
                    'ENTITY_FIELD' => 'CODE_PO',
                    'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_CODE_PO'),
                    'VALUE_OLD' => $arFieldsOrig['CODE_PO'],
                    'VALUE_NEW' => $arFieldsModif['CODE_PO'],
                );

            if (isset($arFieldsOrig['LEGAL_PHONE']) && isset($arFieldsModif['LEGAL_PHONE'])
                && $arFieldsOrig['LEGAL_PHONE'] != $arFieldsModif['LEGAL_PHONE'])
                $arMsg[] = Array(
                    'ENTITY_FIELD' => 'LEGAL_PHONE',
                    'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_LEGAL_PHONE'),
                    'VALUE_OLD' => $arFieldsOrig['LEGAL_PHONE'],
                    'VALUE_NEW' => $arFieldsModif['LEGAL_PHONE'],
                );

            if (isset($arFieldsOrig['LEGAL_EMAIL']) && isset($arFieldsModif['LEGAL_EMAIL'])
                && $arFieldsOrig['LEGAL_EMAIL'] != $arFieldsModif['LEGAL_EMAIL'])
                $arMsg[] = Array(
                    'ENTITY_FIELD' => 'LEGAL_EMAIL',
                    'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_LEGAL_EMAIL'),
                    'VALUE_OLD' => $arFieldsOrig['LEGAL_EMAIL'],
                    'VALUE_NEW' => $arFieldsModif['LEGAL_EMAIL'],
                );

            if (isset($arFieldsOrig['LEGAL_SHIP_ADDRESS']) && isset($arFieldsModif['LEGAL_SHIP_ADDRESS'])
                && $arFieldsOrig['LEGAL_SHIP_ADDRESS'] != $arFieldsModif['LEGAL_SHIP_ADDRESS'])
                $arMsg[] = Array(
                    'ENTITY_FIELD' => 'LEGAL_SHIP_ADDRESS',
                    'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_LEGAL_SHIP_ADDRESS'),
                    'VALUE_OLD' => $arFieldsOrig['LEGAL_SHIP_ADDRESS'],
                    'VALUE_NEW' => $arFieldsModif['LEGAL_SHIP_ADDRESS'],
                );

            if (isset($arFieldsOrig['LEGAL_MAIL_ADDRESS']) && isset($arFieldsModif['LEGAL_MAIL_ADDRESS'])
                && $arFieldsOrig['LEGAL_MAIL_ADDRESS'] != $arFieldsModif['LEGAL_MAIL_ADDRESS'])
                $arMsg[] = Array(
                    'ENTITY_FIELD' => 'LEGAL_MAIL_ADDRESS',
                    'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_LEGAL_MAIL_ADDRESS'),
                    'VALUE_OLD' => $arFieldsOrig['LEGAL_MAIL_ADDRESS'],
                    'VALUE_NEW' => $arFieldsModif['LEGAL_MAIL_ADDRESS'],
                );

            if (isset($arFieldsOrig['LEGAL_FAX']) && isset($arFieldsModif['LEGAL_FAX'])
                && $arFieldsOrig['LEGAL_FAX'] != $arFieldsModif['LEGAL_FAX'])
                $arMsg[] = Array(
                    'ENTITY_FIELD' => 'LEGAL_FAX',
                    'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_LEGAL_FAX'),
                    'VALUE_OLD' => $arFieldsOrig['LEGAL_FAX'],
                    'VALUE_NEW' => $arFieldsModif['LEGAL_FAX'],
                );

            if (isset($arFieldsOrig['LEGAL_OTHER']) && isset($arFieldsModif['LEGAL_OTHER'])
                && $arFieldsOrig['LEGAL_OTHER'] != $arFieldsModif['LEGAL_OTHER'])
                $arMsg[] = Array(
                    'ENTITY_FIELD' => 'LEGAL_OTHER',
                    'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_LEGAL_OTHER'),
                    'VALUE_OLD' => $arFieldsOrig['LEGAL_OTHER'],
                    'VALUE_NEW' => $arFieldsModif['LEGAL_OTHER'],
                );

            if (isset($arFieldsOrig['FACT_ADDRESS']) && isset($arFieldsModif['FACT_ADDRESS'])
                && $arFieldsOrig['FACT_ADDRESS'] != $arFieldsModif['FACT_ADDRESS'])
                $arMsg[] = Array(
                    'ENTITY_FIELD' => 'FACT_ADDRESS',
                    'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_FACT_ADDRESS'),
                    'VALUE_OLD' => $arFieldsOrig['FACT_ADDRESS'],
                    'VALUE_NEW' => $arFieldsModif['FACT_ADDRESS'],
                );

            if (isset($arFieldsOrig['LEGAL_ADDRESS']) && isset($arFieldsModif['LEGAL_ADDRESS'])
                && $arFieldsOrig['LEGAL_ADDRESS'] != $arFieldsModif['LEGAL_ADDRESS'])
                $arMsg[] = Array(
                    'ENTITY_FIELD' => 'LEGAL_ADDRESS',
                    'EVENT_NAME' => GetMessage('ORDER_FIELD_COMPARE_LEGAL_ADDRESS'),
                    'VALUE_OLD' => $arFieldsOrig['LEGAL_ADDRESS'],
                    'VALUE_NEW' => $arFieldsModif['LEGAL_ADDRESS'],
                );
        }
		return $arMsg;
	}

	public function Update($ID, array &$arFields, $bCompare = true,  $options = array()) {
        global $DB;

        $DB->StartTransaction();
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



        if (!isset($arFields['LEGAL']))
            $arFields['LEGAL'] = $arRow['LEGAL'];

		if ($arFields['LEGAL']=='N') {
			unset($arFields['TITLE']);
			unset($arFields['FULL_TITLE']);
		}
        elseif($arFields['LEGAL']=='Y') {
            if (!isset($arFields['CONTACT_GUID']))
                $arFields['CONTACT_GUID'] = $arRow['CONTACT_GUID'];

            if (!isset($arFields['CONTACT_ID']))
                $arFields['CONTACT_ID'] = $arRow['CONTACT_ID'];

            if (!isset($arFields['CONTACT_START_DATE']))
                $arFields['CONTACT_START_DATE'] = $arRow['CONTACT_START_DATE'];

            if (!isset($arFields['TITLE']))
                $arFields['TITLE'] = $arRow['TITLE'];

            if (!isset($arFields['LEGAL_PHONE']))
                $arFields['LEGAL_PHONE'] = $arRow['LEGAL_PHONE'];

            if (!isset($arFields['LEGAL_MAIL']))
                $arFields['LEGAL_MAIL'] = $arRow['LEGAL_MAIL'];

        }

        $arFields['ID']=$ID;

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


			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderAgentUpdate');
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
						$this->LAST_ERROR = GetMessage('ORDER_AGENT_UPDATE_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
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
					$arEvent['ENTITY_TYPE'] = 'AGENT';
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
			$sUpdate = $DB->PrepareUpdate('b_order_agent', $arFields);
			if (strlen($sUpdate) > 0)
			{
				$DB->Query("UPDATE b_order_agent SET {$sUpdate} WHERE ID = '{$ID}'", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
                $bResult = true;

                $arFields['CONTACT_ID']=$arRow['CONTACT_ID'];
                if($arFields['LEGAL']=='Y' && ($arFields['CONTACT_GUID']!=$arRow['CONTACT_GUID']
                        || $arFields['CONTACT_START_DATE']!=$arRow['CONTACT_START_DATE'])) {
                    $COrderContact = new COrderContact();
                    if(isset($arRow['CONTACT_ID']) && $arRow['CONTACT_ID']!=''
                        && $arRow['CONTACT_SHARED']=='N') {
                        $contact=COrderContact::GetByID($arFields['CONTACT_ID']);
                        $arContact = array('END_DATE' => date('d.m.Y'));
                        if (!$COrderContact->Update($contact['ID'], $arContact)) {
                            $this->LAST_ERROR=$COrderContact->LAST_ERROR;
                            $bResult = false;
                        }
                    }
                    if($bResult) {
                        $arFields['CONTACT_ID']=COrderHelper::GetNewID();
                        $arContact = array(
                            'ID'=>$arFields['CONTACT_ID'],
                            'SHARED'=>'N',
                            'GUID'=>$arFields['CONTACT_GUID'],
                            'AGENT_ID' => $ID,
                            'START_DATE'=>date('d.m.Y'),
                            'ASSIGNED_ID'=>COrderHelper::GetCurrentUserID()
                        );
                        if (!$COrderContact->Add($arContact)) {
                            $this->LAST_ERROR=$COrderContact->LAST_ERROR;
                            $bResult = false;
                        }
                    }
                }

                if($bResult && $arFields['LEGAL'] == 'Y')
                    COrderHelper::ChangeAgentInfo($ID,$arFields['LEGAL_PHONE'],$arFields['LEGAL_EMAIL']);

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
                $DB->Commit();
				$afterEvents = GetModuleEvents('order', 'OnAfterOrderAgentUpdate');
				while ($arEvent = $afterEvents->Fetch())
					ExecuteModuleEventEx($arEvent, array(&$arFields));
			} else {
                $DB->Rollback();
            }

		}
		return $bResult;
    }

    public function Add(array &$arFields, $options = array())
	{
		global $DB;

        $DB->StartTransaction();
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

		if (isset($arFields['LEGAL']) && $arFields['LEGAL']=='N') {
			unset($arFields['TITLE']);
			unset($arFields['FULL_TITLE']);
		}

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
				$sEntityPerm = $userPerms->GetPermType('AGENT', 'ADD');
				if ($sEntityPerm == BX_ORDER_PERM_NONE)
				{
					$this->LAST_ERROR = GetMessage('ORDER_PERMISSION_DENIED');
					$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					return false;
				}
			}


			$beforeEvents = GetModuleEvents('order', 'OnBeforeOrderAgentAdd');
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
						$this->LAST_ERROR = GetMessage('ORDER_AGENT_CREATION_CANCELED', array('#NAME#' => $arEvent['TO_NAME']));
						$arFields['RESULT_MESSAGE'] = &$this->LAST_ERROR;
					}
					return false;
				}
			}

			$DB->Add('b_order_agent', $arFields, array(), 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
            if($arFields['LEGAL']=='Y' && isset($arFields['CONTACT_GUID']) && $arFields['CONTACT_GUID']!='') {
                $COrderContact = new COrderContact();
                $arContact = array(
                    'ID'=>COrderHelper::GetNewID(),
                    'SHARED'=>'N',
                    'GUID'=>$arFields['CONTACT_GUID'],
                    'AGENT_ID' => $arFields['ID'],
                    'START_DATE'=>date('d.m.Y'),
                    'ASSIGNED_ID'=>COrderHelper::GetCurrentUserID()
                );
                if (!$COrderContact->Add($arContact)) {
                    $this->LAST_ERROR=$COrderContact->LAST_ERROR;
                    $DB->Rollback();
                    return false;
                }

            }

            if($arFields['LEGAL'] == 'Y')
                COrderHelper::ChangeAgentInfo($arFields['ID'],$arFields['LEGAL_PHONE'],$arFields['LEGAL_EMAIL']);

			/*if($bUpdateSearch)
			{
				$arFilterTmp = Array('ID' => $ID);
				if (!$this->bCheckPermission)
					$arFilterTmp["CHECK_PERMISSIONS"] = "N";
				CCrmSearch::UpdateSearch($arFilterTmp, 'DEAL', true);
			}*/

			$result = $arFields['ID'];

            COrderEvent::RegisterAddEvent(self::$TYPE_NAME, $result, $iUserId);


			$afterEvents = GetModuleEvents('order', 'OnAfterOrderAgentAdd');
			while ($arEvent = $afterEvents->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array(&$arFields));
			}

			if(isset($arFields['ORIGIN_ID']) && $arFields['ORIGIN_ID'] !== '')
			{
				$afterEvents = GetModuleEvents('order', 'OnAfterExternalOrderAgentAdd');
				while ($arEvent = $afterEvents->Fetch())
				{
					ExecuteModuleEventEx($arEvent, array(&$arFields));
				}
			}
		}
        $DB->Commit();
		return $result;
	}

	public function CheckFields(&$arFields)
	{
		$this->LAST_ERROR = '';

		/*if (!empty($arFields['SHARED']) && ($arFields['SHARED']!='Y' && $arFields['SHARED']!='N'))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_SHARED')))."<br />\n";*/

		if (!empty($arFields['LEGAL']) && ($arFields['LEGAL']!='Y' && $arFields['LEGAL']!='N'))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_LEGAL')))."<br />\n";
        elseif($arFields['LEGAL']=='Y' && (!isset($arFields['TITLE']) || $arFields['TITLE']==''))
            $this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_TITLE')))."<br />\n";

		/*if (!empty($arFields['LEGAL_EMAIL']) && !filter_var($arFields['LEGAL_EMAIL'],FILTER_VALIDATE_EMAIL))
			$this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_LEGAL_EMAIL')))."<br />\n";*/

        if(!isset($arFields['ID']) || $arFields['ID']=='')
            $this->LAST_ERROR .= GetMessage('ORDER_ERROR_FIELD_INCORRECT', array('%FIELD_NAME%' => GetMessage('ORDER_FIELD_ID')))."<br />\n";


		if (strlen($this->LAST_ERROR) > 0)
			return false;

		return true;
	}

    public function Delete($ID)
	{
		global $DB, $APPLICATION;

        $DB->StartTransaction();
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
			$sEntityPerm = $this->cPerms->GetPermType('AGENT', 'DELETE');
			if ($sEntityPerm == BX_ORDER_PERM_NONE)
				return false;
			/*else if ($sEntityPerm == BX_CRM_PERM_SELF)
				$sWherePerm = " AND ASSIGNED_BY_ID = {$iUserId}";
			else if ($sEntityPerm == BX_CRM_PERM_OPEN)
				$sWherePerm = " AND (OPENED = 'Y' OR ASSIGNED_BY_ID = {$iUserId})";*/
		}

		$APPLICATION->ResetException();
		$events = GetModuleEvents('order', 'OnBeforeOrderAgentDelete');
		while ($arEvent = $events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if ($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}



		$dbRes = $DB->Query("DELETE FROM b_order_agent WHERE ID = '{$ID}'{$sWherePerm}", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
		if (is_object($dbRes) && $dbRes->AffectedRowsCount() > 0)
		{
            if($arFields['LEGAL']=='Y' && isset($arFields['CONTACT_ID']) &&
                $arFields['CONTACT_ID'] != '' && $arFields['CONTACT_SHARED'] == 'N') {
                $COrderContact = new COrderContact();
                $contact = COrderContact::GetByID($arFields['CONTACT_ID']);
                $arContact = array('END_DATE' => date('d.m.Y'));
                if (!$COrderContact->Update($contact['ID'], $arContact)) {
                    $this->LAST_ERROR = $COrderContact->LAST_ERROR;
                    $DB->Rollback();
                    return false;
                }
            }
            COrderHelper::DeleteAgent($ID);
			/*CCrmSearch::DeleteSearch('DEAL', $ID);

			$DB->Query("DELETE FROM b_crm_entity_perms WHERE ENTITY='DEAL' AND ENTITY_ID = $ID", false, 'FILE: '.__FILE__.'<br /> LINE: '.__LINE__);
			$GLOBALS['USER_FIELD_MANAGER']->Delete(self::$sUFEntityID, $ID);
			$CCrmFieldMulti = new CCrmFieldMulti();
			$CCrmFieldMulti->DeleteByElement('DEAL', $ID);*/
			$COrderEvent = new COrderEvent();
			$COrderEvent->DeleteByElement('AGENT', $ID);




			//if(Bitrix\Crm\Settings\HistorySettings::getCurrent()->isDealDeletionEventEnabled())
			COrderEvent::RegisterDeleteEvent(BX_ORDER_AGENT, $ID, $iUserId, array('FIELDS' => $arFields));

			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				$GLOBALS["CACHE_MANAGER"]->ClearByTag("order_entity_name_".BX_ORDER_AGENT_ID."_".$ID);
			}

			$afterEvents = GetModuleEvents('order', 'OnAfterOrderAgentDelete');
			while ($arEvent = $afterEvents->Fetch())
			{
				ExecuteModuleEventEx($arEvent, array($ID));
			}
		}
        $DB->Commit();
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