<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['validationId'])){
	$id = filter_input(INPUT_POST, 'validationId', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM inhouse_validations WHERE id=?")) {
        $update_stmt->bind_param('s', $id);
        
        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                )); 
        }
        else{
            $result = $update_stmt->get_result();
            $message = array();
            
            if ($row = $result->fetch_assoc()) {
                $message['id'] = $row['id'];
                $message['type'] = $row['type'];
                $message['dealer'] = $row['dealer'];
                $message['dealer_branch'] = $row['dealer_branch'];
                $message['validate_by'] = $row['validate_by'];
                $message['customer_type'] = $row['customer_type'];
                $message['customer'] = $row['customer'];
                $message['branch'] = $row['branch'];
                $message['auto_form_no'] = $row['auto_form_no'];
                $message['machines'] = $row['machines'];
                $message['unit_serial_no'] = $row['unit_serial_no'];
                $message['manufacturing'] = $row['manufacturing'];
                $message['brand'] = $row['brand'];
                $message['model'] = $row['model'];
                $message['capacity'] = $row['capacity'];
                $message['size'] = $row['size'];
                $message['calibrator'] = $row['calibrator'];
                $message['last_calibration_date'] = $row['last_calibration_date'];
                $message['expired_date'] = $row['expired_date'];
                $message['auto_cert_no'] = $row['auto_cert_no'];
                $message['tests'] = ($row['tests'] != null) ? json_decode($row['tests'], true) : [];
                $message['validation_date'] = $row['validation_date'];
                $message['status'] = $row['status'];
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
        }
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>