<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM stamping WHERE id=?")) {
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
            
            while ($row = $result->fetch_assoc()) {
                $message['id'] = $row['id'];
                $message['customers'] = $row['customers'];
                $message['brand'] = $row['brand'];
                $message['descriptions'] = $row['descriptions'];
                $message['model'] = $row['model'];
                $message['capacity'] = $row['capacity'];
                $message['serial_no'] = $row['serial_no'];
                $message['validate_by'] = $row['validate_by'];
                $message['stamping_no'] = $row['stamping_no'];
                $message['invoice_no'] = $row['invoice_no'];
                $message['stamping_date'] = $row['stamping_date'];
                $message['due_date'] = $row['due_date'];
                $message['pic'] = $row['pic'];
                $message['customer_pic'] = $row['customer_pic'];
                $message['follow_up_date'] = $row['follow_up_date'];
                $message['quotation_no'] = $row['quotation_no'];
                $message['remarks'] = $row['remarks'];
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