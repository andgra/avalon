<?php
IncludeModuleLangFile(__FILE__);

if(class_exists("order")) return;

Class order extends CModule
{
    var $MODULE_ID = "order";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = 'Y';
    function order()
    {
        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = GetMessage("ORDER_INSTALL_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("ORDER_INSTALL_DESCRIPTION");
    }
    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        // Install events
        //RegisterModuleDependences("iblock","OnAfterIBlockElementUpdate","order","cMainOrder","onBeforeElementUpdateHandler");
        RegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(GetMessage("ORDER_INSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/order/install/step.php");
        return true;
    }
    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        //UnRegisterModuleDependences("iblock","OnAfterIBlockElementUpdate","order","cMainOrder","onBeforeElementUpdateHandler");
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(GetMessage("ORDER_UNINSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/order/install/unstep.php");
        return true;
    }
}