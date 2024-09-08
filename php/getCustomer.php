<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM customers WHERE id=?")) {
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
                $pricing = array();

                if ($update_stmt2 = $db->prepare("SELECT * FROM branches WHERE customer_id=? AND deleted = '0'")) {
                    $update_stmt2->bind_param('s', $row['id']);

                    if($update_stmt2->execute()) {
                        $result2 = $update_stmt2->get_result();

                        while($row2 = $result2->fetch_assoc()) {
                            $pricing[] = array(
                                "branchid" => $row2['id'],
                                "address1" => $row2['address'],
                                "address2" => $row2['address2'],
                                "address3" => $row2['address3'],
                                "address4" => $row2['address4'],
                            );
                        }
                    }
                }

                $message['id'] = $row['id'];
                $message['dealer'] = $row['dealer'];
                $message['customer_code'] = $row['customer_code'];
                $message['customer_name'] = $row['customer_name'];
                $message['customer_phone'] = $row['customer_phone'];
                $message['customer_email'] = $row['customer_email'];
                $message['pricing'] = $pricing;
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