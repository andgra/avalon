<?php
namespace Bitrix\Order;

use Bitrix\Main\DB;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class DirectionTable extends Entity\DataManager
{
	public static function getUfId()
	{
		return 'ORDER_DIRECTION';
	}

	public static function getMap()
	{
		global $DB;

		return array(
			'ID' => array(
				'data_type' => 'string',
				'primary' => true,
			),
			'PARENT_ID' => array(
				'data_type' => 'string',
				'primary' => true,
			),
			'IS_ROOT'=>array(
				'data_type' => 'string',
				'expression' => array(
					'CASE %s WHEN "" THEN 1 ELSE 0 END',
					'PARENT_ID'
				)
			),
			'APP_BY' => array(
				'data_type' => 'App',
				'reference' => array(
					'=this.PARENT_ID' => array('?',''),
					'%ref.ROOT_ID' => 'this.ID',
				)
			),
			'DATE_CREATE' => array(
				'data_type' => 'datetime'
			),
			'DATE_CREATE_SHORT' => array(
				'data_type' => 'datetime',
				'expression' => array(
					$DB->datetimeToDateFunction('%s'), 'DATE_CREATE'
				)
			),
			'DATE_MODIFY' => array(
				'data_type' => 'datetime'
			),
			'DATE_MODIFY_SHORT' => array(
				'data_type' => 'datetime',
				'expression' => array(
					$DB->datetimeToDateFunction('%s'), 'DATE_MODIFY'
				)
			),
		);
	}
}
