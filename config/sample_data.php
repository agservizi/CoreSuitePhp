<?php
/**
 * Dati di esempio per l'inizializzazione del database durante l'installazione
 */

return [
    // Dati di esempio per i clienti
    'clients' => [
        [
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@example.com',
            'phone' => '3401234567',
            'fiscal_code' => 'RSSMRA80A01H501T',
            'vat_number' => '',
            'address' => 'Via Roma 123',
            'city' => 'Milano',
            'postal_code' => '20123',
            'province' => 'MI'
        ],
        [
            'first_name' => 'Giulia',
            'last_name' => 'Bianchi',
            'email' => 'giulia.bianchi@example.com',
            'phone' => '3497654321',
            'fiscal_code' => 'BNCGLI85M41H501Z',
            'vat_number' => '',
            'address' => 'Via Garibaldi 45',
            'city' => 'Roma',
            'postal_code' => '00187',
            'province' => 'RM'
        ],
        [
            'first_name' => 'Azienda',
            'last_name' => 'Esempio S.r.l.',
            'email' => 'info@aziendaesempio.it',
            'phone' => '0612345678',
            'fiscal_code' => '',
            'vat_number' => '12345678901',
            'address' => 'Viale dell\'Industria 78',
            'city' => 'Torino',
            'postal_code' => '10149',
            'province' => 'TO'
        ]
    ],
    
    // Dati di esempio per i contratti
    'contracts' => [
        // Contratto telefonico per Mario Rossi
        [
            'client_index' => 0, // Riferimento al primo cliente dell'array 'clients'
            'contract_type' => 'phone',
            'provider' => 'Fastweb',
            'activation_address' => 'Via Roma 123, Milano',
            'phone_number' => '3401234567',
            'migration_code' => 'ABCD1234',
            'contract_date' => date('Y-m-d', strtotime('-30 days')),
            'expiration_date' => date('Y-m-d', strtotime('+335 days')),
            'monthly_fee' => 24.90,
            'status' => 'active'
        ],
        // Contratto energia per Giulia Bianchi
        [
            'client_index' => 1, // Riferimento al secondo cliente dell'array 'clients'
            'contract_type' => 'energy',
            'provider' => 'Enel Energia',
            'activation_address' => 'Via Garibaldi 45, Roma',
            'installation_address' => 'Via Garibaldi 45, Roma',
            'contract_date' => date('Y-m-d', strtotime('-15 days')),
            'expiration_date' => date('Y-m-d', strtotime('+350 days')),
            'monthly_fee' => 45.50,
            'status' => 'active'
        ],
        // Contratto telefonico per Azienda Esempio
        [
            'client_index' => 2, // Riferimento al terzo cliente dell'array 'clients'
            'contract_type' => 'phone',
            'provider' => 'Windtre',
            'activation_address' => 'Viale dell\'Industria 78, Torino',
            'phone_number' => '0612345678',
            'migration_code' => 'WXYZ9876',
            'contract_date' => date('Y-m-d', strtotime('-5 days')),
            'expiration_date' => date('Y-m-d', strtotime('+360 days')),
            'monthly_fee' => 39.90,
            'status' => 'pending'
        ],
        // Contratto energia per Mario Rossi
        [
            'client_index' => 0, // Riferimento al primo cliente dell'array 'clients'
            'contract_type' => 'energy',
            'provider' => 'A2A Energia',
            'activation_address' => 'Via Roma 123, Milano',
            'installation_address' => 'Via Roma 123, Milano',
            'contract_date' => date('Y-m-d', strtotime('-60 days')),
            'expiration_date' => date('Y-m-d', strtotime('+305 days')),
            'monthly_fee' => 55.00,
            'status' => 'active'
        ]
    ],
    
    // Dati di esempio per le note dei clienti
    'notes' => [
        [
            'client_index' => 0,
            'content' => 'Cliente interessato a un\'offerta per il gas domestico'
        ],
        [
            'client_index' => 1,
            'content' => 'Verificare l\'installazione del contatore entro la fine del mese'
        ],
        [
            'client_index' => 2,
            'content' => 'Azienda con 10 dipendenti, valutare offerta business'
        ]
    ]
];
