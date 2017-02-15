<?php

function OrderCheckPath($path_name, $param_path, $def_path)
{
    if (strlen($param_path) <= 0 && strlen(COption::GetOptionString('order', strtolower($path_name))) > 0)
        $path_value = htmlspecialcharsbx(COption::GetOptionString('order', strtolower($path_name)));
    else if (strlen($param_path) <= 0)
        $path_value = htmlspecialcharsbx($def_path);
    else
        $path_value = $param_path;

    return $path_value;
}
function normJsonStr($str){
    $str = preg_replace_callback('/\\\\u([a-f0-9]{4})/i', create_function('$m', 'return chr(hexdec($m[1])-1072+224);'), $str);
    return iconv('cp1251', 'utf-8', $str);
}
function printmas($mas,$level=0) {
    $spaces='         ';
    echo "{";
    if(count($mas)>0) {
        echo "\n";
        $level += 1;
        foreach ($mas as $key => $val) {
            for ($i = 0; $i < $level; $i++)
                echo $spaces;
            echo "\"$key\" : ";
            if (is_array($val))
                printmas($val, $level);
            else
                echo "\"".addslashes($val)."\",\n";
        }
        $level -= 1;
        for ($i = 0; $i < $level; $i++)
            echo $spaces;
    }
    echo "},\n";
}

function OrderClearMenuCache()
{
    global $CACHE_MANAGER;
    $CACHE_MANAGER->CleanDir('menu');
    $CACHE_MANAGER->ClearByTag('order_change_role');
}
if (!function_exists('mb_ucfirst') && extension_loaded('mbstring'))
{
    /**
     * mb_ucfirst - ??????????? ?????? ?????? ? ??????? ???????
     * @param string $str - ??????
     * @param string $encoding - ?????????, ??-????????? UTF-8
     * @return string
     */
    function mb_ucfirst($str, $encoding='UTF-8')
    {
        $str = mb_ereg_replace('^[\ ]+', '', $str);
        $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
            mb_substr($str, 1, mb_strlen($str), $encoding);
        return $str;
    }
}
?>