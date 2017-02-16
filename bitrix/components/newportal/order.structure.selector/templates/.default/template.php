<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$id=$arResult['ID'];
$contId="order_structure_container_".$id;

//var_dump(COrderApp::GetDirections(array('ID'=>1827)));
//var_dump(COrderEntitySelectorHelper::GetStructure());
//$tree=COrderDirection::GetTree(null,'000000003');
//$tree=COrderDirection::GetChildren('000000003');
//var_dump($tree);
?>
<div class="order-structure-wraps-container" id="<?=$contId?>">
    <div class="order-structure-info-wrap">
        <span class="order-structure-info-title"></span>
        <input type="hidden" class="order-structure-input-type" name="<?=$id?>_TYPE" value="">
        <input type="hidden" class="order-structure-input-value" name="<?=$id?>_VALUE" value="">
    </div>
    <?if(!$arResult['READONLY']):?>
        <div class="order-structure-buttons-wrap"><a style="text-decoration: underline" href="javascript:void(0)" name="orderStructureSelect" onclick="obj.ShowForm({}); return false">Выбрать</a></div>
    <?endif;?>
    <script>
        BX.ready(
            function()
            {
                obj=BX.OrderStructure.Set(
                    '<?=$id?>',
                    '<?=$contId?>',
                    <?=CUtil::PhpToJSObject($arResult['SELECTED'])?>,
                    <?=CUtil::PhpToJSObject($arResult['PARAMS'])?>
                );
                //BX.addCustomEvent(BX.OrderStructure, 'onSelectDirection', OrderPermAccessSelectProvider);
            }
        );
    </script>
</div>
<?