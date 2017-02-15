<?php

IncludeModuleLangFile(__FILE__);

class COrderHelper
{
    public static function GetIdByGuid($guid) {
        $arUser=COrderStaff::GetByID($guid);
        return $arUser['SYS_ID'];
    }

    public static function GetGuidById($id) {
        GLOBAL $USER;
        $rsUser = $USER->GetByID($id);
        $arUser = $rsUser->Fetch();
        return $arUser['UF_GUID'];
    }

    /*public static function GetEnumList($listCode,$iBlockCode=null,$result=null)
    {
        $arRes = Array();
        if(isset($iBlockCode) && trim($iBlockCode)!='') {
            $res = CIBlock::GetList(Array(), Array("CODE"=>$iBlockCode));
            $ar_res = $res->Fetch();
            $property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("CODE"=>$listCode,'IBLOCK_ID'=> $ar_res['ID']));
        }
        else
            $property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("CODE"=>$listCode));
        while($enum_fields = $property_enums->GetNext())
        {
            switch($result) {
                case 'ID': $arRes[$enum_fields['XML_ID']]=(int)$enum_fields['ID']; break;
                case 'VALUE': $arRes[$enum_fields['XML_ID']]=$enum_fields['VALUE']; break;
                default: $arRes[$enum_fields['XML_ID']]=$enum_fields; break;
            }
        }
        return $arRes;
    }*/

    public static function GetEnumList($table,$field,$lang=LANGUAGE_ID)
    {
        $res = CIBlockElement::GetList(Array('sort'=>'asc'), Array("IBLOCK_CODE" => "ENUMS", 'ACTIVE' => 'Y',
            'PROPERTY_TABLE'=>$table,'PROPERTY_FIELD'=>$field));
        $arList=array();
        while ($ob = $res->GetNextElement()) {
            $arProp = $ob->GetProperties();
            if($lang=='ru')
                $arList[$arProp['TITLE']['VALUE']]=$arProp['VALUE_RU']['VALUE'];
            elseif($lang=='en')
                $arList[$arProp['TITLE']['VALUE']]=$arProp['VALUE_EN']['VALUE'];
        }
        return $arList;
    }

    static function prepareEl($v) {
        if(preg_match('/^(0[1-9]|[12][0-9]|3[01]).(0[1-9]|1[012]).(19|20)\d\d( ([0-1]\d|2[0-3])(:[0-5]\d){2})?$/',$v)==1)
            $v=MakeTimeStamp($v, "DD.MM.YYYY HH:MI:SS");
        elseif(preg_match('/^\-?\d+(\.\d{0,})?$/',$v)==1)
            $v=(float)$v;
        else
            $v=strtoupper($v);
        return $v;
    }

    static function cmp($element,$value,$operation) {
        switch($operation) {
            case '':
            case '=':
                return ($element == $value);
                break;
            case '>':
                return ($element > $value);
                break;
            case '<':
                return ($element < $value);
                break;
            case '>=':
                return ($element >= $value);
                break;
            case '<=':
                return ($element <= $value);
                break;
            case '><':
                return ($element >= $value[0] && $element <= $value[1]);
                break;
            case '%':
                return (preg_match('/'.$value.'/',$element)==1);
                break;
            default:
                return false;
        }
    }

    public static function DoFilter($arEl,$arFilter)
    {
        if($arFilter==array()) {
            return true;
        }
        foreach($arEl as &$val) {
            if(is_array($val)) {
                foreach($val as &$subval) {
                    $subval=self::prepareEl($subval);
                }
                unset($subval);
            }
            else {
                $val=self::prepareEl($val);
            }
        }
        unset($val);
        foreach($arFilter as $code => $val) {
                preg_match('/(\!)?(\>[\<\=]?|\<\=?|\=|\%)?(.*)/',$code,$parsing);

                $neg=($parsing[1]=='!');
                $operation=$parsing[2];
                $field=$parsing[3];
                if(!is_array($arEl[$field])) {
                    $arEl[$field]=array($arEl[$field]);
                }

                if($operation!='><' && !is_array($val) || $operation=='><' && !is_array($val[0])) {
                    $val=array($val);
                }
                elseif($operation=='><'  && !is_array($val)) {
                    return false;
                }
                foreach($val as &$subval) {
                    if($operation!='><')
                        $subval=self::prepareEl($subval);
                    else
                        $subval=array(self::prepareEl($subval[0]),self::prepareEl($subval[1]));
                }
                unset($subval);


                $any=false;
                foreach($val as $itemVal) {
                    foreach($arEl[$field] as $itemEl) {
                        $cmp=self::cmp($itemEl,$itemVal,$operation);
                        if($cmp && !$neg || !$cmp && $neg || $itemVal==='' || $itemVal===array('',''))
                            $any=true;
                    }
                }

                if(!$any)
                    return false;


        }
        return true;
    }



