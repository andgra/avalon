<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
    <script type="text/javascript">
        function order_nomen_delete_grid(title, message, btnTitle, path)
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
foreach($arResult['NOMEN'] as $id => $el) {
    $data=array(
        'id' => $id,
        'actions' => array(
            array(
                'ICONCLASS' => 'edit',
                'TITLE' => GetMessage('ORDER_NOMEN_EDIT_TITLE'),
                'TEXT' => GetMessage('ORDER_NOMEN_EDIT'),
                'ONCLICK' => 'jsUtils.Redirect([], \''.$el['PATH_TO_NOMEN_EDIT'].'\');',
                'DEFAULT' => true,
            ),
            /*array(
                'ICONCLASS' => 'delete',
                'TITLE' => GetMessage('ORDER_NOMEN_DELETE_TITLE'),
                'TEXT' => GetMessage('ORDER_NOMEN_DELETE'),
                'ONCLICK' => "order_nomen_delete_grid('".CUtil::JSEscape(GetMessage('ORDER_NOMEN_DELETE_TITLE'))."', '".CUtil::JSEscape(GetMessage('ORDER_NOMEN_DELETE_CONFIRM'))."', '".CUtil::JSEscape(GetMessage('ORDER_NOMEN_DELETE'))."', '".CUtil::JSEscape($el['PATH_TO_NOMEN_DELETE'])."')"
            )*/
        ),
        'data' => $el,
        'editable' => false
    );
    $row=array(
        'ID' => '<a href="'.$el['PATH_TO_NOMEN_EDIT'].'">'.$el['ID'].'</a>',
        'TITLE' => '<a href="'.$el['PATH_TO_NOMEN_EDIT'].'">'.$el['TITLE'].'</a>',
        'SEMESTER' => $el['SEMESTER'],
        'PRICE' => '<table><tr><th>'.GetMessage('ORDER_NOMEN_PRICE_PHYSICAL').'</th>'.
            '<th>'.GetMessage('ORDER_NOMEN_PRICE_LEGAL').'</th><th>'.GetMessage('ORDER_NOMEN_PRICE_OPT').'</th></tr>'.
            '<tr><td>'.$el['PRICE']['PRICE_PHYSICAL'].'</td><td>'.$el['PRICE']['PRICE_LEGAL'].'</td><td>'.$el['PRICE']['PRICE_OPT'].'</td></tr></table>',
        'MODIFY_BY_ID' => '<a href="'.$el['PATH_TO_MODIFY_BY'].'" target="_blank">'.$el['MODIFY_BY_FULL_NAME'].'</a><br><small style="color:silver">'.$el['MODIFY_BY_EMAIL'].'</small>',
    );

    if(!$arResult['INTERNAL'] || strtoupper($arResult['EXTERNAL_TYPE'])!='DIRECTION') {
        $row=array_merge($row,array(
            'DIRECTION_ID' => ($el['DIRECTION_ID']!=''?'<a href="'.$el['PATH_TO_DIRECTION_EDIT'].'" target="_blank">'.$el['DIRECTION_TITLE'].'</a>':''),
        ));
    }
    $data['columns']=$row;
    $arData[]=$data;
}


$arResult['GRID_DATA']=$arData;



$activityEditorID=''; //Internal
$gridManagerID = $arResult['GRID_ID'].'_MANAGER';
$gridManagerCfg = array(
    'ownerType' => 'NOMEN',
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