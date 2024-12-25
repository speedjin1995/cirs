<?php
require_once "db_connect.php";
require_once 'requires/lookup.php';

session_start();

if(isset($_POST['stampId'])){
	$id = filter_input(INPUT_POST, 'stampId', FILTER_SANITIZE_STRING);
    $type = '';

    if (isset($_POST['type']) && $_POST['type'] != ''){
        $type = $_POST['type'];
    }

    if ($type = 'stamping'){
        if ($update_stmt = $db->prepare("SELECT * FROM stamping_log WHERE item_id=? ORDER BY id DESC")) {
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
                    $message['action'] = $row['action'];
                    $message['user_id'] = searchStaffNameById($row['user_id'], $db);
                    $message['item_id'] = $row['item_id'];
                    $message['date'] = $row['date'];
                    $message['cancel_id'] = searchReasonById($row['cancel_id'], $db);
                    $message['remark'] = $row['remark'];
                }
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $message
                    ));   
            }
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