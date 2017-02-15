<?php
if (!CModule::IncludeModule('report'))
	return;

use Bitrix\Main;
use Bitrix\Order;

class COrderReportManager
{
	private static $OWNER_INFOS = null;

	private static function createOwnerInfo($ID, $className, $title)
	{
		return array(
			'ID' => $ID,
			'HELPER_CLASS' => $className,
			'TITLE' => $title
		);
	}
	public static function getOwnerInfos()
	{
		if(self::$OWNER_INFOS)
		{
			return self::$OWNER_INFOS;
		}

		IncludeModuleLangFile(__FILE__);

		self::$OWNER_INFOS = array();
		self::$OWNER_INFOS[] = self::createOwnerInfo(
			COrderReportHelper::getOwnerId(),
			'COrderReportHelper',
			GetMessage('ORDER_REPORT_OWNER_TITLE_'.strtoupper(COrderReportHelper::getOwnerId()))
		);
		/*self::$OWNER_INFOS[] = self::createOwnerInfo(
			COrderDirectionReportHelper::getOwnerId(),
			'COrderDirectionReportHelper',
			GetMessage('ORDER_REPORT_OWNER_TITLE_'.strtoupper(COrderDirectionReportHelper::getOwnerId()))
		);*/
		return self::$OWNER_INFOS;
	}
	public static function getOwnerInfo($ownerID)
	{
		$ownerID = strval($ownerID);
		if($ownerID === '')
		{
			return null;
		}

		$infos = self::getOwnerInfos();
		foreach($infos as $info)
		{
			if($info['ID'] === $ownerID)
			{
				return $info;
			}
		}
		return null;
	}
	public static function getOwnerHelperClassName($ownerID)
	{
		$info = self::getOwnerInfo($ownerID);
		return $info ? $info['HELPER_CLASS'] : '';
	}
	public static function getReportData($reportID)
	{
		$reportID = intval($reportID);
		return $reportID > 0
			? Bitrix\Report\ReportTable::getById($reportID)->fetch():
			null;
	}
}

abstract class COrderReportHelperBase extends CReportHelper
{
	protected static $CURRENT_RESULT_ROWS = null;
	protected static $CURRENT_RESULT_ROW = null;
	protected static $PAY_SYSTEMS = array();
	protected static $PERSON_TYPES = null;

	public static function buildSelectTreePopupElelemnt($humanTitle, $fullHumanTitle, $fieldDefinition, $fieldType, $ufInfo = array())
	{
		// replace by static:: when php 5.3 available
		$grcFields = static::getGrcColumns();

		$isUF = false;
		$isMultiple = false;
		if (is_array($ufInfo) && isset($ufInfo['ENTITY_ID']) && isset($ufInfo['FIELD_NAME']))
		{
			if (isset($ufInfo['MULTIPLE']) && $ufInfo['MULTIPLE'] === 'Y')
				$isMultiple = true;
			$isUF = true;
		}

		if ($isUF && $isMultiple
			&& substr($fieldDefinition, -strlen(self::UF_TEXT_TRIM_POSTFIX)) === self::UF_TEXT_TRIM_POSTFIX)
		{
			return '';
		}

		return parent::buildSelectTreePopupElelemnt(
			$humanTitle, $fullHumanTitle, $fieldDefinition, $fieldType, $ufInfo
		);
	}

	public static function appendDateTimeUserFieldsAsShort(\Bitrix\Main\Entity\Base $entity)
	{
		/** @global CDatabase $DB */
		global $DB;

		// Advanced fields for datetime user fields
		$dateFields = array();
		foreach($entity->getFields() as $field)
		{
			if (in_array($field->getName(), array('LEAD_BY', 'COMPANY_BY', 'CONTACT_BY'), true) && $field instanceof Bitrix\Main\Entity\ReferenceField)
			{
				self::appendDateTimeUserFieldsAsShort($field->getRefEntity());
			}
			else if ($field instanceof Bitrix\Main\Entity\ExpressionField)
			{
				$arUF = self::detectUserField($field);
				if ($arUF['isUF'])
				{
					$ufDataType = self::getUserFieldDataType($arUF);
					if ($ufDataType === 'datetime' && $arUF['ufInfo']['MULTIPLE'] !== 'Y')
					{
						$dateFields[] = array(
							'def' => array(
								'data_type' => 'datetime',
								'expression' => array(
									$DB->DatetimeToDateFunction('%s'), $arUF['ufInfo']['FIELD_NAME']
								)
							),
							'name' => $arUF['ufInfo']['FIELD_NAME'].self::UF_DATETIME_SHORT_POSTFIX
						);
					}
				}
			}
		}
		foreach ($dateFields as $fieldInfo)
			$entity->addField($fieldInfo['def'], $fieldInfo['name']);
	}

