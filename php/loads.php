<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['loadCell'], $_POST['model'], $_POST['capacity'], $_POST['madeIn'], $_POST['brand'], $_POST['class'], $_POST['oimlApproval'],   $_POST['patternNo'], $_POST['approvalDate'], $_POST['expiryDate'], $_FILES['certificate'])){
    $loadCell = filter_input(INPUT_POST, 'loadCell', FILTER_SANITIZE_STRING);
    $partNo = null;
    $model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
    $madeIn = filter_input(INPUT_POST, 'madeIn', FILTER_SANITIZE_STRING);
    $brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
    $class = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_STRING);
    $oimlApproval = filter_input(INPUT_POST, 'oimlApproval', FILTER_SANITIZE_STRING);
    $patternNo = filter_input(INPUT_POST, 'patternNo', FILTER_SANITIZE_STRING);
    $approvalDate = filter_input(INPUT_POST, 'approvalDate', FILTER_SANITIZE_STRING);
    $expiryDate = filter_input(INPUT_POST, 'expiryDate', FILTER_SANITIZE_STRING);

    if(isset($_POST['partNo']) && $_POST['partNo'] != null && $_POST['partNo'] != ''){
        $partNo = filter_input(INPUT_POST, 'partNo', FILTER_SANITIZE_STRING);
    }

    if($_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE load_cells SET load_cell=?, part_no=?, model=?, capacity=?, made_in=?, brand=?, class=?, oiml_approval=?, pattern_no=?, pattern_datetime=?, pattern_expiry=? WHERE id=?")) {
            $approvalDate = DateTime::createFromFormat('d/m/Y', $approvalDate)->format('Y-m-d H:i:s');
            $expiryDate = DateTime::createFromFormat('d/m/Y', $expiryDate)->format('Y-m-d H:i:s');
            $update_stmt->bind_param('ssssssssssss', $loadCell, $partNo, $model, $capacity, $madeIn, $brand, $class, $oimlApproval, $patternNo, $approvalDate, $expiryDate, $_POST['id']);
            
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
                if(isset($_FILES['certificate']) && $_FILES['certificate']['error'] === UPLOAD_ERR_OK){
                    if ($certificateFile = $db->prepare("SELECT certificate FROM load_cells WHERE id=?")) {
                        $certificateFile->bind_param('s', $_POST['id']);
    
                        // Execute the prepared query.
                        if (!$certificateFile->execute()) {
                            $response['status'] = "failed";
                            $response['message'] = "Something went wrong";
                        }else{
                            $result = $certificateFile->get_result();
                            $message = array();
                            if($row = $result->fetch_assoc()){
                                $updatedFilePath = str_replace('../cirs', '../', $row['certificate']); 
                                if (file_exists($updatedFilePath)) {
                                    unlink($updatedFilePath);

                                    $timestamp = time();
                                    $uploadDir = '../uploads/loadCell/'; // Directory to store uploaded files
                                    $uploadDirDB = '../cirs/uploads/loadCell/'; // filepath for db
                                    $uploadFile = $uploadDir . $timestamp . '_' . basename($_FILES['certificate']['name']);
                                    $uploadFileDB = $uploadDirDB . $timestamp . '_' . basename($_FILES['certificate']['name']);
                                    $tempFile = $_FILES['certificate']['tmp_name'];
                                
                                    // Move the uploaded file to the target directory
                                    if (move_uploaded_file($tempFile, $uploadFile)) {
                                        $certificate = $uploadFileDB; // Add file path to data

                                        if ($update_stmt2 = $db->prepare("UPDATE load_cells SET certificate=? WHERE id=?")) {
                                            $update_stmt2->bind_param('ss', $certificate, $_POST['id']);
                                            $update_stmt2->execute();
                                            $update_stmt2->close();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
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
        if ($insert_stmt = $db->prepare("INSERT INTO load_cells (load_cell, part_no, model, capacity, made_in, brand, class, oiml_approval, pattern_no, pattern_datetime, pattern_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $approvalDate = DateTime::createFromFormat('d/m/Y', $approvalDate)->format('Y-m-d H:i:s');
            $expiryDate = DateTime::createFromFormat('d/m/Y', $expiryDate)->format('Y-m-d H:i:s');
            $insert_stmt->bind_param('sssssssssss', $loadCell, $partNo, $model, $capacity, $madeIn, $brand, $class, $oimlApproval, $patternNo, $approvalDate, $expiryDate);
            
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
                $loadCellId = $insert_stmt->insert_id;

                if($_FILES['certificate']['error'] === 0){
                    $timestamp = time();
                    $uploadDir = '../uploads/loadCell/'; // Directory to store uploaded files
                    $uploadDirDB = '../cirs/uploads/loadCell/'; // filepath for db
                    $uploadFile = $uploadDir . $timestamp . '_' . basename($_FILES['certificate']['name']);
                    $uploadFileDB = $uploadDirDB . $timestamp . '_' . basename($_FILES['certificate']['name']);
                    $tempFile = $_FILES['certificate']['tmp_name'];
                
                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($tempFile, $uploadFile)) {
                        $certificate = $uploadFileDB; // Add file path to data

                        if ($update_stmt = $db->prepare("UPDATE load_cells SET certificate=? WHERE id=?")) {
                            $update_stmt->bind_param('ss', $certificate, $loadCellId);
                            $update_stmt->execute();
                            $update_stmt->close();
                        }
                    } else {
                        $response['file_status'] = "File upload failed.";
                    }
                } else {
                    $response['file_status'] = "No file uploaded or there was an error.";
                }

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