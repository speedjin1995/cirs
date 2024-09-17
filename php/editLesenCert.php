<?php
require_once 'db_connect.php';

$response = array(); // Initialize response array
if(isset($_POST['id'], $_POST['lesenCertId'], $_POST['lesenCertFilePath'], $_POST['lesenCertDetail'], $_POST['lesenCertSerialNo'], $_POST['lesenCertApprDt'], $_POST['lesenCertExpDt'])){
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $lesenCertId = filter_input(INPUT_POST, 'lesenCertId', FILTER_SANITIZE_STRING);
    $lesenCertFilePath = filter_input(INPUT_POST, 'lesenCertFilePath', FILTER_SANITIZE_STRING);
    $lesenCertDetail = filter_input(INPUT_POST, 'lesenCertDetail', FILTER_SANITIZE_STRING);
    $lesenCertSerialNo = filter_input(INPUT_POST, 'lesenCertSerialNo', FILTER_SANITIZE_STRING); 
    $lesenCertApprDt = filter_input(INPUT_POST, 'lesenCertApprDt', FILTER_SANITIZE_STRING);
    $lesenCertExpDt = filter_input(INPUT_POST, 'lesenCertExpDt', FILTER_SANITIZE_STRING);

    // Retrieve existing certificate data
    $stmt = $db->prepare("SELECT lesen_cert FROM companies WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if(($row = $result->fetch_assoc()) !== null){
        $existLesenCertJson = $row['lesen_cert'];
    }

    if(!empty($existLesenCertJson) && !is_null($existLesenCertJson)){
        $lesenCerts = json_decode($row['lesen_cert'], true); 
        if (is_array($lesenCerts)) {
            foreach ($lesenCerts as &$lesenCert) {
                if ($lesenCert['id'] == $lesenCertId) { // Check if the lesenCert id match with $lesenCertId
                    $lesenCert['lesenCertDetail'] = $lesenCertDetail;
                    $lesenCert['lesenCertSerialNo'] = $lesenCertSerialNo;
                    $lesenCert['lesenCertApprDt'] = $lesenCertApprDt;
                    $lesenCert['lesenCertExpDt'] = $lesenCertExpDt;

                    if (isset($_FILES['lesenCertPdf']) && $_FILES['lesenCertPdf']['error'] === 0) { 
                        $updatedFilePath = str_replace('../cirs', '../', $lesenCertFilePath); 
                        if (file_exists($updatedFilePath)) {
                            if (unlink($updatedFilePath)) {
                                $response['file_status'] = "File deleted successfully.";

                                $uploadDir = '../uploads/lesenCert/'; // Directory to store uploaded files
                                $uploadDirDB = '../cirs/uploads/lesenCert/'; // filepath for db
                                $uploadFile = $uploadDir . $lesenCertId . '_' . $lesenCertDetail . '_' . $lesenCertSerialNo . '_'. basename($_FILES['lesenCertPdf']['name']);
                                $uploadFileDB = $uploadDirDB . $lesenCertId . '_' . $lesenCertDetail . '_' . $lesenCertSerialNo . '_'. basename($_FILES['lesenCertPdf']['name']);
                                
                                // Move the uploaded file to the target directory
                                if (move_uploaded_file($_FILES['lesenCertPdf']['tmp_name'], $uploadFile)) {
                                    $response['file_status'] = "File successfully uploaded.";
                                    $nmim['file_path'] = $uploadFileDB; // Add file path to data
                                } else {
                                    $response['file_status'] = "File upload failed.";
                                }
                            } else {
                                $response['file_status'] = "Error deleting file.";
                            }
                        } else {
                            $response['file_status'] = "File does not exist.";
                        }
                    } else {
                        $response['file_status'] = "No file uploaded or there was an error.";
                    }
                }
            }
        } 

        $lesenCerts = json_encode($lesenCerts);
    }

    // Update certificate data in the database
    if ($stmt2 = $db->prepare("UPDATE companies SET lesen_cert=? WHERE id=?")) {
        $stmt2->bind_param('ss', $lesenCerts, $id);
        
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
