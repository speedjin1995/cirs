<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);

session_start();

$uid = $_SESSION['userID'];

if (isset($_POST['newRenew'], $_POST['validator'], $_POST['cawangan'])) {
	$validator = filter_input(INPUT_POST, 'validator', FILTER_SANITIZE_STRING);
	$newRenew = filter_input(INPUT_POST, 'newRenew', FILTER_SANITIZE_STRING);
	$cawangan = filter_input(INPUT_POST, 'cawangan', FILTER_SANITIZE_STRING);
	$validatorlama = null;
	$dueDate = null;
	$stamping = null;
	$stampDate = null;
	$lastYearStampDate = null;
	$noDaftar = null;
	$pinKeselamatan = null;
	$siriKeselamatan = null;
	$borangD = null;
	$borangE = null;
	$borangEDate = null;
	$includeCert = "NO";
	$sealNoLama = null;
	$pegawaiContact = null;
	$sealNoBaru = null;
	$certNo = null;

	$logs = array();

	if (isset($_POST['validatorlama']) && $_POST['validatorlama'] != null && $_POST['validatorlama'] != "") {
		$validatorlama = $_POST['validatorlama'];
	}

	if (isset($_POST['stamping']) && $_POST['stamping'] != null && $_POST['stamping'] != "") {
		$stamping = $_POST['stamping'];
	}

	if (isset($_POST['stampDate']) && $_POST['stampDate'] != null && $_POST['stampDate'] != "") {
		$stampDate = $_POST['stampDate'];
		$stampDate = DateTime::createFromFormat('d/m/Y', $stampDate)->format('Y-m-d H:i:s');
	}

	if (isset($_POST['lastYearStampDate']) && $_POST['lastYearStampDate'] != null && $_POST['lastYearStampDate'] != "") {
		$lastYearStampDate = $_POST['lastYearStampDate'];
		$lastYearStampDate = DateTime::createFromFormat('d/m/Y', $lastYearStampDate)->format('Y-m-d H:i:s');
	}

	if (isset($_POST['noDaftarLama']) && $_POST['noDaftarLama'] != null && $_POST['noDaftarLama'] != "") {
		$noDaftarLama = $_POST['noDaftarLama'];
	}

	if (isset($_POST['noDaftarBaru']) && $_POST['noDaftarBaru'] != null && $_POST['noDaftarBaru'] != "") {
		$noDaftarBaru = $_POST['noDaftarBaru'];
	}

	if (isset($_POST['pinKeselamatan']) && $_POST['pinKeselamatan'] != null && $_POST['pinKeselamatan'] != "") {
		$pinKeselamatan = $_POST['pinKeselamatan'];
	}

	if (isset($_POST['siriKeselamatan']) && $_POST['siriKeselamatan'] != null && $_POST['siriKeselamatan'] != "") {
		$siriKeselamatan = $_POST['siriKeselamatan'];
	}

	if (isset($_POST['borangD']) && $_POST['borangD'] != null && $_POST['borangD'] != "") {
		$borangD = $_POST['borangD'];
	}

	if (isset($_POST['borangE']) && $_POST['borangE'] != null && $_POST['borangE'] != "") {
		$borangE = $_POST['borangE'];
	}

	if (isset($_POST['borangEDate']) && $_POST['borangEDate'] != null && $_POST['borangEDate'] != "") {
		$borangEDate = $_POST['borangEDate'];
		$borangEDate = DateTime::createFromFormat('d/m/Y', $borangEDate)->format('Y-m-d H:i:s');
	}

	if (isset($_POST['dueDate']) && $_POST['dueDate'] != null && $_POST['dueDate'] != "") {
		$dueDate = $_POST['dueDate'];
		$dueDate = DateTime::createFromFormat('d/m/Y', $dueDate)->format('Y-m-d H:i:s');
	}

	if (isset($_POST['includeCert']) && $_POST['includeCert'] != null && $_POST['includeCert'] != "") {
		$includeCert = $_POST['includeCert'];
	}

	if (isset($_POST['sealNoLama']) && $_POST['sealNoLama'] != null && $_POST['sealNoLama'] != "") {
		$sealNoLama = $_POST['sealNoLama'];
	}

	if (isset($_POST['pegawaiContact']) && $_POST['pegawaiContact'] != null && $_POST['pegawaiContact'] != "") {
		$pegawaiContact = $_POST['pegawaiContact'];
	}

	if (isset($_POST['sealNoBaru']) && $_POST['sealNoBaru'] != null && $_POST['sealNoBaru'] != "") {
		$sealNoBaru = $_POST['sealNoBaru'];
	}

	if (isset($_POST['certNo']) && $_POST['certNo'] != null && $_POST['certNo'] != "") {
		$certNo = $_POST['certNo'];
	}

	if (isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != '') {
		//Updated datetime
		$currentDateTime = date('Y-m-d H:i:s');

		if (
			$update_stmt = $db->prepare("UPDATE stamping SET 
		 validator_lama=?, validate_by=?, cawangan=?, no_daftar_lama=?, no_daftar_baru=?, pin_keselamatan=?, siri_keselamatan=?, include_cert=?, borang_d=?
		, borang_e=?, borang_e_date=?, stamping_date=?, last_year_stamping_date=?, due_date=?
		, log=?, stamping_type=?, updated_datetime=?, seal_no_lama=?, seal_no_baru=?, pegawai_contact=?, cert_no=? WHERE id=?")
		) {
			$data = json_encode($logs);

			$update_stmt->bind_param(
				'sssssssssssssssssssssi',
				$validatorlama,
				$validator,
				$cawangan,
				$noDaftarLama,
				$noDaftarBaru,
				$pinKeselamatan,
				$siriKeselamatan,
				$includeCert,
				$borangD,
				$borangE,
				$borangEDate,
				$stampDate,
				$lastYearStampDate,
				$dueDate,
				$data,
				$newRenew,
				$currentDateTime,
				$sealNoLama,
				$sealNoBaru,
				$pegawaiContact,
				$certNo,
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