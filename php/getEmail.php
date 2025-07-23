<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['id'])){
	$type = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM email_setup WHERE type=?")) {
        $update_stmt->bind_param('s', $type);
        
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
                $message['emailCC'] = $row['email_cc'];
                $message['emailTitle'] = $row['email_title'];
                $message['emailBody'] = $row['email_body'];
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