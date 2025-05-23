<?php
// Script per popolare il database con dati di esempio
require_once __DIR__ . '/config/database.php';

try {
    // Assicurati che config/database.php definisca le costanti o variabili seguenti
    $db = new PDO(
        'mysql:host=' . $db_config['host'] . ';dbname=' . $db_config['name'] . ';charset=utf8mb4',
        $db_config['user'],
        $db_config['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Pulisci le tabelle esistenti
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    $db->exec("TRUNCATE TABLE notes");
    $db->exec("TRUNCATE TABLE consents");
    $db->exec("TRUNCATE TABLE attachments");
    $db->exec("TRUNCATE TABLE addresses");
    $db->exec("TRUNCATE TABLE contracts");
    $db->exec("TRUNCATE TABLE providers");
    $db->exec("TRUNCATE TABLE customers");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Inserisci provider
    $providers = [
        ['name' => 'TIM', 'code' => 'TIM', 'type' => 'telefonia', 'form_config' => '{"requires_id":true}'],
        ['name' => 'Vodafone', 'code' => 'VOD', 'type' => 'telefonia', 'form_config' => '{"requires_id":true}'],
        ['name' => 'WindTre', 'code' => 'WIND', 'type' => 'telefonia', 'form_config' => '{"requires_id":true}'],
        ['name' => 'Enel Energia', 'code' => 'ENEL', 'type' => 'energia', 'form_config' => '{"requires_pod":true}'],
        ['name' => 'ENI Gas e Luce', 'code' => 'ENI', 'type' => 'gas', 'form_config' => '{"requires_pdr":true}']
    ];

    $stmt = $db->prepare("
        INSERT INTO providers (id, name, code, type, form_config) 
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($providers as $index => $provider) {
        $stmt->execute([
            $index + 1,
            $provider['name'],
            $provider['code'],
            $provider['type'],
            $provider['form_config']
        ]);
    }

    // Inserisci clienti
    $customers = [
        ['first_name' => 'Mario', 'last_name' => 'Rossi', 'email' => 'mario.rossi@example.com', 'phone' => '3331234567', 'tax_code' => 'RSSMRA80A01H501U', 'notes' => 'Cliente residenziale'],
        ['first_name' => 'Anna', 'last_name' => 'Bianchi', 'email' => 'anna.bianchi@example.com', 'phone' => '3389876543', 'tax_code' => 'BNCNNA75E45F205Z', 'notes' => 'Cliente business'],
        ['first_name' => 'Luigi', 'last_name' => 'Verdi', 'email' => 'luigi.verdi@example.com', 'phone' => '3351122334', 'tax_code' => 'VRDLGU90P10A944K', 'notes' => 'Cliente residenziale con offerte speciali'],
        ['first_name' => 'Giulia', 'last_name' => 'Neri', 'email' => 'giulia.neri@example.com', 'phone' => '3207788991', 'tax_code' => 'NREGLI85D50L219J', 'notes' => 'Cliente residenziale'],
        ['first_name' => 'Marco', 'last_name' => 'Gialli', 'email' => 'marco.gialli@example.com', 'phone' => '3664455667', 'tax_code' => 'GLLMRC70H15F839X', 'notes' => 'Cliente business premium']
    ];

    $stmt = $db->prepare("
        INSERT INTO customers (id, first_name, last_name, email, phone, tax_code, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($customers as $index => $customer) {
        $stmt->execute([
            $index + 1,
            $customer['first_name'],
            $customer['last_name'],
            $customer['email'],
            $customer['phone'],
            $customer['tax_code'],
            $customer['notes']
        ]);
    }

    // Inserisci contratti
    $contracts = [
        ['customer_id' => 1, 'provider' => 1, 'type' => 'telefonia', 'status' => 'attivo', 'created_at' => '2023-01-15 09:30:00', 'user_id' => 1],
        ['customer_id' => 1, 'provider' => 4, 'type' => 'energia', 'status' => 'attivo', 'created_at' => '2023-02-20 14:15:00', 'user_id' => 1],
        ['customer_id' => 2, 'provider' => 2, 'type' => 'telefonia', 'status' => 'in lavorazione', 'created_at' => '2023-03-10 11:00:00', 'user_id' => 1],
        ['customer_id' => 3, 'provider' => 5, 'type' => 'gas', 'status' => 'attivo', 'created_at' => '2023-04-05 16:45:00', 'user_id' => 1],
        ['customer_id' => 4, 'provider' => 3, 'type' => 'telefonia', 'status' => 'scaduto', 'created_at' => '2023-05-12 10:30:00', 'user_id' => 1],
        ['customer_id' => 5, 'provider' => 4, 'type' => 'energia', 'status' => 'attivo', 'created_at' => '2023-06-01 09:00:00', 'user_id' => 1],
        ['customer_id' => 5, 'provider' => 5, 'type' => 'gas', 'status' => 'attivo', 'created_at' => '2023-06-01 09:30:00', 'user_id' => 1]
    ];

    $stmt = $db->prepare("
        INSERT INTO contracts (id, customer_id, provider, type, status, created_at, user_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($contracts as $index => $contract) {
        $stmt->execute([
            $index + 1,
            $contract['customer_id'],
            $contract['provider'],
            $contract['type'],
            $contract['status'],
            $contract['created_at'],
            $contract['user_id']
        ]);
    }

    echo "Database popolato con successo con dati di esempio!";

} catch (PDOException $e) {
    echo "Errore durante la popolazione del database: " . $e->getMessage();
}
?>
