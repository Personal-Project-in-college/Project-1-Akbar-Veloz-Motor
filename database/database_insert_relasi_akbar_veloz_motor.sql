-- roles
INSERT INTO roles (name, created_at) VALUES ('Owner', NOW());

-- users
INSERT INTO users (name, slug, phone, address, username, password, role_id, created_at)
VALUES ('Andi Saputra', 'andi-saputra', '081234567890', 'Jl. Merdeka No.123', 'andis', 'hashed_password', 1, NOW());

-- branches
INSERT INTO branches (name, slug, address, created_at)
VALUES ('Showroom Pusat', 'showroom-pusat', 'Jl. Raya Bandung No.10', NOW());

-- vehicles
INSERT INTO vehicles (id, brand_model, type_vehicle, color, production_year, serial_number, stnk_deadline, kilometer, cc_engine, description, price, status, user_id, branch_id, created_at)
VALUES ('VH00001', 'Toyota Avanza', 'car', 'Hitam', '2019-01-01', 'SN123456789', '2025-12-31', 45000, 1500, 'Mobil keluarga bekas kondisi sangat baik.', 135000000, 'available', 1, 1, NOW());

-- vehicle_documents
INSERT INTO vehicle_documents (vehicle_id, stnk, bpkb, service_note, nota, asuransi, created_at)
VALUES ('VH00001', 'stnk.jpg', 'bpkb.jpg', 'service.jpg', 'nota.jpg', 'asuransi.jpg', NOW());

-- partners
INSERT INTO partners (name, phone, address, created_at)
VALUES ('Budi Partner', '082134567891', 'Jl. Cempaka No.55', NOW());

-- vehicle_loans
INSERT INTO vehicle_loans (partner_id, vehicle_id, user_id, loan_date, return_date, reason, status, created_at)
VALUES (1, 'VH00001', 1, '2025-04-01', '2025-04-10', 'Untuk ditawarkan ke pembeli luar kota.', 'borrowed', NOW());

-- orders
INSERT INTO orders (name, phone, address, vehicle_id, date_order, status, created_at)
VALUES ('Rina Kartika', '081298765432', 'Jl. Mawar No.77', 'VH00001', '2025-04-05', 'test_driver', NOW());

-- test_drivers
INSERT INTO test_drivers (order_id, user_id, result_note, created_at)
VALUES (1, 1, 'Pengujian berjalan lancar dan kendaraan nyaman.', NOW());

-- transactions
INSERT INTO transactions (order_id, partner_id, user_id, grandtotal, payment, `change`, paymentproof, created_at)
VALUES (1, 1, 1, 135000000, 140000000, 5000000, 'bukti_pembayaran.jpg', NOW());

-- vehicle_photos
INSERT INTO vehicle_photos (vehicle_id, photo_path, created_at)
VALUES ('VH00001', 'uploads/vehicles/vh00001_1.jpg', NOW());