    public static function DoSort($arList,$arOrder)
    {
        $arTemp=array();
        foreach($arList as $arEl) {
            $arTemp[$arEl['ID']]=$arEl;
        }

        if($arOrder==array() || count($arList)<=1) {
            return $arTemp;
        }

        $arList=$arTemp;
        foreach($arOrder as $code => $mode) {
            $types[$code]=array(
                "D" => true,
                "F" => true
            );
            foreach ($arList as $arEl) {
                if (preg_match('/^(0[1-9]|[12][0-9]|3[01]).(0[1-9]|1[012]).(19|20)\d\d( ([0-1]\d|2[0-3])(:[0-5]\d){2})?$/', $arEl[$code]) != 1)
                    $types[$code]['D'] = false;

                if (preg_match('/^\-?\d+(\.\d{0,})?$/', $arEl[$code]) != 1)
                    $types[$code]['F'] = false;
            }
        }

        $arOrder=array_reverse($arOrder,true);


        foreach($arOrder as $code => $val) {
            foreach($arTemp as $id=>$item) {
                if($types[$code]['D']) {
                    $arTemp[$id][$code] = MakeTimeStamp($item[$code], "DD.MM.YYYY HH:MI:SS");
                }
                elseif($types[$code]['F']) {
                    $arTemp[$id][$code] = (float)$item[$code];
                }
                else {
                    $arTemp[$id][$code]=strtoupper($item[$code]);
                }
            }
            if(strtolower($val)=='asc') {
                usort($arTemp,function ($a,$b) use ($code) {
                    if ($a[$code] == $b[$code]) {
                        return 0;
                    }
                    return ($a[$code] < $b[$code]) ? -1 : 1;
                });
            }
            elseif(strtolower($val)=='desc') {
                usort($arTemp,function ($a,$b) use ($code) {
                    if ($a[$code] == $b[$code]) {
                        return 0;
                    }
                    return ($a[$code] > $b[$code]) ? -1 : 1;
                });
            }

            

        }
            foreach($arTemp as $arEl) {
                $arReturn[$arEl['ID']]=$arList[$arEl['ID']];
            }
        return $arReturn;
    }

    public static function GetIdByCodeIBlock($code){
        $res = CIBlock::GetList(Array(),Array("CODE"=>$code));
        if($ar_res=$res->Fetch())
            return $ar_res['ID'];
        else
            return false;
    }

    public static function SetAutoSync($schedType='O',$start='',$days=1,$months=1,$prevTask=array())
    {
        $execStart='schtasks /create /tn sync /ru "AVALON\ANDGRA" /rp "DRONCHIK" ';
        $execTest='schtasks /create /tn sync_test /ru "AVALON\ANDGRA" /rp "DRONCHIK" ';
        $exec='';
        switch ($schedType) {
            case 'O':
                $exec.='/sc once ';
                break;
            case 'D':
                $exec.='/sc daily /mo '.$days.' ';
                break;
            case 'W':
                $exec.='/sc weekly /mo '.$months.' ';
                if(count($days)>0) {
                    $d='';
                    foreach ($days as $day)
                        $d .= $day . ',';
                    $d = substr($d, 0, -1);
                    $exec.='/d '.$d.' ';
                }
                break;
        }
        shell_exec('schtasks /delete /tn "sync_test" /f');
        $exec.='/st '.ConvertDateTime($start,'HH:MI:SS').' ';
        $exec.='/sd '.ConvertDateTime($start,'MM/DD/YYYY').' ';
        $exec.='/tr "C:\curl\curl.exe http://localhost/order/api/autosync.php -v --ntlm --negotiate -u ANDGRA:DRONCHIK -O"';
        $return=false;
        shell_exec('schtasks /delete /tn "sync_test" /f');
        $res=shell_exec($execTest.$exec);
        if(substr($res,0,7)=='SUCCESS') {
            shell_exec('schtasks /delete /tn "sync_test" /f');
            if($prevTask==array())
                $prevTask=self::GetAutoSync(); //Получаем значение ENABLED, чтобы выключить новосозданную задачу
            shell_exec('schtasks /delete /tn "sync" /f');
            $res=shell_exec($execStart.$exec);
            $return=(substr($res,0,7)=='SUCCESS');
            if($prevTask['ENABLED']=='N')
                self::DisableAutoSync();
        }
        return $return;
    }

    public static function DisableAutoSync() {
        shell_exec('schtasks /change /tn sync /ru "AVALON\ANDGRA" /rp "DRONCHIK" /disable');
    }
    public static function EnableAutoSync() {
        shell_exec('schtasks /change /tn sync /ru "AVALON\ANDGRA" /rp "DRONCHIK" /enable');
    }