	public static function appendTextUserFieldsAsTrimmed(\Bitrix\Main\Entity\Base $entity)
	{
		/** @global string $DBType */
		global $DBType;
		
		$dbType = ToUpper(strval($DBType));
		
		// Advanced fields for text user fields
		$textFields = array();
		foreach($entity->getFields() as $field)
		{
			if (in_array($field->getName(), array('LEAD_BY', 'COMPANY_BY', 'CONTACT_BY'), true) && $field instanceof Bitrix\Main\Entity\ReferenceField)
			{
				self::appendTextUserFieldsAsTrimmed($field->getRefEntity());
			}
			else if ($field instanceof Bitrix\Main\Entity\ExpressionField)
			{
				$arUF = self::detectUserField($field);
				if ($arUF['isUF'])
				{
					$ufDataType = self::getUserFieldDataType($arUF);
					if ($arUF['ufInfo']['MULTIPLE'] === 'Y')
					{
						if ($dbType === 'ORACLE' || $dbType === 'MSSQL')
						{
							$exprVal = '';
							switch ($dbType)
							{
								case 'ORACLE':
									$maxStrLen = 4000;
									$exprVal = 'TO_CHAR(SUBSTR(%s, 1, '.$maxStrLen.'))';
									break;
								case 'MSSQL':
									$maxStrLen = 8000;
									$exprVal = 'SUBSTRING(%s, 1, '.$maxStrLen.')';
									break;
							}
							/*$textFields[] = array(
								'def' => array(
									'data_type' => 'string',
									'expression' => array(
										$exprVal, $arUF['ufInfo']['FIELD_NAME']
									)
								),
								'name' => $arUF['ufInfo']['FIELD_NAME'].self::UF_TEXT_TRIM_POSTFIX
							);*/
							if ($arUF['ufInfo']['USER_TYPE_ID'] === 'datetime')
								$fdmsGetterName = 'getFDMsMultipleTrimmedDateTime';
							else
								$fdmsGetterName = 'getFDMsMultipleTrimmed';
							$textFields[] = new Main\Entity\ExpressionField(
								$arUF['ufInfo']['FIELD_NAME'].self::UF_TEXT_TRIM_POSTFIX,
								$exprVal,
								array($arUF['ufInfo']['FIELD_NAME']),
								array('fetch_data_modification' => array(__CLASS__, $fdmsGetterName))
							);
						}
					}
				}
			}
		}
		foreach ($textFields as $fieldInfo)
		{
			if (is_object($fieldInfo))
				$entity->addField($fieldInfo);
			else
				$entity->addField($fieldInfo['def'], $fieldInfo['name']);
		}
	}

	public static function getFDMsMultipleTrimmed()
	{
		return array(
			array(__CLASS__, 'fdmMultipleTrimmed')
		);
	}

	public static function getFDMsMultipleTrimmedDateTime()
	{
		return array(
			array(__CLASS__, 'fdmMultipleTrimmed'),
			array(__CLASS__, 'fdmMultipleTrimmedDateTime')
		);
	}

	public static function fdmMultipleTrimmed($value, $query, $dataRow, $columnAlias)
	{
		$result = @unserialize($value);

		return $result;
	}

	public static function fdmMultipleTrimmedDateTime($value, $query, $dataRow, $columnAlias)
	{
		$result = array();

		if (is_array($value))
		{
			foreach ($value as $v)
			{
				if (!empty($v))
				{
					try
					{
						//try new independent datetime format
						$v = new Bitrix\Main\Type\DateTime($v, \Bitrix\Main\UserFieldTable::MULTIPLE_DATETIME_FORMAT);
					}
					catch (Main\ObjectException $e)
					{
						//try site format
						try
						{
							$v = new Bitrix\Main\Type\DateTime($v);
						}
						catch (Main\ObjectException $e)
						{
							//try short format
							$v = Bitrix\Main\Type\DateTime::createFromUserTime($v);
						}
					}
					$result[] = $v;
				}
			}
		}

		return $result;
	}

