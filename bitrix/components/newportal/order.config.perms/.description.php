<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	'NAME' => GetMessage('ORDER_PERMS_NAME'),
	'DESCRIPTION' => GetMessage('ORDER_PERMS_DESCRIPTION'),
	'ICON' => '/images/icon.gif',
	'SORT' => 10,
	'CACHE_PATH' => 'Y',
	'PATH' => array(
		'ID' => 'order',
		'NAME' => GetMessage('ORDER_NAME'),
		'CHILD' => array(
			'ID' => 'config',
			'NAME' => GetMessage('ORDER_CONFIG_NAME'),
    		'CHILD' => array(
    			'ID' => 'config_perms',
                'SORT' => 10
            )
        )
	),
);

?>