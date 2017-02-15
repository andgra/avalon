<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('order'))
	return false;

$arComponentParameters = Array(
	'PARAMETERS' => array(
		'APP_COUNT' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('ORDER_APP_COUNT'),
			'TYPE' => 'STRING',
			'DEFAULT' => '20'
		)							
	)	
);
?>