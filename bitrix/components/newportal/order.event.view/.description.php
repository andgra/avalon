<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	'NAME' => GetMessage('ORDER_EVENT_VIEW_NAME'),
	'DESCRIPTION' => GetMessage('ORDER_EVENT_VIEW_DESCRIPTION'),
	'ICON' => '/images/icon.gif',
	'SORT' => 40,
	'PATH' => array(
		'ID' => 'order',
		'NAME' => GetMessage('ORDER_NAME'),
	),
	'CACHE_PATH' => 'Y'
);
?>