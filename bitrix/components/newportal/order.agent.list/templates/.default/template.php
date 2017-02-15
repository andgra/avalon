<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
    <script type="text/javascript">
        function order_agent_delete_grid(title, message, btnTitle, path)
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
            }
        );
    </script>
<?

$arData=array();
foreach($arResult['AGENT'] as $id => $el) {
    $data=array(
        'id' => $id,
        'actions' => array(
            array(
                'ICONCLASS' => 'edit',
                'TITLE' => GetMessage('ORDER_AGENT_EDIT_TITLE'),
                'TEXT' => GetMessage('ORDER_AGENT_EDIT'),
                'ONCLICK' => 'jsUtils.Redirect([], \''.$el['PATH_TO_AGENT_EDIT'].'\');',
                'DEFAULT' => true,
            ),
            array(
                'ICONCLASS' => 'delete',
                'TITLE' => GetMessage('ORDER_AGENT_DELETE_TITLE'),
                'TEXT' => GetMessage('ORDER_AGENT_DELETE'),
                'ONCLICK' => "order_agent_delete_grid('".CUtil::JSEscape(GetMessage('ORDER_AGENT_DELETE_TITLE'))."', '".CUtil::JSEscape(GetMessage('ORDER_AGENT_DELETE_CONFIRM'))."', '".CUtil::JSEscape(GetMessage('ORDER_AGENT_DELETE'))."', '".CUtil::JSEscape($el['PATH_TO_AGENT_DELETE'])."')"
            )
        ),
        'data' => $el,
        'editable' => true
    );
    $row=array(
        'ID' => '<a href="'.$el['PATH_TO_AGENT_EDIT'].'">'.$el['ID'].'</a>',
        'TITLE' => ($el['LEGAL']=='Y'?'<a href="'.$el['PATH_TO_AGENT_EDIT'].'">'.$el['TITLE'].'</a>':'<a href="'.$el['PATH_TO_PHYSICAL_EDIT'].'" target="_blank">'.$el['TITLE'].'</a>'),
        'CONTACT_ID' => '<a href="'.$el['PATH_TO_CONTACT_EDIT'].'" target="_blank">'.$el['CONTACT_FULL_NAME'].'</a><br><small style="color:silver">'.$el['CONTACT_EMAIL'].'<br>'.$el['CONTACT_PHONE'].'</small>',
        'MODIFY_BY_ID' => '<a href="'.$el['PATH_TO_USER_MODIFIER'].'" target="_blank">'.$el['MODIFY_BY_FULL_NAME'].'</a><br><small style="color:silver">'.$el['MODIFY_BY_EMAIL'].'</small>',
    );
    $data['columns']=$row;
    $arData[]=$data;
}


$arResult['GRID_DATA']=$arData;


