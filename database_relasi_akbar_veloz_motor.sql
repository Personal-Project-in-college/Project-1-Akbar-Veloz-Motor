-- Relasi TABEL users ke roles
ALTER TABLE users
ADD CONSTRAINT fk_users_role
FOREIGN KEY (role_id) REFERENCES roles(id)
ON DELETE CASCADE;

-- Relasi TABEL vehicles ke users
ALTER TABLE vehicles
ADD CONSTRAINT fk_vehicles_user
FOREIGN KEY (user_id) REFERENCES users(id)
ON DELETE CASCADE;

-- Relasi TABEL vehicles ke branches
ALTER TABLE vehicles
ADD CONSTRAINT fk_vehicles_branch
FOREIGN KEY (branch_id) REFERENCES branches(id)
ON DELETE CASCADE;

-- Relasi TABEL vehicle_documents ke vehicles
ALTER TABLE vehicle_documents
ADD CONSTRAINT fk_vehicle_documents_vehicle
FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
ON DELETE CASCADE;

-- Relasi TABEL vehicle_loans ke partners
ALTER TABLE vehicle_loans
ADD CONSTRAINT fk_vehicle_loans_partner
FOREIGN KEY (partner_id) REFERENCES partners(id)
ON DELETE CASCADE;

-- Relasi TABEL vehicle_loans ke vehicles
ALTER TABLE vehicle_loans
ADD CONSTRAINT fk_vehicle_loans_vehicle
FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
ON DELETE CASCADE;

-- Relasi TABEL vehicle_loans ke users
ALTER TABLE vehicle_loans
ADD CONSTRAINT fk_vehicle_loans_user
FOREIGN KEY (user_id) REFERENCES users(id)
ON DELETE CASCADE;

-- Relasi TABEL carts ke customers
ALTER TABLE carts
ADD CONSTRAINT fk_carts_customer
FOREIGN KEY (customer_id) REFERENCES customers(id)
ON DELETE CASCADE;

-- Relasi TABEL carts ke vehicles
ALTER TABLE carts
ADD CONSTRAINT fk_carts_vehicle
FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
ON DELETE CASCADE;

-- Relasi TABEL carts ke users
ALTER TABLE carts
ADD CONSTRAINT fk_carts_user
FOREIGN KEY (user_id) REFERENCES users(id)
ON DELETE CASCADE;

-- Relasi TABEL checkouts ke customers
ALTER TABLE checkouts
ADD CONSTRAINT fk_checkouts_customer
FOREIGN KEY (customer_id) REFERENCES customers(id)
ON DELETE CASCADE;

-- Relasi TABEL checkouts ke partners
ALTER TABLE checkouts
ADD CONSTRAINT fk_checkouts_partner
FOREIGN KEY (partner_id) REFERENCES partners(id)
ON DELETE CASCADE;

-- Relasi TABEL checkouts ke users
ALTER TABLE checkouts
ADD CONSTRAINT fk_checkouts_user
FOREIGN KEY (user_id) REFERENCES users(id)
ON DELETE CASCADE;

-- Relasi TABEL vehicle_photos ke vehicles
ALTER TABLE vehicle_photos
ADD CONSTRAINT fk_vehicle_photos_vehicle
FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
ON DELETE CASCADE;
