<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
global $APPLICATION;
if(!function_exists('OrderEndResponse'))
{
	function OrderEndResponse($result)
	{
		$GLOBALS['APPLICATION']->RestartBuffer();
		Header('Content-Type: application/JSON; charset='.LANG_CHARSET);
		if(!empty($result))
		{
			echo CUtil::PhpToJSObject($result,true);
		}
		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
		die();
	}
}
if (!CModule::IncludeModule('order'))
{
	OrderEndResponse(array('ERROR'=>GetMessage('ERROR_ORDER_MODULE_NOT_INSTALLED')));
}
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/order/public/api/request.php");
if (!isset($_REQUEST[GetMessage('ORDER_1C_FIELD_APP_ID')]))
{
	OrderEndResponse(array('ERROR'=>GetMessage('ERROR_APP_ID')));
}
//Получение заявки по входному значению скрипта. Статус заявки должен быть "готовая к заказу"
$app=COrderApp::GetListEx(array(),array('STATUS'=>'READY','ID'=>$_REQUEST[GetMessage('ORDER_1C_FIELD_APP_ID')]),false,false,array('ID','AGENT_ID'))->Fetch();
//Если заявка найдена
if($app) {
	//Получение списка всех физических лиц, которых нет в 1С, со всеми полями
	$arListPhysical=array();
	$res=COrderPhysical::GetListEx(array(),array('SHARED' => 'N'),false,false,array(
		'ID','LAST_NAME','NAME','SECOND_NAME','GENDER','BDAY','OUT_ADDRESS','REG_ADDRESS',
		'LIVE_ADDRESS','EMAIL','PHONE','OTHER','PROF_EDU','LVL_EDU','PASSPORT','NATION',
		'DEPT','INDEX','REGION','BPLACE','SECOND_EDU','CERT_MID_EDU','SERIAL_DIP','NOM_DIP',
		'WHO_DIP','WHEN_DIP','END_YEAR','HONORS_DIP','ORIGINAL_DIP'
	));
	while($el=$res->Fetch()) {
		$arListPhysical[$el['ID']]=$el;
	}
	//Получение контрагента для найденной заявки без полей физ. лиц
	$agent=COrderAgent::GetListEx(array(),array('ID'=>$app['AGENT_ID']),false,false,array(
		"ID","LEGAL",'TITLE','FULL_TITLE','INN','KPP','CODE_PO','LEGAL_PHONE',"LEGAL_EMAIL",
		"LEGAL_SHIP_ADDRESS","LEGAL_MAIL_ADDRESS","LEGAL_FAX","LEGAL_OTHER",'FACT_ADDRESS',
		'LEGAL_ADDRESS',"CONTACT_ID",'CONTACT_GUID','SHARED'
	))->Fetch();
	//Получение актуального контакта для найденного контрагента без полей физ. лиц
	$contact=COrderContact::GetListEx(array('START_DATE'=>'DESC'),array('AGENT_ID'=>$agent['ID']),false,false,array(
		'ID','GUID','START_DATE','END_DATE','ASSIGNED_ID','AGENT_ID','SHARED'
	))->Fetch();
	//Получение списока всех регистраций найденной заявки
	$arReg=array();
	$arRegStatus=COrderHelper::GetEnumList('REG',"STATUS");
	$res=COrderReg::GetListEx(array(),array('APP_ID'=>$app['ID']),false,false,array('ID','ENTITY_TYPE','ENTITY_ID','PHYSICAL_ID','STATUS','APP_ID'));
	while($el=$res->Fetch()) {
		$el['STATUS']=$arRegStatus[$el['STATUS']];
		$arReg[$el['ID']]=$el;
	}
	//Получение списка всех номенклатур для дальнейшей вставки этой информации в регистрации
	$arNomen=array();
	$res=COrderNomen::GetListEx(array(),array(),false,false,array('ID'));
	while($el=$res->Fetch()) {
		$arNomen[$el['ID']]=$el;
	}
	//Получение списка всех групп для дальнейшей вставки этой информации в регистрации
	$arGroup=array();
	$res=COrderGroup::GetListEx(array(),array(),false,false,array('ID','DATE_START','DATE_END','NOMEN_ID'));
	while($el=$res->Fetch()) {
		$arGroup[$el['ID']]=$el;
	}
	//Получение списка всех сформированных групп для дальнейшей вставки этой информации в регистрации
	$arFormedGroup=array();
	$res=COrderFormedGroup::GetListEx(array(),array(),false,false,array('ID','GROUP_ID','DATE_START','DATE_END','NOMEN_ID'));
	while($el=$res->Fetch()) {
		$arFormedGroup[$el['ID']]=$el;
	}

	//Сбор массива заявки
	$newAgent=array();
	$arPhysical=array();
	$arContact=array();
	$arAppReg=array();
	foreach($arReg as $regID => $reg) {
		$newReg=array();
		switch (strtolower($reg['ENTITY_TYPE'])) {
			case 'formed_group':
				$formedGroup=reset(array_filter($arFormedGroup,function($el) use ($reg) {
					return $el['ID']==$reg['ENTITY_ID'];
				}));
				$newReg=array(
					'GROUP_ID'=>$formedGroup['GROUP_ID'],
					'DATE_START'=>$formedGroup['DATE_START'],
					'DATE_END'=>$formedGroup['DATE_END'],
					'NOMEN_ID'=>$formedGroup['NOMEN_ID'],
					'ENTITY_TYPE'=>'FORMED_GROUP'
				);
				break;
			case 'group':
				$group=reset(array_filter($arGroup,function($el) use ($reg) {
					return $el['ID']==$reg['ENTITY_ID'];
				}));
				$newReg=array(
					'GROUP_ID'=>$group['ID'],
					'NOMEN_ID'=>$group['NOMEN_ID'],
					'ENTITY_TYPE'=>'GROUP'
				);
				break;
			case 'nomen':
				$newReg=array(
					'NOMEN_ID'=>$arNomen[$reg['ENTITY_ID']]['ID'],
					'ENTITY_TYPE'=>'NOMEN'
				);
				break;
		}
		$newReg=array_merge($newReg,array(
			'STATUS'=>$reg['STATUS'],
			'PHYSICAL_ID'=>$reg['PHYSICAL_ID'],
		));
		$arAppReg[]=$newReg;
		if(array_key_exists($reg['PHYSICAL_ID'],$arListPhysical)) {
			$arPhysical[]=$arListPhysical[$reg['PHYSICAL_ID']];
		}
	}
	//Если контрагент - физ. лицо или контакт есть в 1С, удаляем из результата этот контакт
	if($agent['LEGAL']=='N' || $contact['SHARED']=='Y') {
		$contact=null;
	}
	else {
		//Проверяем есть ли физ. лицо этого контакта в списке физ. лиц, отсутствующих в 1С
		if(array_key_exists($contact['GUID'],$arListPhysical)) {
			$arPhysical[$contact['GUID']]=$arListPhysical[$contact['GUID']];
		}
		//Удаляем поле SHARED из результата
		unset ($contact['SHARED']);
	}

	//Если контрагент есть в 1С, удаляем его из результата
	if($agent['SHARED']=='Y') {
		$agent=null;
	}
	else {
		//Если контрагент - физ. лицо и этого физ. лица нет в 1С, добавляем его в результат
		if($agent['LEGAL']=='N' &&	array_key_exists($agent['ID'],$arListPhysical)) {
			$arPhysical[$agent['ID']]=$arListPhysical[$agent['ID']];
		}
		//Удаляем поле SHARED из результата
		unset($agent['SHARED']);
		unset($agent['LEGAL']);
	}

	//Получение результата с ключами в нужном языке
	$newApp=array();
	//Для каждой регистрации подставляем нужные ключи
	foreach($arAppReg as $regID=>$reg) {
		$newReg=$reg;

		$arrKeys=array();
		$arrVals=array();
		//С помощью функции array_combine 2 массива (нужные ключи и значения) собираем в 1
		foreach($newReg as $field=>$val) {
			if($val!='') {
				$arrKeys[] = GetMessage('ORDER_1C_FIELD_REG_' . $field);
				if ($val == 'Y')
					$val = true;
				elseif ($val == 'N')
					$val = false;
				elseif ($val == 'NOMEN')
					$val = GetMessage('ORDER_1C_FIELD_REG_NOMEN');
				elseif ($val == 'GROUP')
					$val = GetMessage('ORDER_1C_FIELD_REG_GROUP');
				elseif ($val == 'FORMED_GROUP')
					$val = GetMessage('ORDER_1C_FIELD_REG_FORMED_GROUP');
				$arrVals[] = $val;
			}
		}
		$newReg = array_combine($arrKeys, $arrVals);
		$newApp['REG'][]=$newReg;
	}

	foreach(array_diff_key($app, $newApp) as $key => $val) {
		$newApp[$key]=$val;
	}
	$arrKeys=array();
	$arrVals=array();
	foreach($newApp as $field=>$val) {
		if($val!='') {
			$arrKeys[] = GetMessage('ORDER_1C_FIELD_APP_' . $field);
			if ($val == 'Y')
				$val = true;
			elseif ($val == 'N')
				$val = false;
			$arrVals[] = $val;
		}
	}
	$newApp = array_combine($arrKeys, $arrVals);


	$newArPhysical=array();
	$newContact=array();
	$newAgent=array();

	//То же самое делаем с массивом физ. лиц, контрагента и контакта
	foreach($arPhysical as $id=>$physical) {
		$newPhysical=$physical;
		$arrKeys=array();
		$arrVals=array();
		foreach($newPhysical as $field=>$val) {
			if($val!='') {
				$arrKeys[] = GetMessage('ORDER_1C_FIELD_PHYSICAL_' . $field);
				if ($val == 'Y')
					$val = true;
				elseif ($val == 'N')
					$val = false;
				$arrVals[] = $val;
			}
		}
		$newPhysical = array_combine($arrKeys, $arrVals);
		$newArPhysical[]=$newPhysical;
	}

	$newContact=$contact;
	$arrKeys=array();
	$arrVals=array();
	foreach($newContact as $field=>$val) {
		if($val!='') {
			$arrKeys[] = GetMessage('ORDER_1C_FIELD_CONTACT_' . $field);
			if ($val == 'Y')
				$val = true;
			elseif ($val == 'N')
				$val = false;
			$arrVals[] = $val;
		}
	}
	$newContact = array_combine($arrKeys, $arrVals);

	$newAgent=$agent;
	$arrKeys=array();
	$arrVals=array();
	foreach($newAgent as $field=>$val) {
		if($field!='CONTACT_ID' && $val!='') {
			$arrKeys[] = GetMessage('ORDER_1C_FIELD_AGENT_'.$field);
			if($val=='Y')
				$val=true;
			elseif($val=='N')
				$val=false;
			$arrVals[] = $val;
		}
	}
	$newAgent = array_combine($arrKeys, $arrVals);

	if(!empty($newArPhysical))
		$newApp[(GetMessage('ORDER_PHYSICAL'))]=$newArPhysical;

	if(!empty($newContact))
		$newApp[(GetMessage('ORDER_CONTACT'))]=$newContact;

	if(!empty($newAgent))
		$newApp[(GetMessage('ORDER_AGENT'))]=$newAgent;

	OrderEndResponse($newApp);
}
else {
	OrderEndResponse(array('ERROR' => GetMessage('ERROR_APP_DOESNT_EXIST')));
}
?>
