<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
CUtil::InitJSCore();
?>
<form action="<?=POST_FORM_ACTION_URI?>" name="orderPermForm" method="POST">
	<input type="hidden" name="ACTION" value="save" id="ACTION">
	<?=bitrix_sessid_post()?>
	<table width="100%" cellpadding="0" cellspacing="0" >
	<tr>
		<td  valign="top" style="min-width:432px">
			<table width="100%" cellpadding="0" cellspacing="0" class="orderPermTable" id="orderPermTable" >
				<tr>
					<th>&nbsp;</th>
					<th><?=GetMessage("ORDER_PERMS_PERM_ROLE")?></th>
				</tr>
				<? foreach ($arResult['RELATION'] as $arRelation): ?>
				<tr data-roleId="<?=$arRelation['RELATION']?>">
					<td><?=$arRelation['NAME']?></td>
					<td class="last-child">
						<div style="float:left">
						<select name="PERMS[<?=$arRelation['RELATION']?>][]">
						<? foreach ($arResult['ROLE'] as $arRole): ?>
							<option <?=($arRole['ID'] == $arRelation['ROLE_ID'] ? 'selected="selected"' : '')?> value="<?=$arRole['ID']?>" title="<?=$arRole['NAME']?>"><?=$arRole['NAME']?></option>
						<? endforeach; ?>
						</select>
						</div>
						<a href="javascript:void(0)" onclick="OrderPermRemoveRow(this.parentNode.parentNode); return false;"  class="orderPermA orderPermADelete" title="<?=GetMessage("ORDER_PERMS_PERM_DELETE")?>"></a>
					</td>
				</tr>
				<? endforeach; ?>
				<tr id="orderPermTableInsertTd" style="display:none">
					<td id="orderPermTableInsertTdName"></td>
					<td class="last-child">
						<div style="float:left">
						<select name="">
						<? foreach ($arResult['ROLE'] as $arRole): ?>
							<option value="<?=$arRole['ID']?>" title="<?=$arRole['NAME']?>"><?=$arRole['NAME']?></option>
						<? endforeach; ?>
						</select>
						</div>
						<a href="javascript:void(0)" onclick="OrderPermRemoveRow(this.parentNode.parentNode); return false;" class="orderPermA orderPermADelete" title="<?=GetMessage("ORDER_PERMS_PERM_DELETE")?>"></a>
					</td>
				</tr>
				<tr  class="AddPerm">
					<td colspan="2"><a name="orderUserSelect" href="javascript:void(0)" onclick="OrderSelectEntity(); return false" ><?=GetMessage("ORDER_PERMS_PERM_ADD")?></a></td>
				</tr>
			</table>
		</td>
		<td style="padding-left:15px; min-width:192px;"  valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" class="orderRoleTable" >
				<tr>
					<th><?=GetMessage("ORDER_PERMS_ROLE_LIST")?>:</th>
				</tr>
				<tr>
					<td>
						<? foreach ($arResult['ROLE'] as $arRole): ?>
							<a href="<?=$arRole['PATH_TO_DELETE']?>" style="float:right"  title="<?=GetMessage("ORDER_PERMS_ROLE_DELETE")?>" class="orderPermA orderPermADelete" onclick="OrderRoleDelete('<?=CUtil::JSEscape(GetMessage('ORDER_PERMS_DLG_TITLE'))?>', '<?=CUtil::JSEscape(GetMessage('ORDER_PERMS_DLG_MESSAGE'))?>', '<?=CUtil::JSEscape(GetMessage('ORDER_PERMS_DLG_BTN'))?>', '<?=CUtil::JSEscape($arRole['PATH_TO_DELETE'])?>'); return false;"></a>
							<a href="<?=$arRole['PATH_TO_EDIT']?>" style="float:right" class="orderPermA orderPermAEdit" title="<?=GetMessage("ORDER_PERMS_ROLE_EDIT")?>"></a>
							<div style="padding-bottom: 4px" algin="left">- <?=$arRole['NAME']?></div>
							<div style="clear:both"></div>
						<? endforeach; ?>
						<div class="orderRole" style="padding-left:10px"><a href="<?=$arResult['PATH_TO_ROLE_ADD']?>"><?=GetMessage("ORDER_PERMS_ROLE_ADD")?></a></div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
	<div id="orderPermButtonBoxPlace">
		<input type="submit" value="<?=GetMessage('ORDER_PERMS_BUTTONS_SAVE');?>">
	</div>
</form>
<script type="text/javascript">
	BX.ready(
		function()
		{
			if(BX.type.isFunction(OrderSelectEntityInit))
			{
				OrderSelectEntityInit();
			}
		}
	);
</script>
<script type="text/javascript">
var arOrderSelected = <?=CUtil::PhpToJsObject($arResult['RELATION_ENTITY']);?>;
var arOrderPermSettings = {};
<?if(isset($arResult['DISABLED_PROVIDERS'])):?>
arOrderPermSettings['DISABLED_PROVIDERS'] = <?=CUtil::PhpToJsObject($arResult['DISABLED_PROVIDERS'])?>;
<?endif;?>
</script>