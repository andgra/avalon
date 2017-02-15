<?php
namespace Bitrix\Order;

use Bitrix\Main\DB;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class EnumsTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return 'b_iblock_element_prop_s63';
	}

	public static function getMap()
	{
		return array(
			'IBLOCK_ELEMENT_ID' => array(
				'data_type' => 'string'
			),
			'PROPERTY_346' => array(
				'data_type' => 'string',
				'primary' => true
			),
			'TABLE' => array(
				'data_type' => 'string',
				'expression' => array('%s','PROPERTY_346')
			),
			'PROPERTY_347' => array(
				'data_type' => 'string',
				'primary' => true
			),
			'FIELD' => array(
				'data_type' => 'string',
				'expression' => array('%s','PROPERTY_347')
			),
			'PROPERTY_348' => array(
				'data_type' => 'string',
				'primary' => true
			),
			'TITLE' => array(
				'data_type' => 'string',
				'expression' => array('%s','PROPERTY_348')
			),
			'PROPERTY_349' => array(
				'data_type' => 'string'
			),
			'VALUE_RU' => array(
				'data_type' => 'string',
				'expression' => array('%s','PROPERTY_349')
			),
			'PROPERTY_350' => array(
				'data_type' => 'string'
			),
			'VALUE_EN' => array(
				'data_type' => 'string',
				'expression' => array('%s','PROPERTY_350')
			),
		);
	}
}
