-- Script seed dữ liệu thiết bị (~100 thiết bị cho 5 phòng)
-- Mỗi phòng có đầy đủ thiết bị tiện ích ký túc xá

USE dormitory_management;

-- Seed dữ liệu thiết bị cho 5 phòng có sẵn

-- PHÒNG A101 (room_id = 1) - 20 thiết bị
INSERT INTO equipment (room_id, equipment_name, equipment_type, brand, model, serial_number, purchase_date, warranty_expiry, status) VALUES
(1, 'Điều hòa inverter 2 chiều', 'Air Conditioner', 'Daikin', 'FTKY25TAVMA', 'DK2021A101001', '2021-06-15', '2024-06-15', 'working'),
(1, 'Quạt trần treo', 'Fan', 'Panasonic', 'F-56C8', 'PC2021A101002', '2021-06-15', '2024-06-15', 'working'),
(1, 'Quạt đứng', 'Fan', 'National', 'NF-4291TY', 'NF2021A101003', '2021-06-15', '2024-06-15', 'working'),
(1, 'Tủ lạnh mini 90L', 'Refrigerator', 'LG', 'GN-B255HL', 'LG2021A101004', '2021-06-15', '2024-06-15', 'working'),
(1, 'Máy nước nóng 50L', 'Water Heater', 'Ariston', 'AN2 R 50B', 'AR2021A101005', '2021-06-15', '2024-06-15', 'working'),
(1, 'Máy lọc nước RO', 'Water Filter', 'Kangaroo', 'KG100A1', 'KG2021A101006', '2021-06-15', '2024-06-15', 'working'),
(1, 'Giường tầng 2 tầng', 'Bed', 'Ikea', 'TUEN LURVY', 'IK2021A101007', '2021-06-15', '2024-06-15', 'working'),
(1, 'Giường tầng 2 tầng', 'Bed', 'Ikea', 'TUEN LURVY', 'IK2021A101008', '2021-06-15', '2024-06-15', 'working'),
(1, 'Bàn học 4 chỗ', 'Table', 'Nội Thất Xanh', 'NTX-4C20', 'NTX2021A101009', '2021-06-15', '2024-06-15', 'working'),
(1, 'Ghế học x 4', 'Chair', 'Ikea', 'JULES', 'IK2021A101010', '2021-06-15', '2024-06-15', 'working'),
(1, 'Tủ quần áo 4 cửa', 'Cabinet', 'Ikea', 'PAX', 'IK2021A101011', '2021-06-15', '2024-06-15', 'working'),
(1, 'Tủ áo cá nhân x 4', 'Cabinet', 'Nội Thất Việt', 'NTV-TC4', 'NTV2021A101012', '2021-06-15', '2024-06-15', 'working'),
(1, 'Giá kệ đồ x 4', 'Shelf', 'Ikea', 'BAGGEBO', 'IK2021A101013', '2021-06-15', '2024-06-15', 'working'),
(1, 'Bàn uống nước', 'Table', 'Nội Thất Việt', 'NTV-BUN15', 'NTV2021A101014', '2021-06-15', '2024-06-15', 'working'),
(1, 'Ổ cắm điện an toàn x 8', 'Outlet', 'Schneider', 'Zen USB', 'SC2021A101015', '2021-06-15', '2024-06-15', 'working'),
(1, 'Đèn LED trần x 4', 'Light', 'Philips', 'Ultinon', 'PH2021A101016', '2021-06-15', '2024-06-15', 'working'),
(1, 'Đèn bàn học x 4', 'Light', 'Panasonic', 'SQ-LD070-K', 'PC2021A101017', '2021-06-15', '2024-06-15', 'working'),
(1, 'Rèm cửa sổ', 'Curtain', 'Nội Thất Đẹp', 'RT-A101', 'NTD2021A101018', '2021-06-15', '2024-06-15', 'maintenance'),
(1, 'Gương phòng x 2', 'Mirror', 'Ikea', 'SALMER', 'IK2021A101019', '2021-06-15', '2024-06-15', 'working'),
(1, 'Chậu rửa và vòi sen', 'Sink', 'Inax', 'LFK-T83', 'IX2021A101020', '2021-06-15', '2024-06-15', 'working');

