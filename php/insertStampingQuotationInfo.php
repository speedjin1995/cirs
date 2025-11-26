<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);

session_start();

$uid = $_SESSION['userID'];

if($_POST['quotation'] != null || $_POST['quotationDate'] != null || $_POST['poNo'] != null || $_POST['poDate'] != null){
	$quotation = null;
	$quotationDate = null;
	
	$logs = array();

	if(isset($_POST['quotation']) && $_POST['quotation']!=null && $_POST['quotation']!=""){
		$quotation = $_POST['quotation'];
	}

	if(isset($_POST['quotationDate']) && $_POST['quotationDate']!=null && $_POST['quotationDate']!=""){
		$quotationDate = $_POST['quotationDate'];
		$quotationDate = DateTime::createFromFormat('d/m/Y', $quotationDate)->format('Y-m-d H:i:s');
	}

	if(isset($_POST['poNo']) && $_POST['poNo']!=null && $_POST['poNo']!=""){
		$poNo = $_POST['poNo'];
	}

	if(isset($_POST['poDate']) && $_POST['poDate']!=null && $_POST['poDate']!=""){
		$poDate = $_POST['poDate'];
		$poDate = DateTime::createFromFormat('d/m/Y', $poDate)->format('Y-m-d H:i:s');
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		//Updated datetime
		$currentDateTime = date('Y-m-d H:i:s');
		if ($update_stmt = $db->prepare("UPDATE stamping SET quotation_no=?, quotation_date=?, purchase_no=?, purchase_date=?, log=?, updated_datetime=? WHERE id=?")){
			$data = json_encode($logs);
			$update_stmt->bind_param('ssssssi', $quotation, $quotationDate, $poNo, $poDate, $data, $currentDateTime, $_POST['id']);
		
			// Execute the prepared query.
			if (! $update_stmt->execute()){
				echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> $update_stmt->error
					)
				);
			} 
			else{
				$stampingId = $_POST['id'];

				$uploadQuotationAttachment = null;

				if(isset($_FILES['uploadQuotationAttachment']) && $_FILES['uploadQuotationAttachment']!=null && $_FILES['uploadQuotationAttachment']!=""){
					$uploadQuotationAttachment = $_FILES['uploadQuotationAttachment'];

					$ds = DIRECTORY_SEPARATOR;
					$storeFolder = '../uploads/stamping';
					if($uploadQuotationAttachment['error'] === 0){
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
									$stmtf->bind_param('ss', $fid, $stampingId);
									$stmtf->execute();
									$stmtf->close();
								}
							} 
						} 
					}
				}

				$stampExtQuery = "SELECT * FROM stamping_ext WHERE stamp_id = $stampingId";
                $stampExtDetail = mysqli_query($db, $stampExtQuery);
                $stampExtRow = mysqli_fetch_assoc($stampExtDetail);

				if($stampExtRow == NULL){
					if ($insert_stmt = $db->prepare("INSERT INTO stamping_ext (stamp_id) 
					VALUES (?)")){
						$insert_stmt->bind_param('s', $stampingId);
						$insert_stmt->execute();
						$insert_stmt->close();
					}
				}

				// UPDATE Stamping System Log
				if ($insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id) 
				VALUES (?, ?, ?)")){
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
						"status"=> "success", 
						"message"=> "Updated Successfully!!" 
					)
				);
			}

			$update_stmt->close();
		}
		else{
			$update_stmt->close();
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> "Error when creating query"
				)
			);
		}

	}
} 
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}

?>