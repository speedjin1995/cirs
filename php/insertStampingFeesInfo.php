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