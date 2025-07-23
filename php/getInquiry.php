<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM inquiry WHERE id=?")) {
        $update_stmt->bind_param('s', $id);
        
        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            $update_stmt->close();
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
                if($row['calling_datetime'] == null || $row['calling_datetime'] == ''){
                    $dateTime = '-';
                }
                else{
                    $convertDate = new DateTime($row['calling_datetime']);
                    $calling_datetime = date_format($convertDate, "d/m/Y H:i:s A");
                }

                if($row['last_validate_date'] == null || $row['last_validate_date'] == ''){
                    $last_validate_date = '-';
                }
                else{
                    $convertDate2 = new DateTime($row['last_validate_date']);
                    $last_validate_date = date_format($convertDate2, "d/m/Y H:i:s A");
                }

                if($row['due_date'] == null || $row['due_date'] == ''){
                    $due_date = '-';
                }
                else{
                    $convertDate3 = new DateTime($row['due_date']);
                    $due_date = date_format($convertDate3, "d/m/Y H:i:s A");
                }

                if($row['created_datetime'] == null || $row['created_datetime'] == ''){
                    $created_datetime = '-';
                }
                else{
                    $convertDate4 = new DateTime($row['created_datetime']);
                    $created_datetime = date_format($convertDate4, "d/m/Y H:i:s A");
                }

                $message['id'] = $row['id'];
                $message['customer_type'] = $row['customer_type'];
                $message['company_name'] = $row['company_name'];
                $message['address1'] = $row['address1'];
                $message['address2'] = $row['address2'];
                $message['address3'] = $row['address3'];
                $message['contact_no'] = $row['contact_no'];
                $message['pic'] = $row['pic'];
                $message['mobile1'] = $row['mobile1'];
                $message['mobile2'] = $row['mobile2'];
                $message['email'] = $row['email'];
                $message['case_status'] = $row['case_status'];
                $message['calling_datetime'] = $calling_datetime;
                $message['calling_by_cus'] = $row['calling_by_cus'];
                $message['user_contact'] = $row['user_contact'];
                $message['machine_type'] = $row['machine_type'];
                $message['brand'] = $row['brand'];
                $message['model'] = $row['model'];
                $message['structure'] = $row['structure'];
                $message['size'] = $row['size'];
                $message['capacity'] = $row['capacity'];
                $message['serial_no'] = $row['serial_no'];
                $message['warranty'] = $row['warranty'];
                $message['status_validate'] = $row['status_validate'];
                $message['case_no'] = $row['case_no'];
                $message['validate_by'] = $row['validate_by'];
                $message['last_validate_date'] = $last_validate_date;
                $message['stamping_no'] = $row['stamping_no'];
                $message['due_date'] = $due_date;
                $message['issues'] = $row['issues'];
                $message['pic_attend'] = $row['pic_attend'];
                $message['created_datetime'] = $created_datetime;
            }
            $update_stmt->close();
            
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