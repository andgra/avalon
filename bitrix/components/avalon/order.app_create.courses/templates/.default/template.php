<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var \CBitrixComponent $component */

global $APPLICATION;
?>
<div id="list">
    <h3><?=$arResult['DIRECTION']['TITLE'];?></h3>
    <p><?=GetMessage('DESCRIPTION')?></p>
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
            <th rowspan="2" width="45%"><?=GetMessage('NAME')?></th>
            <th rowspan="2" width="15%"><?=GetMessage('FORMED_GROUP')?></th>
            <th rowspan="2" width="10%"><?=GetMessage('FREE')?></th>
            <th colspan="3"><?=GetMessage('PRICE')?></th>
        </tr>
        <tr>
            <th><?=GetMessage('PHYSICAL')?></th>
            <th><?=GetMessage('LEGAL')?></th>
            <th><?=GetMessage('OPT')?></th>
        </tr>
        </thead>
        <tbody>
        <?foreach($arResult['NOMEN'] as $arItem):?>
            <tr>
                <td>
                    <p><b><?=$arItem['TITLE']?></b></p>
                    <?foreach($arItem['COURSE'] as $id=>$course):?>
                    <?=$course['TITLE']!=$arItem['TITLE']?'<p>'.$course['TITLE'].'</p>':''?>
                    <p><?=$course['ANNOTATION']?></p>
                    <p><?=GetMessage('COURSE_DURATION',array('#DURATION_CNT#'=>$course['DURATION']))?></p>
                    <?endforeach;?>
                </td>
                <td>
                    <?foreach($arItem['FORMED_GROUP'] as $arGroup):?>
                        <p><a href="<?=$arGroup['URL']?>"><?=$arGroup['DATE_START']?></a></p>
                    <?endforeach?>
                    <p><a href="<?=$arItem['URL']?>"><?=GetMessage('ORDER_NOMEN')?></a></p>
                </td>
                <td>
                    <?foreach($arItem['FORMED_GROUP'] as $arGroup):?>
                        <p><?=$arGroup['FREE']?></p>
                    <?endforeach?>
                    <p>-</p>
                </td>
                <td><?=$arItem['PRICE']['PRICE_PHYSICAL']?></td>
                <td><?=$arItem['PRICE']['PRICE_LEGAL']?></td>
                <td><?=$arItem['PRICE']['PRICE_OPT']?></td>
            </tr>


        <?endforeach?>
        </tbody>
    </table>
</div>
