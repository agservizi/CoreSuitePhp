<?php
session_start();
require_once __DIR__ . '/src/middleware/auth.php';
require_role('admin');
if (!isset($_SESSION['user_id'])) die('Non autorizzato');
require_once __DIR__ . '/src/models/Contract.php';
require_once __DIR__ . '/src/utils/Export.php';
use CoreSuite\Models\Contract;
use CoreSuite\Utils\Export;
$contracts = Contract::allForUser($_SESSION['user_id'], $_SESSION['role']);
$header = ['ID','Cliente','Provider','Tipo','Stato','Creato','Extra'];
$data = [];
foreach ($contracts as $c) {
    $data[] = [
        $c['id'], $c['customer_id'], $c['provider'], $c['type'], $c['status'], $c['created_at'], $c['extra_data']
    ];
}
Export::csv('contratti.csv', $data, $header);