-- PHÒNG A102 (room_id = 2) - 18 thiết bị
INSERT INTO equipment (room_id, equipment_name, equipment_type, brand, model, serial_number, purchase_date, warranty_expiry, status) VALUES
(2, 'Điều hòa inverter 2 chiều', 'Air Conditioner', 'Mitsubishi', 'MSZ-FH25VA-SG', 'MS2021A102001', '2021-07-01', '2024-07-01', 'working'),
(2, 'Quạt trần', 'Fan', 'Asia', 'AFC-60', 'AS2021A102002', '2021-07-01', '2024-07-01', 'working'),
(2, 'Quạt bàn x 2', 'Fan', 'Midea', 'MD-30', 'MD2021A102003', '2021-07-01', '2024-07-01', 'working'),
(2, 'Tủ lạnh mini', 'Refrigerator', 'Panasonic', 'NR-BE296SV', 'PC2021A102004', '2021-07-01', '2024-07-01', 'working'),
(2, 'Máy nước nóng', 'Water Heater', 'Ferroli', 'Sime 50L', 'FR2021A102005', '2021-07-01', '2024-07-01', 'broken'),
(2, 'Giường tầng', 'Bed', 'Ikea', 'TUEN LURVY', 'IK2021A102006', '2021-07-01', '2024-07-01', 'working'),
(2, 'Giường tầng', 'Bed', 'Ikea', 'TUEN LURVY', 'IK2021A102007', '2021-07-01', '2024-07-01', 'working'),
(2, 'Bàn học dài', 'Table', 'Ikea', 'MICKE', 'IK2021A102008', '2021-07-01', '2024-07-01', 'working'),
(2, 'Ghế học x 4', 'Chair', 'Ikea', 'JULES', 'IK2021A102009', '2021-07-01', '2024-07-01', 'working'),
(2, 'Tủ cá nhân x 4', 'Cabinet', 'Ikea', 'PAX', 'IK2021A102010', '2021-07-01', '2024-07-01', 'working'),
(2, 'Kệ sách 4 tầng', 'Shelf', 'Ikea', 'BAGGEBO', 'IK2021A102011', '2021-07-01', '2024-07-01', 'working'),
(2, 'Ổ cắm điện x 8', 'Outlet', 'Legrand', 'Onese', 'LG2021A102012', '2021-07-01', '2024-07-01', 'working'),
(2, 'Đèn LED trần', 'Light', 'Philips', 'Ultinon', 'PH2021A102013', '2021-07-01', '2024-07-01', 'working'),
(2, 'Đèn bàn học x 4', 'Light', 'Panasonic', 'SQ-LD070', 'PC2021A102014', '2021-07-01', '2024-07-01', 'working'),
(2, 'Rèm cửa', 'Curtain', 'Nội Thất', 'RT-A102', 'NT2021A102015', '2021-07-01', '2024-07-01', 'working'),
(2, 'Gương phòng', 'Mirror', 'Ikea', 'SALMER', 'IK2021A102016', '2021-07-01', '2024-07-01', 'working'),
(2, 'Chậu rửa', 'Sink', 'Inax', 'LFK-T83', 'IX2021A102017', '2021-07-01', '2024-07-01', 'working'),
(2, 'Máy lọc không khí', 'Air Purifier', 'Xiaomi', 'Mi Air 3', 'XM2021A102018', '2021-07-01', '2024-07-01', 'maintenance');

