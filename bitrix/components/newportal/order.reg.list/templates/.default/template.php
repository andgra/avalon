<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
    <script type="text/javascript">
        function order_reg_delete_grid(title, message, btnTitle, path)
        {
            var d;
            d = new BX.CDialog({
                title: title,
                head: '',
                content: message,
                resizable: false,
                draggable: true,
                height: 70,
                width: 300
            });

            var _BTN = [
                {
                    title: btnTitle,
                    id: 'orderOk',
                    'action': function ()
                    {
                        window.location.href = path;
                        BX.WindowManager.Get().Close();
                    }
                },
                BX.CDialog.btnCancel
            ];
            d.ClearButtons();
            d.SetButtons(_BTN);
            d.Show();
        }
        BX.ready(
            function()
            {
                if (BX('actallrows_<?=$arResult['GRID_ID']?>')) {
                    BX.bind(BX('actallrows_<?=$arResult['GRID_ID']?>'), 'click', function () {
                        var el_t = BX.findParent(this, {tagName : 'table'});
                        var el_s = BX.findChild(el_t, {tagName : 'select'}, true, false);
                        for (i = 0; i < el_s.options.length; i++)
                        {
                            if (el_s.options[i].value == 'tasks' || el_s.options[i].value == 'calendar')
                                el_s.options[i].disabled = this.checked;
                        }
                        if (this.checked && (el_s.options[el_s.selectedIndex].value == 'tasks' || el_s.options[el_s.selectedIndex].value == 'calendar'))
                            el_s.selectedIndex = 0;
                    });
                }
                <?if($arResult['INTERNAL']):?>
                    var input=BX.findChild(BX('bx_grid_<?=$arResult['GRID_ID']?>_action_buttons'),{name:'save'});
                    input.style.display='none';
                <?endif;?>
            }
        );
    </script>
<?

$arData=array();
foreach($arResult['REG'] as $id => $el) {
    $data=array(
        'id' => $id,
        'actions' => array(
            array(
                'ICONCLASS' => 'edit',
                'TITLE' => GetMessage('ORDER_REG_EDIT_TITLE'),
                'TEXT' => GetMessage('ORDER_REG_EDIT'),
                'ONCLICK' => 'jsUtils.Redirect([], \''.$el['PATH_TO_REG_EDIT'].'\');',
                'DEFAULT' => true,
            )
        ),
        'data' => $el,
        'editable' => $el['PERM_EDIT'] && ($el['SHARED']=='Y'?false:($arResult['INTERNAL']?true:true))
    );
    if($el['SHARED']=='N' && $el['PERM_DELETE']) {
        $data['actions'][]=array(
            'ICONCLASS' => 'delete',
            'TITLE' => GetMessage('ORDER_REG_DELETE_TITLE'),
            'TEXT' => GetMessage('ORDER_REG_DELETE'),
            'ONCLICK' => "order_reg_delete_grid('".CUtil::JSEscape(GetMessage('ORDER_REG_DELETE_TITLE'))."', '".CUtil::JSEscape(GetMessage('ORDER_REG_DELETE_CONFIRM'))."', '".CUtil::JSEscape(GetMessage('ORDER_REG_DELETE'))."', '".CUtil::JSEscape($el['PATH_TO_REG_DELETE'])."')"
        );
    }
    $row=array(
        'ID' => '<a href="'.$el['PATH_TO_REG_EDIT'].'">'.$el['ID'].'</a>',
        'PHYSICAL_ID' => ($el['PHYSICAL_ID']!='')?'<a href="'.$el['PATH_TO_PHYSICAL_EDIT'].'">'.$el['PHYSICAL_FULL_NAME'].'</a>':$el['PHYSICAL_FULL_NAME'],
        'ENTITY_ID' => '<a href="'.$el['PATH_TO_ENTITY_EDIT'].'">'.$el['ENTITY_TITLE'].'</a><br><font color="silver">'.$el['ENTITY_TYPE_NAME'].'</font>',
        'MODIFY_DATE' => $el['MODIFY_DATE'],
        'MODIFY_BY_ID' => '<a href="'.$el['PATH_TO_USER_MODIFIER'].'">'.$el['MODIFY_BY_FULL_NAME'].'</a><br><font color="silver">'.$el['MODIFY_BY_EMAIL'].'</font>',
        'DESCRIPTION' => $el['DESCRIPTION'],
        'STATUS' => $arResult['STATUS_LIST_WRITE'][$el['STATUS']],
        'PERIOD' => empty($el['PERIOD']) ? '' : '<nobr>'.$el['PERIOD'].'</nobr>',
        'PAST' => $el['PAST'],
        'APP_ID' => $el['APP_ID']=='0'?$el['APP_ID']:('<a href="'.$el['PATH_TO_APP_EDIT'].'">'.$el['APP_ID'].'</a>'),
        'ASSIGNED_ID' => $el['ASSIGNED_TITLE'],
    );
    $data['columns']=$row;
    if($el['PERIOD']!='') {
        $data['columnClasses']['PERIOD'] = '';
        if (MakeTimeStamp($el['PERIOD']) <= MakeTimeStamp(ConvertTimeStamp()))
            $data['columnClasses']['PERIOD'] .= 'order-list-today ';
        if (MakeTimeStamp($el['PERIOD']) < MakeTimeStamp(ConvertTimeStamp()))
            $data['columnClasses']['PERIOD'] .= 'order-list-time-expired ';
    }
    $arData[]=$data;
}

