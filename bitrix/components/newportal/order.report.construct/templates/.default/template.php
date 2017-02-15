<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
CUtil::InitJSCore(array('ajax', 'popup'));

global $APPLICATION;
$APPLICATION->AddHeadScript('/bitrix/js/order/order.js');
$APPLICATION->SetAdditionalCSS('/bitrix/js/order/css/order.css');


if($arResult['REPORT_OWNER_ID'] === ''):
$APPLICATION->SetAdditionalCSS('/bitrix/js/report/css/report.css');
?><form method="POST" name="reportOwnerForm" id="reportOwnerForm" action="<?=CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_REPORT_CONSTRUCT"], array("report_id" => $arResult['REPORT_ID'], 'action' => $arResult['ACTION']));?>">
<?= bitrix_sessid_post('csrf_token')?>
<div class="reports-constructor">
	<div class="webform-main-fields">
		<div class="webform-corners-top">
			<div class="webform-left-corner"></div>
			<div class="webform-right-corner"></div>
		</div>
		<div class="webform-content">
			<div class="reports-title-label"><?=htmlspecialcharsbx(GetMessage('ORDER_REPORT_SELECT_OWNER'))?></div>
			<select id="report-helper-selector" name="reportOwnerID" class="filter-dropdown" style="min-width: 250px;"><?
				$ownerInfos = COrderReportManager::getOwnerInfos();
				foreach ($ownerInfos as &$ownerInfo) :
					?><option value="<?=htmlspecialcharsbx($ownerInfo['ID'])?>"><?= htmlspecialcharsbx($ownerInfo['TITLE']) ?></option><?
				endforeach;
				unset($ownerInfo);?>
			</select>
		</div>
	</div>
</div>
<div class="webform-buttons task-buttons">
	<a class="webform-button webform-button-create" id="reportOwnerSelectButton">
		<span class="webform-button-left"></span>
		<span class="webform-button-text"><?= htmlspecialcharsbx(GetMessage('ORDER_REPORT_CONSTRUCT_BUTTON_CONTINUE'))?></span>
		<span class="webform-button-right"></span>
	</a>
	<a class="webform-button-link webform-button-link-cancel" href="<?=htmlspecialcharsbx($arParams['PATH_TO_REPORT_REPORT'])?>"><?= htmlspecialcharsbx(GetMessage('ORDER_REPORT_CONSTRUCT_BUTTON_CANCEL')) ?></a>
</div>
</form>
<script type="text/javascript">
	BX.ready(
		function()
		{
			BX.bind(
				BX('reportOwnerSelectButton'),
				'click',
				function (e)
				{
					BX.PreventDefault(e);
					BX('reportOwnerForm').submit();
				}
			);
		}
	);
</script><?
return;
else:
?><script type="text/javascript">
	BX.ready(
		function()
		{
			var form = BX('task-filter-form');
			if(!form)
			{
				return;
			}

			form.appendChild(
				BX.create(
					'INPUT',
					{
						'attrs':
						{
							'type': 'hidden',
							'name': 'reportOwnerID',
							'value': '<?= CUtil::JSEscape($arResult['REPORT_OWNER_ID']) ?>'
						}
					}
				)
			);

			var popupTitle = '<?=CUtil::JSEscape(htmlspecialcharsbx(GetMessage('REPORT_POPUP_COLUMN_TITLE_'.strtoupper($arResult['REPORT_OWNER_ID']))))?>';

			BX.findChild(
					BX('reports-add_col-popup-cont'),
					{ 'className': 'reports-add_col-popup-title' }).innerHTML = popupTitle;

			BX.findChild(
					BX('reports-add_filcol-popup-cont'),
					{ 'className': 'reports-add_col-popup-title' }).innerHTML = popupTitle;
		}
	);
</script>
<?
endif;

?><style>
.report-filter-compare-User {display: none;}
.report-filter-compare-Group {display: none;}
.report-filter-compare-COMPANY_BY {display: none;}
.report-filter-compare-CONTACT_BY {display: none;}
.report-filter-compare-LEAD_BY {display: none;}
.report-filter-compare-APP-OWNER {display: none;}
</style>

