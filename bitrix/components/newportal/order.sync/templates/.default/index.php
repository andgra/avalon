<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;

CUtil::InitJSCore(array("ajax"));

if(isset($_POST['BTN_save_auto']) && check_bitrix_sessid()){
	$enabled=$_POST['AUTOSYNC_ENABLED'];
	$sType=$_POST['SCHEDULE_TYPE'];
	$start=$_POST['AUTOSYNC_START'];
	$months = false;
	$days=false;
	if($sType=='D') {
		$days = $_POST['AUTOSYNC_DAILY_DAYS'];
	}elseif($sType=='W') {
		if($_POST['AUTOSYNC_DOW_MON']==='Y')
			$days[]='MON';
		if($_POST['AUTOSYNC_DOW_TUE']==='Y')
			$days[]='TUE';
		if($_POST['AUTOSYNC_DOW_WED']==='Y')
			$days[]='WED';
		if($_POST['AUTOSYNC_DOW_THU']==='Y')
			$days[]='THU';
		if($_POST['AUTOSYNC_DOW_FRI']==='Y')
			$days[]='FRI';
		if($_POST['AUTOSYNC_DOW_SAT']==='Y')
			$days[]='SAT';
		if($_POST['AUTOSYNC_DOW_SUN']==='Y')
			$days[]='SUN';
		$months=$_POST['AUTOSYNC_WEEKLY_MONTHS'];
	}
	$autoSyncSettings=COrderHelper::GetAutoSync();
	if($autoSyncSettings['SCHEDULE_TYPE']!=$sType || $autoSyncSettings['START']!=$start ||
		$autoSyncSettings['DAYS']!=$days || $autoSyncSettings['MONTHS']!=$months
	)
		COrderHelper::SetAutoSync($sType,$start,$days,$months,$autoSyncSettings);
	if($enabled=='Y')
		COrderHelper::EnableAutoSync();
	else
		COrderHelper::DisableAutoSync();
}
$autoSyncSettings=COrderHelper::GetAutoSync();

$APPLICATION->IncludeComponent(
	'newportal:order.control_panel',
	'',
	array(
		'ID' => 'SYNC_INDEX',
		'ACTIVE_ITEM_ID' => 'SYNC',
		'PATH_TO_PHYSICAL_LIST' => isset($arResult['PATH_TO_PHYSICAL_LIST']) ? $arResult['PATH_TO_PHYSICAL_LIST'] : '',
		'PATH_TO_PHYSICAL_EDIT' => isset($arResult['PATH_TO_PHYSICAL_EDIT']) ? $arResult['PATH_TO_PHYSICAL_EDIT'] : '',
		'PATH_TO_CONTACT_LIST' => isset($arResult['PATH_TO_CONTACT_LIST']) ? $arResult['PATH_TO_CONTACT_LIST'] : '',
		'PATH_TO_CONTACT_EDIT' => isset($arResult['PATH_TO_CONTACT_EDIT']) ? $arResult['PATH_TO_CONTACT_EDIT'] : '',
		'PATH_TO_AGENT_LIST' => isset($arResult['PATH_TO_AGENT_LIST']) ? $arResult['PATH_TO_AGENT_LIST'] : '',
		'PATH_TO_AGENT_EDIT' => isset($arResult['PATH_TO_AGENT_EDIT']) ? $arResult['PATH_TO_AGENT_EDIT'] : '',
		'PATH_TO_DIRECTION_LIST' => isset($arResult['PATH_TO_DIRECTION_LIST']) ? $arResult['PATH_TO_DIRECTION_LIST'] : '',
		'PATH_TO_DIRECTION_EDIT' => isset($arResult['PATH_TO_DIRECTION_EDIT']) ? $arResult['PATH_TO_DIRECTION_EDIT'] : '',
		'PATH_TO_NOMEN_LIST' => isset($arResult['PATH_TO_NOMEN_LIST']) ? $arResult['PATH_TO_NOMEN_LIST'] : '',
		'PATH_TO_NOMEN_EDIT' => isset($arResult['PATH_TO_NOMEN_EDIT']) ? $arResult['PATH_TO_NOMEN_EDIT'] : '',
		'PATH_TO_COURSE_LIST' => isset($arResult['PATH_TO_COURSE_LIST']) ? $arResult['PATH_TO_COURSE_LIST'] : '',
		'PATH_TO_COURSE_EDIT' => isset($arResult['PATH_TO_COURSE_EDIT']) ? $arResult['PATH_TO_COURSE_EDIT'] : '',
		'PATH_TO_GROUP_LIST' => isset($arResult['PATH_TO_GROUP_LIST']) ? $arResult['PATH_TO_GROUP_LIST'] : '',
		'PATH_TO_GROUP_EDIT' => isset($arResult['PATH_TO_GROUP_EDIT']) ? $arResult['PATH_TO_GROUP_EDIT'] : '',
		'PATH_TO_FORMED_GROUP_LIST' => isset($arResult['PATH_TO_FORMED_GROUP_LIST']) ? $arResult['PATH_TO_FORMED_GROUP_LIST'] : '',
		'PATH_TO_FORMED_GROUP_EDIT' => isset($arResult['PATH_TO_FORMED_GROUP_EDIT']) ? $arResult['PATH_TO_FORMED_GROUP_EDIT'] : '',
		'PATH_TO_APP_LIST' => isset($arResult['PATH_TO_APP_LIST']) ? $arResult['PATH_TO_APP_LIST'] : '',
		'PATH_TO_APP_EDIT' => isset($arResult['PATH_TO_APP_LIST']) ? $arResult['PATH_TO_APP_LIST'] : '',
		'PATH_TO_REG_LIST' => isset($arResult['PATH_TO_REG_LIST']) ? $arResult['PATH_TO_REG_LIST'] : '',
		'PATH_TO_REG_EDIT' => isset($arResult['PATH_TO_REG_EDIT']) ? $arResult['PATH_TO_REG_EDIT'] : '',
		'PATH_TO_SYNC' => isset($arResult['PATH_TO_SYNC']) ? $arResult['PATH_TO_SYNC'] : '',
		'PATH_TO_CONFIG' => isset($arResult['PATH_TO_CONFIG']) ? $arResult['PATH_TO_CONFIG'] : '',
	),
	$component
);

