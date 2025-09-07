<?php
require_once 'db_connect.php';

session_start();

$uid = '';

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
}else{
    $uid = $_SESSION['userID'];
}

if(isset($_POST['duplicateNo'], $_POST['id'])){
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $duplicateNo = filter_input(INPUT_POST, 'duplicateNo', FILTER_SANITIZE_STRING);
    $allSuccess = false; 

    if ($select_stmt = $db->prepare("SELECT * FROM inhouse_validations WHERE id = ?")) {
        $select_stmt->bind_param('s', $id);

        if (!$select_stmt->execute()) {
            $select_stmt->close();
			$db->close();
			
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Failed to get records"
                )); 
        }
        else{
            $result = $select_stmt->get_result();
            $message = array();

            $select_stmt->close();
            
            if ($record = $result->fetch_assoc()) {
                // Prepare insert statement for other_validations
                $insertQuery = "INSERT INTO inhouse_validations (type, company_branch, dealer, dealer_branch, validate_by, customer_type, customer, branch, auto_form_no, machines, unit_serial_no, expired_date, manufacturing, auto_cert_no, brand, model, capacity, size, calibrator, calibrator2, calibrator3, status, validation_date, duplicate) 
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $insertStmt = $db->prepare($insertQuery);

                // Prepare insert statement for other_validation_log
                $insertLogQuery = "INSERT INTO inhouse_log (
                    action, user_id, item_id
                ) VALUES (?, ?, ?)";

                $insertLogStmt = $db->prepare($insertLogQuery);

                for($i=0; $i<(int)$duplicateNo; $i++){
                    $insertStmt->execute([
                        $record['type'], $record['company_branch'], $record['dealer'], $record['dealer_branch'], $record['validate_by'], $record['customer_type'], $record['customer'], $record['branch'], $record['auto_form_no'], $record['machines'], $record['unit_serial_no'], null, $record['manufacturing'], $record['auto_cert_no'], $record['brand'], $record['model'], $record['capacity'], $record['size'], null, null, null, 'Pending', null,'Y'
                    ]);

                    // Get the last inserted ID
                    $newValidationId = $insertStmt->insert_id;
                    $action = "INSERT";
                    $insertLogStmt->execute([
                        $action, $uid, $newValidationId
                    ]);

                }

                $insertStmt->close();
                $insertLogStmt->close();
				
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Added Successfully!!" 
					)
				);
            }

            $db->close();
        }
    }
    else{
        $db->close();

        echo json_encode(
            array(
                "status"=> "failed", 
                "message"=> "Something went wrong!"
            )
        );
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