<!-- filter value control -->
<div id="report-filter-value-control-examples-custom" style="display: none">

	<span name="report-filter-value-control-ASSIGNED_TYPE" class="report-filter-vcc">
		<select class="reports-filter-select-small" name="value">
			<option value=""><?=GetMessage('ORDER_REPORT_INCLUDE_ALL')?></option>
			<?
				$arResult['enumValues']['ASSIGNED_TYPE'] = COrderHelper::GetEnumList('ACCESS','PROVIDER');
			?>
			<? foreach($arResult['enumValues']['ASSIGNED_TYPE'] as $key => $val): ?>
			<option value="<?=htmlspecialcharsbx($key)?>"><?=htmlspecialcharsbx($val)?></option>
			<? endforeach; ?>
		</select>
	</span>

	<span name="report-filter-value-control-ROOT_ID" class="report-filter-vcc">
		<select class="reports-filter-select-small" name="value">
			<option value=""><?=GetMessage('ORDER_REPORT_INCLUDE_ALL')?></option>
			<?
				$res=COrderDirection::GetListEx(array(),array('PARENT_ID'=>''));
				while($el=$res->Fetch()) {
					$arResult['enumValues']['ROOT_ID']['#'.$el['ID'].'#']=$el['TITLE'];
				}
			?>
			<? foreach($arResult['enumValues']['ROOT_ID'] as $key => $val): ?>
			<option value="<?=htmlspecialcharsbx($key)?>"><?=htmlspecialcharsbx($val)?></option>
			<? endforeach; ?>
		</select>
	</span>
	<?/*
	<span name="report-filter-value-control-SOURCE_ID" class="report-filter-vcc">
		<select class="reports-filter-select-small" name="value">
			<option value=""><?=GetMessage('ORDER_REPORT_INCLUDE_ALL')?></option>
			<? $arResult['enumValues']['SOURCE_ID'] = COrderStatus::GetStatusList('SOURCE'); ?>
			<? foreach($arResult['enumValues']['SOURCE_ID'] as $key => $val): ?>
			<option value="<?=htmlspecialcharsbx($key)?>"><?=htmlspecialcharsbx($val)?></option>
			<? endforeach; ?>
		</select>
	</span>*/?>
</div>

