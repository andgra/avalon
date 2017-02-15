<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	'NAME' => GetMessage('ORDER_REG_COMPLEX_NAME'),
	'DESCRIPTION' => GetMessage('ORDER_REG_COMPLEX_DESCRIPTION'),
	'ICON' => '/images/icon.gif',
	'SORT' => 10,
	'COMPLEX' => 'Y',
	'PATH' => array(
		'ID' => 'order',
		'NAME' => GetMessage('ORDER_NAME'),
		'CHILD' => array(
			'ID' => 'reg',
			'NAME' => GetMessage('ORDER_REG_NAME')
		)
	),
	'CACHE_PATH' => 'Y'	
);
?>