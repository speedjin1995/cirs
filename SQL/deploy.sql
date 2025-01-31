ALTER TABLE other_validations ADD jenis_alat INT(5) NOT NULL AFTER manufacturing;

ALTER TABLE `inhouse_validations` ADD `jenis_alat` INT(5) NOT NULL AFTER `machines`;

ALTER TABLE `stamping` ADD `quotation_attachment` INT(5) NULL AFTER `quotation_date`;

ALTER TABLE `stamping` ADD `invoice_attachment` INT(5) NULL AFTER `invoice_no`;
