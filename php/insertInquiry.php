<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['userID'];

if(isset($_POST['customerType'], $_POST['machineType'], $_POST['problems'], $_POST['brand'], $_POST['dateTime'], $_POST['address1'], $_POST['model'], $_POST['picAttend']
,$_POST['address2'], $_POST['structure'], $_POST['size'], $_POST['contact'], $_POST['pic'], $_POST['capacity'], $_POST['mobile1'], $_POST['serialNo'], $_POST['email']
,$_POST['warranty'], $_POST['caseStatus'], $_POST['statusValidate'], $_POST['caseNo'], $_POST['validationBy'], $_POST['callingDate'], $_POST['validateDate']
,$_POST['callingBy'], $_POST['stampingNo'], $_POST['userContact'], $_POST['dueDate'])){
	$customerType = filter_input(INPUT_POST, 'customerType', FILTER_SANITIZE_STRING);
	$machineType = filter_input(INPUT_POST, 'machineType', FILTER_SANITIZE_STRING);
	$brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
	$address1 = filter_input(INPUT_POST, 'address1', FILTER_SANITIZE_STRING);
	$model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
	$address2 = filter_input(INPUT_POST, 'address2', FILTER_SANITIZE_STRING);
	$structure = filter_input(INPUT_POST, 'structure', FILTER_SANITIZE_STRING);
	$size = filter_input(INPUT_POST, 'size', FILTER_SANITIZE_STRING);
	$contact = filter_input(INPUT_POST, 'contact', FILTER_SANITIZE_STRING);
	$pic = filter_input(INPUT_POST, 'pic', FILTER_SANITIZE_STRING);
	$capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
	$mobile1 = filter_input(INPUT_POST, 'mobile1', FILTER_SANITIZE_STRING);
	$serialNo = filter_input(INPUT_POST, 'serialNo', FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
	$warranty = filter_input(INPUT_POST, 'warranty', FILTER_SANITIZE_STRING);
	$caseStatus = filter_input(INPUT_POST, 'caseStatus', FILTER_SANITIZE_STRING);
	$statusValidate = filter_input(INPUT_POST, 'statusValidate', FILTER_SANITIZE_STRING);
	$caseNo = filter_input(INPUT_POST, 'caseNo', FILTER_SANITIZE_STRING);
	$validationBy = filter_input(INPUT_POST, 'validationBy', FILTER_SANITIZE_STRING);
	$callingBy = filter_input(INPUT_POST, 'callingBy', FILTER_SANITIZE_STRING);
	$stampingNo = filter_input(INPUT_POST, 'stampingNo', FILTER_SANITIZE_STRING);
	$userContact = filter_input(INPUT_POST, 'userContact', FILTER_SANITIZE_STRING);
	$picAttend = filter_input(INPUT_POST, 'picAttend', FILTER_SANITIZE_STRING);
	$customerName = "";
	$address3 = null;
	$mobile2 = null;

	$date = new DateTime($_POST['dateTime']);
	$createdDateTime = date_format($date, "Y-m-d H:i:s");
	$date2 = new DateTime($_POST['callingDate']);
	$callingDate = date_format($date2, "Y-m-d H:i:s");
	$date3 = new DateTime($_POST['validateDate']);
	$validateDate = date_format($date3, "Y-m-d H:i:s");
	$date4 = new DateTime($_POST['dueDate']);
	$dueDate = date_format($date4, "Y-m-d H:i:s");

	if($customerType == "NEW"){
		$customerName = $_POST['companyText'];
	}
	else{
		$customerName = $_POST['company'];
	}

	if($_POST['address3']!=null && $_POST['address3']!=""){
		$address3 = $_POST['address3'];
	}

	if($_POST['mobile2']!=null && $_POST['mobile2']!=""){
		$mobile2 = $_POST['mobile2'];
	}
	
	$pro = $_POST['problems'];
	$problems = json_encode($pro);

	if($_POST['id'] != null && $_POST['id'] != ''){
		if ($update_stmt = $db->prepare("UPDATE inquiry SET customer_type=?, company_name=?, address1=?, address2=?, address3=?, contact_no=?, pic=?, mobile1=?, mobile2=?
		, email=?, case_status=?, case_no=?, calling_datetime=?, calling_by_cus=?, user_contact=?, machine_type=?, brand=?, model=?, structure=?, size=?, capacity=?, serial_no=?, 
		warranty=?, status_validate=?, validate_by=?, last_validate_date=?, stamping_no=?, due_date=?, issues=?, pic_attend=?, updated_by=? WHERE id=?")){
			$update_stmt->bind_param('ssssssssssssssssssssssssssssssss', $customerType, $customerName, $address1, $address2, $address3, $contact, 
			$pic, $mobile1, $mobile2, $email, $caseStatus, $caseNo, $callingDate, $callingBy, $userContact, $machineType, $brand, $model, $structure,
			$size, $capacity, $serialNo, $warranty, $statusValidate, $validationBy, $validateDate, $stampingNo, $dueDate, $problems, $picAttend, $uid, $_POST['id']);
		
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
					"message"=> $insert_stmt->error
				)
			);
		}
	}
	else{
		if ($insert_stmt = $db->prepare("INSERT INTO inquiry (customer_type, company_name, address1, address2, address3, contact_no, pic, mobile1,
		mobile2, email, case_status, case_no, calling_datetime, calling_by_cus, user_contact, machine_type, brand, model, structure, size, capacity, 
		serial_no, warranty, status_validate, validate_by, last_validate_date, stamping_no, due_date, issues, pic_attend, created_datetime, created_by,
		updated_by) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
			$insert_stmt->bind_param('sssssssssssssssssssssssssssssssss', $customerType, $customerName, $address1, $address2, $address3, $contact, 
			$pic, $mobile1, $mobile2, $email, $caseStatus, $caseNo, $callingDate, $callingBy, $userContact, $machineType, $brand, $model, $structure,
			$size, $capacity, $serialNo, $warranty, $statusValidate, $validationBy, $validateDate, $stampingNo, $dueDate, $problems, $picAttend,
			$createdDateTime, $uid, $uid);
			
			// Execute the prepared query.
			if (! $insert_stmt->execute()){
				echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> $insert_stmt->error
					)
				);
			} 
			else{
				$insert_stmt->close();
				$db->close();
				
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Added Successfully!!" 
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