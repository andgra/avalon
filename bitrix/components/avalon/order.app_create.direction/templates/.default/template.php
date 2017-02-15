<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var \CBitrixComponent $component */

global $APPLICATION;
?>
<h3><?=$arResult['DIRECTION']['TITLE'];?></h3>
<ul>

    <?php if($arResult['DIRECTION']['PARENT_ID']=='000000003'):?>
        <li><a href="<?=$arResult['PATH_TO_COURSES']?>"><?=GetMessage("SCHEDULE")?></a></li>
    <?elseif($arResult['DIRECTION']['PARENT_ID']=='000000017'
        || $arResult['DIRECTION']['PARENT_ID']=='000000001'):?>
        <li><a href="<?=$arResult['DIRECTION']['URL']?>"><?=GetMessage("CREATE_APP")?></a></li>
    <?endif;?>
</ul>
