<?
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

IncludeModuleLangFile(__FILE__);

if(!function_exists('GetPhysicalListHtml'))
{
    function GetPhysicalListHtml($arrPhys,$createMode)
    {
        global $id;
        ob_start();
        ?>
        <div class="order-person-overflow-div order-person-physical-list">
            <table class="order-person-list-table">
                <tr>
                    <th><?=GetMessage("order_person_physical_field_id")?></th>
                    <th><?=GetMessage("order_person_physical_field_title")?></th>
                    <th><?=GetMessage("order_person_physical_field_phone")?></th>
                    <th><?=GetMessage("order_person_physical_field_email")?></th>
                    <th><?=GetMessage("order_person_physical_field_select")?></th>
                </tr>
                <? foreach ($arrPhys as $arItem):?>
                <tr id="<?=$id?>_list_tr_<?=$arItem["ID"]?>"
                <?=($arItem['SELECTED']==true)?'style="background-color:#A1F43D;"':''?>>
                    <td><?=$arItem["ID"]?></td>
                    <td><?=$arItem["FULL_NAME"]?></td>
                    <td><?=$arItem["PHONE"]?></td>
                    <td><?=$arItem["EMAIL"]?></td>
                    <td><button class="order-icon order-icon-plus"
                                onclick="<?if($createMode):?>obOrder['<?=$id?>'].ChooseFromList('<?=$arItem['ID']?>');
                                    <?else:?>obOrder['<?=$id?>'].SelectItem('<?=$arItem['ID']?>');<?endif;?>
                                    return false;"></button></td>
                </tr>
                <?endforeach;?>
            </table>
        </div>
        <?
        return ob_get_clean();
    }
}

if(!function_exists('GetAgentListHtml'))
{
    function GetAgentListHtml($arrList,$createMode,$legal)
    {
        global $id;
        ob_start();
        ?>
        <div class="order-person-overflow-div order-person-agent-list<?=$legal=='Y'?' order-person-legal':''?>">
            <table class="order-person-list-table">
                <tr>
                    <th><?=GetMessage("order_person_agent_field_id")?></th>
                    <th><?=GetMessage("order_person_agent_field_title")?></th>
                    <th><?=GetMessage("order_person_agent_field_phone")?></th>
                    <th><?=GetMessage("order_person_agent_field_email")?></th>
                    <?if($legal=='N' && $createMode):?><th><?=GetMessage("order_person_agent_field_agent")?></th><?endif;?>
                    <th><?=GetMessage("order_person_agent_field_select")?></th>
                </tr>
                <? foreach ($arrList as $arItem): if($arItem['LEGAL']==$legal):?>
                <tr id="<?=$id?>_list_tr_<?=$arItem["ID"]?>"
                    <?=($arItem['SELECTED']==true)?'style="background-color:#A1F43D;"':''?>>
                    <td><?=$arItem["ID"]?></td>
                    <td><?=$legal=='N' && $createMode?$arItem["FULL_NAME"]:$arItem["TITLE"]?></td>
                    <td><?=$arItem["PHONE"]?></td>
                    <td><?=$arItem["EMAIL"]?></td>
                    <?if($legal=='N' && $createMode):?><td><?=$arItem["IS_AGENT"]?'y':'n'?></td><?endif;?>
                    <td><button class="order-icon order-icon-plus"
                                onclick="<?if($createMode):?>obOrder['<?=$id?>'].ChooseFromList('<?=$arItem['ID']?>');
                                    <?else:?>obOrder['<?=$id?>'].SelectItem('<?=$arItem['ID']?>');<?endif;?>
                                    return false;"></button></td>
                </tr>
                <?endif; endforeach;?>
            </table>
        </div>
        <?
        return ob_get_clean();
    }
}



if(!CModule::IncludeModule('order')) {
    ?>
    <div class="order-person-container" style="padding:20px"><?echo GetMessage("order_person_module_order_not_installed")?></div>
    <?
    die();
}
if(!$USER->IsAuthorized()):
    ?>
    <div class="order-person-container" style="padding:20px"><?echo GetMessage("order_person_access_denied")?></div>
    <?
    die();
