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