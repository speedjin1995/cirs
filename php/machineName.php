<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.php";</script>';
}

if(isset($_POST['machineNo'], $_POST['machineName'])){
    $machineNo = filter_input(INPUT_POST, 'machineNo', FILTER_SANITIZE_STRING);
    $machineName = filter_input(INPUT_POST, 'machineName', FILTER_SANITIZE_STRING);

    if($_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE machine_names SET machine_no=?, machine_name=? WHERE id=?")) {
            $update_stmt->bind_param('sss', $machineNo, $machineName, $_POST['id']);
            
            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );
            }
            else{                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }

            $update_stmt->close();
        }
    }
    else{
        if ($insert_stmt = $db->prepare("INSERT INTO machine_names (machine_no, machine_name) VALUES (?, ?)")) {
            $insert_stmt->bind_param('ss', $machineNo, $machineName);
            
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $insert_stmt->error
                    )
                );
            }
            else{                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );
            }

            $insert_stmt->close();
        }
    }

    $db->close();
}
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>