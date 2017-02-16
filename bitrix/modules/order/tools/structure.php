<?
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

IncludeModuleLangFile(__FILE__);

if (!function_exists('GetDirectionTreeHtml'))
{
    function GetDirectionTreeHtml($arrDir, $selectable = true,$finals=false)
    {
        global $id;
        ob_start();
        ?>
        <ul class="ul-treefree ul-dropfree">
            <?
            $previousLevel = 0;
            foreach ($arrDir as $arItem):
            ?>
                <? if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel): ?>
                    <?= str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"])); ?>
                <? endif ?>

                <? if ($arItem["IS_PARENT"]): ?>
                <li>
                    <a href="javascript:void(0);" <?= $arItem["SELECTED"] == 'Y' ? 'class="order-structure-a-selected"' : '' ?>
                        <?if(!$finals):?>
                            onclick="obOrder['<?= $id ?>'].SelectDirection('<?= $arItem['ID'] ?>')"
                        <?else:?>
                            onclick="obOrder['<?= $id ?>'].SelectItem({type:'direction',value:'<?= $arItem['ID'] ?>'})"
                        <?endif?>
                       id="order_structure_btn_direction_<?= $arItem['ID'] ?>"
                       class="order-structure-provider-button<?/* if ($first == $arItem['ID']) echo " order-structure-provider-button-selected" */?>"
                       hidefocus="true"><?= htmlspecialcharsbx($arItem["TITLE"]) ?></a>
                    <?if($selectable && !$finals){?>
                        <span class="order-plus"
                          onclick="obOrder['<?= $id ?>'].SelectItem({type:'direction',value:'<?= $arItem['ID'] ?>'})"> </span>
                    <?}?>
                    <ul class="ul-treefree ul-dropfree">
                <? else: ?>

                    <li>
                        <a href="javascript:void(0);" <?= $arItem["SELECTED"] == 'Y' ? 'class="order-structure-a-selected"' : '' ?>
                            <?if(!$finals):?>
                                onclick="obOrder['<?= $id ?>'].SelectDirection('<?= $arItem['ID'] ?>')"
                            <?else:?>
                                onclick="obOrder['<?= $id ?>'].SelectItem({type:'direction',value:'<?= $arItem['ID'] ?>'})"
                            <?endif?>
                           id="order_structure_btn_direction_<?= $arItem['ID'] ?>"
                           class="order-structure-provider-button<?/* if ($first == $arItem['ID']) echo " order-structure-provider-button-selected" */?>"
                           hidefocus="true"><?= htmlspecialcharsbx($arItem["TITLE"]) ?></a>
                        <?if($selectable && !$finals){?>
                            <span class="order-plus"
                                  onclick="obOrder['<?= $id ?>'].SelectItem({type:'direction',value:'<?= $arItem['ID'] ?>'})"> </span>
                        <?}?>
                    </li>

                    <?
                endif ?>
                <? $previousLevel = $arItem["DEPTH_LEVEL"]; ?>

            <? endforeach ?>

            <? if ($previousLevel > 1)://close last item tags?>
                <?= str_repeat("</ul></li>", ($previousLevel - 1)); ?>
            <? endif ?>
        </ul>
        <?
        return ob_get_clean();
    }
}
if (!function_exists('GetDirectionListHtml'))
{
    function GetDirectionListHtml($arrDir, $selectable = true,$finals=false)
    {
        global $id;
        ob_start();
        ?>
        <ul class="order-structure-nav">
            <? foreach ($arrDir as $arItem): ?>
                <li>
                    <a href="javascript:void(0);"
                        <?if(!$finals):?>
                            onclick="obOrder['<?= $id ?>'].SelectDirection('<?= $arItem['ID'] ?>')"
                        <?else:?>
                            onclick="obOrder['<?= $id ?>'].SelectItem({type:'direction',value:'<?= $arItem['ID'] ?>'})"
                        <?endif?>

                       id="order_structure_btn_direction_<?= $arItem['ID'] ?>"
                       class="<?= $arItem["SELECTED"] == 'Y' ? 'order-structure-a-selected' : '' ?>"
                        ><?= htmlspecialcharsbx($arItem["TITLE"]) ?></a>
                    <?if($selectable && !$finals){?>
                        <button class="order-icon order-icon-plus"
                                onclick="obOrder['<?= $id ?>'].SelectItem({type:'direction',value:'<?= $arItem['ID'] ?>'})"></button>
                    <?}?>
                </li>
            <? endforeach ?>
        </ul>
        <?
        return ob_get_clean();
    }
}

