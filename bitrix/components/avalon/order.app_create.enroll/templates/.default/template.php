<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?if ($_GET["formresult"] == "addok"):?><?=GetMessage('FORM_DATA_SAVED')?></p>

<?else:?>
	<?
	foreach($arResult["ERROR"] as $error)
	{
		ShowError($error);
	}
	?>
	<style>
		input[type='text'], input[type='email'], input[type='tel'], input[type='number'], textarea, td > select {
			width:100%;
		}
	</style>
	<?=GetMessage("DESCRIPTION")?>
	<?=$arResult["FORM_HEADER"]?>
	<?
	//var_dump($arResult['arrVALUES']);
	?>

	<?
	/***********************************************************************************
	form questions
	 ***********************************************************************************/
	?>
	<table class="form-table data-table" style="width:70%">
		<thead>
		</thead>
		<tbody>
		<tr>
			<td><font color="red">*</font><?=GetMessage('AGENT_TYPE')?></td>
			<td>
				<div class="type_face"><input type="radio" name="AGENT_TYPE" id="FACE_TYPE_1" value="P" onchange="PhysicalMode()" <?=$arResult["arrVALUES"]['AGENT_TYPE'] != 'L'?'checked':''?> ><label for="FACE_TYPE_1"><?=GetMessage('PHYSICAL_TYPE')?></label></div>
				<div class="type_face"><input type="radio" name="AGENT_TYPE" id="FACE_TYPE_2" value="L" onchange="LegalMode()" <?=$arResult["arrVALUES"]['AGENT_TYPE'] == 'L'?'checked':''?> ><label for="FACE_TYPE_2"><?=GetMessage('LEGAL_TYPE')?></label> </div>
			</td>
		</tr>
		<tr>
			<td width="40%"><font color="red">*</font><?=GetMessage('AGENT_TITLE')?></td>
			<td width="60%"><input id="agent_title" type="text" name="AGENT[TITLE]" value="<?=htmlspecialcharsEx($arResult['arrVALUES']['AGENT']['TITLE'])?>"> </td>
		</tr>
		<tr>
			<td><font color="red">*</font><?=GetMessage('AGENT_PHONE')?></td>
			<td><input type="tel" name="AGENT[PHONE]" value="<?=$arResult['arrVALUES']['AGENT']['PHONE']?>"> </td>
		</tr>
		<tr>
			<td><font color="red">*</font><?=GetMessage('AGENT_EMAIL')?></td>
			<td><input type="email" name="AGENT[EMAIL]" value="<?=$arResult['arrVALUES']['AGENT']['EMAIL']?>"> </td>
		</tr>

		<tr style="display:none" name="CONTACT_INFO">
			<td><font color="red">*</font><?=GetMessage('CONTACT_NAME')?></td>
			<td><input type="text" name="CONTACT[NAME]" value="<?=htmlspecialcharsEx($arResult['arrVALUES']['CONTACT']['NAME'])?>"> </td>
		</tr>
		<tr style="display:none" name="CONTACT_INFO">
			<td><font color="red">*</font><?=GetMessage('CONTACT_PHONE')?></td>
			<td><input type="tel" name="CONTACT[PHONE]" value="<?=$arResult['arrVALUES']['CONTACT']['PHONE']?>"> </td>
		</tr>
		<tr style="display:none" name="CONTACT_INFO">
			<td><font color="red">*</font><?=GetMessage('CONTACT_EMAIL')?></td>
			<td><input type="email" name="CONTACT[EMAIL]" value="<?=$arResult['arrVALUES']['CONTACT']['EMAIL']?>"> </td>
		</tr>

		<tr>
			<td><?=GetMessage('CLIENT_PAST')?></td>
			<td><input type="checkbox" name="PAST" value="Y" <?=$arResult['arrVALUES']['PAST']=='Y'?'checked':'';?>> </td>
		</tr>
		<tr>
			<td><?=GetMessage('CLIENT_SOURCE')?></td>
			<td>

				<select name="SOURCE">
				<?foreach( $arResult['SRC_LIST'] as $opt):?>-->
					<option value="<?=$opt?>" <?=$arResult['arrVALUES']['SOURCE']==$opt?'selected':'';?>><?=$opt?></option>
				<?endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?=GetMessage('CLIENT_DESCRIPTION')?></td>
			<td><textarea name="DESCRIPTION" rows="5" style="resize:none;"><?=$arResult['arrVALUES']['DESCRIPTION']?></textarea> </td>
		</tr>

		<?foreach ($arResult['ENTITY'] as $arEntity):?>
		<?
		if(count($arResult['arrVALUES']['REG'][$arEntity['TYPE']][$arEntity['ID']])<1)
		{
			$arResult['arrVALUES']['REG'][$arEntity['TYPE']][$arEntity['ID']][0]=Array('NAME'=>'');
		}
		?>
			<tr>
				<td colspan="2">
					<div style="border-top: 1px solid black;">
						<div class="div-entity-title">
							<?switch(strtolower($arEntity['TYPE'])) {
								case 'formed_group':?>
									<p><b><?=$arEntity['NOMEN_TITLE']?></b></p>
									<p>
										<?=GetMessage('DATE_START')?>
										<select>
											<option value="<?=$arEntity['ID']?>"><?=$arEntity['DATE_START']?></option>
											<?foreach ($arEntity['SIBLINGS'] as $fGroup):?>
												<option value="<?=$fGroup['ID']?>"><?=$fGroup['DATE_START']?></option>
											<?endforeach;?>
											<option value="nomen"><?=GetMessage('FREE_DATE')?></option>
										</select>
									</p>
									<?break;
								case 'nomen':?>
									<b><?=$arEntity['TITLE']?></b>
									<p>
										<?=GetMessage('DATE_START')?>
										<select>
											<?foreach ($arEntity['FORMED_GROUP'] as $fGroup):?>
												<option value="<?=$fGroup['ID']?>"><?=$fGroup['DATE_START']?></option>
											<?endforeach;?>
											<option value="nomen"><?=GetMessage('FREE_DATE')?></option>
										</select>
									</p>
									<?break;
								case 'direction':
									echo $arEntity['TITLE'];
									break;
							}?>
						</div>
						<div class="div-entity-delete"><span class="bx-order-btn-delete" onclick='deleteEntity(this)'></span></div>


					</div>
					<p><?=GetMessage('PERSON_COUNT')?> <input onkeydown="if (event.which == 13) {event.preventDefault();}" min="0" max="999" style="width: 3em" type="number" oninput="changeCount(this)" id="<?=$arEntity['TYPE']?>_<?=$arEntity['ID']?>_personCount" name="personCount" value="<?=count($arResult['arrVALUES']['REG'][$arEntity['TYPE']][$arEntity['ID']])?>"></p>
					<table class="entity">
						<thead>
						<tr>
							<th width="5%"><?=GetMessage('PERSON_NUMBER')?></th>
							<th><?=GetMessage('PERSON_NAME')?></th>
							<th width="5%"><?=GetMessage('PERSON_COL_PAST')?></th>
							<th width="20%"><button id="person_add" style="white-space: nowrap; width: 100%" name="person_add" person_number="<?=count($arResult['arrVALUES']['REG'][$arEntity['TYPE']][$arEntity['ID']])?>" entity_type="<?=$arEntity['TYPE']?>" entity_id="<?=$arEntity['ID']?>" onclick="addPerson(this); return false;"><?=GetMessage('PERSON_ADD')?></button></th>
							<th width="5%"><?=GetMessage('PERSON_COL_DELETE')?></th>
						</tr>
						</thead>
						<tbody>

						<?foreach ($arResult['arrVALUES']['REG'][$arEntity['TYPE']][$arEntity['ID']] as $key => $arReg):?>
							<tr>
								<td><?=$key+1?></td>
								<td><input type="text" name="REG[<?=$arEntity['TYPE']?>][<?=$arEntity['ID']?>][<?=$key?>][NAME]" value="<?=$arReg['NAME']?>"></td>
								<td><input type="checkbox" name="REG[<?=$arEntity['TYPE']?>][<?=$arEntity['ID']?>][<?=$key?>][PAST]" value="Y" <?=($arReg['PAST'] == 'Y')?'checked':''?> ></td>
								<td><button style="white-space: nowrap; width: 100%" onclick="typeTitle(this); return false;"><?=GetMessage("LIKE_AN_AGENT")?></button></td>
								<td><span class="bx-order-btn-delete" onclick='deletePerson(this)'></span></td>
							</tr>
						<?endforeach;?>

						</tbody>
					</table>
				</td>
			</tr>
		<?endforeach;?>
		<tr>
			<td colspan="2">
				<button onclick="addEntity()"><?=GetMessage('ENTITY_ADD')?></button>
				<select id="newEntityDirectionRoot">
					<option value="DIRECTION"><?=GetMessage('ENTITY_TITLE_ADD_'.$arResult["ENTITY_TYPE"])?></option>
					<option value="/"><?=GetMessage('ENTITY_TITLE_ADD_'.$arResult["ENTITY_TYPE"])?></option>
					<option value="DIRECTION"><?=GetMessage('ENTITY_TITLE_ADD_'.$arResult["ENTITY_TYPE"])?></option>
				</select>
				<select id="newEntityDirection">
					<option value="DIRECTION"><?=GetMessage('ENTITY_TITLE_ADD_'.$arResult["ENTITY_TYPE"])?></option>
					<option value="/"><?=GetMessage('ENTITY_TITLE_ADD_'.$arResult["ENTITY_TYPE"])?></option>
					<option value="DIRECTION"><?=GetMessage('ENTITY_TITLE_ADD_'.$arResult["ENTITY_TYPE"])?></option>
				</select>
				<select id="newEntityNomen">
					<option value="DIRECTION"><?=GetMessage('ENTITY_TITLE_ADD_'.$arResult["ENTITY_TYPE"])?></option>
					<option value="/"><?=GetMessage('ENTITY_TITLE_ADD_'.$arResult["ENTITY_TYPE"])?></option>
					<option value="DIRECTION"><?=GetMessage('ENTITY_TITLE_ADD_'.$arResult["ENTITY_TYPE"])?></option>
				</select>
				<select id="newEntityFormedGroup">
					<option value="DIRECTION"><?=GetMessage('ENTITY_TITLE_ADD_'.$arResult["ENTITY_TYPE"])?></option>
					<option value="/"><?=GetMessage('ENTITY_TITLE_ADD_'.$arResult["ENTITY_TYPE"])?></option>
					<option value="DIRECTION"><?=GetMessage('ENTITY_TITLE_ADD_'.$arResult["ENTITY_TYPE"])?></option>
				</select>
			</td>
		</tr>
		<?
