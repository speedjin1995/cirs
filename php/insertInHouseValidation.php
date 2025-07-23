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

if(isset($_POST['type'], $customerType, $_POST['validator'], $_POST['address1'], $_POST['machineType'], $_POST['serial'], $_POST['expiredDate'], $_POST['manufacturing'], $_POST['auto_cert_no'], $_POST['brand'], $_POST['model'], $_POST['capacity'], $_POST['size'], $_POST['calibrator'], $_POST['alatHidden'], $_POST['validationDate'])){
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
	// $customerType = filter_input(INPUT_POST, 'customerType', FILTER_SANITIZE_STRING);
	$validator = filter_input(INPUT_POST, 'validator', FILTER_SANITIZE_STRING);
	$address1 = filter_input(INPUT_POST, 'address1', FILTER_SANITIZE_STRING);
	$machineType = filter_input(INPUT_POST, 'machineType', FILTER_SANITIZE_STRING);
	$serial = filter_input(INPUT_POST, 'serial', FILTER_SANITIZE_STRING);
	$manufacturing = filter_input(INPUT_POST, 'manufacturing', FILTER_SANITIZE_STRING);
	$autoCertNo = filter_input(INPUT_POST, 'auto_cert_no', FILTER_SANITIZE_STRING);
	$brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
	$model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
	$capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
	$size = filter_input(INPUT_POST, 'size', FILTER_SANITIZE_STRING);
	$calibrator = filter_input(INPUT_POST, 'calibrator', FILTER_SANITIZE_STRING);
	$alat = filter_input(INPUT_POST, 'alatHidden', FILTER_SANITIZE_STRING);

	if(isset($_POST['validationDate']) && $_POST['validationDate']!=null && $_POST['validationDate']!=""){
		$validationDate = $_POST['validationDate'];
		$validationDate = DateTime::createFromFormat('d/m/Y', $validationDate)->format('Y-m-d H:i:s');
	}

	if(isset($_POST['expiredDate']) && $_POST['expiredDate']!=null && $_POST['expiredDate']!=""){
		$expiredDate = $_POST['expiredDate'];
		$expiredDate = DateTime::createFromFormat('d/m/Y', $expiredDate)->format('Y-m-d H:i:s');
	}

	$dealer = null;
	$reseller_branch = null;
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
		if ($select_stmt = $db->prepare("SELECT id FROM customers WHERE customer_name=? and deleted = '0'")) {
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
				# to generate customer code
				$custNameFirstLetter = substr($companyText, 0, 1);
				$firstChar = $custNameFirstLetter;
				$code = 'C-'.strtoupper($custNameFirstLetter);

				$customerQuery = "SELECT * FROM customers WHERE customer_code LIKE '%$code%' ORDER BY customer_code DESC";
				$customerDetail = mysqli_query($db, $customerQuery);
				$customerRow = mysqli_fetch_assoc($customerDetail);
		
				$customerCode = null;
				$codeSeq = null;
				$count = '';
		
				if(!empty($customerRow)){
					$customerCode = $customerRow['customer_code'];
					preg_match('/\d+/', $customerCode, $matches);
					$codeSeq = (int)$matches[0]; 
					$nextSeq = $codeSeq+1;
					$count = str_pad($nextSeq, 4, '0', STR_PAD_LEFT); 
					$code.=$count;
				}
				else{
					$nextSeq = 1;
					$count = str_pad($nextSeq, 4, '0', STR_PAD_LEFT); 
					$code.=$count;
				}

				// Customer does not exist, create a new customer
				if ($insert_stmt = $db->prepare("INSERT INTO customers (customer_name, customer_code, customer_address, address2, address3, address4, customer_phone, customer_email, customer_status, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
					$customer_status = 'CUSTOMERS';
					$insert_stmt->bind_param('sssssssssss', $_POST['companyText'], $code, $address1, $address2, $address3, $address4, $phone, $email, $customer_status, $pic, $contact);
					
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
				} else {
					echo json_encode(
						array(
							"status"=> "failed", 
							"message"=> $insert_stmt->error
						)
					);
				}
				$insert_stmt->close();
			}
			$select_stmt->close();
		}
	}
	else{
		$customer = $_POST['company'];
		$customerType = 'EXISTING';
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		//Updated datetime
		$currentDateTime = date('Y-m-d H:i:s');
		if ($update_stmt = $db->prepare("UPDATE inhouse_validations SET type=?, dealer=?, dealer_branch=?, validate_by=?, customer_type=?, customer=?, branch=?, machines=?, jenis_alat=?, unit_serial_no=?, expired_date=?, manufacturing=?, auto_cert_no=?, brand=?
		, model=?, capacity=?, size=?, calibrator=?, validation_date=?, update_datetime=? WHERE id=?")){
			$data = json_encode($logs);
			$update_stmt->bind_param('sssssssssssssssssssss', $type, $dealer, $reseller_branch, $validator, $customerType, $customer, $branch, $machineType, $alat, $serial, $expiredDate, $manufacturing, $autoCertNo, $brand, $model, $capacity, $size, $calibrator, $validationDate, $currentDateTime, $_POST['id']);

			// Execute the prepared query.
			if (! $update_stmt->execute()){
				$response['status'] = "failed";
    			$response['message'] = $update_stmt->error;
			} 
			else{
				for($i=1; $i<=10; $i++){
					$loadTestings[] = array(
						"no" => $_POST["no$i"] != '' ? $_POST["no$i"] : '0.0',
						"standardValue" => $_POST["standardValue$i"] != '' ? $_POST["standardValue$i"] : '0.0',
						"calibrationReceived" => $_POST["calibrationReceived$i"] != '' ? $_POST["calibrationReceived$i"] : '0.0',
						"variance" =>  $_POST["variance$i"] != '' ? $_POST["variance$i"] : '0.0',
						"afterAdjustReading" => $_POST["afterAdjustReading$i"] != '' ? $_POST["afterAdjustReading$i"] : '0.0',
					);
				}

				// Update certificate data in the database
				if ($stmt2 = $db->prepare("UPDATE inhouse_validations SET tests=? WHERE id=?")) {
					$data = json_encode([$loadTestings], JSON_PRETTY_PRINT);
					$stmt2->bind_param('ss', $data, $_POST['id']);
					$stmt2->execute();
					$stmt2->close();
				} 

				// UPDATE Inhouse Validation System Log
				if ($insert_stmt3 = $db->prepare("INSERT INTO inhouse_log (action, user_id, item_id) 
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
		if($misc_stmt = $db->prepare("SELECT * FROM miscellaneous WHERE code='inhouse'")){
			if(!$misc_stmt->execute()){
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => "Something went wrong querying miscellaneous"
                    )); 
            }else{
                $result = $misc_stmt->get_result();
                while ($row = $result->fetch_assoc()){
					$description = $row['description'];
					$charSize = strlen($row['value']);
                    $misValue = $row['value'];
                    
                    $autoFormNo = $description.'-';
                    for($i=0; $i<(5-(int)$charSize); $i++){
                        $autoFormNo.='0';  // S0000
                    }

                    $autoFormNo.=$misValue;

					if ($insert_stmt = $db->prepare("INSERT INTO inhouse_validations (type, dealer, dealer_branch, validate_by, customer_type, customer, branch, auto_form_no, machines, jenis_alat, unit_serial_no, expired_date, manufacturing, auto_cert_no, brand, model, capacity, size, calibrator, status, validation_date) 
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
						$data = json_encode($logs);
						$loadTestings = null;
						$status = 'Pending';
						$insert_stmt->bind_param('sssssssssssssssssssss', $type, $dealer, $reseller_branch, $validator, $customerType, $customer, $branch, $autoFormNo, $machineType, $alat, $serial, $expiredDate, $manufacturing, $autoCertNo, $brand, $model, $capacity, $size, $calibrator, $status, $validationDate);
						
						// Execute the prepared query.
						if (! $insert_stmt->execute()){
							$response['status'] = "failed";
							$response['message'] = $insert_stmt->error;
						} 
						else{
							$validation_id = $insert_stmt->insert_id;
							for($i=1; $i<=10; $i++){
								$loadTestings[] = array(
									"no" => $_POST["no$i"] != '' ? $_POST["no$i"] : '0.0',
									"standardValue" => $_POST["standardValue$i"] != '' ? $_POST["standardValue$i"] : '0.0',
									"calibrationReceived" => $_POST["calibrationReceived$i"] != '' ? $_POST["calibrationReceived$i"] : '0.0',
									"variance" =>  $_POST["variance$i"] != '' ? $_POST["variance$i"] : '0.0',
									"afterAdjustReading" => $_POST["afterAdjustReading$i"] != '' ? $_POST["afterAdjustReading$i"] : '0.0',
								);
							}

							// Update certificate data in the database
							if ($stmt2 = $db->prepare("UPDATE inhouse_validations SET tests=? WHERE id=?")) {
								$data = json_encode([$loadTestings], JSON_PRETTY_PRINT);
								$stmt2->bind_param('ss', $data, $validation_id);
								$stmt2->execute();
								$stmt2->close();
							} 

							#Update miscellaneous value
                            $misValue++;

                            if($updmisc_stmt = $db->prepare("UPDATE miscellaneous SET value=? WHERE code='inhouse'")){
                                $updmisc_stmt->bind_param('s', $misValue);
								$updmisc_stmt->execute();
								$updmisc_stmt->close();
							}

							// Insert Inhouse Validation System Log
							if ($insert_stmt3 = $db->prepare("INSERT INTO inhouse_log (action, user_id, item_id) 
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
				}
				$misc_stmt->close();
			}
		}
	}
} 
else{
	$response['status'] = "failed";
	$response['message'] = "Please fill in all the fields";
}

echo json_encode($response);

?>