$arResult['GRID_DATA']=$arData;


if($arResult['PERMS']['EDIT'])
{
    // Setup STATUS_ID -->
    $status = '<div id="ACTION_STATUS_WRAPPER" style="display:none;"><select name="ACTION_STATUS_ID" size="1">';
    $status .= '<option value="" title="'.GetMessage('ORDER_STATUS_INIT').'" selected="selected">'.GetMessage('ORDER_STATUS_INIT').'</option>';
    foreach($arResult['STATUS_LIST_WRITE'] as $id => $name):
        $name = htmlspecialcharsbx($name);
        $status .= '<option value="'.$id.'" title="'.$name.'">'.$name.'</option>';
    endforeach;
    $status .= '</select></div>';
    $actionHtml .= $status;
    // <-- Setup STATUS_ID

    // Setup PERIOD -->
    $period = '<div id="ACTION_PERIOD_WRAPPER" style="display:none;"><input type="text" class="typeinput" name="ACTION_PERIOD" size="12">'.Calendar("ACTION_PERIOD", "form_".$arResult['GRID_ID']).'</div>';
    $actionHtml .= $period;
    // <-- Setup PERIOD

    // Setup ENTITY_ID -->
    ob_start();
    $APPLICATION->IncludeComponent('newportal:order.entity.selector',
        '',
        array(
            'ENTITY_TYPE' => array('DIRECTION','NOMEN','GROUP','FORMED_GROUP'),
            'INPUT_NAME' => 'ACTION_ENTITY_ID',
            'INPUT_VALUE' => isset($_REQUEST['ACTION_ENTITY_ID']) ? $_REQUEST['ACTION_ENTITY_ID'] : '',
            'FORM_NAME' => $arResult['GRID_ID'],
            'MULTIPLE' => 'N',
            'FILTER' => false
        ),
        false,
        array('HIDE_ICONS' => 'Y')
    );
    $entity = '<div id="ACTION_ENTITY_WRAPPER" style="display:none; margin-right: 10px; margin-left:10px;">'.ob_get_clean().'</div>';
    $actionHtml .= $entity;
    // <-- Setup ENTITY

    // Setup PHYSICAL_ID -->
    ob_start();
    $APPLICATION->IncludeComponent('newportal:order.entity.selector',
        '',
        array(
            'ENTITY_TYPE' => 'PHYSICAL',
            'INPUT_NAME' => 'ACTION_PHYSICAL_ID',
            'INPUT_VALUE' => isset($_REQUEST['ACTION_PHYSICAL_ID']) ? $_REQUEST['ACTION_PHYSICAL_ID'] : '',
            'FORM_NAME' => $arResult['GRID_ID'],
            'MULTIPLE' => 'N',
            'FILTER' => false
        ),
        false,
        array('HIDE_ICONS' => 'Y')
    );
    $physical = '<div id="ACTION_PHYSICAL_WRAPPER" style="display:none; margin-right: 10px; margin-left:10px;">'.ob_get_clean().'</div>';
    $actionHtml .= $physical;
    // <-- Setup PHYSICAL

    // Setup DESCRIPTION -->
    $desc = '<div id="ACTION_DESCRIPTION_WRAPPER" style="display:none;"><input type="text" class="typeinput" name="ACTION_DESCRIPTION" size="30"></div>';
    $actionHtml .= $desc;
    // <-- Setup DESCRIPTION

    //_c($statuses);
    // Setup ASSIGNED_BY_ID -->
    /*ob_start();
    CCrmViewHelper::RenderUserSearch(
        "{$prefix}_ACTION_ASSIGNED_BY",
        "ACTION_ASSIGNED_BY_SEARCH",
        "ACTION_ASSIGNED_BY_ID",
        "{$prefix}_ACTION_ASSIGNED_BY",
        SITE_ID,
        $arParams['~NAME_TEMPLATE'],
        500
    );
    $actionHtml .= '<div id="ACTION_ASSIGNED_BY_WRAPPER" style="display:none;">'.ob_get_clean().'</div>';
    // <-- Setup ASSIGNED_BY_ID

    // Setup OPENED -->
    $opened = '<div id="ACTION_OPENED_WRAPPER" style="display:none;"><select name="ACTION_OPENED" size="1">';
    $opened .= '<option value="Y">'.GetMessage("CRM_DEAL_MARK_AS_OPENED_YES").'</option>';
    $opened .= '<option value="N">'.GetMessage("CRM_DEAL_MARK_AS_OPENED_NO").'</option>';
    $opened .= '</select></div>';
    $actionHtml .= $opened;*/
    // Setup OPENED -->

    $actionHtml .= '
		<script type="text/javascript">
			BX.ready(
				function(){
				var select = BX.findChild(BX.findPreviousSibling(BX.findParent(BX("ACTION_STATUS_WRAPPER"), { "tagName":"td" })), { "tagName":"select" });
				BX.bind(
					select,
					"change",
					function(e){
						BX("ACTION_STATUS_WRAPPER").style.display = select.value === "set_status" ? "" : "none";
						BX("ACTION_PERIOD_WRAPPER").style.display = select.value === "set_period" ? "" : "none";
						BX("ACTION_DESCRIPTION_WRAPPER").style.display = select.value === "set_description" ? "" : "none";
						BX("ACTION_ENTITY_WRAPPER").style.display = select.value === "set_entity" ? "" : "none";
						BX("ACTION_PHYSICAL_WRAPPER").style.display = select.value === "set_physical" ? "" : "none";
					}
				)
			}
		);
		</script>';
}

