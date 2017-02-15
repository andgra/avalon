<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	'NAME' => GetMessage('ORDER_COURSE_EDIT_NAME'),
	'DESCRIPTION' => GetMessage('ORDER_COURSE_EDIT_DESCRIPTION'),
	'ICON' => '/images/icon.gif',
	'SORT' => 30,
	'PATH' => array(
		'ID' => 'order',
		'NAME' => GetMessage('ORDER_NAME'),
		'CHILD' => array(
			'ID' => 'course',
			'NAME' => GetMessage('ORDER_COURSE_NAME')
		)
	),
	'CACHE_PATH' => 'Y'
);
?>