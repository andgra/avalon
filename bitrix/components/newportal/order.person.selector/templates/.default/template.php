<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$id=$arResult['ID'];
$newSelected=$arResult['SELECTED'];
$type=$arResult['TYPE'];
$contId="order_person_container_".$id;
$buttonTitle=$newSelected['id']==''?GetMessage('ORDER_PERSON_CREATE'):GetMessage('ORDER_PERSON_SELECT');
$title=$newSelected['id']==''?$newSelected['title']:'<a href="'.$newSelected['url'].'" target="_blank">'.$newSelected['title'].'</a>';
//var_dump(COrderApp::GetDirections(array('ID'=>1827)));
//var_dump(COrderEntitySelectorHelper::GetStructure());
//$tree=COrderDirection::GetTree(null,'000000003');
//$tree=COrderDirection::GetChildren('000000003');
//var_dump($tree);
?>
<div class="order-person-wraps-container" id="<?=$contId?>">
    <div class="order-person-info-wrap order-person-info-wrap">
        <span class="order-person-info-title"><?=$title?></span>
        <input type="hidden" class="order-person-input-value" name="ORDER_PERSON_<?=$id?>_VALUE" value="">
        <div class="order-person-info-desc"></div>
    </div>
    <?if(!$arResult['READONLY']):?>
        <div class="order-person-buttons-wrap"><a href="javascript:void(0)" name="orderPersonSelect" onclick="obj_<?=$id?>.ShowForm(); return false"><?=$buttonTitle?></a></div>
    <?endif;?>
    <script>
        BX.ready(
            function()
            {
                obj_<?=$id?>=BX.OrderPerson.Set(
                    '<?=$id?>',
                    '<?=$contId?>',
                    '<?=$type?>',
                    <?=CUtil::PhpToJSObject($newSelected)?>
                );
            }
        );
    </script>
</div>
<?