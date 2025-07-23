<?php
require_once "db_connect.php";
require_once 'requires/lookup.php';

session_start();

if(isset($_POST['id'])){
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $type = '';

    if (isset($_POST['type']) && $_POST['type'] != ''){
        $type = $_POST['type'];
    }

    if ($type == 'Stamping'){ 
        if ($update_stmt = $db->prepare("SELECT * FROM stamping_log WHERE item_id=? ORDER BY id DESC")) {
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
                $data = array();
                $counter = 1;

                while ($row = $result->fetch_assoc()) {
                    // Build the data array with necessary information
                    $data[] = array(
                        "no" => $counter,
                        "id" => $row['id'],
                        "action" => $row['action'],
                        "user_id" => searchStaffNameById($row['user_id'], $db),
                        "item_id" => $row['item_id'],
                        "date" => $row['date'],
                        "cancel_id" => $row['cancel_id'] != null ? searchReasonById($row['cancel_id'], $db) : '',
                        "remark" => $row['remark'] ?? ''
                    );
                    $counter++;
                }

                $update_stmt->close();
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $data
                    )
                );  
            }

            $db->close();
        }
    }elseif ($type == 'Other') {
        if ($update_stmt = $db->prepare("SELECT * FROM other_validation_log WHERE item_id=? ORDER BY id DESC")) {
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
                $data = array();
                $counter = 1;

                while ($row = $result->fetch_assoc()) {
                    // Build the data array with necessary information
                    $data[] = array(
                        "no" => $counter,
                        "id" => $row['id'],
                        "action" => $row['action'],
                        "user_id" => searchStaffNameById($row['user_id'], $db),
                        "item_id" => $row['item_id'],
                        "date" => $row['date'],
                        "cancel_id" => $row['cancel_id'] != null ? searchReasonById($row['cancel_id'], $db) : '',
                        "remark" => $row['remark'] ?? ''
                    );
                    $counter++;
                }

                $update_stmt->close();
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $data
                    )
                );  
            }

            $db->close();
        }
    }elseif ($type == 'Inhouse') {
        if ($update_stmt = $db->prepare("SELECT * FROM inhouse_log WHERE item_id=? ORDER BY id DESC")) {
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
                $data = array();
                $counter = 1;

                while ($row = $result->fetch_assoc()) {
                    // Build the data array with necessary information
                    $data[] = array(
                        "no" => $counter,
                        "id" => $row['id'],
                        "action" => $row['action'],
                        "user_id" => searchStaffNameById($row['user_id'], $db),
                        "item_id" => $row['item_id'],
                        "date" => $row['date'],
                        "cancel_id" => $row['cancel_id'] != null ? searchReasonById($row['cancel_id'], $db) : '',
                        "remark" => $row['remark'] ?? ''
                    );
                    $counter++;
                }

                $update_stmt->close();
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $data
                    )
                );  
            }

            $db->close();
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