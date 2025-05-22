<?php
// Configurazione campi dinamici per ogni tipologia/provider
return [
    'telefonia' => [
        'steps' => [
            'anagrafica' => [
                'label' => 'Dati Anagrafici',
                'fields' => [
                    ['name' => 'nome', 'label' => 'Nome', 'type' => 'text', 'required' => true],
                    ['name' => 'cognome', 'label' => 'Cognome', 'type' => 'text', 'required' => true],
                    ['name' => 'codice_fiscale', 'label' => 'Codice Fiscale', 'type' => 'text', 'required' => true, 'validate' => 'cf'],
                    ['name' => 'data_nascita', 'label' => 'Data di nascita', 'type' => 'date', 'required' => true],
                    ['name' => 'luogo_nascita', 'label' => 'Luogo di nascita', 'type' => 'text', 'required' => true],
                    ['name' => 'provincia_nascita', 'label' => 'Provincia', 'type' => 'text', 'required' => true],
                    ['name' => 'sesso', 'label' => 'Sesso', 'type' => 'select', 'options' => ['M','F'], 'required' => true],
                    ['name' => 'cittadinanza', 'label' => 'Cittadinanza', 'type' => 'text', 'required' => true],
                    ['name' => 'tipo_documento', 'label' => 'Tipo documento', 'type' => 'select', 'options' => ['Carta Identità','Patente','Passaporto'], 'required' => true],
                    ['name' => 'numero_documento', 'label' => 'Numero documento', 'type' => 'text', 'required' => true],
                    ['name' => 'data_rilascio', 'label' => 'Data rilascio', 'type' => 'date', 'required' => true],
                    ['name' => 'data_scadenza', 'label' => 'Data scadenza', 'type' => 'date', 'required' => true],
                    ['name' => 'ente_rilascio', 'label' => 'Ente rilascio', 'type' => 'text', 'required' => true],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                    ['name' => 'email_conferma', 'label' => 'Conferma Email', 'type' => 'email', 'required' => true],
                    ['name' => 'telefono', 'label' => 'Telefono fisso', 'type' => 'text'],
                    ['name' => 'cellulare', 'label' => 'Cellulare', 'type' => 'text', 'required' => true],
                    ['name' => 'pec', 'label' => 'PEC (se azienda)', 'type' => 'email'],
                ]
            ],
            'indirizzo_residenza' => [
                'label' => 'Indirizzo Residenza/Sede Legale',
                'fields' => [
                    ['name' => 'via', 'label' => 'Via/Piazza', 'type' => 'text', 'required' => true],
                    ['name' => 'civico', 'label' => 'Numero civico', 'type' => 'text', 'required' => true],
                    ['name' => 'interno', 'label' => 'Interno/Scala/Piano', 'type' => 'text'],
                    ['name' => 'cap', 'label' => 'CAP', 'type' => 'text', 'required' => true, 'validate' => 'cap'],
                    ['name' => 'citta', 'label' => 'Città', 'type' => 'text', 'required' => true],
                    ['name' => 'provincia', 'label' => 'Provincia', 'type' => 'text', 'required' => true],
                    ['name' => 'regione', 'label' => 'Regione', 'type' => 'text', 'required' => true],
                    ['name' => 'stato', 'label' => 'Stato', 'type' => 'text', 'required' => true, 'default' => 'Italia'],
                ]
            ],
            'indirizzo_servizio' => [
                'label' => 'Indirizzo Attivazione Servizio',
                'fields' => [
                    ['name' => 'uguale_residenza', 'label' => 'Uguale a indirizzo residenza', 'type' => 'checkbox'],
                    ['name' => 'via_servizio', 'label' => 'Via/Piazza', 'type' => 'text', 'required' => true],
                    ['name' => 'civico_servizio', 'label' => 'Numero civico', 'type' => 'text', 'required' => true],
                    ['name' => 'interno_servizio', 'label' => 'Interno/Scala/Piano', 'type' => 'text'],
                    ['name' => 'cap_servizio', 'label' => 'CAP', 'type' => 'text', 'required' => true],
                    ['name' => 'citta_servizio', 'label' => 'Città', 'type' => 'text', 'required' => true],
                    ['name' => 'provincia_servizio', 'label' => 'Provincia', 'type' => 'text', 'required' => true],
                    ['name' => 'regione_servizio', 'label' => 'Regione', 'type' => 'text', 'required' => true],
                    ['name' => 'nome_citofono', 'label' => 'Nome citofono/Referente locale', 'type' => 'text'],
                    ['name' => 'note_accesso', 'label' => 'Note accesso', 'type' => 'textarea'],
                ]
            ],
            'dati_professionali' => [
                'label' => 'Dati Professionali (se P.IVA)',
                'fields' => [
                    ['name' => 'partita_iva', 'label' => 'Partita IVA', 'type' => 'text', 'validate' => 'piva'],
                    ['name' => 'ragione_sociale', 'label' => 'Ragione sociale', 'type' => 'text'],
                    ['name' => 'codice_ateco', 'label' => 'Codice ATECO', 'type' => 'text'],
                    ['name' => 'forma_giuridica', 'label' => 'Forma giuridica', 'type' => 'text'],
                    ['name' => 'codice_sdi', 'label' => 'Codice destinatario SDI', 'type' => 'text'],
                    ['name' => 'pec_fatturazione', 'label' => 'PEC fatturazione', 'type' => 'email'],
                    ['name' => 'rappresentante_legale', 'label' => 'Rappresentante legale', 'type' => 'text'],
                    ['name' => 'cf_rappresentante', 'label' => 'Codice fiscale rappresentante', 'type' => 'text'],
                    ['name' => 'carica_sociale', 'label' => 'Carica sociale', 'type' => 'text'],
                ]
            ],
            'servizi' => [
                'label' => 'Servizi Richiesti',
                'fields' => [
                    ['name' => 'tipologia_contratto', 'label' => 'Tipologia contratto', 'type' => 'select', 'options' => ['Solo telefono fisso','Solo ADSL/Fibra','Telefono + Internet','Mobile','Pacchetto completo'], 'required' => true],
                    ['name' => 'velocita', 'label' => 'Velocità richiesta', 'type' => 'select', 'options' => ['ADSL 7 Mega','ADSL 20 Mega','Fibra 100 Mega','Fibra 200 Mega','Fibra 1000 Mega'], 'required' => true],
                    ['name' => 'tecnologia', 'label' => 'Tecnologia preferita', 'type' => 'select', 'options' => ['ADSL','VDSL','FTTH','FWA']],
                    ['name' => 'modem', 'label' => 'Modem', 'type' => 'select', 'options' => ['Sì','No','Già presente']],
                    ['name' => 'servizi_aggiuntivi', 'label' => 'Servizi aggiuntivi', 'type' => 'checkbox_group', 'options' => ['Linea telefonica','Chiamate illimitate','Segreteria telefonica','Trasferimento chiamata','Avviso di chiamata']],
                    ['name' => 'numero_sim', 'label' => 'Numero SIM richieste', 'type' => 'number'],
                    ['name' => 'piano_tariffario', 'label' => 'Piano tariffario', 'type' => 'text'],
                    ['name' => 'giga', 'label' => 'Giga inclusi', 'type' => 'number'],
                    ['name' => 'minuti_sms', 'label' => 'Minuti/SMS inclusi', 'type' => 'text'],
                    ['name' => 'servizi_extra_mobile', 'label' => 'Servizi extra mobile', 'type' => 'checkbox_group', 'options' => ['Roaming','Hotspot']],
                ]
            ],
            'migrazione' => [
                'label' => 'Migrazione/Portabilità',
                'fields' => [
                    ['name' => 'portabilita', 'label' => 'Portabilità numero', 'type' => 'select', 'options' => ['Sì','No'], 'required' => true],
                    ['name' => 'numero_portare', 'label' => 'Numero da portare', 'type' => 'text'],
                    ['name' => 'operatore_attuale', 'label' => 'Operatore attuale', 'type' => 'text'],
                    ['name' => 'codice_migrazione', 'label' => 'Codice migrazione', 'type' => 'text', 'validate' => 'migrazione'],
                    ['name' => 'intestatario_linea_attuale', 'label' => 'Intestatario linea attuale', 'type' => 'text'],
                    ['name' => 'autorizzazione_portabilita', 'label' => 'Autorizzazione portabilità', 'type' => 'checkbox'],
                    ['name' => 'mantenimento_linea', 'label' => 'Mantenimento linea esistente', 'type' => 'select', 'options' => ['Sì','No']],
                    ['name' => 'data_attivazione', 'label' => 'Data attivazione preferita', 'type' => 'date'],
                    ['name' => 'fascia_oraria', 'label' => 'Fascia oraria contatto', 'type' => 'text'],
                ]
            ],
            'pagamento' => [
                'label' => 'Modalità di Pagamento',
                'fields' => [
                    ['name' => 'metodo_pagamento', 'label' => 'Metodo pagamento', 'type' => 'select', 'options' => ['SEPA/RID','Bollettino postale','Bonifico bancario','Carta di credito'], 'required' => true],
                    ['name' => 'iban', 'label' => 'IBAN', 'type' => 'text', 'validate' => 'iban'],
                    ['name' => 'intestatario_conto', 'label' => 'Intestatario conto', 'type' => 'text'],
                    ['name' => 'banca', 'label' => 'Banca', 'type' => 'text'],
                    ['name' => 'autorizzazione_sepa', 'label' => 'Autorizzazione SEPA', 'type' => 'checkbox'],
                    ['name' => 'tipo_carta', 'label' => 'Tipo carta', 'type' => 'text'],
                    ['name' => 'intestatario_carta', 'label' => 'Intestatario carta', 'type' => 'text'],
                    ['name' => 'codice_autorizzazione', 'label' => 'Codice autorizzazione', 'type' => 'text'],
                ]
            ],
            'consensi' => [
                'label' => 'Consensi e Privacy',
                'fields' => [
                    ['name' => 'consenso_trattamento', 'label' => 'Consenso trattamento dati personali', 'type' => 'checkbox', 'required' => true],
                    ['name' => 'consenso_marketing', 'label' => 'Consenso finalità marketing', 'type' => 'checkbox'],
                    ['name' => 'consenso_terze_parti', 'label' => 'Consenso comunicazioni terze parti', 'type' => 'checkbox'],
                    ['name' => 'consenso_profilazione', 'label' => 'Consenso profilazione', 'type' => 'checkbox'],
                    ['name' => 'registro_opposizioni', 'label' => 'Iscrizione registro opposizioni', 'type' => 'checkbox'],
                    ['name' => 'modalita_comunicazioni', 'label' => 'Modalità invio comunicazioni', 'type' => 'checkbox_group', 'options' => ['Email','SMS','Posta tradizionale','Telefono']],
                ]
            ],
            'upload' => [
                'label' => 'Upload Documenti',
                'fields' => [
                    ['name' => 'doc_identita', 'label' => 'Documento identità (fronte/retro)', 'type' => 'file', 'required' => true],
                    ['name' => 'codice_fiscale_file', 'label' => 'Codice fiscale', 'type' => 'file', 'required' => true],
                    ['name' => 'ultima_bolletta', 'label' => 'Ultima bolletta attuale', 'type' => 'file'],
                    ['name' => 'visura_camerale', 'label' => 'Visura camerale (se P.IVA)', 'type' => 'file'],
                    ['name' => 'delega', 'label' => 'Delega (se diverso intestatario)', 'type' => 'file'],
                    ['name' => 'planimetria', 'label' => 'Planimetria/Foto contatore', 'type' => 'file'],
                    ['name' => 'autorizzazioni_speciali', 'label' => 'Autorizzazioni speciali', 'type' => 'file'],
                    ['name' => 'contratto_precedente', 'label' => 'Contratto precedente', 'type' => 'file'],
                ]
            ],
            'note' => [
                'label' => 'Note e Comunicazioni',
                'fields' => [
                    ['name' => 'note_cliente', 'label' => 'Richieste particolari del cliente', 'type' => 'textarea'],
                    ['name' => 'note_tecnico', 'label' => 'Note per il tecnico', 'type' => 'textarea'],
                    ['name' => 'preferenze_orari', 'label' => 'Preferenze orari contatto', 'type' => 'text'],
                    ['name' => 'giorni_disponibilita', 'label' => 'Giorni disponibilità installazione', 'type' => 'text'],
                    ['name' => 'comunicazioni_urgenti', 'label' => 'Comunicazioni urgenti', 'type' => 'textarea'],
                    ['name' => 'referente_alternativo', 'label' => 'Referente alternativo per sopralluoghi', 'type' => 'text'],
                ]
            ],
            'riepilogo' => [
                'label' => 'Riepilogo e Conferma',
                'fields' => []
            ]
        ]
    ],
    'luce' => [
        'steps' => [
            'anagrafica' => [
                'label' => 'Dati Anagrafici',
                'fields' => [
                    ['name' => 'nome', 'label' => 'Nome', 'type' => 'text', 'required' => true],
                    ['name' => 'cognome', 'label' => 'Cognome', 'type' => 'text', 'required' => true],
                    ['name' => 'codice_fiscale', 'label' => 'Codice Fiscale', 'type' => 'text', 'required' => true, 'validate' => 'cf'],
                    ['name' => 'data_nascita', 'label' => 'Data di nascita', 'type' => 'date', 'required' => true],
                    ['name' => 'luogo_nascita', 'label' => 'Luogo di nascita', 'type' => 'text', 'required' => true],
                    ['name' => 'provincia_nascita', 'label' => 'Provincia', 'type' => 'text', 'required' => true],
                    ['name' => 'sesso', 'label' => 'Sesso', 'type' => 'select', 'options' => ['M','F'], 'required' => true],
                    ['name' => 'cittadinanza', 'label' => 'Cittadinanza', 'type' => 'text', 'required' => true],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                    ['name' => 'telefono', 'label' => 'Telefono fisso', 'type' => 'text'],
                    ['name' => 'cellulare', 'label' => 'Cellulare', 'type' => 'text', 'required' => true],
                ]
            ],
            'dati_fornitura' => [
                'label' => 'Dati Fornitura Elettrica',
                'fields' => [
                    ['name' => 'tipologia_cliente', 'label' => 'Tipologia cliente', 'type' => 'select', 'options' => ['Domestico residente','Domestico non residente','Altri usi','Attività commerciali/industriali'], 'required' => true],
                    ['name' => 'pod', 'label' => 'Codice POD', 'type' => 'text', 'required' => true, 'validate' => 'pod'],
                    ['name' => 'potenza_impegnata', 'label' => 'Potenza impegnata (kW)', 'type' => 'number', 'required' => true],
                    ['name' => 'potenza_disponibile', 'label' => 'Potenza disponibile (kW)', 'type' => 'number'],
                    ['name' => 'tensione', 'label' => 'Tensione fornitura', 'type' => 'select', 'options' => ['BT','MT','AT']],
                    ['name' => 'tipologia_contatore', 'label' => 'Tipologia contatore', 'type' => 'select', 'options' => ['Tradizionale','Elettronico']],
                    ['name' => 'matricola_contatore', 'label' => 'Numero matricola contatore', 'type' => 'text'],
                    ['name' => 'regime_tutela', 'label' => 'Regime di tutela attuale', 'type' => 'select', 'options' => ['Mercato libero','Servizio di tutela graduali','Servizio a tutele graduali','Altro']],
                    ['name' => 'fornitore_attuale', 'label' => 'Fornitore attuale', 'type' => 'text'],
                    ['name' => 'codice_cliente_attuale', 'label' => 'Codice cliente attuale', 'type' => 'text'],
                    ['name' => 'data_ultima_bolletta', 'label' => 'Data ultima bolletta', 'type' => 'date'],
                    ['name' => 'consumo_annuo', 'label' => 'Consumo annuo stimato (kWh)', 'type' => 'number', 'required' => true],
                ]
            ],
            'indirizzo_fornitura' => [
                'label' => 'Indirizzo Fornitura',
                'fields' => [
                    ['name' => 'via', 'label' => 'Via/Piazza', 'type' => 'text', 'required' => true],
                    ['name' => 'civico', 'label' => 'Numero civico', 'type' => 'text', 'required' => true],
                    ['name' => 'interno', 'label' => 'Interno/Scala/Piano', 'type' => 'text'],
                    ['name' => 'cap', 'label' => 'CAP', 'type' => 'text', 'required' => true, 'validate' => 'cap'],
                    ['name' => 'citta', 'label' => 'Città', 'type' => 'text', 'required' => true],
                    ['name' => 'provincia', 'label' => 'Provincia', 'type' => 'text', 'required' => true],
                    ['name' => 'regione', 'label' => 'Regione', 'type' => 'text', 'required' => true],
                    ['name' => 'codice_istat', 'label' => 'Codice ISTAT comune', 'type' => 'text'],
                    ['name' => 'gps', 'label' => 'Coordinate GPS', 'type' => 'text'],
                    ['name' => 'facilita_accesso', 'label' => 'Facilità accesso contatore', 'type' => 'select', 'options' => ['Libero accesso','Necessario appuntamento','Chiavi presso terzi']],
                ]
            ],
            'offerta' => [
                'label' => 'Offerta Selezionata',
                'fields' => [
                    ['name' => 'nome_offerta', 'label' => 'Nome offerta', 'type' => 'text', 'required' => true],
                    ['name' => 'prezzo_offerta', 'label' => 'Prezzo offerta', 'type' => 'text', 'required' => true],
                    ['name' => 'risparmio_stimato', 'label' => 'Risparmio stimato', 'type' => 'text'],
                    ['name' => 'durata_contratto', 'label' => 'Durata contratto', 'type' => 'text'],
                    ['name' => 'data_inizio', 'label' => 'Data inizio fornitura', 'type' => 'date'],
                    ['name' => 'data_fine', 'label' => 'Data fine fornitura', 'type' => 'date'],
                ]
            ],
            'letture' => [
                'label' => 'Letture Contatore',
                'fields' => [
                    ['name' => 'lettura_iniziale', 'label' => 'Lettura iniziale', 'type' => 'number', 'required' => true],
                    ['name' => 'data_lettura_iniziale', 'label' => 'Data lettura iniziale', 'type' => 'date', 'required' => true],
                    ['name' => 'lettura_finale', 'label' => 'Lettura finale', 'type' => 'number'],
                    ['name' => 'data_lettura_finale', 'label' => 'Data lettura finale', 'type' => 'date'],
                ]
            ],
            'fatturazione' => [
                'label' => 'Dati Fatturazione',
                'fields' => [
                    ['name' => 'metodo_fatturazione', 'label' => 'Metodo fatturazione', 'type' => 'select', 'options' => ['Fattura elettronica','Fattura cartacea'], 'required' => true],
                    ['name' => 'email_fatturazione', 'label' => 'Email per fatturazione', 'type' => 'email'],
                    ['name' => 'telefono_fatturazione', 'label' => 'Telefono per fatturazione', 'type' => 'text'],
                ]
            ],
            'servizi_aggiuntivi' => [
                'label' => 'Servizi Aggiuntivi',
                'fields' => [
                    ['name' => 'servizio_assistenza', 'label' => 'Assistenza 24/7', 'type' => 'checkbox'],
                    ['name' => 'servizio_manutenzione', 'label' => 'Manutenzione impianto', 'type' => 'checkbox'],
                    ['name' => 'servizio_telelettura', 'label' => 'Telelettura contatore', 'type' => 'checkbox'],
                ]
            ],
            'upload' => [
                'label' => 'Upload Documenti',
                'fields' => [
                    ['name' => 'doc_identita', 'label' => 'Documento identità (fronte/retro)', 'type' => 'file', 'required' => true],
                    ['name' => 'codice_fiscale_file', 'label' => 'Codice fiscale', 'type' => 'file', 'required' => true],
                    ['name' => 'ultima_bolletta', 'label' => 'Ultima bolletta attuale', 'type' => 'file'],
                    ['name' => 'visura_camerale', 'label' => 'Visura camerale (se P.IVA)', 'type' => 'file'],
                    ['name' => 'delega', 'label' => 'Delega (se diverso intestatario)', 'type' => 'file'],
                    ['name' => 'planimetria', 'label' => 'Planimetria/Foto contatore', 'type' => 'file'],
                    ['name' => 'autorizzazioni_speciali', 'label' => 'Autorizzazioni speciali', 'type' => 'file'],
                    ['name' => 'contratto_precedente', 'label' => 'Contratto precedente', 'type' => 'file'],
                ]
            ],
            'consensi' => [
                'label' => 'Consensi e Privacy',
                'fields' => [
                    ['name' => 'consenso_trattamento', 'label' => 'Consenso trattamento dati personali', 'type' => 'checkbox', 'required' => true],
                    ['name' => 'consenso_marketing', 'label' => 'Consenso finalità marketing', 'type' => 'checkbox'],
                    ['name' => 'consenso_terze_parti', 'label' => 'Consenso comunicazioni terze parti', 'type' => 'checkbox'],
                    ['name' => 'consenso_profilazione', 'label' => 'Consenso profilazione', 'type' => 'checkbox'],
                    ['name' => 'registro_opposizioni', 'label' => 'Iscrizione registro opposizioni', 'type' => 'checkbox'],
                    ['name' => 'modalita_comunicazioni', 'label' => 'Modalità invio comunicazioni', 'type' => 'checkbox_group', 'options' => ['Email','SMS','Posta tradizionale','Telefono']],
                ]
            ],
            'note' => [
                'label' => 'Note e Comunicazioni',
                'fields' => [
                    ['name' => 'note_cliente', 'label' => 'Richieste particolari del cliente', 'type' => 'textarea'],
                    ['name' => 'note_tecnico', 'label' => 'Note per il tecnico', 'type' => 'textarea'],
                    ['name' => 'preferenze_orari', 'label' => 'Preferenze orari contatto', 'type' => 'text'],
                    ['name' => 'giorni_disponibilita', 'label' => 'Giorni disponibilità installazione', 'type' => 'text'],
                    ['name' => 'comunicazioni_urgenti', 'label' => 'Comunicazioni urgenti', 'type' => 'textarea'],
                    ['name' => 'referente_alternativo', 'label' => 'Referente alternativo per sopralluoghi', 'type' => 'text'],
                ]
            ],
            'riepilogo' => [
                'label' => 'Riepilogo e Conferma',
                'fields' => []
            ]
        ]
    ],
    'gas' => [
        'steps' => [
            'anagrafica' => [
                'label' => 'Dati Anagrafici',
                'fields' => [
                    ['name' => 'nome', 'label' => 'Nome', 'type' => 'text', 'required' => true],
                    ['name' => 'cognome', 'label' => 'Cognome', 'type' => 'text', 'required' => true],
                    ['name' => 'codice_fiscale', 'label' => 'Codice Fiscale', 'type' => 'text', 'required' => true, 'validate' => 'cf'],
                    ['name' => 'data_nascita', 'label' => 'Data di nascita', 'type' => 'date', 'required' => true],
                    ['name' => 'luogo_nascita', 'label' => 'Luogo di nascita', 'type' => 'text', 'required' => true],
                    ['name' => 'provincia_nascita', 'label' => 'Provincia', 'type' => 'text', 'required' => true],
                    ['name' => 'sesso', 'label' => 'Sesso', 'type' => 'select', 'options' => ['M','F'], 'required' => true],
                    ['name' => 'cittadinanza', 'label' => 'Cittadinanza', 'type' => 'text', 'required' => true],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                    ['name' => 'telefono', 'label' => 'Telefono fisso', 'type' => 'text'],
                    ['name' => 'cellulare', 'label' => 'Cellulare', 'type' => 'text', 'required' => true],
                ]
            ],
            'dati_fornitura_gas' => [
                'label' => 'Dati Fornitura Gas',
                'fields' => [
                    ['name' => 'tipologia_utilizzo', 'label' => 'Tipologia utilizzo', 'type' => 'checkbox_group', 'options' => ['Cottura cibi','Acqua calda sanitaria','Riscaldamento individuale','Riscaldamento centralizzato','Uso industriale/commerciale'], 'required' => true],
                    ['name' => 'pdr', 'label' => 'Codice PDR', 'type' => 'text', 'required' => true, 'validate' => 'pdr'],
                    ['name' => 'classe_contatore', 'label' => 'Classe contatore', 'type' => 'text', 'required' => true],
                    ['name' => 'matricola_contatore', 'label' => 'Matricola contatore', 'type' => 'text'],
                    ['name' => 'coefficiente_c', 'label' => 'Coefficiente correttivo (C)', 'type' => 'text'],
                    ['name' => 'pcs', 'label' => 'Potere calorifico superiore (PCS)', 'type' => 'text'],
                    ['name' => 'pressione', 'label' => 'Pressione di fornitura', 'type' => 'text'],
                    ['name' => 'categoria_uso', 'label' => 'Categoria d\'uso', 'type' => 'select', 'options' => ['C1','C2','C3','C4','C5','C6']],
                    ['name' => 'distributore_locale', 'label' => 'Distributore locale', 'type' => 'text'],
                    ['name' => 'fornitore_attuale', 'label' => 'Fornitore attuale', 'type' => 'text'],
                    ['name' => 'codice_cliente_attuale', 'label' => 'Codice cliente attuale', 'type' => 'text'],
                    ['name' => 'consumo_annuo', 'label' => 'Consumo annuo stimato (Smc)', 'type' => 'number', 'required' => true],
                ]
            ],
            'indirizzo_fornitura' => [
                'label' => 'Indirizzo Fornitura',
                'fields' => [
                    ['name' => 'via', 'label' => 'Via/Piazza', 'type' => 'text', 'required' => true],
                    ['name' => 'civico', 'label' => 'Numero civico', 'type' => 'text', 'required' => true],
                    ['name' => 'interno', 'label' => 'Interno/Scala/Piano', 'type' => 'text'],
                    ['name' => 'cap', 'label' => 'CAP', 'type' => 'text', 'required' => true, 'validate' => 'cap'],
                    ['name' => 'citta', 'label' => 'Città', 'type' => 'text', 'required' => true],
                    ['name' => 'provincia', 'label' => 'Provincia', 'type' => 'text', 'required' => true],
                    ['name' => 'regione', 'label' => 'Regione', 'type' => 'text', 'required' => true],
                    ['name' => 'codice_istat', 'label' => 'Codice ISTAT comune', 'type' => 'text'],
                    ['name' => 'gps', 'label' => 'Coordinate GPS', 'type' => 'text'],
                    ['name' => 'facilita_accesso', 'label' => 'Facilità accesso contatore', 'type' => 'select', 'options' => ['Libero accesso','Necessario appuntamento','Chiavi presso terzi']],
                ]
            ],
            'offerta' => [
                'label' => 'Offerta Selezionata',
                'fields' => [
                    ['name' => 'nome_offerta', 'label' => 'Nome offerta', 'type' => 'text', 'required' => true],
                    ['name' => 'prezzo_offerta', 'label' => 'Prezzo offerta', 'type' => 'text', 'required' => true],
                    ['name' => 'risparmio_stimato', 'label' => 'Risparmio stimato', 'type' => 'text'],
                    ['name' => 'durata_contratto', 'label' => 'Durata contratto', 'type' => 'text'],
                    ['name' => 'data_inizio', 'label' => 'Data inizio fornitura', 'type' => 'date'],
                    ['name' => 'data_fine', 'label' => 'Data fine fornitura', 'type' => 'date'],
                ]
            ],
            'letture' => [
                'label' => 'Letture Contatore',
                'fields' => [
                    ['name' => 'lettura_iniziale', 'label' => 'Lettura iniziale', 'type' => 'number', 'required' => true],
                    ['name' => 'data_lettura_iniziale', 'label' => 'Data lettura iniziale', 'type' => 'date', 'required' => true],
                    ['name' => 'lettura_finale', 'label' => 'Lettura finale', 'type' => 'number'],
                    ['name' => 'data_lettura_finale', 'label' => 'Data lettura finale', 'type' => 'date'],
                ]
            ],
            'fatturazione' => [
                'label' => 'Dati Fatturazione',
                'fields' => [
                    ['name' => 'metodo_fatturazione', 'label' => 'Metodo fatturazione', 'type' => 'select', 'options' => ['Fattura elettronica','Fattura cartacea'], 'required' => true],
                    ['name' => 'email_fatturazione', 'label' => 'Email per fatturazione', 'type' => 'email'],
                    ['name' => 'telefono_fatturazione', 'label' => 'Telefono per fatturazione', 'type' => 'text'],
                ]
            ],
            'servizi_aggiuntivi' => [
                'label' => 'Servizi Aggiuntivi',
                'fields' => [
                    ['name' => 'servizio_assistenza', 'label' => 'Assistenza 24/7', 'type' => 'checkbox'],
                    ['name' => 'servizio_manutenzione', 'label' => 'Manutenzione impianto', 'type' => 'checkbox'],
                    ['name' => 'servizio_telelettura', 'label' => 'Telelettura contatore', 'type' => 'checkbox'],
                ]
            ],
            'upload' => [
                'label' => 'Upload Documenti',
                'fields' => [
                    ['name' => 'doc_identita', 'label' => 'Documento identità (fronte/retro)', 'type' => 'file', 'required' => true],
                    ['name' => 'codice_fiscale_file', 'label' => 'Codice fiscale', 'type' => 'file', 'required' => true],
                    ['name' => 'ultima_bolletta', 'label' => 'Ultima bolletta attuale', 'type' => 'file'],
                    ['name' => 'visura_camerale', 'label' => 'Visura camerale (se P.IVA)', 'type' => 'file'],
                    ['name' => 'delega', 'label' => 'Delega (se diverso intestatario)', 'type' => 'file'],
                    ['name' => 'planimetria', 'label' => 'Planimetria/Foto contatore', 'type' => 'file'],
                    ['name' => 'autorizzazioni_speciali', 'label' => 'Autorizzazioni speciali', 'type' => 'file'],
                    ['name' => 'contratto_precedente', 'label' => 'Contratto precedente', 'type' => 'file'],
                ]
            ],
            'consensi' => [
                'label' => 'Consensi e Privacy',
                'fields' => [
                    ['name' => 'consenso_trattamento', 'label' => 'Consenso trattamento dati personali', 'type' => 'checkbox', 'required' => true],
                    ['name' => 'consenso_marketing', 'label' => 'Consenso finalità marketing', 'type' => 'checkbox'],
                    ['name' => 'consenso_terze_parti', 'label' => 'Consenso comunicazioni terze parti', 'type' => 'checkbox'],
                    ['name' => 'consenso_profilazione', 'label' => 'Consenso profilazione', 'type' => 'checkbox'],
                    ['name' => 'registro_opposizioni', 'label' => 'Iscrizione registro opposizioni', 'type' => 'checkbox'],
                    ['name' => 'modalita_comunicazioni', 'label' => 'Modalità invio comunicazioni', 'type' => 'checkbox_group', 'options' => ['Email','SMS','Posta tradizionale','Telefono']],
                ]
            ],
            'note' => [
                'label' => 'Note e Comunicazioni',
                'fields' => [
                    ['name' => 'note_cliente', 'label' => 'Richieste particolari del cliente', 'type' => 'textarea'],
                    ['name' => 'note_tecnico', 'label' => 'Note per il tecnico', 'type' => 'textarea'],
                    ['name' => 'preferenze_orari', 'label' => 'Preferenze orari contatto', 'type' => 'text'],
                    ['name' => 'giorni_disponibilita', 'label' => 'Giorni disponibilità installazione', 'type' => 'text'],
                    ['name' => 'comunicazioni_urgenti', 'label' => 'Comunicazioni urgenti', 'type' => 'textarea'],
                    ['name' => 'referente_alternativo', 'label' => 'Referente alternativo per sopralluoghi', 'type' => 'text'],
                ]
            ],
            'riepilogo' => [
                'label' => 'Riepilogo e Conferma',
                'fields' => []
            ]
        ]
    ]
];
