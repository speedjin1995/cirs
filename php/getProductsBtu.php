<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'], $_POST['alat'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$alat = filter_input(INPUT_POST, 'alat', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM products WHERE capacity=? AND jenis_alat=? AND deleted='0'")) {
        $update_stmt->bind_param('ss', $id, $alat);
        
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
                $message['name'] = $row['name'];
                $message['machine_type'] = $row['machine_type'];
                $message['jenis_alat'] = $row['jenis_alat'];
                $message['capacity'] = $row['capacity'];
                $message['validator'] = $row['validator'];
                $message['type'] = $row['type'];
                $message['price'] = $row['price'];
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