	public static function getCurrentVersion()
	{
		global $arModuleVersion;

		include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/order/install/version.php");
		return $arModuleVersion['VERSION'];
	}
	public static function fillFilterReferenceColumn(&$filterElement, Main\Entity\ReferenceField $field)
	{
		if ($field->getRefEntityName() == '\Bitrix\Order\Contact')
		{
			// OrderContact
			if ($filterElement['value'])
			{
				$entity = COrderContact::GetById($filterElement['value']);
				if ($entity)
				{
					$filterElement['value'] = array('id' => $entity['ID'], 'name' => $entity['FULL_NAME']);
				}
				else
				{
					$filterElement['value'] = array('id' => $filterElement['value'], 'name' => GetMessage('ORDER_CONTACT_NOT_FOUND'));
				}
			}
			else
			{
				$filterElement['value'] = array('id' => '');
			}
		}
		elseif ($field->getRefEntityName() == '\Bitrix\Order\App')
		{
			// OrderDeal
			if ($filterElement['value'])
			{
				$entity = COrderApp::GetById($filterElement['value']);
				if ($entity)
				{
					$filterElement['value'] = array('id' => $entity['ID'], 'name' => $entity['TITLE']);
				}
				else
				{
					$filterElement['value'] = array('id' => $filterElement['value'], 'name' => GetMessage('ORDER_APP_NOT_FOUND'));
				}
			}
			else
			{
				$filterElement['value'] = array('id' => '');
			}
		}
		parent::fillFilterReferenceColumn($filterElement, $field);
	}
	public static function formatResults(&$rows, &$columnInfo, $total, &$customChartData = null)
	{
		self::$CURRENT_RESULT_ROWS = $rows;
		foreach ($rows as $rowNum => &$row)
		{
			self::$CURRENT_RESULT_ROW = $row;
			foreach ($row as $k => &$v)
			{
				if (!array_key_exists($k, $columnInfo))
				{
					continue;
				}

				$cInfo = $columnInfo[$k];

				if (is_array($v))
				{
					foreach ($v as $subk => &$subv)
					{
						$customChartValue = is_null($customChartData) ? null : array();
						static::formatResultValue($k, $subv, $row, $cInfo, $total, $customChartValue);
						if (is_array($customChartValue)
							&& isset($customChartValue['exist']) && $customChartValue['exist'] = true)
						{
							if (!isset($customChartData[$rowNum]))
								$customChartData[$rowNum] = array();
							if (!isset($customChartData[$rowNum][$k]))
								$customChartData[$rowNum][$k] = array();
							$customChartData[$rowNum][$k]['multiple'] = true;
							if (!isset($customChartData[$rowNum][$k][$subk]))
								$customChartData[$rowNum][$k][$subk] = array();
							$customChartData[$rowNum][$k][$subk]['type'] = $customChartValue['type'];
							$customChartData[$rowNum][$k][$subk]['value'] = $customChartValue['value'];
						}
					}
				}
				else
				{
					$customChartValue = is_null($customChartData) ? null : array();
					static::formatResultValue($k, $v, $row, $cInfo, $total, $customChartValue);
					if (is_array($customChartValue)
						&& isset($customChartValue['exist']) && $customChartValue['exist'] = true)
					{
						if (!isset($customChartData[$rowNum]))
							$customChartData[$rowNum] = array();
						if (!isset($customChartData[$rowNum][$k]))
							$customChartData[$rowNum][$k] = array();
						$customChartData[$rowNum][$k]['multiple'] = false;
						if (!isset($customChartData[$rowNum][$k][0]))
							$customChartData[$rowNum][$k][0] = array();
						$customChartData[$rowNum][$k][0]['type'] = $customChartValue['type'];
						$customChartData[$rowNum][$k][0]['value'] = $customChartValue['value'];
					}
				}
			}
		}

		unset($row, $v, $subv);
		self::$CURRENT_RESULT_ROWS = self::$CURRENT_RESULT_ROW = null;
	}
	protected static function prepareAppTitleHtml($appID, $title)
	{
		$url = CComponentEngine::MakePathFromTemplate(
			COption::GetOptionString('order', 'path_to_app_edit'),
			array('app_id' => $appID)
		);

		return '<a target="_blank" href="'.htmlspecialcharsbx($url).'">'.htmlspecialcharsbx($title).'</a>';
	}

	protected static function getEnumName($code, $table, $field, $htmlEncode = false)
	{
		$code = strval($code);
		$field = strval($field);
		if($code === '' || $field === '')
		{
			return '';
		}

		$statuses = COrderHelper::GetEnumList($table,$field);
		$name = array_key_exists($code, $statuses) ? $statuses[$code] : $code;
		return $htmlEncode ? htmlspecialcharsbx($name) : $name;
	}

