<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);

session_start();

$uid = $_SESSION['userID'];

if(isset($_POST['assignTo'])){
	$assignTo = filter_input(INPUT_POST, 'assignTo', FILTER_SANITIZE_STRING);

	$assignTo2 = null;
	$assignTo3 = null;
	$serviceReportNo = null;
	
	$logs = array();

	if(isset($_POST['serviceReportNo']) && $_POST['serviceReportNo']!=null && $_POST['serviceReportNo']!=""){
		$serviceReportNo = $_POST['serviceReportNo'];
	}
	
	if(isset($_POST['assignTo2']) && $_POST['assignTo2']!=null && $_POST['assignTo2']!=""){
		$assignTo2 = $_POST['assignTo2'];
	}

	if(isset($_POST['assignTo3']) && $_POST['assignTo3']!=null && $_POST['assignTo3']!=""){
		$assignTo3 = $_POST['assignTo3'];
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		//Updated datetime
		$currentDateTime = date('Y-m-d H:i:s');

		if ($update_stmt = $db->prepare("UPDATE stamping SET service_report_no=?, assignTo=?, assignTo2=?, assignTo3=?, log=?, updated_datetime=? WHERE id=?")){
			$data = json_encode($logs);
			$update_stmt->bind_param('ssssssi',$serviceReportNo, $assignTo, $assignTo2, $assignTo3, $data, $currentDateTime, $_POST['id']);
	
			
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

				$uploadServiceReportAttachment = null;

				if(isset($_FILES['uploadServiceReportAttachment']) && $_FILES['uploadServiceReportAttachment']!=null && $_FILES['uploadServiceReportAttachment']!=""){
					$uploadServiceReportAttachment = $_FILES['uploadServiceReportAttachment'];

					$ds = DIRECTORY_SEPARATOR;
					$storeFolder = '../uploads/stamping';
					if($uploadServiceReportAttachment['error'] === 0){

						$update_stmt = $db->prepare("UPDATE stamping SET status=?, updated_datetime=? WHERE id=?");
					    $update_stmt->bind_param('Serviced', $currentDateTime, $_POST['id']);
		
						// Execute the prepared query.
						if (! $update_stmt->execute()){
							echo json_encode(
								array(
									"status"=> "failed", 
									"message"=> $update_stmt->error
								)
							);
						} 

						$timestamp = time();
						$uploadDir = $storeFolder . $ds; // Directory to store uploaded files
						$folderDir = dirname(__DIR__, 2) . '/' . $uploadDir;
						// Check if folder exists, if not, create it with correct permissions
						if (!is_dir($folderDir)) {
							mkdir($folderDir, 0777, true); // true allows recursive directory creation
						}

						$filename = $timestamp . '_' . basename($_FILES['uploadServiceReportAttachment']['name']);
						$uploadFile = dirname(__DIR__, 2) . '/' . $uploadDir . $filename;
						$tempFile = $_FILES['uploadServiceReportAttachment']['tmp_name'];

						// Move the uploaded file to the target directory
						if (move_uploaded_file($tempFile, $uploadFile)) {
							$serviceReportFilePath = $uploadDir . $filename;
							// Update certificate data in the database
							if ($stmt3 = $db->prepare("INSERT INTO files (filename, filepath) VALUES (?, ?)")) {
								$stmt3->bind_param('ss', $filename, $serviceReportFilePath);
								$stmt3->execute();
								$fid = $stmt3->insert_id;
								$stmt3->close();
								
								if ($stmtf = $db->prepare("UPDATE stamping SET service_report_attachment=? WHERE id=?")) {
									$stmtf->bind_param('ss', $fid, $stampingId);
									$stmtf->execute();
									$stmtf->close();
								}
							} 
						} 
					}
				}
				else{
					$update_stmt = $db->prepare("UPDATE stamping SET status=?, updated_datetime=? WHERE id=?");
					$update_stmt->bind_param('Servicing', $currentDateTime, $_POST['id']);
		
						// Execute the prepared query.
						if (! $update_stmt->execute()){
							echo json_encode(
								array(
									"status"=> "failed", 
									"message"=> $update_stmt->error
								)
							);
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