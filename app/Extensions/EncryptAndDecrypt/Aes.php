<?php
namespace App\Extensions\EncryptAndDecrypt;

/**
 * AES128加解密类
 * php7 版本
 */
class Aes
{
    private $key;
    private $iv;

    /**
     * Aes constructor.
     * @param $key
     */
    function __construct($key)
    {
        $this->key = hash('sha256', $key, true);
        $this->iv = $this->hexToStr('00000000000000000000000000000000');
    }

    /**
     * @param $str
     * @return string
     */
    public function encrypt($str)
    {
        return bin2hex(openssl_encrypt($str, 'aes-256-cbc', $this->key, OPENSSL_RAW_DATA, $this->iv));
//        return base64_encode(openssl_encrypt($str, 'aes-256-cbc', $this->key, OPENSSL_RAW_DATA, $this->iv));
    }

    /**
     * @param $str
     * @return string
     */
    public function decrypt($str)
    {
        return openssl_decrypt(pack("H*", $str), 'aes-256-cbc', $this->key, OPENSSL_RAW_DATA, $this->iv);
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
