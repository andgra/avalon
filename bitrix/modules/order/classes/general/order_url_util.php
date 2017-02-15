<?php

class COrderUrlUtil
{
    public static function GetUrlScheme($url)
    {
        $url = trim(strval($url));
        $colonOffset = strpos($url, ':');
        if($colonOffset === false)
        {
            $colonOffset = -1;
        }

        $slashOffset = strpos($url, '/');
        if($slashOffset === false)
        {
            $slashOffset = -1;
        }

        return $colonOffset > 0 && ($slashOffset < 0 || $colonOffset < $slashOffset)
            ? strtolower(substr($url, 0, $colonOffset)) : '';
    }
    public static function HasScheme($url)
    {
        return self::GetUrlScheme($url) !== '';
    }
    public static function IsSecureUrl($url)
    {
        $scheme = self::GetUrlScheme($url);
        return $scheme === '' || preg_match('/^(?:(?:ht|f)tp(?:s)?){1}/i', $scheme) === 1;
    }
    public static function IsAbsoluteUrl($url)
    {
        return self::GetUrlScheme($url) !== '';
    }
    public static function ToAbsoluteUrl($url)
    {
        $url = trim(strval($url));

        if($url === '')
        {
            return '';
        }
        elseif(self::GetUrlScheme($url) !== '')
        {
            return $url;
        }

        $scheme = (CMain::IsHTTPS() ? 'https' : 'http');

        $host = '';
        if(defined('SITE_SERVER_NAME') && is_string(SITE_SERVER_NAME))
        {
            $host = SITE_SERVER_NAME;
        }

        if($host === '')
        {
            $host = COption::GetOptionString('main', 'server_name', '');
        }

        if($host === '')
        {
            $host = $_SERVER['SERVER_NAME'];
        }

        $port = intval($_SERVER['SERVER_PORT']);

        if(preg_match('/^\//', $url))
        {
            $url = substr($url, 1);
        }

        return $scheme.'://'.$host.(($port !== 80 && $port !== 443) ? ':'.$port : '').'/'.$url;
    }
    public static function UrnEncode($str, $charset = false)
    {
        global $APPLICATION;

        $result = '';
        $arParts = preg_split("#(://|:\\d+/|/|\\?|=|&)#", $str, -1, PREG_SPLIT_DELIM_CAPTURE);

        if($charset === false)
        {
            $charset = LANG_CHARSET;
        }

        foreach($arParts as $i => $part)
        {
            $result .= ($i % 2)
                ? $part
                : rawurlencode($APPLICATION->ConvertCharset($part, $charset, 'UTF-8'));
            //$result .= ($i % 2) ? $part : urlencode(iconv('windows-1251', 'UTF-8', $part));
        }

        return $result;
    }
    public static function AddUrlParams($url, $params)
    {
        if(empty($params))
        {
            return $url;
        }

        $query = array();
        foreach($params as $k => &$v)
        {
            $query[] = $k.'='.$v;
        }
        unset($v);

        return $url.(strpos($url, '?') === false ? '?' : '&').implode('&', $query);
    }
}