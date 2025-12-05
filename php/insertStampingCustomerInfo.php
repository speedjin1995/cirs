<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);

session_start();

$uid = $_SESSION['userID'];

if (isset($_POST['customerType'])) {
	$customerType = $_POST['customerType'];
} else {
	$customerType = $_POST['customerTypeEdit'];
}

if (isset($_POST['type'], $customerType, $_POST['companyBranch'])) {
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
	$companyBranch = filter_input(INPUT_POST, 'companyBranch', FILTER_SANITIZE_STRING);
	$customerType = filter_input(INPUT_POST, 'customerType', FILTER_SANITIZE_STRING);
	$status = "Created";

	$company = null;
	$customerText = null;
	$otherCode = null;
	$address1 = null;
	$address2 = null;
	$address3 = null;
	$address4 = null;
	$address5 = null;
	$phone = null;
	$email = null;
	$contact = null;

	//MIGHT NEEDED Y EVERY FILES
	$dealer = null;
	$reseller_branch = null;
	$customer = "";
	$brand = null;
	$machineType = null;
	$model = null;
	$makeIn = null;
	$capacity = null;
	$serial = null;
	$assignTo = null;
	$assignTo2 = null;
	$assignTo3 = null;
	$ownershipStatus = null;
	$validatorlama = null;
	$validator = null;
	$cawangan = null;
	$jenisAlat = null;
	$machineName = null;
	$machineLocation = null;
	$machineArea = null;
	$machineSerialNo = null;
	$trade = null;
	$noDaftarLama = null;
	$noDaftarBaru = null;
	$pinKeselamatan = null;
	$siriKeselamatan = null;
	$includeCert = "NO";
	$borangD = null;
	$borangE = null;
	$borangEDate = null;
	$invoice = null;
	$invoicePaymentType = null;
	$invoicePayRef = null;
	$cashBill = null;
	$stampDate = null;
	$lastYearStampDate = null;
	$dueDate = null;
	$pic = null;
	$quotation = null;
	$quotationDate = null;
	$poNo = null;
	$poDate = null;
	$remark = null;
	$internalRemark = null;
	$validatorInvoice = null;
	$subAmountSst = null;
	$rebate = null;
	$rebateAmount = null;
	$subtotalPrice = '0.00';
	$unitPrice = '0.00';
	$certPrice = '0.00';
	$totalPrice = '0.00';
	$sst = '0.00';
	$labourCharge = '0.00';
	$stampLabourCharge = '0.00';
	$roundUp = '0.00';
	$totalCharge = '0.00';
	$data = null;
	$product = null;
	$newRenew = null;
	$branch = null;
	$sealNoLama = null;
	$sealNoBaru = null;
	$pegawaiContact = null;
	$certNo = null;


	$logs = array();

	if (isset($_POST['reseller_branch']) && $_POST['reseller_branch'] != null && $_POST['reseller_branch'] != "") {
		$reseller_branch = $_POST['reseller_branch'];
	}

	if (isset($_POST['company']) && $_POST['company'] != null && $_POST['company'] != "") {
		$company = $_POST['company'];
	}

	if (isset($_POST['companyText']) && $_POST['companyText'] != null && $_POST['companyText'] != "") {
		$companyText = $_POST['companyText'];
	}

	if (isset($_POST['otherCode']) && $_POST['otherCode'] != null && $_POST['otherCode'] != "") {
		$otherCode = $_POST['otherCode'];
	}

	if (isset($_POST['address1']) && $_POST['address1'] != null && $_POST['address1'] != "") {
		$address1 = $_POST['address1'];
	}

	if (isset($_POST['address2']) && $_POST['address2'] != null && $_POST['address2'] != "") {
		$address2 = $_POST['address2'];
	}

	if (isset($_POST['address3']) && $_POST['address3'] != null && $_POST['address3'] != "") {
		$address3 = $_POST['address3'];
	}

	if (isset($_POST['address4']) && $_POST['address4'] != null && $_POST['address4'] != "") {
		$address4 = $_POST['address4'];
	}

	if (isset($_POST['address5']) && $_POST['address5'] != null && $_POST['address5'] != "") {
		$address5 = $_POST['address5'];
	}

	if (isset($_POST['branch']) && $_POST['branch'] != null && $_POST['branch'] != "") {
		$branch = $_POST['branch'];
	}

	if (isset($_POST['phone']) && $_POST['phone'] != null && $_POST['phone'] != "") {
		$phone = $_POST['phone'];
	}

	if (isset($_POST['email']) && $_POST['email'] != null && $_POST['email'] != "") {
		$email = $_POST['email'];
	}

	if (isset($_POST['pic']) && $_POST['pic'] != null && $_POST['pic'] != "") {
		$pic = $_POST['pic'];
	}

	if (isset($_POST['contact']) && $_POST['contact'] != null && $_POST['contact'] != "") {
		$contact = $_POST['contact'];
	}

	if ($customerType == "NEW") {
		if ($select_stmt = $db->prepare("SELECT id FROM customers WHERE customer_name=? and deleted = '0'")) {
			$select_stmt->bind_param('s', $_POST['companyText']);
			$select_stmt->execute();
			$result = $select_stmt->get_result();

			if ($row = $result->fetch_assoc()) {
				$customer = $row['id'];
				$customerType = 'EXISTING';
			} else {
				$dealer = null;
				$branchName = '';
				$mapUrl = '';

				if (isset($_POST['dealer']) && $_POST['dealer'] != null && $_POST['dealer'] != "" && $type == 'DEALER') {
					$dealer = filter_input(INPUT_POST, 'dealer', FILTER_SANITIZE_STRING);
				}

				$custNameFirstLetter = substr($_POST['companyText'], 0, 1);
				$firstChar = $custNameFirstLetter;
				$code = 'C-' . strtoupper($custNameFirstLetter);

				$customerQuery = "SELECT * FROM customers WHERE customer_code LIKE '%$code%' ORDER BY customer_code DESC";
				$customerDetail = mysqli_query($db, $customerQuery);
				$customerRow = mysqli_fetch_assoc($customerDetail);

				$customerCode = null;
				$codeSeq = null;
				$count = '';

				if (!empty($customerRow)) {
					$customerCode = $customerRow['customer_code'];
					preg_match('/\d+/', $customerCode, $matches);
					$codeSeq = (int) $matches[0];
					$nextSeq = $codeSeq + 1;
					$count = str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
					$code .= $count;
				} else {
					$nextSeq = 1;
					$count = str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
					$code .= $count;
				}

				// Customer does not exist, create a new customer
				if ($insert_stmt = $db->prepare("INSERT INTO customers (customer_name, customer_code, customer_address, address2, address3, address4, address5, customer_phone, customer_email, customer_status, pic, pic_contact, other_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
					$customer_status = 'CUSTOMERS';
					$insert_stmt->bind_param('sssssssssssss', $_POST['companyText'], $code, $address1, $address2, $address3, $address4, $address5, $phone, $email, $customer_status, $pic, $contact, $otherCode);

					if ($insert_stmt->execute()) {
						$customer = $insert_stmt->insert_id;
						$customerType = 'EXISTING';

						if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4, address5, branch_name, map_url, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
							$insert_stmt2->bind_param('ssssssssss', $customer, $address1, $address2, $address3, $address4, $address5, $branchName, $mapUrl, $pic, $contact);
							$insert_stmt2->execute();
							$branch = $insert_stmt2->insert_id;
							$insert_stmt2->close();
						}
					} else {
						echo json_encode(
							array(
								"status" => "failed",
								"message" => $insert_stmt->error
							)
						);
					}

					$insert_stmt->close();
				}
			}

			$select_stmt->close();
		}
	} else {
		$customer = $_POST['company'];
		$customerType = 'EXISTING';
	}

	if (isset($_POST['pic']) && $_POST['pic'] != null && $_POST['pic'] != "") {
		$pic = $_POST['pic'];
	}

	//TJW START INSERT THE DATA INTO STAMPING TABLE
    $columns = [
        'type','company_branch','dealer','dealer_branch','customer_type','customers','brand','machine_type','model','make_in','capacity','serial_no',
        'assignTo','assignTo2','assignTo3','ownership_status','validator_lama','validate_by','cawangan','jenis_alat','machine_name','machine_location',
        'machine_area','machine_serial_no','trade','no_daftar_lama','no_daftar_baru','pin_keselamatan','siri_keselamatan','include_cert','borang_d',
        'borang_e','borang_e_date','invoice_no','invoice_payment_type','invoice_payment_ref','cash_bill','stamping_date','last_year_stamping_date',
        'due_date','pic','customer_pic','quotation_no','quotation_date','purchase_no','purchase_date','remarks','internal_remark','validator_invoice',
        'unit_price','cert_price','total_amount','sst','subtotal_sst_amt','rebate','rebate_amount','subtotal_amount','log','products','stamping_type',
        'branch','labour_charge','stampfee_labourcharge','int_round_up','total_charges','seal_no_lama','seal_no_baru','pegawai_contact','cert_no', 'status'
    ];

    $params = [
        $type, $companyBranch, $dealer, $reseller_branch, $customerType, $customer, $brand, $machineType, $model, $makeIn, $capacity, $serial,
        $assignTo, $assignTo2, $assignTo3, $ownershipStatus, $validatorlama, $validator, $cawangan, $jenisAlat, $machineName, $machineLocation,
        $machineArea, $machineSerialNo, $trade, $noDaftarLama, $noDaftarBaru, $pinKeselamatan, $siriKeselamatan, $includeCert, $borangD,
        $borangE, $borangEDate, $invoice, $invoicePaymentType, $invoicePayRef, $cashBill, $stampDate, $lastYearStampDate,
        $dueDate, $uid, $pic, $quotation, $quotationDate, $poNo, $poDate, $remark, $internalRemark, $validatorInvoice,
        $unitPrice, $certPrice, $totalPrice, $sst, $subAmountSst, $rebate, $rebateAmount, $subtotalPrice, $data, $product, $newRenew,
        $branch, $labourCharge, $stampLabourCharge, $roundUp, $totalCharge, $sealNoLama, $sealNoBaru, $pegawaiContact, $certNo, $status
    ];

    if (count($columns) !== count($params)) {
        echo json_encode(["status" => "failed", "message" => "Column count (".count($columns).") does not match param count (".count($params).")"]);
        $db->close();
        exit;
    }

	$placeholders = implode(',', array_fill(0, count($params), '?'));
    $sql = "INSERT INTO stamping (" . implode(',', $columns) . ") VALUES ($placeholders)";


	if (
		$insert_stmt = $db->prepare($sql)
	) {
		 $types = str_repeat('s', count($params)); // adjust types if needed
            $bind_names = [];
            $bind_names[] = & $types;
            for ($i = 0; $i < count($params); $i++) {
                $bind_names[] = & $params[$i];
            }
            call_user_func_array([$insert_stmt, 'bind_param'], $bind_names);


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

} else {
	echo json_encode(
		array(
			"status" => "failed",
			"message" => "Please fill in all the fields"
		)
	);
}
?>