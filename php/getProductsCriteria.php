<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['jenisAlat'], $_POST['capacity'], $_POST['validator'])){
	$jenisAlat = filter_input(INPUT_POST, 'jenisAlat', FILTER_SANITIZE_STRING);
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
    $validator = filter_input(INPUT_POST, 'validator', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM products WHERE jenis_alat=? AND capacity=? AND validator=?")) {
        $update_stmt->bind_param('sss', $jenisAlat, $capacity, $validator);
        
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

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $message['id'] = $row['id'];
                    $message['name'] = $row['name'];
                    $message['price'] = $row['price'];
                }
            }
            else{
                $message['id'] = '';
                $message['name'] = '';
                $message['price'] = '0.00';
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