-- PHÒNG A201 (room_id = 3) - 20 thiết bị
INSERT INTO equipment (room_id, equipment_name, equipment_type, brand, model, serial_number, purchase_date, warranty_expiry, status) VALUES
(3, 'Điều hòa inverter', 'Air Conditioner', 'Daikin', 'FTKY25TAVMA', 'DK2021A201001', '2021-08-15', '2024-08-15', 'working'),
(3, 'Quạt trần', 'Fan', 'Panasonic', 'F-56C8', 'PC2021A201002', '2021-08-15', '2024-08-15', 'working'),
(3, 'Tủ lạnh', 'Refrigerator', 'Samsung', 'RB29FSRNDSA', 'SS2021A201003', '2021-08-15', '2024-08-15', 'working'),
(3, 'Máy giặt mini', 'Washing Machine', 'LG', 'F2514RTGC', 'LG2021A201004', '2021-08-15', '2024-08-15', 'working'),
(3, 'Máy sấy tóc', 'Dryer', 'Panasonic', 'EH-ND27', 'PC2021A201005', '2021-08-15', '2024-08-15', 'working'),
(3, 'Máy nước nóng', 'Water Heater', 'Ariston', 'AN2 R 50B', 'AR2021A201006', '2021-08-15', '2024-08-15', 'working'),
(3, 'Máy lọc nước', 'Water Filter', 'Kangaroo', 'KG100A1', 'KG2021A201007', '2021-08-15', '2024-08-15', 'working'),
(3, 'Giường tầng x 2', 'Bed', 'Ikea', 'TUEN LURVY', 'IK2021A201008', '2021-08-15', '2024-08-15', 'working'),
(3, 'Giường tầng x 2', 'Bed', 'Ikea', 'TUEN LURVY', 'IK2021A201009', '2021-08-15', '2024-08-15', 'working'),
(3, 'Nệm x 4', 'Mattress', 'Dunlopillo', 'Della M', 'DL2021A201010', '2021-08-15', '2024-08-15', 'working'),
(3, 'Bàn học 4 chỗ', 'Table', 'Nội Thất Xanh', 'NTX-4C20', 'NTX2021A201011', '2021-08-15', '2024-08-15', 'working'),
(3, 'Ghế học x 4', 'Chair', 'Ikea', 'JULES', 'IK2021A201012', '2021-08-15', '2024-08-15', 'working'),
(3, 'Tủ quần áo lớn', 'Cabinet', 'Ikea', 'PAX', 'IK2021A201013', '2021-08-15', '2024-08-15', 'working'),
(3, 'Tủ cá nhân x 4', 'Cabinet', 'Ikea', 'TROFAST', 'IK2021A201014', '2021-08-15', '2024-08-15', 'broken'),
(3, 'Kệ sách', 'Shelf', 'Ikea', 'BAGGEBO', 'IK2021A201015', '2021-08-15', '2024-08-15', 'working'),
(3, 'Ổ cắm điện x 8', 'Outlet', 'Schneider', 'Zen USB', 'SC2021A201016', '2021-08-15', '2024-08-15', 'working'),
(3, 'Đèn LED trần x 4', 'Light', 'Philips', 'Ultinon', 'PH2021A201017', '2021-08-15', '2024-08-15', 'working'),
(3, 'Đèn bàn học x 4', 'Light', 'Panasonic', 'SQ-LD070', 'PC2021A201018', '2021-08-15', '2024-08-15', 'working'),
(3, 'Gương x 2', 'Mirror', 'Ikea', 'SALMER', 'IK2021A201019', '2021-08-15', '2024-08-15', 'working'),
(3, 'Chậu rửa và vòi', 'Sink', 'Inax', 'LFK-T83', 'IX2021A201020', '2021-08-15', '2024-08-15', 'working');