	protected static function getAppStatusName($code, $htmlEncode = false)
	{
		return self::getEnumName($code, 'APP', 'STATUS', $htmlEncode);
	}
	protected static function getTypeName($code, $htmlEncode = false)
	{
		return self::getEnumName($code, 'ACCESS', 'PROVIDER', $htmlEncode);
	}
	protected static function getDirectionsTitles($str, $htmlEncode = false)
	{
		$str=str_replace('#;#','#; #',$str);
		$res=COrderDirection::GetListEx();
		while($el=$res->Fetch()) {
			$str=str_replace('#'.$el['ID'].'#',$el['TITLE'],$str);
		}
		return $htmlEncode ? htmlspecialcharsbx($str) : $str;
	}
	protected static function getNomensTitles($str, $htmlEncode = false)
	{
		$str=str_replace('#;#','#; #',$str);
		//Erasing the direction apps
		$str=str_replace('#-1#;','',$str); //First/middle occurrence
		$str=str_replace(';#-1#','',$str); //Last/middle occurrence
		$str=str_replace('#-1#','',$str); //Only one occurrence
		$res=COrderNomen::GetListEx();
		while($el=$res->Fetch()) {
			$str=str_replace('#'.$el['ID'].'#',$el['TITLE'],$str);
		}
		return $htmlEncode ? htmlspecialcharsbx($str) : $str;
	}

}

class COrderReportHelper extends COrderReportHelperBase
{
	public static function getEntityName()
	{
		return 'Bitrix\Order\App';
	}
	public static function getOwnerId()
	{
		return 'order';
	}
	public static function getColumnList()
	{
		IncludeModuleLangFile(__FILE__);

		$columnList = array(
			'ASSIGNED_TYPE',
			'ASSIGNED_TITLE',
			'ROOT_ID',
			'DIRECTION_ID',
			'NOMEN_ID',
			'MODIFY_BY_FULL_NAME',
			'ID',
			/*'ASSIGNED_LOGIN',
			'ASSIGNED_EMAIL',*/
			'STATUS',
			'STATUS_SUB'=>array(
				'IS_NEW',
				'IS_PROCESSED',
				'IS_CONVERTED',
				'IS_DENIED'
			),
			'IS_EXPIRED',
			//'DATE_CREATE',
			'DATE_CREATE_SHORT',
			//'DATE_MODIFY',
			'DATE_MODIFY_SHORT',
			'IS_HAND_MADE',
			/*'SOURCE',
			'DIRECTION',
			'AGENT'=>array(
				'ID',
				'LEGAL',
				'TITLE',
				'PHONE',
				'EMAIL'
			),
			'CONTACT'=>array(
				'ID',
				'GUID',
				'FULL_NAME',
				'PHONE',
				'EMAIL'
			),
			'MAY_TO_READY',
			'MODIFY_BY_ID',
			'MODIFY_BY_LOGIN',
			'MODIFY_BY_FULL_NAME',
			'MODIFY_BY_EMAIL',*/
		);

		return $columnList;
	}



	public static function getDefaultColumns()
	{
		return array(
			array('name' => 'ASSIGNED_TITLE'),
			array('name' => 'ID'),
		);
	}
	public static function getCalcVariations()
	{
		return array_merge(
			parent::getCalcVariations(),
			array(
				'IS_NEW' => array('SUM'),
				'IS_PROCESSED' => array('SUM'),
				'IS_CONVERTED' => array('SUM'),
				'IS_DENIED' => array('SUM'),
				'IS_EXPIRED' => array('SUM'),
				'IS_HAND_MADE' => array('SUM'),
			)
		);
	}
	public static function getGrcColumns()
	{
		return array(
			'ROOT_ID',
			'DIRECTION_ID',
			'NOMEN_ID',
		);
	}
	public static function getCompareVariations()
	{
		return array_merge(
			parent::getCompareVariations(),
			array(
				'ASSIGNED_TYPE' => array(
					'EQUAL',
					'NOT_EQUAL'
				),
			)
		);
	}
	public static function beforeViewDataQuery(&$select, &$filter, &$group, &$order, &$limit, &$options, &$runtime = null)
	{
		if(isset($select['ROOT_ID']) && empty($group))
			$group[]='ROOT_ID';
		if(isset($select['DIRECTION_ID']) && empty($group))
			$group[]='DIRECTION_ID';
		if(isset($select['NOMEN_ID']) && empty($group))
			$group[]='NOMEN_ID';

		if(isset($select['DATE_CREATE']))
		{
			unset($select['DATE_CREATE']);
		}

	}


