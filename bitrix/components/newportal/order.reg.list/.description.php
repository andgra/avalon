<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	'NAME' => GetMessage('ORDER_REG_LIST_NAME'),
	'DESCRIPTION' => GetMessage('ORDER_REG_LIST_DESCRIPTION'),
	'ICON' => '/images/icon.gif',
	'SORT' => 20,
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