/*$tbButtons = array(
	array(
		'TEXT' => GetMessage('ORDER_CONFIGS_LINK_TEXT'),
		'TITLE' => GetMessage('ORDER_CONFIGS_LINK_TITLE'),
		'LINK' => CComponentEngine::MakePathFromTemplate($arResult['PATH_TO_CONFIGS_INDEX'], array()),
		'ICON' => 'go-back'
	)
);
if (!empty($tbButtons))
{
	$APPLICATION->IncludeComponent(
		'bitrix:main.interface.toolbar',
		'',
		array(
			'BUTTONS' => $tbButtons
		),
		$component,
		array(
			'HIDE_ICONS' => 'Y'
		)
	);
}*/
?>
	<div class="order-detail-lead-wrap-wrap">
		<div class="order-detail-lead-wrap">
			<div class="order-detail-title">
				<div class="order-instant-editor-fld-block order-title-name-wrap">
					<span class="order-detail-title-name"><span class="order-instant-editor-fld order-instant-editor-fld-input"><span class="order-instant-editor-fld-text"><?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_CONNECT_SECTION_TITLE')) ?></span></span></span>
				</div>
			</div>
			<div class="order-instant-editor-fld-block">
				<div class="order-detail-comments-text" style="padding-left: 0; cursor: text;">
					<p><?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_CONNECT_COMMENT_P1')) ?></p>
					<p><?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_CONNECT_COMMENT_P2')) ?></p>
				</div>
			</div>
		</div>
	</div>
	<form method="post" action="<?=$arResult['PATH_TO_SYNC_ALL'];?>" class="order-synch-form">
		<?=bitrix_sessid_post()?>
		<div class="order-detail-lead-wrap-wrap">
			<div class="order-detail-lead-wrap">
				<div class="order-detail-title">
					<div class="order-instant-editor-fld-block order-title-name-wrap">
						<span class="order-detail-title-name"><?=GetMessage('ORDER_SYNC_MANUALLY_SECTION_TITLE')?></span>
					</div>
				</div>
				<div class="order-instant-editor-fld-block">
					<?if(isset($arResult['ENTITY_TITLE']) && $arResult['ENTITY_TITLE']!=array()):?>
						<ul id="cbx-entity">
							<?foreach($arResult['ENTITY_TITLE'] as $code=>$title):
								echo '<li><input type="checkbox" name="ENTITY_TO_SYNC[]" id="'.$code.'" value="'.$code.'"><label for="'.$code.'">'.$title.'</label></li>';
							endforeach;?>
						</ul>
						<p style="margin-bottom: 50px;"><input type="checkbox" id="checkAll" onchange="toggleAll(this)"><label for="checkAll">Выделить все</label></p>
						<script>
							function toggleAll(cbx) {
								var cbxs=BX.findChildren(BX("cbx-entity"),{'tagName':'input','attribute':{'type':'checkbox'}},true,true);
								if(cbx.checked==true) {
									for(var i in cbxs) {
										cbxs[i].checked=true;
									}
								} else {
									for(var i in cbxs) {
										cbxs[i].checked=false;
									}
								}
							}
						</script>

						<input type="hidden" name="START_MANUALLY" value="Y">
						<p><input class="btn btn-success btn-lg" type="submit" name="BTN_SYNC_START" value="<?=GetMessage('ORDER_BUTTON_SYNCH')?>" title="<?=GetMessage('ORDER_BUTTON_SYNCH_TITLE')?>"></p>
					<?else:
						echo '<h4 style="text-align: center;">'.GetMessage('ORDER_NOTHING_TO_SHOW').'</h4>';
					endif;?>
				</div>
			</div>
		</div>
	</form>
	<div class="order-detail-lead-wrap-wrap">
		<div class="order-detail-lead-wrap">
			<div class="order-detail-title">
				<div class="order-instant-editor-fld-block order-title-name-wrap">
					<span class="order-detail-title-name"><span class="order-instant-editor-fld order-instant-editor-fld-input"><span class="order-instant-editor-fld-text"><?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_SECTION_TITLE')) ?></span></span></span>
				</div>
			</div>
			<div class="order-instant-editor-fld-block">
				<form method="post" action="<?=$APPLICATION->GetCurPage()?>" name="autosync_form">
					<?=bitrix_sessid_post()?>
					<p><?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_P1')) ?><input type="checkbox" id="AUTOSYNC_ENABLED" name="AUTOSYNC_ENABLED" value="Y" <?=$autoSyncSettings['ENABLED']=='Y'?'checked':''?>></p>
					<p><?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_NEXT')) ?> <input type="text" readonly value="<?=$autoSyncSettings['NEXT']?>" size="16"></p>
					<div id="order-autosync-left" <?=$autoSyncSettings['ENABLED']!='Y'?'style="display: none;"':''?>>
						<p><input type="radio" id="ORDER_SYNC_SCHED_O" name="SCHEDULE_TYPE" value="O" <?=$autoSyncSettings['SCHEDULE_TYPE']=='O'?'checked':''?>> <?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_ONCE')) ?></p>
						<p><input type="radio" id="ORDER_SYNC_SCHED_D" name="SCHEDULE_TYPE" value="D" <?=$autoSyncSettings['SCHEDULE_TYPE']=='D'?'checked':''?>> <?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_DAILY')) ?></p>
						<p><input type="radio" id="ORDER_SYNC_SCHED_W" name="SCHEDULE_TYPE" value="W" <?=$autoSyncSettings['SCHEDULE_TYPE']=='W'?'checked':''?>> <?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_WEEKLY')) ?></p>
					</div>
					<div id="order-autosync-right" <?=$autoSyncSettings['ENABLED']!='Y'?'style="display: none;"':''?>>
						<p>
							<?=htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_START')) ?>
							<?$APPLICATION->IncludeComponent("bitrix:main.calendar","",Array(
								"SHOW_INPUT" => "Y",
								"FORM_NAME" => "",
								"INPUT_NAME" => "AUTOSYNC_START",
								"INPUT_VALUE" => ConvertDateTime($autoSyncSettings['START'],'DD.MM.YYYY HH:MI:SS'),
								"SHOW_TIME" => "Y",
								'INPUT_ADDITIONAL_ATTR' => 'size="16"'
							));?>
						</p>
						<div id="order-autosync-daily" <?=$autoSyncSettings['SCHEDULE_TYPE']!='D'?'style="display: none;"':''?>>
							<p>
								<?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_DAILY_1')) ?>
								<input type="number" style="width: 2.5em" name="AUTOSYNC_DAILY_DAYS" value="<?=(int)$autoSyncSettings['DAYS']===$autoSyncSettings['DAYS']?$autoSyncSettings['DAYS']:'1'?>">
								<?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_DAILY_2')) ?>
							</p>
						</div>
						<div id="order-autosync-weekly" <?=$autoSyncSettings['SCHEDULE_TYPE']!='W'?'style="display: none;"':''?>>
							<p>
								<?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_WEEKLY_1')) ?>
								<input type="number" style="width: 2.5em" name="AUTOSYNC_WEEKLY_MONTHS" value="<?=(int)$autoSyncSettings['MONTHS']===$autoSyncSettings['MONTHS']?$autoSyncSettings['MONTHS']:'1'?>">
								<?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_WEEKLY_2')) ?>
							</p>
							<p><table id="order-autosync-weekly-days" class="order-autosync-weekly-days">
								<tr>
									<td><input type="checkbox" name="AUTOSYNC_DOW_MON" value="Y" <?=in_array('MON',$autoSyncSettings['DAYS'])?'checked':''?>><?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_WEEKLY_MON')) ?></td>
									<td><input type="checkbox" name="AUTOSYNC_DOW_TUE" value="Y" <?=in_array('TUE',$autoSyncSettings['DAYS'])?'checked':''?>><?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_WEEKLY_TUE')) ?></td>
									<td><input type="checkbox" name="AUTOSYNC_DOW_WED" value="Y" <?=in_array('WED',$autoSyncSettings['DAYS'])?'checked':''?>><?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_WEEKLY_WED')) ?></td>
									<td><input type="checkbox" name="AUTOSYNC_DOW_THU" value="Y" <?=in_array('THU',$autoSyncSettings['DAYS'])?'checked':''?>><?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_WEEKLY_THU')) ?></td>
								</tr>
								<tr>
									<td><input type="checkbox" name="AUTOSYNC_DOW_FRI" value="Y" <?=in_array('FRI',$autoSyncSettings['DAYS'])?'checked':''?>><?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_WEEKLY_FRI')) ?></td>
									<td><input type="checkbox" name="AUTOSYNC_DOW_SAT" value="Y" <?=in_array('SAT',$autoSyncSettings['DAYS'])?'checked':''?>><?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_WEEKLY_SAT')) ?></td>
									<td><input type="checkbox" name="AUTOSYNC_DOW_SUN" value="Y" <?=in_array('SUN',$autoSyncSettings['DAYS'])?'checked':''?>><?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_COMMENT_WEEKLY_SUN')) ?></td>
								</tr>
							</table></p>
						</div>
					</div>
					<p><input class="btn btn-success btn-lg" type="submit" id="BTN_save_auto" name="BTN_save_auto" value="<?= htmlspecialcharsbx(GetMessage('ORDER_SYNC_AUTOSYNC_SUBMIT')) ?>"></p>
				</form>
			</div>
		</div>
	</div><?
