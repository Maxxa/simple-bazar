<?php

namespace App\Helpers;


use Exception;

class CryptoHelper
{
    public static function binToHex($byte_string)
    {
        $hex = '';
        $len = self::ourStrlen($byte_string);
        for ($i = 0; $i < $len; ++$i) {
            $c = \ord($byte_string[$i]) & 0xf;
            $b = \ord($byte_string[$i]) >> 4;
            $hex .= \pack(
                'CC',
                87 + $b + ((($b - 10) >> 8) & ~38),
                87 + $c + ((($c - 10) >> 8) & ~38)
            );
        }
        return $hex;
    }

    public static function hexToBin($hex_string)
    {
        $hex_pos = 0;
        $bin = '';
        $hex_len = self::ourStrlen($hex_string);
        $state = 0;
        $c_acc = 0;
        while ($hex_pos < $hex_len) {
            $c = \ord($hex_string[$hex_pos]);
            $c_num = $c ^ 48;
            $c_num0 = ($c_num - 10) >> 8;
            $c_alpha = ($c & ~32) - 55;
            $c_alpha0 = (($c_alpha - 10) ^ ($c_alpha - 16)) >> 8;
            if (($c_num0 | $c_alpha0) === 0) {
                throw new \Exception(
                    'hexToBin() input is not a hex string.'
                );
            }
            $c_val = ($c_num0 & $c_num) | ($c_alpha & $c_alpha0);
            if ($state === 0) {
                $c_acc = $c_val * 16;
            } else {
                $bin .= \pack('C', $c_acc | $c_val);
            }
            $state ^= 1;
            ++$hex_pos;
        }
        return $bin;
    }

    public static function ourStrlen($str)
    {
        static $exists = null;
        if ($exists === null) {
            $exists = \function_exists('mb_strlen');
        }
        if ($exists) {
            $length = \mb_strlen($str, '8bit');
            if ($length === false) {
                throw new Exception();
            }
            return $length;
        } else {
            return \strlen($str);
        }
    }


}