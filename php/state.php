<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.php";</script>';
}

if(isset($_POST['units'])){
    $state = filter_input(INPUT_POST, 'units', FILTER_SANITIZE_STRING);

    if($_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE `state` SET `state`=? WHERE id=?")) {
            $update_stmt->bind_param('ss', $state, $_POST['id']);
            
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
        if ($insert_stmt = $db->prepare("INSERT INTO `state` (`state`) VALUES (?)")) {
            $insert_stmt->bind_param('s', $state);
            
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
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Something goes wrong when create brand"
                )
            );
        }

        $insert_stmt->close();
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