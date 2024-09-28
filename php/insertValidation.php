<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['userID'];

if(isset($_POST['customerType'], $_POST['autoFormNo'], $_POST['validator'], $_POST['address1'], $_POST['machineType'], $_POST['serial'], $_POST['manufacturing'], $_POST['brand'], $_POST['model'], $_POST['capacity'], $_POST['size'])){
	$customerType = filter_input(INPUT_POST, 'customerType', FILTER_SANITIZE_STRING);
	$autoFormNo = filter_input(INPUT_POST, 'autoFormNo', FILTER_SANITIZE_STRING);
	$validator = filter_input(INPUT_POST, 'validator', FILTER_SANITIZE_STRING);
	$address1 = filter_input(INPUT_POST, 'address1', FILTER_SANITIZE_STRING);
	$machineType = filter_input(INPUT_POST, 'machineType', FILTER_SANITIZE_STRING);
	$serial = filter_input(INPUT_POST, 'serial', FILTER_SANITIZE_STRING);
	$manufacturing = filter_input(INPUT_POST, 'manufacturing', FILTER_SANITIZE_STRING);
	$brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
	$model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
	$capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
	$size = filter_input(INPUT_POST, 'size', FILTER_SANITIZE_STRING);
	
	$company = null;
	$customerText = null;
	$branch = null;
	$address2 = null;
	$address3 = null;
	$address4 = null;
	$phone = null;
	$email = null;
	$pic = null;
	$contact = null;
	$logs = array();

	if(isset($_POST['company']) && $_POST['company']!=null && $_POST['company']!=""){
		$company = $_POST['company'];
	}

	if(isset($_POST['companyText']) && $_POST['companyText']!=null && $_POST['companyText']!=""){
		$companyText = $_POST['companyText'];
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

	if($customerType == "NEW"){
		if ($select_stmt = $db->prepare("SELECT id FROM customers WHERE customer_name=?")) {
			$select_stmt->bind_param('s', $_POST['companyText']);
			$select_stmt->execute();
			$result = $select_stmt->get_result();
        
			if ($row = $result->fetch_assoc()) {
				$customer = $row['id'];
				$customerType = 'EXISTING';
			} 
			else {
				$branchName = '';
				$mapUrl = '';
				
				// Customer does not exist, create a new customer
				if ($insert_stmt = $db->prepare("INSERT INTO customers (customer_name, customer_address, address2, address3, address4, customer_phone, customer_email, customer_status, pic, pic_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
					$customer_status = 'CUSTOMER';
					$insert_stmt->bind_param('ssssssssss', $_POST['companyText'], $address1, $address2, $address3, $address4, $phone, $email, $customer_status, $pic, $contact);
					
					if ($insert_stmt->execute()) {
						$customer = $insert_stmt->insert_id;
						$customerType = 'EXISTING';

						if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4, branch_name, map_url, pic, pic_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                            $insert_stmt2->bind_param('sssssssss', $customer, $address1, $address2, $address3, $address4, $branchName, $mapUrl, $pic, $contact);
                            $insert_stmt2->execute();
							$branch = $insert_stmt2->insert_id;
                            $insert_stmt2->close();
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
		if ($update_stmt = $db->prepare("UPDATE other_validations SET validate_by=?, customer_type=?, customer=?, branch=?, auto_form_no=?, machines=?, unit_serial_no=?, manufacturing=?, brand=?
		, model=?, capacity=?, size=?, update_datetime=? WHERE id=?")){
			$data = json_encode($logs);
			$update_stmt->bind_param('ssssssssssssss', $validator, $customerType, $customer, $branch, $autoFormNo, $machineType, $serial, $manufacturing, $brand, $model, $capacity, $size, $currentDateTime, $_POST['id']);
		
			// Execute the prepared query.
			if (! $update_stmt->execute()){
				$response['status'] = "failed";
    			$response['message'] = $update_stmt->error;
			} 
			else{
				$lastCalibrationFilePath = '';
				$expiredCalibrationFilePath = '';

				$no = $_POST['no'];
				$lastCalibrationDate = $_POST['lastCalibrationDate'];
				$uploadlastCalibrationPdf = $_FILES['uploadlastCalibrationPdf'];

				if(isset($_POST['lastCalibrationFilePath']) && $_POST['lastCalibrationFilePath']!=null && $_POST['lastCalibrationFilePath']!=""){
					$lastCalibrationFilePath = $_POST['lastCalibrationFilePath'];
				}

				$expiredCalibrationDate = $_POST['expiredCalibrationDate'];
				$uploadexpiredCalibrationPdf = $_FILES['uploadexpiredCalibrationPdf'];

				if(isset($_POST['expiredCalibrationFilePath']) && $_POST['expiredCalibrationFilePath']!=null && $_POST['expiredCalibrationFilePath']!=""){
					$expiredCalibrationFilePath = $_POST['expiredCalibrationFilePath'];
				}

				$ds = DIRECTORY_SEPARATOR;
				$storeFolder = '../uploads/calibration';
				$dataJson = '';

				if(isset($no) && $no != null && count($no) > 0){
					for($i=0; $i<count($no); $i++){
						$load_calibrations_info[] = array(
							"no" => $no[$i],
							"lastCalibrationDate" => $lastCalibrationDate[$i],
							"expiredCalibrationDate" => $expiredCalibrationDate[$i]
						);

						if(isset($lastCalibrationFilePath) && $lastCalibrationFilePath!=null && $lastCalibrationFilePath!=""){
							$load_calibrations_info[$i]['lastCalibrationFilePath'] = $lastCalibrationFilePath[$i];
						}

						if(isset($expiredCalibrationFilePath) && $expiredCalibrationFilePath!=null && $expiredCalibrationFilePath!=""){
							$load_calibrations_info[$i]['expiredCalibrationFilePath'] = $expiredCalibrationFilePath[$i];
						}

						if($uploadlastCalibrationPdf['error'][$i] === 0){
							if(isset($lastCalibrationFilePath) && $lastCalibrationFilePath!=null && $lastCalibrationFilePath!=""){
								$updatedLastCalibFilePath = str_replace('../cirs/', '../', $lastCalibrationFilePath[$i]); 

								if (file_exists($updatedLastCalibFilePath)) {
									unlink($updatedLastCalibFilePath);
								}
							}
							
							$timestamp = time();
							$uploadDir = '../uploads/calibration/'; // Directory to store uploaded files
							$uploadDirDB = '../cirs/uploads/calibration/'; // filepath for db
							$uploadFile = $uploadDir . $timestamp . '_' . basename($_FILES['uploadlastCalibrationPdf']['name'][$i]);
							$uploadFileDB = $uploadDirDB . $timestamp . '_' . basename($_FILES['uploadlastCalibrationPdf']['name'][$i]);
							$tempFile = $_FILES['uploadlastCalibrationPdf']['tmp_name'][$i];
						
							// Move the uploaded file to the target directory
							if (move_uploaded_file($tempFile, $uploadFile)) {
								$load_calibrations_info[$i]['lastCalibrationFilePath'] = $uploadFileDB; // Add file path to data
							} else {
								$response['file_status'] = "File upload failed.";
							}
						} else {
							$response['file_status'] = "No file uploaded or there was an error.";
						}

						if($uploadexpiredCalibrationPdf['error'][$i] === 0){
							if(isset($expiredCalibrationFilePath) && $expiredCalibrationFilePath!=null && $expiredCalibrationFilePath!=""){
								$updatedExpiredCalibFilePath = str_replace('../cirs/', '../', $expiredCalibrationFilePath[$i]); 

								if (file_exists($updatedExpiredCalibFilePath)) {
									unlink($updatedExpiredCalibFilePath);
								}
							}
							
							$timestamp = time();
							$uploadDir = '../uploads/calibration/'; // Directory to store uploaded files
							$uploadDirDB = '../cirs/uploads/calibration/'; // filepath for db
							$uploadFile = $uploadDir . $timestamp . '_' . basename($_FILES['uploadexpiredCalibrationPdf']['name'][$i]);
							$uploadFileDB = $uploadDirDB . $timestamp . '_' . basename($_FILES['uploadexpiredCalibrationPdf']['name'][$i]);
							$tempFile = $_FILES['uploadexpiredCalibrationPdf']['tmp_name'][$i];

							// Move the uploaded file to the target directory
							if (move_uploaded_file($tempFile, $uploadFile)) {
								$load_calibrations_info[$i]['expiredCalibrationFilePath'] = $uploadFileDB; // Add file path to data
							} else {
								$response['file_status'] = "File upload failed.";
							}
						} else {
							$response['file_status'] = "No file uploaded or there was an error.";
						}
					}

					$dataJson = json_encode([$load_calibrations_info], JSON_PRETTY_PRINT);
				}

				// Update certificate data in the database
				if ($stmt2 = $db->prepare("UPDATE other_validations SET calibrations=? WHERE id=?")) {
					$stmt2->bind_param('ss', $dataJson, $_POST['id']);
					$stmt2->execute();
					$stmt2->close();
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
		if ($insert_stmt = $db->prepare("INSERT INTO other_validations (validate_by, customer_type, customer, branch, auto_form_no, machines, unit_serial_no, manufacturing, brand, model, capacity, size, status) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
			$data = json_encode($logs);
			$calibrations = null;
			$status = 'Pending';
			$insert_stmt->bind_param('sssssssssssss', $validator, $customerType, $customer, $branch, $autoFormNo,$machineType, $serial, $manufacturing, $brand, $model,$capacity, $size, $status);
			
			// Execute the prepared query.
			if (! $insert_stmt->execute()){
				$response['status'] = "failed";
    			$response['message'] = $insert_stmt->error;
			} 
			else{
				$validation_id = $insert_stmt->insert_id;
				$no = $_POST['no'];
				$lastCalibrationDate = $_POST['lastCalibrationDate'];
				$uploadlastCalibrationPdf = $_FILES['uploadlastCalibrationPdf'];
				$expiredCalibrationDate = $_POST['expiredCalibrationDate'];
				$uploadexpiredCalibrationPdf = $_FILES['uploadexpiredCalibrationPdf'];
				$ds = DIRECTORY_SEPARATOR;
				$storeFolder = '../uploads/calibration';
				$dataJson = '';

				if(isset($no) && $no != null && count($no) > 0){
					for($i=0; $i<count($no); $i++){
						$load_cells_info[] = array(
							"no" => $no[$i],
							"lastCalibrationDate" => $lastCalibrationDate[$i],
							"expiredCalibrationDate" => $expiredCalibrationDate[$i],
						);

						if($uploadlastCalibrationPdf['error'][$i] === 0){
							$timestamp = time();
							$uploadDir = '../uploads/calibration/'; // Directory to store uploaded files
							$uploadDirDB = '../cirs/uploads/calibration/'; // filepath for db
							$uploadFile = $uploadDir . $timestamp . '_' . basename($_FILES['uploadlastCalibrationPdf']['name'][$i]);
							$uploadFileDB = $uploadDirDB . $timestamp . '_' . basename($_FILES['uploadlastCalibrationPdf']['name'][$i]);
							$tempFile = $_FILES['uploadlastCalibrationPdf']['tmp_name'][$i];
						
							// Move the uploaded file to the target directory
							if (move_uploaded_file($tempFile, $uploadFile)) {
								$load_cells_info[$i]['lastCalibrationFilePath'] = $uploadFileDB; // Add file path to data
							} else {
								$response['file_status'] = "File upload failed.";
							}
						} else {
							$response['file_status'] = "No file uploaded or there was an error.";
						}

						if($uploadexpiredCalibrationPdf['error'][$i] === 0){
							$timestamp = time();
							$uploadDir = '../uploads/calibration/'; // Directory to store uploaded files
							$uploadDirDB = '../cirs/uploads/calibration/'; // filepath for db
							$uploadFile = $uploadDir . $timestamp . '_' . basename($_FILES['uploadexpiredCalibrationPdf']['name'][$i]);
							$uploadFileDB = $uploadDirDB . $timestamp . '_' . basename($_FILES['uploadexpiredCalibrationPdf']['name'][$i]);
							$tempFile = $_FILES['uploadexpiredCalibrationPdf']['tmp_name'][$i];

							// Move the uploaded file to the target directory
							if (move_uploaded_file($tempFile, $uploadFile)) {
								$load_cells_info[$i]['expiredCalibrationFilePath'] = $uploadFileDB; // Add file path to data
							} else {
								$response['file_status'] = "File upload failed.";
							}
						} else {
							$response['file_status'] = "No file uploaded or there was an error.";
						}
					}

					$dataJson = json_encode([$load_cells_info], JSON_PRETTY_PRINT);
				}

				// Update certificate data in the database
				if ($stmt2 = $db->prepare("UPDATE other_validations SET calibrations=? WHERE id=?")) {
					$stmt2->bind_param('ss', $dataJson, $validation_id);
					$stmt2->execute();
					$stmt2->close();
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