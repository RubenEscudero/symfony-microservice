INSERT INTO promotion (id, name, type, adjustment, criteria) VALUES
(1, 'Black Friday half price sale', 'date_range_multiplier', 0.5, '{\"to\": \"2023-12-01\", \"from\": \"2023-08-01\"}'),
(2, 'Voucher OU812', 'fixed_price_voucher', 100, '{\"code\": \"OU812\"}');