<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
//$APPLICATION->SetAdditionalCSS("/bitrix/css/main/bootstrap.css");
$component = $this->__component;

?>
<div class="order-detail-lead-wrap-wrap">
    <div class="order-detail-lead-wrap">
        <div class="order-detail-title">
            <div class="order-instant-editor-fld-block order-title-name-wrap">
                <span class="order-detail-title-name"><?=GetMessage("ORDER_SECTION_TITLE")?></span>
            </div>
        </div>
        <div class="order-instant-editor-fld-block">

            <? if ($arResult['START_MANUALLY'] != "Y"): ?>
                <h4 align="center"><? ShowError(GetMessage("ORDER_ERROR_NOT_MANUALLY")) ?></h4>
            <? elseif ($arResult['START_MANUALLY'] == "Y"): ?>
                <form method="post" action="<?= POST_FORM_ACTION_URI; ?>" class="order-synch-form">
                    <?= bitrix_sessid_post() ?>
                    <ul>
                        <? foreach ($arResult['ENTITY'] as $code):
                            echo '<li><div class="order-sync-entity-result-title">' . $arResult['ENTITY_TITLE'][$code] . '</div>' .
                                '<span class="order-sync-entity-result-ajax" id="RESULT_' . $code . '">'.GetMessage("ORDER_PROCESSING").'</span></li>';
                            $data = CUtil::PhpToJSObject(array(
                                "ENTITY" => array($code),
                                "MODE" => "MANUALLY"
                            ));
                            ?>
                            <script>
                                var entity = '<?=$code;?>';
                                var ajax<?=$code;?> = BX.ajax({
                                    url: '/bitrix/components/newportal/order.sync.all/ajax.php',
                                    method: 'POST',
                                    dataType: 'json',
                                    data: {'DATA': "<?=$data;?>"},
                                    onsuccess: function (data) {
                                        console.log(data);
                                        BX('RESULT_<?=$code;?>').innerHTML = '';
                                        if (data['complete'] !== undefined) {
                                            var added = 0;
                                            if (data['complete']['ADD'] !== undefined)
                                                added = data['complete']['ADD']['<?=$code;?>'] !== undefined ?
                                                    data['complete']['ADD']['<?=$code;?>'].length : 0;
                                            var updated = 0;
                                            if (data['complete']['UPDATE'] !== undefined)
                                                updated = data['complete']['UPDATE']['<?=$code;?>'] !== undefined ?
                                                    data['complete']['UPDATE']['<?=$code;?>'].length : 0;
                                            var deleted = 0;
                                            if (data['complete']['DELETE'] !== undefined)
                                                deleted = data['complete']['DELETE']['<?=$code;?>'] !== undefined ?
                                                    data['complete']['DELETE']['<?=$code;?>'].length : 0;
                                            var done = BX.create('span', {
                                                text: '<?=GetMessage("ORDER_COMPLETE")?>'
                                            })
                                            var result = BX.create('span', {
                                                style: {'margin-left': '60px'},
                                                text: '<?=GetMessage("ORDER_ADDED")?>' + added + '<?=GetMessage("ORDER_UPDATED")?>' + updated + '<?=GetMessage("ORDER_DELETED")?>' + deleted
                                            })
                                            BX('RESULT_<?=$code;?>').appendChild(done);
                                            BX('RESULT_<?=$code;?>').appendChild(result);
                                        } else if (data['error'] !== undefined) {
                                            var done = BX.create('span', {
                                                text: '<?=GetMessage("ORDER_ERROR_OCCURS")?>' + data['error']
                                            })
                                            BX('RESULT_<?=$code;?>').appendChild(done);
                                        } else {
                                            var done = BX.create('span', {
                                                text: '<?=GetMessage("ORDER_UNKNOWN_ERROR")?>'
                                            })
                                            BX('RESULT_<?=$code;?>').appendChild(done);
                                        }


                                    },
                                    onfailure: function (data) {
                                    }
                                });
                            </script><?
                        endforeach; ?>
                    </ul>
                </form>
            <? endif; ?>
            <p align="center"><a href="<?=$arResult['BACK_URL']?>" class="btn btn-success btn-lg" type="submit" name="BTN_SYNC_START" title="<?=GetMessage('ORDER_BUTTON_BACK_TITLE')?>"><?=GetMessage('ORDER_BUTTON_BACK')?></a></p>
        </div>
    </div>
</div>
<? /*var_dump($arResult['BX_LIST']);
var_dump($arResult['1C_LIST']);*/
?>
