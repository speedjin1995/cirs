<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['userID'];

if(isset($_POST['customerType'])){
	$customerType = $_POST['customerType'];
}else{
	$customerType = $_POST['customerTypeEdit'];
}

if(isset($_POST['type'], $customerType, $_POST['validator'], $_POST['address1'], $_POST['machineType'], $_POST['serial'], $_POST['manufacturing'], $_POST['brand'], $_POST['model'], $_POST['capacity'], $_POST['size'])){
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
	// $customerType = filter_input(INPUT_POST, 'customerType', FILTER_SANITIZE_STRING);
	$validator = filter_input(INPUT_POST, 'validator', FILTER_SANITIZE_STRING);
	$address1 = filter_input(INPUT_POST, 'address1', FILTER_SANITIZE_STRING);
	$machineType = filter_input(INPUT_POST, 'machineType', FILTER_SANITIZE_STRING);
	$serial = filter_input(INPUT_POST, 'serial', FILTER_SANITIZE_STRING);
	$manufacturing = filter_input(INPUT_POST, 'manufacturing', FILTER_SANITIZE_STRING);
	$brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
	$model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
	$capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
	$size = filter_input(INPUT_POST, 'size', FILTER_SANITIZE_STRING);

	$validationDate = null;
	if(isset($_POST['validationDate']) && $_POST['validationDate']!=null && $_POST['validationDate']!=""){
		$validationDate = $_POST['validationDate'];
		$validationDate = \DateTime::createFromFormat('d/m/Y', $validationDate)->format('Y-m-d H:i:s');
	}
	
	$dealer = null;
	$reseller_branch = null;
	$company = null;
	$customerText = null;
	$otherCode = null;
	$branch = null;
	$address2 = null;
	$address3 = null;
	$address4 = null;
	$phone = null;
	$email = null;
	$pic = null;
	$contact = null;
	$logs = array();
	$lastCalibrationDate = null;
	$expiredCalibrationDate = null;
	$certNo = null;
	$uploadAttachment = null;

	if(isset($_POST['dealer']) && $_POST['dealer']!=null && $_POST['dealer']!=""){
		$dealer = $_POST['dealer'];
	}

	if(isset($_POST['reseller_branch']) && $_POST['reseller_branch']!=null && $_POST['reseller_branch']!=""){
		$reseller_branch = $_POST['reseller_branch'];
	}

	if(isset($_POST['company']) && $_POST['company']!=null && $_POST['company']!=""){
		$company = $_POST['company'];
	}

	if(isset($_POST['companyText']) && $_POST['companyText']!=null && $_POST['companyText']!=""){
		$companyText = $_POST['companyText'];
	}

	if(isset($_POST['otherCode']) && $_POST['otherCode']!=null && $_POST['otherCode']!=""){
		$otherCode = $_POST['otherCode'];
	}

	if(isset($_POST['address2']) && $_POST['address2']!=null && $_POST['address2']!=""){
		$address2 = $_POST['address2'];
	}

	if(isset($_POST['address3']) && $_POST['address3']!=null && $_POST['address3']!=""){
		$address3 = $_POST['address3'];
	}

	if(isset($_POST['address4']) && $_POST['address4']!=null && $_POST['address4']!=""){
		$address4 = $_POST['address4'];
	}

	if(isset($_POST['branch']) && $_POST['branch']!=null && $_POST['branch']!=""){
		$branch = $_POST['branch'];
	}

	if(isset($_POST['phone']) && $_POST['phone']!=null && $_POST['phone']!=""){
		$phone = $_POST['phone'];
	}

	if(isset($_POST['email']) && $_POST['email']!=null && $_POST['email']!=""){
		$email = $_POST['email'];
	}

	if(isset($_POST['pic']) && $_POST['pic']!=null && $_POST['pic']!=""){
		$pic = $_POST['pic'];
	}

	if(isset($_POST['contact']) && $_POST['contact']!=null && $_POST['contact']!=""){
		$contact = $_POST['contact'];
	}

	if(isset($_POST['lastCalibrationDate']) && $_POST['lastCalibrationDate']!=null && $_POST['lastCalibrationDate']!=""){
		$lastCalibrationDate = $_POST['lastCalibrationDate'];
		$lastCalibrationDate = DateTime::createFromFormat('d/m/Y', $lastCalibrationDate)->format('Y-m-d H:i:s');
	}

	if(isset($_POST['expiredCalibrationDate']) && $_POST['expiredCalibrationDate']!=null && $_POST['expiredCalibrationDate']!=""){
		$expiredCalibrationDate = $_POST['expiredCalibrationDate'];
		$expiredCalibrationDate = DateTime::createFromFormat('d/m/Y', $expiredCalibrationDate)->format('Y-m-d H:i:s');
	}

	if(isset($_POST['autoFormNo']) && $_POST['autoFormNo']!=null && $_POST['autoFormNo']!=""){
		$certNo = $_POST['autoFormNo'];
	}

	if($customerType == "NEW"){
		if ($select_stmt = $db->prepare("SELECT id FROM customers WHERE customer_name=? and deleted = '0'")) {
			$select_stmt->bind_param('s', $companyText);
			$select_stmt->execute();
			$result = $select_stmt->get_result();
        
			if ($row = $result->fetch_assoc()) {
				$customer = $row['id'];
				$customerType = 'EXISTING';
			} 
			else {
				$branchName = '';
				$mapUrl = '';
				
				# to generate customer code
				$custNameFirstLetter = substr($companyText, 0, 1);
				$firstChar = $custNameFirstLetter;
		
				if($misc_stmt = $db->prepare("SELECT * FROM miscellaneous WHERE code='customer' AND description=?")){
					$misc_stmt->bind_param('s', $custNameFirstLetter);
		
					if(!$misc_stmt->execute()){
						echo json_encode(
							array(
								"status" => "failed",
								"message" => "Something went wrong querying miscellaneous"
							)); 
					}else{
						$result = $misc_stmt->get_result();
						while ($row = $result->fetch_assoc()){
							$charSize = strlen($row['value']);
							$misValue = $row['value'];
							
							$code = 'C-'.strtoupper($custNameFirstLetter);
							for($i=0; $i<(4-(int)$charSize); $i++){
								$code.='0';  // S0000
							}

							$code.=$misValue;

							// Customer does not exist, create a new customer
							if ($insert_stmt = $db->prepare("INSERT INTO customers (customer_name, customer_code, customer_address, address2, address3, address4, customer_phone, customer_email, customer_status, pic, pic_contact, other_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
								$customer_status = 'CUSTOMERS';
								$insert_stmt->bind_param('ssssssssssss', $companyText, $code, $address1, $address2, $address3, $address4, $phone, $email, $customer_status, $pic, $contact, $otherCode);
								
								if ($insert_stmt->execute()) {
									$customer = $insert_stmt->insert_id;
									$customerType = 'EXISTING';

									if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4, branch_name, map_url, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
										$insert_stmt2->bind_param('sssssssss', $customer, $address1, $address2, $address3, $address4, $branchName, $mapUrl, $pic, $contact);
										$insert_stmt2->execute();
										$branch = $insert_stmt2->insert_id;
										$insert_stmt2->close();
									} 
								} 
							}

							#Update miscellaneous value
                            $misValue++;

                            if($updmisc_stmt = $db->prepare("UPDATE miscellaneous SET value=? WHERE code='customer' AND description=?")){
                                $updmisc_stmt->bind_param('ss', $misValue, $firstChar);
										
                                // Execute the prepared query.
                                if (! $updmisc_stmt->execute()){
                    
                                    echo json_encode(
                                        array(
                                            "status"=> "failed", 
                                            "message"=> $updmisc_stmt->error
                                        )
                                    );
                                }
                            }
						}
					}
				}
			}
		}
	}
	else{
		$customer = $_POST['company'];
		$customerType = 'EXISTING';
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		//Updated datetime
		$currentDateTime = date('Y-m-d H:i:s');
		if ($update_stmt = $db->prepare("UPDATE other_validations SET type=?, dealer=?, dealer_branch=?, validate_by=?, customer_type=?, customer=?, branch=?, auto_form_no=?, machines=?, unit_serial_no=?, manufacturing=?, brand=?
		, model=?, capacity=?, size=?, last_calibration_date=?, expired_calibration_date=?, validation_date=?, update_datetime=? WHERE id=?")){
			$data = json_encode($logs);
			$update_stmt->bind_param('ssssssssssssssssssss', $type, $dealer, $reseller_branch, $validator, $customerType, $customer, $branch, $certNo, $machineType, $serial, $manufacturing, $brand, $model, $capacity, $size, $lastCalibrationDate, $expiredCalibrationDate, $validationDate, $currentDateTime, $_POST['id']);
			
			// Execute the prepared query.
			if (! $update_stmt->execute()){
				$response['status'] = "failed";
    			$response['message'] = $update_stmt->error;
			} 
			else{
				$calibrationFilePath = '';
				// $no = $_POST['no'];
				// $lastCalibrationDate = $_POST['lastCalibrationDate'];
				// $expiredCalibrationDate = $_POST['expiredCalibrationDate'];
				$attachments = [];
				for ($i = 1; $i <= 5; $i++) {
					$fileKey = 'uploadAttachment' . $i;
					$pathKey = 'calibrationFilePath' . $i;

					// Check if the file is uploaded and has no error
					if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === 0) {
						$attachments[$i] = $_FILES[$fileKey];

						// Delete existing file if it exists
						if (isset($_POST[$pathKey]) && $_POST[$pathKey] != "") {
							$existingFilePath = $_POST[$pathKey];
							if (file_exists($existingFilePath)) {
								unlink($existingFilePath);
							}
						}
					}
				}

				$ds = DIRECTORY_SEPARATOR;
				$storeFolder = '../uploads/calibration';

				foreach ($attachments as $index => $file) {
					$timestamp = time();
					$uploadDir = $storeFolder . $ds;
					$uploadFile = $uploadDir . $timestamp . '_' . basename($file['name']);
					$tempFile = $file['tmp_name'];

					// Move the uploaded file to the target directory
					if (move_uploaded_file($tempFile, $uploadFile)) {
						$certFilePath = $uploadFile;

						// Prepare the column name for the current file path
						$columnName = "cert_file_path" . $index;

						// Update the specific certificate data column in the database
						$query = "UPDATE other_validations SET $columnName=? WHERE id=?";
						if ($stmt2 = $db->prepare($query)) {
							$stmt2->bind_param('ss', $certFilePath, $_POST['id']);
							$stmt2->execute();
							$stmt2->close();
						}
					}
				}


				// if(isset($no) && $no != null && count($no) > 0){
				// 	for($i=0; $i<count($no); $i++){
				// 		$load_calibrations_info[] = array(
				// 			"no" => $no[$i],
				// 			"lastCalibrationDate" => $lastCalibrationDate[$i],
				// 			"expiredCalibrationDate" => $expiredCalibrationDate[$i]
				// 		);

				// 		if(isset($calibrationFilePath) && $calibrationFilePath!=null && $calibrationFilePath!=""){
				// 			$load_calibrations_info[$i]['calibrationFilePath'] = $calibrationFilePath[$i];
				// 		}

				// 		if($uploadAttachment['error'][$i] === 0){
				// 			if(isset($calibrationFilePath) && $calibrationFilePath!=null && $calibrationFilePath!=""){
				// 				$calibrationFilePath = str_replace('../cirs/', '../', $calibrationFilePath[$i]); 

				// 				if (file_exists($calibrationFilePath)) {
				// 					unlink($calibrationFilePath);
				// 				}
				// 			}
							
				// 			$timestamp = time();
				// 			$uploadDir = '../uploads/calibration/'; // Directory to store uploaded files
				// 			$uploadDirDB = '../uploads/calibration/'; // filepath for db
				// 			$uploadFile = $uploadDir . $timestamp . '_' . basename($_FILES['uploadAttachment']['name'][$i]);
				// 			$uploadFileDB = $uploadDirDB . $timestamp . '_' . basename($_FILES['uploadAttachment']['name'][$i]);
				// 			$tempFile = $_FILES['uploadAttachment']['tmp_name'][$i];

				// 			// Move the uploaded file to the target directory
				// 			if (move_uploaded_file($tempFile, $uploadFile)) {
				// 				$load_calibrations_info[$i]['calibrationFilePath'] = $uploadFileDB; // Add file path to data
				// 			} else {
				// 				$response['file_status'] = "File upload failed.";
				// 			}
				// 		} else {
				// 			$response['file_status'] = "No file uploaded or there was an error.";
				// 		}
				// 	}

				// 	$dataJson = json_encode([$load_calibrations_info], JSON_PRETTY_PRINT);
				// }

				// // Update certificate data in the database
				// if ($stmt2 = $db->prepare("UPDATE other_validations SET calibrations=? WHERE id=?")) {
				// 	$stmt2->bind_param('ss', $dataJson, $_POST['id']);
				// 	$stmt2->execute();
				// 	$stmt2->close();
				// } 
				
				// UPDATE Other Validation System Log
				if ($insert_stmt3 = $db->prepare("INSERT INTO other_validation_log (action, user_id, item_id) 
				VALUES (?, ?, ?)")){
					$action = "UPDATE";
					$insert_stmt3->bind_param('sss', $action, $uid, $_POST['id']);
					$insert_stmt3->execute();
					$insert_stmt3->close();
				}

				$update_stmt->close();
				$db->close();
				
				$response['status'] = "success";
				$response['message'] = "Updated Successfully!!";
			}
		}
		else{
			$response['status'] = "failed";
			$response['message'] = "Error when creating query";
		}
	}
	else{
		if ($insert_stmt = $db->prepare("INSERT INTO other_validations (type, dealer, dealer_branch, validate_by, customer_type, customer, branch, machines, unit_serial_no, manufacturing, brand, model, capacity, size, last_calibration_date, expired_calibration_date, auto_form_no, status, validation_date) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
			$data = json_encode($logs);
			$calibrations = null;
			$status = 'Pending';
			$insert_stmt->bind_param('sssssssssssssssssss', $type, $dealer, $reseller_branch, $validator, $customerType, $customer, $branch,$machineType, $serial, $manufacturing, $brand, $model,$capacity, $size, $lastCalibrationDate, $expiredCalibrationDate, $certNo, $status, $validationDate);
			
			// Execute the prepared query.
			if (! $insert_stmt->execute()){
				$response['status'] = "failed";
    			$response['message'] = $insert_stmt->error;
			} 
			else{
				$validation_id = $insert_stmt->insert_id;
				// $no = $_POST['no'];
				// $lastCalibrationDate = $_POST['lastCalibrationDate'];
				// $expiredCalibrationDate = $_POST['expiredCalibrationDate'];

				$attachments = [];
				for ($i = 1; $i <= 5; $i++) {
					$fileKey = 'uploadAttachment' . $i;
					if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === 0) {
						$attachments[$i] = $_FILES[$fileKey]; // Store each file with its index
					}
				}

				$ds = DIRECTORY_SEPARATOR;
				$storeFolder = '../uploads/calibration';

				foreach ($attachments as $index => $file) {
					$timestamp = time();
					$uploadDir = $storeFolder . $ds; // Directory to store uploaded files
					$uploadFile = $uploadDir . $timestamp . '_' . basename($file['name']);
					$tempFile = $file['tmp_name'];

					// Move the uploaded file to the target directory
					if (move_uploaded_file($tempFile, $uploadFile)) {
						$certFilePath = $uploadFile;

						// Prepare the column name based on the index
						$columnName = "cert_file_path" . $index;

						// Update the specific certificate data column in the database
						$query = "UPDATE other_validations SET $columnName=? WHERE id=?";
						if ($stmt2 = $db->prepare($query)) {
							$stmt2->bind_param('ss', $certFilePath, $validation_id);
							$stmt2->execute();
							$stmt2->close();
						} 
					}
				}

				// if(isset($no) && $no != null && count($no) > 0){
				// 	for($i=0; $i<count($no); $i++){
				// 		$load_cells_info[] = array(
				// 			"no" => $no[$i],
				// 			"lastCalibrationDate" => $lastCalibrationDate[$i],
				// 			"expiredCalibrationDate" => $expiredCalibrationDate[$i],
				// 		);

				// 		if($uploadAttachment['error'][$i] === 0){
				// 			$timestamp = time();
				// 			$uploadDir = '../uploads/calibration/'; // Directory to store uploaded files
				// 			$uploadDirDB = '../uploads/calibration/'; // filepath for db
				// 			$uploadFile = $uploadDir . $timestamp . '_' . basename($_FILES['uploadAttachment']['name'][$i]);
				// 			$uploadFileDB = $uploadDirDB . $timestamp . '_' . basename($_FILES['uploadAttachment']['name'][$i]);
				// 			$tempFile = $_FILES['uploadAttachment']['tmp_name'][$i];

				// 			// Move the uploaded file to the target directory
				// 			if (move_uploaded_file($tempFile, $uploadFile)) {
				// 				$load_cells_info[$i]['calibrationFilePath'] = $uploadFileDB; // Add file path to data
				// 			} else {
				// 				$response['file_status'] = "File upload failed.";
				// 			}
				// 		} else {
				// 			$response['file_status'] = "No file uploaded or there was an error.";
				// 		}
				// 	}

				// 	$dataJson = json_encode([$load_cells_info], JSON_PRETTY_PRINT);
				// }

				// Update certificate data in the database
				// if ($stmt2 = $db->prepare("UPDATE other_validations SET calibrations=? WHERE id=?")) {
				// 	$stmt2->bind_param('ss', $dataJson, $validation_id);
				// 	$stmt2->execute();
				// 	$stmt2->close();
				// } 

				// Insert Other Validation System Log
				if ($insert_stmt3 = $db->prepare("INSERT INTO other_validation_log (action, user_id, item_id) 
				VALUES (?, ?, ?)")){
					$action = "INSERT";
					$insert_stmt3->bind_param('sss', $action, $uid, $validation_id);
					$insert_stmt3->execute();
					$insert_stmt3->close();
				}

				$insert_stmt->close();
				$db->close();
				
				$response['status'] = "success";
				$response['message'] = "Added Successfully!!";
			}
		}
		else{
			$response['status'] = "failed";
			$response['message'] = "Error when creating query";
		}
	}
} 
else{
	$response['status'] = "failed";
	$response['message'] = "Please fill in all the fields";
}

echo json_encode($response);

?>