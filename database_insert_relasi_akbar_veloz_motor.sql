-- INSERT INTO roles
INSERT INTO roles (name, created_at) VALUES ('admin', '2025-04-10 09:41:17');

-- INSERT INTO users
INSERT INTO users (name, slug, phone, address, username, password, role_id, created_at) VALUES ('Farhan Ginting', 'farhan-ginting', '081234567890', 'Jl. Raya Subang No. 1', 'farhan', 'hashed_password', 1, '2025-04-10 09:41:17');

-- INSERT INTO branches
INSERT INTO branches (name, slug, address, created_at) VALUES ('Pusat', 'pusat', 'Jl. Veteran No. 9, Subang', '2025-04-10 09:41:17');

-- INSERT INTO vehicles
INSERT INTO vehicles (name, type_vehicle, color, production_year, stnk_deadline, kilometer, description, price, `condition`, user_id, branch_id, created_at) VALUES ('Toyota Avanza', 'car', 'Silver', '2021-01-01', '2025-01-01', 20000, 'Mobil keluarga irit dan nyaman.', 150000000, 'second', 1, 1, '2025-04-10 09:41:17');

-- INSERT INTO vehicle_documents
INSERT INTO vehicle_documents (vehicle_id, stnk, bpkb, service_note, nota, asuransi, created_at) VALUES (1, 'stnk.jpg', 'bpkb.jpg', 'service_note.pdf', 'nota.pdf', 'asuransi.jpg', '2025-04-10 09:41:17');

-- INSERT INTO partners
INSERT INTO partners (name, phone, address, created_at) VALUES ('Budi Motor', '089876543210', 'Jl. Merdeka No. 2', '2025-04-10 09:41:17');

-- INSERT INTO vehicle_loans
INSERT INTO vehicle_loans (partner_id, vehicle_id, user_id, loan_date, return_date, reason, status, created_at) VALUES (1, 1, 1, '2025-04-01', '2025-04-10', 'Untuk ditampilkan di showroom cabang.', 'borrowed', '2025-04-10 09:41:17');

-- INSERT INTO customers
INSERT INTO customers (name, slug, phone, address, status, created_at) VALUES ('Andi Saputra', 'andi-saputra', '087765432198', 'Jl. Anggrek No. 7', 'unpaid', '2025-04-10 09:41:17');

-- INSERT INTO carts
INSERT INTO carts (customer_id, vehicle_id, subtotal_price, user_id, created_at) VALUES (1, 1, 150000000, 1, '2025-04-10 09:41:17');

-- INSERT INTO checkouts
INSERT INTO checkouts (customer_id, partner_id, user_id, grandtotal, payment, `change`, paymentproof, created_at) VALUES (1, NULL, 1, 150000000, 150000000, 0, 'bukti_transfer.jpg', '2025-04-10 09:41:17');

-- INSERT INTO vehicle_photos
INSERT INTO vehicle_photos (vehicle_id, photo_path, created_at) VALUES (1, 'avanza_silver.jpg', '2025-04-10 09:41:17');