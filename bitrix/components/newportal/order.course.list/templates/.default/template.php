<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
    <script type="text/javascript">
        function order_course_delete_grid(title, message, btnTitle, path)
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
foreach($arResult['COURSE'] as $id => $el) {
    $data=array(
        'id' => $id,
        'actions' => array(
            array(
                'ICONCLASS' => 'edit',
                'TITLE' => GetMessage('ORDER_COURSE_EDIT_TITLE'),
                'TEXT' => GetMessage('ORDER_COURSE_EDIT'),
                'ONCLICK' => 'jsUtils.Redirect([], \''.$el['PATH_TO_COURSE_EDIT'].'\');',
                'DEFAULT' => true,
            ),
            /*array(
                'ICONCLASS' => 'delete',
                'TITLE' => GetMessage('ORDER_COURSE_DELETE_TITLE'),
                'TEXT' => GetMessage('ORDER_COURSE_DELETE'),
                'ONCLICK' => "order_course_delete_grid('".CUtil::JSEscape(GetMessage('ORDER_COURSE_DELETE_TITLE'))."', '".CUtil::JSEscape(GetMessage('ORDER_COURSE_DELETE_CONFIRM'))."', '".CUtil::JSEscape(GetMessage('ORDER_COURSE_DELETE'))."', '".CUtil::JSEscape($el['PATH_TO_COURSE_DELETE'])."')"
            )*/
        ),
        'data' => $el,
        'editable' => false
    );
    $valExam='<tr><th>'.GetMessage('ORDER_COURSE_EXAM_TITLE').'</th><th>'.GetMessage('ORDER_COURSE_EXAM_MARK').'</th></tr>';
    foreach($el['EXAM'] as $num=>$item) {
        $valExam.='<tr><td>'.$item['EXAM_TITLE'].'</td><td>'.$item['EXAM_MARK'].'</td></tr>';
    }
    $valLiter='<tr><th>'.GetMessage('ORDER_COURSE_LITER_ID').'</th></tr>';
    foreach($el['LITER'] as $num=>$item) {
        $valLiter.='<tr><td>'.$item['LITER_ID'].'</td></tr>';
    }
    $valDoc='<tr><th>'.GetMessage('ORDER_COURSE_DOC_TITLE').'</th></tr>';
    foreach($el['DOC'] as $num=>$item) {
        $valDoc.='<tr><td>'.$item['DOC_TITLE'].'</td></tr>';
    }
    $valNomen='<tr><th>'.GetMessage('ORDER_COURSE_NOMEN_ID').'</th><th>'.GetMessage('ORDER_COURSE_NOMEN_TITLE').'</th></tr>';
    foreach($el['NOMEN'] as $num=>$item) {
        $valNomen.='<tr><td>'.$item['NOMEN_ID'].'</td>';
        $valNomen.='<td><a href="/order/nomen/'.$item['NOMEN_ID'].'/">'.$arResult['NOMEN'][$item['NOMEN_ID']]['TITLE'].'</a></td></tr>';
    }
    $valTeacher='<tr><th>'.GetMessage('ORDER_COURSE_TEACHER_ID').'</th><th>'.GetMessage('ORDER_COURSE_TEACHER_FULL_NAME').'</th></tr>';
    foreach($el['TEACHER'] as $num=>$item) {
        $valTeacher.='<tr><td>'.$item['TEACHER_ID'].'</td>';
        $valTeacher.='<td><a href="/order/physical/'.$item['TEACHER_ID'].'/">'.$arResult['PHYSICAL'][$item['TEACHER_ID']]['FULL_NAME'].'</a></td></tr>';
    }
    $row=array(
        'ID' => '<a href="'.$el['PATH_TO_COURSE_EDIT'].'">'.$el['ID'].'</a>',
        'TITLE' => '<a href="'.$el['PATH_TO_COURSE_EDIT'].'">'.$el['TITLE'].'</a>',
        'ANNOTATION' => $el['ANNOTATION'],
        'DESCRIPTION' => $el['DESCRIPTION'],
        'COURSE_PROG' => $el['COURSE_PROG'],
        'PREV_COURSE' => ($el['PREV_COURSE']!=''?'<a href="'.$el['PATH_TO_PREV_COURSE_EDIT'].'" target="_blank">'.$el['PREV_COURSE_TITLE'].'</a>':''),
        'EXAM' => '<table>'.$valExam.'</table>',
        'LITER' => '<table>'.$valLiter.'</table>',
        'DOC' => '<table>'.$valDoc.'</table>',
        'NOMEN' => '<table>'.$valNomen.'</table>',
        'TEACHER' => '<table>'.$valTeacher.'</table>',
        'MODIFY_BY_ID' => '<a href="'.$el['PATH_TO_MODIFY_BY'].'" target="_blank">'.$el['MODIFY_BY_FULL_NAME'].'</a><br><small style="color:silver">'.$el['MODIFY_BY_EMAIL'].'</small>',
    );
    $data['columns']=$row;
    $arData[]=$data;
}


$arResult['GRID_DATA']=$arData;



$activityEditorID=''; //Internal
$gridManagerID = $arResult['GRID_ID'].'_MANAGER';
$gridManagerCfg = array(
    'ownerType' => 'COURSE',
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