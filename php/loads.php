<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['loadCell'], $_POST['machineType'], $_POST['brand'], $_POST['model'], $_POST['jenisAlat'], $_POST['madeIn']
, $_POST['class'], $_POST['patternNo'], $_POST['approvalDate'], $_POST['expiryDate'])){
    $loadCell = filter_input(INPUT_POST, 'loadCell', FILTER_SANITIZE_STRING);
    $machineType = filter_input(INPUT_POST, 'machineType', FILTER_SANITIZE_STRING);
    $brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
    $model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
    $jenisAlat = filter_input(INPUT_POST, 'jenisAlat', FILTER_SANITIZE_STRING);
    $madeIn = filter_input(INPUT_POST, 'madeIn', FILTER_SANITIZE_STRING);
    $class = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_STRING);
    $patternNo = filter_input(INPUT_POST, 'patternNo', FILTER_SANITIZE_STRING);
    $approvalDate = filter_input(INPUT_POST, 'approvalDate', FILTER_SANITIZE_STRING);
    $expiryDate = filter_input(INPUT_POST, 'expiryDate', FILTER_SANITIZE_STRING);

    if($_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE load_cells SET load_cell=?, machine_type=?, brand=?, model=?, jenis_alat=?, made_in=?, class=?, pattern_no=?, pattern_datetime=?, pattern_expiry=? WHERE id=?")) {
            $approvalDate = DateTime::createFromFormat('d/m/Y', $approvalDate)->format('Y-m-d H:i:s');
            $expiryDate = DateTime::createFromFormat('d/m/Y', $expiryDate)->format('Y-m-d H:i:s');
            $update_stmt->bind_param('sssssssssss', $loadCell, $machineType, $brand, $model, $jenisAlat, $madeIn, $class, $patternNo, $approvalDate, $expiryDate, $_POST['id']);
            
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
        if ($insert_stmt = $db->prepare("INSERT INTO load_cells (load_cell, machine_type, brand, model, jenis_alat, made_in, class, pattern_no, pattern_datetime, pattern_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $approvalDate = DateTime::createFromFormat('d/m/Y', $approvalDate)->format('Y-m-d H:i:s');
            $expiryDate = DateTime::createFromFormat('d/m/Y', $expiryDate)->format('Y-m-d H:i:s');
            $insert_stmt->bind_param('ssssssssss', $loadCell, $machineType, $brand, $model, $jenisAlat, $madeIn, $class, $patternNo, $approvalDate, $expiryDate);
            
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