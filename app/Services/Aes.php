<?php
namespace App\Services;

/**
 * AES128加解密类
 * php7 版本
 */
class Aes
{
    private $key;


    /**
     * Aes constructor.
     * @param $key
     */
    function __construct()
    {
        $this->key = '1234567890123456';
    }

    /**
     * @param $str
     * @return string
     */
    public function encrypt($str)
    {
        $encrypted = openssl_encrypt($str, 'AES-128-ECB', $this->key, OPENSSL_RAW_DATA);
        return base64_encode($encrypted);
    }

    /**
     * @param $str
     * @return string
     */
    public function decrypt($str)
    {
        return openssl_decrypt(base64_decode($str), 'AES-128-ECB', $this->key, OPENSSL_RAW_DATA);
    }

    /**
     * @param $hex
     * @return string
     */
    private function hexToStr($hex)
    {
        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }
}