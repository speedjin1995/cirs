<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM users WHERE id=?")) {
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
                $message['id'] = $row['id'];
                $message['username'] = $row['username'];
                $message['name'] = $row['name'];
                $message['ic_number'] = $row['ic_number'];
                $message['designation'] = $row['designation'];
                $message['contact_number'] = $row['contact_number'];
                $message['role_code'] = $row['role_code'];
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
            )); 
}
?>