    public static function GetAutoSync()
    {
        $str=shell_exec('schtasks /query /tn "sync" /fo csv /v');
        $search='"Repeat: Stop If Still Running"';
        eval('$arr1=array('.substr($str,0,strpos($str,$search)+strlen($search)).');');
        eval('$arr2=array('.substr($str,strpos($str,$search)+strlen($search)).');');
        $arr=array_combine($arr1,$arr2);
        $sTime=array();
        preg_match_all('([^: ]+)',$arr['Start Time'],$sTime);
        if($sTime[0][3]=='PM')
            $sTime[0][0]+=12;
        $sDate=array();
        preg_match_all('([^:/]+)',$arr['Start Date'],$sDate);
        $start=ConvertTimeStamp(mktime(
            (int)($sTime[0][0]),(int)($sTime[0][1]),(int)($sTime[0][2]),
            (int)($sDate[0][0]),(int)($sDate[0][1]),(int)($sDate[0][2])
        ),'FULL');

        $nArr=array();
        preg_match_all('([^: //]+)',$arr['Next Run Time'],$nArr);
        if($nArr[0][6]=='PM')
            $nArr[0][3]+=12;
        $next=ConvertTimeStamp(mktime(
            (int)($nArr[0][3]),(int)($nArr[0][4]),(int)($nArr[0][5]),
            (int)($nArr[0][0]),(int)($nArr[0][1]),(int)($nArr[0][2])
        ),'FULL');


        $shedType=substr($arr['Schedule Type'],0,1);

        $days=false;
        $months=false;
        switch($shedType) {
            case 'D':
                preg_match('([0-9]+)',$arr['Days'],$days);
                $days=(int)$days[0];
                break;
            case 'W':
                $days=array();
                if($arr['Days']=='Every day of the week')
                    $days=array('MON','TUE','WED','THU','FRI','SAT','SUN');
                else {
                    if(strpos($arr['Days'],'MON')!==false)
                        $days[]='MON';
                    if(strpos($arr['Days'],'TUE')!==false)
                        $days[]='TUE';
                    if(strpos($arr['Days'],'WED')!==false)
                        $days[]='WED';
                    if(strpos($arr['Days'],'THU')!==false)
                        $days[]='THU';
                    if(strpos($arr['Days'],'FRI')!==false)
                        $days[]='FRI';
                    if(strpos($arr['Days'],'SAT')!==false)
                        $days[]='SAT';
                    if(strpos($arr['Days'],'SUN')!==false)
                        $days[]='SUN';
                }
                preg_match('([0-9]+)',$arr['Months'],$months);
                $months=(int)$months[0];
                break;
        }
        $arResult=array(
            'ENABLED'=>($arr['Status']=='Ready'?'Y':'N'),
            'SCHEDULE_TYPE'=>$shedType,
            'START'=>$start,
            'DAYS'=>$days,
            'MONTHS'=>$months,
            'NEXT'=>$next
        );
        return $arResult;
    }

    public static function GetNewID()
    {
        $res = CIBlockElement::GetList(Array(), Array("IBLOCK_CODE" => "CATALOG", 'ACTIVE' => 'Y'));
        $newId=false;
        while ($ob = $res->GetNextElement()) {
            $arProp = $ob->GetProperties();
            if($arProp['NAME']['VALUE']=='free_id') {
                $arFields = $ob->GetFields();
                $sysId = $arFields['ID'];
                $iblockId = $arFields['IBLOCK_ID'];
                $newId = (int)$arProp['VALUE']['VALUE'];
                $arReg = Array(
                    "IBLOCK_ID" => $iblockId,
                    "PROPERTY_VALUES" => array(
                        'NAME' => 'free_id',
                        'VALUE' => $newId + 1,
                    ),
                    "NAME" => "catalog",
                    "ACTIVE" => "Y"
                );
                $el = new CIBlockElement;

                if (!$el->Update($sysId, $arReg))
                    return false;
                break;
            }
        }
        return $newId;
    }

    public static function GetRootDirectionList()
    {
        $res = CIBlockElement::GetList(Array(), Array("IBLOCK_CODE" => "CATALOG", 'ACTIVE' => 'Y'));
        $arRootList=false;
        while ($ob = $res->GetNextElement()) {
            $arProp = $ob->GetProperties();
            if($arProp['NAME']['VALUE']=='root_direction') {
                $arRootList = unserialize(htmlspecialcharsback($arProp['VALUE']['VALUE']));
                break;
            }
        }
        return $arRootList;
    }

    public static function GetGUID($title)
    {
        $arGet=COrderConnection::getJSON("GUID",array('title'=>$title));
        return $arGet['GUID'];
    }

