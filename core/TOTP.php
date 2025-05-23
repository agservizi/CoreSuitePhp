<?php
// Helper TOTP per MFA (Google Authenticator compatibile)
namespace Core;
class TOTP {
    public static function base32Decode($b32) {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $b32 = strtoupper($b32);
        $l = strlen($b32);
        $n = 0;
        $j = 0;
        $binary = '';
        for ($i = 0; $i < $l; $i++) {
            $n = $n << 5;
            $n = $n + strpos($alphabet, $b32[$i]);
            $j += 5;
            if ($j >= 8) {
                $j -= 8;
                $binary .= chr(($n & (0xFF << $j)) >> $j);
            }
        }
        return $binary;
    }
    public static function getCode($secret, $timeSlice = null) {
        if ($timeSlice === null) $timeSlice = floor(time() / 30);
        $secretkey = self::base32Decode($secret);
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);
        $hm = hash_hmac('sha1', $time, $secretkey, true);
        $offset = ord(substr($hm, -1)) & 0x0F;
        $hashpart = substr($hm, $offset, 4);
        $value = unpack('N', $hashpart)[1] & 0x7FFFFFFF;
        return str_pad($value % 1000000, 6, '0', STR_PAD_LEFT);
    }
    public static function verify($secret, $code) {
        $current = self::getCode($secret);
        $prev = self::getCode($secret, floor(time() / 30) - 1);
        $next = self::getCode($secret, floor(time() / 30) + 1);
        return ($code === $current || $code === $prev || $code === $next);
    }
    public static function randomSecret($length = 16) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }
}
