<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	'NAME' => GetMessage('ORDER_REPORT_COMPLEX_NAME'),
	'DESCRIPTION' => GetMessage('ORDER_REPORT_COMPLEX_DESCRIPTION'),
	'ICON' => '/images/icon.gif',
	'SORT' => 10,
	'COMPLEX' => 'Y',
	'PATH' => array(
		'ID' => 'order',
		'NAME' => GetMessage('ORDER_NAME')
	),
	'CACHE_PATH' => 'Y'
);
?>