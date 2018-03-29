<?php
namespace App\Services;

class Helper
{
    /**
     * 自定义日志写入
     * @param $fileName
     * @param array $data
     */
    public static function log($fileName, $data = [])
    {
        $log = new \Monolog\Logger($fileName);
        $log->pushHandler(new \Monolog\Handler\StreamHandler(storage_path() . '/logs/' .$fileName. '-' . date('Y-m-d') .'.log'));
        $log->addInfo($fileName, $data);
    }

    /**
     * @return mixed
     */
    public static function getIp()
    {
        $ip = '';
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $ipArr = explode(',', $ip);
        return $ipArr[0];
    }

    public static function getMicroTime()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }

    public static function Sec2Time($time, $showSeconds = false)
    {
        if (is_numeric($time)) {
            $value = array(
              'years' => 0, 'days' => 0, 'hours' => 0,
              'minutes' => 0, 'seconds' => 0,
            );
            if($time >= 31556926){
              $value['years'] = floor($time/31556926);
              $time = ($time%31556926);
            }
            if($time >= 86400){
              $value['days'] = floor($time/86400);
              $time = ($time%86400);
            }
            if($time >= 3600){
              $value['hours'] = floor($time/3600);
              $time = ($time%3600);
            }
            if($time >= 60){
              $value['minutes'] = floor($time/60);
              $time = ($time%60);
            }
            $value['seconds'] = floor($time);

            $t = '';
            if ($value['years'] > 0) {
                $t .= $value['years'] .'年 ';
            }
            $t .= $value['days'] . '天 ' . $value['hours'] . '小时 ' . $value['minutes'] . '分 ';

            if ($showSeconds) {
              $t .= $value['seconds'] . '秒';
            }
            Return $t;
        } else {
            return (bool) FALSE;
        }
     }

     // 自定义ID加密编码
    private static $_customCode = [
        0 => ['a', 'b', 'c'],
        1 => ['d', 'e', '1'],
        2 => ['f', 'g', 'h'],
        3 => ['i', 'j', '3'],
        4 => ['k', 'l', 'm'],
        5 => ['n', 'o', '5'],
        6 => ['p', 'q', 'r'],
        7 => ['s', 't', '7'],
        8 => ['u', 'v', 'w'],
        9 => ['x', 'y', '9'],
    ];

    // 自定义加密
    public static function customEncode($string)
    {
        $code = '';
        for ($i=0; $i < strlen($string); $i++) {
            $num = substr($string, $i, 1);
            $code .= self::$_customCode[$num][array_rand(self::$_customCode[$num])];
        }

        // 不足4位填充z
        $j = 4 - strlen($code);
        for ($i=0; $i < $j; $i++) {
            $code .= 'z';
        }

        return $code;
    }

    // 自定义解密
    public static function customDecode($code)
    {
        $string = '';
        for ($i=0; $i < strlen($code); $i++) {
            $str = substr($code, $i, 1);
            foreach (self::$_customCode as $key => $arr) {
                if (in_array($str, $arr)) {
                    $string .= $key;
                    break;
                }
            }
        }

        return $string;
    }

    // api接口加密
    public static function apiEncode($data)
    {
        return base64_encode((new Mcrypt3Des('sdfj903a', 'sdfj903a'))->encrypt(json_encode($data)));
    }
}
