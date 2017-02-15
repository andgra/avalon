<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$_REQUEST['DATA']=$_GET['DATA']=CUtil::PhpToJSObject(array(
    "ENTITY"=>array('PHYSICAL','CONTACT','AGENT',
        'DIRECTION','NOMEN','COURSE','GROUP','FORMED_GROUP','REG','TEACHER','ROOM','SCHEDULE','MARK'),
    "MODE"=>"AUTO"
));
/*$_REQUEST['DATA']=$_GET['DATA']=CUtil::PhpToJSObject(array(
    "ENTITY"=>array('REG'),
    "MODE"=>"AUTO"
));*/
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/newportal/order.sync.all/ajax.php");


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>