<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['capacityName'], $_POST['machineType'], $_POST['jenisAlat'], $_POST['capacity'], $_POST['validator'], $_POST['price'])){
    $capacityName = filter_input(INPUT_POST, 'capacityName', FILTER_SANITIZE_STRING);
    $machineType = filter_input(INPUT_POST, 'machineType', FILTER_SANITIZE_STRING);
    $jenisAlat = filter_input(INPUT_POST, 'jenisAlat', FILTER_SANITIZE_STRING);
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
    $validator = filter_input(INPUT_POST, 'validator', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);

    if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE products SET name=?, machine_type=?, jenis_alat=?, capacity=?, validator=?, price=? WHERE id=?")) {
            $update_stmt->bind_param('sssssss', $capacityName, $machineType, $jenisAlat, $capacity, $validator, $price, $_POST['id']);
            
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
                $update_stmt->close();
                $db->close();
                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }
        }
    }
    else{
        if ($insert_stmt = $db->prepare("INSERT INTO products (name, machine_type, jenis_alat, capacity, validator, price) VALUES (?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssss', $capacityName, $machineType, $jenisAlat, $capacity, $validator, $price);
            
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
                $insert_stmt->close();
                $db->close();
                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );
            }
        }
    }
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