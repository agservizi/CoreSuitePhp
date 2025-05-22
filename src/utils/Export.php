<?php
namespace CoreSuite\Utils;

class Export
{
    public static function csv($filename, $data, $header = null)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        if ($header) fputcsv($out, $header);
        foreach ($data as $row) fputcsv($out, $row);
        fclose($out);
        exit;
    }

    public static function json($filename, $data)
    {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
}
