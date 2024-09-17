<?php
require_once 'db_connect.php';

$response = array(); // Initialize response array
if(isset($_POST['id'], $_POST['nmimDetail'], $_POST['nmimApprNo'], $_POST['nmimApprDt'], $_POST['nmimExpDt'], $_FILES['nmimPdf'])){
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $nmimDetail = filter_input(INPUT_POST, 'nmimDetail', FILTER_SANITIZE_STRING);
    $nmimApprNo = filter_input(INPUT_POST, 'nmimApprNo', FILTER_SANITIZE_STRING);
    $nmimApprDt = filter_input(INPUT_POST, 'nmimApprDt', FILTER_SANITIZE_STRING);
    $nmimExpDt = filter_input(INPUT_POST, 'nmimExpDt', FILTER_SANITIZE_STRING);

    $data = [
        'nmimDetail' => $nmimDetail,
        'nmimApprNo' => $nmimApprNo,
        'nmimApprDt' => $nmimApprDt,
        'nmimExpDt' => $nmimExpDt,
    ];

    // Retrieve existing certificate data
    $stmt = $db->prepare("SELECT nmim FROM companies WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if(($row = $result->fetch_assoc()) !== null){
        $existNmimJson = $row['nmim'];
    }

    if(!empty($existNmimJson) && !is_null($existNmimJson)){
        $dataArray = json_decode($existNmimJson, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $prevId = end($dataArray)['id'];
            $newId = $prevId + 1;
            $data = array_merge(['id' => $newId], $data); 
            // Check if file was uploaded
            if ($_FILES['nmimPdf']['error'] === 0) {
                $uploadDir = '../uploads/nmim/'; // Directory to store uploaded files
                $uploadDirDB = '../cirs/uploads/nmim/'; // filepath for db
                $uploadFile = $uploadDir . $newId . '_' . $nmimDetail . '_' . $nmimApprNo . '_'. basename($_FILES['nmimPdf']['name']);
                $uploadFileDB = $uploadDirDB . $newId . '_' . $nmimDetail . '_' . $nmimApprNo . '_'. basename($_FILES['nmimPdf']['name']);
                
                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES['nmimPdf']['tmp_name'], $uploadFile)) {
                    $response['file_status'] = "File successfully uploaded.";
                    $data['file_path'] = $uploadFileDB; // Add file path to data
                } else {
                    $response['file_status'] = "File upload failed.";
                }
            } else {
                $response['file_status'] = "No file uploaded or there was an error.";
            }

            $dataArray[] = $data;
            $dataJson = json_encode($dataArray, JSON_PRETTY_PRINT);
        }
    } else {
        $data = array_merge(['id' => 1], $data);  // Prepend 'id' to the array
        // Check if file was uploaded
        if ($_FILES['nmimPdf']['error'] === 0) {
            $uploadDir = '../uploads/nmim/'; // Directory to store uploaded files
            $uploadDirDB = '../cirs/uploads/nmim/'; // filepath for db
            $uploadFile = $uploadDir . $nmimDetail . '_' . $nmimApprNo . '_'. basename($_FILES['nmimPdf']['name']);
            $uploadFileDB = $uploadDirDB . '1_' . $nmimDetail . '_' . $nmimApprNo . '_'. basename($_FILES['nmimPdf']['name']);
            
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['nmimPdf']['tmp_name'], $uploadFile)) {
                $response['file_status'] = "File successfully uploaded.";
                $data['file_path'] = $uploadFileDB; // Add file path to data
            } else {
                $response['file_status'] = "File upload failed.";
            }
        } else {
            $response['file_status'] = "No file uploaded or there was an error.";
        }

        $dataJson = json_encode([$data], JSON_PRETTY_PRINT);
    }

    // Update certificate data in the database
    if ($stmt2 = $db->prepare("UPDATE companies SET nmim=? WHERE id=?")) {
        $stmt2->bind_param('ss', $dataJson, $id);
        
        if($stmt2->execute()){
            $response['status'] = "success";
            $response['message'] = "Your NMIM Pattern Approval Certificate is added successfully!";
        } else{
            $response['status'] = "failed";
            $response['message'] = $stmt2->error;
        }
    } else {
        $response['status'] = "failed";
        $response['message'] = "Something went wrong!";
    }
    
    $stmt->close();
    $stmt2->close();
    $db->close();
} else {
    $response['status'] = "failed";
    $response['message'] = "Please fill in all fields";
}

// Output the JSON response
echo json_encode($response);
?>
