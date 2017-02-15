<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
CUtil::InitJSCore();
?>
<form action="<?=POST_FORM_ACTION_URI?>" name="orderPermForm" method="POST">
	<input type="hidden" id="ROLE_ACTION" name="save" value=""/>
	<input type="hidden" name="ROLE_ID" value="<?=$arResult['ROLE']['ID']?>"/>
	<?=bitrix_sessid_post()?>
	<?=GetMessage('ORDER_PERMS_FILED_NAME')?>: <input name="NAME" value="<?=htmlspecialcharsbx($arResult['ROLE']['NAME'])?>" class="orderPermRoleName"/>
	<br/>
	<br/>
	<table width="100%" cellpadding="0" cellspacing="0" class="orderPermRoleTable" id="orderPermRoleTable" >
		<tr>
			<th><?=GetMessage('ORDER_PERMS_HEAD_ENTITY')?></th>
			<th><?=GetMessage('ORDER_PERMS_HEAD_READ')?></th>
			<th><?=GetMessage('ORDER_PERMS_HEAD_ADD')?></th>
			<th><?=GetMessage('ORDER_PERMS_HEAD_EDIT')?></th>
			<th><?=GetMessage('ORDER_PERMS_HEAD_DELETE')?></th>
		</tr>
		<? foreach ($arResult['ENTITY'] as $entityType => $entityName): ?>
		<tr>
			<td><? if (isset($arResult['ENTITY_FIELDS'][$entityType])): ?><a href="javascript:void(0)" class="orderPermRoleTreePlus" onclick="OrderPermRoleShowRow(this)"></a><?endif;?><?=$entityName?></td>
			<td>
				<span id="divPermsBox<?=$entityType?>Read" class="divPermsBoxText" onclick="OrderPermRoleShowBox(this.id)"><?=$arResult['ROLE_PERM'][$entityType][$arResult['ROLE_PERMS'][$entityType]['READ']['-']]?></span>
				<span id="divPermsBox<?=$entityType?>Read_Select" style="display:none">
					<select id="divPermsBox<?=$entityType?>Read_SelectBox" name="ROLE_PERMS[<?=$entityType?>][READ][-]">
					<? foreach ($arResult['ROLE_PERM'][$entityType] as $rolePermAtr => $rolePermName): ?>
						<option value="<?=$rolePermAtr?>" <?=($rolePermAtr == $arResult['ROLE_PERMS'][$entityType]['READ']['-'] ? 'selected="selected"' : '')?>><?=$rolePermName?></option>
					<? endforeach; ?>
					</select>
				</span>
			</td>
			<td>
				<span id="divPermsBox<?=$entityType?>Add" class="divPermsBoxText" onclick="OrderPermRoleShowBox(this.id)"><?=$arResult['ROLE_PERM'][$entityType][$arResult['ROLE_PERMS'][$entityType]['ADD']['-']]?></span>
				<span id="divPermsBox<?=$entityType?>Add_Select" style="display:none">
					<select id="divPermsBox<?=$entityType?>Add_SelectBox" name="ROLE_PERMS[<?=$entityType?>][ADD][-]">
					<? foreach ($arResult['ROLE_PERM'][$entityType] as $rolePermAtr => $rolePermName): ?>
						<option value="<?=$rolePermAtr?>" <?=($rolePermAtr == $arResult['ROLE_PERMS'][$entityType]['ADD']['-'] ? 'selected="selected"' : '')?>><?=$rolePermName?></option>
					<? endforeach; ?>
					</select>
				</span>
			</td>
			<td>
				<span id="divPermsBox<?=$entityType?>Edit" class="divPermsBoxText" onclick="OrderPermRoleShowBox(this.id)"><?=$arResult['ROLE_PERM'][$entityType][$arResult['ROLE_PERMS'][$entityType]['EDIT']['-']]?></span>
				<span id="divPermsBox<?=$entityType?>Edit_Select" style="display:none">
					<select id="divPermsBox<?=$entityType?>Edit_SelectBox" name="ROLE_PERMS[<?=$entityType?>][EDIT][-]">
					<? foreach ($arResult['ROLE_PERM'][$entityType] as $rolePermAtr => $rolePermName): ?>
						<option value="<?=$rolePermAtr?>" <?=($rolePermAtr == $arResult['ROLE_PERMS'][$entityType]['EDIT']['-'] ? 'selected="selected"' : '')?>><?=$rolePermName?></option>
					<? endforeach; ?>
					</select>
				</span>
			</td>
			<td>
				<span id="divPermsBox<?=$entityType?>Delete" class="divPermsBoxText" onclick="OrderPermRoleShowBox(this.id)"><?=$arResult['ROLE_PERM'][$entityType][$arResult['ROLE_PERMS'][$entityType]['DELETE']['-']]?></span>
				<span id="divPermsBox<?=$entityType?>Delete_Select" style="display:none">
					<select id="divPermsBox<?=$entityType?>Delete_SelectBox" name="ROLE_PERMS[<?=$entityType?>][DELETE][-]">
					<? foreach ($arResult['ROLE_PERM'][$entityType] as $rolePermAtr => $rolePermName): ?>
						<option value="<?=$rolePermAtr?>" <?=($rolePermAtr == $arResult['ROLE_PERMS'][$entityType]['DELETE']['-'] ? 'selected="selected"' : '')?>><?=$rolePermName?></option>
					<? endforeach; ?>
					</select>
				</span>
			</td>
		</tr>
		<?	if (isset($arResult['ENTITY_FIELDS'][$entityType])):
				foreach ($arResult['ENTITY_FIELDS'][$entityType] as $fieldID => $arFieldValue):
					foreach ($arFieldValue as $fieldValueID => $fieldValue):
		?>
		<tr class="orderPermRoleFields" style="display:none">
			<td><?=$fieldValue?></td>
			<td>
					<?
						$sOrigPermAttr = '-';
						if (isset($arResult['~ROLE_PERMS'][$entityType]['READ'][$fieldID]) && array_key_exists($fieldValueID, $arResult['~ROLE_PERMS'][$entityType]['READ'][$fieldID]))
							$sOrigPermAttr = $arResult['~ROLE_PERMS'][$entityType]['READ'][$fieldID][$fieldValueID];
					?>
				<span id="divPermsBox<?=$entityType.$fieldID.$fieldValueID?>Read" class="divPermsBoxText <?=(!isset($arResult['~ROLE_PERMS'][$entityType]['READ'][$fieldID][$fieldValueID]) ? 'divPermsBoxTextGray' : '')?>" onclick="OrderPermRoleShowBox(this.id, 'divPermsBox<?=$entityType?>Read')"><?=$arResult['ROLE_PERM'][$entityType][$arResult['ROLE_PERMS'][$entityType]['READ'][$fieldID][$fieldValueID]]?></span>
				<span id="divPermsBox<?=$entityType.$fieldID.$fieldValueID?>Read_Select" style="display:none">

					<select id="divPermsBox<?=$entityType.$fieldID.$fieldValueID?>Read_SelectBox" name="ROLE_PERMS[<?=$entityType?>][READ][<?=$fieldID?>][<?=$fieldValueID?>]">
						<option value="-" <?=('-' == $sOrigPermAttr ? 'selected="selected"' : '')?> class="divPermsBoxOptionGray"><?=GetMessage('ORDER_PERMS_PERM_INHERIT')?></option>
					<? foreach ($arResult['ROLE_PERM'][$entityType] as $rolePermAtr => $rolePermName):?>
						<option value="<?=$rolePermAtr?>" <?=($rolePermAtr == $sOrigPermAttr ? 'selected="selected"' : '')?>><?=$rolePermName?></option>
					<? endforeach; ?>
					</select>
				</span>
			</td>
			<td>
				<span id="divPermsBox<?=$entityType.$fieldID.$fieldValueID?>Add" class="divPermsBoxText <?=(!isset($arResult['~ROLE_PERMS'][$entityType]['ADD'][$fieldID][$fieldValueID]) ? 'divPermsBoxTextGray' : '')?>" onclick="OrderPermRoleShowBox(this.id, 'divPermsBox<?=$entityType?>Add')"><?=$arResult['ROLE_PERM'][$entityType][$arResult['ROLE_PERMS'][$entityType]['ADD'][$fieldID][$fieldValueID]]?></span>
				<span id="divPermsBox<?=$entityType.$fieldID.$fieldValueID?>Add_Select" style="display:none">
					<?
						$sOrigPermAttr = '-';
						if (isset($arResult['~ROLE_PERMS'][$entityType]['ADD'][$fieldID]) && array_key_exists($fieldValueID, $arResult['~ROLE_PERMS'][$entityType]['ADD'][$fieldID]))
							$sOrigPermAttr =  $arResult['~ROLE_PERMS'][$entityType]['ADD'][$fieldID][$fieldValueID];
					?>
					<select id="divPermsBox<?=$entityType.$fieldID.$fieldValueID?>Add_SelectBox" name="ROLE_PERMS[<?=$entityType?>][ADD][<?=$fieldID?>][<?=$fieldValueID?>]">
						<option value="-" <?=('-' == $sOrigPermAttr ? 'selected="selected"' : '')?> class="divPermsBoxOptionGray"><?=GetMessage('ORDER_PERMS_PERM_INHERIT')?></option>
					<? foreach ($arResult['ROLE_PERM'][$entityType] as $rolePermAtr => $rolePermName): ?>
						<option value="<?=$rolePermAtr?>" <?=($rolePermAtr == $sOrigPermAttr ? 'selected="selected"' : '')?>><?=$rolePermName?></option>
					<? endforeach; ?>
					</select>
				</span>
			</td>
			<td>
				<span id="divPermsBox<?=$entityType.$fieldID.$fieldValueID?>Edit" class="divPermsBoxText <?=(!isset($arResult['~ROLE_PERMS'][$entityType]['EDIT'][$fieldID][$fieldValueID]) ? 'divPermsBoxTextGray' : '')?>" onclick="OrderPermRoleShowBox(this.id, 'divPermsBox<?=$entityType?>Edit')"><?=$arResult['ROLE_PERM'][$entityType][$arResult['ROLE_PERMS'][$entityType]['EDIT'][$fieldID][$fieldValueID]]?></span>
				<span id="divPermsBox<?=$entityType.$fieldID.$fieldValueID?>Edit_Select" style="display:none">
					<?
						$sOrigPermAttr = '-';
						if (isset($arResult['~ROLE_PERMS'][$entityType]['EDIT'][$fieldID]) && array_key_exists($fieldValueID, $arResult['~ROLE_PERMS'][$entityType]['EDIT'][$fieldID]))
							$sOrigPermAttr =  $arResult['~ROLE_PERMS'][$entityType]['EDIT'][$fieldID][$fieldValueID];
					?>
					<select id="divPermsBox<?=$entityType.$fieldID.$fieldValueID?>Edit_SelectBox" name="ROLE_PERMS[<?=$entityType?>][EDIT][<?=$fieldID?>][<?=$fieldValueID?>]">
						<option value="-" <?=('-' == $sOrigPermAttr ? 'selected="selected"' : '')?> class="divPermsBoxOptionGray"><?=GetMessage('ORDER_PERMS_PERM_INHERIT')?></option>
					<? foreach ($arResult['ROLE_PERM'][$entityType] as $rolePermAtr => $rolePermName): ?>
						<option value="<?=$rolePermAtr?>" <?=($rolePermAtr == $sOrigPermAttr ? 'selected="selected"' : '')?>><?=$rolePermName?></option>
					<? endforeach; ?>
					</select>
				</span>
			</td>
			<td>
				<span id="divPermsBox<?=$entityType.$fieldID.$fieldValueID?>Delete" class="divPermsBoxText <?=(!isset($arResult['~ROLE_PERMS'][$entityType]['DELETE'][$fieldID][$fieldValueID]) ? 'divPermsBoxTextGray' : '')?>" onclick="OrderPermRoleShowBox(this.id, 'divPermsBox<?=$entityType?>Delete')"><?=$arResult['ROLE_PERM'][$entityType][$arResult['ROLE_PERMS'][$entityType]['DELETE'][$fieldID][$fieldValueID]]?></span>
				<span id="divPermsBox<?=$entityType.$fieldID.$fieldValueID?>Delete_Select" style="display:none">
					<?
						$sOrigPermAttr = '-';
						if (isset($arResult['~ROLE_PERMS'][$entityType]['DELETE'][$fieldID]) && array_key_exists($fieldValueID, $arResult['~ROLE_PERMS'][$entityType]['DELETE'][$fieldID]))
							$sOrigPermAttr =  $arResult['~ROLE_PERMS'][$entityType]['DELETE'][$fieldID][$fieldValueID];
					?>
					<select id="divPermsBox<?=$entityType.$fieldID.$fieldValueID?>Delete_SelectBox" name="ROLE_PERMS[<?=$entityType?>][DELETE][<?=$fieldID?>][<?=$fieldValueID?>]">
						<option value="-" <?=('-' == $sOrigPermAttr ? 'selected="selected"' : '')?> class="divPermsBoxOptionGray"><?=GetMessage('ORDER_PERMS_PERM_INHERIT')?></option>
					<? foreach ($arResult['ROLE_PERM'][$entityType] as $rolePermAtr => $rolePermName): ?>
						<option value="<?=$rolePermAtr?>" <?=($rolePermAtr == $sOrigPermAttr ? 'selected="selected"' : '')?>><?=$rolePermName?></option>
					<? endforeach; ?>
					</select>
				</span>
			</td>
		</tr>
		<?
					endforeach;
				endforeach;
			endif;
		endforeach;
		?>
		<tr  class="SyncEdit">
			<td colspan="7"><input name="ROLE_PERMS[SYNC][READ][-]" <?=($arResult['ROLE_PERMS']['SYNC']['READ']['-'] == 'X' ? 'checked="checked"' : '')?> value="X" id="orderSyncEdit" type="checkbox" /><label for="orderSyncEdit"><?=GetMessage("ORDER_PERMS_SYNC_READ")?></label></td>
		</tr>
		<tr  class="ConfigEdit">
			<td colspan="7"><input name="ROLE_PERMS[CONFIG][WRITE][-]" <?=($arResult['ROLE_PERMS']['CONFIG']['WRITE']['-'] == 'X' ? 'checked="checked"' : '')?> value="X" id="orderConfigEdit" type="checkbox" /><label for="orderConfigEdit"><?=GetMessage("ORDER_PERMS_PERM_ADD")?></label></td>
		</tr>
	</table>
	<br/>
	<div id="orderPermButtonBoxPlace">
		<? if ($arResult['ROLE']['ID'] > 0): ?>
		<div style="float:right; padding-right: 10px;"><a href="<?=$arResult['PATH_TO_ROLE_DELETE']?>" onclick="OrderRoleDelete('<?=CUtil::JSEscape(GetMessage('ORDER_PERMS_DLG_TITLE'))?>', '<?=CUtil::JSEscape(GetMessage('ORDER_PERMS_DLG_MESSAGE'))?>', '<?=CUtil::JSEscape(GetMessage('ORDER_PERMS_DLG_BTN'))?>', '<?=CUtil::JSEscape($arResult['PATH_TO_ROLE_DELETE'])?>'); return false;" style="color:#E00000"><?=GetMessage('ORDER_PERMS_ROLE_DELETE')?></a></div>
		<? endif;?>
		<div align="left">
			<input type="submit" name="save" value="<?=GetMessage('ORDER_PERMS_BUTTONS_SAVE');?>"/>
			<input type="submit" naem="apply" value="<?=GetMessage('ORDER_PERMS_BUTTONS_APPLY');?>" onclick="BX('ROLE_ACTION').name='apply'"/>
		</div>
	</div>
</form>