	public static function formatResultValue($k, &$v, &$row, &$cInfo, $total, &$customChartValue = null)
	{
		// HACK: detect if 'report.view' component is rendering excel spreadsheet
		$isHtml = !(isset($_GET['EXCEL']) && $_GET['EXCEL'] === 'Y');
		$field = $cInfo['field'];
		$fieldName = isset($cInfo['fieldName']) ? $cInfo['fieldName'] : $field->GetName();
		if($fieldName === 'ASSIGNED_TYPE')
		{
			if($v !== '')
			{
				$v = self::getTypeName($v, $isHtml);
			}
		}
		if($fieldName === 'ROOT_ID' || $fieldName === 'DIRECTION_ID')
		{
			if($v !== '')
			{
				$v = self::getDirectionsTitles($v, $isHtml);
			}
		}
		elseif($fieldName === 'NOMEN_ID')
		{
			if($v !== '')
			{
				$v = self::getNomensTitles($v, $isHtml);
			}
		}
	}

	public static function getPeriodFilter($date_from, $date_to)
	{
		if(is_null($date_from) && is_null($date_to))
		{
			return array(); // Empty filter for empty time interval.
		}

		//$now = ConvertTimeStamp(time(), 'FULL');
		$filter = array('LOGIC' => 'AND');
		if(!is_null($date_to))
		{
			$filter[] = array(
				'LOGIC' => 'OR',
				'<=DATE_CREATE_SHORT' => $date_to,
				'=DATE_CREATE_SHORT' => null
			);
			//$filter['<=BEGINDATE_SHORT'] = $date_to;
		}

		if(!is_null($date_from))
		{
			$filter[] = array(
				'LOGIC' => 'OR',
				'>=DATE_MODIFY_SHORT' => $date_from,
				'=DATE_MODIFY_SHORT' => null
			);
			//$filter['>=CLOSEDATE_SHORT'] = $date_from;
		}

		return $filter;
	}

	public static function clearMenuCache()
	{
		OrderClearMenuCache();
	}

	public static function formatResultsTotal(&$total, &$columnInfo, &$customChartTotal = null)
	{
		parent::formatResultsTotal($total, $columnInfo, $customChartTotal);
	}

