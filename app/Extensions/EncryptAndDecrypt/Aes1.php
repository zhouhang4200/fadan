<?php
namespace App\Extensions\EncryptAndDecrypt;

/**
 * 测试一下，不要使用此类
 * php 7.0及之前版本用这个
 * AES128加解密类
 */
class Aes1
{
    private $hexIv = '00000000000000000000000000000000';

    private $key;

    function __construct($key)
    {
        $this->key = hash('sha256', $key, true);

    }

    function encrypt($str)
    {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($td, $this->key, $this->hexToStr($this->hexIv));
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $encrypted = mcrypt_generic($td, $str);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return bin2hex($encrypted);
    }

    function decrypt($code)
    {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($td, $this->key, $this->hexToStr($this->hexIv));
        $str = mdecrypt_generic($td, pack("H*", $code));
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $this->strIppAdding($str);
    }

    /**
     * For PKCS7 padding
     * @param $string
     * @param int $blockSize
     * @return string
     */
    private function addpadding($string, $blockSize = 16)
    {
        $len = strlen($string);
        $pad = $blockSize - ($len % $blockSize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }

    /**
     * @param $string
     * @return bool|string
     */
    private function strIppAdding($string)
    {
        $sLast = ord(substr($string, -1));
        $slastc = chr($sLast);
        $pCheck = substr($string, -$sLast);
        if (preg_match("/$slastc{" . $sLast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $sLast);
            return $string;
        } else {
            return false;
        }
    }

    private function hexToStr($hex)
    {
        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }
}
