<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('order'))
	return false;

$arComponentParameters = Array(
	'PARAMETERS' => array(		
		'ELEMENT_ID' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('ORDER_ELEMENT_ID'),
			'TYPE' => 'STRING',
			'DEFAULT' => '={$_REQUEST["reg_id"]}'
		),
		'TYPE' => Array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('ORDER_MENU_TYPE'),
			'TYPE' => 'LIST',
			'VALUES' => array('list' => 'LIST', 'edit' => 'EDIT'),
			'DEFAULT' => 'page'		
		)							
	)	
);
?>