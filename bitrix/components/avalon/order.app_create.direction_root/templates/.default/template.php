<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var \CBitrixComponent $component */

global $APPLICATION;
?>
<h3><?=$arResult['DIRECTION']['TITLE'];?></h3>
<ul>
    <?foreach($arResult['DIRECTION']['CHILDREN'] as $id=>$el):?>
    <li><a href="<?=$el['URL']?>"><?=$el['TITLE']?></a></li>
    <?endforeach;?>
</ul>
