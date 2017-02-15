<?php
namespace Bitrix\Order;

use Bitrix\Main\DB;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class AppTable extends Entity\DataManager
{
	public static function getUfId()
	{
		return 'ORDER_APP';
	}

	public static function getMap()
	{
		global $DB;

		return array(
			'ID' => array(
				'data_type' => 'string',
				'primary' => true,
			),
			'ROOT_ID' => array(
				'data_type' => 'string',
				'expression' => array(
					'(
						SELECT GROUP_CONCAT(CONCAT("#",T.ROOT_ID,"#") ORDER BY T.ROOT_ID SEPARATOR ";") ROOT_ID
						FROM (
							SELECT DISTINCT(CASE UPPER(ENTITY_TYPE)
								WHEN "DIRECTION" THEN (SELECT MAX(D.ROOT_ID) FROM b_order_direction D
									WHERE D.ID=R1.ENTITY_ID)
								WHEN "NOMEN" THEN (SELECT MAX(D.ROOT_ID) FROM b_order_nomen E
									LEFT JOIN b_order_direction D ON E.DIRECTION_ID=D.ID
									WHERE E.ID=R1.ENTITY_ID)
								WHEN "GROUP" THEN (SELECT MAX(D.ROOT_ID) FROM b_order_group E
									LEFT JOIN b_order_nomen N ON E.NOMEN_ID=N.ID
									LEFT JOIN b_order_direction D ON N.DIRECTION_ID=D.ID
									WHERE E.ID=R1.ENTITY_ID)
								WHEN "FORMED_GROUP" THEN (SELECT MAX(D.ROOT_ID) FROM b_order_formed_group FG
									LEFT JOIN b_order_group G ON FG.GROUP_ID=G.ID
									LEFT JOIN b_order_nomen N ON G.NOMEN_ID=N.ID
									LEFT JOIN b_order_direction D ON N.DIRECTION_ID=D.ID
									WHERE FG.ID=R1.ENTITY_ID)
								END) ROOT_ID,
								APP_ID
							FROM b_order_reg R1
							WHERE APP_ID<>0
						) T
						WHERE T.APP_ID=%s
						GROUP BY T.APP_ID
					)',
					'ID'
				)
			),
			'DIRECTION_ID' => array(
				'data_type' => 'string',
				'expression' => array(
					'(
						SELECT GROUP_CONCAT(CONCAT("#",T.DIRECTION_ID,"#") ORDER BY T.DIRECTION_ID SEPARATOR ";") ROOT_ID
						FROM (
							SELECT DISTINCT(CASE UPPER(ENTITY_TYPE)
								WHEN "DIRECTION" THEN R1.ENTITY_ID
								WHEN "NOMEN" THEN (SELECT MAX(E.DIRECTION_ID) FROM b_order_nomen E
									WHERE E.ID=R1.ENTITY_ID)
								WHEN "GROUP" THEN (SELECT MAX(N.DIRECTION_ID) FROM b_order_group E
									LEFT JOIN b_order_nomen N ON E.NOMEN_ID=N.ID
									WHERE E.ID=R1.ENTITY_ID)
								WHEN "FORMED_GROUP" THEN (SELECT MAX(N.DIRECTION_ID) FROM b_order_formed_group FG
									LEFT JOIN b_order_group G ON FG.GROUP_ID=G.ID
									LEFT JOIN b_order_nomen N ON G.NOMEN_ID=N.ID
									WHERE FG.ID=R1.ENTITY_ID)
								END) DIRECTION_ID,
								APP_ID
							FROM b_order_reg R1
							WHERE APP_ID<>0
						) T
						WHERE T.APP_ID=%s
						GROUP BY T.APP_ID
					)',
					'ID'
				)
			),
			'NOMEN_ID' => array(
				'data_type' => 'string',
				'expression' => array(
					'(
						SELECT GROUP_CONCAT(CONCAT("#",T.NOMEN_ID,"#") ORDER BY T.NOMEN_ID SEPARATOR ";") ROOT_ID
						FROM (
							SELECT DISTINCT(CASE UPPER(ENTITY_TYPE)
								WHEN "DIRECTION" THEN -1
								WHEN "NOMEN" THEN R1.ENTITY_ID
								WHEN "GROUP" THEN (SELECT MAX(E.NOMEN_ID) FROM b_order_group E
									WHERE E.ID=R1.ENTITY_ID)
								WHEN "FORMED_GROUP" THEN (SELECT MAX(G.NOMEN_ID) FROM b_order_formed_group FG
									LEFT JOIN b_order_group G ON FG.GROUP_ID=G.ID
									WHERE FG.ID=R1.ENTITY_ID)
								END) NOMEN_ID,
								APP_ID
							FROM b_order_reg R1
							WHERE APP_ID<>0
						) T
						WHERE T.APP_ID=%s
						GROUP BY T.APP_ID
					)',
					'ID'
				)
			),
			'STATUS' => array(
				'data_type' => 'string'
			),
			'STATUS_BY' => array(
				'data_type' => 'Enums',
				'reference' => array(
					'=this.STATUS' => "ref.PROPERTY_348",
					'=ref.PROPERTY_346' => array('?','APP'),
					'=ref.PROPERTY_347' => array('?','STATUS')
				)
			),
			'PERIOD' => array(
				'data_type' => 'datetime',
				'expression' => array(
					'(SELECT MIN(PERIOD) MIN_PERIOD FROM b_order_reg WHERE APP_ID=%s)',
					'ID'
				)
			),
			'IS_EXPIRED' => array(
				'data_type' => 'boolean',
				'expression' => array(
					'CASE WHEN %s<NOW() THEN 1 ELSE 0 END',
					'PERIOD'
				),
				'values' => array(0, 1)
			),
			'HAND_MADE' => array(
				'data_type' => 'string',
			),
			'IS_HAND_MADE' => array(
				'data_type' => 'boolean',
				'expression' => array(
					'CASE %s WHEN "Y" THEN 1 ELSE 0 END',
					'HAND_MADE'
				),
				'values' => array(0, 1)
			),
			'MODIFY_BY_ID' => array(
				'data_type' => 'string'
			),
			'MODIFY_BY_FULL_NAME' => array(
				'data_type' => 'string',
				'expression' => array(
					'(
						SELECT CONCAT(UP.LAST_NAME, " ", UP.NAME)
						FROM b_uts_user UF LEFT JOIN b_order_physical UP ON UF.UF_GUID=UP.ID
						WHERE %s = UF.UF_GUID
					)',
					'MODIFY_BY_ID'
				),
			),
			'ASSIGNED_ID' => array(
				'data_type' => 'string'
			),
			'ASSIGNED_TYPE' => array(
				'data_type' => 'string',
				'expression' => array(
					'CASE
					WHEN LEFT(%s,1)="U" THEN "user"
					WHEN LEFT(%s,1)="G" THEN "group"
					WHEN LEFT(%s,1)="D" THEN "department"
					 ELSE "other" END',
					'ASSIGNED_ID',
					'ASSIGNED_ID',
					'ASSIGNED_ID',
				)
			),
			'ASSIGNED_TITLE' => array(
				'data_type' => 'string',
				'expression' => array(
					'CASE %s
					WHEN "user" THEN (
						SELECT CONCAT(UP.LAST_NAME, " ", UP.NAME)
						FROM b_uts_user UF LEFT JOIN b_order_physical UP ON UF.UF_GUID=UP.ID
						WHERE SUBSTRING(%s,2) = UF.VALUE_ID
					)
					WHEN "group" THEN (
						SELECT NAME
						FROM b_group
						WHERE SUBSTRING(%s,2) = ID
					)
					WHEN "department" THEN (
						SELECT NAME
						FROM b_iblock_section
						WHERE SUBSTRING(%s,2) = ID
					) ELSE "" END',
					'ASSIGNED_TYPE',
					'ASSIGNED_ID','ASSIGNED_ID','ASSIGNED_ID'
				),
			),
			'IS_CONVERTED' => array(
				'data_type' => 'boolean',
				'expression' => array(
					'CASE WHEN %s="CONVERTED" THEN 1 ELSE 0 END',
					'STATUS'
				),
				'values' => array(0, 1)
			),
			'IS_DENIED' => array(
				'data_type' => 'boolean',
				'expression' => array(
					'CASE WHEN %s="DENIED" THEN 1 ELSE 0 END',
					'STATUS'
				),
				'values' => array(0, 1)
			),
			'IS_PROCESSED' => array(
				'data_type' => 'boolean',
				'expression' => array(
					'CASE WHEN %s="PROCESSED" THEN 1 ELSE 0 END',
					'STATUS'
				),
				'values' => array(0, 1)
			),
			'IS_NEW' => array(
				'data_type' => 'boolean',
				'expression' => array(
					'CASE WHEN %s="NEW" THEN 1 ELSE 0 END',
					'STATUS'
				),
				'values' => array(0, 1)
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
