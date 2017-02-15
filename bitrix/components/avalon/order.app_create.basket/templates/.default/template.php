<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div id="basket">
    <?if(count($arResult['ENTITY'])>0):?>
        <p><?=GetMessage('BASKET_NAME')?></p>
        <table cellspacing="0">
            <thead>
            <tr>
                <th>#</th>
                <th><?=GetMessage('TYPE')?></th>
                <th><?=GetMessage('TITLE')?></th>
                <th><?=GetMessage('DELETE')?></th>
            </tr>
            </thead>
            <tbody>
            <?$i=1; foreach($arResult['ENTITY'] as $arEntity):
                if($arEntity['TYPE']=='FORMED_GROUP') {
                    $arEntity['TITLE']=$arEntity['NOMEN_TITLE'].' ('.$arEntity['DATE_START'].')';
                }elseif($arEntity['TYPE']=='NOMEN') {
                    $arEntity['TITLE']=$arEntity['TITLE'].' ('.GetMessage('NOMEN_TITLE_WITHOUT_DATE').')';
                }
                ?>
                <tr>
                    <td><?=$i++;?></td>
                    <td><?=GetMessage('TYPE_'.$arEntity['TYPE'])?></td>
                    <td><?=$arEntity['TITLE']?></td>
                    <td><span style="padding-bottom: 5px;" class="bx-order-btn-delete" onclick="entity_delete('<?=$arEntity['TYPE']?>','<?=$arEntity['ID']?>')"></span></td>
                </tr>
            <?endforeach;?>
            </tbody>
        </table>

        <p class="order-basket-legend"><?=GetMessage('LEGEND')?></p>

        <button onclick="clear_basket()"><?=GetMessage('CLEAR_BASKET')?></button>
        <button onclick="window.location.href='<?=$arResult['PATH_TO_FORM']?>'"><?=GetMessage('ORDER_BASKET')?></button>
    <?else:?>
        <p><?=GetMessage('BASKET_EMPTY')?></p>
    <?endif;?>
<a id="compare-refresh" style="display:none;" rel="nofollow" href="<?=$_SERVER['REQUEST_URI']?>">Обновить</a>
</div>
<script>
    function clear_basket() {
        BX.ajax({
            url: '<?=$arResult["PATH_TO_BASKET"]?>',
            data: {'MODE':'CLEAR'},
            method: 'POST',
            dataType: 'html',
            timeout: 30,
            async: true,
            processData: true,
            scriptsRunFirst: false,
            emulateOnload: true,
            start: true,
            cache: false,
            onsuccess: function(data){
                console.log(data);
                document.getElementById('compare-refresh').click();
            },
            onfailure: function(){
                console.log('fail');
            }
        });
    }
    function entity_delete(type,id) {
        console.log('<?=$arResult["PATH_TO_BASKET"]?>');
        BX.ajax({
            url: '<?=$arResult["PATH_TO_BASKET"]?>',
            data: {'MODE':'CLEAR','ENTITY_ID':id,'ENTITY_TYPE':type},
            method: 'POST',
            dataType: 'html',
            timeout: 30,
            async: true,
            processData: true,
            scriptsRunFirst: false,
            emulateOnload: true,
            start: true,
            cache: false,
            onsuccess: function(data){
                console.log(data);
                document.getElementById('compare-refresh').click();
            },
            onfailure: function(){
                console.log('fail');
            }
        });
    }
</script>