	public static function getDefaultReports()
	{
		IncludeModuleLangFile(__FILE__);

		$reports = array(
			'11.0.6' => array(
				array(
					'title' => GetMessage('ORDER_REPORT_DEFAULT_VOLUME_BY_MANAGERS'),
					//'description' => GetMessage('ORDER_REPORT_DEFAULT_VOLUME_BY_MANAGERS_DESCR'),
					'mark_default' => 0,
					'settings' => unserialize('a:10:{s:6:"entity";s:16:"Bitrix\Order\App";s:6:"period";a:2:{s:4:"type";s:5:"month";s:5:"value";N;}s:6:"select";a:8:{i:0;a:2:{s:4:"name";s:14:"ASSIGNED_TITLE";s:5:"alias";s:26:"Ответственный";}i:7;a:1:{s:4:"name";s:13:"ASSIGNED_TYPE";}i:1;a:3:{s:4:"name";s:2:"ID";s:5:"alias";s:24:"Кол-во заявок";s:4:"aggr";s:14:"COUNT_DISTINCT";}i:2;a:3:{s:4:"name";s:10:"IS_EXPIRED";s:5:"alias";s:22:"Просроченно";s:4:"aggr";s:3:"SUM";}i:3;a:3:{s:4:"name";s:6:"IS_NEW";s:5:"alias";s:28:"Новых в очереди";s:4:"aggr";s:3:"SUM";}i:4;a:3:{s:4:"name";s:12:"IS_PROCESSED";s:5:"alias";s:21:"В обработке";s:4:"aggr";s:3:"SUM";}i:5;a:3:{s:4:"name";s:12:"IS_CONVERTED";s:5:"alias";s:40:"Преобразованы в заказ";s:4:"aggr";s:3:"SUM";}i:6;a:3:{s:4:"name";s:9:"IS_DENIED";s:5:"alias";s:16:"Отменены";s:4:"aggr";s:3:"SUM";}}s:6:"filter";a:1:{i:0;a:2:{i:0;a:5:{s:4:"type";s:5:"field";s:4:"name";s:13:"ASSIGNED_TYPE";s:7:"compare";s:5:"EQUAL";s:5:"value";s:4:"user";s:10:"changeable";s:1:"1";}s:5:"LOGIC";s:3:"AND";}}s:4:"sort";i:1;s:9:"sort_type";s:4:"DESC";s:5:"limit";N;s:12:"red_neg_vals";b:0;s:13:"grouping_mode";b:0;s:5:"chart";N;}')
				),
				array(
					'title' => GetMessage('ORDER_REPORT_DEFAULT_VOLUME_BY_MODIFIEDS'),
					//'description' => GetMessage('ORDER_REPORT_DEFAULT_VOLUME_BY_MODIFIEDS_DESCR'),
					'mark_default' => 0,
					'settings' => unserialize('a:10:{s:6:"entity";s:16:"Bitrix\Order\App";s:6:"period";a:2:{s:4:"type";s:5:"month";s:5:"value";N;}s:6:"select";a:8:{i:2;a:1:{s:4:"name";s:19:"MODIFY_BY_FULL_NAME";}i:1;a:2:{s:4:"name";s:2:"ID";s:4:"aggr";s:14:"COUNT_DISTINCT";}i:3;a:2:{s:4:"name";s:10:"IS_EXPIRED";s:4:"aggr";s:3:"SUM";}i:4;a:2:{s:4:"name";s:6:"IS_NEW";s:4:"aggr";s:3:"SUM";}i:5;a:2:{s:4:"name";s:12:"IS_PROCESSED";s:4:"aggr";s:3:"SUM";}i:6;a:2:{s:4:"name";s:12:"IS_CONVERTED";s:4:"aggr";s:3:"SUM";}i:7;a:2:{s:4:"name";s:9:"IS_DENIED";s:4:"aggr";s:3:"SUM";}i:9;a:2:{s:4:"name";s:12:"IS_HAND_MADE";s:4:"aggr";s:3:"SUM";}}s:6:"filter";a:0:{}s:4:"sort";i:1;s:9:"sort_type";s:3:"ASC";s:5:"limit";N;s:12:"red_neg_vals";b:0;s:13:"grouping_mode";b:0;s:5:"chart";N;}')
				),
				array(
					'title' => GetMessage('ORDER_REPORT_DEFAULT_VOLUME_BY_ROOTS'),
					//'description' => GetMessage('ORDER_REPORT_DEFAULT_VOLUME_BY_ROOTS_DESCR'),
					'mark_default' => 0,
					'settings' => unserialize('a:10:{s:6:"entity";s:16:"Bitrix\Order\App";s:6:"period";a:2:{s:4:"type";s:3:"all";s:5:"value";N;}s:6:"select";a:8:{i:8;a:1:{s:4:"name";s:7:"ROOT_ID";}i:1;a:2:{s:4:"name";s:2:"ID";s:4:"aggr";s:14:"COUNT_DISTINCT";}i:10;a:2:{s:4:"name";s:6:"IS_NEW";s:4:"aggr";s:3:"SUM";}i:11;a:2:{s:4:"name";s:12:"IS_PROCESSED";s:4:"aggr";s:3:"SUM";}i:12;a:2:{s:4:"name";s:12:"IS_CONVERTED";s:4:"aggr";s:3:"SUM";}i:13;a:2:{s:4:"name";s:9:"IS_DENIED";s:4:"aggr";s:3:"SUM";}i:14;a:2:{s:4:"name";s:10:"IS_EXPIRED";s:4:"aggr";s:3:"SUM";}i:15;a:2:{s:4:"name";s:12:"IS_HAND_MADE";s:4:"aggr";s:3:"SUM";}}s:6:"filter";a:1:{i:0;a:2:{i:0;a:5:{s:4:"type";s:5:"field";s:4:"name";s:7:"ROOT_ID";s:7:"compare";s:8:"CONTAINS";s:5:"value";s:0:"";s:10:"changeable";s:1:"1";}s:5:"LOGIC";s:3:"AND";}}s:4:"sort";i:8;s:9:"sort_type";s:4:"DESC";s:5:"limit";N;s:12:"red_neg_vals";b:0;s:13:"grouping_mode";b:0;s:5:"chart";N;}')
				)
			)
		);


		//TODO Include lang file for alias

		/*foreach ($reports as &$vreports)
		{
			foreach ($vreports as &$report)
			{
				if ($report['mark_default'] === 1)
				{
					$report['settings']['select'][0]['alias'] = GetMessage('ORDER_REPORT_ALIAS_ASSIGNED_TITLE');
					$report['settings']['select'][20]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APP_TYPE');
					$report['settings']['select'][2]['alias'] = GetMessage('ORDER_REPORT_ALIAS_RESPONSIBLE');
					$report['settings']['select'][7]['alias'] = GetMessage('ORDER_REPORT_ALIAS_COMPANY');
					$report['settings']['select'][23]['alias'] = GetMessage('ORDER_REPORT_ALIAS_COMPANY_TYPE');
					$report['settings']['select'][6]['alias'] = GetMessage('ORDER_REPORT_ALIAS_CONTACT');
					$report['settings']['select'][27]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_PROFIT');
					$report['settings']['select'][4]['alias'] = GetMessage('ORDER_REPORT_ALIAS_CLOSING_DATE');
				}
				elseif ($report['mark_default'] === 2)
				{
					$report['settings']['select'][4]['alias'] = GetMessage('ORDER_REPORT_ALIAS_PRODUCT');
					$report['settings']['select'][5]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_QUANTITY');
					$report['settings']['select'][6]['alias'] = GetMessage('ORDER_REPORT_ALIAS_SOLD_PRODUCTS_QUANTITY');
					$report['settings']['select'][7]['alias'] = GetMessage('ORDER_REPORT_ALIAS_SALES_PROFIT');
				}
				elseif ($report['mark_default'] === 3)
				{
					$report['settings']['select'][4]['alias'] = GetMessage('ORDER_REPORT_ALIAS_LAST_NAME');
					$report['settings']['select'][5]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_QUANTITY');
					$report['settings']['select'][6]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_INPROCESS_QUANTITY');
					$report['settings']['select'][8]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_LOSE_QUANTITY');
					$report['settings']['select'][7]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_WON_QUANTITY');
					$report['settings']['select'][9]['alias'] = GetMessage('ORDER_REPORT_ALIAS_AVERAGE_APP');
					$report['settings']['select'][10]['alias'] = GetMessage('ORDER_REPORT_ALIAS_OPPORTUNITY_AMOUNT');
					$report['settings']['select'][12]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_PROFIT');
				}
				elseif ($report['mark_default'] === 4)
				{
					$report['settings']['select'][4]['alias'] = GetMessage('ORDER_REPORT_ALIAS_COMPANY');
					$report['settings']['select'][5]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_QUANTITY');
					$report['settings']['select'][6]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_INPROCESS_QUANTITY');
					$report['settings']['select'][8]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_LOSE_QUANTITY');
					$report['settings']['select'][7]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_WON_QUANTITY');
					$report['settings']['select'][9]['alias'] = GetMessage('ORDER_REPORT_ALIAS_AVERAGE_APP');
					$report['settings']['select'][10]['alias'] = GetMessage('ORDER_REPORT_ALIAS_OPPORTUNITY_AMOUNT');
					$report['settings']['select'][12]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_PROFIT');
				}
				elseif ($report['mark_default'] === 5)
				{
					$report['settings']['select'][2]['alias'] = GetMessage('ORDER_REPORT_ALIAS_RESPONSIBLE');
					$report['settings']['select'][4]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_QUANTITY');
					$report['settings']['select'][5]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_INPROCESS_QUANTITY');
					$report['settings']['select'][7]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_LOSE_QUANTITY');
					$report['settings']['select'][6]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_WON_QUANTITY');
					$report['settings']['select'][11]['alias'] = GetMessage('ORDER_REPORT_ALIAS_AVERAGE_APP');
					$report['settings']['select'][10]['alias'] = GetMessage('ORDER_REPORT_ALIAS_OPPORTUNITY_AMOUNT');
					$report['settings']['select'][9]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_PROFIT');
				}
				elseif ($report['mark_default'] === 6)
				{
					$report['settings']['select'][0]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APP');
					$report['settings']['select'][2]['alias'] = GetMessage('ORDER_REPORT_ALIAS_RESPONSIBLE');
					$report['settings']['select'][7]['alias'] = GetMessage('ORDER_REPORT_ALIAS_COMPANY');
					$report['settings']['select'][6]['alias'] = GetMessage('ORDER_REPORT_ALIAS_CONTACT');
					$report['settings']['select'][14]['alias'] = GetMessage('ORDER_REPORT_ALIAS_OPPORTUNITY_AMOUNT');
				}
				elseif ($report['mark_default'] === 7)
				{
					$report['settings']['select'][0]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APP');
					$report['settings']['select'][2]['alias'] = GetMessage('ORDER_REPORT_ALIAS_RESPONSIBLE');
					$report['settings']['select'][15]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APP_TYPE');
					$report['settings']['select'][7]['alias'] = GetMessage('ORDER_REPORT_ALIAS_CONTACT');
					$report['settings']['select'][8]['alias'] = GetMessage('ORDER_REPORT_ALIAS_COMPANY');
					$report['settings']['select'][14]['alias'] = GetMessage('ORDER_REPORT_ALIAS_OPPORTUNITY_AMOUNT');
				}
				elseif ($report['mark_default'] === 8)
				{
					$report['settings']['select'][7]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_QUANTITY');
					$report['settings']['select'][12]['alias'] = GetMessage('ORDER_REPORT_ALIAS_PROPORTION');
					$report['settings']['select'][9]['alias'] = GetMessage('ORDER_REPORT_ALIAS_OPPORTUNITY_AMOUNT');
					$report['settings']['select'][11]['alias'] = GetMessage('ORDER_REPORT_ALIAS_APPS_PROFIT');
				}
			}
			unset($report);
		}*/

		return $reports;
	}

