<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/crm/configs/exch1c/index.php");
$APPLICATION->SetTitle(GetMessage("CRM_TITLE"));
?><?
$APPLICATION->IncludeComponent(
	"newportal:order.sync",
	".default",
	array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/order/sync/",
	),
	false
);
/*$username='andgra@portal.ru';
$password='DRONCHIK';
//$host = 'legolas.avalon.ru';
$host = 'localhost';
$service_uri ='/temp/nomenclature.txt';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://'.$host.$service_uri);
$header=array(
	'Content-Type: application/json',
	'Accept: application/json;charset=utf-8',
	'Connection: Keep-Alive'
);
curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPGET, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLINFO_HEADER_OUT, 0);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
//curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);


$html=curl_exec($ch);
curl_close($ch);
$html=json_decode($html,true);
var_dump($html);*/
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>