/*
<div>
<form id="ORDER_SYNC_ENABLE_FORM" method="POST" action="<?=POST_FORM_ACTION_URI?>">
    <input id="ORDER_SYNC_ENABLE_CHECK" type="checkbox" name="ORDER_SYNC_ENABLE"<?= ($arResult['ORDER_SYNC_ENABLED'] === 'Y') ? ' checked="checked"' : '' ?> /><span><?= ' '.htmlspecialcharsbx(GetMessage('ORDER_SYNC_ENABLED_TITLE')) ?></span>
</form>
</div>*/ ?>
	<script type="text/javascript">
		BX.ready(function () {
			var days=BX.findChildren(BX('order-autosync-weekly-days'),{
				"tag":"input",
				"name":"AUTOSYNC_DOW"
			},true);
			var submit=BX('BTN_save_auto');
			var submDis=function() {
				submit.disabled=true;
				for (var j in days) {
					if(days[j].checked==true) {
						submit.disabled=false;
					}
				}
			};
			if(days.length==7) {
				for (var i in days) {
					BX.bind(days[i], 'change',
						submDis
					);
				}
			}
			BX.bind(BX('AUTOSYNC_ENABLED'), 'change',
				function() {
					if(this.checked==true) {
						BX.style(BX('order-autosync-left'),'display','inline-block');
						BX.style(BX('order-autosync-right'),'display','inline-block');
					} else {
						BX.style(BX('order-autosync-left'),'display','none');
						BX.style(BX('order-autosync-right'),'display','none');
					}
				}
			);
			var once =BX('ORDER_SYNC_SCHED_O');
			var daily =BX('ORDER_SYNC_SCHED_D');
			var weekly =BX('ORDER_SYNC_SCHED_W');
			BX.bind(once, 'click',
				function () {
					submit.disabled=false;
					BX.style(BX('order-autosync-daily'),'display','none');
					BX.style(BX('order-autosync-weekly'),'display','none');
				}
			);
			BX.bind(daily, 'click',
				function () {
					submit.disabled=false;
					BX.style(BX('order-autosync-daily'),'display','block');
					BX.style(BX('order-autosync-weekly'),'display','none');
				}
			);
			BX.bind(weekly, 'click',
				function () {
					submDis();
					BX.style(BX('order-autosync-daily'),'display','none');
					BX.style(BX('order-autosync-weekly'),'display','block');
				}
			);
		});
	</script>
<?
