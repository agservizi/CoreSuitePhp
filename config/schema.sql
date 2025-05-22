-- CoreSuite Database Schema

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    mfa_secret VARCHAR(64),
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    surname VARCHAR(100) NOT NULL,
    fiscal_code VARCHAR(16) NOT NULL,
    phone VARCHAR(30),
    email VARCHAR(255),
    created_at DATETIME NOT NULL,
    date_of_birth DATE,
    place_of_birth VARCHAR(100),
    province_of_birth VARCHAR(10),
    gender CHAR(1),
    citizenship VARCHAR(50),
    document_type VARCHAR(50),
    document_number VARCHAR(50),
    document_expiry DATE,
    mobile VARCHAR(30),
    pec VARCHAR(255),
    notes TEXT
);

CREATE TABLE IF NOT EXISTS providers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM(
        'telefonia',
        'luce',
        'gas',
        'energia',
        'altro'
    ) NOT NULL,
    logo VARCHAR(255),
    form_config JSON
);

CREATE TABLE IF NOT EXISTS contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    provider INT NOT NULL,
    type ENUM(
        'telefonia',
        'luce',
        'gas',
        'energia',
        'altro'
    ) NOT NULL,
    status VARCHAR(50) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    customer_id INT,
    extra_data JSON,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (customer_id) REFERENCES customers (id),
    FOREIGN KEY (provider) REFERENCES providers (id)
);

CREATE TABLE IF NOT EXISTS addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    type ENUM(
        'residenza',
        'sede_legale',
        'attivazione',
        'installazione',
        'fornitura'
    ) NOT NULL,
    street VARCHAR(255) NOT NULL,
    civic VARCHAR(20),
    internal VARCHAR(20),
    postal_code VARCHAR(10) NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(10) NOT NULL,
    region VARCHAR(50),
    state VARCHAR(50),
    gps_lat DECIMAL(10, 8),
    gps_lng DECIMAL(11, 8),
    istat_code VARCHAR(10),
    FOREIGN KEY (contract_id) REFERENCES contracts (id)
);

CREATE TABLE IF NOT EXISTS attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    upload_date DATETIME NOT NULL,
    type VARCHAR(50),
    FOREIGN KEY (contract_id) REFERENCES contracts (id)
);

CREATE TABLE IF NOT EXISTS consents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    privacy TINYINT(1) NOT NULL,
    marketing TINYINT(1) NOT NULL DEFAULT 0,
    third_parties TINYINT(1) NOT NULL DEFAULT 0,
    profiling TINYINT(1) NOT NULL DEFAULT 0,
    registry_optout TINYINT(1) NOT NULL DEFAULT 0,
    communication_modes VARCHAR(100),
    signed_at DATETIME,
    signature VARCHAR(255),
    FOREIGN KEY (contract_id) REFERENCES contracts (id)
);

CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    note_type VARCHAR(50),
    content TEXT,
    created_by INT,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (contract_id) REFERENCES contracts (id),
    FOREIGN KEY (created_by) REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50),
    entity VARCHAR(50),
    entity_id INT,
    details TEXT,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS contract_drafts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM(
        'telefonia',
        'luce',
        'gas',
        'energia',
        'altro'
    ) NOT NULL,
    data JSON NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_from INT NOT NULL,
    provider_to INT NOT NULL,
    migration_code VARCHAR(50) NOT NULL,
    contract_id INT NOT NULL,
    FOREIGN KEY (provider_from) REFERENCES providers (id),
    FOREIGN KEY (provider_to) REFERENCES providers (id),
    FOREIGN KEY (contract_id) REFERENCES contracts (id)
);