if (!function_exists('GetNomenListHtml')) {
    function GetNomenListHtml($arrNomen, $selectable = true,$finals=false)
    {
        global $id;
        ob_start();
        ?>
        <ul class="order-structure-nav">
            <? foreach ($arrNomen as $arItem): ?>
                <li>
                    <a href="javascript:void(0);"
                       <?if(!$finals):?>
                           onclick="obOrder['<?= $id ?>'].SelectNomen('<?= $arItem['DIRECTION_ID'] ?>','<?= $arItem['ID'] ?>')"
                       <?else:?>
                           onclick="obOrder['<?= $id ?>'].SelectItem({type:'nomen',value:'<?= $arItem['ID'] ?>'})"
                       <?endif?>
                       id="order_structure_btn_nomen_<?= $arItem['ID'] ?>"
                       class="<?= $arItem["SELECTED"] == 'Y' ? 'order-structure-a-selected' : '' ?>"
                        ><?= htmlspecialcharsbx($arItem["TITLE"]) ?></a>
                    <?if($selectable && !$finals){?>
                        <button class="order-icon order-icon-plus"
                                        onclick="obOrder['<?= $id ?>'].SelectItem({type:'nomen',value:'<?= $arItem['ID'] ?>'})"></button>
                    <?}?>
                </li>
            <? endforeach ?>
        </ul>
        <?
        return ob_get_clean();
    }
}

if (!function_exists('GetGroupListHtml')) {
    function GetGroupListHtml($arrGroup, $arrFormedGroup, $selectableG = true, $selectableFG = true)
    {
        global $id;
        ob_start();
        ?>
        <ul class="order-structure-nav">
            <? foreach ($arrGroup as $arItem1): ?>
                <li>
                    <a href="javascript:void(0);"
                       class="<?= $arItem1["SELECTED"] == 'Y' ? 'order-structure-a-selected' : '' ?>"
                       id="order_structure_btn_group_<?= $arItem1['ID'] ?>"
                        <?if($selectableG){?>
                        onclick="obOrder['<?= $id ?>'].SelectItem({type:'group',value:'<?= $arItem1['ID'] ?>'})"
                        <?}?>
                    ><?= $arItem1["TITLE"] ?></a>
                    <ul class="order-structure-nav">
                        <? foreach ($arrFormedGroup as $arItem2) if ($arItem2['GROUP_ID'] == $arItem1['ID']): ?>
                            <li>
                                <a href="javascript:void(0);"
                                   class="<?= $arItem2["SELECTED"] == 'Y' ? 'order-structure-a-selected' : '' ?>"
                                   id="order_structure_btn_formed_group_<?= $arItem2['ID'] ?>"
                                    <?if($selectableFG){?>
                                           onclick="obOrder['<?= $id ?>'].SelectItem({type:'formed_group',value:'<?= $arItem2['ID'] ?>'})"
                                    <?}?>
                                ><?= $arItem2["DATE_START"] . ' - ' . $arItem2["DATE_END"] ?></a>
                                <font <? if ($arItem2["FREE"] == 0): ?> color="orange"<?php elseif ($arItem2["FREE"] < 0): ?> color="red"<? endif; ?>
                                    >(<?= $arItem2["ENROLLED"] . '/' . $arItem2["FREE"] . '/' . $arItem2["MAX"] ?>
                                    )</font>
                                | <?= $arItem1["D"] ?>
                            </li>
                        <? endif ?>
                    </ul>
                </li>
            <? endforeach ?>
        </ul>
        <?
        return ob_get_clean();
    }
}

if (!CModule::IncludeModule('order')) {
    ?>
    <div class="order-structure-container"
         style="padding:20px"><? echo GetMessage("order_structure_module_order_not_installed") ?></div>
    <?
    die();
}
if (!$USER->IsAuthorized()):
    ?>
    <div class="order-structure-container"
         style="padding:20px"><? echo GetMessage("order_structure_access_denied") ?></div>
    <?
    die();
