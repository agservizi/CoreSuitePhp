<?php
namespace CoreSuite\Utils;

class Audit
{
    public static function log($userId, $action, $details = null)
    {
        $line = date('Y-m-d H:i:s') . " [user:$userId] $action";
        if ($details) $line .= " | " . json_encode($details);
        $line .= "\n";
        file_put_contents(__DIR__ . '/../../../logs/audit.log', $line, FILE_APPEND);
    }
}