-- PHÒNG B101 (room_id = 4) - 20 thiết bị
INSERT INTO equipment (room_id, equipment_name, equipment_type, brand, model, serial_number, purchase_date, warranty_expiry, status) VALUES
(4, 'Điều hòa inverter', 'Air Conditioner', 'Gree', 'GWC09KF-K', 'GR2021B101001', '2021-09-01', '2024-09-01', 'working'),
(4, 'Quạt trần', 'Fan', 'Asia', 'AFC-60', 'AS2021B101002', '2021-09-01', '2024-09-01', 'working'),
(4, 'Quạt bàn x 2', 'Fan', 'Midea', 'MD-30', 'MD2021B101003', '2021-09-01', '2024-09-01', 'working'),
(4, 'Tủ lạnh 130L', 'Refrigerator', 'Panasonic', 'NR-BE296SV', 'PC2021B101004', '2021-09-01', '2024-09-01', 'working'),
(4, 'Máy giặt', 'Washing Machine', 'Samsung', 'WW90T534DAN', 'SS2021B101005', '2021-09-01', '2024-09-01', 'working'),
(4, 'Máy nước nóng', 'Water Heater', 'Ferroli', 'Sime 50L', 'FR2021B101006', '2021-09-01', '2024-09-01', 'working'),
(4, 'Máy lọc nước', 'Water Filter', 'Kangaroo', 'KG100A2', 'KG2021B101007', '2021-09-01', '2024-09-01', 'working'),
(4, 'Giường tầng x 2', 'Bed', 'Nội Thất Việt', 'NTV-GT20', 'NTV2021B101008', '2021-09-01', '2024-09-01', 'working'),
(4, 'Giường tầng x 2', 'Bed', 'Nội Thất Việt', 'NTV-GT20', 'NTV2021B101009', '2021-09-01', '2024-09-01', 'working'),
(4, 'Nệm x 4', 'Mattress', 'Dunlopillo', 'Comfort S', 'DL2021B101010', '2021-09-01', '2024-09-01', 'broken'),
(4, 'Bàn học', 'Table', 'Ikea', 'MICKE', 'IK2021B101011', '2021-09-01', '2024-09-01', 'working'),
(4, 'Ghế học x 4', 'Chair', 'Ikea', 'JULES', 'IK2021B101012', '2021-09-01', '2024-09-01', 'working'),
(4, 'Tủ áo lớn', 'Cabinet', 'Ikea', 'PAX', 'IK2021B101013', '2021-09-01', '2024-09-01', 'working'),
(4, 'Tủ cá nhân x 4', 'Cabinet', 'Ikea', 'TROFAST', 'IK2021B101014', '2021-09-01', '2024-09-01', 'working'),
(4, 'Kệ sách 4 tầng', 'Shelf', 'Ikea', 'BAGGEBO', 'IK2021B101015', '2021-09-01', '2024-09-01', 'working'),
(4, 'Ổ cắm x 8', 'Outlet', 'Legrand', 'Onese', 'LG2021B101016', '2021-09-01', '2024-09-01', 'working'),
(4, 'Đèn LED trần x 4', 'Light', 'Philips', 'Ultinon', 'PH2021B101017', '2021-09-01', '2024-09-01', 'working'),
(4, 'Đèn bàn học x 4', 'Light', 'Panasonic', 'SQ-LD070', 'PC2021B101018', '2021-09-01', '2024-09-01', 'working'),
(4, 'Rèm cửa', 'Curtain', 'Nội Thất', 'RT-B101', 'NT2021B101019', '2021-09-01', '2024-09-01', 'working'),
(4, 'Gương và chậu', 'Mirror', 'Ikea', 'SALMER', 'IK2021B101020', '2021-09-01', '2024-09-01', 'maintenance');