	public static function getFirstVersion()
	{
		return '11.0.6';
	}
}

class COrderDirectionReportHelper extends COrderReportHelperBase
{
	public static function getEntityName()
	{
		return 'Bitrix\Order\Direction';
	}
	public static function getOwnerId()
	{
		return 'order_direction';
	}
	public static function getColumnList()
	{
		IncludeModuleLangFile(__FILE__);

		$columnList = array(
			'ID',
			'IS_ROOT',
			'APP_BY'=>array(
				'ID',
				'ROOT_ID'
			)
		);

		return $columnList;
	}



	public static function getDefaultColumns()
	{
		return array(
			//array('name' => 'TITLE'),
			//array('name' => 'STATUS'),
			//array('name' => 'ASSIGNED_TITLE'),
			//array('name' => 'PERIOD')
		);
	}
	public static function getCalcVariations()
	{
		return array_merge(
			parent::getCalcVariations(),
			array(
				'APP_BY.ID' => array('SUM'),
				/*'IS_PROCESSED' => array('SUM'),
				'IS_CONVERTED' => array('SUM'),
				'IS_DENIED' => array('SUM'),
				'IS_EXPIRED' => array('SUM')*/
			)
		);
	}
	public static function getCompareVariations()
	{
		return array_merge(
			parent::getCompareVariations(),
			array(
				/*'ASSIGNED_TYPE' => array(
					'EQUAL',
					'NOT_EQUAL'
				),*/
			)
		);
	}
	public static function beforeViewDataQuery(&$select, &$filter, &$group, &$order, &$limit, &$options, &$runtime = null)
	{
		$a=1;

	}


