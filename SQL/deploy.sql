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
