ALTER TABLE other_validations ADD jenis_alat INT(5) NOT NULL AFTER manufacturing;

ALTER TABLE `inhouse_validations` ADD `jenis_alat` INT(5) NOT NULL AFTER `machines`;

ALTER TABLE `stamping` ADD `quotation_attachment` INT(5) NULL AFTER `quotation_date`;

ALTER TABLE `stamping` ADD `invoice_attachment` INT(5) NULL AFTER `invoice_no`;

-- 04/02/2025 --

ALTER TABLE `companies` ADD `stamp_prefer_validator` INT(5) NOT NULL AFTER `tarikh_dikeluarkan`;

-- 10/02/2025 --

ALTER TABLE `size` CHANGE `size` `size` VARCHAR(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL;

-- 11/02/2025 --

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `email_setup` (
  `id` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `email_cc` varchar(255) DEFAULT NULL,
  `email_title` varchar(255) DEFAULT NULL,
  `email_body` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `email_setup`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `email_setup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

-- 27/02/2025 --
ALTER TABLE `stamping_ext` ADD `nilai_jangkaan_maksimum` VARCHAR(10) NULL AFTER `jenama_other`, ADD `bahan_pembuat` VARCHAR(30) NULL AFTER `nilai_jangkaan_maksimum`;

ALTER TABLE `stamping_ext` ADD `bahan_pembuat_other` VARCHAR(50) NULL AFTER `bahan_pembuat`;

DELETE FROM `alat`;

INSERT INTO `alat` (`id`, `alat`, `deleted`) VALUES
(1, 'ATK', '0'),
(2, 'ATP', '0'),
(3, 'ATM', '1'),
(4, 'ATS', '0'),
(5, 'ATN', '0'),
(6, 'ATE', '0'),
(7, 'BTU', '0'),
(8, 'ATN', '1'),
(9, 'ATL', '0'),
(10, 'ATP-AUTO MACHINE', '0'),
(11, 'BAP', '0'),
(12, 'SIA', '0'),
(13, 'SIC', '0'),
(14, 'SLL', '0'),
(15, 'SMM', '0'),
(16, 'SMP', '0'),
(17, 'ATS (H)', '0'),
(18, 'ATN (G)', '0'),
(19, 'PROCAL - (SA)', '0'),
(20, 'SIRIM - (SA)', '0'),
(21, 'MSPK - (SA)', '0'),
(22, 'UNKNOWN', '1'),
(23, 'ATP (MOTORCAR)', '0'),
(24, 'test', '1'),
(25, 'test2', '1'),
(26, 'BTU - (BOX)', '0');

ALTER TABLE `stamping_ext` ADD `btu_box_info` TEXT NULL AFTER `batu_ujian_lain`;

-- 06/03/2025 --
ALTER TABLE `stamping` ADD `labour_charge` VARCHAR(10) NULL AFTER `subtotal_amount`, ADD `stampfee_labourcharge` VARCHAR(10) NULL AFTER `labour_charge`, ADD `int_round_up` VARCHAR(10) NULL AFTER `stampfee_labourcharge`, ADD `total_charges` VARCHAR(10) NULL AFTER `int_round_up`;

-- 10/06/2025 -- 
ALTER TABLE `stamping` MODIFY `serial_no` VARCHAR(50);

-- 19/07/2025 --
ALTER TABLE `stamping_ext` ADD `btu_info` TEXT NULL AFTER `batu_ujian_lain`;

UPDATE stamping_ext
SET btu_info = JSON_ARRAY(
  JSON_OBJECT(
    'no', '1',
    'batuUjian', batu_ujian,
    'batuUjianLain', batu_ujian_lain,
    'penandaanBatuUjian', penandaan_batu_ujian,
    'batuDaftarLama', '',
    'batuDaftarBaru', ''
  )
)
WHERE batu_ujian IS NOT NULL;

-- 09/08/2025 --
ALTER TABLE `stamping` ADD `subtotal_sst_amt` VARCHAR(10) NULL AFTER `sst`, ADD `rebate` VARCHAR(10) NULL AFTER `subtotal_sst_amt`, ADD `rebate_amount` VARCHAR(10) NULL AFTER `rebate`;

ALTER TABLE `stamping` ADD `validator_invoice` VARCHAR(50) NULL AFTER `log`;

INSERT INTO `roles` (`id`, `role_code`, `role_name`, `module`, `deleted`) VALUES (NULL, 'ACCOUNT', 'ACCOUNT', NULL, '0');

-- 10/08/2025 --
UPDATE stamping SET total_amount = CAST(COALESCE(NULLIF(total_amount, 'NaN'), '0') AS DECIMAL(15,2)), sst = CAST(COALESCE(NULLIF(sst, 'NaN'), '0') AS DECIMAL(15,2));

UPDATE stamping SET subtotal_sst_amt = CAST(total_amount + sst AS DECIMAL(15,2));

-- 12/08/2025 --
ALTER TABLE `stamping` ADD `copy` VARCHAR(3) NOT NULL DEFAULT 'N' AFTER `duplicate`;

-- 17/08/2025 --
ALTER TABLE users 
ADD COLUMN email VARCHAR(100) NULL,
ADD COLUMN reset_token VARCHAR(255) NULL,
ADD COLUMN reset_expires DATETIME NULL;

-- 24/08/2025 --
CREATE TABLE `company_branches` (
  `id` int(11) NOT NULL,
  `branch_code` varchar(50) DEFAULT NULL,
  `branch_name` varchar(100) NOT NULL,
  `map_url` text DEFAULT NULL,
  `address_line_1` text NOT NULL,
  `address_line_2` text DEFAULT NULL,
  `address_line_3` text DEFAULT NULL,
  `address_line_4` text DEFAULT NULL,
  `pic` varchar(50) DEFAULT NULL,
  `pic_contact` varchar(30) DEFAULT NULL,
  `office_no` varchar(30) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `company_branches` ADD PRIMARY KEY (`id`);

ALTER TABLE `company_branches` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users` ADD `branch` INT(11) NULL AFTER `role_code`;

ALTER TABLE `stamping` ADD `company_branch` INT(11) NULL AFTER `type`;

ALTER TABLE `other_validations` ADD `company_branch` INT(11) NULL AFTER `type`;

ALTER TABLE `inhouse_validations` ADD `company_branch` INT(11) NULL AFTER `type`;

ALTER TABLE `stamping` ADD `borang_e_date` DATETIME NULL AFTER `borang_e`;

ALTER TABLE `inhouse_validations` ADD `calibrator2` INT(5) NULL AFTER `calibrator`, ADD `calibrator3` INT(5) NULL AFTER `calibrator2`;

ALTER TABLE `stamping` ADD `notification_period` VARCHAR(10) NULL AFTER `invoice_attachment`;

ALTER TABLE `stamping_ext` ADD `weighbridge_location` VARCHAR(100) NULL AFTER `stamp_id`, ADD `weighbridge_name` VARCHAR(100) NULL AFTER `weighbridge_location`, ADD `weighbridge_serial_no` VARCHAR(100) NULL AFTER `weighbridge_name`;

ALTER TABLE `stamping` ADD `machine_name` VARCHAR(100) NULL AFTER `jenis_alat`, ADD `machine_location` VARCHAR(100) NULL AFTER `machine_name`, ADD `machine_serial_no` VARCHAR(100) NULL AFTER `machine_location`;

ALTER TABLE `stamping_ext` DROP `weighbridge_location`, DROP `weighbridge_name`, DROP `weighbridge_serial_no`;

ALTER TABLE `stamping` ADD `assignTo2` INT(5) NULL AFTER `assignTo`, ADD `assignTo3` INT(5) NULL AFTER `assignTo2`;

ALTER TABLE `stamping` ADD `seal_no_lama` VARCHAR(100) NULL AFTER `no_daftar_baru`, ADD `seal_no_baru` VARCHAR(100) NULL AFTER `seal_no_lama`, ADD `pegawai_contact` VARCHAR(30) NULL AFTER `seal_no_baru`;

ALTER TABLE `stamping` ADD `cert_no` VARCHAR(50) NULL AFTER `include_cert`;

CREATE TABLE `machine_names` (
  `id` int(11) NOT NULL,
  `machine_no` varchar(50) NOT NULL,
  `machine_name` varchar(100) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `machine_names` ADD PRIMARY KEY (`id`);
  
ALTER TABLE `machine_names` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `validator_officers` (
  `id` int(11) NOT NULL,
  `officer_name` varchar(100) NOT NULL,
  `officer_contact` varchar(30) DEFAULT NULL,
  `officer_position` varchar(30) DEFAULT NULL,
  `officer_company` int(5) DEFAULT NULL,
  `officer_cawangan` int(5) DEFAULT NULL,
  `deleted` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `validator_officers` ADD PRIMARY KEY (`id`);

ALTER TABLE `validator_officers` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `stamping` CHANGE `other_reason` `other_reason` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;

ALTER TABLE `stamping` ADD `validator_lama` INT(5) NULL AFTER `serial_no`;

-- 31/08/2025 --
ALTER TABLE `stamping` ADD `machine_area` VARCHAR(100) NULL AFTER `machine_location`;

INSERT INTO company_branches (branch_name, address_line_1, pic, pic_contact, email)
SELECT 
    c.name, 
    c.address, 
    c.person_incharge, 
    c.contact_no, 
    c.email
FROM companies c
WHERE NOT EXISTS (
    SELECT 1 
    FROM company_branches cb 
    WHERE UPPER(cb.branch_name COLLATE utf8mb4_unicode_ci) = UPPER(c.name COLLATE utf8mb4_unicode_ci) 
      AND cb.deleted = 0
);

ALTER TABLE `stamping` ADD `internal_remark` TEXT NULL AFTER `remarks`;

UPDATE `stamping` SET company_branch = 1 WHERE company_branch IS NULL AND deleted = 0;

UPDATE `other_validations` SET company_branch = 1 WHERE company_branch IS NULL AND deleted = 0;

UPDATE `inhouse_validations` SET company_branch = 1 WHERE company_branch IS NULL AND deleted = 0;

-- 09/09/2025 --
ALTER TABLE `stamping` ADD `ownership_status` VARCHAR(10) NULL AFTER `assignTo3`, ADD `rental_attachment` INT(5) NULL AFTER `ownership_status`;

ALTER TABLE `stamping` ADD `invoice_payment_type` VARCHAR(10) NULL AFTER `invoice_attachment`, ADD `invoice_payment_ref` TEXT NULL AFTER `invoice_payment_type`;

CREATE TABLE `stamping_status_log` (
  `id` int(11) NOT NULL,
  `stamp_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `status_remark` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `occurred_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `stamping_status_log` ADD PRIMARY KEY (`id`);
  
ALTER TABLE `stamping_status_log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `miscellaneous` (code, description, value)
VALUES
('stamping_status', 'Quotation Issued / Follow-Up', 1),
('stamping_status', 'Quotation Chop Sign Back By Customer', 2),
('stamping_status', 'Purchase Order (PO) Received', 3),
('stamping_status', 'Pre-Stamping Completed', 4),
('stamping_status', 'Stamping Date Confirmed / Customer Notified', 5),
('stamping_status', 'Stamping Completed', 6),
('stamping_status', 'SPMT Payment Completed', 7),
('stamping_status', 'Metrology Department Payment Completed', 8);

ALTER TABLE `stamping` ADD `restamping` VARCHAR(3) NULL DEFAULT 'N' AFTER `copy`;

-- 14/09/2025 --
INSERT INTO `roles` (`role_code`, `role_name`, `module`, `deleted`) VALUES ('ADMIN/ACCOUNT', 'ADMIN/ACCOUNT', NULL, '0');

-- 16/09/2025 --
UPDATE `company_branches` SET branch_code = 'HQ' WHERE id = 1;

-- 03/10/2025 --
ALTER TABLE `customers` ADD `address5` TEXT NULL AFTER `address4`;

ALTER TABLE `company_branches` ADD `address_line_5` TEXT NULL AFTER `address_line_4`;

ALTER TABLE `branches` ADD `address5` TEXT NULL AFTER `address4`;

ALTER TABLE `dealer` ADD `address5` TEXT NULL AFTER `address4`;

ALTER TABLE `reseller_branches` ADD `address5` TEXT NULL AFTER `address4`;

ALTER TABLE `inhouse_validations` CHANGE `last_calibration_date` `last_calibration_date` DATE NULL;

ALTER TABLE `inhouse_validations` CHANGE `tests` `tests` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `other_validations` CHANGE `calibrations` `calibrations` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;