<?/* //Если убирать комментарии до конца, то убрать </div>
	<span name="report-filter-value-control-COMPANY_BY" callback="orderCompanySelector">
		<a href="" class="report-select-popup-link" caller="true"><?=GetMessage('REPORT_CHOOSE')?></a>
		<input type="hidden" name="value" />
	</span>*/?>
	<?/*
	//COrderCompany
	$arCompanyTypeList = COrderStatus::GetStatusListEx('COMPANY_TYPE');
	$arCompanyIndustryList = COrderStatus::GetStatusListEx('INDUSTRY');
	$obRes = COrderCompany::GetListEx(
		array('ID' => 'DESC'),
		array(),
		false,
		array('nTopCount' => 50),
		array('ID', 'TITLE', 'COMPANY_TYPE', 'INDUSTRY',  'LOGO')
	);
	$arFiles = array();
	$arCompanies = array();
	while ($arRes = $obRes->Fetch())
	{
		if (!empty($arRes['LOGO']) && !isset($arFiles[$arRes['LOGO']]))
		{
			if ($arFile = CFile::GetFileArray($arRes['LOGO']))
			{
				$arFiles[$arRes['LOGO']] = CHTTP::URN2URI($arFile['SRC']);
			}
		}

		$arRes['SID'] = $arRes['ID'];

		$arDesc = Array();
		if (isset($arCompanyTypeList[$arRes['COMPANY_TYPE']]))
			$arDesc[] = $arCompanyTypeList[$arRes['COMPANY_TYPE']];
		if (isset($arCompanyIndustryList[$arRes['INDUSTRY']]))
			$arDesc[] = $arCompanyIndustryList[$arRes['INDUSTRY']];

		$arCompanies[] = array(
			'title' => (str_replace(array(';', ','), ' ', $arRes['TITLE'])),
			'desc' => implode(', ', $arDesc),
			'id' => $arRes['SID'],
			'url' => CComponentEngine::MakePathFromTemplate(
				COption::GetOptionString('order', 'path_to_company_show'),
				array('company_id' => $arRes['ID'])
			),
			'image' => isset($arFiles[$arRes['LOGO']]) ? $arFiles[$arRes['LOGO']] : '',
			'type'  => 'company',
			'selected' => false
		);
	}

	//OrderContact
	$obRes = COrderContact::GetListEx(
		array('LAST_NAME' => 'ASC', 'NAME' => 'ASC'),
		array(),
		false,
		array('nTopCount' => 50),
		array('ID', 'NAME', 'SECOND_NAME', 'LAST_NAME', 'COMPANY_TITLE', 'PHOTO')
	);
	$arContacts = array();
	while ($arRes = $obRes->Fetch())
	{
		if (!empty($arRes['PHOTO']) && !isset($arFiles[$arRes['PHOTO']]))
		{
			if ($arFile = CFile::GetFileArray($arRes['PHOTO']))
			{
				$arFiles[$arRes['PHOTO']] = CHTTP::URN2URI($arFile['SRC']);
			}
		}

		$arContacts[] =
			array(
				'id' => $arRes['ID'],
				'url' => CComponentEngine::MakePathFromTemplate(
					COption::GetOptionString('order', 'path_to_contact_show'),
					array('contact_id' => $arRes['ID'])
				),
				'title' => (str_replace(array(';', ','), ' ', COrderContact::PrepareFormattedName($arRes))),
				'desc' => empty($arRes['COMPANY_TITLE'])? '': $arRes['COMPANY_TITLE'],
				'image' => isset($arFiles[$arRes['PHOTO']])? $arFiles[$arRes['PHOTO']] : '',
				'type' => 'contact',
				'selected' => false
			);
	}

	//OrderLead
	$arLeads = array();
	$obRes = COrderLead::GetListEx(
		array('TITLE' => 'ASC'),
		array(),
		false,
		array('nTopCount' => 50),
		array('ID', 'TITLE', 'NAME', 'SECOND_NAME', 'LAST_NAME', 'STATUS_ID')
	);
	while ($arRes = $obRes->Fetch())
	{
		$arLeads[] =
			array(
				'id' => $arRes['ID'],
				'url' => CComponentEngine::MakePathFromTemplate(
					COption::GetOptionString('order', 'path_to_lead_show'),
					array('lead_id' => $arRes['ID'])
				),
				'title' => (str_replace(array(';', ','), ' ', $arRes['TITLE'])),
				'desc' => COrderLead::PrepareFormattedName($arRes),
				'type' => 'lead',
				'selected' => false
			)
		;
	}

	//OrderApp
	$arApps = array();
	$obRes = COrderApp::GetListEx(
		array('TITLE' => 'ASC'),
		array(),
		false,
		array('nTopCount' => 50),
		array('ID', 'TITLE')
	);
	while ($arRes = $obRes->Fetch())
	{
		$arApps[] =
			array(
				'id' => $arRes['ID'],
				'url' => CComponentEngine::MakePathFromTemplate(
					COption::GetOptionString('order', 'path_to_app_edit'),
					array('app_id' => $arRes['ID'])
				),
				'title' => (str_replace(array(';', ','), ' ', $arRes['TITLE'])),
				'desc' => '',
				'type' => 'app',
				'selected' => false
			)
		;
	}
	?>

	<script type="text/javascript">
		var orderCompanyElements = <? echo CUtil::PhpToJsObject($arCompanies); ?>;
		var orderContactElements = <? echo CUtil::PhpToJsObject($arContacts); ?>;
		var orderLeadElements = <? echo CUtil::PhpToJsObject($arLeads); ?>;
		var orderAppElements = <? echo CUtil::PhpToJsObject($arApps); ?>;

		var orderCompanyDialogID = '';
		var orderContactDialogID = '';
		var orderLeadDialogID = '';
		var orderAppDialogID = '';

		var orderCompanySelector_LAST_CALLER = null;
		var orderContactSelector_LAST_CALLER = null;
		var orderLeadSelector_LAST_CALLER = null;
		var orderAppSelector_LAST_CALLER = null;

		function openOrderEntityDialog(name, typeName, elements, caller, onClose)
		{
			var dlgID = ORDER.Set(caller,
				name,
				typeName, //subName for dlgID
				elements,
				false,
				false,
				[typeName],
				{
					'company': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_COMPANY'))?>',
					'contact': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_CONTACT'))?>',
					'lead': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_LEAD'))?>',
					'ok': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_OK'))?>',
					'cancel': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_CANCEL'))?>',
					'close': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_CLOSE'))?>',
					'wait': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_SEARCH'))?>',
					'noresult': '<?=CUtil::JSEscape(GetMessage('ORDER_FF_NO_RESULT'))?>',
					'add' : '<?=CUtil::JSEscape(GetMessage('ORDER_FF_CHOISE'))?>',
					'edit' : '<?=CUtil::JSEscape(GetMessage('ORDER_FF_CHANGE'))?>',
					'search' : '<?=CUtil::JSEscape(GetMessage('ORDER_FF_SEARCH'))?>',
					'last' : '<?=CUtil::JSEscape(GetMessage('ORDER_FF_LAST'))?>'
				},
				true
			);

			var dlg = obOrder[dlgID];
			dlg.AddOnSaveListener(onClose);
			dlg.Open();

			return dlgID;
		}

		function orderCompanySelector(span)
		{
			BX.bind(
				BX.findChild(span, { 'tag':'a' }, false, false),
				'click',
				function(e)
				{
					if(!e)
					{
						e = window.event;
					}

					orderCompanySelector_LAST_CALLER = this;
					orderCompanyDialogID =  openOrderEntityDialog('company', 'company', orderCompanyElements, orderCompanySelector_LAST_CALLER, onOrderCompanyDialogClose);
					BX.PreventDefault(e);
				});
		}

		function onOrderCompanyDialogClose(arElements)
		{
			if(!arElements || typeof(arElements['company']) == 'undefined')
			{
				return;
			}

			var element = arElements['company']['0'];
			if(element)
			{
				orderCompanySelectorCatch({ 'id':element['id'], 'name':element['title'] });
			}
			else
			{
				orderCompanySelectorCatch(null);
			}

			obOrder[orderCompanyDialogID].RemoveOnSaveListener(onOrderCompanyDialogClose);
		}

		function orderCompanySelectorCatch(item)
		{
			if(item && BX.type.isNotEmptyString(item['name']))
			{
				orderCompanySelector_LAST_CALLER.innerHTML = BX.util.htmlspecialchars(item['name']);
				BX.addClass(orderCompanySelector_LAST_CALLER, 'report-select-popup-link-active');
			}
			else
			{
				orderCompanySelector_LAST_CALLER.innerHTML = '<?=GetMessageJS('REPORT_CHOOSE')?>';
				BX.removeClass(orderCompanySelector_LAST_CALLER, 'report-select-popup-link-active');
			}

			var h = BX.findNextSibling(orderCompanySelector_LAST_CALLER, { 'tag':'input', 'type':'hidden', 'name':'value' });
			h.setAttribute('value', item ? item['id'] : '');
		}

		function orderContactSelector(span)
		{
			BX.bind(
				BX.findChild(span, { 'tag':'a' }, false, false),
				'click',
				function(e)
				{
					if(!e)
					{
						e = window.event;
					}

					orderContactSelector_LAST_CALLER = this;
					orderContactDialogID =  openOrderEntityDialog('contact', 'contact', orderContactElements, orderContactSelector_LAST_CALLER, onOrderContactDialogClose);
					BX.PreventDefault(e);
				});
		}

		function onOrderContactDialogClose(arElements)
		{
			if(!arElements || typeof(arElements['contact']) == 'undefined')
			{
				return;
			}

			var element = arElements['contact']['0'];
			if(element)
			{
				orderContactSelectorCatch({ 'id':element['id'], 'name':element['title'] });
			}
			else
			{
				orderContactSelectorCatch(null);
			}

			obOrder[orderContactDialogID].RemoveOnSaveListener(onOrderContactDialogClose);
		}

		function orderContactSelectorCatch(item)
		{
			if(item && BX.type.isNotEmptyString(item['name']))
			{
				orderContactSelector_LAST_CALLER.innerHTML = BX.util.htmlspecialchars(item['name']);
				BX.addClass(orderContactSelector_LAST_CALLER, 'report-select-popup-link-active');
			}
			else
			{
				orderContactSelector_LAST_CALLER.innerHTML = '<?=GetMessageJS('REPORT_CHOOSE')?>';
				BX.removeClass(orderContactSelector_LAST_CALLER, 'report-select-popup-link-active');
			}

			var h = BX.findNextSibling(orderContactSelector_LAST_CALLER, { 'tag':'input', 'type':'hidden', 'name':'value' });
			h.setAttribute('value', item ? item['id'] : '');
		}

		function orderLeadSelector(span)
		{
			BX.bind(
				BX.findChild(span, { 'tag':'a' }, false, false),
				'click',
				function(e)
				{
					if(!e)
					{
						e = window.event;
					}

					orderLeadSelector_LAST_CALLER = this;
					orderLeadDialogID =  openOrderEntityDialog('lead', 'lead', orderLeadElements, orderLeadSelector_LAST_CALLER, onOrderLeadDialogClose);
					BX.PreventDefault(e);
				});
		}

		function onOrderLeadDialogClose(arElements)
		{
			if(!arElements || typeof(arElements['lead']) == 'undefined')
			{
				return;
			}

			var element = arElements['lead']['0'];
			if(element)
			{
				orderLeadSelectorCatch({ 'id':element['id'], 'name':element['title'] });
			}
			else
			{
				orderLeadSelectorCatch(null);
			}

			obOrder[orderLeadDialogID].RemoveOnSaveListener(onOrderLeadDialogClose);
		}

		function orderLeadSelectorCatch(item)
		{
			if(item && BX.type.isNotEmptyString(item['name']))
			{
				orderLeadSelector_LAST_CALLER.innerHTML = BX.util.htmlspecialchars(item['name']);
				BX.addClass(orderLeadSelector_LAST_CALLER, 'report-select-popup-link-active');
			}
			else
			{
				orderLeadSelector_LAST_CALLER.innerHTML = '<?=GetMessageJS('REPORT_CHOOSE')?>';
				BX.removeClass(orderLeadSelector_LAST_CALLER, 'report-select-popup-link-active');
			}

			var h = BX.findNextSibling(orderLeadSelector_LAST_CALLER, { 'tag':'input', 'type':'hidden', 'name':'value' });
			h.setAttribute('value', item ? item['id'] : '');
		}

		function orderAppSelector(span)
		{
			BX.bind(
					BX.findChild(span, { 'tag':'a' }, false, false),
					'click',
					function(e)
					{
						if(!e)
						{
							e = window.event;
						}

						orderAppSelector_LAST_CALLER = this;
						orderAppDialogID =  openOrderEntityDialog('app', 'app', orderAppElements, orderAppSelector_LAST_CALLER, onOrderAppDialogClose);
						BX.PreventDefault(e);
					});
		}

		function onOrderAppDialogClose(arElements)
		{
			if(!arElements || typeof(arElements['app']) == 'undefined')
			{
				return;
			}

			var element = arElements['app']['0'];
			if(element)
			{
				orderAppSelectorCatch({ 'id':element['id'], 'name':element['title'] });
			}
			else
			{
				orderAppSelectorCatch(null);
			}

			obOrder[orderAppDialogID].RemoveOnSaveListener(onOrderAppDialogClose);
		}

		function orderAppSelectorCatch(item)
		{
			if(item && BX.type.isNotEmptyString(item['name']))
			{
				orderAppSelector_LAST_CALLER.innerHTML = BX.util.htmlspecialchars(item['name']);
				BX.addClass(orderAppSelector_LAST_CALLER, 'report-select-popup-link-active');
			}
			else
			{
				orderAppSelector_LAST_CALLER.innerHTML = '<?=GetMessageJS('REPORT_CHOOSE')?>';
				BX.removeClass(orderAppSelector_LAST_CALLER, 'report-select-popup-link-active');
			}

			var h = BX.findNextSibling(orderAppSelector_LAST_CALLER, { 'tag':'input', 'type':'hidden', 'name':'value' });
			h.setAttribute('value', item ? item['id'] : '');
		}
	</script>
</div><?

*/
$APPLICATION->IncludeComponent(
	'bitrix:report.construct',
	'',
	Array(
		'USER_ID' => $arParams['USER_ID'],
		'REPORT_ID' => $arParams['REPORT_ID'],
		'ACTION' => $arParams['ACTION'],
		'PATH_TO_REPORT_LIST' => $arParams['PATH_TO_REPORT_REPORT'],
		'PATH_TO_REPORT_CONSTRUCT' => $arParams['PATH_TO_REPORT_CONSTRUCT'],
		'PATH_TO_REPORT_VIEW' => $arParams['PATH_TO_REPORT_VIEW'],
		'REPORT_HELPER_CLASS' => $arResult['REPORT_HELPER_CLASS'],
		'USE_CHART' => true
	),
	$component
);
?>