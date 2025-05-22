<?php
// Esporta clienti in CSV
require_once __DIR__ . '/src/models/Customer.php';
require_once __DIR__ . '/src/middleware/auth.php';
use CoreSuite\Models\Customer;
require_role('admin');
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=clienti_export.csv');
$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Nome', 'Cognome', 'Codice Fiscale', 'Email', 'Telefono']);
foreach (Customer::all() as $c) {
    fputcsv($output, [
        $c['id'],
        $c['name'],
        $c['surname'],
        $c['fiscal_code'],
        $c['email'],
        $c['phone']
    ]);
}
fclose($output);
exit;
