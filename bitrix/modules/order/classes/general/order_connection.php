<?php

class COrderConnection
{
    public static function getJSON($entityType,$params=array()) {
        if(!is_null($params['title']))
            $title=str_replace('+','%20',urlencode($params['title']));
        if(!is_null($params['id']))
            $id=str_replace('+','%20',urlencode($params['id']));
        if(!is_null($params['newPhone']))
            $newPhone=str_replace('+','%20',urlencode($params['newPhone']));
        if(!is_null($params['newEmail']))
            $newEmail=str_replace('+','%20',urlencode($params['newEmail']));
        switch (strtoupper($entityType)) {
            case 'PHYSICAL':
                $entUri = 'fiz';
                break;
            case 'CONTACT':
                $entUri = 'KL';
                break;
            case 'AGENT':
                $entUri = 'Contragents';
                break;
            case 'DIRECTION':
                $entUri = 'directions';
                break;
            case 'NOMEN':
                $entUri = 'nomenclature';
                break;
            case 'COURSE':
                $entUri = 'Courses';
                break;
            case 'GROUP':
                $entUri = 'Groups';
                break;
            case 'FORMED_GROUP':
                $entUri = 'FormedGroups';
                break;
            case 'REG':
                $entUri = 'registrations';
                break;
            case 'STAFF':
                $entUri = 'Staff';
                break;
            case 'GUID':
                $entUri ='NewGUID/'.$title;
                break;
            case 'LEGAL_ID':
                $entUri ='NewLegalEntity/'.$title;
                break;
            case 'CHANGE_AGENT_INFO':
                $entUri ='ChangeContactInfo/'.$id.'/'.$newPhone.'/'.$newEmail;
                break;
            case 'DELETE_AGENT':
                $entUri ='DeleteLegalEntity/'.$id;
                break;
            case 'TEACHER':
                $entUri ='Teachers';
                break;
            case 'ROOM':
                $entUri ='Classes';
                break;
            case 'SCHEDULE':
                $entUri ='Schedule';
                break;
            case 'MARK':
                $entUri ='Grades';
                break;
            default:
                return array('ERROR'=>'unknown entity type');
                break;
        }
        $username = 'ANDGRA';
        $password = 'ANDGRA';
        $host = 'legolas.avalon.ru';
        $service_uri = '/UNF/hs/1CDB/' . $entUri;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://' . $host . $service_uri);
        $header = array(
            'Content-Type: application/json',
            'Accept: application/json;charset=utf-8',
            'Connection: Keep-Alive'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 0);
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);


        $html = curl_exec($ch);
        curl_close($ch);
        $html = substr ($html,1);
        //return $html;
        return CUtil::JsObjectToPhp($html);
    }
}