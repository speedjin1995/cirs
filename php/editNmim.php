<?php
require_once 'db_connect.php';

$response = array(); // Initialize response array
if(isset($_POST['id'], $_POST['nmimId'], $_POST['nmimFilePath'], $_POST['nmimDetail'], $_POST['nmimApprNo'], $_POST['nmimApprDt'], $_POST['nmimExpDt'])){
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $nmimId = filter_input(INPUT_POST, 'nmimId', FILTER_SANITIZE_STRING);
    $nmimFilePath = filter_input(INPUT_POST, 'nmimFilePath', FILTER_SANITIZE_STRING);
    $nmimDetail = filter_input(INPUT_POST, 'nmimDetail', FILTER_SANITIZE_STRING);
    $nmimApprNo = filter_input(INPUT_POST, 'nmimApprNo', FILTER_SANITIZE_STRING); 
    $nmimApprDt = filter_input(INPUT_POST, 'nmimApprDt', FILTER_SANITIZE_STRING);
    $nmimExpDt = filter_input(INPUT_POST, 'nmimExpDt', FILTER_SANITIZE_STRING);

    // Retrieve existing certificate data
    $stmt = $db->prepare("SELECT nmim FROM companies WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if(($row = $result->fetch_assoc()) !== null){
        $existNmimJson = $row['nmim'];
    }

    if(!empty($existNmimJson) && !is_null($existNmimJson)){
        $nmims = json_decode($row['nmim'], true); 
        if (is_array($nmims)) {
            foreach ($nmims as &$nmim) {
                if ($nmim['id'] == $nmimId) { // Check if the nmim id match with $nmimId
                    $nmim['nmimDetail'] = $nmimDetail;
                    $nmim['nmimApprNo'] = $nmimApprNo;
                    $nmim['nmimApprDt'] = $nmimApprDt;
                    $nmim['nmimExpDt'] = $nmimExpDt;

                    if (isset($_FILES['nmimPdf']) && $_FILES['nmimPdf']['error'] === 0) { 
                        $updatedFilePath = str_replace('../cirs', '../', $nmimFilePath); 
                        if (file_exists($updatedFilePath)) {
                            if (unlink($updatedFilePath)) {
                                $response['file_status'] = "File deleted successfully.";

                                $uploadDir = '../uploads/nmim/'; // Directory to store uploaded files
                                $uploadDirDB = '../cirs/uploads/nmim/'; // filepath for db
                                $uploadFile = $uploadDir . $nmimId . '_' . $nmimDetail . '_' . $nmimApprNo . '_'. basename($_FILES['nmimPdf']['name']);
                                $uploadFileDB = $uploadDirDB . $nmimId . '_' . $nmimDetail . '_' . $nmimApprNo . '_'. basename($_FILES['nmimPdf']['name']);
                                
                                // Move the uploaded file to the target directory
                                if (move_uploaded_file($_FILES['nmimPdf']['tmp_name'], $uploadFile)) {
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

        $nmims = json_encode($nmims);
    }

    // Update certificate data in the database
    if ($stmt2 = $db->prepare("UPDATE companies SET nmim=? WHERE id=?")) {
        $stmt2->bind_param('ss', $nmims, $id);
        
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
