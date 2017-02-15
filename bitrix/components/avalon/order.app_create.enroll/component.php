<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!function_exists('GetNomenGroups')) {
	function GetNomenWithGroups($nomenId)
	{
		$arrNomen=COrderNomen::GetByID($nomenId);
		$arrNomen['TYPE']='NOMEN';
		$res=COrderFormedGroup::GetListEx(array(),array('NOMEN_ID'=>$nomenId,'>DATE_START'=>ConvertTimeStamp(false, "FULL")));
		while($el=$res->Fetch()) {
			$arrNomen['FORMED_GROUP'][$el['ID']]=$el;
		}
		return $arrNomen;
	}
}
if (!function_exists('GetGroupSiblings')) {
	function GetGroupSiblings($arrGroup)
	{
		$siblings=array();
		$res=COrderFormedGroup::GetListEx(array(),array(
			'!ID'=>$arrGroup['ID'],
			'NOMEN_ID'=>$arrGroup['NOMEN_ID'],
			'>DATE_START'=>ConvertTimeStamp(false, "FULL")));
		while($el=$res->Fetch()) {
			$siblings[$el['ID']]=$el;
		}
		return $siblings;
	}
}
if (!function_exists('GetFullStructure')) {
	function GetFullStructure()
	{
		$structure=COrderHelper::GetRootDirectionList();

		$res=COrderDirection::GetListEx();
		while($el=$res->Fetch()) {
			$directionList[$el['ID']]=$el;
		}

		foreach($structure as $id=>$el) {
			$structure[$id]=$directionList[$id];
		}

		foreach($structure as $id=>$el) {
			$structure[$id]['SUB_DIRECTIONS']=$directionList[$id];
		}

		$res=COrderNomen::GetListEx();
		while($el=$res->Fetch()) {
			$nomenList[$el['ID']]=$el;
		}

		$res=COrderFormedGroup::GetListEx();
		while($el=$res->Fetch()) {
			$formedGroupList[$el['ID']]=$el;
		}

		return $structure;
	}
}