endif;
$arParams = false;
if(isset($_REQUEST["id"]) && $_REQUEST["id"]!='') {
    $id=$_REQUEST["id"];
} else {
    ?>
    <div class="order-person-container" style="padding:20px"><?echo GetMessage("order_person_no_id")?></div>
    <?
    die();
}
if(isset($_REQUEST["type"]) && $_REQUEST["type"]!='') {
    $type=$_REQUEST["type"];
} else {
    ?>
    <div class="order-person-container" style="padding:20px"><?echo GetMessage("order_person_no_type")?></div>
    <?
    die();
}
if(isset($_REQUEST["mode"]) && $_REQUEST["mode"]!='') {
    $mode=$_REQUEST["mode"];
} else {
    ?>
    <div class="order-person-container" style="padding:20px"><?echo GetMessage("order_person_no_mode")?></div>
    <?
    die();
}
$path['PHYSICAL']=OrderCheckPath('PATH_TO_PHYSICAL_EDIT', '', '/order/physical/edit/#physical_id#');
$path['CONTACT']=OrderCheckPath('PATH_TO_CONTACT_EDIT', '', '/order/contact/edit/#contact_id#');
$path['AGENT']=OrderCheckPath('PATH_TO_AGENT_EDIT', '', '/order/agent/edit/#agent_id#');
$answer=array();
switch(strtolower($type)) {
    case 'physical':
        if ($mode=='layout') {
            ob_start();
            $selected = $_REQUEST["obSelected"];
            $res=COrderPhysical::GetListEx();
            $physicalList=array();
            while($el=$res->Fetch()) {
                if(isset($selected['id']) && $selected['id']==$el['ID']) {
                    $el['SELECTED']=true;
                } else {
                    $el['SELECTED']=false;
                }
                $el['URL']=CComponentEngine::makePathFromTemplate($path['PHYSICAL'], array('physical_id' => $el['ID']));
                $physicalList[$el['ID']]=$el;
            }
            ?>
            <div class="order-person-container">
                <div class="order-person-errors-container"></div>
            <?
            if (!isset($selected['id']) || $selected['id'] == '') {
                $sTitle=$sPhone=$sEmail=array();
                $arTitle=explode(' ',$selected['title']);
                if(isset($selected['title']) && $selected['title']!='') {
                    $sTitle = array_filter($physicalList, function ($el) use ($arTitle) {
                        $bl=COrderHelper::GetDistance(strtolower($el['LAST_NAME']), strtolower($arTitle[0])) <= 2
                            || strpos(strtolower($el['LAST_NAME']), strtolower($arTitle[0]))!==false;
                        $bn=!isset($arTitle[1]) || $arTitle[1]==''
                            || COrderHelper::GetDistance(strtolower($el['NAME']), strtolower($arTitle[1])) <= 3
                            || strpos(strtolower($el['NAME']), strtolower($arTitle[1]))!==false;
                        $bs=!isset($arTitle[2]) || $arTitle[2]==''
                            || COrderHelper::GetDistance(strtolower($el['SECOND_NAME']), strtolower($arTitle[2])) <= 3
                            || strpos(strtolower($el['SECOND_NAME']), strtolower($arTitle[2]))!==false;
                        return $bl && $bn && $bs;
                    });
                }
                if(isset($selected['phone']) && $selected['phone']!='') {
                    $sPhone = array_filter($physicalList, function ($el) use ($selected) {
                        return $el['PHONE'] == $selected['phone'];
                    });
                }
                if(isset($selected['email']) && $selected['email']!='') {
                    $sEmail = array_filter($physicalList, function ($el) use ($selected) {
                        return $el['EMAIL'] == $selected['email'];
                    });
                }
                if(!empty($sTitle) || !empty($sPhone) || !empty($sEmail)) {
                    $physicalList=$sTitle+$sPhone+$sEmail;
                } elseif($selected['title']!='') {
                    $physicalList=array();
                }
                $create=true;
                ?>
                <div class="order-person-create-container">
                    <form name="<?=$id?>_create_form">
                        <p>
                            <label><?=GetMessage("order_person_physical_field_id")?>:</label>
                            <input type="text" id="<?=$id?>_id" value="<?=$selected['id']?>" disabled="disabled" style="width: calc(100% - 10.5em)">
                            <span class="order-icon-delete" onclick="obOrder['<?=$id?>'].Unchoose(this)"></span>
                        </p>
                        <p><label><?=GetMessage("order_person_physical_field_last_name")?>:</label> <input type="text" id="<?=$id?>_last_name" value="<?=$arTitle[0]?>"></p>
                        <p><label><?=GetMessage("order_person_physical_field_name")?>:</label> <input type="text" id="<?=$id?>_name" value="<?=$arTitle[1]?>"></p>
                        <p><label><?=GetMessage("order_person_physical_field_second_name")?>:</label> <input type="text" id="<?=$id?>_second_name" value="<?=$arTitle[2]?>"></p>
                        <p><label><?=GetMessage("order_person_physical_field_phone")?>:</label> <input type="text" id="<?=$id?>_phone" value="<?=$selected['phone']?>"></p>
                        <p><label><?=GetMessage("order_person_physical_field_email")?>: </label> <input type="text" id="<?=$id?>_email" value="<?=$selected['email']?>"></p>
                        <p style="text-align: center"><b><?=GetMessage("order_person_physical_field_list")?></b></p>
                    </form>
                </div>
                <?
            } else {

                $create=false;
                ?>
                <div class="order-person-search-container">
                    <form name="<?=$id?>_create_form">
                        <p style="text-align: center"><?=GetMessage("order_person_physical_field_search")?></p>
                        <p><label><?=GetMessage("order_person_physical_field_last_name")?>:</label> <input type="text" id="<?=$id?>_last_name"></p>
                        <p><label><?=GetMessage("order_person_physical_field_name")?>:</label> <input type="text" id="<?=$id?>_name"></p>
                        <p><label><?=GetMessage("order_person_physical_field_second_name")?>:</label> <input type="text" id="<?=$id?>_second_name"></p>
                        <p><label><?=GetMessage("order_person_physical_field_phone")?>:</label> <input type="text" id="<?=$id?>_phone"></p>
                        <p><label><?=GetMessage("order_person_physical_field_email")?>: </label> <input type="text" id="<?=$id?>_email"></p>
                        <p style="text-align: center"><b><?=GetMessage("order_person_physical_field_list")?></b></p>
                    </form>
                </div>
                <?
            }
            ?>
                <div class="order-person-list-container">
                    <?=GetPhysicalListHtml($physicalList,$create)?>
                </div>
            </div>
            <?
            $answer['layout']=ob_get_clean();
            ob_start();
            ?>
            <script>
                <?ob_clean();ob_start();?>
                $('.order-person-create-container input').on('input',function(){obOrder['<?=$id?>'].SearchList()});
                $('.order-person-search-container input').on('input',function(){obOrder['<?=$id?>'].SearchList()});
                <?$answer['script']=ob_get_clean(); ob_start();?>
            </script>
            <?
            ob_clean();
            $answer['list']=$physicalList;
        } elseif($mode=='search_list') {
            $selected = $_REQUEST["inputs"];
            $selectedItm = $_REQUEST["obSelected"];
            $create=!!$_REQUEST["create"];

            if (!isset($selected['id']) || $selected['id'] == '') {
                $res=COrderPhysical::GetListEx();
                $physicalList=array();
                while($el=$res->Fetch()) {
                    if(isset($selectedItm['id']) && $selectedItm['id']==$el['ID']) {
                        $el['SELECTED']=true;
                    } else {
                        $el['SELECTED']=false;
                    }
                    $el['URL']=CComponentEngine::makePathFromTemplate($path['PHYSICAL'], array('physical_id' => $el['ID']));
                    $physicalList[$el['ID']]=$el;
                }
                $sTitle=$sPhone=$sEmail=array();

                $sTitle = array_filter($physicalList, function ($el) use ($selected) {
                    $bl=COrderHelper::GetDistance(strtolower($el['LAST_NAME']), strtolower($selected['last_name'])) <= 2
                        || strpos(strtolower($el['LAST_NAME']), strtolower($selected['last_name']))!==false;
                    $bn=!isset($selected['name']) || $selected['name']==''
                        || COrderHelper::GetDistance(strtolower($el['NAME']), strtolower($selected['name'])) <= 3
                        || strpos(strtolower($el['NAME']), strtolower($selected['name']))!==false;
                    $bs=!isset($selected['second_name']) || $selected['second_name']==''
                        || COrderHelper::GetDistance(strtolower($el['SECOND_NAME']), strtolower($selected['second_name'])) <= 3
                        || strpos(strtolower($el['SECOND_NAME']), strtolower($selected['second_name']))!==false;
                    return $bl && $bn && $bs;
                });
                if(isset($selected['phone']) && $selected['phone']!='') {
                    $sPhone = array_filter($physicalList, function ($el) use ($selected) {
                        return strpos(strtolower($el['PHONE']), strtolower($selected['phone']))!==false;
                    });
                }
                if(isset($selected['email']) && $selected['email']!='') {
                    $sEmail = array_filter($physicalList, function ($el) use ($selected) {
                        return strpos(strtolower($el['EMAIL']), strtolower($selected['email']))!==false;
                    });
                }
                if(!empty($sTitle) || !empty($sPhone) || !empty($sEmail)) {
                    $physicalList=$sTitle+$sPhone+$sEmail;
                } elseif($selected['last_name']!='' || $selected['name']!='' || $selected['second_name']!=''
                    || (!$create && ($selected['phone']!='' || $selected['email']!=''))) {
                    $physicalList=array();
                }
                $answer['listHtml']=GetPhysicalListHtml($physicalList,$create);
                $answer['list']=$physicalList;
                $answer['refresh']=true;
            } else {
                $answer['refresh']=false;
            }
        } elseif($mode=='save') {
            GLOBAL $DB;
            $DB->StartTransaction();
            $data = isset($_REQUEST['fields']) && is_array($_REQUEST['fields']) ? $_REQUEST['fields'] : array();
            if(count($data) == 0)
            {
                echo json_encode(array('error'=>'source data are not found!'));
                die();
            }
            //$ID = isset($data['id']) ? $data['id'] : '';
            $ID=isset($data['id'])?$data['id']:'';
            $phone = $data['phone'];
            $email = $data['email'];
            $name=isset($data['name'])?$data['name']:'';
            $lastName=isset($data['last_name'])?$data['last_name']:'';
            $secondName=isset($data['second_name'])?$data['second_name']:'';
            if(trim($name==='') || trim($lastName==='')) {
                echo json_encode(array('error'=>'name and last name must be entered'));
                die();
            }
            $title = $lastName . ' ' . $name . ' ' . $secondName;

            if($ID=='') {
                $ID = COrderHelper::GetGUID($title);
                $arPhysical = Array(
                    'ID' => $ID,
                    'NAME' => $name,
                    'LAST_NAME' => $lastName,
                    'SECOND_NAME' => $secondName,
                    'PHONE' => $phone,
                    'EMAIL' => $email,
                    'SHARED' => 'N',
                );
                $COrderPhysical = new COrderPhysical(false);
                $bSuccess = ($COrderPhysical->Add($arPhysical)) ? true : false;
            } else {
                $arPhysical = Array(
                    'PHONE' => $phone,
                    'EMAIL' => $email,
                );
                $COrderPhysical = new COrderPhysical(false);
                $bSuccess = ($COrderPhysical->Update($ID,$arPhysical)) ? true : false;
            }
            if(!$bSuccess){
                $DB->Rollback();
                echo json_decode(array('error'=>'something went wrong','data'=>$arPhysical));
                die();
            }
            else{
                $DB->Commit();
                $answer['data']=array(
                    'id'=>$ID,
                    'title'=>$title,
                    'phone' => $phone,
                    'email' => $email,
                    'url' => CComponentEngine::makePathFromTemplate($path['PHYSICAL'], array('physical_id' => $ID)),
                );
                $answer['complete']=true;
            }
        }
        break;
    case 'agent':
        if ($mode=='layout') {
            ob_start();
            $selected = $_REQUEST["obSelected"];
            $legal=$selected['legal'];
            $res=COrderAgent::GetListEx();
            $agentList=array();
            while($el=$res->Fetch()) {
                if(isset($selected['id']) && $selected['id']==$el['ID']) {
                    $el['SELECTED']=true;
                } else {
                    $el['SELECTED']=false;
                }
                $el['URL']=CComponentEngine::makePathFromTemplate($path['AGENT'], array('agent_id' => $el['ID']));
                $el['CONTACT_URL']=CComponentEngine::makePathFromTemplate($path['CONTACT'], array('contact_id' => $el['CONTACT_ID']));
                $agentList[$el['ID']]=$el;
            }
            $res=COrderPhysical::GetListEx();
            $physicalList=array();
            while($el=$res->Fetch()) {
                if($legal=='N') {
                    if(isset($selected['id']) && $selected['id']==$el['ID']) {
                        $el['SELECTED']=true;
                    } else {
                        $el['SELECTED']=false;
                    }
                    $el['URL']=CComponentEngine::makePathFromTemplate($path['AGENT'], array('agent_id' => $el['ID']));
                    $el['LEGAL']='N';
                    $el['IS_AGENT']=isset($agentList[$el['ID']]);
                } else {
                    $el['URL']=CComponentEngine::makePathFromTemplate($path['CONTACT'], array('contact_id' => $el['ID']));
                }
                $physicalList[$el['ID']]=$el;
            }
            ?>
            <div class="order-person-container">
                <div class="order-person-errors-container"></div>
            <?
            if (!isset($selected['id']) || $selected['id'] == '') {
                $sTitle=$sPhone=$sEmail=array();
                if($legal=='N') {
                    $arrToList=$physicalList;
                    $arTitle = explode(' ', $selected['title']);
                    if (isset($selected['title']) && $selected['title'] != '') {
                        $sTitle = array_filter($arrToList, function ($el) use ($arTitle) {
                            $bl=COrderHelper::GetDistance(strtolower($el['LAST_NAME']), strtolower($arTitle[0])) <= 2
                                || strpos(strtolower($el['LAST_NAME']), strtolower($arTitle[0]))!==false;
                            $bn=!isset($arTitle[1]) || $arTitle[1]==''
                                || COrderHelper::GetDistance(strtolower($el['NAME']), strtolower($arTitle[1])) <= 3
                                || strpos(strtolower($el['NAME']), strtolower($arTitle[1]))!==false;
                            $bs=!isset($arTitle[2]) || $arTitle[2]==''
                                || COrderHelper::GetDistance(strtolower($el['SECOND_NAME']), strtolower($arTitle[2])) <= 3
                                || strpos(strtolower($el['SECOND_NAME']), strtolower($arTitle[2]))!==false;
                            return $bl && $bn && $bs;
                        });
                    }
                    if(isset($selected['phone']) && $selected['phone']!='') {
                        $sPhone = array_filter($arrToList, function ($el) use ($selected) {
                            return $el['PHONE'] == $selected['phone'];
                        });
                    }
                    if(isset($selected['email']) && $selected['email']!='') {
                        $sEmail = array_filter($arrToList, function ($el) use ($selected) {
                            return $el['EMAIL'] == $selected['email'];
                        });
                    }
                    if(!empty($sTitle) || !empty($sPhone) || !empty($sEmail)) {
                        $arrToList=$sTitle+$sPhone+$sEmail;
                    } elseif($selected['title']!='') {
                        $arrToList=array();
                    }
                } else {
                    $arrToList=$agentList;
                    if (isset($selected['title']) && $selected['title'] != '') {
                        $sTitle = array_filter($arrToList, function ($el) use ($selected) {
                            return COrderHelper::GetDistance(strtolower($el['TITLE']), strtolower($selected['title'])) <= 3
                            || strpos(strtolower($el['TITLE']), strtolower($selected['title'])) !== false;
                        });
                    }
                    if(isset($selected['phone']) && $selected['phone']!='') {
                        $sPhone = array_filter($arrToList, function ($el) use ($selected) {
                            return $el['PHONE'] == $selected['phone'];
                        });
                    }
                    if(isset($selected['email']) && $selected['email']!='') {
                        $sEmail = array_filter($arrToList, function ($el) use ($selected) {
                            return $el['EMAIL'] == $selected['email'];
                        });
                    }
                    if(!empty($sTitle) || !empty($sPhone) || !empty($sEmail)) {
                        $arrToList=$sTitle+$sPhone+$sEmail;
                    } elseif($selected['title']!='') {
                        $arrToList=array();
                    }


                    $sContactTitle=$sContactPhone=$sContactEmail=array();
                    $arrToContactList=$physicalList;
                    $arTitle = explode(' ', $selected['contact_title']);
                    if (isset($selected['contact_title']) && $selected['contact_title'] != '') {
                        $sContactTitle = array_filter($arrToContactList, function ($el) use ($arTitle) {
                            $bl=COrderHelper::GetDistance(strtolower($el['LAST_NAME']), strtolower($arTitle[0])) <= 2
                                || strpos(strtolower($el['LAST_NAME']), strtolower($arTitle[0]))!==false;
                            $bn=!isset($arTitle[1]) || $arTitle[1]==''
                                || COrderHelper::GetDistance(strtolower($el['NAME']), strtolower($arTitle[1])) <= 3
                                || strpos(strtolower($el['NAME']), strtolower($arTitle[1]))!==false;
                            $bs=!isset($arTitle[2]) || $arTitle[2]==''
                                || COrderHelper::GetDistance(strtolower($el['SECOND_NAME']), strtolower($arTitle[2])) <= 3
                                || strpos(strtolower($el['SECOND_NAME']), strtolower($arTitle[2]))!==false;
                            return $bl && $bn && $bs;
                        });
                    }
                    if(isset($selected['phone']) && $selected['contact_phone']!='') {
                        $sContactPhone = array_filter($arrToContactList, function ($el) use ($selected) {
                            return $el['PHONE'] == $selected['contact_phone'];
                        });
                    }
                    if(isset($selected['contact_email']) && $selected['contact_email']!='') {
                        $sContactEmail = array_filter($arrToContactList, function ($el) use ($selected) {
                            return $el['EMAIL'] == $selected['contact_email'];
                        });
                    }
                    if(!empty($sContactTitle) || !empty($sContactPhone) || !empty($sContactEmail)) {
                        $arrToContactList=$sContactTitle+$sContactPhone+$sContactEmail;
                    } elseif($selected['contact_title']!='') {
                        $arrToContactList=array();
                    }
                    $answer['listContact']=$arrToContactList;
                }

                $create=true;
                ?>
                <div class="order-person-create-container">
                    <form name="<?=$id?>_create_form">
                    <p>
                        <label><?=GetMessage("order_person_agent_field_legal")?>:</label>
                        <input type="text" id="<?=$id?>_legal_title" value="<?=$legal=='Y'?GetMessage("order_person_agent_field_value_legal_y"):
                            GetMessage("order_person_agent_field_value_legal_n")?>" disabled="disabled">
                        <input type="hidden" id="<?=$id?>_legal" value="<?=$legal?>" disabled="disabled">
                    </p>
                    <p>
                        <label><?=GetMessage("order_person_agent_field_id")?>:</label>
                        <input type="text" id="<?=$id?>_id" value="<?=$selected['id']?>" disabled="disabled" style="width: calc(100% - 10.5em)">
                        <span class="order-icon-delete" onclick="obOrder['<?=$id?>'].Unchoose(this)"></span>
                    </p>
                    <?if($legal=='N'):?>
                        <p><label><?=GetMessage("order_person_agent_field_last_name")?>:</label> <input type="text" id="<?=$id?>_last_name" value="<?=$arTitle[0]?>"></p>
                        <p><label><?=GetMessage("order_person_agent_field_name")?>:</label> <input type="text" id="<?=$id?>_name" value="<?=$arTitle[1]?>"></p>
                        <p><label><?=GetMessage("order_person_agent_field_second_name")?>:</label> <input type="text" id="<?=$id?>_second_name" value="<?=$arTitle[2]?>"></p>
                    <?else:?>
                        <p><label><?=GetMessage("order_person_agent_field_title")?>:</label> <input type="text" id="<?=$id?>_title" value="<?=$selected['title']?>"></p>
                    <?endif;?>
                    <p><label><?=GetMessage("order_person_agent_field_phone")?>:</label> <input type="text" id="<?=$id?>_phone" value="<?=$selected['phone']?>"></p>
                    <p><label><?=GetMessage("order_person_agent_field_email")?>: </label> <input type="text" id="<?=$id?>_email" value="<?=$selected['email']?>"></p>
                    <p style="text-align: center"><b><?=GetMessage("order_person_agent_field_list")?></b></p>
                    </form>
                </div>
                <?
            } else {

                $arrToList = $agentList;
                $create=false;
                ?>
                <div class="order-person-search-container">
                    <form name="<?=$id?>_create_form">
                        <p style="text-align: center"><?=GetMessage("order_person_agent_field_search")?></p>
                        <p>
                            <label><?=GetMessage("order_person_agent_field_legal")?>:</label>
                            <input type="text" id="<?=$id?>_legal_title" value="<?=$legal=='Y'?GetMessage("order_person_agent_field_value_legal_y"):
                                GetMessage("order_person_agent_field_value_legal_n")?>" disabled="disabled">
                            <input type="hidden" id="<?=$id?>_legal" value="<?=$legal?>" disabled="disabled">
                        </p>
                        <?if($legal=='N'):?>
                            <p><label><?=GetMessage("order_person_agent_field_last_name")?>:</label> <input type="text" id="<?=$id?>_last_name"></p>
                            <p><label><?=GetMessage("order_person_agent_field_name")?>:</label> <input type="text" id="<?=$id?>_name"></p>
                            <p><label><?=GetMessage("order_person_agent_field_second_name")?>:</label> <input type="text" id="<?=$id?>_second_name"></p>
                        <?else:?>
                            <p><label><?=GetMessage("order_person_agent_field_title")?>:</label> <input type="text" id="<?=$id?>_title"></p>
                        <?endif;?>
                        <p><label><?=GetMessage("order_person_agent_field_phone")?>:</label> <input type="text" id="<?=$id?>_phone"></p>
                        <p><label><?=GetMessage("order_person_agent_field_email")?>: </label> <input type="text" id="<?=$id?>_email"></p>
                        <p style="text-align: center"><b><?=GetMessage("order_person_agent_field_list")?></b></p>
                    </form>
                </div>
                <?
            }
            ?>
                <div class="order-person-list-container">
                    <?=GetAgentListHtml($arrToList,$create,$legal)?>
                </div>
                <?if($legal=='Y' && $create):?>
                    <div class="order-person-contact-container">
                    <div class="order-person-create-container">
                        <form name="<?=$id?>_create_contact_form">
                            <p style="text-align: center"><b><?=GetMessage("order_person_physical_contact_title")?></b></p>
                            <p>
                                <label><?=GetMessage("order_person_physical_field_id")?>:</label>
                                <input type="text" id="<?=$id?>_contact_id" value="<?=$selected['id']?>" disabled="disabled" style="width: calc(100% - 10.5em)">
                                <span class="order-icon-delete" onclick="obOrder['<?=$id?>'].Unchoose(this)"></span>
                            </p>
                            <p><label><?=GetMessage("order_person_physical_field_last_name")?>:</label> <input type="text" id="<?=$id?>_contact_last_name" value="<?=$arTitle[0]?>"></p>
                            <p><label><?=GetMessage("order_person_physical_field_name")?>:</label> <input type="text" id="<?=$id?>_contact_name" value="<?=$arTitle[1]?>"></p>
                            <p><label><?=GetMessage("order_person_physical_field_second_name")?>:</label> <input type="text" id="<?=$id?>_contact_second_name" value="<?=$arTitle[2]?>"></p>
                            <p><label><?=GetMessage("order_person_physical_field_phone")?>:</label> <input type="text" id="<?=$id?>_contact_phone" value="<?=$selected['contact_phone']?>"></p>
                            <p><label><?=GetMessage("order_person_physical_field_email")?>: </label> <input type="text" id="<?=$id?>_contact_email" value="<?=$selected['contact_email']?>"></p>
                            <p style="text-align: center"><b><?=GetMessage("order_person_physical_field_list")?></b></p>
                        </form>
                    </div>
                    <div class="order-person-list-contact-container">
                        <?=GetPhysicalListHtml($arrToContactList,$create)?>
                    </div>
                    </div>
                <?endif;?>
            </div>
            <?
            $answer['layout']=ob_get_clean();
            ob_start();?><script><?ob_clean();ob_start();?>
                <?if($legal=='Y'):?>
                console.log(obOrder['<?=$id?>'].popup.contentContainer.children[0].offsetHeight);
                var div=obOrder['<?=$id?>'].popup.contentContainer.children[0];
                //div.style.height=(div.offsetHeight+300)+'px';
                <?endif;?>
                $('.order-person-create-container input').on('input',function(){obOrder['<?=$id?>'].SearchList()});
                $('.order-person-search-container input').on('input',function(){obOrder['<?=$id?>'].SearchList()});
                <?$answer['script']=ob_get_clean();
            ob_start();?></script><?ob_clean();
            $answer['list']=$arrToList;
        } elseif($mode=='search_list') {
            $selected=$_REQUEST["inputs"];
            $selectedEl=$_REQUEST["obSelected"];
            $legal=$selected['legal'];
            $create=$_REQUEST["create"];

            if (!isset($selected['id']) || $selected['id'] == '') {
                $res=COrderAgent::GetListEx(array(),array('LEGAL'=>$legal));
                $agentList=array();
                while($el=$res->Fetch()) {
                    if(isset($selectedEl['id']) && $selectedEl['id']==$el['ID']) {
                        $el['SELECTED']=true;
                    } else {
                        $el['SELECTED']=false;
                    }
                    $el['URL']=CComponentEngine::makePathFromTemplate($path['AGENT'], array('agent_id' => $el['ID']));
                    $el['CONTACT_URL']=CComponentEngine::makePathFromTemplate($path['CONTACT'], array('contact_id' => $el['CONTACT_ID']));
                    $agentList[$el['ID']]=$el;
                }
                $res=COrderPhysical::GetListEx();
                $physicalList=array();
                while($el=$res->Fetch()) {
                    if($legal=='N') {
                        if(isset($selectedEl['id']) && $selectedEl['id']==$el['ID']) {
                            $el['SELECTED']=true;
                        } else {
                            $el['SELECTED']=false;
                        }
                        $el['URL']=CComponentEngine::makePathFromTemplate($path['AGENT'], array('agent_id' => $el['ID']));
                        $el['LEGAL']='N';
                        $el['IS_AGENT']=isset($agentList[$el['ID']]);
                    } else {
                        $el['URL']=CComponentEngine::makePathFromTemplate($path['CONTACT'], array('contact_id' => $el['ID']));
                    }
                    $physicalList[$el['ID']]=$el;
                }
                $sTitle=$sPhone=$sEmail=array();

                if($legal=='N') {
                    $arrToList=$physicalList;
                    $sTitle = array_filter($arrToList, function ($el) use ($selected) {
                        $bl=COrderHelper::GetDistance(strtolower($el['LAST_NAME']), strtolower($selected['last_name'])) <= 2
                            || strpos(strtolower($el['LAST_NAME']), strtolower($selected['last_name']))!==false;
                        $bn=!isset($selected['name']) || $selected['name']==''
                            || COrderHelper::GetDistance(strtolower($el['NAME']), strtolower($selected['name'])) <= 3
                            || strpos(strtolower($el['NAME']), strtolower($selected['name']))!==false;
                        $bs=!isset($selected['second_name']) || $selected['second_name']==''
                            || COrderHelper::GetDistance(strtolower($el['SECOND_NAME']), strtolower($selected['second_name'])) <= 3
                            || strpos(strtolower($el['SECOND_NAME']), strtolower($selected['second_name']))!==false;
                        return $bl && $bn && $bs;
                    });
                    if(isset($selected['phone']) && $selected['phone']!='') {
                        $sPhone = array_filter($arrToList, function ($el) use ($selected) {
                            return strpos(strtolower($el['PHONE']), strtolower($selected['phone']))!==false;
                        });
                    }
                    if(isset($selected['email']) && $selected['email']!='') {
                        $sEmail = array_filter($arrToList, function ($el) use ($selected) {
                            return strpos(strtolower($el['EMAIL']), strtolower($selected['email']))!==false;
                        });
                    }
                    if(!empty($sTitle) || !empty($sPhone) || !empty($sEmail)) {
                        $arrToList=$sTitle+$sPhone+$sEmail;
                    } elseif($selected['last_name']!='' || $selected['name']!='' || $selected['second_name']!=''
                        || (!$create && ($selected['phone']!='' || $selected['email']!=''))) {
                        $arrToList=array();
                    }
                } else {
                    $arrToList=$agentList;
                    $sTitle = array_filter($arrToList, function ($el) use ($selected) {
                        return COrderHelper::GetDistance(strtolower($el['TITLE']), strtolower($selected['title'])) <= 2
                        || strpos(strtolower($el['TITLE']), strtolower($selected['title']))!==false;
                    });
                    if(isset($selected['phone']) && $selected['phone']!='') {
                        $sPhone = array_filter($arrToList, function ($el) use ($selected) {
                            return strpos(strtolower($el['LEGAL_PHONE']), strtolower($selected['phone']))!==false;
                        });
                    }
                    if(isset($selected['email']) && $selected['email']!='') {
                        $sEmail = array_filter($arrToList, function ($el) use ($selected) {
                            return strpos(strtolower($el['LEGAL_EMAIL']), strtolower($selected['email']))!==false;
                        });
                    }
                    $answer['arrb4filt']=array($sTitle,$sPhone,$sEmail);
                    if(!empty($sTitle) || !empty($sPhone) || !empty($sEmail)) {
                        $arrToList=$sTitle+$sPhone+$sEmail;
                    } elseif($selected['title']!=''
                        || (!$create && ($selected['phone']!='' || $selected['email']!=''))) {
                        $arrToList=array();
                    }


                    $sContactTitle=$sContactPhone=$sContactEmail=array();
                    $arrToContactList=$physicalList;
                    $sContactTitle = array_filter($arrToContactList, function ($el) use ($selected) {
                        $bl=COrderHelper::GetDistance(strtolower($el['LAST_NAME']), strtolower($selected['contact_last_name'])) <= 2
                            || strpos(strtolower($el['LAST_NAME']), strtolower($selected['contact_last_name']))!==false;
                        $bn=!isset($selected['contact_name']) || $selected['contact_name']==''
                            || COrderHelper::GetDistance(strtolower($el['NAME']), strtolower($selected['contact_name'])) <= 3
                            || strpos(strtolower($el['NAME']), strtolower($selected['contact_name']))!==false;
                        $bs=!isset($selected['contact_second_name']) || $selected['contact_second_name']==''
                            || COrderHelper::GetDistance(strtolower($el['SECOND_NAME']), strtolower($selected['contact_second_name'])) <= 3
                            || strpos(strtolower($el['SECOND_NAME']), strtolower($selected['contact_second_name']))!==false;
                        return $bl && $bn && $bs;
                    });
                    if(isset($selected['phone']) && $selected['contact_phone']!='') {
                        $sContactPhone = array_filter($arrToContactList, function ($el) use ($selected) {
                            return strpos(strtolower($el['PHONE']), strtolower($selected['contact_phone']))!==false;
                        });
                    }
                    if(isset($selected['contact_email']) && $selected['contact_email']!='') {
                        $sContactEmail = array_filter($arrToContactList, function ($el) use ($selected) {
                            return strpos(strtolower($el['EMAIL']), strtolower($selected['contact_email']))!==false;
                        });
                    }
                    if(!empty($sContactTitle) || !empty($sContactPhone) || !empty($sContactEmail)) {
                        $arrToContactList=$sContactTitle+$sContactPhone+$sContactEmail;
                    } elseif($selected['contact_last_name']!='' || $selected['contact_name']!='' || $selected['contact_second_name']!=''
                        || (!$create && ($selected['contact_phone']!='' || $selected['contact_email']!=''))) {
                        $arrToContactList=array();
                    }
                    $answer['listContactHtml']=GetPhysicalListHtml($arrToContactList,$create,$legal);
                    $answer['listContact']=$arrToContactList;
                }

                $answer['listHtml']=GetAgentListHtml($arrToList,$create,$legal);
                $answer['list']=$arrToList;
                $answer['sel']=$selected;
                $answer['refresh']=true;
                //$answer['arrs']=array('sTitle'=>$sTitle,'sEmail'=>$sEmail,'sPhone'=>$sPhone);
            } else {
                $answer['refresh']=false;
                $answer['sel']=$selected;
            }
        } elseif($mode=='save') {
            GLOBAL $DB;
            $answer['data']=array();
            $DB->StartTransaction();
            $data = isset($_REQUEST['fields']) && is_array($_REQUEST['fields']) ? $_REQUEST['fields'] : array();
            if (count($data) == 0) {
                echo json_encode(array('error' => 'source data are not found!'));
                die();
            }
            //$ID = isset($data['id']) ? $data['id'] : '';
            $legal = $data['legal'];
            $ID = isset($data['id']) ? $data['id'] : '';
            $bSuccess=false;
            if ($legal == 'N') {
                $aPhone = isset($data['phone']) ? $data['phone'] : '';
                $aEmail = isset($data['email']) ? $data['email'] : '';
                $name = isset($data['name']) ? $data['name'] : '';
                $lastName = isset($data['last_name']) ? $data['last_name'] : '';
                $secondName = isset($data['second_name']) ? $data['second_name'] : '';
                if (trim($name === '') || trim($lastName === '')) {
                    echo json_encode(array('error' => 'name and last name must be entered'));
                    die();
                }
                $aTitle = $lastName . ' ' . $name . ' ' . $secondName;


                if ($ID === '') {
                    $ID = COrderHelper::GetGUID($aTitle);
                    $arPhysical = Array(
                        'ID' => $ID,
                        'NAME' => $name,
                        'LAST_NAME' => $lastName,
                        'SECOND_NAME' => $secondName,
                        'PHONE' => $aPhone,
                        'EMAIL' => $aEmail,
                        'SHARED' => 'N',
                    );
                    $COrderPhysical = new COrderPhysical(false);
                    $bSuccess = ($COrderPhysical->Add($arPhysical)) ? true : false;
                } else {
                    $arPhysical = Array(
                        'PHONE' => $aPhone,
                        'EMAIL' => $aEmail,
                    );
                    $COrderPhysical = new COrderPhysical(false);
                    $bSuccess = ($COrderPhysical->Update($ID, $arPhysical)) ? true : false;
                }
                if(!COrderAgent::GetByID(($ID))) {
                    $arAgent = Array(
                        'ID' => $ID,
                        'LEGAL' => 'N',
                    );
                    $COrderAgent = new COrderAgent(false);
                    $bSuccess = ($COrderAgent->Add($arAgent)) ? true : false;
                }
            } elseif ($legal == 'Y') {
                $aTitle = isset($data['title']) ? $data['title'] : '';
                if (trim($aTitle === '')) {
                    echo json_encode(array('error' => 'title must be entered'));
                    die();
                }
                $aPhone = isset($data['phone']) ? $data['phone'] : '';
                $aEmail = isset($data['email']) ? $data['email'] : '';

                //first, we have to create/update an agent for contact
                //create agent
                if($ID==='') {
                    $ID = COrderHelper::GetLegalID($aTitle);
                    $arAgent = Array(
                        'ID' => $ID,
                        //'SHARED' => 'N',
                        'LEGAL' => 'Y',
                        'TITLE' => $aTitle,
                        'LEGAL_PHONE' => $aPhone,
                        'LEGAL_EMAIL' => $aEmail,
                    );
                    $COrderAgent = new COrderAgent(false);
                    $bSuccess = ($COrderAgent->Add($arAgent)) ? true : false;
                }
                //update agent
                else {
                    $arAgent = Array(
                        'LEGAL_PHONE' => $aPhone,
                        'LEGAL_EMAIL' => $aEmail,
                    );
                    $COrderAgent = new COrderAgent(false);
                    $bSuccess = ($COrderAgent->Update($ID,$arAgent)) ? true : false;
                }

                //then we creating (updating) contact
                if ($bSuccess) {
                    $guid = isset($data['contact_id']) ? $data['contact_id'] : '';
                    $cPhone = $data['contact_phone'];
                    $cEmail = $data['contact_email'];

                    //create new physical for contact
                    if ($guid === '') {
                        $cName = isset($data['contact_name']) ? $data['contact_name'] : '';
                        $cLastName = isset($data['contact_last_name']) ? $data['contact_last_name'] : '';
                        $cSecondName = isset($data['contact_second_name']) ? $data['contact_second_name'] : '';
                        if (trim($cName === '') || trim($cLastName === '')) {
                            $DB->Rollback();
                            COrderHelper::DeleteAgent($ID);
                            echo json_encode(array('error' => 'name and last name of contact must be entered'));
                            die();
                        }
                        $cTitle = $cLastName . ' ' . $cName . ' ' . $cSecondName;
                        $guid = COrderHelper::GetGUID($cTitle);
                        $arPhysical = Array(
                            'ID' => $guid,
                            'NAME' => $cName,
                            'LAST_NAME' => $cLastName,
                            'SECOND_NAME' => $cSecondName,
                            'PHONE' => $cPhone,
                            'EMAIL' => $cEmail,
                            'SHARED' => 'N',
                        );
                        $COrderPhysical = new COrderPhysical(false);
                        $bSuccess = ($COrderPhysical->Add($arPhysical)) ? true : false;
                    }
                    //update physical for  contact
                    else {
                        $arPhysical = Array(
                            'PHONE' => $cPhone,
                            'EMAIL' => $cEmail,
                        );
                        $COrderPhysical = new COrderPhysical(false);
                        $bSuccess = ($COrderPhysical->Update($guid, $arPhysical)) ? true : false;
                    }

                    //update/create contact
                    $arAgentContact=array(
                        'CONTACT_GUID'=>$guid
                    );
                    $bSuccess = ($COrderAgent->Update($ID,$arAgentContact)) ? true : false;
                    $answer['data']=array_merge($answer['data'],array(
                        'contact_id'=>$arAgentContact['CONTACT_ID'],
                        'contact_guid'=>$arAgentContact['CONTACT_GUID'],
                        'contact_title'=>isset($cTitle)?$cTitle:$arPhysical['FULL_NAME'],
                        'contact_url' => CComponentEngine::makePathFromTemplate($path['CONTACT'],
                            array('contact_id' => $arAgentContact['CONTACT_ID'])),
                    ));
                    if($cPhone!='') {
                        $answer['data']['contact_phone']=$cPhone;
                    }
                    if($cEmail!='') {
                        $answer['data']['contact_email']=$cEmail;
                    }
                }
            }
            else $bSuccess = false;

            if(!$bSuccess){
                $DB->Rollback();
                COrderHelper::DeleteAgent($ID);
                echo json_decode(array('error'=>'something went wrong','data'=>$data));
                die();
            }
            else{
                $DB->Commit();
                $answer['data']=array_merge($answer['data'],array(
                    'id'=>$ID,
                    'legal'=>$legal,
                    'title'=>$aTitle,
                    'phone'=>$aPhone,
                    'email'=>$aEmail,
                    'url' => CComponentEngine::makePathFromTemplate($path['AGENT'], array('agent_id' => $ID)),
                ));
                $answer['complete']=true;
            }
        }
        break;
    }
    echo json_encode($answer);
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
    ?>
