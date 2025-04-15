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

-- Relasi TABEL orders ke vehicles
ALTER TABLE orders
ADD CONSTRAINT fk_orders_vehicles
FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
ON DELETE CASCADE;

-- Relasi TABEL test_drivers ke orders
ALTER TABLE test_drivers
ADD CONSTRAINT fk_test_drivers_orders
FOREIGN KEY (order_id) REFERENCES orders(id)
ON DELETE CASCADE;

-- Relasi TABEL test_drivers ke users
ALTER TABLE test_drivers
ADD CONSTRAINT fk_test_drivers_users
FOREIGN KEY (user_id) REFERENCES users(id)
ON DELETE CASCADE;

-- Relasi TABEL transactions ke orders
ALTER TABLE transactions
ADD CONSTRAINT fk_transactions_orders
FOREIGN KEY (order_id) REFERENCES orders(id)
ON DELETE CASCADE;

-- Relasi TABEL transactions ke partners
ALTER TABLE transactions
ADD CONSTRAINT fk_transactions_partners
FOREIGN KEY (partner_id) REFERENCES partners(id)
ON DELETE CASCADE;

-- Relasi TABEL transactions ke users
ALTER TABLE transactions
ADD CONSTRAINT fk_transactions_users
FOREIGN KEY (user_id) REFERENCES users(id)
ON DELETE CASCADE;

-- Relasi TABEL vehicle_photos ke vehicles
ALTER TABLE vehicle_photos
ADD CONSTRAINT fk_vehicle_photos_vehicle
FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
ON DELETE CASCADE;
