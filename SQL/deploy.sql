ALTER TABLE other_validations ADD jenis_alat INT(5) NOT NULL AFTER manufacturing;

ALTER TABLE `inhouse_validations` ADD `jenis_alat` INT(5) NOT NULL AFTER `machines`;

ALTER TABLE `stamping` ADD `quotation_attachment` INT(5) NULL AFTER `quotation_date`;

ALTER TABLE `stamping` ADD `invoice_attachment` INT(5) NULL AFTER `invoice_no`;

-- 04/02/2025 --

ALTER TABLE `companies` ADD `stamp_prefer_validator` INT(5) NOT NULL AFTER `tarikh_dikeluarkan`;

-- 10/02/2025 --

ALTER TABLE `size` CHANGE `size` `size` VARCHAR(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL;