if($arResult['PERMS']['EDIT'])
{
    /*// Setup STATUS_ID -->
    $status = '<div id="ACTION_STATUS_WRAPPER" style="display:none;"><select name="ACTION_STATUS_ID" size="1">';
    $status .= '<option value="" title="'.GetMessage('ORDER_STATUS_INIT').'" selected="selected">'.GetMessage('ORDER_STATUS_INIT').'</option>';
    foreach($arResult['STATUS_LIST_WRITE'] as $id => $name):
        $name = htmlspecialcharsbx($name);
        $status .= '<option value="'.$id.'" title="'.$name.'">'.$name.'</option>';
    endforeach;
    $status .= '</select></div>';
    $actionHtml .= $status;
    // <-- Setup STATUS_ID
    

    // Setup ASSIGNED_ID -->
    ob_start();
    $APPLICATION->IncludeComponent('newportal:order.entity.selector',
        '',
        array(
            'ENTITY_TYPE' => 'STAFF',
            'INPUT_NAME' => 'ACTION_ASSIGNED_ID',
            'INPUT_VALUE' => isset($_REQUEST['ACTION_ASSIGNED_ID']) ? intval($_REQUEST['ACTION_ASSIGNED_ID']) : '',
            'FORM_NAME' => $arResult['GRID_ID'],
            'MULTIPLE' => 'N',
            'FILTER' => true
        ),
        false,
        array('HIDE_ICONS' => 'Y')
    );
    $assigned = '<div id="ACTION_ASSIGNED_WRAPPER" style="display:none; margin-right: 10px; margin-left:10px;">'.ob_get_clean().'</div>';
    $actionHtml .= $assigned;
    // <-- Setup ASSIGNED*/

    // Setup DESCRIPTION -->
    $desc = '<div id="ACTION_DESCRIPTION_WRAPPER" style="display:none;"><input type="text" class="typeinput" name="ACTION_DESCRIPTION" size="30"></div>';
    $actionHtml .= $desc;
    // <-- Setup DESCRIPTION

    

    $actionHtml .= '
		<script type="text/javascript">
			BX.ready(
				function(){
				var select = BX.findChild(BX.findPreviousSibling(BX.findParent(BX("ACTION_DESCRIPTION_WRAPPER"), { "tagName":"td" })), { "tagName":"select" });
				BX.bind(
					select,
					"change",
					function(e){
						/*BX("ACTION_STATUS_WRAPPER").style.display = select.value === "set_status" ? "" : "none";
						BX("ACTION_ASSIGNED_WRAPPER").style.display = select.value === "set_assigned" ? "" : "none";*/
						BX("ACTION_DESCRIPTION_WRAPPER").style.display = select.value === "set_description" ? "" : "none";
					}
				)
			}
		);
		</script>';
}

$arActionList = array();
if($arResult['PERMS']['EDIT'])
{

    //if (IsModuleInstalled(CRM_MODULE_CALENDAR_ID))
    //	$arActionList['calendar'] = GetMessage('CRM_DEAL_CALENDAR');

    /*$arActionList['set_status'] =GetMessage('ORDER_AGENT_SET_STATUS');
    $arActionList['set_assigned'] = GetMessage('ORDER_AGENT_SET_ASSIGNED');*/
    $arActionList['set_description'] = GetMessage('ORDER_AGENT_SET_DESCRIPTION');

}
$activityEditorID=''; //Internal
$gridManagerID = $arResult['GRID_ID'].'_MANAGER';
$gridManagerCfg = array(
    'ownerType' => 'AGENT',
    'gridId' => $arResult['GRID_ID'],
    'formName' => "form_{$arResult['GRID_ID']}",
    'allRowsCheckBoxId' => "actallrows_{$arResult['GRID_ID']}",
    'activityEditorId' => $activityEditorID,
    'filterFields' => array()
);


$top=$arResult['PAGE_NUM']*$arResult['PAGE_SIZE']<$arResult['ROWS_COUNT']?$arResult['PAGE_NUM']*$arResult['PAGE_SIZE']:$arResult['ROWS_COUNT'];
$val=$arResult['ROWS_COUNT']==0?'0':(($arResult['PAGE_NUM']-1)*$arResult['PAGE_SIZE']+1).' - '.$top;
$footer=array(
    array(
        'title' => GetMessage('ORDER_ALL'),
        'value' => $arResult['ROWS_COUNT']
    ),
    array(
        'title'=>GetMessage('ORDER_SHOWN'),
        'value'=>$val
    )
);

$APPLICATION->IncludeComponent(
    'newportal:order.interface.grid',
    '',
    array(
        'GRID_ID' => $arResult['GRID_ID'],
        'HEADERS' => $arResult['HEADERS'],
        'SORT' => $arResult['SORT'],
        'SORT_VARS' => $arResult['SORT_VARS'],
        'ROWS' => $arResult['GRID_DATA'],
        'FOOTER' => $footer,
        'EDITABLE' =>  $arResult['PERMS']['EDIT'] ? 'Y' : 'N',
        'ACTIONS' => array(
            'delete' => $arResult['PERMS']['DELETE'],
            'custom_html' => $actionHtml,
            'list' => $arActionList
        ),
        'ACTION_ALL_ROWS' => false,
        'NAV_OBJECT' => $arResult['NAV_OBJECT'],
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