endif;
$arParams = false;
if (isset($_REQUEST["id"]) && $_REQUEST["id"] != '') {
    $id = $_REQUEST["id"];
} else {
    ?>
    <div class="order-structure-container"
         style="padding:20px"><? echo GetMessage("order_structure_no_id") ?></div>
    <?
    die();
}
if (isset($_REQUEST["mode"]) && $_REQUEST["mode"] != '') {
    $mode = $_REQUEST["mode"];
} else {
    ?>
    <div class="order-structure-container"
         style="padding:20px"><? echo GetMessage("order_structure_no_mode") ?></div>
    <?
    die();
}

$path['DIRECTION']=OrderCheckPath('PATH_TO_DIRECTION_EDIT', '', '/order/direction/edit/#direction_id#');
$path['NOMEN']=OrderCheckPath('PATH_TO_NOMEN_EDIT', '', '/order/nomen/edit/#nomen_id#');
$path['GROUP']=OrderCheckPath('PATH_TO_GROUP_EDIT', '', '/order/group/edit/#group_id#');
$path['FORMED_GROUP']=OrderCheckPath('PATH_TO_FORMED_GROUP_EDIT', '', '/order/formed_group/edit/#formed_group_id#');
$answer = array();
$filter = array();
$selectable = array('direction', 'nomen', 'group', 'formed_group');
if (isset($_REQUEST["arParams"]) && is_array($_REQUEST["arParams"])) {
    if (isset($_REQUEST["arParams"]['selectable']) && !empty($_REQUEST["arParams"]['selectable'])) {
        foreach ($selectable as $num => $itm) {
            if (!in_array($itm, $_REQUEST["arParams"]['selectable'])) {
                unset($selectable[$num]);
            }
        }
    }
    $filter['DIRECTION_ID']=isset($_REQUEST["arParams"]['filter']['DIRECTION_ID'])?
        $_REQUEST["arParams"]['filter']['DIRECTION_ID']:null;
    $filter['NOMEN_ID']=isset($_REQUEST["arParams"]['filter']['NOMEN_ID'])?
        $_REQUEST["arParams"]['filter']['NOMEN_ID']:null;
    $filter['GROUP_ID']=isset($_REQUEST["arParams"]['filter']['GROUP_ID'])?
        $_REQUEST["arParams"]['filter']['GROUP_ID']:null;
}

$fNomen=$fGroup=array();
if(isset($filter['DIRECTION_ID']) || isset($filter['NOMEN_ID']) || isset($filter['GROUP_ID'])) {
    $answer['filter'] = $filter;
    if(!is_null($filter['DIRECTION_ID'])) {
        $fNomen['DIRECTION_ID']=$filter['DIRECTION_ID'];
        $fGroup['DIRECTION_ID']=$filter['DIRECTION_ID'];
    }
    if(!is_null($filter['NOMEN_ID'])) {
        $fNomen['ID']=$filter['NOMEN_ID'];
        $fGroup['NOMEN_ID']=$filter['NOMEN_ID'];
    }
    if(!is_null($filter['GROUP_ID'])) {
        $fGroup['ID']=$filter['GROUP_ID'];
    }
}

