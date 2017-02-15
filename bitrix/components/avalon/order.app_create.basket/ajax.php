<?

if(!function_exists('__OrderEndResponse'))
{
    function __OrderEndResponse($result)
    {
        $GLOBALS['APPLICATION']->RestartBuffer();
        Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
        if(!empty($result))
        {
            echo json_encode($result);
        }
        die();
    }
}
session_start();
//__OrderEndResponse($_POST);
if(isset($_POST['MODE']) && $_POST['MODE']=='ADD' && isset($_POST['ENTITY_ID']) && isset($_POST['ENTITY_TYPE']))
{
    $arEnt=array('ENTITY_TYPE'=>$_POST['ENTITY_TYPE'],'ENTITY_ID'=>$_POST['ENTITY_ID']);
    if(isset($_SESSION['BASKET']))
    {
        if(!in_array($arEnt,$_SESSION['BASKET']))
            $_SESSION['BASKET'][]=$arEnt;
    }
    else
        $_SESSION['BASKET'][]=$arEnt;
}
elseif(isset($_POST['MODE']) && $_POST['MODE']=='CLEAR')
{
    if(!isset($_POST['ENTITY_ID']) || !isset($_POST['ENTITY_TYPE']) )
    {
        if(isset($_SESSION['BASKET']))
            unset($_SESSION['BASKET']);
    }

    else {
        $arEnt = array('ENTITY_TYPE' => $_POST['ENTITY_TYPE'], 'ENTITY_ID' => $_POST['ENTITY_ID']);
        foreach ($_SESSION['BASKET'] as $k => $ent) {
            if ($ent == $arEnt) {
                unset($_SESSION['BASKET'][$k]);
                break;
            }
        }
    }
}