if($arResult["isUseCaptcha"] == "Y")
{
?>
		<tr>
			<th colspan="2"><b><?=GetMessage("FORM_CAPTCHA_TABLE_TITLE")?></b></th>
		</tr>
		<tr>
			<td rowspan="2" style="vertical-align: middle"><font color="red">*</font><?=GetMessage("FORM_CAPTCHA_FIELD_TITLE")?></td>
			<td><input type="hidden" name="captcha_sid" value="<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" /><img src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" width="180" height="40" /></td>
		</tr>
		<tr>
			<td><input type="text" style="width: auto" name="captcha_word" size="30" maxlength="50" value="" class="inputtext" /></td>
		</tr>
<?
} // isUseCaptcha
		?>
		</tbody>
		<tfoot>
		<tr>
			<th colspan="2">
				<input type="submit" name="web_form_submit" value="<?=GetMessage("FORM_ADD")?>" />
			</th>
		</tr>
		</tfoot>
	</table>
	<?=$arResult["FORM_FOOTER"]?>
	<script>

		<?=$arResult["arrVALUES"]['AGENT_TYPE'] == 'L'?'LegalMode()':''?>

		function addEntity() {

		}

		function typeTitle(elem) {
			var agentTitle=document.getElementById('agent_title').value;
			var tr=elem.parentNode.parentNode;
			var input=BX.findChild(tr,{tag:'input',attr:{type:'text'}},true);
			input.value=agentTitle;
		}

		function changeCount(elem) {
			var table=BX.findChild(elem.parentNode.parentNode,{tag:'table'},true);
			var btnAdd=table.querySelector('button[name="person_add"]');
			var numberOld=btnAdd.getAttribute('person_number');
			var numberNew=Number(elem.value);
			for(var i=numberOld; i<numberNew; i++) {
				addPerson(btnAdd,true);
			}
			var tbody=BX.findChild(table,{tag:'tbody'},true);
			var trs=tbody.getElementsByTagName('tr');
			var numberOld=trs.length;
			for(var i=0; i<numberOld-numberNew; i++) {
				if(numberOld-i>0) {
					var btnDelete=BX.findChild(trs[numberOld-i-1],{tag:'span',className:'bx-order-btn-delete'},true)
					deletePerson(btnDelete,true);
				}
			}
		}
		function addPerson(elem,rec) {
			var table=BX.findChild(elem.parentNode.parentNode.parentNode.parentNode,{tag:'tbody'});
			var entityType=elem.getAttribute('entity_type');
			var entityId=elem.getAttribute('entity_id');
			var number=elem.getAttribute('person_number');
			var newTr=BX.create('tr');
			newTr.appendChild(
				BX.create(
					'td',
					{
						html: Number(number)+1
					}
				)
			);
			newTr.appendChild(
				BX.create(
					'td',
					{
						html: '<input type="text" name="REG['+entityType+']['+entityId+']['+(number)+'][NAME]"> '
					}
				)
			);
			newTr.appendChild(
				BX.create(
					'td',
					{
						html: '<input type="checkbox" name="REG['+entityType+']['+entityId+']['+(number)+'][PAST]" value="Y">'
					}
				)
			);
			newTr.appendChild(
				BX.create(
					'td',
					{
						html: '<button style="width: 100%" onclick="typeTitle(this); return false;"><?=GetMessage("LIKE_AN_AGENT")?></button>'
					}
				)
			);
			newTr.appendChild(
				BX.create(
					'td',
					{
						html: '<span class="bx-order-btn-delete" onclick="deletePerson(this)" style="color:red;"></span>'
					}
				)
			);
			table.appendChild(newTr);
			var inpCnt=BX(entityType+'_'+entityId+'_personCount');
			elem.setAttribute('person_number',++number);
			if(!rec) inpCnt.value=Number(inpCnt.value)+1;

		}
		function deletePerson(elem,rec) {
			var tr=elem.parentNode.parentNode;
			var table=tr.parentNode.parentNode.parentNode;
			var add=table.querySelector('button[name="person_add"]');
			var entityId=add.getAttribute('entity_id')
			var entityType=add.getAttribute('entity_type')
			add.setAttribute('person_number',add.getAttribute('person_number')-1);
			var c=tr.querySelector('input[type="checkbox"]').getAttribute('name');
			c=c.substr(c.indexOf(']')+1)
			c=c.substr(c.indexOf(']')+1)
			c=Number(c.substring(c.indexOf('[')+1, c.indexOf(']')));
			tr.parentNode.removeChild(tr);
			var trs=table.getElementsByTagName('tr');
			for(var i=c+1; i<trs.length; i++) {
				trs[i].getElementsByTagName('td')[0].innerHTML=i;
				trs[i].querySelector('input[type="checkbox"]').setAttribute('name','REG['+entityType+']['+entityId+']['+(i-1)+'][PAST]');
				trs[i].querySelector('input[type="text"]').setAttribute('name','REG['+entityType+']['+entityId+']['+(i-1)+'][NAME]');
			}
			var inpCnt=BX(entityType+'_'+entityId+'_personCount');
			if(!rec) inpCnt.value=Number(inpCnt.value)-1;
		}
		function deleteEntity(elem) {
			var entity=elem.parentNode.parentNode.parentNode;
			entity.parentNode.removeChild(entity);
			var add=entity.querySelector('button[name="person_add"]');
			var entityType=add.getAttribute('entity_type');
			var entityId=add.getAttribute('entity_id');
		}
		function toggleContact() {
			el=document.getElementsByName('CONTACT_INFO');
			for (var i=0; i<el.length; i++) {
				if (el[i].getAttribute('style')=='display:none') {
					el[i].setAttribute('style','');
				}
				else {
					el[i].setAttribute('style','display:none');
				}
			}
		}
		function LegalMode() {
			var el=document.getElementsByName('CONTACT_INFO');
			for (var i=0; i<el.length; i++) {
				el[i].setAttribute('style','');

			}
			console.log(document.getElementsByName('AGENT[TITLE]')[0].parentNode.parentNode.getElementsByTagName('td')[0]);
			document.getElementsByName('AGENT[TITLE]')[0].parentNode.parentNode.getElementsByTagName('td')[0].innerHTML="<font color='red'>*</font><?=GetMessage('AGENT_L_TITLE')?>";
			document.getElementsByName('AGENT[PHONE]')[0].parentNode.parentNode.getElementsByTagName('td')[0].innerHTML="<?=GetMessage('AGENT_PHONE')?>";
			document.getElementsByName('AGENT[EMAIL]')[0].parentNode.parentNode.getElementsByTagName('td')[0].innerHTML="<?=GetMessage('AGENT_EMAIL')?>";
		}
		function PhysicalMode() {
			var el=document.getElementsByName('CONTACT_INFO');

			for (var i=0; i<el.length; i++) {
				el[i].setAttribute('style','display:none');
			}

			document.getElementsByName('AGENT[TITLE]')[0].parentNode.parentNode.getElementsByTagName('td')[0].innerHTML="<font color='red'>*</font><?=GetMessage('AGENT_TITLE')?>";
			document.getElementsByName('AGENT[PHONE]')[0].parentNode.parentNode.getElementsByTagName('td')[0].innerHTML="<font color='red'>*</font><?=GetMessage('AGENT_PHONE')?>";
			document.getElementsByName('AGENT[EMAIL]')[0].parentNode.parentNode.getElementsByTagName('td')[0].innerHTML="<font color='red'>*</font><?=GetMessage('AGENT_EMAIL')?>";
			//alert(document.getElementsByName('AGENT[TITLE]')[0].parentNode.parentNode.getElementsByTagName('td')[0].innerHTML);
		}
	</script>
<?endif;?>