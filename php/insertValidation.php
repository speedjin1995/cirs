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
	// $pinKeselamatan = null;
	// $attnTo = null;
	// $siriKeselamatan = null;
	// $borangD = null;
	// $cashBill = null;
	// $invoice = null;
	// $pic = null;
	// $followUpDate = null;
	// $quotation = null;
	// $quotationDate = null;
	// $remark = null;
	// $customer = "";
	// $includeCert = "NO";
	// $poNo = null;
	// $poDate = null;
	// $unitPrice = '0.00';
	// $certPrice = '0.00';
	// $totalPrice = '0.00';
	// $sst = '0.00';
	// $subtoalPrice = '0.00';

	$logs = array();

	// if(isset($_POST['product']) && $_POST['product']!=null && $_POST['product']!=""){
	// 	$product = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_STRING);
	// }
	// else{
	// 	if ($select_stmtP = $db->prepare("SELECT id FROM products WHERE machine_type=? AND jenis_alat=? AND capacity=? AND validator=?")) {
	// 		$select_stmtP->bind_param('ssss', $machineType, $jenisAlat, $capacity, $validator);
	// 		$select_stmtP->execute();
	// 		$resultP = $select_stmtP->get_result();
        
	// 		if ($rowP = $resultP->fetch_assoc()) {
	// 			$product = $rowP['id'];
	// 		} 
	// 		else {
	// 			if(isset($_POST['unitPrice']) && $_POST['unitPrice']!=null && $_POST['unitPrice']!="" && $_POST['unitPrice']!="0.00"){
	// 				// Customer does not exist, create a new customer
	// 				if ($insert_stmtP = $db->prepare("INSERT INTO products (name, machine_type, jenis_alat, capacity, validator, price) VALUES (?, ?, ?, ?, ?, ?)")) {
	// 					$pname = 'product'.$machineType.$jenisAlat.$capacity.$validator;
	// 					$insert_stmtP->bind_param('ssssss', $pname , $machineType, $jenisAlat, $capacity, $validator, $_POST['unitPrice']);
						
	// 					if ($insert_stmtP->execute()) {
	// 						$product = $insert_stmtP->insert_id;
	// 					} 
	// 				}
	// 			}
	// 		}
	// 	}
	// }

	// if(isset($_POST['dealer']) && $_POST['dealer']!=null && $_POST['dealer']!=""){
	// 	$dealer = $_POST['dealer'];
	// }

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
				// $email = null;
				// $phone = null;
				// $dealer = null;
				$branchName = '';
				$mapUrl = '';

				// if(isset($_POST['dealer'] ) && $_POST['dealer'] != null && $_POST['dealer'] != "" && $type == 'DEALER'){
				// 	$dealer = filter_input(INPUT_POST, 'dealer', FILTER_SANITIZE_STRING);
				// }

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

	// if(isset($_POST['stamping']) && $_POST['stamping']!=null && $_POST['stamping']!=""){
	// 	$stamping = $_POST['stamping'];
	// }

	// if(isset($_POST['stampDate']) && $_POST['stampDate']!=null && $_POST['stampDate']!=""){
	// 	$stampDate = $_POST['stampDate'];
	// 	$stampDate = DateTime::createFromFormat('d/m/Y', $stampDate)->format('Y-m-d H:i:s');
	// }
	
	// if(isset($_POST['noDaftar']) && $_POST['noDaftar']!=null && $_POST['noDaftar']!=""){
	// 	$noDaftar = $_POST['noDaftar'];
	// }

	// if(isset($_POST['pinKeselamatan']) && $_POST['pinKeselamatan']!=null && $_POST['pinKeselamatan']!=""){
	// 	$pinKeselamatan = $_POST['pinKeselamatan'];
	// }

	// if(isset($_POST['attnTo']) && $_POST['attnTo']!=null && $_POST['attnTo']!=""){
	// 	$attnTo = $_POST['attnTo'];
	// }

	// if(isset($_POST['siriKeselamatan']) && $_POST['siriKeselamatan']!=null && $_POST['siriKeselamatan']!=""){
	// 	$siriKeselamatan = $_POST['siriKeselamatan'];
	// }
	
	// if(isset($_POST['pic']) && $_POST['pic']!=null && $_POST['pic']!=""){
	// 	$pic = $_POST['pic'];
	// }

	// if(isset($_POST['borangD']) && $_POST['borangD']!=null && $_POST['borangD']!=""){
	// 	$borangD = $_POST['borangD'];
	// }

	// if(isset($_POST['remark']) && $_POST['remark']!=null && $_POST['remark']!=""){
	// 	$remark = $_POST['remark'];
	// }

	// if(isset($_POST['dueDate']) && $_POST['dueDate']!=null && $_POST['dueDate']!=""){
	// 	$dueDate = $_POST['dueDate'];
	// 	$dueDate = DateTime::createFromFormat('d/m/Y', $dueDate)->format('Y-m-d H:i:s');
	// }

	// if(isset($_POST['quotation']) && $_POST['quotation']!=null && $_POST['quotation']!=""){
	// 	$quotation = $_POST['quotation'];
	// }

	// if(isset($_POST['quotationDate']) && $_POST['quotationDate']!=null && $_POST['quotationDate']!=""){
	// 	$quotationDate = $_POST['quotationDate'];
	// 	$quotationDate = DateTime::createFromFormat('d/m/Y', $quotationDate)->format('Y-m-d H:i:s');
	// }

	// if(isset($_POST['includeCert']) && $_POST['includeCert']!=null && $_POST['includeCert']!=""){
	// 	$includeCert = $_POST['includeCert'];
	// }

	// if(isset($_POST['poNo']) && $_POST['poNo']!=null && $_POST['poNo']!=""){
	// 	$poNo = $_POST['poNo'];
	// }

	// if(isset($_POST['poDate']) && $_POST['poDate']!=null && $_POST['poDate']!=""){
	// 	$poDate = $_POST['poDate'];
	// 	$poDate = DateTime::createFromFormat('d/m/Y', $poDate)->format('Y-m-d H:i:s');
	// }

	// if(isset($_POST['cashBill']) && $_POST['cashBill']!=null && $_POST['cashBill']!=""){
	// 	$cashBill = $_POST['cashBill'];
	// }

	// if(isset($_POST['invoice']) && $_POST['invoice']!=null && $_POST['invoice']!=""){
	// 	$invoice = $_POST['invoice'];
	// }

	// if(isset($_POST['unitPrice']) && $_POST['unitPrice']!=null && $_POST['unitPrice']!=""){
	// 	$unitPrice = $_POST['unitPrice'];
	// }

	// if(isset($_POST['certPrice']) && $_POST['certPrice']!=null && $_POST['certPrice']!=""){
	// 	$certPrice = $_POST['certPrice'];
	// }

	// if(isset($_POST['totalAmount']) && $_POST['totalAmount']!=null && $_POST['totalAmount']!=""){
	// 	$totalPrice = $_POST['totalAmount'];
	// }

	// if(isset($_POST['sst']) && $_POST['sst']!=null && $_POST['sst']!=""){
	// 	$sst = $_POST['sst'];
	// }

	// if(isset($_POST['subAmount']) && $_POST['subAmount']!=null && $_POST['subAmount']!=""){
	// 	$subtotalPrice = $_POST['subAmount'];
	// }

	// if(isset($_POST['certPrice']) && $_POST['certPrice']!=null && $_POST['certPrice']!=""){
	// 	$certPrice = $_POST['certPrice'];
	// }

	// if(isset($_POST['totalAmount']) && $_POST['totalAmount']!=null && $_POST['totalAmount']!=""){
	// 	$totalPrice = $_POST['totalAmount'];
	// }

	// if(isset($_POST['sst']) && $_POST['sst']!=null && $_POST['sst']!=""){
	// 	$sst = $_POST['sst'];
	// }

	// if(isset($_POST['subAmount']) && $_POST['subAmount']!=null && $_POST['subAmount']!=""){
	// 	$subtotalPrice = $_POST['subAmount'];
	// }

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		//Updated datetime
		$currentDateTime = date('Y-m-d H:i:s');

		if ($update_stmt = $db->prepare("UPDATE stamping SET type=?, dealer=?, customers=?, address1=?, address2=?, address3=?, brand=?, machine_type=?, model=?
		, capacity=?, serial_no=?, validate_by=?, jenis_alat=?, no_daftar=?, pin_keselamatan=?, siri_keselamatan=?, include_cert=?, borang_d=?
		, invoice_no=?, cash_bill=?, stamping_date=?, due_date=?, pic=?, customer_pic=?, quotation_no=?, quotation_date=?, purchase_no=?, purchase_date=?
		, remarks=?, unit_price=?, cert_price=?, total_amount=?, sst=?, subtotal_amount=?, log=?, products=?, stamping_type=?, updated_datetime=?, branch=? WHERE id=?")){
			$data = json_encode($logs);
			$update_stmt->bind_param('ssssssssssssssssssssssssssssssssssssssss', $type, $dealer, $customer, $address1, $address2, $address3, $brand, $machineType, $model, $capacity, $serial, 
			$validator, $jenisAlat, $noDaftar, $pinKeselamatan, $siriKeselamatan, $includeCert, $borangD, $invoice, $cashBill, $stampDate, $dueDate, $uid, $pic, 
			$quotation, $quotationDate, $poNo, $poDate, $remark, $unitPrice, $certPrice, $totalPrice, $sst, $subtotalPrice, $data, $product, $newRenew, $currentDateTime, $branch, $_POST['id']);
		
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
				// For ATK Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '1'){
					$penentusan_baru = null;
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

					$no = $_POST['no'];
					$loadCells = $_POST['loadCells'];
					$loadCellBrand = $_POST['loadCellBrand'];
					$loadCellModel = $_POST['loadCellModel'];
					$loadCellCapacity = $_POST['loadCellCapacity'];
					$loadCellSerial = $_POST['loadCellSerial'];

					if(isset($no) && $no != null && count($no) > 0){
						for($i=0; $i<count($no); $i++){
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

					if(isset($_POST['penentusanBaru']) && $_POST['penentusanBaru']!=null && $_POST['penentusanBaru']!=""){
						$penentusan_baru = $_POST['penentusanBaru'];
					}
				
					if(isset($_POST['penentusanSemula']) && $_POST['penentusanSemula']!=null && $_POST['penentusanSemula']!=""){
						$penentusan_semula = $_POST['penentusanSemula'];
					}
				
					if(isset($_POST['kelulusanMSPK']) && $_POST['kelulusanMSPK']!=null && $_POST['kelulusanMSPK']!=""){
						$kelulusan_mspk = $_POST['kelulusanMSPK'];
					}
				
					if(isset($_POST['noMSPK']) && $_POST['noMSPK']!=null && $_POST['noMSPK']!=""){
						$no_kelulusan = $_POST['noMSPK'];
					}
				
					if(isset($_POST['noSerialIndicator']) && $_POST['noSerialIndicator']!=null && $_POST['noSerialIndicator']!=""){
						$indicator_serial = $_POST['noSerialIndicator'];
					}

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}
				
					if(isset($_POST['platformType']) && $_POST['platformType']!=null && $_POST['platformType']!=""){
						$platform_type = $_POST['platformType'];
					}
				
					if(isset($_POST['size']) && $_POST['size']!=null && $_POST['size']!=""){
						$size = $_POST['size'];
					}

					if(isset($_POST['jenisPelantar']) && $_POST['jenisPelantar']!=null && $_POST['jenisPelantar']!=""){
						$jenis_pelantar = $_POST['jenisPelantar'];
					}
				
					if(isset($_POST['others']) && $_POST['others']!=null && $_POST['others']!=""){
						$others = $_POST['others'];
					}
				
					if(isset($_POST['loadCellCountry']) && $_POST['loadCellCountry']!=null && $_POST['loadCellCountry']!=""){
						$load_cell_country = $_POST['loadCellCountry'];
					}

					if(isset($_POST['noOfLoadCell']) && $_POST['noOfLoadCell']!=null && $_POST['noOfLoadCell']!=""){
						$load_cell_no = $_POST['noOfLoadCell'];
					}

					if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET penentusan_baru = ?, penentusan_semula = ?, kelulusan_mspk = ?, no_kelulusan = ?, indicator_serial = ?, platform_country = ?, platform_type = ?, 
					size = ?, jenis_pelantar = ?, other_info = ?, load_cell_country = ?, load_cell_no = ?, load_cells_info = ? WHERE stamp_id = ?")){
						$data = json_encode($load_cells_info);
						$insert_stmt2->bind_param('ssssssssssssss', $penentusan_baru, $penentusan_semula, $kelulusan_mspk, $no_kelulusan, $indicator_serial, $platform_country, $platform_type, 
						$size, $jenis_pelantar, $others, $load_cell_country, $load_cell_no, $data, $_POST['id']);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}
				
				$update_stmt->close();
				$db->close();
				
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Updated Successfully!!" 
					)
				);
			}
		}
		else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> "Error when creating query"
				)
			);
		}
	}
	else{
		if ($insert_stmt = $db->prepare("INSERT INTO other_validations (validate_by, customer, branch, auto_form_no, machines, unit_serial_no, manufacturing, brand, model, capacity, size, status) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
			$data = json_encode($logs);
			$calibrations = null;
			$status = 'Pending';
			$insert_stmt->bind_param('ssssssssssss', $validator, $customer, $branch, $autoFormNo,$machineType, $serial, $manufacturing, $brand, $model, 
			$capacity, $size, $status);
			
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