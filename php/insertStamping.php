<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['userID'];

if(isset($_POST['customer'], $_POST['brand'], $_POST['model'], $_POST['machineType'], $_POST['capacity'], $_POST['validator']
, $_POST['serial'], $_POST['dueDate'])){
	$customer = filter_input(INPUT_POST, 'customer', FILTER_SANITIZE_STRING);
	$brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
	$model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
	$machineType = filter_input(INPUT_POST, 'machineType', FILTER_SANITIZE_STRING);
	$capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
	$validator = filter_input(INPUT_POST, 'validator', FILTER_SANITIZE_STRING);
	$serial = filter_input(INPUT_POST, 'serial', FILTER_SANITIZE_STRING);
	$dueDate = filter_input(INPUT_POST, 'dueDate', FILTER_SANITIZE_STRING);
	$dueDate = DateTime::createFromFormat('d/m/Y', $dueDate)->format('Y-m-d H:i:s');

	$stamping = null;
	$stampDate = null;
	$invoice = null;
	$pic = null;
	$followUpDate = null;
	$quotation = null;
	$remark = null;

	if(isset($_POST['stamping']) && $_POST['stamping']!=null && $_POST['stamping']!=""){
		$stamping = $_POST['stamping'];
	}

	if(isset($_POST['stampDate']) && $_POST['stampDate']!=null && $_POST['stampDate']!=""){
		$stampDate = $_POST['stampDate'];
		$stampDate = DateTime::createFromFormat('d/m/Y', $stampDate)->format('Y-m-d H:i:s');
	}
	
	if(isset($_POST['invoice']) && $_POST['invoice']!=null && $_POST['invoice']!=""){
		$invoice = $_POST['invoice'];
	}

	if(isset($_POST['pic']) && $_POST['pic']!=null && $_POST['pic']!=""){
		$pic = $_POST['pic'];
	}

	if(isset($_POST['followUpDate']) && $_POST['followUpDate']!=null && $_POST['followUpDate']!=""){
		$followUpDate = $_POST['followUpDate'];
		$followUpDate = DateTime::createFromFormat('d/m/Y', $followUpDate)->format('Y-m-d H:i:s');
	}

	if(isset($_POST['quotation']) && $_POST['quotation']!=null && $_POST['quotation']!=""){
		$quotation = $_POST['quotation'];
	}

	if(isset($_POST['remark']) && $_POST['remark']!=null && $_POST['remark']!=""){
		$remark = $_POST['remark'];
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		if ($update_stmt = $db->prepare("UPDATE stamping SET customers=?, brand=?, descriptions=?, model=?, capacity=?, serial_no=?
		, validate_by=?, stamping_no=?, invoice_no=?, stamping_date=?, due_date=?, pic=?, customer_pic=?, follow_up_date=?, quotation_no=?
		, remarks=? WHERE id=?")){
			$update_stmt->bind_param('sssssssssssssssss', $customer, $brand, $machineType, $model, $capacity, $serial, 
			$validator, $stamping, $invoice, $stampDate, $dueDate, $uid, $pic, $followUpDate, $quotation, $remark, $_POST['id']);
		
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
					"message"=> "Error when creating query"
				)
			);
		}
	}
	else{
		if ($insert_stmt = $db->prepare("INSERT INTO stamping (customers, brand, descriptions, model, capacity, serial_no, 
		validate_by, stamping_no, invoice_no, stamping_date, due_date, pic, customer_pic, follow_up_date, quotation_no, remarks) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
			$insert_stmt->bind_param('ssssssssssssssss', $customer, $brand, $machineType, $model, $capacity, $serial, 
			$validator, $stamping, $invoice, $stampDate, $dueDate, $uid, $pic, $followUpDate, $quotation, $remark);
			
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