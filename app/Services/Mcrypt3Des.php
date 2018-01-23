<?php
namespace App\Services;

/**
 * 支持php 7.1
 * Class Mcrypt3Des
 * @package App\Services
 */
class Mcrypt3Des
{
    private $key = '';
    private $iv = '';

    /**
     * Mcrypt3Des constructor.
     * @param string $key
     * @param string $iv
     * @throws \Exception
     */
    function __construct($key = 'wanzi001', $iv = 'wanzi001')
    {
        if (strlen($key) != 8 || strlen($iv) != 8) {
            throw new  \Exception('key 与 iv 长度需为8');
        }
        $this->key = $key;
        $this->iv = $iv;
    }

    /**
     * 加密
     * @param $value
     * @return string
     */
    public function encrypt($value)
    {
        $value = $this->paddingPKCS7($value);
        $cipher = "DES-EDE3-CBC";
        $result = '';
        if (in_array($cipher, openssl_get_cipher_methods())) {
            $result = openssl_encrypt($value, $cipher, $this->key, OPENSSL_SSLV23_PADDING, $this->iv);
        }
        return $result;
    }

    /**
     * 解密
     * @param <type> $value
     * @return bool|string <type>
     */
    public function decrypt($value)
    {
        $decrypted = openssl_decrypt($value, 'DES-EDE3-CBC', $this->key, OPENSSL_SSLV23_PADDING, $this->iv);
        $ret = $this->unPaddingPKCS7($decrypted);
        return $ret;
    }

    /**
     *
     * @param $data
     * @return string
     */
    private function paddingPKCS7($data)
    {
        $blockSize = 8;
        $paddingChar = $blockSize - (strlen($data) % $blockSize);
        $data .= str_repeat(chr($paddingChar), $paddingChar);
        return $data;
    }

    /**
     * @param $text
     * @return bool|string
     */
    private function unPaddingPKCS7($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }
}