$arNomenFilter=$arGroupFilter=array();
$selected = $_REQUEST["obSelected"];
$selected['type']=isset($selected['type'])?strtolower($selected['type']):'';
switch ($selected['type']) {
    case 'formed_group':
        $sElem = COrderFormedGroup::GetByID($selected['value']);
        $selected['formed_group_id'] = $sElem['ID'];
        $selected['group_id'] = false;
        $selected['nomen_id'] = $sElem['NOMEN_ID'];
        $arGroupFilter['NOMEN_ID'] = $sElem['NOMEN_ID'];
        $selected['direction_id'] = $sElem['DIRECTION_ID'];
        break;
    case 'group':
        $sElem = COrderGroup::GetByID($selected['value']);
        $selected['formed_group_id'] = false;
        $selected['group_id'] = $sElem['ID'];
        $selected['nomen_id'] = $sElem['NOMEN_ID'];
        $arGroupFilter['NOMEN_ID'] = $sElem['NOMEN_ID'];
        $selected['direction_id'] = $sElem['DIRECTION_ID'];
        break;
    case 'nomen':
        $sElem = COrderNomen::GetByID($selected['value']);
        $selected['formed_group_id'] = false;
        $selected['group_id'] = false;
        $selected['nomen_id'] = $sElem['ID'];
        $arGroupFilter['NOMEN_ID'] = $sElem['ID'];
        $selected['direction_id'] = $sElem['DIRECTION_ID'];
        break;
    case 'direction':
        $sElem = COrderDirection::GetByID($selected['value']);
        $selected['formed_group_id'] = false;
        $selected['group_id'] = false;
        $selected['nomen_id'] = false;
        $selected['direction_id'] = $sElem['ID'];
        break;
}
if ($mode == 'layout') {
    $selTitle=array(
        'direction'=>'',
        'nomen'=>'',
    );
    if(isset($filter['DIRECTION_ID'])) {
        $res = COrderDirection::GetListEx(array(),array('ID'=>$filter['DIRECTION_ID']));
        while($el=$res->Fetch()) {
            $el['URL']=CComponentEngine::makePathFromTemplate($path['DIRECTION'], array('direction_id' => $el['ID']));
            $dirList[$el['ID']]=$el;
        }
    } else {
        $res = COrderDirection::GetTreeMenu();
        foreach ($res as $el) {
            $el['URL']=CComponentEngine::makePathFromTemplate($path['DIRECTION'], array('direction_id' => $el['ID']));
            $dirList[$el['ID']]=$el;
        }
    }

    if(count($dirList)==1) {
        $selTitle['direction']=reset($dirList);
        $selTitle['direction']=$selTitle['direction']['TITLE'];
    }
    if (isset($_REQUEST["obSelected"]) && is_array($_REQUEST["obSelected"])
        && isset($_REQUEST["obSelected"]['type']) && isset($_REQUEST["obSelected"]['value'])
    ) {
        if (isset($selected['direction_id']) && $selected['direction_id'] != ''
            && is_array($dirChildren = array_keys(COrderDirection::GetChildren($selected['direction_id'])))
        ) {
            $dirChildren = array_merge($dirChildren, array($selected['direction_id']));
        } else {
            $dirChildren = array($selected['direction_id']);
        }
        //$dirChildren=false;
        $arNomenFilter['DIRECTION_ID'] = $dirChildren;
        $arGroupFilter['DIRECTION_ID'] = $dirChildren;


    }
    //var_dump($arNomenFilter);
    $arNomenFilter=array_merge($arNomenFilter,$fNomen);
    foreach ($arNomenFilter as $k => $v) {
        if($v=='') unset($arNomenFilter[$k]);
    }

    $arGroupFilter=array_merge($arGroupFilter,$fGroup);
    foreach ($arGroupFilter as $k => $v) {
        if($v=='') unset($arGroupFilter[$k]);
    }

    $res = COrderNomen::GetListEx(array(), $arNomenFilter);
    while ($el = $res->Fetch()) {
        if (isset($selected['nomen_id']) && $selected['type'] == 'nomen'
            && $el['ID'] == $selected['nomen_id']
        ) {
            $selTitle['direction']=$el['DIRECTION_TITLE'];
            $el['SELECTED'] = 'Y';
        } else
            $el['SELECTED'] = 'N';
        $el['URL']=CComponentEngine::makePathFromTemplate($path['NOMEN'], array('nomen_id' => $el['ID']));
        $nomenList[$el['ID']] = $el;
    }
    $res = COrderGroup::GetListEx(array(), $arGroupFilter);
    $answer['gFilter']=$arGroupFilter;
    while ($el = $res->Fetch()) {
        if (isset($selected['group_id']) && $selected['type'] == 'group'
            && $el['ID'] == $selected['group_id']
        ) {
            $selTitle['nomen']=$el['NOMEN_TITLE'];
            $selTitle['direction']=$el['DIRECTION_TITLE'];
            $el['SELECTED'] = 'Y';
        } else
            $el['SELECTED'] = 'N';
        $el['URL']=CComponentEngine::makePathFromTemplate($path['GROUP'], array('group_id' => $el['ID']));
        $groupList[$el['ID']] = $el;
    }
    $res = COrderFormedGroup::GetListEx(array(), $arGroupFilter);
    while ($el = $res->Fetch()) {
        if (isset($selected['formed_group_id']) && $selected['type'] == 'formed_group'
            && $el['ID'] == $selected['formed_group_id']
        ) {
            $selTitle['nomen']=$el['NOMEN_TITLE'];
            $selTitle['direction']=$el['DIRECTION_TITLE'];
            $el['SELECTED'] = 'Y';
        } else
            $el['SELECTED'] = 'N';
        $el['URL']=CComponentEngine::makePathFromTemplate($path['FORMED_GROUP'], array('formed_group_id' => $el['ID']));
        $formedGroupList[$el['ID']] = $el;
    }
    foreach ($dirList as $num => $dir) {
        /*if(isset($filter['DIRECTION_ID']) && $dir['ID']!=$filter['DIRECTION_ID']) {
            unset($dirList[$num]);
            continue;
        }*/
        if (isset($selected['direction_id']) && $selected['type'] == 'direction'
            && $dir['ID'] == $selected['direction_id']
        ) {
            $dirList[$num]['SELECTED'] = 'Y';
        } else
            $dirList[$num]['SELECTED'] = 'N';
    }
    if(isset($selected['type']) && $selected['type']!='') {
        $opened=($selected['type']=='formed_group'?'group':$selected['type']);
    } elseif(count($dirList)>1) {
        $opened='direction';
    } elseif(count($nomenList)>1) {
        $opened='nomen';
    } else {
        $opened='group';
    }
    if(count($dirList)<2) {
        $disabled[]='direction';
    }
    if(count($nomenList)<2) {
        $disabled[]='nomen';
    }
    $hidden=array();
    if(!in_array('formed_group',$selectable) && !in_array('group',$selectable)) {
        $hidden[]='group';
        if(!in_array('nomen',$selectable)) {
            $hidden[]='nomen';
        }
    }
    //var_dump($arGroupFilter);
    ob_start();
    ?>
    <div class="order-structure-container">

        <div class="order-structure-section-title<?=in_array('direction',$disabled)?' order-structure-disabled':''?>">
            <span class="order-structure-vertical-drop
                <?=$opened=='direction'?'order-structure-vertical-opened"':''?>"></span>
            <b><?= GetMessage('order_structure_section_title_direction') ?></b>
            <span class="order-structure-selected-item-title"><?=$selTitle['direction'];?></span>
        </div>

        <div class="order-structure-block-container order-structure-direction-container"
            <?=$opened!='direction'?'style="display: none"':''?>>
            <div class="order-structure-list">
                <?= isset($filter['DIRECTION_ID'])? GetDirectionListHtml($dirList,in_array('direction',$selectable),in_array('nomen',$hidden))
                    :GetDirectionTreeHtml($dirList,in_array('direction',$selectable),in_array('nomen',$hidden))?>
            </div>
        </div>


        <?if(!in_array('nomen',$hidden)):?>
        <div class="order-structure-section-title<?=in_array('nomen',$disabled)?' order-structure-disabled':''?>">
            <span class="order-structure-vertical-drop
                <?=$opened=='nomen'?'order-structure-vertical-opened"':''?>"></span>
            <b><?= GetMessage('order_structure_section_title_nomen') ?></b>
            <span class="order-structure-selected-item-title"><?=$selTitle['nomen'];?></span>
        </div>

        <div class="order-structure-block-container order-structure-nomen-container"
            <?=$opened!='nomen'?'style="display: none"':''?>>
            <div class="order-structure-search">
                <input type="text" class="order-search-inp" placeholder="search"
                       oninput="obOrder['<?= $id ?>'].SearchNomen(this.value)" name="search_nomen_input">
            </div>
            <div class="order-structure-list">
                <?= GetNomenListHtml($nomenList,in_array('nomen',$selectable),in_array('group',$hidden)) ?>
            </div>
        </div>
        <?endif;?>

        <?if(!in_array('group',$hidden)):?>
        <div class="order-structure-section-title">
            <span class="order-structure-vertical-drop
                <?=$opened=='group'?'order-structure-vertical-opened"':''?>"></span>
            <b style="margin-right: 20px;"><?= GetMessage('order_structure_section_title_group') ?></b>
        </div>

        <div class="order-structure-block-container order-structure-group-container"
            <?=$opened!='group'?'style="display: none"':''?>>
            <div class="order-structure-search">
                <input type="text" class="order-search-inp" placeholder="search"
                       oninput="obOrder['<?= $id ?>'].SearchGroup(this.value)" name="search_group_input">
            </div>
            <div class="order-structure-list">
                <?= GetGroupListHtml($groupList, $formedGroupList,in_array('group',$selectable),in_array('formed_group',$selectable)) ?>
            </div>
        </div>
        <?endif;?>
    </div>
    <?
    $answer['layout'] = ob_get_clean();
    $answer['list']=array('direction'=>$dirList,'nomen'=>$nomenList,'group'=>$groupList,'formed_group'=>$formedGroupList);
    $answer['post'] = $_POST;
} elseif ($mode == 'direction') {
    $arNomenFilter=$arGroupFilter=array();
    if (isset($_REQUEST["direction"])) {
        $dirChildren = array_keys(COrderDirection::GetChildren($_REQUEST["direction"]));
        if ($_REQUEST["direction"] != '' && is_array($dirChildren)) {
            $dirChildren = array_merge($dirChildren, array($_REQUEST["direction"]));
        } else {
            $dirChildren = $_REQUEST["direction"];
        }
        //$dirChildren=false;
        $arNomenFilter['DIRECTION_ID'] = $dirChildren;
        $arGroupFilter['DIRECTION_ID'] = $dirChildren;
    }
    $res = COrderNomen::GetListEx(array(), $arNomenFilter);
    while ($el = $res->Fetch()) {
        if (isset($selected['nomen_id']) && $selected['type'] == 'nomen'
            && $el['ID'] == $selected['nomen_id']
        )
            $el['SELECTED'] = 'Y';
        else
            $el['SELECTED'] = 'N';
        $el['URL']=CComponentEngine::makePathFromTemplate($path['NOMEN'], array('nomen_id' => $el['ID']));
        $nomenList[$el['ID']] = $el;
    }
    $res = COrderGroup::GetListEx(array(), $arGroupFilter);
    while ($el = $res->Fetch()) {
        if (isset($selected['group_id']) && $selected['type'] == 'group'
            && $el['ID'] == $selected['group_id']
        )
            $el['SELECTED'] = 'Y';
        else
            $el['SELECTED'] = 'N';
        $el['URL']=CComponentEngine::makePathFromTemplate($path['GROUP'], array('group_id' => $el['ID']));
        $groupList[$el['ID']] = $el;
    }
    $res = COrderFormedGroup::GetListEx(array(), $arGroupFilter);
    while ($el = $res->Fetch()) {
        if (isset($selected['formed_group_id']) && $selected['type'] == 'formed_group'
            && $el['ID'] == $selected['formed_group_id']
        )
            $el['SELECTED'] = 'Y';
        else
            $el['SELECTED'] = 'N';
        $el['URL']=CComponentEngine::makePathFromTemplate($path['FORMED_GROUP'], array('formed_group_id' => $el['ID']));
        $formedGroupList[$el['ID']] = $el;
    }

    $answer['nomen'] = GetNomenListHtml($nomenList,in_array('nomen',$selectable));
    $answer['group'] = GetGroupListHtml($groupList, $formedGroupList,in_array('group',$selectable),in_array('formed_group',$selectable));
    $answer['list']=array('nomen'=>$nomenList,'group'=>$groupList,'formed_group'=>$formedGroupList);
    $answer['filters']=array('nomen'=>$arNomenFilter,'group'=>$arGroupFilter);
} elseif ($mode == 'nomen') {
    $arGroupFilter = array();
    if (isset($_REQUEST["nomen"]) && $_REQUEST["nomen"] != '') {
        $arGroupFilter['NOMEN_ID'] = $_REQUEST["nomen"];
        //$arGroupFilter['NOMEN_ID'] = '';
    }
    $res = COrderGroup::GetListEx(array(), $arGroupFilter);
    while ($el = $res->Fetch()) {
        if (isset($selected['group_id']) && $selected['type'] == 'group'
            && $el['ID'] == $selected['group_id']
        )
            $el['SELECTED'] = 'Y';
        else
            $el['SELECTED'] = 'N';
        $el['URL']=CComponentEngine::makePathFromTemplate($path['GROUP'], array('group_id' => $el['ID']));
        $groupList[$el['ID']] = $el;
    }
    $res = COrderFormedGroup::GetListEx(array(), $arGroupFilter);
    while ($el = $res->Fetch()) {
        if (isset($selected['formed_group_id']) && $selected['type'] == 'formed_group'
            && $el['ID'] == $selected['formed_group_id']
        )
            $el['SELECTED'] = 'Y';
        else
            $el['SELECTED'] = 'N';
        $el['URL']=CComponentEngine::makePathFromTemplate($path['FORMED_GROUP'], array('formed_group_id' => $el['ID']));
        $formedGroupList[$el['ID']] = $el;
    }
    $answer['group'] = GetGroupListHtml($groupList, $formedGroupList,in_array('group',$selectable),in_array('formed_group',$selectable));
    $answer['list']=array('group'=>$groupList,'formed_group'=>$formedGroupList);
} elseif ($mode == 'search_nomen') {

    $nomenList=isset($_REQUEST['list'])?$_REQUEST['list']:array();
    foreach($nomenList as $id=>$el) {
        if(isset($_REQUEST["search"]) && $_REQUEST["search"] != '' &&
            strripos(strtolower($el['TITLE']),strtolower($_REQUEST["search"]))===false) {
            $answer['comp'][]=array($el['TITLE'],$_REQUEST["search"],strripos($_REQUEST["search"],$el['TITLE']));
            unset($nomenList[$id]);
            continue;
        }
        if (isset($selected['nomen_id']) && $selected['type'] == 'nomen'
            && $el['ID'] == $selected['nomen_id']
        )
            $el['SELECTED'] = 'Y';
        else
            $el['SELECTED'] = 'N';
        $nomenList[$id] = $el;
    }
    $answer['nomen'] = GetNomenListHtml($nomenList,in_array('nomen',$selectable));
    //$answer['list']=array('nomen'=>$nomenList);
    $answer['req']=$_REQUEST;

} elseif ($mode == 'search_group') {
    $formedGroupList=isset($_REQUEST['fGList'])?$_REQUEST['fGList']:array();
    foreach($formedGroupList as $id=>$el) {
        if(isset($_REQUEST["search"]) && $_REQUEST["search"] != ''
            && strripos(strtolower($el['TITLE']),strtolower($_REQUEST["search"]))===false
            /*&& strripos(strtolower($el['DATE_START']),strtolower($_REQUEST["search"]))===false
            && strripos(strtolower($el['DATE_END']),strtolower($_REQUEST["search"]))===false*/
        ) {
            unset($formedGroupList[$id]);
            continue;
        }
        if (isset($selected['formed_group_id']) && $selected['type'] == 'formed_group'
            && $el['ID'] == $selected['formed_group_id']
        )
            $el['SELECTED'] = 'Y';
        else
            $el['SELECTED'] = 'N';
        $formedGroupList[$id] = $el;
    }
    $gIds = array();
    foreach ($formedGroupList as $v) {
        $gIds[] = $v['GROUP_ID'];
    }

    $groupList=isset($_REQUEST['gList'])?$_REQUEST['gList']:array();
    foreach($groupList as $id=>$el) {
        if(!in_array($el['ID'],$gIds) && isset($_REQUEST["search"]) && $_REQUEST["search"] != ''
            && strripos(strtolower($el['TITLE']),strtolower($_REQUEST["search"]))===false
            /*&& strripos(strtolower($el['DATE_START']),strtolower($_REQUEST["search"]))===false
            && strripos(strtolower($el['DATE_END']),strtolower($_REQUEST["search"]))===false*/
        ) {
            unset($groupList[$id]);
            continue;
        }
        if (isset($selected['group_id']) && $selected['type'] == 'group'
            && $el['ID'] == $selected['group_id']
        )
            $el['SELECTED'] = 'Y';
        else
            $el['SELECTED'] = 'N';
        $groupList[$id] = $el;
    }
    $answer['gIds']=$gIds;
    $answer['group'] = GetGroupListHtml($groupList, $formedGroupList,in_array('group',$selectable),in_array('formed_group',$selectable));
    $answer['list']=array('group'=>$groupList,'formed_group'=>$formedGroupList);
}
echo json_encode($answer);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php");
        ?>
