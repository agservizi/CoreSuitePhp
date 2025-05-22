<?php
// Statistiche rapide per la dashboard
use CoreSuite\Models\Customer;
use CoreSuite\Models\Contract;
use CoreSuite\Models\Provider;
session_start();
$userId = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;
$customerCount = count(Customer::allForUser($userId, $role));
$contractCount = count(Contract::allForUser($userId, $role));
$providerCount = count(Provider::all());
?>
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $customerCount ?></h3>
                <p>Clienti</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="/customers.php" class="small-box-footer">Vai ai clienti <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $contractCount ?></h3>
                <p>Contratti</p>
            </div>
            <div class="icon"><i class="fas fa-file-contract"></i></div>
            <a href="/contracts.php" class="small-box-footer">Vai ai contratti <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $providerCount ?></h3>
                <p>Provider</p>
            </div>
            <div class="icon"><i class="fas fa-building"></i></div>
            <a href="/providers.php" class="small-box-footer">Vai ai provider <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
