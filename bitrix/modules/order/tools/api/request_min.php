<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
global $APPLICATION;
if(!function_exists('__OrderEndResponse'))
{
    function __OrderEndResponse($result)
    {
        $GLOBALS['APPLICATION']->RestartBuffer();
        Header('Content-Type: application/JSON; charset='.LANG_CHARSET);
        if(!empty($result))
        {
            echo CUtil::PhpToJSObject($result,true);
        }
        require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
        die();
    }
}
if (!CModule::IncludeModule('order'))
{
    __OrderEndResponse(array('ERROR'=>'Модуль order не установлен.'));
}
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/order/public/api/request_min.php");
$res=COrderApp::GetListEx(array(),array('STATUS'=>'READY'),false,false,array('ID','AGENT_ID'));
while($el=$res->Fetch()) {
    $arListApp[$el['ID']]=$el;
}
$res=COrderAgent::GetListEx(array(),array(),false,false,array(
    "ID",'TITLE'
));
while($el=$res->Fetch()) {
    $arListAllAgent[$el['ID']]=$el;
}
$res=COrderReg::GetListEx(array(),array('!APP_ID'=>'0'),false,false,array('ID','ENTITY_ID','ENTITY_TYPE','PHYSICAL_ID','STATUS_TITLE','APP_ID'));
while($el=$res->Fetch()) {
    $arListReg[$el['ID']]=$el;
}
$res=COrderFormedGroup::GetListEx(array(),array(),false,false,array('ID','GROUP_ID','NOMEN_ID','NOMEN_TITLE'));
while($el=$res->Fetch()) {
    $arFormedGroup[$el['ID']]=$el;
}
$res=COrderGroup::GetListEx(array(),array(),false,false,array('ID','NOMEN_ID','NOMEN_TITLE'));
while($el=$res->Fetch()) {
    $arGroup[$el['ID']]=$el;
}
$res=COrderNomen::GetListEx(array(),array(),false,false,array('ID','TITLE'));
while($el=$res->Fetch()) {
    $arNomen[$el['ID']]=$el;
}
$arApp=array();
foreach($arListApp as $appID => $app) {
    $newApp=array();
    $newApp['НомерЗаявки']=$app['ID'];
    $newApp['НаименованиеКонтрагента']=$arListAllAgent[$app['AGENT_ID']]['TITLE'];
    $arReg=array_filter($arListReg, function ($el) use($app){
        return $el['APP_ID']==$app['ID'];
    });
    $newApp['НаименованияНоменклатур']=array();
    foreach($arReg as $regID => $reg) {
        switch (strtolower($reg['ENTITY_TYPE'])) {
            case 'formed_group':
                $formedGroup=reset(array_filter($arFormedGroup,function($el) use ($reg) {
                    return $el['ID']==$reg['ENTITY_ID'];
                }));
                if(!in_array($formedGroup['NOMEN_TITLE'],$newApp['NOMEN_TITLE']))
                    $newApp['НаименованияНоменклатур'][]=$formedGroup['NOMEN_TITLE'];
                break;
            case 'group':
                $group=reset(array_filter($arGroup,function($el) use ($reg) {
                    return $el['ID']==$reg['ENTITY_ID'];
                }));
                if(!in_array($group['NOMEN_TITLE'],$newApp['NOMEN_TITLE']))
                    $newApp['НаименованияНоменклатур'][]=$group['NOMEN_TITLE'];
                break;
            case 'nomen':
                if(isset($arNomen[$reg['ENTITY_ID']]) && !in_array($arNomen[$reg['ENTITY_ID']]['TITLE'],$newApp['NOMEN_TITLE']))
                    $newApp['НаименованияНоменклатур'][]=$arNomen[$reg['ENTITY_ID']]['TITLE'];
                break;
        }
    }
    $newApp['НаименованияНоменклатур']=array_values(array_unique($newApp['НаименованияНоменклатур']));
    $newApp['КоличествоСлушателей']=count($arReg);
    $arApp[]=$newApp;

}
__OrderEndResponse($arApp);


?>
