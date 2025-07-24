<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.php";</script>';
}

if(isset($_POST['type'], $_POST['price'])){
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);

    $capacityName = null;
    $machineType = null;
    $jenisAlat = null;
    $capacity = null;
    $validator = null;

    if(isset($_POST['machineType']) && $_POST['machineType'] != null && $_POST['machineType'] != ''){
        $machineType = filter_input(INPUT_POST, 'machineType', FILTER_SANITIZE_STRING);
    }
    
    if(isset($_POST['jenisAlat']) && $_POST['jenisAlat'] != null && $_POST['jenisAlat'] != ''){
        $jenisAlat = filter_input(INPUT_POST, 'jenisAlat', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['capacity']) && $_POST['capacity'] != null && $_POST['capacity'] != ''){
        $capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['validator']) && $_POST['validator'] != null && $_POST['validator'] != ''){
        $validator = filter_input(INPUT_POST, 'validator', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE products SET name=?, machine_type=?, jenis_alat=?, capacity=?, validator=?, type=?, price=? WHERE id=?")) {
            $update_stmt->bind_param('ssssssss', $capacityName, $machineType, $jenisAlat, $capacity, $validator, $type, $price, $_POST['id']);
            
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
        if ($insert_stmt = $db->prepare("INSERT INTO products (name, machine_type, jenis_alat, capacity, validator, type, price) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssss', $capacityName, $machineType, $jenisAlat, $capacity, $validator, $type, $price);
            
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