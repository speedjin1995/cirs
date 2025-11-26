<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);

session_start();

$uid = $_SESSION['userID'];

if (
	$_POST['validatorInvoice'] != null || $_POST['unitPrice'] != null || $_POST['certPrice'] != null || $_POST['totalAmount'] != null
	|| $_POST['sst'] != null || $_POST['subAmountSst'] != null || $_POST['rebate'] != null || $_POST['rebateAmount'] != null
) {

	$unitPrice = '0.00';
	$certPrice = '0.00';
	$totalPrice = '0.00';
	$sst = '0.00';
	$subtoalPrice = '0.00';
	$labourCharge = '0.00';
	$stampLabourCharge = '0.00';
	$roundUp = '0.00';
	$totalCharge = '0.00';

	$logs = array();

	if (isset($_POST['validatorInvoice']) && $_POST['validatorInvoice'] != null && $_POST['validatorInvoice'] != "") {
		$validatorInvoice = $_POST['validatorInvoice'];
	}

	if (isset($_POST['unitPrice']) && $_POST['unitPrice'] != null && $_POST['unitPrice'] != "") {
		$unitPrice = $_POST['unitPrice'];
	}

	if (isset($_POST['certPrice']) && $_POST['certPrice'] != null && $_POST['certPrice'] != "") {
		$certPrice = $_POST['certPrice'];
	}

	if (isset($_POST['totalAmount']) && $_POST['totalAmount'] != null && $_POST['totalAmount'] != "") {
		$totalPrice = $_POST['totalAmount'];
	}

	if (isset($_POST['sst']) && $_POST['sst'] != null && $_POST['sst'] != "") {
		$sst = $_POST['sst'];
	}

	if (isset($_POST['subAmountSst']) && $_POST['subAmountSst'] != null && $_POST['subAmountSst'] != "") {
		$subAmountSst = $_POST['subAmountSst'];
	}

	if (isset($_POST['rebate']) && $_POST['rebate'] != null && $_POST['rebate'] != "") {
		$rebate = $_POST['rebate'];
	}

	if (isset($_POST['rebateAmount']) && $_POST['rebateAmount'] != null && $_POST['rebateAmount'] != "") {
		$rebateAmount = $_POST['rebateAmount'];
	}

	if (isset($_POST['subAmount']) && $_POST['subAmount'] != null && $_POST['subAmount'] != "") {
		$subtotalPrice = $_POST['subAmount'];
	}

	if (isset($_POST['labourCharge']) && $_POST['labourCharge'] != null && $_POST['labourCharge'] != "") {
		$labourCharge = $_POST['labourCharge'];
	}

	if (isset($_POST['stampLabourCharge']) && $_POST['stampLabourCharge'] != null && $_POST['stampLabourCharge'] != "") {
		$stampLabourCharge = $_POST['stampLabourCharge'];
	}

	if (isset($_POST['roundUp']) && $_POST['roundUp'] != null && $_POST['roundUp'] != "") {
		$roundUp = $_POST['roundUp'];
	}

	if (isset($_POST['totalCharge']) && $_POST['totalCharge'] != null && $_POST['totalCharge'] != "") {
		$totalCharge = $_POST['totalCharge'];
	}



	if (isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != '') {
		//Updated datetime
		$currentDateTime = date('Y-m-d H:i:s');

		if ($update_stmt = $db->prepare("UPDATE stamping SET validator_invoice=?, unit_price=?, cert_price=?, total_amount=?, sst=?, subtotal_sst_amt=?, rebate=?, rebate_amount=?, subtotal_amount=?, log=?, updated_datetime=?, labour_charge=?, stampfee_labourcharge=?, int_round_up=?, total_charges=? WHERE id=?")) {
			$data = json_encode($logs);
			$update_stmt->bind_param(
				'sssssssssssssssi',
				$validatorInvoice,
				$unitPrice,
				$certPrice,
				$totalPrice,
				$sst,
				$subAmountSst,
				$rebate,
				$rebateAmount,
				$subtotalPrice,
				$data,
				$currentDateTime,
				$labourCharge,
				$stampLabourCharge,
				$roundUp,
				$totalCharge,
				$_POST['id']
			);

			// Execute the prepared query.
			if (!$update_stmt->execute()) {
				echo json_encode(
					array(
						"status" => "failed",
						"message" => $update_stmt->error
					)
				);
			} else {
				$stampingId = $_POST['id'];

				$stampExtQuery = "SELECT * FROM stamping_ext WHERE stamp_id = $stampingId";
				$stampExtDetail = mysqli_query($db, $stampExtQuery);
				$stampExtRow = mysqli_fetch_assoc($stampExtDetail);

				if ($stampExtRow == NULL) {
					if (
						$insert_stmt = $db->prepare("INSERT INTO stamping_ext (stamp_id) 
					VALUES (?)")
					) {
						$insert_stmt->bind_param('s', $stampingId);
						$insert_stmt->execute();
						$insert_stmt->close();
					}
				}

				// UPDATE Stamping System Log
				if (
					$insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id) 
				VALUES (?, ?, ?)")
				) {
					$action = "UPDATE";
					$insert_stmt3->bind_param('sss', $action, $uid, $_POST['id']);
					$insert_stmt3->execute();
					$insert_stmt3->close();
				}

				// Logic to save stamping status timeline
				$stmt = $db->prepare("SELECT quotation_no, quotation_attachment, purchase_no, serial_no, stamping_date, invoice_no, invoice_payment_type, invoice_payment_ref, validator_invoice FROM stamping WHERE id=?");
				$stmt->bind_param('i', $stampingId);
				$stmt->execute();
				$stmt->bind_result($quotation_no, $quotation_attachment, $purchase_no, $serial_no, $stamping_date, $invoice_no, $invoice_payment_type, $invoice_payment_ref, $validator_invoice);
				$stmt->fetch();
				$stmt->close();

				// Statuses and their conditions
				$statuses = [
					1 => !empty($quotation_no),
					2 => !empty($quotation_attachment),
					3 => !empty($purchase_no),
					4 => !empty($serial_no),
					5 => !empty($stamping_date),
					6 => !empty($invoice_no),
					7 => (!empty($invoice_payment_type) && !empty($invoice_payment_ref)),
					8 => !empty($validator_invoice),
				];

				// Status descriptions (should match your miscellaneous table)
				$status_desc = [];
				if ($status_stmt = $db->prepare("SELECT * FROM miscellaneous WHERE code='stamping_status' AND deleted = 0")) {
					$status_stmt->execute();
					$result = $status_stmt->get_result();
					while ($row = $result->fetch_assoc()) {
						$status_desc[$row['value']] = $row['description'];
					}
					$status_stmt->close();
				}

				// Insert log for each status reached, in order
				$created_by = $_SESSION['userID'];
				$now = date('Y-m-d H:i:s');
				foreach ($statuses as $val => $reached) {
					if ($reached) {
						// Check if already logged
						$check = $db->prepare("SELECT id FROM stamping_status_log WHERE stamp_id=? AND status=?");
						$check->bind_param('is', $stampingId, $status_desc[$val]);
						$check->execute();
						$check->store_result();
						if ($check->num_rows == 0) {
							$insert = $db->prepare("INSERT INTO stamping_status_log (stamp_id, status, created_by, occurred_at) VALUES (?, ?, ?, ?)");
							$insert->bind_param('isss', $stampingId, $status_desc[$val], $created_by, $now);
							$insert->execute();
							$insert->close();
						}
						$check->close();
					}
				}

				echo json_encode(
					array(
						"status" => "success",
						"message" => "Updated Successfully!!"
					)
				);
			}

			$update_stmt->close();
		} else {
			$update_stmt->close();
			echo json_encode(
				array(
					"status" => "failed",
					"message" => "Error when creating query"
				)
			);
		}

	} else {
		if (
			$insert_stmt = $db->prepare("INSERT INTO stamping (type, company_branch, dealer, dealer_branch, customer_type, customers, brand, machine_type, model, make_in, capacity, serial_no, assignTo, assignTo2, assignTo3, ownership_status, validator_lama,
		validate_by, cawangan, jenis_alat, machine_name, machine_location, machine_area, machine_serial_no, trade, no_daftar_lama, no_daftar_baru, pin_keselamatan, siri_keselamatan, include_cert, borang_d, borang_e, borang_e_date, invoice_no, invoice_payment_type, invoice_payment_ref, notification_period, cash_bill, stamping_date, last_year_stamping_date, due_date, pic, customer_pic, 
		quotation_no, quotation_date, purchase_no, purchase_date, remarks, internal_remark, validator_invoice, unit_price, cert_price, total_amount, sst, subtotal_sst_amt, rebate, rebate_amount, subtotal_amount, log, products, stamping_type, branch, labour_charge, stampfee_labourcharge, int_round_up, total_charges, seal_no_lama, seal_no_baru, pegawai_contact, cert_no) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
		) {
			$data = json_encode($logs);
			$insert_stmt->bind_param(
				'ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss',
				$type,
				$companyBranch,
				$dealer,
				$reseller_branch,
				$customerType,
				$customer,
				$brand,
				$machineType,
				$model,
				$makeIn,
				$capacity,
				$serial,
				$assignTo,
				$assignTo2,
				$assignTo3,
				$ownershipStatus,
				$validatorlama,
				$validator,
				$cawangan,
				$jenisAlat,
				$machineName,
				$machineLocation,
				$machineArea,
				$machineSerialNo,
				$trade,
				$noDaftarLama,
				$noDaftarBaru,
				$pinKeselamatan,
				$siriKeselamatan,
				$includeCert,
				$borangD,
				$borangE,
				$borangEDate,
				$invoice,
				$invoicePaymentType,
				$invoicePayRef,
				$notificationPeriod,
				$cashBill,
				$stampDate,
				$lastYearStampDate,
				$dueDate,
				$uid,
				$pic,
				$quotation,
				$quotationDate,
				$poNo,
				$poDate,
				$remark,
				$internalRemark,
				$validatorInvoice,
				$unitPrice,
				$certPrice,
				$totalPrice,
				$sst,
				$subAmountSst,
				$rebate,
				$rebateAmount,
				$subtotalPrice,
				$data,
				$product,
				$newRenew,
				$branch,
				$labourCharge,
				$stampLabourCharge,
				$roundUp,
				$totalCharge,
				$sealNoLama,
				$sealNoBaru,
				$pegawaiContact,
				$certNo
			);

			// Execute the prepared query.
			if (!$insert_stmt->execute()) {
				echo json_encode(
					array(
						"status" => "failed",
						"message" => $insert_stmt->error
					)
				);
			} else {
				$stamp_id = $insert_stmt->insert_id;

				$uploadQuotationAttachment = null;
				$uploadInvoiceAttachment = null;
				$uploadRentalAttachment = null;
				if (isset($_FILES['uploadQuotationAttachment']) && $_FILES['uploadQuotationAttachment'] != null && $_FILES['uploadQuotationAttachment'] != "") {
					$uploadQuotationAttachment = $_FILES['uploadQuotationAttachment'];

					$ds = DIRECTORY_SEPARATOR;
					$storeFolder = '../uploads/stamping';
					if ($uploadQuotationAttachment['error'] === 0) {
						# Delete Existing File 
						// if(isset($_POST['quotationFilePath']) && $_POST['quotationFilePath']!=null && $_POST['quotationFilePath']!=""){
						// 	$quotationFilePath = $_POST['quotationFilePath'];
						// 	if (file_exists($quotationFilePath)) {
						// 		unlink($quotationFilePath);
						// 	}
						// }

						$timestamp = time();
						$uploadDir = $storeFolder . $ds; // Directory to store uploaded files
						$folderDir = dirname(__DIR__, 2) . '/' . $uploadDir;
						// Check if folder exists, if not, create it with correct permissions
						if (!is_dir($folderDir)) {
							mkdir($folderDir, 0777, true); // true allows recursive directory creation
						}

						$filename = $timestamp . '_' . basename($_FILES['uploadQuotationAttachment']['name']);
						$uploadFile = dirname(__DIR__, 2) . '/' . $uploadDir . $filename;
						$tempFile = $_FILES['uploadQuotationAttachment']['tmp_name'];

						// Move the uploaded file to the target directory
						if (move_uploaded_file($tempFile, $uploadFile)) {
							$dbDir = "../uploads/stamping/";
							$quotationFilePath = $dbDir . $filename;
							// Update certificate data in the database
							if ($stmt3 = $db->prepare("INSERT INTO files (filename, filepath) VALUES (?, ?)")) {
								$stmt3->bind_param('ss', $filename, $quotationFilePath);
								$stmt3->execute();
								$fid = $stmt3->insert_id;
								$stmt3->close();

								if ($stmtf = $db->prepare("UPDATE stamping SET quotation_attachment=? WHERE id=?")) {
									$stmtf->bind_param('ss', $fid, $stamp_id);
									$stmtf->execute();
									$stmtf->close();
								}
							}
						}
					}
				}

				if (isset($_FILES['uploadInvoiceAttachment']) && $_FILES['uploadInvoiceAttachment'] != null && $_FILES['uploadInvoiceAttachment'] != "") {
					$uploadInvoiceAttachment = $_FILES['uploadInvoiceAttachment'];

					$ds = DIRECTORY_SEPARATOR;
					$storeFolder = '../uploads/stamping';
					if ($uploadInvoiceAttachment['error'] === 0) {
						# Delete Existing File 
						// if(isset($_POST['InvoiceFilePath']) && $_POST['InvoiceFilePath']!=null && $_POST['InvoiceFilePath']!=""){
						// 	$InvoiceFilePath = $_POST['InvoiceFilePath'];
						// 	if (file_exists($InvoiceFilePath)) {
						// 		unlink($InvoiceFilePath);
						// 	}
						// }

						$timestamp = time();
						$uploadDir = $storeFolder . $ds; // Directory to store uploaded files
						$folderDir = dirname(__DIR__, 2) . '/' . $uploadDir;
						// Check if folder exists, if not, create it with correct permissions
						if (!is_dir($folderDir)) {
							mkdir($folderDir, 0777, true); // true allows recursive directory creation
						}

						$filename = $timestamp . '_' . basename($_FILES['uploadInvoiceAttachment']['name']);
						$uploadFile = dirname(__DIR__, 2) . '/' . $uploadDir . $filename;
						$tempFile = $_FILES['uploadInvoiceAttachment']['tmp_name'];

						// Move the uploaded file to the target directory
						if (move_uploaded_file($tempFile, $uploadFile)) {
							$invoiceFilePath = $uploadDir . $filename;
							// Update certificate data in the database
							if ($stmt3 = $db->prepare("INSERT INTO files (filename, filepath) VALUES (?, ?)")) {
								$stmt3->bind_param('ss', $filename, $invoiceFilePath);
								$stmt3->execute();
								$fid = $stmt3->insert_id;
								$stmt3->close();

								if ($stmtf = $db->prepare("UPDATE stamping SET invoice_attachment=? WHERE id=?")) {
									$stmtf->bind_param('ss', $fid, $stamp_id);
									$stmtf->execute();
									$stmtf->close();
								}
							}
						}
					}
				}

				if (isset($_FILES['uploadRentalAttachment']) && $_FILES['uploadRentalAttachment'] != null && $_FILES['uploadRentalAttachment'] != "") {
					$uploadRentalAttachment = $_FILES['uploadRentalAttachment'];

					$ds = DIRECTORY_SEPARATOR;
					$storeFolder = '../uploads/stamping';
					if ($uploadRentalAttachment['error'] === 0) {
						# Delete Existing File 
						// if(isset($_POST['InvoiceFilePath']) && $_POST['InvoiceFilePath']!=null && $_POST['InvoiceFilePath']!=""){
						// 	$InvoiceFilePath = $_POST['InvoiceFilePath'];
						// 	if (file_exists($InvoiceFilePath)) {
						// 		unlink($InvoiceFilePath);
						// 	}
						// }

						$timestamp = time();
						$uploadDir = $storeFolder . $ds; // Directory to store uploaded files
						$folderDir = dirname(__DIR__, 2) . '/' . $uploadDir;
						// Check if folder exists, if not, create it with correct permissions
						if (!is_dir($folderDir)) {
							mkdir($folderDir, 0777, true); // true allows recursive directory creation
						}

						$filename = $timestamp . '_' . basename($_FILES['uploadRentalAttachment']['name']);
						$uploadFile = dirname(__DIR__, 2) . '/' . $uploadDir . $filename;
						$tempFile = $_FILES['uploadRentalAttachment']['tmp_name'];

						// Move the uploaded file to the target directory
						if (move_uploaded_file($tempFile, $uploadFile)) {
							$rentalFilePath = $uploadDir . $filename;
							// Update certificate data in the database
							if ($stmt3 = $db->prepare("INSERT INTO files (filename, filepath) VALUES (?, ?)")) {
								$stmt3->bind_param('ss', $filename, $rentalFilePath);
								$stmt3->execute();
								$fid = $stmt3->insert_id;
								$stmt3->close();

								if ($stmtf = $db->prepare("UPDATE stamping SET rental_attachment=? WHERE id=?")) {
									$stmtf->bind_param('ss', $fid, $stamp_id);
									$stmtf->execute();
									$stmtf->close();
								}
							}
						}
					}
				}

				// For ATK Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'ATK')) {
					$penentusan_semula = null;
					$kelulusan_mspk = null;
					$no_kelulusan = null;
					$indicator_serial = null;
					$platform_country = null;
					$platform_type = null;
					$size = null;
					$jenis_pelantar = null;
					$others = null;
					$load_cell_country = null;
					$load_cell_no = null;
					$load_cells_info = [];

					$no = $_POST['no'] ?? [];
					$loadCells = $_POST['loadCells'] ?? [];
					$loadCellBrand = $_POST['loadCellBrand'] ?? [];
					$loadCellModel = $_POST['loadCellModel'] ?? [];
					$loadCellCapacity = $_POST['loadCellCapacity'] ?? [];
					$loadCellSerial = $_POST['loadCellSerial'] ?? [];

					if (isset($no) && $no != null && count($no) > 0) {
						for ($i = 0; $i < count($no); $i++) {
							$load_cells_info[] = array(
								"no" => $no[$i],
								"loadCells" => $loadCells[$i],
								"loadCellBrand" => $loadCellBrand[$i],
								"loadCellModel" => $loadCellModel[$i],
								"loadCellCapacity" => $loadCellCapacity[$i],
								"loadCellSerial" => $loadCellSerial[$i]
							);
						}
					}

					if (isset($_POST['penentusanBaru']) && $_POST['penentusanBaru'] != null && $_POST['penentusanBaru'] != "") {
						$penentusan_baru = $_POST['penentusanBaru'];
					}

					if (isset($_POST['penentusanSemula']) && $_POST['penentusanSemula'] != null && $_POST['penentusanSemula'] != "") {
						$penentusan_semula = $_POST['penentusanSemula'];
					}

					if (isset($_POST['kelulusanMSPK']) && $_POST['kelulusanMSPK'] != null && $_POST['kelulusanMSPK'] != "") {
						$kelulusan_mspk = $_POST['kelulusanMSPK'];
					}

					if (isset($_POST['noMSPK']) && $_POST['noMSPK'] != null && $_POST['noMSPK'] != "") {
						$no_kelulusan = $_POST['noMSPK'];
					}

					if (isset($_POST['noSerialIndicator']) && $_POST['noSerialIndicator'] != null && $_POST['noSerialIndicator'] != "") {
						$indicator_serial = $_POST['noSerialIndicator'];
					}

					if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
						$platform_country = $_POST['platformCountry'];
					}

					if (isset($_POST['platformType']) && $_POST['platformType'] != null && $_POST['platformType'] != "") {
						$platform_type = $_POST['platformType'];
					}

					if (isset($_POST['size']) && $_POST['size'] != null && $_POST['size'] != "" && $_POST['size'] != '-') {
						$size = $_POST['size'];
					}

					if (isset($_POST['jenisPelantar']) && $_POST['jenisPelantar'] != null && $_POST['jenisPelantar'] != "") {
						$jenis_pelantar = $_POST['jenisPelantar'];
					}

					if (isset($_POST['others']) && $_POST['others'] != null && $_POST['others'] != "") {
						$others = $_POST['others'];
					}

					if (isset($_POST['loadCellCountry']) && $_POST['loadCellCountry'] != null && $_POST['loadCellCountry'] != "") {
						$load_cell_country = $_POST['loadCellCountry'];
					}

					if (isset($_POST['noOfLoadCell']) && $_POST['noOfLoadCell'] != null && $_POST['noOfLoadCell'] != "") {
						$load_cell_no = $_POST['noOfLoadCell'];
					}

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, penentusan_baru, penentusan_semula, kelulusan_mspk, no_kelulusan, indicator_serial, platform_country, 
						platform_type, size, jenis_pelantar, other_info, load_cell_country, load_cell_no, load_cells_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
					) {
						$data = json_encode($load_cells_info);
						$insert_stmt2->bind_param(
							'ssssssssssssss',
							$stamp_id,
							$penentusan_baru,
							$penentusan_semula,
							$kelulusan_mspk,
							$no_kelulusan,
							$indicator_serial,
							$platform_country,
							$platform_type,
							$size,
							$jenis_pelantar,
							$others,
							$load_cell_country,
							$load_cell_no,
							$data
						);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For ATS - H Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'ATS (H)')) {
					$platform_country = null;

					if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
						$platform_country = $_POST['platformCountry'];
					}

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country) 
					VALUES (?, ?)")
					) {
						$insert_stmt2->bind_param('ss', $stamp_id, $platform_country);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For ATS Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'ATS') && !str_contains($jenisAlatName, 'ATS (H)')) {
					$platform_country = null;

					if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
						$platform_country = $_POST['platformCountry'];
					}

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country) 
					VALUES (?, ?)")
					) {
						$insert_stmt2->bind_param('ss', $stamp_id, $platform_country);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For ATP (MOTORCAR) Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'ATP (MOTORCAR)')) {
					$platform_country = null;
					$jenis_penunjuk = null;

					if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
						$platform_country = $_POST['platformCountry'];
					}

					if (isset($_POST['steelyard']) && $_POST['steelyard'] != null && $_POST['steelyard'] != "") {
						$steelyard = $_POST['steelyard'];
					}

					if (isset($_POST['bilanganKaunterpois']) && $_POST['bilanganKaunterpois'] != null && $_POST['bilanganKaunterpois'] != "") {
						$bilanganKaunterpois = $_POST['bilanganKaunterpois'];
					}

					$nilais = [
						[
							"no" => 1,
							"nilai" => $_POST['nilai1'] ?? null,
						],
						[
							"no" => 2,
							"nilai" => $_POST['nilai2'] ?? null,
						],
						[
							"no" => 3,
							"nilai" => $_POST['nilai3'] ?? null,
						],
						[
							"no" => 4,
							"nilai" => $_POST['nilai4'] ?? null,
						],
						[
							"no" => 5,
							"nilai" => $_POST['nilai5'] ?? null,
						],
						[
							"no" => 6,
							"nilai" => $_POST['nilai6'] ?? null,
						]
					];

					$nilaiString = json_encode($nilais, JSON_PRETTY_PRINT);

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, steelyard, bilangan_kaunterpois, nilais) 
					VALUES (?, ?, ?, ?, ?)")
					) {
						$insert_stmt2->bind_param('sssss', $stamp_id, $platform_country, $steelyard, $bilanganKaunterpois, $nilaiString);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For AUTO_PACKER Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'ATP-AUTO MACHINE')) {
					$platform_country = null;
					$jenis_penunjuk = null;

					if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
						$platform_country = $_POST['platformCountry'];
					}

					if (isset($_POST['jenis_penunjuk']) && $_POST['jenis_penunjuk'] != null && $_POST['jenis_penunjuk'] != "") {
						$jenis_penunjuk = $_POST['jenis_penunjuk'];
					}

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, jenis_penunjuk) 
					VALUES (?, ?, ?)")
					) {
						$insert_stmt2->bind_param('sss', $stamp_id, $platform_country, $jenis_penunjuk);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
					// $platform_country = null;

					// if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
					// 	$platform_country = $_POST['platformCountry'];
					// }

					// $nilais = [
					// 	[
					// 		"no" => 1,
					// 		"nilai" => $_POST['nilai1'] ?? null,
					// 	],
					// 	[
					// 		"no" => 2,
					// 		"nilai" => $_POST['nilai2'] ?? null,
					// 	],
					// 	[
					// 		"no" => 3,
					// 		"nilai" => $_POST['nilai3'] ?? null,
					// 	],
					// 	[
					// 		"no" => 4,
					// 		"nilai" => $_POST['nilai4'] ?? null,
					// 	],
					// 	[
					// 		"no" => 5,
					// 		"nilai" => $_POST['nilai5'] ?? null,
					// 	],
					// 	[
					// 		"no" => 6,
					// 		"nilai" => $_POST['nilai6'] ?? null,
					// 	]
					// ];

					// $nilaistring = json_encode($nilais, JSON_PRETTY_PRINT);

					// if ($insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, nilais) 
					// VALUES (?, ?, ?)")){
					// 	$insert_stmt2->bind_param('sss', $stamp_id, $platform_country, $nilaistring);
					// 	$insert_stmt2->execute();
					// 	$insert_stmt2->close();
					// }
				}

				// For ATP Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'ATP') && !str_contains($jenisAlatName, 'ATP (MOTORCAR)') && !str_contains($jenisAlatName, 'ATP-AUTO MACHINE')) {
					$platform_country = null;
					$jenis_penunjuk = null;

					if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
						$platform_country = $_POST['platformCountry'];
					}

					if (isset($_POST['jenis_penunjuk']) && $_POST['jenis_penunjuk'] != null && $_POST['jenis_penunjuk'] != "") {
						$jenis_penunjuk = $_POST['jenis_penunjuk'];
					}

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, jenis_penunjuk) 
					VALUES (?, ?, ?)")
					) {
						$insert_stmt2->bind_param('sss', $stamp_id, $platform_country, $jenis_penunjuk);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For ATN Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'ATN')) {
					$platform_country = null;
					$alat_type = null;
					$bentuk_dulang = null;

					if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
						$platform_country = $_POST['platformCountry'];
					}

					if (isset($_POST['alat_type']) && $_POST['alat_type'] != null && $_POST['alat_type'] != "") {
						$alat_type = $_POST['alat_type'];
					}

					if (isset($_POST['bentuk_dulang']) && $_POST['bentuk_dulang'] != null && $_POST['bentuk_dulang'] != "") {
						$bentuk_dulang = $_POST['bentuk_dulang'];
					}

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, alat_type, bentuk_dulang) 
					VALUES (?, ?, ?, ?)")
					) {
						$insert_stmt2->bind_param('ssss', $stamp_id, $platform_country, $alat_type, $bentuk_dulang);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For ATE Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'ATE')) {
					$platform_country = null;
					$class = null;

					if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
						$platform_country = $_POST['platformCountry'];
					}

					if (isset($_POST['class']) && $_POST['class'] != null && $_POST['class'] != "") {
						$class = $_POST['class'];
					}

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, class) 
					VALUES (?, ?, ?)")
					) {
						$insert_stmt2->bind_param('sss', $stamp_id, $platform_country, $class);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For SLL Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'SLL')) {
					$platform_country = null;
					$alat_type = null;

					if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
						$platform_country = $_POST['platformCountry'];
					}

					if (isset($_POST['alat_type']) && $_POST['alat_type'] != null && $_POST['alat_type'] != "") {
						$alat_type = $_POST['alat_type'];
					}

					$questions = [
						[
							"no" => 1,
							"answer" => $_POST['question1'] ?? null,
						],
						[
							"no" => 2,
							"answer" => $_POST['question2'] ?? null,
						],
						[
							"no" => 3,
							"answer" => $_POST['question3'] ?? null,
						],
						[
							"no" => 4,
							"answer" => $_POST['question4'] ?? null,
						],
						[
							"no" => 5.1,
							"answer" => $_POST['question5_1'] ?? null,
						],
						[
							"no" => 5.2,
							"answer" => $_POST['question5_2'] ?? null,
						],
						[
							"no" => 6,
							"answer" => $_POST['question6'] ?? null,
						],
						[
							"no" => 7,
							"answer" => $_POST['question7'] ?? null,
						],
					];

					$questionString = json_encode($questions, JSON_PRETTY_PRINT);

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, alat_type, questions) 
					VALUES (?, ?, ?, ?)")
					) {
						$insert_stmt2->bind_param('ssss', $stamp_id, $platform_country, $alat_type, $questionString);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For BTU (BOX) Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'BTU - (BOX)')) {
					$btu_info = [];

					$noOfBtu = $_POST['noOfBtu'] ?? null;
					$no = $_POST['no'] ?? [];
					$batuUjian = $_POST['batuUjian'] ?? [];
					$batuUjianLain = $_POST['batuUjianLain'] ?? [];
					$penandaanBatuUjian = $_POST['penandaanBatuUjian'] ?? [];
					$batuDaftarLama = $_POST['batuDaftarLama'] ?? [];
					$batuDaftarBaru = $_POST['batuDaftarBaru'] ?? [];
					$batuNoSiriPelekatKeselamatan = $_POST['batuNoSiriPelekatKeselamatan'] ?? [];
					$batuBorangD = $_POST['batuBorangD'] ?? [];
					$batuBorangE = $_POST['batuBorangE'] ?? [];

					if (isset($no) && $no != null && count($no) > 0) {
						for ($i = 0; $i < count($no); $i++) {
							$btu_info[] = array(
								"no" => $no[$i],
								"batuUjian" => $batuUjian[$i],
								"batuUjianLain" => $batuUjianLain[$i],
								"penandaanBatuUjian" => $penandaanBatuUjian[$i],
								"batuDaftarLama" => $batuDaftarLama[$i],
								"batuDaftarBaru" => $batuDaftarBaru[$i],
								"batuNoSiriPelekatKeselamatan" => $batuNoSiriPelekatKeselamatan[$i],
								"batuBorangD" => $batuBorangD[$i],
								"batuBorangE" => $batuBorangE[$i]
							);
						}
					}

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, btu_box_qty, btu_box_info) 
					VALUES (?, ?, ?)")
					) {
						$btuInfo = json_encode($btu_info);
						$insert_stmt2->bind_param('sss', $stamp_id, $noOfBtu, $btuInfo);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For BTU Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'BTU') && !str_contains($jenisAlatName, 'BTU - (BOX)')) {
					$platform_country = null;
					$batuUjian = null;
					$batuUjianLain = null;
					$penandaanBatuUjian = null;

					if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
						$platform_country = $_POST['platformCountry'];
					}

					if (isset($_POST['penandaanBatuUjian']) && $_POST['penandaanBatuUjian'] != null && $_POST['penandaanBatuUjian'] != "") {
						$penandaanBatuUjian = $_POST['penandaanBatuUjian'];
					}

					if (isset($_POST['batuUjian']) && $_POST['batuUjian'] != null && $_POST['batuUjian'] != "") {
						$batuUjian = $_POST['batuUjian'];
					}

					if (isset($_POST['batuUjianLain']) && $_POST['batuUjianLain'] != null && $_POST['batuUjianLain'] != "") {
						$batuUjianLain = $_POST['batuUjianLain'];
					}

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, penandaan_batu_ujian, batu_ujian, batu_ujian_lain) 
					VALUES (?, ?, ?, ?, ?)")
					) {
						$insert_stmt2->bind_param('sssss', $stamp_id, $platform_country, $penandaanBatuUjian, $batuUjian, $batuUjianLain);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For SIA Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'SIA')) {
					$platform_country = null;
					$nilaiJangka = null;
					$nilaiJangkaOther = null;
					$diperbuatDaripada = null;
					$diperbuatDaripadaOther = null;

					if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
						$platform_country = $_POST['platformCountry'];
					}

					if (isset($_POST['nilaiJangka']) && $_POST['nilaiJangka'] != null && $_POST['nilaiJangka'] != "") {
						$nilaiJangka = $_POST['nilaiJangka'];
					}

					if (isset($_POST['nilaiJangkaOther']) && $_POST['nilaiJangkaOther'] != null && $_POST['nilaiJangkaOther'] != "") {
						$nilaiJangkaOther = $_POST['nilaiJangkaOther'];
					}

					if (isset($_POST['diperbuatDaripada']) && $_POST['diperbuatDaripada'] != null && $_POST['diperbuatDaripada'] != "") {
						$diperbuatDaripada = $_POST['diperbuatDaripada'];
					}

					if (isset($_POST['diperbuatDaripadaOther']) && $_POST['diperbuatDaripadaOther'] != null && $_POST['diperbuatDaripadaOther'] != "") {
						$diperbuatDaripadaOther = $_POST['diperbuatDaripadaOther'];
					}

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, nilai_jangka, nilai_jangka_other, diperbuat_daripada,diperbuat_daripada_other ) 
					VALUES (?, ?, ?, ?, ?, ?)")
					) {
						$insert_stmt2->bind_param('ssssss', $stamp_id, $platform_country, $nilaiJangka, $nilaiJangkaOther, $diperbuatDaripada, $diperbuatDaripadaOther);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}

				}

				// For BAP Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'BAP')) {
					$pamNo = null;
					$kelulusanBentuk = null;
					$alatType = null;
					$kadarPengaliran = null;
					$bentukPenunjuk = null;
					$jenama = null;
					$jenamaOther = null;

					if (isset($_POST['pamNo']) && $_POST['pamNo'] != null && $_POST['pamNo'] != "") {
						$pamNo = $_POST['pamNo'];
					}

					if (isset($_POST['kelulusanBentuk']) && $_POST['kelulusanBentuk'] != null && $_POST['kelulusanBentuk'] != "") {
						$kelulusanBentuk = $_POST['kelulusanBentuk'];
					}

					if (isset($_POST['alatType']) && $_POST['alatType'] != null && $_POST['alatType'] != "") {
						$alatType = $_POST['alatType'];
					}

					if (isset($_POST['kadarPengaliran']) && $_POST['kadarPengaliran'] != null && $_POST['kadarPengaliran'] != "") {
						$kadarPengaliran = $_POST['kadarPengaliran'];
					}

					if (isset($_POST['bentukPenunjuk']) && $_POST['bentukPenunjuk'] != null && $_POST['bentukPenunjuk'] != "") {
						$bentukPenunjuk = $_POST['bentukPenunjuk'];
					}

					if (isset($_POST['jenama']) && $_POST['jenama'] != null && $_POST['jenama'] != "") {
						$jenama = $_POST['jenama'];
					}

					if (isset($_POST['jenamaOther']) && $_POST['jenamaOther'] != null && $_POST['jenamaOther'] != "") {
						$jenamaOther = $_POST['jenamaOther'];
					}

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, pam_no, kelulusan_bentuk, alat_type, kadar_pengaliran, bentuk_penunjuk, jenama, jenama_Other) 
					VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
					) {
						$insert_stmt2->bind_param('ssssssss', $stamp_id, $pamNo, $kelulusanBentuk, $alatType, $kadarPengaliran, $bentukPenunjuk, $jenama, $jenamaOther);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For SIC Additional fields
				if (($validator == '10' || $validator == '9') && str_contains($jenisAlatName, 'SIC')) {
					$nilaiMaksimum = null;
					$bahanPembuat = null;
					$bahanPembuatOther = null;

					if (isset($_POST['nilaiMaksimum']) && $_POST['nilaiMaksimum'] != null && $_POST['nilaiMaksimum'] != "") {
						$nilaiMaksimum = $_POST['nilaiMaksimum'];
					}

					if (isset($_POST['bahanPembuat']) && $_POST['bahanPembuat'] != null && $_POST['bahanPembuat'] != "") {
						$bahanPembuat = $_POST['bahanPembuat'];
					}

					if (isset($_POST['bahanPembuatOther']) && $_POST['bahanPembuatOther'] != null && $_POST['bahanPembuatOther'] != "") {
						$bahanPembuatOther = $_POST['bahanPembuatOther'];
					}

					if (
						$insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, nilai_jangkaan_maksimum, bahan_pembuat, bahan_pembuat_other) 
					VALUES (?, ?, ?, ?)")
					) {
						$insert_stmt2->bind_param('ssss', $stamp_id, $nilaiMaksimum, $bahanPembuat, $bahanPembuatOther);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// Insert Stamping System Log
				if (
					$insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id) 
				VALUES (?, ?, ?)")
				) {
					$action = "INSERT";
					$insert_stmt3->bind_param('sss', $action, $uid, $stamp_id);
					$insert_stmt3->execute();
					$insert_stmt3->close();
				}

				// Logic to save stamping status timeline
				$stmt = $db->prepare("SELECT quotation_no, quotation_attachment, purchase_no, serial_no, stamping_date, invoice_no, invoice_payment_type, invoice_payment_ref, validator_invoice FROM stamping WHERE id=?");
				$stmt->bind_param('i', $stamp_id);
				$stmt->execute();
				$stmt->bind_result($quotation_no, $quotation_attachment, $purchase_no, $serial_no, $stamping_date, $invoice_no, $invoice_payment_type, $invoice_payment_ref, $validator_invoice);
				$stmt->fetch();
				$stmt->close();

				// Statuses and their conditions
				$statuses = [
					1 => !empty($quotation_no),
					2 => !empty($quotation_attachment),
					3 => !empty($purchase_no),
					4 => !empty($serial_no),
					5 => !empty($stamping_date),
					6 => !empty($invoice_no),
					7 => (!empty($invoice_payment_type) && !empty($invoice_payment_ref)),
					8 => !empty($validator_invoice),
				];

				// Status descriptions (should match your miscellaneous table)
				$status_desc = [];
				if ($status_stmt = $db->prepare("SELECT * FROM miscellaneous WHERE code='stamping_status' AND deleted = 0")) {
					$status_stmt->execute();
					$result = $status_stmt->get_result();
					while ($row = $result->fetch_assoc()) {
						$status_desc[$row['value']] = $row['description'];
					}
					$status_stmt->close();
				}

				// Insert log for each status reached, in order
				$created_by = $_SESSION['userID'];
				$now = date('Y-m-d H:i:s');
				foreach ($statuses as $val => $reached) {
					if ($reached) {
						// Check if already logged
						$check = $db->prepare("SELECT id FROM stamping_status_log WHERE stamp_id=? AND status=?");
						$check->bind_param('is', $stamp_id, $status_desc[$val]);
						$check->execute();
						$check->store_result();
						if ($check->num_rows == 0) {
							$insert = $db->prepare("INSERT INTO stamping_status_log (stamp_id, status, created_by, occurred_at) VALUES (?, ?, ?, ?)");
							$insert->bind_param('isss', $stamp_id, $status_desc[$val], $created_by, $now);
							$insert->execute();
							$insert->close();
						}
						$check->close();
					}
				}

				echo json_encode(
					array(
						"status" => "success",
						"message" => "Added Successfully!!"
					)
				);
			}

			$insert_stmt->close();
		} else {
			$insert_stmt->close();
			echo json_encode(
				array(
					"status" => "failed",
					"message" => "Error when creating query"
				)
			);
		}

		$db->close();
	}
} else {
	echo json_encode(
		array(
			"status" => "failed",
			"message" => "Please fill in all the fields"
		)
	);
}

?>