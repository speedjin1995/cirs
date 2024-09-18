<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM dealer WHERE id=?")) {
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
                $branches = array();

                if ($update_stmt2 = $db->prepare("SELECT * FROM reseller_branches WHERE reseller_id=? AND deleted = '0'")) {
                    $update_stmt2->bind_param('s', $row['id']);

                    if($update_stmt2->execute()) {
                        $result2 = $update_stmt2->get_result();

                        while($row2 = $result2->fetch_assoc()) {
                            $branches[] = array(
                                "branchid" => $row2['id'],
                                "name" => $row2['branch_name'] ?? '',
                                "branch_address1" => $row2['address'] ?? '',
                                "branch_address2" => $row2['address2'] ?? '',
                                "branch_address3" => $row2['address3'] ?? '',
                                "branch_address4" => $row2['address4'] ?? '',
                                "map_url" => $row2['map_url'] ?? '',
                            );
                        }
                    }
                }

                $message['id'] = $row['id'];
                $message['customer_code'] = $row['customer_code'];
                $message['customer_name'] = $row['customer_name'];
                $message['customer_address'] = $row['customer_address'];
                $message['address2'] = $row['address2'];
                $message['address3'] = $row['address3'];
                $message['customer_phone'] = $row['customer_phone'];
                $message['customer_email'] = $row['customer_email'];
                $message['branches'] = $branches;

            }
            
            // while ($row = $result->fetch_assoc()) {
                // $message['id'] = $row['id'];
                // $message['customer_code'] = $row['customer_code'];
                // $message['customer_name'] = $row['customer_name'];
                // $message['customer_address'] = $row['customer_address'];
                // $message['address2'] = $row['address2'];
                // $message['address3'] = $row['address3'];
                // $message['customer_phone'] = $row['customer_phone'];
                // $message['customer_email'] = $row['customer_email'];
            // }
            
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