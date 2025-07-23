<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM standard WHERE id=?")) {
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
                $message['standard_avg_temp'] = $row['standard_avg_temp'];
                $message['relative_humidity'] = $row['relative_humidity'];
                $message['capacity'] = $row['capacity'];
                $message['unit'] = $row['unit'];
                $message['variance'] = $row['variance'];
                $message['test_1'] = $row['test_1'];
                $message['test_2'] = $row['test_2'];
                $message['test_3'] = $row['test_3'];
                $message['test_4'] = $row['test_4'];
                $message['test_5'] = $row['test_5'];
                $message['test_6'] = $row['test_6'];
                $message['test_7'] = $row['test_7'];
                $message['test_8'] = $row['test_8'];
                $message['test_9'] = $row['test_9'];
                $message['test_10'] = $row['test_10'];
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