<?php
require_once 'db_connect.php';

$response = array(); // Initialize response array
if(isset($_POST['id'], $_POST['lesenCertDetail'], $_POST['lesenCertSerialNo'], $_POST['lesenCertApprDt'], $_POST['lesenCertExpDt'], $_FILES['lesenCertPdf'])){
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $lesenCertDetail = filter_input(INPUT_POST, 'lesenCertDetail', FILTER_SANITIZE_STRING);
    $lesenCertSerialNo = filter_input(INPUT_POST, 'lesenCertSerialNo', FILTER_SANITIZE_STRING);
    $lesenCertApprDt = filter_input(INPUT_POST, 'lesenCertApprDt', FILTER_SANITIZE_STRING);
    $lesenCertExpDt = filter_input(INPUT_POST, 'lesenCertExpDt', FILTER_SANITIZE_STRING);

    $data = [
        'lesenCertDetail' => $lesenCertDetail,
        'lesenCertSerialNo' => $lesenCertSerialNo,
        'lesenCertApprDt' => $lesenCertApprDt,
        'lesenCertExpDt' => $lesenCertExpDt,
    ];

    // Retrieve existing certificate data
    $stmt = $db->prepare("SELECT lesen_cert FROM companies WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if(($row = $result->fetch_assoc()) !== null){
        $existCertLesenJson = $row['lesen_cert'];
    }

    if(!empty($existCertLesenJson) && !is_null($existCertLesenJson)){
        $dataArray = json_decode($existCertLesenJson, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            $prevId = end($dataArray)['id'];
            $newId = $prevId + 1;
            $data = array_merge(['id' => $newId], $data); 

            // Check if file was uploaded
            if ($_FILES['lesenCertPdf']['error'] === 0) {
                $uploadDir = '../uploads/lesenCert/'; // Directory to store uploaded files
                $uploadDirDB = '../uploads/lesenCert/'; // filepath for db
                $filename = $newId . '_' . $lesenCertDetail . '_' . $lesenCertSerialNo . '_'. basename($_FILES['lesenCertPdf']['name']);
                $uploadFile = dirname(__DIR__, 2) . '/' . $uploadDir . $filename;
                $uploadFileDB = $uploadDirDB . $filename;
                
                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES['lesenCertPdf']['tmp_name'], $uploadFile)) {
                    $response['file_status'] = "File successfully uploaded.";
					// Update certificate data in the database
					if ($stmt4 = $db->prepare("INSERT INTO files (filename, filepath) VALUES (?, ?)")) {
						$stmt4->bind_param('ss', $filename, $uploadFileDB);
						$stmt4->execute();
						$fid = $stmt4->insert_id;
						$stmt4->close();
						$data['file_path'] = $fid;
					} 
                } 
                else {
                    $response['file_status'] = "File upload failed.";
                }
            } else {
                $response['file_status'] = "No file uploaded or there was an error.";
            }

            $dataArray[] = $data;
            $dataJson = json_encode($dataArray, JSON_PRETTY_PRINT);
        }
    } 
    else {
        $data = array_merge(['id' => 1], $data);  // Prepend 'id' to the array

        // Check if file was uploaded
        if ($_FILES['lesenCertPdf']['error'] === 0) {
            $uploadDir = '../uploads/lesenCert/'; // Directory to store uploaded files
            $uploadDirDB = '../uploads/lesenCert/'; // filepath for db
            $filename = '1_' . $lesenCertDetail . '_' . $lesenCertSerialNo . '_'. basename($_FILES['lesenCertPdf']['name']);
            $uploadFile = dirname(__DIR__, 2) . '/' . $uploadDir . $filename;
            $uploadFileDB = $uploadDirDB . $filename;
            
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['lesenCertPdf']['tmp_name'], $uploadFile)) {
                $response['file_status'] = "File successfully uploaded.";
                // Update certificate data in the database
				if ($stmt4 = $db->prepare("INSERT INTO files (filename, filepath) VALUES (?, ?)")) {
					$stmt4->bind_param('ss', $filename, $uploadFileDB);
					$stmt4->execute();
					$fid = $stmt4->insert_id;
					$stmt4->close();
					$data['file_path'] = $fid;
				} 
            } 
            else {
                $response['file_status'] = "File upload failed.";
            }
        } 
        else {
            $response['file_status'] = "No file uploaded or there was an error.";
        }

        $dataJson = json_encode([$data], JSON_PRETTY_PRINT);
    }

    // Update certificate data in the database
    if ($stmt2 = $db->prepare("UPDATE companies SET lesen_cert=? WHERE id=?")) {
        $stmt2->bind_param('ss', $dataJson, $id);
        
        if($stmt2->execute()){
            $response['status'] = "success";
            $response['message'] = "Your Certificate No.Lesen is added successfully!";
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