$arActionList = array();
if($arResult['PERMS']['EDIT'])
{


    $arActionList['set_status'] =GetMessage('ORDER_REG_SET_STATUS');
    $arActionList['set_period'] =GetMessage('ORDER_REG_SET_PERIOD');
    $arActionList['set_entity'] = GetMessage('ORDER_REG_SET_ENTITY');
    $arActionList['set_physical'] = GetMessage('ORDER_REG_SET_PHYSICAL');
    $arActionList['set_description'] = GetMessage('ORDER_REG_SET_DESCRIPTION');

}
$activityEditorID=''; //Internal
$gridManagerID = $arResult['GRID_ID'].'_MANAGER';
$gridManagerCfg = array(
    'ownerType' => 'REG',
    'gridId' => $arResult['GRID_ID'],
    'formName' => "form_{$arResult['GRID_ID']}",
    'allRowsCheckBoxId' => "actallrows_{$arResult['GRID_ID']}",
    'activityEditorId' => $activityEditorID,
    'filterFields' => array()
);


$footer=array(array(
    'title' => GetMessage('ORDER_ALL'),
    'value' => $arResult['ROWS_COUNT']
));
$actions=array('delete' => $arResult['PERMS']['DELETE']);
if(!$arResult['INTERNAL']) {

    $top=$arResult['PAGE_NUM']*$arResult['PAGE_SIZE']<$arResult['ROWS_COUNT']?$arResult['PAGE_NUM']*$arResult['PAGE_SIZE']:$arResult['ROWS_COUNT'];
    $val=$arResult['ROWS_COUNT']==0?'0':(($arResult['PAGE_NUM']-1)*$arResult['PAGE_SIZE']+1).' - '.$top;
    $footer[]=array(
        'title'=>GetMessage('ORDER_SHOWN'),
        'value'=>$val
    );
    $actions=array_merge($actions,array(
        'custom_html' => $actionHtml,
        'list' => $arActionList
    ));

} else {
    $actions=array_merge($actions,array(
        'add' => $arResult['PERMS']['ADD']
    ));

}

$APPLICATION->IncludeComponent(
    'newportal:order.interface.grid',
    '',
    array(
        'GRID_ID' => $arResult['GRID_ID'],
        'HEADERS' => $arResult['HEADERS'],
        'SORT' => $arResult['SORT'],
        'SORT_VARS' => $arResult['SORT_VARS'],
        'ROWS' => $arResult['GRID_DATA'],
        'FOOTER' =>$footer,
        'EDITABLE' =>  $arResult['PERMS']['EDIT'] ? 'Y' : 'N',
        'ACTIONS' => $actions,
        'ACTION_ALL_ROWS' => false,
        'NAV_OBJECT' => $arResult['INTERNAL']?false:$arResult['NAV_OBJECT'],
        'FORM_ID' => $arResult['FORM_ID'],
        'TAB_ID' => $arResult['TAB_ID'],
        'AJAX_MODE' => $arResult['AJAX_MODE'],
        'AJAX_ID' => $arResult['AJAX_ID'],
        'AJAX_OPTION_JUMP' => $arResult['AJAX_OPTION_JUMP'],
        'AJAX_OPTION_HISTORY' => $arResult['AJAX_OPTION_HISTORY'],
        'AJAX_LOADER' => isset($arParams['AJAX_LOADER']) ? $arParams['AJAX_LOADER'] : null,
        'FILTER' => $arResult['FILTER'],
        'MANAGER' => array(
            'ID' => $gridManagerID,
            'CONFIG' => $gridManagerCfg
        )
    ),
    $component
);
?>