	public static function formatResultValue($k, &$v, &$row, &$cInfo, $total, &$customChartValue = null)
	{
		// HACK: detect if 'report.view' component is rendering excel spreadsheet
		$isHtml = !(isset($_GET['EXCEL']) && $_GET['EXCEL'] === 'Y');
		$field = $cInfo['field'];
		$fieldName = isset($cInfo['fieldName']) ? $cInfo['fieldName'] : $field->GetName();
		/*if($fieldName === 'ASSIGNED_TYPE')
		{
			if($v !== '')
			{
				$v = self::getTypeName($v, $isHtml);
			}
		}
		if($fieldName === 'ASSIGNED_TITLE')
		{
			if($v !== '')
			{
				//$v = 'asd';
			}
		}*/
	}

	public static function getPeriodFilter($date_from, $date_to)
	{
		if(is_null($date_from) && is_null($date_to))
		{
			return array(); // Empty filter for empty time interval.
		}

		//$now = ConvertTimeStamp(time(), 'FULL');
		$filter = array('LOGIC' => 'AND');
		if(!is_null($date_to))
		{
			$filter[] = array(
				'LOGIC' => 'OR',
				'<=DATE_CREATE_SHORT' => $date_to,
				'=DATE_CREATE_SHORT' => null
			);
			//$filter['<=BEGINDATE_SHORT'] = $date_to;
		}

		if(!is_null($date_from))
		{
			$filter[] = array(
				'LOGIC' => 'OR',
				'>=DATE_MODIFY_SHORT' => $date_from,
				'=DATE_MODIFY_SHORT' => null
			);
			//$filter['>=CLOSEDATE_SHORT'] = $date_from;
		}

		return $filter;
	}

	public static function clearMenuCache()
	{
		OrderClearMenuCache();
	}

	public static function formatResultsTotal(&$total, &$columnInfo, &$customChartTotal = null)
	{
		parent::formatResultsTotal($total, $columnInfo, $customChartTotal);

	}

	public static function getDefaultReports()
	{
		IncludeModuleLangFile(__FILE__);
		$reports=array();


		return $reports;
	}

	public static function getFirstVersion()
	{
		return '11.0.6';
	}
}
?>
