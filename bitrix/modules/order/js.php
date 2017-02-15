<?
$pathJS = '/bitrix/js/order';
$pathCSS = '/bitrix/js/order/css';
$pathLang = BX_ROOT.'/modules/order/lang/'.LANGUAGE_ID;
//WARNING: Don't use CUserOptions here! CJSCore::Init can be called from php_interface/init.php where no $USER exists

$arJSCoreConfig = array(
	'structure' => array(
		'js' => $pathJS.'/structure.js',
		'css' => $pathCSS.'/structure.css',
		'lang' => $pathLang.'/js_structure.php',
		'rel' => array('popup', 'ajax', 'finder')
	),
	'person' => array(
		'js' => $pathJS.'/person.js',
		'css' => $pathCSS.'/person.css',
		'lang' => $pathLang.'/js_person.php',
		'rel' => array('popup', 'ajax', 'finder')
	),
);
foreach ($arJSCoreConfig as $ext => $arExt)
{
	CJSCore::RegisterExt($ext, $arExt);
}
?>