    public static function GetLegalID($title)
    {
        $arGet=COrderConnection::getJSON("LEGAL_ID",array('title'=>$title));
        return $arGet['Код'];
    }

    public static function ChangeAgentInfo($id,$newPhone,$newEmail)
    {
        $arGet=COrderConnection::getJSON("CHANGE_AGENT_INFO",array('id'=>$id,'newPhone'=>$newPhone,'newEmail'=>$newEmail));
        return $arGet['Result']=='ОК';
    }

    public static function DeleteAgent($id)
    {
        $arGet=COrderConnection::getJSON("DELETE_AGENT",array('id'=>$id));
        return $arGet['Result']=='ОК';
    }

    public static function GetCurrentUserID()
    {
        global $USER;
        $rsUser = $USER->GetByID(self::GetCurrentUser()->GetID());
        $arUser = $rsUser->Fetch();
        return $arUser['UF_GUID'];

    }

    /** @return CUser */
    public static function GetCurrentUser()
    {
        return isset($USER) && ((get_class($USER) === 'CUser') || ($USER instanceof CUser))
            ? $USER : new CUser();
    }

    public static function IsAuthorized()
    {
        return self::GetCurrentUser()->IsAuthorized();
    }

    public static function InnerJoin($ar1,$ar2,$on) {
        foreach($ar1 as $id1 => $el1) {
            foreach($ar2 as $id2 => $el2) {
                $good=true;
                foreach($on as $k1 => $k2) {
                    if($el1[$k1]!=$el2[$k2]) $good=false;
                }
                if($good) {
                    $arRes[]=array_merge($el1,$el2);
                }
            }
        }
        if(!is_array($arRes))
            return false;
        return $arRes;
    }

    static function GetShortEntityType($sEntity)
	{
		$sShortEntityType = '';
		switch ($sEntity)
		{
			case 'REG': $sShortEntityType = 'R'; break;
			case 'APP': $sShortEntityType = 'A'; break;
			case 'PHYSICAL': $sShortEntityType = 'PH'; break;
			case 'CONTACT': $sShortEntityType = 'CO'; break;
			case 'AGENT': $sShortEntityType = 'AG'; break;
			case 'DIRECTION': $sShortEntityType = 'D'; break;
			case 'NOMEN': $sShortEntityType = 'N'; break;
			case 'COURSE': $sShortEntityType = 'C'; break;
			case 'GROUP': $sShortEntityType = 'GR'; break;
			case 'FORMED_GROUP': $sShortEntityType = 'FG'; break;
			case 'STAFF':
			default : $sShortEntityType = 'S'; break;
		}
		return $sShortEntityType;
	}

    static function GetLongEntityType($sEntity)
	{
		switch ($sEntity)
		{
			case 'R': $sLongEntityType = 'REG'; break;
			case 'A': $sLongEntityType = 'APP'; break;
			case 'PH': $sLongEntityType = 'PHYSICAL'; break;
			case 'CO': $sLongEntityType = 'CONTACT'; break;
			case 'AG': $sLongEntityType = 'AGENT'; break;
			case 'D': $sLongEntityType = 'DIRECTION'; break;
			case 'N': $sLongEntityType = 'NOMEN'; break;
			case 'C': $sLongEntityType = 'COURSE'; break;
			case 'GR': $sLongEntityType = 'GROUP'; break;
			case 'FG': $sLongEntityType = 'FORMED_GROUP'; break;
			case 'S':
			default : $sLongEntityType = 'STAFF'; break;
		}
		return $sLongEntityType;
	}
    static public function GetProviderName($provider,$id,$arProvideNames) {
        $name='';
        if(isset($arProvideNames[$provider])) {
            $name = $arProvideNames[$provider]['name'];
            if($name=='' && isset($arProvideNames[$provider]['prefixes'])) {
                foreach($arProvideNames[$provider]['prefixes'] as $pref) {
                    if(preg_match('/'.$pref['pattern'].'/',$id)===1) {
                        $name=$pref['prefix'];
                    }
                }
            }
        }

        return $name;
    }

    static public function GetDistance($str1,$str2) {
        $n=strlen($str1);
        $m=strlen($str2);
        $d=array(array(0));
        for($i=1; $i<=$m; $i++) {
            $d[$i][0] = $d[$i-1][0] + 1;
        }
        for($j=1; $j<=$n; $j++)
            $d[0][$j] = $d[0][$j-1] + 1;

        for($i=1; $i<=$m; $i++) {
            for($j=1; $j<=$n; $j++) {
                $d[$i][$j]=min(
                    $d[$i-1][$j]+1,
                    $d[$i][$j-1]+1,
                    $d[$i-1][$j-1]+(int)((substr($str1,$j-1,1)==substr($str2,$i-1,1))?0:1)
                );
            }
        }
        return $d[$m][$n];
    }
}