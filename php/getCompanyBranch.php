<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM company_branches WHERE id=?")) {
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
            
            if ($row = $result->fetch_assoc()) {
                $message['id'] = $row['id'];
                $message['branch_code'] = $row['branch_code'];
                $message['branch_name'] = $row['branch_name'];
                $message['address_line_1'] = $row['address_line_1'];
                $message['address_line_2'] = $row['address_line_2'];
                $message['address_line_3'] = $row['address_line_3'];
                $message['address_line_4'] = $row['address_line_4'];
                $message['map_url'] = $row['map_url'];
                $message['pic'] = $row['pic'];
                $message['pic_contact'] = $row['pic_contact'];
                $message['office_no'] = $row['office_no'];
                $message['email'] = $row['email'];
            }
            
            $update_stmt->close();

            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
        }

        $db->close();
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
        )
    ); 
}
?>