<?php
namespace CoreSuite\Utils;

class Notifier
{
    public static function sendMail($to, $subject, $body, $from = null)
    {
        $headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n";
        if ($from) $headers .= "From: $from\r\n";
        return mail($to, $subject, $body, $headers);
    }

    public static function log($message, $type = 'info')
    {
        $line = date('Y-m-d H:i:s') . " [$type] $message\n";
        file_put_contents(__DIR__ . '/../../../logs/app.log', $line, FILE_APPEND);
    }
}
