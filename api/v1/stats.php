<?php
// API: GET /api/v1/stats
require_once __DIR__ . '/../../../src/models/Contract.php';
require_once __DIR__ . '/../../../src/models/Customer.php';
use CoreSuite\Models\Contract;
use CoreSuite\Models\Customer;
header('Content-Type: application/json');
require_once __DIR__ . '/jwt_middleware.php';
api_require_auth();

$totalContracts = count(Contract::allForUser(null, 'admin'));
$totalCustomers = count(Customer::all());

// Demo: dati statici, da collegare a query reali
$stats = [
    'contracts_total' => $totalContracts,
    'customers_total' => $totalCustomers,
    'revenue_month' => 32000,
    'performance_kpi' => 87,
    'contracts_by_month' => [12, 19, 15, 22, 30, 28, 35],
    'providers_distribution' => [30, 20, 15, 25, 10]
];
echo json_encode($stats);
