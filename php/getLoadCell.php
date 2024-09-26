<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM load_cells WHERE id=?")) {
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
                $message['id'] = $row['id'];
                $message['load_cell'] = $row['load_cell'];
                $message['part_no'] = $row['part_no'];
                $message['capacity'] = $row['capacity'];
                $message['brand'] = $row['brand'];
                $message['model'] = $row['model'];
                $message['oiml_approval'] = $row['oiml_approval'];
                $message['made_in'] = $row['made_in'];
                $message['class'] = $row['class'];
                $message['pattern_no'] = $row['pattern_no'];
                $message['pattern_datetime'] = $row['pattern_datetime'];
                $message['pattern_expiry'] = $row['pattern_expiry'];
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
        )
    ); 
}
?>