if (CModule::IncludeModule("order")) {
	global $DB;
	$arResult['PATH_TO_BASKET'] = isset($arParams['PATH_TO_BASKET']) ? $arParams['PATH_TO_BASKET'] : '/bitrix/tools/order/ajax_basket.php';
	$GLOBALS['strError'] = '';
	if ($_GET["formresult"] == "addok")
		$this->IncludeComponentTemplate();
	else {
		if (isset($_GET['direction_id']) && $_GET['direction_id']!='') {
			$fEntity=COrderDirection::GetByID($_GET['direction_id']);
			$fEntity['TYPE']='DIRECTION';
			if($fEntity['DEFAULT_NOMEN_ID']!='') {
				$fEntity=GetNomenWithGroups($fEntity['DEFAULT_NOMEN_ID']);
			}
			$arResult["ENTITY"][] = $fEntity;
		} elseif (isset($_GET['nomen_id']) && $_GET['nomen_id']!='') {
			$fEntity=GetNomenWithGroups($_GET['nomen_id']);
			$arResult["ENTITY"][] = $fEntity;
		} elseif (isset($_GET['formed_group_id']) && $_GET['formed_group_id']!='') {
			$fEntity=COrderFormedGroup::GetByID($_GET['formed_group_id']);
			$fEntity['TYPE']='FORMED_GROUP';
			$fEntity['SIBLINGS']=GetGroupSiblings($fEntity);
			$arResult["ENTITY"][] = $fEntity;
		}
		var_dump($arResult);

		if (count($arResult["ENTITY"]) <= 0)
			$arResult['ERROR'][] = GetMessage('ERROR_NO_ENTITY');
		$arResult["SRC_LIST"] = COrderHelper::GetEnumListNew('APP','SOURCE');
		$arResult["isUseCaptcha"] = 'Y';

		if (count($arResult["ERROR"]) <= 0) {
			// ************************************************************* //
			// ****************** get/post processing ********************** //
			// ************************************************************* //

			if ($arResult["isUseCaptcha"] == 'Y')
				$arResult["CAPTCHACode"] = $APPLICATION->CaptchaGetCode();

			$arResult["arrVALUES"] = array();
			$bSuccess = false;

			$arResult["arrVALUES"] = $_REQUEST;
			if (strlen($_REQUEST["web_form_submit"]) > 0 || strlen($_REQUEST["web_form_apply"]) > 0) {


				$DB->StartTransaction();
				if ($arResult["arrVALUES"]['AGENT_TYPE'] == 'P') {
					if (!isset($arResult["arrVALUES"]['AGENT']['TITLE']) || count(explode(' ', $arResult["arrVALUES"]['AGENT']['TITLE'])) < 2)
						$arResult['ERROR'][] = GetMessage('ERROR_AGENT_TITLE');
					if (!isset($arResult["arrVALUES"]['AGENT']['PHONE']) || strlen($arResult["arrVALUES"]['AGENT']['PHONE']) < 7)
						$arResult['ERROR'][] = GetMessage('ERROR_AGENT_PHONE');
					if (!isset($arResult["arrVALUES"]['AGENT']['EMAIL']) || strlen($arResult["arrVALUES"]['AGENT']['EMAIL']) < 6)
						$arResult['ERROR'][] = GetMessage('ERROR_AGENT_EMAIL');
				}
				if ($arResult["arrVALUES"]['AGENT_TYPE'] == 'L') {
					if (!isset($arResult["arrVALUES"]['AGENT']['TITLE']) || strlen($arResult["arrVALUES"]['AGENT']['TITLE']) < 3)
						$arResult['ERROR'][] = GetMessage('ERROR_AGENT_L_TITLE');
					if (!isset($arResult["arrVALUES"]['CONTACT']['NAME']) || count(explode(' ', $arResult["arrVALUES"]['CONTACT']['NAME'])) < 2)
						$arResult['ERROR'][] = GetMessage('ERROR_CONTACT_NAME');
					if (!isset($arResult["arrVALUES"]['CONTACT']['PHONE']) || strlen($arResult["arrVALUES"]['CONTACT']['PHONE']) <7)
						$arResult['ERROR'][] = GetMessage('ERROR_CONTACT_PHONE');
					if (!isset($arResult["arrVALUES"]['CONTACT']['EMAIL']) || strlen($arResult["arrVALUES"]['CONTACT']['EMAIL']) <6)
						$arResult['ERROR'][] = GetMessage('ERROR_CONTACT_EMAIL');
				}
				if ($arResult["isUseCaptcha"] == 'Y' && !$APPLICATION->CaptchaCheckCode($arResult["arrVALUES"]['captcha_word'], $arResult["arrVALUES"]['captcha_sid']))
					$arResult['ERROR'][] = GetMessage('ERROR_CAPTCHA');


				$bSuccess = count($arResult['ERROR']) <= 0;
				if ($bSuccess) {
					$aTitle = $arResult["arrVALUES"]['AGENT']['TITLE'];
					$aPhone = $arResult["arrVALUES"]['AGENT']['PHONE'];
					$aEmail = $arResult["arrVALUES"]['AGENT']['EMAIL'];
					if ($arResult["arrVALUES"]['AGENT_TYPE'] == 'P') {
						$cName = '';
						$cPhone = '';
						$cEmail = '';
					} elseif ($arResult["arrVALUES"]['AGENT_TYPE'] == 'L') {
						$cName = $arResult["arrVALUES"]['CONTACT']['NAME'];
						$cPhone = $arResult["arrVALUES"]['CONTACT']['PHONE'];
						$cEmail = $arResult["arrVALUES"]['CONTACT']['EMAIL'];
					} else $bSuccess = false;


					if ($bSuccess) {

						$appID = COrderHelper::GetNewID();
						$arApp = Array(
							"ID" => $appID,
							"AGENT_ID" => '',
							"AGENT_LEGAL" => $arResult["arrVALUES"]['AGENT_TYPE'] == 'L' ? 'Y' : 'N',
							"AGENT_TITLE" => $aTitle,
							"AGENT_PHONE" => $aPhone,
							"AGENT_EMAIL" => $aEmail,
							"CONTACT_FULL_NAME" => $cName,
							"CONTACT_PHONE" => $cPhone,
							"CONTACT_EMAIL" => $cEmail,
							"ASSIGNED_ID" => 'D54',
							"STATUS" => 'NEW',
							"SOURCE" => $arResult["arrVALUES"]['SOURCE'],
							"PAST" => $arResult["arrVALUES"]['PAST'] == 'Y' ? 'Y' : 'N',
							"DESCRIPTION" => $arResult["arrVALUES"]['DESCRIPTION'],
							"PUBLIC_SOURCE" => 'Y',
						);
						$COrderApp=new COrderApp(false);
						$bSuccess = ($COrderApp->Add($arApp)) ? true : false;

						if ($bSuccess) {
							foreach ($arResult["arrVALUES"]['REG'] as $entityType => $IDs) {
								foreach ($IDs as $entityID => $arRegs) {
									foreach ($arRegs as $reg) {
										if ($bSuccess) {
											if (isset($regID)) {
												$arRegID[] = $regID;
												unset($regID);
											}

											$pName = $reg['NAME'];
											$regID = COrderHelper::GetNewID();
											$arReg = Array(
												'ID' => $regID,
												'SHARED' => 'N',
												'APP_ID' => $appID,
												'ENTITY_TYPE' => strtolower($entityType),
												'ENTITY_ID' => $entityID,
												'PHYSICAL_ID' => '',
												'PHYSICAL_FULL_NAME' => $pName,
												'PAST' => count(explode(' ', $pName)) < 2 ? 'N' : ($reg['PAST'] == 'Y' ? 'Y' : 'N'),
												'STATUS' => 'NEW',
												'PERIOD' => date('d.m.Y', mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"))),
											);
											$COrderReg=new COrderReg(false);
											$bSuccess = ($COrderReg->Add($arReg)) ? true : false;
										}
										unset($reg);
										unset($nameStudent);
										unset($studentID);
									}
								}
							}
						}
					}

				}

				if ($bSuccess) {
					$aUpdate=array('ASSIGNED_ID'=>COrderApp::SetAssigned($appID));
					$COrderApp->Update($appID,$aUpdate);
					unset($_SESSION['BASKET']);
					$DB->Commit();
					LocalRedirect("?formresult=addok");
				} else {
					$DB->Rollback();
				}
			}
			// ************************************************************* //
			//                                             output                                                                    //
			// ************************************************************* //


			$arResult["FORM_HEADER"] = sprintf( // form header (<form> tag and hidden inputs)
				"<form name=\"%s\" action=\"%s\" method=\"%s\" enctype=\"multipart/form-data\">",
				'new_order', '', "POST"
			);
			$arResult["FORM_FOOTER"] = '</form>';
			// include default template
			$this->IncludeComponentTemplate();
		} else {
			foreach ($arResult["ERROR"] as $error) {
				ShowError($error);
			}
		}
	}
}
else
{
	ShowError(GetMessage("FORM_MODULE_NOT_INSTALLED"));
}
?>