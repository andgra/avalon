<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var \CBitrixComponent $component */

global $APPLICATION;
?>
<table class="order-table-main-directions" cellspacing="0">
    <?$i=0; foreach($arResult['DIRECTION'] as $el): if(++$i % 2 ==1) echo '<tr>';?>
    <td>
        <p><a href="<?=$arResult['PATH_TO_SELF'].$el["ID"].'/'?>"><?=$el["TITLE"]?></a></p>
        <p>
            <? $first=true; foreach($el['CHILD_DIRECTIONS'] as $cEl):?>
                <? if($first!=true) echo '|'; else $first=false;?>
                <a href="<?=$cEl['URL']?>"><?=$cEl["TITLE"]?></a>
            <? endforeach;?>
        </p>
    </td>
    <? if($i % 2 ==0) echo '</tr>'; endforeach; if($i % 2 ==1) echo '</tr>';?>
</table>
<?/*<div id="list">
    <h3><?=GetMessage('CHOOSE_DIRECTION');?></h3>
    <ul class="nav">
        <?
        $previousLevel = 0;
        foreach($arResult['DIRECTION'] as $arItem):
        ?>
        <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
            <?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
        <?endif?>

        <?if ($arItem["IS_PARENT"]):?>
        <li>
            <a href="<?=$arResult['PATH_TO_NOMEN'].'?direction='.$arItem["ID"]?>"><?=$arItem["TITLE"]?></a>
            <ul>
                <?else:?>

                    <li>
                        <a href="<?=$arItem["PATH_TO_NOMEN"]?>"><?=$arItem["TITLE"]?></a>
                    </li>

                <?endif?>
                <?$previousLevel = $arItem["DEPTH_LEVEL"];?>

                <?endforeach?>

                <?if ($previousLevel > 1)://close last item tags?>
                    <?=str_repeat("</ul></li>", ($previousLevel-1) );?>
                <?endif?>
            </ul>
            <button onclick="add_to_basket('DIRECTION','000000000')"><?=GetMessage('CANT_DECIDE')?></button>
</div>
<script>
    function add_to_basket(type,id) {
        BX.ajax({
            url: '<?=$arResult["PATH_TO_BASKET"]?>',
            data: {'ENTITY_ID':id,'ENTITY_TYPE':type, 'MODE':'ADD'},
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

            }
        });

    }

</script>*/?>
