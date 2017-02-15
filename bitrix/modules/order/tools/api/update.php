<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
define('LOG_FILENAME',$_SERVER['DOCUMENT_ROOT'].'/order/api/log.txt');
global $APPLICATION,$USER,$DB;
if(!function_exists('__OrderEndResponse'))
{
    function __OrderEndResponse($result)
    {
        $GLOBALS['APPLICATION']->RestartBuffer();
        Header('Content-Type: application/JSON; charset='.LANG_CHARSET);
        AddMessage2Log(array('Входные параметры'=>$_REQUEST));
        if(!empty($result))
        {
            echo normJsonStr(json_encode($result));
        }
        require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
        die();
    }
}
if (!CModule::IncludeModule('order'))
{
    __OrderEndResponse(array('Ошибка'=>'Модуль order не установлен.'));
}
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/order/public/api/update.php");
$arData=$_REQUEST['Данные'];
//__OrderEndResponse(array('arData'=>$arData,'$_REQUEST'=>$_REQUEST));
$user=$USER->GetByLogin($_REQUEST['Инициатор'])->Fetch();
$USER->Authorize($user['ID']);
//$iUserId=COrderHelper::GetCurrentUserID();
$arRes=array();
if(isset($arData) && is_array($arGet=CUtil::JsObjectToPhp($arData))) {
    $DB->StartTransaction();
    foreach($arGet as $el) {
        if($el['ТипОбъекта']=='ФизЛицо') {
            $id=strval(isset($el['GUID'])?$el['GUID']:$el['Код']);
            $names=explode(' ',strval($el['ФИО']));
            $newEl=array(
                'ID'=>$id,
                'SHARED'=>$el['ЕстьВ1С']===true?"Y":'N',
                'LAST_NAME'=>isset($names[0])?$names[0]:'',
                'NAME'=>isset($names[1])?$names[1]:'',
                'SECOND_NAME'=>isset($names[2])?$names[2]:'',
                'GENDER'=>strval($el['Пол']),
                'BDAY'=>strval($el['ДатаРождения']),
                'OUT_ADDRESS'=>strval($el['АдресзапределамиРФ']),
                'REG_ADDRESS'=>strval($el['Адреспопрописке']),
                'LIVE_ADDRESS'=>strval($el['Адреспроживания']),
                'EMAIL'=>strval($el['Адресдляинформирования']),
                'PHONE'=>strval($el['Телефон']),
                'OTHER'=>strval($el['Другое']),
                'PROF_EDU'=>strval($el['Дипломопрофобразовании']),
                'LVL_EDU'=>strval($el['Уровеньобразования']),
                'NATION'=>strval($el['Гражданство']),
                'INDEX'=>strval($el['Индекс']),
                'REGION'=>strval($el['Кодрегиона']),
                'BPLACE'=>strval($el['Месторождения']),
                'SECOND_EDU'=>strval($el['Второевысшее']),
                'CERT_MID_EDU'=>strval($el['Аттестатосреднемобразовании']),
                'SERIAL_DIP'=>strval($el['Сериядиплом']),
                'NOM_DIP'=>strval($el['Номердиплом']),
                'WHO_DIP'=>strval($el['Кемвыдандиплом']),
                'WHEN_DIP'=>strval($el['Когдавыдандиплом']),
                'END_YEAR'=>strval($el['Годокончания']),
                'HONORS_DIP'=>strval($el['Наличиедипломасотличием']),
                'ORIGINAL_DIP'=>strval($el['Предоставленподлинник']),
            );

            if(!isset($COrderPhysical))
                $COrderPhysical=new COrderPhysical(false);
            $bxEl=COrderPhysical::GetListEx(array(),array('ID'=>$id),false,false,array_keys($newEl))->Fetch();
            if($el['Удален']===true) {
                if(!$COrderPhysical->Delete($id)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка удаления физ. лица # '.$id,
                        'Описание'=>$COrderPhysical->LAST_ERROR
                    ));
                }else
                    $arRes['Удалено']['Физ. лица'][]=$id;
            }elseif($bxEl && $bxEl!=$newEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                if(!$COrderPhysical->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения физ. лица # '.$newEl['ID'],
                        'Описание'=>$COrderPhysical->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Физ. лица'][]=$newEl['ID'];
            }elseif(!$bxEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                //$newEl['CREATED_BY_ID']=$iUserId;
                if(!$COrderPhysical->Add($newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат' => 'Ошибка создания физ. лица # ' . $newEl['ID'],
                        'Описание' => $COrderPhysical->LAST_ERROR
                    ));
                }else
                    $arRes['Добавлено']['Физ. лица'][]=$newEl['ID'];
            }
        } elseif($el['ТипОбъекта']=='КонтактноеЛицо') {
            $guid=strval(isset($el['GUID'])?$el['GUID']:$el['Код']);
            $aID=strval($el['Владелец']);
            $dStart=strval($el['ДатаРегистрацииСвязи']);
            $id='';
            $newEl=array(
                'ID'=>$id,
                'SHARED'=>'Y',
                'GUID'=>$guid,
                //'FULL_NAME'=>strval($el['Наименование']),
                'AGENT_ID'=>$aID,
                'START_DATE'=>$dStart,
                'END_DATE'=>strval($el['ДатаПрекращенияСвязи']),
                'ASSIGNED_ID'=>strval($el['Ответственный']),
            );
            if(!isset($COrderContact))
                $COrderContact=new COrderContact(false);
            $bxEl=COrderContact::GetListEx(array(),
                array('GUID'=>$guid,'START_DATE'=>$dStart),false,false,array_keys($newEl))->Fetch();

            foreach($bxEl as $code=> $val) {
                $bxEl[$code]=htmlspecialcharsback($bxEl[$code]);
            }
            $newEl['ID']=$bxEl['ID'];

            if($el['Удален']===true) {
                if (!$COrderContact->Delete($bxEl['ID'])) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат' => 'Ошибка удаления контакта с GUID ' . $guid .
                            ' и датой регистрации связи ' . $dStart,
                        'Описание' => $COrderContact->LAST_ERROR
                    ));
                }else
                    $arRes['Удалено']['Контакты'][] = $bxEl['ID'];
            }elseif($bxEl && $bxEl!=$newEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                if(!$COrderContact->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения контакта с GUID '.$guid.
                            ' и датой регистрации связи '.$dStart,
                        'Описание'=>$COrderContact->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Контакты'][]=$newEl['ID'];
            } elseif(!$bxEl) {
                $newEl['ID']=COrderHelper::GetNewID();
                //$newEl['MODIFY_BY_ID']=$iUserId;
                //$newEl['CREATED_BY_ID']=$iUserId;
                if(!$COrderContact->Add($newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка создания контакта с GUID '.$guid.
                            ' и датой регистрации связи '.$dStart,
                        'Описание'=>$COrderContact->LAST_ERROR
                    ));
                }else
                    $arRes['Добавлено']['Контакты'][]=$newEl['ID'];
            }

        } elseif($el['ТипОбъекта']=='Контрагент') {
            if(isset($el['Юр.лицо'])) {
                $type=$el['Юр.лицо']===true?'Y':'N';
            } else {
                __OrderEndResponse(array(
                    'Результат'=>'Ошибка контрагента',
                    'Описание'=>'Поле "Юр.лицо" обязательно'
                ));
            }
            if(!isset($el['GUID']) || $type=='Y')
                $id=strval($el['Код']);
            else
                $id=strval($el['GUID']);
            $newEl=array(
                'ID'=>$id,
                //'SHARED'=>'Y',
                'LEGAL'=>$type,
                'TITLE'=>strval($el['Наименование']),
                'FULL_TITLE'=>strval($el['НаименованиеПолное']),
                'INN'=>strval($el['ИНН']),
                'KPP'=>strval($el['КПП']),
                'CODE_PO'=>strval($el['КодПоОКПО']),
                'LEGAL_PHONE'=>strval($el['Телефон']),
                'LEGAL_EMAIL'=>strval($el['Email']),
                'LEGAL_SHIP_ADDRESS'=>strval($el['Адресдоставки']),
                'LEGAL_MAIL_ADDRESS'=>strval($el['Почтовыйадрес']),
                'LEGAL_FAX'=>strval($el['Факс']),
                'LEGAL_OTHER'=>strval($el['Другое']),
                'FACT_ADDRESS'=>strval($el['Фактическийадрес']),
                'LEGAL_ADDRESS'=>strval($el['Юридическийадрес']),
            );


            if(!isset($COrderAgent))
                $COrderAgent=new COrderAgent(false);
            $bxEl=COrderAgent::GetListEx(array(),array('ID'=>$id),false,false,array_keys($newEl))->Fetch();


            if($el['Удален']===true) {
                if(!$COrderAgent->Delete($id)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка удаления контрагента # '.$id,
                        'Описание'=>$COrderAgent->LAST_ERROR
                    ));
                }else
                    $arRes['Удалено']['Контрагенты'][]=$id;
            }elseif($bxEl && $bxEl!=$newEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                if(!$COrderAgent->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения контрагента # '.$newEl['ID'],
                        'Описание'=>$COrderAgent->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Контрагенты'][]=$newEl['ID'];
            }elseif(!$bxEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                //$newEl['CREATED_BY_ID']=$iUserId;
                if(!$COrderAgent->Add($newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка создания контрагента # '.$newEl['ID'],
                        'Описание'=>$COrderAgent->LAST_ERROR
                    ));
                }else
                    $arRes['Добавлено']['Контрагенты'][]=$newEl['ID'];
            }
        } elseif($el['ТипОбъекта']=='Направление') {
            $id=strval($el['Код']);
            $newEl=array(
                'ID'=>$id,
                'TITLE'=>strval($el['Наименование']),
                'PARENT_ID'=>strval($el['Родитель']),
                'MANAGER_ID'=>strval($el['Руководитель']),
                'DESCRIPTION'=>strval($el['Описание']),
                'PRIVATE'=>strval($el['Приватно'])===true?'Y':'N',
            );

            if(!isset($COrderDirection))
                $COrderDirection=new COrderDirection(false);
            $bxEl=COrderDirection::GetListEx(array(),array('ID'=>$id),false,false,array_keys($newEl))->Fetch();

            if($el['Удален']===true) {
                if(!$COrderDirection->Delete($id)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка удаления направления # '.$id,
                        'Описание'=>$COrderDirection->LAST_ERROR
                    ));
                }else
                    $arRes['Удалено']['Направления'][]=$id;
            }elseif($bxEl && $bxEl!=$newEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                if(!$COrderDirection->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения направления # '.$newEl['ID'],
                        'Описание'=>$COrderDirection->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Направления'][]=$newEl['ID'];
            }elseif(!$bxEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                //$newEl['CREATED_BY_ID']=$iUserId;
                if(!$COrderDirection->Add($newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка создания направления # '.$newEl['ID'],
                        'Описание'=>$COrderDirection->LAST_ERROR
                    ));
                }else
                    $arRes['Добавлено']['Направления'][]=$newEl['ID'];
            }
        } elseif($el['ТипОбъекта']=='Номенклатура') {
            $arLang=array(
                'Оптовая цена'=>'PRICE_OPT',
                'ФИЗ_Базовая'=>'PRICE_PHYSICAL',
                'ЮР_Базовая'=>'PRICE_LEGAL',
            );
            $prices=array(
                'PRICE_PHYSICAL'=>'',
                'PRICE_LEGAL'=>'',
                'PRICE_OPT'=>'',
            );
            foreach($el['Цены'] as $np => $prc) {
                $para=each($prc);
                $prices[$arLang[$para['key']]]=strval($para['value']);
            }
            $id=strval($el['Код']);
            $newEl=array(
                'ID'=>$id,
                'TITLE'=>strval($el['Номенклатура']),
                'DIRECTION_ID'=>strval($el['УчебноеНаправление']),
                'SEMESTER'=>strval($el['Семестр']),
                'PRICE'=>$prices,
                'PRIVATE'=>strval($el['Приватна'])===true?'Y':'N',
            );

            if(!isset($COrderNomen))
                $COrderNomen=new COrderNomen(false);
            $bxEl=COrderNomen::GetListEx(array(),array('ID'=>$id),false,false,array_keys($newEl))->Fetch();

            if($bxEl) $bxEl['PRICE']=unserialize($bxEl['PRICE']);
            //__OrderEndResponse(array('newEl'=>$newEl,'bxEl'=>$bxEl,'el'=>$el));
            if($el['Удален']===true) {
                if(!$COrderNomen->Delete($id)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка удаления номенклатуры # '.$id,
                        'Описание'=>$COrderNomen->LAST_ERROR
                    ));
                }else
                    $arRes['Удалено']['Номенклатуры'][]=$id;

            }elseif($bxEl && $bxEl!=$newEl) {
                $newEl['PRICE']=serialize($newEl['PRICE']);
                //$newEl['MODIFY_BY_ID']=$iUserId;
                if(!$COrderNomen->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения номенклатуры # '.$newEl['ID'],
                        'Описание'=>$COrderNomen->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Номенклатуры'][]=$newEl['ID'];
            }elseif(!$bxEl) {
                $newEl['PRICE']=serialize($newEl['PRICE']);
                //$newEl['MODIFY_BY_ID']=$iUserId;
                //$newEl['CREATED_BY_ID']=$iUserId;
                if(!$COrderNomen->Add($newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка создания номенклатуры # '.$newEl['ID'],
                        'Описание'=>$COrderNomen->LAST_ERROR
                    ));
                }else
                    $arRes['Добавлено']['Номенклатуры'][]=$newEl['ID'];
            }
        } elseif($el['ТипОбъекта']=='УчебныйКурс') {
            $arLang=array(
                'Экзамен'=>'EXAM_TITLE',
                'ПроходнойБалл'=>'EXAM_MARK',
                'Литература'=>'LITER_ID',
                'Документ'=>'DOC_TITLE',
                'Код'=>'NOMEN_ID',
                'GUID'=>'TEACHER_ID',
            );
            $exams=array();
            foreach($el['ВступительныеЭкзамены'] as $np => $prc) {
                foreach($prc as $k => $v)
                    $exams[$np][$arLang[$k]]=strval($v);
            }
            $liters=array();
            foreach($el['ЛитератураПоКурсу'] as $np => $prc) {
                foreach($prc as $k => $v)
                    $liters[$np][$arLang[$k]]=strval($v);
            }
            $docs=array();
            foreach($el['ВыпускныеДокументы'] as $np => $prc) {
                foreach($prc as $k => $v)
                    $docs[$np][$arLang[$k]]=strval($v);
            }
            $nomens=array();
            foreach($el['Номенклатура'] as $np => $prc) {
                foreach($prc as $k => $v)
                    $nomens[$np][$arLang[$k]]=strval($v);
            }
            $teachers=array();
            foreach($el['Преподаватели'] as $np => $prc) {
                foreach($prc as $k => $v)
                    $teachers[$np][$arLang[$k]]=strval($v);
            }
            $id=strval($el['Код']);
            $newEl=array(
                'ID'=>$id,
                'TITLE'=>strval($el['Наименование']),
                'ANNOTATION'=>strval($el['Аннотация']),
                'DESCRIPTION'=>strval($el['Описание']),
                'COURSE_PROG'=>strval($el['ПрограммаКурса']),
                'DURATION'=>strval($el['Продолжительность']),
                'PREV_COURSE'=>strval($el['ПредыдущийКурс']),
                'EXAM'=>$exams,
                'LITER'=>$liters,
                'DOC'=>$docs,
                'NOMEN'=>$nomens,
                'TEACHER'=>$teachers,
            );
            
            if(!isset($COrderCourse))
                $COrderCourse=new COrderCourse(false);
            $bxEl=COrderCourse::GetListEx(array(),array('ID'=>$id),false,false,array_keys($newEl))->Fetch();

            if($bxEl) $bxEl=array_merge($bxEl,array(
                'EXAM'=>unserialize($bxEl['EXAM']),
                'LITER'=>unserialize($bxEl['LITER']),
                'DOC'=>unserialize($bxEl['DOC']),
                'NOMEN'=>unserialize($bxEl['NOMEN']),
                'TEACHER'=>unserialize($bxEl['TEACHER']),
            ));
            if($el['Удален']===true) {
                if(!$COrderCourse->Delete($id)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка удаления курса # '.$id,
                        'Описание'=>$COrderCourse->LAST_ERROR
                    ));
                }else
                    $arRes['Удалено']['Курсы'][]=$id;
            }elseif($bxEl && $bxEl!=$newEl) {
                $newEl=array_merge($newEl,array(
                    'EXAM'=>serialize($newEl['EXAM']),
                    'LITER'=>serialize($newEl['LITER']),
                    'DOC'=>serialize($newEl['DOC']),
                    'NOMEN'=>serialize($newEl['NOMEN']),
                    'TEACHER'=>serialize($newEl['TEACHER']),
                ));
                //$newEl['MODIFY_BY_ID']=$iUserId;
                if(!$COrderCourse->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения курса # '.$newEl['ID'],
                        'Описание'=>$COrderCourse->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Курсы'][]=$newEl['ID'];
            }elseif(!$bxEl) {
                $newEl=array_merge($newEl,array(
                    'EXAM'=>serialize($newEl['EXAM']),
                    'LITER'=>serialize($newEl['LITER']),
                    'DOC'=>serialize($newEl['DOC']),
                    'NOMEN'=>serialize($newEl['NOMEN']),
                    'TEACHER'=>serialize($newEl['TEACHER']),
                ));
                //$newEl['MODIFY_BY_ID']=$iUserId;
                //$newEl['CREATED_BY_ID']=$iUserId;
                if(!$COrderCourse->Add($newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка создания курса # '.$newEl['ID'],
                        'Описание'=>$COrderCourse->LAST_ERROR
                    ));
                }else
                    $arRes['Добавлено']['Курсы'][]=$newEl['ID'];
            }
        } elseif($el['ТипОбъекта']=='УчебнаяГруппа') {
            $id=strval($el['Код']);
            $newEl=array(
                'ID'=>$id,
                'TITLE'=>strval($el['Наименование']),
                'NOMEN_ID'=>strval($el['Номенклатура']),
                'PRIVATE'=>strval($el['Приватна'])===true?'Y':'N',
            );

            if(!isset($COrderGroup))
                $COrderGroup=new COrderGroup(false);
            $bxEl=COrderGroup::GetListEx(array(),array('ID'=>$id),false,false,array_keys($newEl))->Fetch();

            if($el['Удален']===true) {
                if(!$COrderGroup->Delete($id)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения учебной группы # '.$id,
                        'Описание'=>$COrderGroup->LAST_ERROR
                    ));
                }else
                    $arRes['Удалено']['Учебные группы'][]=$id;
            }elseif($bxEl && $bxEl!=$newEl) {
                $newEl['MODIFY_BY_ID'] = $iUserId;
                if(!$COrderGroup->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения учебной группы # '.$newEl['ID'],
                        'Описание'=>$COrderGroup->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Учебные группы'][]=$newEl['ID'];
            }elseif(!$bxEl) {
                $newEl['MODIFY_BY_ID'] = $iUserId;
                //$newEl['CREATED_BY_ID']=$iUserId;
                if(!$COrderGroup->Add($newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка создания учебной группы # '.$newEl['ID'],
                        'Описание'=>$COrderGroup->LAST_ERROR
                    ));
                }else
                    $arRes['Добавлено']['Учебные группы'][]=$newEl['ID'];
            }
        } elseif($el['ТипОбъекта']=='СформированнаяГруппа') {
            $id=strval($el['КодГруппы']);
            $gID=strval($el['Код']);
            $dStart=strval($el['ДатаНачалаОбучения']);
            $dEnd=strval($el['ДатаОкончанияОбучения']);
            $newEl=array(
                'ID'=>$id,
                'GROUP_ID'=>$gID,
                'DATE_START'=>$dStart,
                'DATE_END'=>$dEnd,
                'MAX'=>strval($el['МаксимальноеКоличествоСлушателей']),
            );

            if(!isset($COrderFormedGroup))
                $COrderFormedGroup=new COrderFormedGroup(false);
            $bxEl=COrderFormedGroup::GetListEx(array(),
                array('ID'=>$id),false,false,array_keys($newEl))->Fetch();

            foreach($bxEl as $code=> $val) {
                $bxEl[$code]=htmlspecialcharsback($bxEl[$code]);
            }
            $newEl['ID']=$bxEl['ID'];

            if($el['Удален']===true) {
                if(!$COrderFormedGroup->Delete($id)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка удаления сформированной группы #'.$id,
                        'Описание'=>$COrderFormedGroup->LAST_ERROR
                    ));
                }else
                    $arRes['Удалено']['Сформированные группы'][]=$id;
            }elseif($bxEl && $bxEl!=$newEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                if(!$COrderFormedGroup->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения сформированной группы # '.$newEl['ID'],
                        'Описание'=>$COrderFormedGroup->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Сформированные группы'][]=$newEl['ID'];
            } elseif(!$bxEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                //$newEl['CREATED_BY_ID']=$iUserId;
                if(!$COrderFormedGroup->Add($newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка создания сформированной группы # '.$newEl['ID'],
                        'Описание'=>$COrderFormedGroup->LAST_ERROR
                    ));
                }else
                    $arRes['Добавлено']['Сформированные группы'][]=$newEl['ID'];
            }

        } elseif($el['ТипОбъекта']=='Заявка') {
            $id=strval($el['НомерЗаявки']);
            $newEl=array(
                'ID'=>$id,
                'STATUS'=>'CONVERTED',
                'DESCRIPTION'=>'Сконвертирована в заказ # '.strval($el['НомерЗаказа']),
            );

            if(!isset($COrderApp))
                $COrderApp=new COrderApp(false);
            $bxEl=COrderApp::GetListEx(array(),array('ID'=>$id),false,false,array_keys($newEl))->Fetch();

            if($bxEl && $bxEl!=$newEl) {
                $newEl['MODIFY_BY_ID'] = $iUserId;
                if(!$COrderApp->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения заявки # '.$newEl['ID'],
                        'Описание'=>$COrderApp->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Заявки'][]=$newEl['ID'];
            }
        } elseif($el['ТипОбъекта']=='Регистрация') {
            if(strval($el['Состояние'])=='Переведен')
                continue;
            $arLang=array(
                'Оформлен'=>'ARRANGED',
                'Зачислен'=>'ENROLLED',
                'Отчислен'=>'EXPELLED',
                'Переведен'=>'MOVED',
                'Прервал обучение'=>'INTERRUPTED',
                'Зачислен по заявлению'=>'STATEMENT',
                'Зачислен на условиях'=>'REQUIREMENT',
            );

            $currentEntityId=strval($el['КодГруппы']);
            $oldEntityId=strval($el['СтараяГруппа']);
            if($oldEntityId=='')
                $oldEntityId=$currentEntityId;

            $entityType='FORMED_GROUP';
            $physicalID=strval($el['Слушатель']);
            $statusID=strval($el['Состояние']);
            $id='';
            $newEl=array(
                'ID'=>$id,
                'SHARED'=>'Y',
                'ENTITY_TYPE' => $entityType,
                'ENTITY_ID' => $currentEntityId,
                'PHYSICAL_ID'=>$physicalID,
                'STATUS'=>isset($arLang[$statusID])?$arLang[$statusID]:$statusID,
                'DESCRIPTION'=>strval($el['Комментарий']),
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
                    $newEl['APP_ID']=$arApp[$orderID]['ID'];
            }

            if(!isset($COrderReg))
                $COrderReg=new COrderReg(false);
            $bxEl=COrderReg::GetListEx(array(),
                array('ENTITY_TYPE'=>$entityType,'ENTITY_ID'=>$oldEntityId,'PHYSICAL_ID'=>$physicalID),
                false,false,array_keys($newEl))->Fetch();

            foreach($bxEl as $code=> $val) {
                $bxEl[$code]=htmlspecialcharsback($bxEl[$code]);
            }
            $newEl['ID']=$bxEl['ID'];
            if($el['Удален']===true) {
                if(!$COrderReg->Delete($bxEl['ID'])) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка удаления регистрации сформированной группы # '.$oldEntityId.
                            ' / слушателя # '.$physicalID,
                        'Описание'=>$COrderReg->LAST_ERROR
                    ));
                }else
                    $arRes['Удалено']['Регистрации'][]=$bxEl['ID'];
            }elseif($bxEl && $bxEl!=$newEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                //__OrderEndResponse(array('newEl'=>$newEl,'bxEl'=>$bxEl,'el'=>$el,'oldEnt'=>$oldEntity));
                if(!$COrderReg->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения регистрации сформированной группы # '.$oldEntityId.
                            ' / слушателя # '.$physicalID,
                        'Описание'=>$COrderReg->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Регистрации'][]=$newEl['ID'];
            } elseif(!$bxEl) {
                $newEl['ID']=COrderHelper::GetNewID();
                //$newEl['MODIFY_BY_ID']=$iUserId;
                //$newEl['CREATED_BY_ID']=$iUserId;
                if(!$COrderReg->Add($newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат' => 'Ошибка создания регистрации сформированной группы # ' . $currentEntityId .
                            ' / слушателя # ' . $physicalID,
                        'Описание' => $COrderReg->LAST_ERROR
                    ));
                }else
                    $arRes['Добавлено']['Регистрации'][]=$newEl['ID'];
            }
        } elseif($el['ТипОбъекта']=='Сотрудник') {
            $id=strval($el['GUID']);
            $newEl=array(
                'ID'=>$id,
                'EMPLOYMENT'=>strval($el['ТипЗанятости']),
                'DEPARTMENT'=>strval($el['Группа']),
            );
            $arDept=COrderStaff::GetDepartments(array('NAME'=>$newEl['DEPARTMENT']));
            foreach($arDept as $id=>$el) {
                $dept[]=$id;
            }

            $newEl['DEPARTMENT']=$dept;
            if(!isset($COrderStaff))
                $COrderStaff=new COrderStaff(false);
            $bxEl=COrderStaff::GetListEx(array(),array('ID'=>$id),false,false,array_keys($newEl))->Fetch();

            if($el['Удален']===true) {
                //__OrderEndResponse(array('id'=>$id,'bxEl'=>$bxEl,'el'=>$el,'request'=>$_REQUEST));
                if(!$COrderStaff->Delete($id)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка удаления сотрудника # '.$id,
                        'Описание'=>$COrderStaff->LAST_ERROR
                    ));
                }else
                    $arRes['Удалено']['Сотрудники'][]=$id;
            }elseif($bxEl && $bxEl!=$newEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                if(!$COrderStaff->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения сотрудника # '.$newEl['ID'],
                        'Описание'=>$COrderStaff->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Сотрудники'][]=$newEl['ID'];
            }elseif(!$bxEl) {
                $DB->Rollback();
                __OrderEndResponse(array(
                    'Результат'=>'Ошибка: добавление сотрудников невозможно',
                    'Описание'=>'Смотри доступные для изменения/удаления сотрудники в списке пользователей Bitrix (у кого есть GUID в доп. полях)'
                ));
                //$newEl['MODIFY_BY_ID']=$iUserId;
                //$newEl['CREATED_BY_ID']=$iUserId;
                /*if(!$COrderStaff->Add($newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка создания направления # '.$newEl['ID'],
                        'Описание'=>$COrderStaff->LAST_ERROR
                    ));
                }else
                    $arRes['Добавлено']['Направления'][]=$newEl['ID'];*/
            }
        } elseif($el['ТипОбъекта']=='Преподаватель') {
            $id=strval($el['GIUD']);
            $newEl=array(
                'ID'=>$id,
                'TITLE' => strval($el['ФИОКратко']),
                'EDUCATION' => strval($el['Образование']),
                'EXPERIENCE' => strval($el['ОпытРаботы']),
                'INTERESTS' => strval($el['ПрофессиональныеИнтересы']),
                'DEGREE' => strval($el['УченаяСтепень']),
            );

            if(!isset($COrderTeacher))
                $COrderTeacher=new COrderTeacher(false);
            $bxEl=COrderTeacher::GetListEx(array(),array('ID'=>$id),false,false,array_keys($newEl))->Fetch();

            if($el['Удален']===true) {
                //__OrderEndResponse(array('id'=>$id,'bxEl'=>$bxEl,'el'=>$el,'request'=>$_REQUEST));
                if(!$COrderTeacher->Delete($id)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка удаления преподавателя # '.$id,
                        'Описание'=>$COrderTeacher->LAST_ERROR
                    ));
                }else
                    $arRes['Удалено']['Преподаватели'][]=$id;
            }elseif($bxEl && $bxEl!=$newEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                if(!$COrderTeacher->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения преподавателя # '.$newEl['ID'],
                        'Описание'=>$COrderTeacher->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Преподаватели'][]=$newEl['ID'];
            }elseif(!$bxEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                //$newEl['CREATED_BY_ID']=$iUserId;
                if(!$COrderTeacher->Add($newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка создания преподавателя # '.$newEl['ID'],
                        'Описание'=>$COrderTeacher->LAST_ERROR
                    ));
                }else
                    $arRes['Добавлено']['Преподаватели'][]=$newEl['ID'];
            }
        } elseif($el['ТипОбъекта']=='УчебныйКласс') {
            $id=strval($el['Код']);
            $newEl=array(
                'ID'=>$id,
                'NUMBER' => strval($el['Номер']),
                'TITLE' => strval($el['Наименование']),
                'TYPE' => strval($el['ВидКласса']),
                'CAPACITY' => strval($el['Вместимость']),
                'DESCRIPTION' => strval($el['Описание']),
                'LOCATION' => strval($el['Местоположение']),
            );

            if(!isset($COrderRoom))
                $COrderRoom=new COrderRoom(false);
            $bxEl=COrderRoom::GetListEx(array(),array('ID'=>$id),false,false,array_keys($newEl))->Fetch();

            if($el['Удален']===true) {
                //__OrderEndResponse(array('id'=>$id,'bxEl'=>$bxEl,'el'=>$el,'request'=>$_REQUEST));
                if(!$COrderRoom->Delete($id)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка удаления аудитории # '.$id,
                        'Описание'=>$COrderRoom->LAST_ERROR
                    ));
                }else
                    $arRes['Удалено']['Аудитории'][]=$id;
            }elseif($bxEl && $bxEl!=$newEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                if(!$COrderRoom->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения аудитории # '.$newEl['ID'],
                        'Описание'=>$COrderRoom->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Аудитории'][]=$newEl['ID'];
            }elseif(!$bxEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                //$newEl['CREATED_BY_ID']=$iUserId;
                if(!$COrderRoom->Add($newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка создания аудитории # '.$newEl['ID'],
                        'Описание'=>$COrderRoom->LAST_ERROR
                    ));
                }else
                    $arRes['Добавлено']['Аудитории'][]=$newEl['ID'];
            }
        } elseif($el['ТипОбъекта']=='Расписание') {
            $id=strval($el['КодЗанятия']);
            $newEl=array(
                'ID'=>$id,
                'GROUP_ID' => strval($el['УчебнаяГруппа']),
                'DATE_START' => strval(ConvertDateTime($el['ДатаНачалаЗанятия'])),
                'DATE_END' => strval(ConvertDateTime($el['ДатаОкончанияЗанятия'])),
                'ROOM_ID' => strval($el['УчебныйКласс']),
                'COURSE_ID' => strval($el['УчебныйКурс']),
                'TEACHER_ID' => strval($el['Преподаватель']),
                'LESSON_TYPE' => strval($el['ФормаПроведенияЗанятия']),
            );

            if(!isset($COrderSchedule))
                $COrderSchedule=new COrderSchedule(false);
            $bxEl=COrderSchedule::GetListEx(array(),array('ID'=>$id),false,false,array_keys($newEl))->Fetch();

            if($el['Удален']===true) {
                //__OrderEndResponse(array('id'=>$id,'bxEl'=>$bxEl,'el'=>$el,'request'=>$_REQUEST));
                if(!$COrderSchedule->Delete($id)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка удаления расписания # '.$id,
                        'Описание'=>$COrderSchedule->LAST_ERROR
                    ));
                }else
                    $arRes['Удалено']['Расписание'][]=$id;
            }elseif($bxEl && $bxEl!=$newEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                if(!$COrderSchedule->Update($newEl['ID'], $newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения расписания # '.$newEl['ID'],
                        'Описание'=>$COrderSchedule->LAST_ERROR
                    ));
                }else
                    $arRes['Изменено']['Расписание'][]=$newEl['ID'];
            }elseif(!$bxEl) {
                //$newEl['MODIFY_BY_ID']=$iUserId;
                //$newEl['CREATED_BY_ID']=$iUserId;
                if(!$COrderSchedule->Add($newEl)) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка создания расписания # '.$newEl['ID'],
                        'Описание'=>$COrderSchedule->LAST_ERROR
                    ));
                }else
                    $arRes['Добавлено']['Расписание'][]=$newEl['ID'];
            }
        } elseif($el['ТипОбъекта']=='Оценки') {
            $gID = strval($el['УчебнаяГруппа']);
            $dStart = strval(ConvertDateTime($el['ДатаНачалаЗанятия']));
            $arMarks=$el['Оценки'];
            foreach($arMarks as $mark) {
                $pID = strval($mark['Слушатель']);
                $type = strval($mark['ТипОценки']);
                $id=serialize(array($pID,$type));
                $ar1C[$id]=array(
                    'ID'=>$id,
                    'PHYSICAL_ID'=>$pID,
                    'GROUP_ID'=>$gID,
                    'DATE_START'=>$dStart,
                    'TYPE' => $type,
                    'MARK' => strval($mark['Оценка']),
                    'POINTS' => strval($mark['Баллы']),
                );
            }
            if(!isset($COrderMark))
                $COrderMark=new COrderMark(false);
            $res=COrderMark::GetListEx(array(),
                array('GROUP_ID'=>$gID,'DATE_START'=>$dStart),false,false,array_keys(reset($ar1C)));

            while($el=$res->Fetch()) {
                $arBX[$el['ID']]=$el;
            }
            foreach($arBX as $id=>$elem) {
                foreach($elem as $code=> $val) {
                    $arBX[$id][$code]=htmlspecialcharsback($arBX[$id][$code]);
                }
                $altID=serialize(array(
                    $elem['PHYSICAL_ID'],
                    $elem['TYPE'],
                ));
                $arIDs[$altID]=$elem['ID'];
                $arBX[$id]['ID']=$altID;
                $arBX[$altID]=$arBX[$id];
                unset($arBX[$id]);
            }
            //__OrderEndResponse(array('ar1C'=>$ar1C,'arBX'=>$arBX,'arIDs'=>$arIDs));

            foreach($ar1C as $id=>$item) {
                if(isset($arBX[$id]) && $arBX[$id]!=$item)
                    $arMod[]=$id;
                elseif(!isset($arBX[$id]))
                    $arAdd[]=$id;
            }

            foreach($arBX as $id=>$item) {
                if(!isset($ar1C[$id]) || $el['Удален']===true)
                    $arDel[]=$id;
            }
            //__OrderEndResponse(array('1C'=>$ar1C,'BX'=>$arBX,'arMod'=>$arMod,'arAdd'=>$arAdd,'arDel'=>$arDel,'arIDs'=>$arIDs));

            foreach($arAdd as $id) {
                $ar1C[$id]['ID']=COrderHelper::GetNewID();
                if(!$COrderMark->Add($ar1C[$id])) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка создания оценки '.
                            'с ID слушателя '.$ar1C[$id]['PHYSICAL_ID'].', группы  #'.$gID.', датой начала занятия '.
                            $dStart.' и типом оценки '.$ar1C[$id]['TYPE'],
                        'Описание'=>$COrderMark->LAST_ERROR
                    ));
                }
                else $arRes['Добавлено']['Оценки'][]=$ar1C[$id]['ID'];
            }
            foreach($arMod as $num=>$id) {
                if(!$COrderMark->Update($arIDs[$id],$ar1C[$id])) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка изменения оценки '.
                            'с ID слушателя '.$ar1C[$id]['PHYSICAL_ID'].', группы  #'.$gID.', датой начала занятия '.
                            $dStart.' и типом оценки '.$ar1C[$id]['TYPE'],
                        'Описание'=>$COrderMark->LAST_ERROR
                    ));
                }
                else $arRes['Изменено']['Оценки'][]=$arIDs[$id];
            }
            foreach($arDel as $id) {
                if(!$COrderMark->Delete($arIDs[$id])) {
                    $DB->Rollback();
                    __OrderEndResponse(array(
                        'Результат'=>'Ошибка удаления оценки '.
                            'с ID слушателя '.$arBX[$id]['PHYSICAL_ID'].', группы  #'.$gID.', датой начала занятия '.
                            $dStart.' и типом оценки '.$arBX[$id]['TYPE'],
                        'Описание'=>$COrderMark->LAST_ERROR
                    ));
                }
                else $arRes['Удалено']['Оценки'][]=$arIDs[$id];
            }

        }

    }
    $DB->Commit();
}
else {
    __OrderEndResponse(array('Результат'=>'Ошибка конвертации данных в массив','Входные параметры'=>$_REQUEST));
}
__OrderEndResponse(array('Результат'=>'Выполнено','Готово'=>$arRes));
?>
