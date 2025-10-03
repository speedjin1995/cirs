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
                                "code" => $row2['branch_code'] ?? '',
                                "name" => $row2['branch_name'] ?? '',
                                "branch_address1" => $row2['address'] ?? '',
                                "branch_address2" => $row2['address2'] ?? '',
                                "branch_address3" => $row2['address3'] ?? '',
                                "branch_address4" => $row2['address4'] ?? '',
                                "branch_address5" => $row2['address5'] ?? '',
                                "map_url" => $row2['map_url'] ?? '',
                                "pic" => $row2['pic'] ?? '',
                                "pic_contact" => $row2['pic_contact'] ?? '',
                                "office_no" => $row2['office_no'] ?? '',
                                "email" => $row2['email'] ?? '',
                                "address1" => $row2['address'] ?? '',
                                "address2" => $row2['address2'] ?? '',
                                "address3" => $row2['address3'] ?? '',
                                "address4" => $row2['address4'] ?? '',
                            );
                        }
                    }
                    $update_stmt2->close();
                }

                $message['id'] = $row['id'];
                $message['dealer'] = $row['dealer'];
                $message['customer_code'] = $row['customer_code'];
                $message['other_code'] = $row['other_code'];
                $message['customer_name'] = $row['customer_name'];
                $message['customer_address'] = $row['customer_address'];
                $message['address2'] = $row['address2'];
                $message['address3'] = $row['address3'];
                $message['address4'] = $row['address4'];
                $message['address5'] = $row['address5'];
                $message['customerMapUrl'] = $row['map_url'];
                $message['customer_phone'] = $row['customer_phone'];
                $message['customer_email'] = $row['customer_email'];
                $message['pic'] = $row['pic'];
                $message['pic_contact'] = $row['pic_contact'];
                $message['pricing'] = $pricing;
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
        }

        $update_stmt->close();
    }

    $db->close();
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>