-- PHÒNG B102 (room_id = 5) - 20 thiết bị  
INSERT INTO equipment (room_id, equipment_name, equipment_type, brand, model, serial_number, purchase_date, warranty_expiry, status) VALUES
(5, 'Điều hòa inverter', 'Air Conditioner', 'Electrolux', 'EACS12WIA01', 'EL2021B102001', '2021-10-01', '2024-10-01', 'working'),
(5, 'Quạt trần', 'Fan', 'Panasonic', 'F-56C8', 'PC2021B102002', '2021-10-01', '2024-10-01', 'working'),
(5, 'Quạt đứng', 'Fan', 'National', 'NF-4291TY', 'NF2021B102003', '2021-10-01', '2024-10-01', 'working'),
(5, 'Tủ lạnh mini', 'Refrigerator', 'LG', 'GN-B255HL', 'LG2021B102004', '2021-10-01', '2024-10-01', 'working'),
(5, 'Máy giặt', 'Washing Machine', 'Electrolux', 'EWF1025BMGM', 'EL2021B102005', '2021-10-01', '2024-10-01', 'working'),
(5, 'Máy sấy tóc', 'Dryer', 'Philips', 'HP8100', 'PH2021B102006', '2021-10-01', '2024-10-01', 'working'),
(5, 'Máy nước nóng', 'Water Heater', 'Ariston', 'AN2 R 50B', 'AR2021B102007', '2021-10-01', '2024-10-01', 'broken'),
(5, 'Máy lọc nước', 'Water Filter', 'Kangaroo', 'KG100A3', 'KG2021B102008', '2021-10-01', '2024-10-01', 'working'),
(5, 'Máy lọc không khí', 'Air Purifier', 'Xiaomi', 'Mi Air 3', 'XM2021B102009', '2021-10-01', '2024-10-01', 'working'),
(5, 'Giường tầng x 2', 'Bed', 'Ikea', 'TUEN LURVY', 'IK2021B102010', '2021-10-01', '2024-10-01', 'working'),
(5, 'Giường tầng x 2', 'Bed', 'Ikea', 'TUEN LURVY', 'IK2021B102011', '2021-10-01', '2024-10-01', 'working'),
(5, 'Nệm cao su x 4', 'Mattress', 'Dunlopillo', 'Latex S', 'DL2021B102012', '2021-10-01', '2024-10-01', 'working'),
(5, 'Bàn học 4 chỗ', 'Table', 'Nội Thất Xanh', 'NTX-4C20', 'NTX2021B102013', '2021-10-01', '2024-10-01', 'working'),
(5, 'Ghế học x 4', 'Chair', 'Ikea', 'JULES', 'IK2021B102014', '2021-10-01', '2024-10-01', 'working'),
(5, 'Tủ áo lớn', 'Cabinet', 'Ikea', 'PAX', 'IK2021B102015', '2021-10-01', '2024-10-01', 'working'),
(5, 'Tủ cá nhân x 4', 'Cabinet', 'Ikea', 'TROFAST', 'IK2021B102016', '2021-10-01', '2024-10-01', 'working'),
(5, 'Kệ sách 4 tầng', 'Shelf', 'Ikea', 'BAGGEBO', 'IK2021B102017', '2021-10-01', '2024-10-01', 'working'),
(5, 'Ổ cắm x 8', 'Outlet', 'Schneider', 'Zen USB', 'SC2021B102018', '2021-10-01', '2024-10-01', 'working'),
(5, 'Đèn LED trần x 4', 'Light', 'Philips', 'Ultinon', 'PH2021B102019', '2021-10-01', '2024-10-01', 'maintenance'),
(5, 'Đèn bàn và gương', 'Light', 'Ikea', 'SALMER', 'IK2021B102020', '2021-10-01', '2024-10-01', 'working');

-- Kiểm tra số lượng thiết bị đã insert
SELECT COUNT(*) as total_equipment FROM equipment;

-- Thống kê thiết bị theo trạng thái
SELECT status, COUNT(*) as count 
FROM equipment 
GROUP BY status
ORDER BY count DESC;

-- Thống kê thiết bị theo loại
SELECT equipment_type, COUNT(*) as count 
FROM equipment 
GROUP BY equipment_type
ORDER BY count DESC;

-- Thống kê thiết bị theo phòng
SELECT room_id, COUNT(*) as total_equipment
FROM equipment
GROUP BY room_id
ORDER BY room_id;

