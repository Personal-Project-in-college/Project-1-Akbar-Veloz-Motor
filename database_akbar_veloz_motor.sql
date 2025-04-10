-- TABEL 1: roles
CREATE TABLE roles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(155) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- TABEL 2: users
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(155) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(12) UNIQUE NOT NULL,
    address LONGTEXT NOT NULL,
    username VARCHAR(155) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id BIGINT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- TABEL 3: branches
CREATE TABLE branches (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(155) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    address LONGTEXT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- TABEL 4: vehicles
CREATE TABLE vehicles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(155) NOT NULL,
    type_vehicle ENUM('motorcyle', 'car') NOT NULL,
    color VARCHAR(155) NOT NULL,
    production_year DATE NOT NULL,
    stnk_deadline DATE NOT NULL,
    kilometer INT(11) NOT NULL,
    `description` LONGTEXT NOT NULL,
    price INT(11) NOT NULL,
   `condition` ENUM('new', 'second') NOT NULL DEFAULT 'second',
    user_id BIGINT NOT NULL,
    branch_id BIGINT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- TABEL 5: vehicle_documents
CREATE TABLE vehicle_documents (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    vehicle_id BIGINT NOT NULL,
    stnk VARCHAR(255) NOT NULL,
    bpkb VARCHAR(255) NOT NULL,
    service_note VARCHAR(255) NOT NULL,
    nota VARCHAR(255) NOT NULL,
    asuransi VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- TABEL 6: partners
CREATE TABLE partners (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(155) NOT NULL,
    phone VARCHAR(12) UNIQUE NOT NULL,
    address LONGTEXT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- TABEL 7: vehicle_loans
CREATE TABLE vehicle_loans (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    partner_id BIGINT NOT NULL,
    vehicle_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    loan_date DATE NOT NULL,
    return_date DATE NOT NULL,
    reason LONGTEXT NOT NULL,
    status ENUM('borrowed', 'returned') NOT NULL DEFAULT 'borrowed',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- TABEL 8: customers
CREATE TABLE customers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(155) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(12) UNIQUE NOT NULL,
    address LONGTEXT NOT NULL,
    status ENUM('paid', 'unpaid') NOT NULL DEFAULT 'unpaid',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- TABEL 9: carts
CREATE TABLE carts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    customer_id BIGINT NOT NULL,
    vehicle_id BIGINT NOT NULL,
    subtotal_price INT(11) NOT NULL,
    user_id BIGINT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- TABEL 10: checkouts
CREATE TABLE checkouts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    customer_id BIGINT NULL,
    partner_id BIGINT NULL,
    user_id BIGINT NOT NULL,
    grandtotal INT(11) NOT NULL,
    payment INT(11) NOT NULL,
    `change` INT(11) NOT NULL,
    paymentproof VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- TABEL 11: vehicle_photos
CREATE TABLE vehicle_photos (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    vehicle_id BIGINT NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);