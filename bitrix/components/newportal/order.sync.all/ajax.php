<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
global $APPLICATION,$USER,$DB;
if(!function_exists('__OrderEndResponse'))
{
    function __OrderEndResponse($result)
    {
        $GLOBALS['APPLICATION']->RestartBuffer();
        Header('Content-Type: application/JSON; charset='.LANG_CHARSET);
        if(!empty($result))
        {
            echo CUtil::PhpToJSObject($result);
        }
        require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
        AddMessage2Log(array('Входные параметры'=>$_REQUEST,'result'=>$result));
        die();
    }
}
$arData=(isset($_REQUEST['DATA']) && is_array(CUtil::JsObjectToPhp($_REQUEST['DATA'])))?
    CUtil::JsObjectToPhp($_REQUEST['DATA']):array();
$bAuto=(isset($arData['MODE']) && $arData['MODE']=='AUTO');
if($bAuto)
    define('LOG_FILENAME',$_SERVER['DOCUMENT_ROOT'].'/bitrix/components/newportal/order.sync.all/log.autosync.txt');
else
    define('LOG_FILENAME',$_SERVER['DOCUMENT_ROOT'].'/bitrix/components/newportal/order.sync.all/log.txt');
AddMessage2Log('Start sync');
if (!CModule::IncludeModule('order'))
{
    __OrderEndResponse(array('error'=>'Модуль order не установлен.'));
}
$arRes=array();
//__OrderEndResponse(array('flag'=>in_array('PHYSICAL',$arData['ENTITY'])));
$DB->StartTransaction();
foreach($arData['ENTITY'] as $entity) {
    if ($entity=='PHYSICAL') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            $id = strval(isset($el['GUID']) ? $el['GUID'] : $el['Код']);
            $names = explode(' ', strval($el['ФИО']));
            $ar1C[$id] = array(
                'ID' => $id,
                'SHARED' => strval($el['ЕстьВ1С']) === true ? "Y" : 'N',
                'LAST_NAME' => $names[0],
                'NAME' => $names[1],
                'SECOND_NAME' => isset($names[2]) ? $names[2] : '',
                'GENDER' => strval($el['Пол']),
                'BDAY' => strval($el['ДатаРождения']),
                'OUT_ADDRESS' => strval($el['АдресзапределамиРФ']),
                'REG_ADDRESS' => strval($el['Адреспопрописке']),
                'LIVE_ADDRESS' => strval($el['Адреспроживания']),
                'EMAIL' => strval($el['Адресдляинформирования']),
                'PHONE' => strval($el['Телефон']),
                'OTHER' => strval($el['Другое']),
                'PROF_EDU' => strval($el['Дипломопрофобразовании']),
                'LVL_EDU' => strval($el['Уровеньобразования']),
                'NATION' => strval($el['Гражданство']),
                'ZIP_CODE' => strval($el['Индекс']),
                'REGION' => strval($el['Кодрегиона']),
                'BPLACE' => strval($el['Месторождения']),
                'SECOND_EDU' => strval($el['Второевысшее']),
                'CERT_MID_EDU' => strval($el['Аттестатосреднемобразовании']),
                'SERIAL_DIP' => strval($el['Сериядиплом']),
                'NOM_DIP' => strval($el['Номердиплом']),
                'WHO_DIP' => strval($el['Кемвыдандиплом']),
                'WHEN_DIP' => strval($el['Когдавыдандиплом']),
                'END_YEAR' => strval($el['Годокончания']),
                'HONORS_DIP' => strval($el['Наличиедипломасотличием']),
                'ORIGINAL_DIP' => strval($el['Предоставленподлинник']),
            );
        }

        if (!isset($COrderPhysical))
            $COrderPhysical = new COrderPhysical(false);
        $res = COrderPhysical::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            $arBX[$el['ID']] = $el;
        }
        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el && $el['SHARED'] == 'Y')
                $arMod[] = $id;
            elseif (!isset($arBX[$id]) && $el['SHARED'] == 'Y')
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]) && $el['SHARED'] == 'Y')
                $arDel[] = $id;
        }
        //__OrderEndResponse(array('1C'=>$ar1C['9171'],'BX'=>$arBX['9171'],'arMod'=>$arMod,'arAdd'=>$arAdd,'arDel'=>$arDel));
        foreach ($arAdd as $id) {
            if (!$COrderPhysical->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderPhysical->LAST_ERROR));
            } else $arRes['ADD']['PHYSICAL'][] = $id;
        }
        foreach ($arMod as $num => $id) {
            if (!$COrderPhysical->Update($id, $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderPhysical->LAST_ERROR));
            } else $arRes['UPDATE']['PHYSICAL'][] = $id;
        }
        foreach ($arDel as $id) {
            if (!$COrderPhysical->Delete($id)) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderPhysical->LAST_ERROR));
            } else $arRes['DELETE']['PHYSICAL'][] = $id;
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    }
    elseif ($entity=='CONTACT') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            $guid = strval(isset($el['GUID']) ? $el['GUID'] : $el['Код']);
            $dStart = strval($el['ДатаРегистрацииСвязи']);
            $id = serialize(array($guid, $dStart));
            $ar1C[$id] = array(
                'ID' => $id,
                'SHARED' => 'Y',
                'GUID' => $guid,
                //'FULL_NAME'=>strval($el['Наименование']),
                'AGENT_ID' => strval($el['Владелец']),
                'START_DATE' => $dStart,
                'END_DATE' => strval($el['ДатаПрекращенияСвязи']),
                'ASSIGNED_ID' => strval($el['Ответственный']),
            );
        }

        if (!isset($COrderPhysical))
            $COrderContact = new COrderContact(false);
        $res = COrderContact::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            $arBX[$el['ID']] = $el;
        }
        foreach ($arBX as $id => $el) {
            foreach ($el as $code => $val) {
                $arBX[$id][$code] = htmlspecialcharsback($arBX[$id][$code]);
            }
            $altID = serialize(array(
                strval($el['GUID']),
                strval($el['START_DATE']),
            ));
            $arIDs[$altID] = $el['ID'];
            $arBX[$id]['ID'] = $altID;
            $arBX[$altID] = $arBX[$id];
            unset($arBX[$id]);
        }
        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el)
                $arMod[] = $id;
            elseif (!isset($arBX[$id]))
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]) && $el['SHARED'] == 'Y')
                $arDel[] = $id;
        }

        foreach ($arAdd as $id) {
            $ar1C[$id]['ID'] = COrderHelper::GetNewID();
            if (!$COrderContact->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderContact->LAST_ERROR));
            } else $arRes['ADD']['CONTACT'][] = $ar1C[$id]['ID'];
        }
        foreach ($arMod as $num => $id) {
            if (!$COrderContact->Update($arIDs[$id], $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderContact->LAST_ERROR));
            } else $arRes['UPDATE']['CONTACT'][] = $arIDs[$id];
        }
        foreach ($arDel as $id) {
            if (!$COrderContact->Delete($arIDs[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderContact->LAST_ERROR));
            } else $arRes['DELETE']['CONTACT'][] = $arIDs[$id];
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    }
    elseif ($entity=='AGENT') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            if(isset($el['Юр.лицо'])) {
                $type=$el['Юр.лицо']===true?'Y':'N';
            } else {
               continue;
            }
            if (!isset($el['GUID']) || $type == 'Y')
                $id = strval($el['Код']);
            else
                $id = strval($el['GUID']);
            $ar1C[$id] = array(
                'ID' => $id,
                //'SHARED'=>'Y',
                'LEGAL' => $type,
                'TITLE' => $type == 'N' ? '' : strval($el['Наименование']),
                'FULL_TITLE' => $type == 'N' ? '' : strval($el['НаименованиеПолное']),
                'INN' => strval($el['ИНН']),
                'KPP' => strval($el['КПП']),
                'CODE_PO' => strval($el['КодПоОКПО']),
                'LEGAL_PHONE' => $type == 'N' ? '' : strval($el['Телефон']),
                'LEGAL_EMAIL' => $type == 'N' ? '' : strval($el['Email']),
                'LEGAL_SHIP_ADDRESS' => strval($el['Адресдоставки']),
                'LEGAL_MAIL_ADDRESS' => strval($el['Почтовыйадрес']),
                'LEGAL_FAX' => strval($el['Факс']),
                'LEGAL_OTHER' => strval($el['Другое']),
                'FACT_ADDRESS' => strval($el['Фактическийадрес']),
                'LEGAL_ADDRESS' => strval($el['Юридическийадрес']),
            );
        }

        if (!isset($COrderAgent))
            $COrderAgent = new COrderAgent(false);
        $res = COrderAgent::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            if ($el['LEGAL'] == 'N') {
                $el['TITLE'] = '';
            }
            $arBX[$el['ID']] = $el;
        }
        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el)
                $arMod[] = $id;
            elseif (!isset($arBX[$id]))
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]) && $el['LEGAL'] == 'Y')
                $arDel[] = $id;
        }

        foreach ($arAdd as $id) {
            if (!$COrderAgent->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderAgent->LAST_ERROR));
            } else $arRes['ADD']['AGENT'][] = $id;
        }
        foreach ($arMod as $num => $id) {
            if (!$COrderAgent->Update($id, $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderAgent->LAST_ERROR));
            } else $arRes['UPDATE']['AGENT'][] = $id;
        }
        foreach ($arDel as $id) {
            if (!$COrderAgent->Delete($id)) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderAgent->LAST_ERROR));
            } else $arRes['DELETE']['AGENT'][] = $id;
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    }
    elseif ($entity=='DIRECTION') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            $id = strval($el['Код']);
            $ar1C[$id] = array(
                'ID' => $id,
                'TITLE' => strval($el['Наименование']),
                'PARENT_ID' => strval($el['Родитель']),
                'MANAGER_ID' => strval($el['Руководитель']),
                'DESCRIPTION' => strval($el['Описание']),
                'PRIVATE' => strval($el['Приватно'])===true?'Y':'N',
            );
        }

        if (!isset($COrderDirection))
            $COrderDirection = new COrderDirection(false);
        $res = COrderDirection::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            $arBX[$el['ID']] = $el;
        }
        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el)
                $arMod[] = $id;
            elseif (!isset($arBX[$id]))
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]) && $id != '000000000')
                $arDel[] = $id;
        }

        foreach ($arAdd as $id) {
            if (!$COrderDirection->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderDirection->LAST_ERROR));
            } else $arRes['ADD']['DIRECTION'][] = $id;
        }
        foreach ($arMod as $num => $id) {
            if (!$COrderDirection->Update($id, $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderDirection->LAST_ERROR));
            } else $arRes['UPDATE']['DIRECTION'][] = $id;
        }
        foreach ($arDel as $id) {
            if (!$COrderDirection->Delete($id)) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderDirection->LAST_ERROR));
            } else $arRes['DELETE']['DIRECTION'][] = $id;
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    }
    elseif ($entity=='NOMEN') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            $arLang = array(
                'Оптовая цена' => 'PRICE_OPT',
                'ФИЗ_Базовая' => 'PRICE_PHYSICAL',
                'ЮР_Базовая' => 'PRICE_LEGAL',
            );
            $prices = array(
                'PRICE_PHYSICAL'=>'',
                'PRICE_LEGAL'=>'',
                'PRICE_OPT'=>'',
            );
            foreach ($el['Цены'] as $np => $prc) {
                $para = each($prc);
                $prices[$arLang[$para['key']]] = strval($para['value']);
            }
            $id = strval($el['Код']);
            $ar1C[$id] = array(
                'ID' => $id,
                'TITLE' => strval($el['Номенклатура']),
                'DIRECTION_ID' => strval($el['УчебноеНаправление']),
                'SEMESTER' => strval($el['Семестр']),
                'PRIVATE' => strval($el['Приватна'])===true?'Y':'N',
                'PRICE' => $prices,
            );
        }

        if (!isset($COrderNomen))
            $COrderNomen = new COrderNomen(false);
        $res = COrderNomen::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            $el['PRICE'] = unserialize($el['PRICE']);
            $arBX[$el['ID']] = $el;
        }
        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el)
                $arMod[] = $id;
            elseif (!isset($arBX[$id]))
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]))
                $arDel[] = $id;
        }

        foreach ($arAdd as $id) {
            $ar1C[$id]['PRICE'] = serialize($ar1C[$id]['PRICE']);
            if (!$COrderNomen->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderNomen->LAST_ERROR));
            } else $arRes['ADD']['NOMEN'][] = $id;
        }
        foreach ($arMod as $num => $id) {
            $ar1C[$id]['PRICE'] = serialize($ar1C[$id]['PRICE']);
            if (!$COrderNomen->Update($id, $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderNomen->LAST_ERROR));
            } else $arRes['UPDATE']['NOMEN'][] = $id;
        }
        foreach ($arDel as $id) {
            if (!$COrderNomen->Delete($id)) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderNomen->LAST_ERROR));
            } else $arRes['DELETE']['NOMEN'][] = $id;
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    }
    elseif ($entity=='COURSE') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            $arLang = array(
                'Экзамен' => 'EXAM_TITLE',
                'ПроходнойБалл' => 'EXAM_MARK',
                'Литература' => 'LITER_ID',
                'Документ' => 'DOC_TITLE',
                'Код' => 'NOMEN_ID',
                'GUID' => 'TEACHER_ID',
            );
            $exams = array();
            foreach ($el['ВступительныеЭкзамены'] as $np => $prc) {
                foreach ($prc as $k => $v)
                    $exams[$np][$arLang[$k]] = strval($v);
            }
            $liters = array();
            foreach ($el['ЛитератураПоКурсу'] as $np => $prc) {
                foreach ($prc as $k => $v)
                    $liters[$np][$arLang[$k]] = strval($v);
            }
            $docs = array();
            foreach ($el['ВыпускныеДокументы'] as $np => $prc) {
                foreach ($prc as $k => $v)
                    $docs[$np][$arLang[$k]] = strval($v);
            }
            $nomens = array();
            foreach ($el['Номенклатура'] as $np => $prc) {
                foreach ($prc as $k => $v)
                    $nomens[$np][$arLang[$k]] = strval($v);
            }
            $teachers = array();
            foreach ($el['Преподаватели'] as $np => $prc) {
                foreach ($prc as $k => $v)
                    $teachers[$np][$arLang[$k]] = strval($v);
            }
            $id = strval($el['Код']);
            $ar1C[$id] = array(
                'ID' => $id,
                'TITLE' => strval($el['Наименование']),
                'ANNOTATION' => strval($el['Аннотация']),
                'DESCRIPTION' => strval($el['Описание']),
                'COURSE_PROG' => strval($el['ПрограммаКурса']),
                'DURATION' => strval($el['Продолжительность']),
                'PREV_COURSE' => strval($el['ПредыдущийКурс']),
                'EXAM' => $exams,
                'LITER' => $liters,
                'DOC' => $docs,
                'NOMEN' => $nomens,
                'TEACHER' => $teachers,
            );
        }

        if (!isset($COrderCourse))
            $COrderCourse = new COrderCourse(false);
        $res = COrderCourse::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            $el = array_merge($el, array(
                'EXAM' => unserialize($el['EXAM']),
                'LITER' => unserialize($el['LITER']),
                'DOC' => unserialize($el['DOC']),
                'NOMEN' => unserialize($el['NOMEN']),
                'TEACHER' => unserialize($el['TEACHER']),
            ));
            $arBX[$el['ID']] = $el;
        }
        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el)
                $arMod[] = $id;
            elseif (!isset($arBX[$id]))
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]))
                $arDel[] = $id;
        }

        foreach ($arAdd as $id) {
            $ar1C[$id] = array_merge($ar1C[$id], array(
                'EXAM' => serialize($ar1C[$id]['EXAM']),
                'LITER' => serialize($ar1C[$id]['LITER']),
                'DOC' => serialize($ar1C[$id]['DOC']),
                'NOMEN' => serialize($ar1C[$id]['NOMEN']),
                'TEACHER' => serialize($ar1C[$id]['TEACHER']),
            ));
            if (!$COrderCourse->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderCourse->LAST_ERROR));
            } else $arRes['ADD']['COURSE'][] = $id;
        }
        foreach ($arMod as $num => $id) {
            $ar1C[$id] = array_merge($ar1C[$id], array(
                'EXAM' => serialize($ar1C[$id]['EXAM']),
                'LITER' => serialize($ar1C[$id]['LITER']),
                'DOC' => serialize($ar1C[$id]['DOC']),
                'NOMEN' => serialize($ar1C[$id]['NOMEN']),
                'TEACHER' => serialize($ar1C[$id]['TEACHER']),
            ));
            if (!$COrderCourse->Update($id, $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderCourse->LAST_ERROR));
            } else $arRes['UPDATE']['COURSE'][] = $id;
        }
        foreach ($arDel as $id) {
            if (!$COrderCourse->Delete($id)) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderCourse->LAST_ERROR));
            } else $arRes['DELETE']['COURSE'][] = $id;
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    }
    elseif ($entity=='GROUP') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            $id = strval($el['Код']);
            $ar1C[$id] = array(
                'ID' => $id,
                'TITLE' => strval($el['Наименование']),
                'NOMEN_ID' => strval($el['Номенклатура']),
                'PRIVATE' => strval($el['Приватна'])===true?'Y':'N',
            );
        }

        if (!isset($COrderGroup))
            $COrderGroup = new COrderGroup(false);

        $res = COrderGroup::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            $arBX[$el['ID']] = $el;
        }
        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el)
                $arMod[] = $id;
            elseif (!isset($arBX[$id]))
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]))
                $arDel[] = $id;
        }

        foreach ($arAdd as $id) {
            if (!$COrderGroup->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderGroup->LAST_ERROR));
            } else $arRes['ADD']['GROUP'][] = $id;
        }
        foreach ($arMod as $num => $id) {
            if (!$COrderGroup->Update($id, $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderGroup->LAST_ERROR));
            } else $arRes['UPDATE']['GROUP'][] = $id;
        }
        foreach ($arDel as $id) {
            if (!$COrderGroup->Delete($id)) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderGroup->LAST_ERROR));
            } else $arRes['DELETE']['GROUP'][] = $id;
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    }
    elseif ($entity=='FORMED_GROUP') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            $id = strval($el['КодГруппы']);
            $ar1C[$id] = array(
                'ID' => $id,
                'GROUP_ID' => strval($el['Код']),
                'DATE_START' => strval($el['ДатаНачалаОбучения']),
                'DATE_END' => strval($el['ДатаОкончанияОбучения']),
                'MAX' => strval($el['МаксимальноеКоличествоСлушателей']),
            );
        }

        if (!isset($COrderFormedGroup))
            $COrderFormedGroup = new COrderFormedGroup(false);
        $res = COrderFormedGroup::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            foreach ($el as $k => $v) {
                $el[$k] = htmlspecialcharsback($v);
            }
            $arBX[$el['ID']] = $el;
        }

        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el)
                $arMod[] = $id;
            elseif (!isset($arBX[$id]))
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]))
                $arDel[] = $id;
        }

        foreach ($arAdd as $id) {
            if (!$COrderFormedGroup->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderFormedGroup->LAST_ERROR));
            } else $arRes['ADD']['FORMED_GROUP'][] = $ar1C[$id]['ID'];
        }
        foreach ($arMod as $num => $id) {
            if (!$COrderFormedGroup->Update($id, $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderFormedGroup->LAST_ERROR));
            } else $arRes['UPDATE']['FORMED_GROUP'][] = $id;
        }
        foreach ($arDel as $id) {
            if (!$COrderFormedGroup->Delete($id)) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderFormedGroup->LAST_ERROR));
            } else $arRes['DELETE']['FORMED_GROUP'][] = $id;
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    }
    elseif ($entity=='REG') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            if(strval($el['Состояние'])=='Переведен')
                continue;
            $arLang = array(
                'Оформлен' => 'ARRANGED',
                'Зачислен' => 'ENROLLED',
                'Отчислен' => 'EXPELLED',
                'Переведен' => 'MOVED',
                'Прервал обучение' => 'INTERRUPTED',
                'Зачислен по заявлению' => 'STATEMENT',
                'Зачислен на условиях' => 'REQUIREMENT',
            );

            $entID = strval($el['КодГруппы']);
            $physicalID = strval($el['Слушатель']);
            $entType = 'FORMED_GROUP';
            $id = serialize(array(
                $entType,
                $entID,
                $physicalID
            ));
            $ar1C[$id] = array(
                'ID' => $id,
                'SHARED' => 'Y',
                'ENTITY_TYPE' => $entType,
                'ENTITY_ID' => $entID,
                'PHYSICAL_ID' => $physicalID,
                'STATUS' => isset($arLang[strval($el['Состояние'])]) ? $arLang[strval($el['Состояние'])] : strval($el['Состояние']),
                'DESCRIPTION' => strval($el['Комментарий']),
            );

            $orderID=strval($el['НомерЗаказа']);
            if($orderID!="") {
                if(!isset($arApp)) {
                    $res=COrderApp::GetListEx(array(),array('STATUS'=>'CONVERTED'));
                    while($resEl=$res->Fetch()) {
                        $arApp[$resEl['STATUS_TEXT']]=$resEl;
                    }
                }
                if(isset($arApp[$orderID]))
                    $ar1C[$id]['APP_ID']=$arApp[$orderID]['ID'];
            }
        }

        if (!isset($COrderReg))
            $COrderReg = new COrderReg(false);
        $res = COrderReg::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            $arBX[$el['ID']] = $el;
        }

        foreach ($arBX as $id => $el) {
            foreach ($el as $code => $val) {
                $arBX[$id][$code] = htmlspecialcharsback($val);
            }
            $altID = serialize(array(
                $el['ENTITY_TYPE'],
                htmlspecialcharsback($el['ENTITY_ID']),
                $el['PHYSICAL_ID'],
            ));
            $arIDs[$altID] = $el['ID'];
            $arBX[$id]['ID'] = $altID;
            $arBX[$altID] = $arBX[$id];
            unset($arBX[$id]);
        }

        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el)
                $arMod[] = $id;
            elseif (!isset($arBX[$id]))
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]) && $el['SHARED'] == 'Y')
                $arDel[] = $id;
        }
        foreach ($arAdd as $id) {
            $ar1C[$id]['ID'] = COrderHelper::GetNewID();
            if (!$COrderReg->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderReg->LAST_ERROR));
            } else $arRes['ADD']['REG'][] = $ar1C[$id]['ID'];
        }
        foreach ($arMod as $num => $id) {
            if (!$COrderReg->Update($arIDs[$id], $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderReg->LAST_ERROR));
            } else $arRes['UPDATE']['REG'][] = $arIDs[$id];
        }
        foreach ($arDel as $id) {
            if (!$COrderReg->Delete($arIDs[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderReg->LAST_ERROR));
            } else $arRes['DELETE']['REG'][] = $arIDs[$id];
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    } elseif ($entity=='STAFF') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            $id = strval($el['GIUD']);
            $ar1C[$id] = array(
                'ID' => $id,
                'TITLE' => strval($el['Наименование']),
                'EMPLOYMENT' => strval($el['ТипЗанятости']),
                'DEPARTMENT' => strval($el['Группа']),
                'LOGIN' => strtolower(strval($el['Login'])),
            );
        }

        if (!isset($COrderStaff))
            $COrderStaff = new COrderStaff(false);
        $res = COrderStaff::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            $arBX[$el['ID']] = $el;
        }
        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el)
                $arMod[] = $id;
            elseif (!isset($arBX[$id]))
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]))
                $arDel[] = $id;
        }

        foreach ($arAdd as $id) {
            if (!$COrderStaff->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderStaff->LAST_ERROR));
            } else $arRes['ADD']['TEACHER'][] = $id;
        }
        foreach ($arMod as $num => $id) {
            if (!$COrderStaff->Update($id, $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderStaff->LAST_ERROR));
            } else $arRes['UPDATE']['TEACHER'][] = $id;
        }
        foreach ($arDel as $id) {
            if (!$COrderStaff->Delete($id)) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderStaff->LAST_ERROR));
            } else $arRes['DELETE']['TEACHER'][] = $id;
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    } elseif ($entity=='TEACHER') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            $id = strval($el['GIUD']);
            $ar1C[$id] = array(
                'ID' => $id,
                'TITLE' => strval($el['ФИОКратко']),
                'EDUCATION' => strval($el['Образование']),
                'EXPERIENCE' => strval($el['ОпытРаботы']),
                'INTERESTS' => strval($el['ПрофессиональныеИнтересы']),
                'DEGREE' => strval($el['УченаяСтепень']),
            );
        }

        if (!isset($COrderTeacher))
            $COrderTeacher = new COrderTeacher(false);
        $res = COrderTeacher::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            $arBX[$el['ID']] = $el;
        }
        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el)
                $arMod[] = $id;
            elseif (!isset($arBX[$id]))
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]))
                $arDel[] = $id;
        }

        foreach ($arAdd as $id) {
            if (!$COrderTeacher->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderTeacher->LAST_ERROR));
            } else $arRes['ADD']['TEACHER'][] = $id;
        }
        foreach ($arMod as $num => $id) {
            if (!$COrderTeacher->Update($id, $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderTeacher->LAST_ERROR));
            } else $arRes['UPDATE']['TEACHER'][] = $id;
        }
        foreach ($arDel as $id) {
            if (!$COrderTeacher->Delete($id)) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderTeacher->LAST_ERROR));
            } else $arRes['DELETE']['TEACHER'][] = $id;
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    } elseif ($entity=='ROOM') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            $id = strval($el['Код']);
            $ar1C[$id] = array(
                'ID' => $id,
                'NUMBER' => strval($el['Номер']),
                'TITLE' => strval($el['Наименование']),
                'TYPE' => strval($el['ВидКласса']),
                'CAPACITY' => strval($el['Вместимость']),
                'DESCRIPTION' => strval($el['Описание']),
                'LOCATION' => strval($el['Местоположение']),
            );
        }

        if (!isset($COrderRoom))
            $COrderRoom = new COrderRoom(false);
        $res = COrderRoom::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            $arBX[$el['ID']] = $el;
        }
        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el)
                $arMod[] = $id;
            elseif (!isset($arBX[$id]))
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]))
                $arDel[] = $id;
        }

        foreach ($arAdd as $id) {
            if (!$COrderRoom->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderRoom->LAST_ERROR));
            } else $arRes['ADD']['ROOM'][] = $id;
        }
        foreach ($arMod as $num => $id) {
            if (!$COrderRoom->Update($id, $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderRoom->LAST_ERROR));
            } else $arRes['UPDATE']['ROOM'][] = $id;
        }
        foreach ($arDel as $id) {
            if (!$COrderRoom->Delete($id)) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderRoom->LAST_ERROR));
            } else $arRes['DELETE']['ROOM'][] = $id;
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    }
    elseif ($entity=='SCHEDULE') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            $id = strval($el['КодЗанятия']);
            $ar1C[$id] = array(
                'ID' => $id,
                'GROUP_ID' => strval($el['УчебнаяГруппа']),
                'DATE_START' => strval(ConvertDateTime($el['ДатаНачалаЗанятия'])),
                'DATE_END' => strval(ConvertDateTime($el['ДатаОкончанияЗанятия'])),
                'ROOM_ID' => strval($el['УчебныйКласс']),
                'COURSE_ID' => strval($el['УчебныйКурс']),
                'TEACHER_ID' => strval($el['Преподаватель']),
                'LESSON_TYPE' => strval($el['ФормаПроведенияЗанятия']),
            );
        }

        if (!isset($COrderSchedule))
            $COrderSchedule = new COrderSchedule(false);
        $res = COrderSchedule::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            $arBX[$el['ID']] = $el;
        }
        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el)
                $arMod[] = $id;
            elseif (!isset($arBX[$id]))
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]))
                $arDel[] = $id;
        }

        foreach ($arAdd as $id) {
            if (!$COrderSchedule->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderSchedule->LAST_ERROR));
            } else $arRes['ADD']['SCHEDULE'][] = $id;
        }
        foreach ($arMod as $num => $id) {
            if (!$COrderSchedule->Update($id, $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderSchedule->LAST_ERROR));
            } else $arRes['UPDATE']['SCHEDULE'][] = $id;
        }
        foreach ($arDel as $id) {
            if (!$COrderSchedule->Delete($id)) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderSchedule->LAST_ERROR));
            } else $arRes['DELETE']['SCHEDULE'][] = $id;
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    }
    elseif ($entity=='MARK') {
        $arGet = COrderConnection::getJSON($entity);
        if(!is_array($arGet) || !is_array(reset($arGet))) continue;
        $ar1C = $arBX = $arAdd = $arMod = $arDel = $arIDs = array();
        foreach ($arGet as $el) {
            $pID = strval($el['Слушатель']);
            $gID = strval($el['УчебнаяГруппа']);
            $dStart = strval(ConvertDateTime($el['ДатаНачалаЗанятия']));
            $type = strval($el['ТипОценки']);
            $id = serialize(array($pID, $gID, $dStart, $type));
            $ar1C[$id] = array(
                'ID' => $id,
                'PHYSICAL_ID' => $pID,
                'GROUP_ID' => $gID,
                'DATE_START' => $dStart,
                'TYPE' => $type,
                'MARK' => strval($el['Оценка']),
                'POINTS' => strval($el['Баллы']),
            );
        }

        if (!isset($COrderMark))
            $COrderMark = new COrderMark(false);
        $res = COrderMark::GetListEx(array(), array(), false, false, array_keys(reset($ar1C)));
        while ($el = $res->Fetch()) {
            $arBX[$el['ID']] = $el;
        }
        foreach ($arBX as $id => $el) {
            foreach ($el as $code => $val) {
                $arBX[$id][$code] = htmlspecialcharsback($arBX[$id][$code]);
            }
            $altID = serialize(array(
                $el['PHYSICAL_ID'],
                $el['GROUP_ID'],
                $el['DATE_START'],
                $el['TYPE'],
            ));
            $arIDs[$altID] = $el['ID'];
            $arBX[$id]['ID'] = $altID;
            $arBX[$altID] = $arBX[$id];
            unset($arBX[$id]);
        }

        foreach ($ar1C as $id => $el) {
            if (isset($arBX[$id]) && $arBX[$id] != $el)
                $arMod[] = $id;
            elseif (!isset($arBX[$id]))
                $arAdd[] = $id;
        }

        foreach ($arBX as $id => $el) {
            if (!isset($ar1C[$id]))
                $arDel[] = $id;
        }
        //__OrderEndResponse(array('1C'=>$ar1C,'BX'=>$arBX,'arMod'=>$arMod,'arAdd'=>$arAdd,'arDel'=>$arDel));

        foreach ($arAdd as $id) {
            $ar1C[$id]['ID'] = COrderHelper::GetNewID();
            if (!$COrderMark->Add($ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderMark->LAST_ERROR));
            } else $arRes['ADD']['MARK'][] = $ar1C[$id]['ID'];
        }
        foreach ($arMod as $num => $id) {
            if (!$COrderMark->Update($arIDs[$id], $ar1C[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderMark->LAST_ERROR));
            } else $arRes['UPDATE']['MARK'][] = $arIDs[$id];
        }
        foreach ($arDel as $id) {
            if (!$COrderMark->Delete($arIDs[$id])) {
                $DB->Rollback();
                __OrderEndResponse(array('error' => $COrderMark->LAST_ERROR));
            } else $arRes['DELETE']['MARK'][] = $arIDs[$id];
        }
        if (count($arData['ENTITY']) > 1) AddMessage2Log($entity);
    }
}

$DB->Commit();
__OrderEndResponse(array('complete'=>$arRes));
?>
