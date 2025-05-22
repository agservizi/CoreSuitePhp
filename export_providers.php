<?php
// Esporta provider in CSV
require_once __DIR__ . '/src/models/Provider.php';
require_once __DIR__ . '/src/middleware/auth.php';
use CoreSuite\Models\Provider;
require_role('admin');
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=provider_export.csv');
$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Nome', 'Tipo', 'Logo']);
foreach (Provider::all() as $p) {
    fputcsv($output, [
        $p['id'],
        $p['name'],
        $p['type'],
        $p['logo']
    ]);
}
fclose($output);
exit;
