<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['action'])){
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if ($action == 'Report') {
        $type = '';
        
        if (isset($_POST['type']) && !empty($_POST['type']) && $_POST['type'] != '') {
            $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        }

        $validatorType = '';
        if ($type == 'Stamping') {
            $sql = "SELECT * FROM validators WHERE type='STAMPING' AND deleted = '0'";
        } else if ($type == 'Other') {
            $sql = "SELECT * FROM validators WHERE type='OTHER' AND deleted = '0'";
        } else if ($type == 'Inhouse') {
            $sql = "SELECT * FROM validators WHERE type='INHOUSE' AND deleted = '0'";
        } else {
            $sql = "SELECT * FROM validators WHERE deleted = '0'";
        }

        if ($update_stmt = $db->prepare($sql)) {
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
                    $message[] = array(
                        'id' => $row['id'],
                        'validator' => $row['validator'],
                    );
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
    }else{
        if(isset($_POST['userID'])){
            $id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

            if ($update_stmt = $db->prepare("SELECT * FROM validators WHERE id=?")) {
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
                        $message['validator'] = $row['validator'];
                        $message['type'] = $row['type'];
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