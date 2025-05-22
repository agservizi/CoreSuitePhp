<?php
namespace CoreSuite\Utils;
class Validation {
    public static function cf($cf) {
        // Algoritmo di controllo codice fiscale italiano
        $cf = strtoupper($cf);
        if (!preg_match('/^[A-Z0-9]{16}$/', $cf)) return false;
        $set1 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $set2 = 'ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $even_map = [0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5];
        $odd_map = [1,0,5,7,9,13,15,17,19,21,2,4,18,20,11,3,6,8,12,14,16,10,22,25,24,23];
        $sum = 0;
        for ($i=0; $i<15; $i++) {
            $c = strpos($set1, $cf[$i]);
            $sum += ($i%2) ? $even_map[$c] : $odd_map[$c];
        }
        $check = chr($sum%26+65);
        return $check === $cf[15];
    }
    public static function piva($piva) {
        // Algoritmo di controllo partita IVA italiana
        if (!preg_match('/^[0-9]{11}$/', $piva)) return false;
        $s = 0;
        for ($i=0; $i<11; $i++) {
            $n = intval($piva[$i]);
            if (($i % 2) == 0) $s += $n;
            else {
                $n *= 2;
                if ($n > 9) $n -= 9;
                $s += $n;
            }
        }
        return ($s % 10) == 0;
    }
    public static function iban($iban) {
        $iban = strtoupper(str_replace(' ', '', $iban));
        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/', $iban)) return false;
        $iban = substr($iban, 4) . substr($iban, 0, 4);
        $iban = preg_replace_callback('/[A-Z]/', function($m) { return ord($m[0]) - 55; }, $iban);
        $rem = intval(substr($iban, 0, 1));
        for ($i = 1; $i < strlen($iban); $i++) {
            $rem = ($rem * 10 + intval($iban[$i])) % 97;
        }
        return $rem == 1;
    }
    public static function cap($cap) {
        return preg_match('/^[0-9]{5}$/', $cap);
    }
    public static function pod($pod) {
        return preg_match('/^IT[0-9A-Z]{14}$/', strtoupper($pod));
    }
    public static function pdr($pdr) {
        return preg_match('/^[0-9]{14}$/', $pdr);
    }
    public static function migrazione($code) {
        return preg_match('/^[A-Z0-9]{7,18}$/i', $code);
    }
}
