<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();


if(isset($_POST['companyId']) && isset($_POST['lesenCertId'])){
	$id = $_POST['companyId'];
	$lesenCertId = $_POST['lesenCertId'];

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
			if(count($lesenCerts) > 1){
				foreach ($lesenCerts as $key => &$lesenCert) {
					if ($lesenCert['id'] == $lesenCertId) { // Check if the lesenCert id match with $lesenCertId
						$updatedFilePath = str_replace('../cirs', '../', $lesenCert['file_path']); 
                        if (file_exists($updatedFilePath)) {
                            if (unlink($updatedFilePath)) {
								$response['file_status'] = "File deleted successfully.";
								unset($lesenCerts[$key]);	
							}else {
                                $response['file_status'] = "Error deleting file.";
                            }
                        } else {
                            $response['file_status'] = "File does not exist.";
						}
					}
				}

				$lesenCerts = json_encode($lesenCerts);

				if ($stmt2 = $db->prepare("UPDATE companies SET lesen_cert=? WHERE id=?")) {
					$stmt2->bind_param('ss', $lesenCerts, $id);
					
					if($stmt2->execute()){
						$stmt2->close();
						$db->close();
						
						echo json_encode(
							array(
								"status"=> "success", 
								"message"=> "Deleted"
							)
						);
					} else{
						echo json_encode(
							array(
								"status"=> "failed", 
								"message"=> $stmt2->error
							)
						);
					}
				} 
				else{
					echo json_encode(
						array(
							"status"=> "failed", 
							"message"=> "Somthings wrong"
						)
					);
				}
			}else{
				$updatedFilePath = str_replace('../cirs', '../', $lesenCerts[0]['file_path']); 
				if (file_exists($updatedFilePath)) {
					if (unlink($updatedFilePath)) {
						$response['file_status'] = "File deleted successfully.";
					}else {
						$response['file_status'] = "Error deleting file.";
					}
				} else {
					$response['file_status'] = "File does not exist.";
				}


				if ($stmt2 = $db->prepare("UPDATE companies SET lesen_cert=null WHERE id=?")) {
					$stmt2->bind_param('s', $id);
					
					if($stmt2->execute()){
						$stmt2->close();
						$db->close();
						
						echo json_encode(
							array(
								"status"=> "success", 
								"message"=> "Deleted"
							)
						);
					} else{
						echo json_encode(
							array(
								"status"=> "failed", 
								"message"=> $stmt2->error
							)
						);
					}
				} 
				else{
					echo json_encode(
						array(
							"status"=> "failed", 
							"message"=> "Somthings wrong"
						)
					);
				}
			}            
		}
	}	
}

?>