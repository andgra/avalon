<?php
IncludeModuleLangFile(__FILE__);

class COrderEntitySelectorHelper
{
	public static function GetStructureSelector($id,$selected) {
		ob_start();
		GLOBAL $APPLICATION;
		$funcSuffix = uniqid();
		$id=$id==''?$funcSuffix:$id;
		CUtil::InitJSCore( array('ajax' , 'popup', 'jquery','structure' ));
		$contId="order_structure_container_".$id;
		$arResult['SELECTED']=array();
		if(isset($selected)
			&& isset($selected['TYPE']) && $selected['TYPE']!=''
			&& isset($selected['VALUE']) && $selected['VALUE']!='')
			$arResult['SELECTED']=array(
				'type'=>$selected['TYPE'],
				'value'=>$selected['VALUE'],
				'title'=>$selected['TITLE']
			);
		?>
		<div class="order-structure-wraps-container" id="<?=$contId?>">
			<div class="order-structure-info-wrap">
				<span class="order-structure-info-title"></span>
				<input type="hidden" class="order-structure-input-type" name="ORDER_STRUCTURE_<?=$id?>_TYPE" value="">
				<input type="hidden" class="order-structure-input-value" name="ORDER_STRUCTURE_<?=$id?>_VALUE" value="">
			</div>
			<div class="order-structure-buttons-wrap"><a style="text-decoration: underline" href="javascript:void(0)" name="orderStructureSelect" onclick="obj_<?=$id?>.ShowForm(); return false"><?=GetMessage('interface_form_edit');?></a></div>
		</div>
		<?
		$html=ob_get_contents();
		ob_end_clean();
		?>
		<script>
			<?ob_start();?>
			BX.ready(
				function()
				{
					obj_<?=$id?>=BX.OrderStructure.Set(
						'<?=$id?>',
						'<?=$contId?>',
						<?=CUtil::PhpToJSObject($arResult['SELECTED'])?>
					);
					//BX.addCustomEvent(BX.OrderStructure, 'onSelectDirection', OrderPermAccessSelectProvider);
				}
			);

			<?
		$script=ob_get_contents();
		ob_end_clean();?>
		</script>
		<?
		return array('html'=>$html,'script'=>$script,'id'=>$id,'contId'=>$contId,'selected'=>$arResult['SELECTED']);
	}
	public static function GetPersonSelector($id,$type,$selected) {
		ob_start();
		$funcSuffix = uniqid();
		$id=$id==''?$funcSuffix:$id;
		CUtil::InitJSCore( array('ajax' , 'popup', 'jquery','person' ));
		$contId="order_person_container_".$id;
		$newSelected['id']=isset($selected['ID'])?$selected['ID']:'';
		switch($type) {
			case 'physical':
				$newSelected['title']=isset($selected['FULL_NAME'])?$selected['FULL_NAME']:'';
				$newSelected['phone']=isset($selected['PHONE'])?$selected['PHONE']:'';
				$newSelected['email']=isset($selected['EMAIL'])?$selected['EMAIL']:'';
				break;
			case 'agent':
				$newSelected['legal']=isset($selected['LEGAL'])?$selected['LEGAL']:'N';
				$newSelected['title']=isset($selected['TITLE'])?$selected['TITLE']:'';
				$newSelected['phone']=isset($selected['PHONE'])?$selected['PHONE']:'';
				$newSelected['email']=isset($selected['EMAIL'])?$selected['EMAIL']:'';
				if($newSelected['legal']=='Y') {
					$newSelected['contact_title']=isset($selected['CONTACT_FULL_NAME'])?$selected['CONTACT_FULL_NAME']:'';
					$newSelected['contact_phone']=isset($selected['CONTACT_PHONE'])?$selected['CONTACT_PHONE']:'';
					$newSelected['contact_email']=isset($selected['CONTACT_EMAIL'])?$selected['CONTACT_EMAIL']:'';
				}
				break;
		}
		?>
		<div class="order-person-wraps-container" id="<?=$contId?>">
			<div class="order-person-info-wrap">
				<span class="order-person-info-title"></span>
				<input type="hidden" class="order-person-input-value" name="ORDER_PERSON_<?=$id?>_VALUE" value="">
			</div>
			<div class="order-person-buttons-wrap"><a style="text-decoration: underline" href="javascript:void(0)" name="orderPersonSelect" onclick="obj_<?=$id?>.ShowForm(); return false"><?=GetMessage('interface_form_edit');?></a></div>
		</div>
		<?
		$html=ob_get_contents();
		ob_end_clean();
		?>
		<script>
			<?ob_start();?>
			BX.ready(
				function()
				{
					obj_<?=$id?>=BX.OrderPerson.Set(
						'<?=$id?>',
						'<?=$contId?>',
						'<?=$type?>',
						<?=CUtil::PhpToJSObject($newSelected)?>
					);
					//BX.addCustomEvent(BX.OrderStructure, 'onSelectDirection', OrderPermAccessSelectProvider);
				}
			);

			<?
		$script=ob_get_contents();
		ob_end_clean();?>
		</script>
		<?
		return array('html'=>$html,'script'=>$script,'id'=>$id,'contId'=>$contId,'selected'=>$newSelected,'type'=>$type);
	}
	public static function GetSelector($entityType,$params) {
		ob_start();
		global $APPLICATION;
		$APPLICATION->AddHeadScript('/bitrix/js/order/order.js');
		$APPLICATION->SetAdditionalCSS('/bitrix/js/order/css/order.css');
		$APPLICATION->AddHeadScript('/bitrix/js/order/interface_form.js');
		$APPLICATION->AddHeadScript('/bitrix/js/order/common.js');
		if(!is_array($entityType))
			$entityType=array($entityType);
		$entityValue = isset($params['INPUT_VALUE']) ? $params['INPUT_VALUE'] : '';
		$containerID = "{$params['FORM_ID']}_FIELD_CONTAINER_{$params['INPUT_NAME']}";
		$selectorID = "{$params['FORM_ID']}_CHANGE_BTN_{$params['INPUT_NAME']}";
		$changeButtonID = "{$params['FORM_ID']}_CHANGE_BTN_{$params['INPUT_NAME']}";

		foreach($entityType as $i=>$ent) {
			$entityType[$i]=strtolower($ent);
		}
		if (!is_array($entityValue))
			$entityValue = array(htmlspecialcharsBack(htmlspecialcharsBack($entityValue)));
		else
		{
			$ar = array();
			foreach ($entityValue as $key=> $value)
				if (!empty($value))
					$ar[$key] = htmlspecialcharsBack(htmlspecialcharsBack($value));
			$entityValue = $ar;
		}
		$arSelected = array();
		foreach ($entityValue as $key => $value)
		{
			// Try to get raw entity ID
			$ary = explode('#_#', $value);
			if(count($ary) > 1)	{
				$value = $ary[1];
				$type=$ary[0];
			} else {
				$type=$entityType[0];
			}
			$arSelected[strtolower($type.'#_#'.$value)] = $type.'#_#'.$value;

		}
		
		$arElements = array();
		$filter=is_array($params['FILTER'])?$params['FILTER']:array();
		$arElements =COrderEntitySelectorHelper::PreparePopupItems($entityType,$filter);

		foreach($arElements as &$el) {
			if (isset($arSelected[strtolower($el['type'].'#_#'.$el['id'])]))
			{
				unset($arSelected[strtolower($el['type'].'#_#'.$el['id'])]);
				$el['selected'] = 'Y';
			}
			else
				$el['selected'] = 'N';
		}
		unset($el);
		?>
		<div id="<?=$containerID?>" style="white-space: nowrap">
			<div style="vertical-align: top; display:inline-block;">
				<a style="text-decoration: underline" id="<?=$changeButtonID?>" href="#open" onclick="obOrder[this.id].Open(); return false;" class=""><?=GetMessage('interface_form_edit');?></a>
			</div>
		</div><?

		$html=ob_get_contents();
		ob_end_clean();
		?><script type="text/javascript">
			<?ob_start();?>
			BX.ready(
				function()
				{
					ORDER.Set(
						BX('<?=CUtil::JSEscape($changeButtonID) ?>'),
						'<?=CUtil::JSEscape($selectorID)?>',
						'',
						<?=CUtil::PhpToJsObject($arElements)?>,
						false,
						false,
						<?echo CUtil::PhpToJsObject($entityType);?>,
						<?=CUtil::PhpToJsObject(COrderEntitySelectorHelper::PrepareCommonMessages())?>,
						false,
						{
							'filter':<?echo CUtil::PhpToJsObject($filter);?>
						}
					);
				}
			);
			<?$script = ob_get_clean();?>
		</script><?
		//if ($advancedInfoHTML !== '') $html.= $advancedInfoHTML;
		return array('html'=>$html,'script'=>$script);
	}
	public static function GetAddSelector($entityType,$params) {
		ob_start();
		echo '<div class="order-offer-info-data-wrap" style="white-space: nowrap">';
		global $APPLICATION;
		$APPLICATION->AddHeadScript('/bitrix/js/order/order.js');
		$APPLICATION->SetAdditionalCSS('/bitrix/js/order/css/order.css');
		$APPLICATION->AddHeadScript('/bitrix/js/order/interface_form.js');
		$APPLICATION->AddHeadScript('/bitrix/js/order/common.js');
		$entityID = '';
		$entityValue = isset($params['INPUT_VALUE']) ? $params['INPUT_VALUE'] : array();
		$editorID = "{$params['FORM_ID']}_{$params['INPUT_NAME']}";
		$containerID = "{$params['FORM_ID']}_FIELD_CONTAINER_{$params['INPUT_NAME']}";
		$selectorID = "{$params['FORM_ID']}_ENTITY_SELECTOR_{$params['INPUT_NAME']}";
		$changeButtonID = "{$params['FORM_ID']}_CHANGE_BTN_{$params['INPUT_NAME']}";
		$dataInputName = $params['FORM_ID'].'_CHANGE_BTN_'.$params['INPUT_NAME'];
		$dataInputID = "{$params['FORM_ID']}_DATA_INPUT_{$dataInputName}";
		$newDataInputName = isset($params['NEW_INPUT_NAME']) ? $params['NEW_INPUT_NAME'] : '';
		$newDataInputID = $newDataInputName !== '' ? "{$params['FORM_ID']}_NEW_DATA_INPUT_{$dataInputName}" : '';
		$entityInfo = COrderEntitySelectorHelper::PrepareEntityInfo($entityType,'', $entityValue);
		$advancedInfoHTML = COrderEntitySelectorHelper::PrepareEntityAdvancedInfoHTML($entityType, $entityInfo, array('CONTAINER_ID' => $containerID.'_descr'));
		$arMultiFields = is_array($entityInfo['ADVANCED_INFO']['MULTI_FIELDS']) ? $entityInfo['ADVANCED_INFO']['MULTI_FIELDS'] : array();
		foreach ($arMultiFields as $mf)
		{
			$entityInfo[$mf['TYPE_ID']] = trim($mf['VALUE']);
		}
		unset($entityInfo['ADVANCED_INFO']);
		unset($entityInfo['URL']);
		?><div id="<?=htmlspecialcharsbx($containerID)?>" class="bx-order-edit-order-entity-field">
			<div class="bx-order-entity-info-wrapper"><?=htmlspecialcharsEx($entityInfo['TITLE'])?></div>
			<input type="hidden" id="<?=htmlspecialcharsbx($dataInputID)?>" name="<?=htmlspecialcharsbx($dataInputName)?>" value="<?=htmlspecialcharsbx($entityID)?>" /><?
			if($newDataInputName !== ''):
				?><input type="hidden" id="<?=htmlspecialcharsbx($newDataInputID)?>" name="<?=htmlspecialcharsbx($newDataInputName)?>" value="" /><?
			endif;
			?><div class="bx-order-entity-buttons-wrapper">
				<span id="<?=htmlspecialcharsbx($changeButtonID)?>" class="bx-order-edit-order-entity-change"><?= htmlspecialcharsbx(GetMessage('interface_form_similar_select'))?></span><?
				if($newDataInputName !== ''):
					?> <span class="bx-order-edit-order-entity-add"><?=htmlspecialcharsEx(GetMessage('interface_form_add_new_entity'))?></span><?
				endif;
				?></div>
		</div><?
		$serviceUrl = '';
		$actionName = '';
		$dialogSettings = array(
			'addButtonName' => GetMessage('interface_form_add_dialog_btn_add'),
			'cancelButtonName' => GetMessage('interface_form_cancel')
		);
		if($entityType === 'PHYSICAL')
		{
			$serviceUrl = '/bitrix/components/newportal/order.physical.edit/ajax.php?'.bitrix_sessid_get();
			$actionName = 'SAVE_PHYSICAL';

			$dialogSettings['title'] = GetMessage('interface_form_add_physical_dlg_title');
			$dialogSettings['lastNameTitle'] = GetMessage('interface_form_add_physical_fld_last_name');
			$dialogSettings['nameTitle'] = GetMessage('interface_form_add_physical_fld_name');
			$dialogSettings['secondNameTitle'] = GetMessage('interface_form_add_physical_fld_second_name');
			$dialogSettings['emailTitle'] = GetMessage('interface_form_add_physical_fld_email');
			$dialogSettings['phoneTitle'] = GetMessage('interface_form_add_physical_fld_phone');
			$dialogSettings['genderTitle'] = GetMessage('interface_form_add_physical_fld_gender');
			$dialogSettings['bDayTitle'] = GetMessage('interface_form_add_physical_fld_b_day');
			$dialogSettings['descriptionTitle'] = GetMessage('interface_form_add_physical_fld_description');
			$dialogSettings['genderList'] = COrderEntitySelectorHelper::PrepareListItems(array(
				GetMessage('interface_form_add_physical_fld_gender_m')=>GetMessage('interface_form_add_physical_fld_gender_m'),
				GetMessage('interface_form_add_physical_fld_gender_f')=>GetMessage('interface_form_add_physical_fld_gender_f'),
			));
		}
		elseif($entityType === 'CONTACT')
		{
			$serviceUrl = '/bitrix/components/newportal/order.contact.edit/ajax.php?'.bitrix_sessid_get();
			$actionName = 'SAVE_CONTACT';

			$dialogSettings['title'] = GetMessage('interface_form_add_contact_dlg_title');
			$dialogSettings['lastNameTitle'] = GetMessage('interface_form_add_contact_fld_last_name');
			$dialogSettings['nameTitle'] = GetMessage('interface_form_add_contact_fld_name');
			$dialogSettings['secondNameTitle'] = GetMessage('interface_form_add_contact_fld_second_name');
			$dialogSettings['emailTitle'] = GetMessage('interface_form_add_contact_fld_email');
			$dialogSettings['phoneTitle'] = GetMessage('interface_form_add_contact_fld_phone');
		}
		elseif($entityType === 'AGENT')
		{
			$serviceUrl = '/bitrix/components/newportal/order.agent.edit/ajax.php?'.bitrix_sessid_get();
			$actionName = 'SAVE_AGENT';

			$dialogSettings['title'] = GetMessage('interface_form_add_agent_dlg_title');
			$dialogSettings['typeTitle'] = GetMessage('interface_form_add_agent_fld_type');
			$dialogSettings['legal'] = GetMessage('interface_form_add_agent_fld_legal');
			$dialogSettings['physical'] = GetMessage('interface_form_add_agent_fld_physical');
			$dialogSettings['physicalTitle'] = GetMessage('interface_form_add_agent_fld_physical_title');
			$dialogSettings['contactTitle'] = GetMessage('interface_form_add_agent_fld_contact_title');
			$dialogSettings['titleTitle'] = GetMessage('interface_form_add_agent_fld_title');
			$dialogSettings['fullTitleTitle'] = GetMessage('interface_form_add_agent_fld_full_title');
			$dialogSettings['lastNameTitle'] = GetMessage('interface_form_add_agent_fld_last_name');
			$dialogSettings['nameTitle'] = GetMessage('interface_form_add_agent_fld_name');
			$dialogSettings['secondNameTitle'] = GetMessage('interface_form_add_agent_fld_second_name');
			$dialogSettings['emailTitle'] = GetMessage('interface_form_add_agent_fld_email');
			$dialogSettings['phoneTitle'] = GetMessage('interface_form_add_agent_fld_phone');
			$dialogSettings['contactLastNameTitle'] = GetMessage('interface_form_add_agent_fld_contact_last_name');
			$dialogSettings['contactNameTitle'] = GetMessage('interface_form_add_agent_fld_contact_name');
			$dialogSettings['contactSecondNameTitle'] = GetMessage('interface_form_add_agent_fld_contact_second_name');
			$dialogSettings['contactEmailTitle'] = GetMessage('interface_form_add_agent_fld_contact_email');
			$dialogSettings['contactPhoneTitle'] = GetMessage('interface_form_add_agent_fld_contact_phone');
		}


		$html=ob_get_contents();
		ob_end_clean();
		?><script type="text/javascript">
			<?ob_start();?>
			BX.ready(
				function()
				{
					var entitySelectorId = ORDER.Set(
						BX('<?=CUtil::JSEscape($changeButtonID) ?>'),
						'<?=CUtil::JSEscape($selectorID)?>',
						'',
						<?=CUtil::PhpToJsObject(COrderEntitySelectorHelper::PreparePopupItems($entityType))?>,
						false,
						false,
						['<?=CUtil::JSEscape(strtolower($entityType))?>'],
						<?=CUtil::PhpToJsObject(COrderEntitySelectorHelper::PrepareCommonMessages())?>,
						true,
						{
							'serviceUrl': '<?= CUtil::JSEscape('/bitrix/components/newportal/order.entity.selector/ajax.php?'.bitrix_sessid_get()) ?>'
						}
					);
					BX.OrderEntityEditor.messages =
					{
						'unknownError': '<?=GetMessageJS('interface_form_ajax_unknown_error')?>',
						'prefContactType': '<?=GetMessageJS('interface_form_entity_selector_prefContactType')?>',
						'prefPhone': '<?=GetMessageJS('interface_form_entity_selector_prefPhone')?>',
						'prefEmail': '<?=GetMessageJS('interface_form_entity_selector_prefEmail')?>',
						'contactTitle': '<?=GetMessageJS('interface_form_add_agent_fld_contact_title')?>'
					};

					BX.OrderEntityEditor.create(
						'<?=CUtil::JSEscape($editorID)?>',
						{
							'typeName': '<?=CUtil::JSEscape($entityType)?>',
							'containerId': '<?=CUtil::JSEscape($containerID)?>',
							'dataInputId': '<?=CUtil::JSEscape($dataInputID)?>',
							'newDataInputId': '<?=CUtil::JSEscape($newDataInputID)?>',
							'entitySelectorId': entitySelectorId,
							'serviceUrl': '<?= CUtil::JSEscape($serviceUrl) ?>',
							'actionName': '<?= CUtil::JSEscape($actionName) ?>',
							'dialog': <?=CUtil::PhpToJSObject($dialogSettings)?>,
							'data':<?=CUtil::PhpToJSObject($entityInfo)?>
						}
					);
				}
			);
			<?$script = ob_get_clean();?>
		</script><?
		$html.= '</div>';
		if ($advancedInfoHTML !== '') $html.= $advancedInfoHTML;
		return array('html'=>$html,'script'=>$script);
	}
	public static function PrepareEntityInfo($entityTypeName, $entityID, $options = array())
	{
		$entityTypeName = strtoupper(strval($entityTypeName));
		if(!is_array($options))
		{
			$options = array();
		}
		$result = array(
			'TITLE' => "{$entityTypeName}_{$entityID}",
			'URL' => ''
		);

		/*if($entityTypeName === '' || $entityID <= 0)
		{
			return $result;
		}*/

		if($entityTypeName === 'PHYSICAL')
		{
			if($entityID!='') {
				$arRes = COrderPhysical::GetByID($entityID);

				$result['TITLE'] = $arRes['FULL_NAME'];

				$result['URL'] = CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_physical_edit'),
					array(
						'physical_id' => $arRes['ID']
					)
				);

				// advanced info
				if (isset($arRes['EMAIL']) && trim($arRes['EMAIL']) != '')
					$result['ADVANCED_INFO']['MULTI_FIELDS'][] = array(
						'TYPE_ID' => 'EMAIL',
						'VALUE' => $arRes['EMAIL']
					);
				if (isset($arRes['PHONE']) && trim($arRes['PHONE']) != '')
					$result['ADVANCED_INFO']['MULTI_FIELDS'][] = array(
						'TYPE_ID' => 'PHONE',
						'VALUE' => $arRes['PHONE']
					);
			}
			else {

				$result['TITLE'] = $options['FULL_NAME'];

				if (isset($options['EMAIL']) && trim($options['EMAIL']) != '')
					$result['ADVANCED_INFO']['MULTI_FIELDS'][] = array(
						'TYPE_ID' => 'EMAIL',
						'VALUE' => $options['EMAIL']
					);
				if (isset($options['PHONE']) && trim($options['PHONE']) != '')
					$result['ADVANCED_INFO']['MULTI_FIELDS'][] = array(
						'TYPE_ID' => 'PHONE',
						'VALUE' => $options['PHONE']
					);
			}
		}
		elseif($entityTypeName === 'CONTACT')
		{
			if($entityID!='') {
				$arRes = COrderContact::GetByID($entityID);

				$result['TITLE'] = $arRes['FULL_NAME'];

				$result['URL'] = CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_contact_edit'),
					array(
						'contact_id' => $arRes['ID']
					)
				);

				// advanced info
				if (isset($arRes['EMAIL']) && trim($arRes['EMAIL']) != '')
					$result['ADVANCED_INFO']['MULTI_FIELDS'][] = array(
						'TYPE_ID' => 'EMAIL',
						'VALUE' => $arRes['EMAIL']
					);
				if (isset($arRes['PHONE']) && trim($arRes['PHONE']) != '')
					$result['ADVANCED_INFO']['MULTI_FIELDS'][] = array(
						'TYPE_ID' => 'PHONE',
						'VALUE' => $arRes['PHONE']
					);
			}
			else {

				$result['TITLE'] = $options['FULL_NAME'];

				if (isset($options['EMAIL']) && trim($options['EMAIL']) != '')
					$result['ADVANCED_INFO']['MULTI_FIELDS'][] = array(
						'TYPE_ID' => 'EMAIL',
						'VALUE' => $options['EMAIL']
					);
				if (isset($options['PHONE']) && trim($options['PHONE']) != '')
					$result['ADVANCED_INFO']['MULTI_FIELDS'][] = array(
						'TYPE_ID' => 'PHONE',
						'VALUE' => $options['PHONE']
					);
			}
		}
		elseif($entityTypeName === 'AGENT')
		{
			if($entityID!='') {
				$arRes = COrderAgent::GetByID($entityID);

				$result['TITLE'] = $arRes['TITLE'];

				$result['URL'] = CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_agent_edit'),
					array(
						'agent_id' => $arRes['ID']
					)
				);

				// advanced info
				if (isset($arRes['EMAIL']) && trim($arRes['EMAIL']) != '')
					$result['ADVANCED_INFO']['MULTI_FIELDS'][] = array(
						'TYPE_ID' => 'EMAIL',
						'VALUE' => $arRes['EMAIL']
					);
				if (isset($arRes['PHONE']) && trim($arRes['PHONE']) != '')
					$result['ADVANCED_INFO']['MULTI_FIELDS'][] = array(
						'TYPE_ID' => 'PHONE',
						'VALUE' => $arRes['PHONE']
					);
				if(isset($arRes['LEGAL']) && $arRes['LEGAL'] == 'Y') {
					if (isset($arRes['CONTACT_ID']) && trim($arRes['CONTACT_ID']) != '')
						$result['ADVANCED_INFO']['CONTACT']['ID'] = $arRes['CONTACT_ID'];

					if (isset($arRes['CONTACT_FULL_NAME']) && trim($arRes['CONTACT_FULL_NAME']) != '')
						$result['ADVANCED_INFO']['CONTACT']['TITLE'] = $arRes['CONTACT_FULL_NAME'];


					if (isset($arRes['CONTACT_EMAIL']) && trim($arRes['CONTACT_EMAIL']) != '')
						$result['ADVANCED_INFO']['CONTACT']['MULTI_FIELDS'][] = array(
							'TYPE_ID' => 'EMAIL',
							'VALUE' => $arRes['CONTACT_EMAIL']
						);
					if (isset($arRes['CONTACT_PHONE']) && trim($arRes['CONTACT_PHONE']) != '')
						$result['ADVANCED_INFO']['CONTACT']['MULTI_FIELDS'][] = array(
							'TYPE_ID' => 'PHONE',
							'VALUE' => $arRes['CONTACT_PHONE']
						);
				}
			}
			else {

				$result['TITLE'] = $options['TITLE'];
				$result['LEGAL'] = $options['LEGAL'];

				if (isset($options['EMAIL']) && trim($options['EMAIL']) != '')
					$result['ADVANCED_INFO']['MULTI_FIELDS'][] = array(
						'TYPE_ID' => 'EMAIL',
						'VALUE' => $options['EMAIL']
					);
				if (isset($options['PHONE']) && trim($options['PHONE']) != '')
					$result['ADVANCED_INFO']['MULTI_FIELDS'][] = array(
						'TYPE_ID' => 'PHONE',
						'VALUE' => $options['PHONE']
					);
				if($options['LEGAL']=='Y') {
					$result['CONTACT_FULL_NAME'] = $options['CONTACT_FULL_NAME'];
					$result['CONTACT_PHONE'] = $options['CONTACT_PHONE'];
					$result['CONTACT_EMAIL'] = $options['CONTACT_EMAIL'];

					if (isset($options['CONTACT_FULL_NAME']) && trim($options['CONTACT_FULL_NAME']) != '') {
						$result['ADVANCED_INFO']['CONTACT']['TITLE'] = $options['CONTACT_FULL_NAME'];
						$result['ADVANCED_INFO']['CONTACT']['ID'] = '';

						if (isset($options['CONTACT_EMAIL']) && trim($options['CONTACT_EMAIL']) != '')
							$result['ADVANCED_INFO']['CONTACT']['MULTI_FIELDS'][] = array(
								'TYPE_ID' => 'EMAIL',
								'VALUE' => $options['CONTACT_EMAIL']
							);
						if (isset($options['CONTACT_PHONE']) && trim($options['CONTACT_PHONE']) != '')
							$result['ADVANCED_INFO']['CONTACT']['MULTI_FIELDS'][] = array(
								'TYPE_ID' => 'PHONE',
								'VALUE' => $options['CONTACT_PHONE']
							);
					}
				}
			}
		}
		elseif($entityTypeName === 'STAFF') {
			$user = COrderStaff::GetByID($entityID);



			$result['TITLE'] = $user['NAME'].' '.$user['LAST_NAME'];

			$result['URL'] = CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_staff_edit'),
				array(
					'user_id' => $user['ID']
				)
			);

			// advanced info
			if (isset($user['EMAIL']) && trim($user['EMAIL']) != '')
				$result['ADVANCED_INFO']['MULTI_FIELDS'][] = array(
					'TYPE_ID' => 'EMAIL',
					'VALUE' => $user['EMAIL']
				);

		}


		return $result;
	}

	public static function PreparePopupItems($entityTypeNames,$arFilter=[])
	{
		if(!is_array($entityTypeNames))
		{
			$entityTypeNames = array(strval($entityTypeNames));

		}


		$arItems = array();
		$i = 0;
		foreach($entityTypeNames as $typeName)
		{
			$typeName = strtoupper(strval($typeName));

			if($typeName === 'PHYSICAL')
			{
				$res = COrderPhysical::GetListEx(
					array(),
					array(),
					false,
					false,
					array('ID','FULL_NAME','EMAIL','PHONE')
				);

				while($el=$res->Fetch())
				{


					$arItems[$i] = array(
						'title' => $el['FULL_NAME'],
						'desc'  => $el['EMAIL'],
						'id' => $el['ID'],
						'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_physical_edit'),
							array(
								'physical_id' => $el['ID']
							)
						),
						'type'  => 'physical',
						'selected' => 'N'
					);
					// advanced info
					if(isset($el['EMAIL']) && trim($el['EMAIL'])!='')
						$arItems[$i]['advancedInfo']['multiFields'][] = array(
							'TYPE_ID' => 'EMAIL',
							'VALUE' => $el['EMAIL']
						);
					if(isset($el['PHONE']) && trim($el['PHONE'])!='')
						$arItems[$i]['advancedInfo']['multiFields'][] = array(
							'TYPE_ID' => 'PHONE',
							'VALUE' => $el['PHONE']
						);
					$i++;

				}


				unset($res);
			}
			elseif($typeName === 'CONTACT')
			{
				$res = COrderContact::GetListEx(
					array(),
					array(),
					false,
					false,
					array('ID','FULL_NAME','EMAIL','PHONE')
				);

				while($el=$res->Fetch())
				{


					$arItems[$i] = array(
						'title' => $el['FULL_NAME'],
						'desc'  => $el['EMAIL'],
						'id' => $el['ID'],
						'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_contact_edit'),
							array(
								'contact_id' => $el['ID']
							)
						),
						'type'  => 'contact',
						'selected' => 'N'
					);
					// advanced info
					if(isset($el['EMAIL']) && trim($el['EMAIL'])!='')
						$arItems[$i]['advancedInfo']['multiFields'][] = array(
							'TYPE_ID' => 'EMAIL',
							'VALUE' => $el['EMAIL']
						);
					if(isset($el['PHONE']) && trim($el['PHONE'])!='')
						$arItems[$i]['advancedInfo']['multiFields'][] = array(
							'TYPE_ID' => 'PHONE',
							'VALUE' => $el['PHONE']
						);
					$i++;

				}


				unset($res);
			}
			elseif($typeName === 'AGENT')
			{
				$res = COrderAgent::GetListEx();

				while($el=$res->Fetch())
				{


					$arItems[$i] = array(
						'title' => $el['TITLE'],
						'desc'  => $el['TYPE_TITLE'],
						'id' => $el['ID'],
						'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_agent_edit'),
							array(
								'agent_id' => $el['ID']
							)
						),
						'type'  => 'agent',
						'selected' => 'N'
					);
					if(isset($el['LEGAL']) && trim($el['LEGAL'])!='')
						$arItems[$i]['advancedInfo']['legal'] = $el['LEGAL'];

					if(isset($el['LEGAL']) && $el['LEGAL']=='Y' && $el['CONTACT_ID']!='') {
						// advanced info
						if(isset($el['LEGAL_EMAIL']) && trim($el['LEGAL_EMAIL'])!='')
							$arItems[$i]['advancedInfo']['multiFields'][] = array(
								'TYPE_ID' => 'EMAIL',
								'VALUE' => $el['LEGAL_EMAIL']
							);
						if(isset($el['LEGAL_PHONE']) && trim($el['LEGAL_PHONE'])!='')
							$arItems[$i]['advancedInfo']['multiFields'][] = array(
								'TYPE_ID' => 'PHONE',
								'VALUE' => $el['LEGAL_PHONE']
							);
						$arItems[$i]['advancedInfo']['contact'] = array(
							'id'=>$el['CONTACT_ID'],
							'title'=>$el['CONTACT_FULL_NAME']
						);
						if(isset($el['CONTACT_EMAIL']) && trim($el['CONTACT_EMAIL'])!='')
							$arItems[$i]['advancedInfo']['contact']['multiFields'][] = array(
								'TYPE_ID' => 'EMAIL',
								'VALUE' => $el['CONTACT_EMAIL']
							);
						if(isset($el['CONTACT_PHONE']) && trim($el['CONTACT_PHONE'])!='')
							$arItems[$i]['advancedInfo']['contact']['multiFields'][] = array(
								'TYPE_ID' => 'PHONE',
								'VALUE' => $el['CONTACT_PHONE']
							);
					} else {
						// advanced info
						if(isset($el['EMAIL']) && trim($el['EMAIL'])!='')
							$arItems[$i]['advancedInfo']['multiFields'][] = array(
								'TYPE_ID' => 'EMAIL',
								'VALUE' => $el['EMAIL']
							);
						if(isset($el['PHONE']) && trim($el['PHONE'])!='')
							$arItems[$i]['advancedInfo']['multiFields'][] = array(
								'TYPE_ID' => 'PHONE',
								'VALUE' => $el['PHONE']
							);
					}
					$i++;

				}


				unset($res);
			}
			elseif($typeName === 'DIRECTION')
			{
				$res = COrderDirection::GetListEx(
					array(),
					array(),
					false,
					false,
					array('ID','TITLE','DESCRIPTION')
				);

				while($el=$res->Fetch())
				{


					$arItems[$i] = Array(
						'title' => $el['TITLE'],
						'desc' => $el['DESCRIPTION'],
						'id' => $el['ID'],
						'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_direction_edit'),
							array(
								'direction_id' => $el['ID']
							)
						),
						'type'  => 'direction',
						'selected' => 'N'
					);
					$i++;
				}
				unset($res);
			}
			elseif($typeName === 'NOMEN')
			{
				$res = COrderNomen::GetListEx(
					array(),
					array(),
					false,
					false,
					array('ID','TITLE','DESCRIPTION')
				);

				while($el=$res->Fetch())
				{


					$arItems[$i] = Array(
						'title' => $el['TITLE'],
						'desc' => $el['DESCRIPTION'],
						'id' => $el['ID'],
						'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_nomen_edit'),
							array(
								'nomen_id' => $el['ID']
							)
						),
						'type'  => 'nomen',
						'selected' => 'N'
					);
					$i++;
				}
				unset($res);
			}
			elseif($typeName === 'COURSE')
			{
				$res = COrderCourse::GetListEx(
					array(),
					array(),
					false,
					false,
					array('ID','TITLE','DESCRIPTION')
				);

				while($el=$res->Fetch())
				{

					$arItems[$i] = Array(
						'title' => $el['TITLE'],
						'desc' => $el['DESCRIPTION'],
						'id' => $el['ID'],
						'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_course_edit'),
							array(
								'course_id' => $el['ID']
							)
						),
						'type'  => 'course',
						'selected' => 'N'
					);
					$i++;
				}
				unset($res);
			}
			elseif($typeName === 'GROUP')
			{
				$res = COrderGroup::GetListEx(
					array(),
					array(),
					false,
					false,
					array('ID','TITLE','DESCRIPTION')
				);

				while($el=$res->Fetch())
				{

					$arItems[$i] = Array(
						'title' => $el['TITLE'],
						'desc' => $el["DESCRIPTION"],
						'id' => $el['ID'],
						'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_group_edit'),
							array(
								'group_id' => $el['ID']
							)
						),
						'type'  => 'group',
						'selected' => 'N'
					);
					$i++;
				}
				unset($res);
			}
			elseif($typeName === 'FORMED_GROUP')
			{
				$res = COrderFormedGroup::GetListEx(
					array(),
					array(),
					false,
					false,
					array('ID','GROUP_TITLE','DESCRIPTION','ENROLLED','FREE','MAX','DATE_START','DATE_END')
				);

				while($el=$res->Fetch())
				{

					$arItems[$i] = Array(
						'title' => $el['GROUP_TITLE'].' ('.$el["DATE_START"].'-'.$el["DATE_END"].')',
						'desc' => $el['ENROLLED'].'/'.$el["FREE"].'/'.$el["MAX"],
						'date_start'=>$el["DATE_START"],
						'date_end'=>$el["DATE_END"],
						'enrolled'=>$el["ENROLLED"],
						'free'=>$el["FREE"],
						'max'=>$el["MAX"],
						'id' => $el['ID'],
						'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_formed_group_edit'),
							array(
								'formed_group_id' => $el['ID']
							)
						),
						'type'  => 'formed_group',
						'selected' => 'N'
					);
					$i++;
				}
				unset($res);
			}
			elseif($typeName === 'APP')
			{
				$res = COrderApp::GetListEx(
					array(),
					$arFilter,
					false,
					false,
					array('ID','DESCRIPTION')
				);

				while($el=$res->Fetch())
				{

					$arItems[$i] = Array(
						'title' => '['.$el['ID'].']',
						'desc' => $el['DESCRIPTION'],
						'id' => $el['ID'],
						'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_app_edit'),
							array(
								'app_id' => $el['ID']
							)
						),
						'type'  => 'app',
						'selected' => 'N'
					);
					$i++;
				}
				unset($res);
			}
			elseif($typeName === 'REG')
			{
				$res = COrderReg::GetListEx(
					array(),
					array(),
					false,
					false,
					array('ID','DESCRIPTION')
				);

				while($el=$res->Fetch())
				{

					$arItems[$i] = Array(
						'title' => '['.$el['ID'].']',
						'desc' => $el['DESCRIPTION'],
						'id' => $el['ID'],
						'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_reg_edit'),
							array(
								'reg_id' => $el['ID']
							)
						),
						'type'  => 'reg',
						'selected' => 'N'
					);
					$i++;
				}
				unset($res);
			}
			elseif($typeName === 'STAFF')
			{
				$res = COrderStaff::GetListEx(
					array(),
					array(),
					false,
					false,
					array('ID','FULL_NAME','EMAIL')
				);

				while($el=$res->Fetch())
				{

					$arItems[$i] = array(
						'title' => $el["FULL_NAME"],
						'desc' => $el['EMAIL'],
						'id' => $el['ID'],
						'url' => CComponentEngine::MakePathFromTemplate(COption::GetOptionString('order', 'path_to_staff_edit'),
							array(
								'user_id' => $el['ID']
							)
						),
						'type' => 'staff',
						'selected' => 'N'
					);
					// advanced info

					if (isset($el['EMAIL']) && trim($el['EMAIL']) != '')
						$arItems[$i]['advancedInfo']['multiFields'][] = array(
							'TYPE_ID' => 'EMAIL',
							'VALUE' => $el['EMAIL']
						);
					$i++;
				}



				unset($res);
			}



		}
		unset($typeName);

		return $arItems;
	}

	public static function PrepareListItems($arSource)
	{
		$result = array();
		if(is_array($arSource))
		{
			foreach($arSource as $k => &$v)
			{
				$result[] = array('value' => $k, 'text' => $v);
			}
			unset($v);
		}
		return $result;
	}

	public static function PrepareCommonMessages()
	{
		return array(
			'physical' => GetMessage('ORDER_FF_PHYSICAL'),
			'contact' => GetMessage('ORDER_FF_CONTACT'),
			'agent' => GetMessage('ORDER_FF_AGENT'),
			'direction'=> GetMessage('ORDER_FF_DIRECTION'),
			'nomen'=> GetMessage('ORDER_FF_NOMEN'),
			'course'=> GetMessage('ORDER_FF_COURSE'),
			'group'=> GetMessage('ORDER_FF_GROUP'),
			'formed_group'=> GetMessage('ORDER_FF_FORMED_GROUP'),
			'reg'=> GetMessage('ORDER_FF_REG'),
			'app' => GetMessage('ORDER_FF_APP'),
			'staff'=> GetMessage('ORDER_FF_STAFF'),
			'teacher'=> GetMessage('ORDER_FF_TEACHER'),
			'room'=> GetMessage('ORDER_FF_ROOM'),
			'schedule'=> GetMessage('ORDER_FF_SCHEDULE'),
			'mark'=> GetMessage('ORDER_FF_MARK'),
			'lead'=> GetMessage('ORDER_FF_LEAD'),
			'ok' => GetMessage('ORDER_FF_OK'),
			'cancel' => GetMessage('ORDER_FF_CANCEL'),
			'close' => GetMessage('ORDER_FF_CLOSE'),
			'wait' => GetMessage('ORDER_FF_WAIT'),
			'noresult' => GetMessage('ORDER_FF_NO_RESULT'),
			'add' => GetMessage('ORDER_FF_CHOICE'),
			'add_similar' => GetMessage('ORDER_FF_CHOICE_SIMILAR'),
			'edit' => GetMessage('ORDER_FF_CHANGE'),
			'search' => GetMessage('ORDER_FF_SEARCH'),
			'last' => GetMessage('ORDER_FF_LAST')
		);
	}

	public static function PrepareEntityTitles()
	{
		return array(
			'PHYSICAL' => GetMessage('ORDER_TITLE_PHYSICAL'),
			'CONTACT' => GetMessage('ORDER_TITLE_CONTACT'),
			'AGENT' => GetMessage('ORDER_TITLE_AGENT'),
			'DIRECTION'=> GetMessage('ORDER_TITLE_DIRECTION'),
			'NOMEN'=> GetMessage('ORDER_TITLE_NOMEN'),
			'COURSE'=> GetMessage('ORDER_TITLE_COURSE'),
			'GROUP'=> GetMessage('ORDER_TITLE_GROUP'),
			'FORMED_GROUP'=> GetMessage('ORDER_TITLE_FORMED_GROUP'),
			'REG'=> GetMessage('ORDER_TITLE_REG'),
			'APP' => GetMessage('ORDER_TITLE_APP'),
			//'STAFF'=> GetMessage('ORDER_TITLE_STAFF'),
			'TEACHER'=> GetMessage('ORDER_TITLE_TEACHER'),
			'ROOM'=> GetMessage('ORDER_TITLE_ROOM'),
			'SCHEDULE'=> GetMessage('ORDER_TITLE_SCHEDULE'),
			'MARK'=> GetMessage('ORDER_TITLE_MARK')
		);
	}
	
	public static function PrepareEntityAdvancedInfoHTML($entityTypeName, $entityInfo = array(), $options = array())
	{
		$result = '';

		// multifields
		$arPhone = array();
		$arEmail = array();
		$arMultiFields = is_array($entityInfo['ADVANCED_INFO']['MULTI_FIELDS']) ? $entityInfo['ADVANCED_INFO']['MULTI_FIELDS'] : array();
		foreach ($arMultiFields as $mf)
		{
			if (isset($mf['TYPE_ID']) && $mf['TYPE_ID'] === 'PHONE')
			{
				$arPhone[] = array('VALUE' => trim(strval($mf['VALUE'])));
			}
			if (isset($mf['TYPE_ID']) && $mf['TYPE_ID'] === 'EMAIL')
			{
				$arEmail[] = array('VALUE' => trim(strval($mf['VALUE'])));
			}
		}
		unset($arMultiFields);
		if(is_array($entityInfo['ADVANCED_INFO']['CONTACT'])) {
			// multifields
			$contactId=$entityInfo['ADVANCED_INFO']['CONTACT']['ID'];
			$contactTitle=$entityInfo['ADVANCED_INFO']['CONTACT']['TITLE'];
			$arContactPhone = array();
			$arContactEmail = array();
			$arMultiFields = is_array($entityInfo['ADVANCED_INFO']['CONTACT']['MULTI_FIELDS']) ? $entityInfo['ADVANCED_INFO']['CONTACT']['MULTI_FIELDS'] : array();
			foreach ($arMultiFields as $mf) {
				if (isset($mf['TYPE_ID']) && $mf['TYPE_ID'] === 'PHONE') {
					$arContactPhone[] = array('VALUE' => trim(strval($mf['VALUE'])));
				}
				if (isset($mf['TYPE_ID']) && $mf['TYPE_ID'] === 'EMAIL') {
					$arContactEmail[] = array('VALUE' => trim(strval($mf['VALUE'])));
				}
			}
			unset($arMultiFields);
		}

		$containerID = isset($options['CONTAINER_ID']) ? $options['CONTAINER_ID'] : '';

		$result .= '<div'.($containerID != '' ? ' id="'.htmlspecialcharsbx($containerID).'"' : '').
			' class="order-offer-info-description">';

		switch (ToUpper($entityTypeName))
		{
			case 'CONTACT':
			case 'PHYSICAL':
			case 'STAFF':
				if (!empty($arPhone))
				{
					$result .=
						"\t" .
						'<span class="order-offer-info-descrip-tem order-offer-info-descrip-tel">'.
						GetMessage('ORDER_ENT_SEL_HLP_PREF_PHONE').': '.htmlspecialcharsbx($arPhone[0]['VALUE']).
						'<a href="callto:'.htmlspecialcharsbx($arPhone[0]['VALUE']).
						'" class="order-offer-info-descrip-icon"></a>'.
						'</span><br/>';
				}
				if (!empty($arEmail))
				{
					$result .=
						"\t" .
						'<span class="order-offer-info-descrip-tem order-offer-info-descrip-email">'.
						GetMessage('ORDER_ENT_SEL_HLP_PREF_EMAIL').': '.htmlspecialcharsbx($arEmail[0]['VALUE']).
						'<a href="mailto:'.htmlspecialcharsbx($arEmail[0]['VALUE']).
						'" class="order-offer-info-descrip-icon"></a>'.
						'</span><br/>';
				}
				break;
			case 'AGENT':
				if (!empty($arPhone))
				{
					$result .=
						"\t" .
						'<span class="order-offer-info-descrip-tem order-offer-info-descrip-tel">'.
						GetMessage('ORDER_ENT_SEL_HLP_PREF_PHONE').': '.htmlspecialcharsbx($arPhone[0]['VALUE']).
						'<a href="callto:'.htmlspecialcharsbx($arPhone[0]['VALUE']).
						'" class="order-offer-info-descrip-icon"></a>'.
						'</span><br/>';
				}
				if (!empty($arEmail))
				{
					$result .=
						"\t" .
						'<span class="order-offer-info-descrip-tem order-offer-info-descrip-email">'.
						GetMessage('ORDER_ENT_SEL_HLP_PREF_EMAIL').': '.htmlspecialcharsbx($arEmail[0]['VALUE']).
						'<a href="mailto:'.htmlspecialcharsbx($arEmail[0]['VALUE']).
						'" class="order-offer-info-descrip-icon"></a>'.
						'</span><br/>';
				}
				if(isset($contactTitle) && $contactTitle!='') {
					$result .=
						"\t" .
						'<span class="order-offer-info-descrip-tem">' .
						GetMessage('interface_form_add_agent_fld_contact_title') . ': ' . htmlspecialcharsbx($contactTitle) .
						'</span><br/>';
					if (!empty($arContactPhone)) {
						$result .=
							"\t" .
							'<span class="order-offer-info-descrip-tem order-offer-info-descrip-tel">' .
							GetMessage('ORDER_ENT_SEL_HLP_PREF_PHONE') . ': ' . htmlspecialcharsbx($arContactPhone[0]['VALUE']) .
							'<a href="callto:' . htmlspecialcharsbx($arContactPhone[0]['VALUE']) .
							'" class="order-offer-info-descrip-icon"></a>' .
							'</span><br/>';
					}
					if (!empty($arContactEmail)) {
						$result .=
							"\t" .
							'<span class="order-offer-info-descrip-tem order-offer-info-descrip-email">' .
							GetMessage('ORDER_ENT_SEL_HLP_PREF_EMAIL') . ': ' . htmlspecialcharsbx($arContactEmail[0]['VALUE']) .
							'<a href="mailto:' . htmlspecialcharsbx($arContactEmail[0]['VALUE']) .
							'" class="order-offer-info-descrip-icon"></a>' .
							'</span><br/>';
					}
				}
				break;
		}

		$result .= '</div>';

		return $result;
	}

	public static function getStructure() {
		$res=COrderDirection::GetListEx(array(),array('!ID'=>'000000000'));
		while($el=$res->Fetch()) {
			$arDirection[$el['ID']]=$el;
		}
		$res=COrderNomen::GetListEx();
		while($el=$res->Fetch()) {
			$arNomen[$el['ID']]=$el;
		}
		$res=COrderGroup::GetListEx();
		while($el=$res->Fetch()) {
			$arGroup[$el['ID']]=$el;
		}
		$res=COrderFormedGroup::GetListEx();
		while($el=$res->Fetch()) {
			$arGroup[$el['GROUP_ID']]['CHILD_FORMED_GROUPS'][$el['ID']]=$el;
		}
		foreach($arGroup as $num=>$el) {
			$arNomen[$el['NOMEN_ID']]['CHILD_GROUPS'][$el['ID']]=$el;
		}
		foreach($arNomen as $num=>$el) {
			$arDirection[$el['DIRECTION_ID']]['CHILD_NOMENS'][$el['ID']]=$el;
		}
		$tree=COrderDirection::GetTreeMenu($arDirection);
		return $tree;
	}
}
