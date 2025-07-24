<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();


if(isset($_POST['companyId']) && isset($_POST['nmimId'])){
	$id = $_POST['companyId'];
	$nmimId = $_POST['nmimId'];

	// Retrieve existing certificate data
    $stmt = $db->prepare("SELECT nmim FROM companies WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if(($row = $result->fetch_assoc()) !== null){
        $existNmimJson = $row['nmim'];
    }

	$stmt->close();

    if(!empty($existNmimJson) && !is_null($existNmimJson)){
        $nmims = json_decode($row['nmim'], true);
        if (is_array($nmims)) {
			if(count($nmims) > 1){
				foreach ($nmims as $key => &$nmim) {
					if ($nmim['id'] == $nmimId) { // Check if the nmim id match with $nmimId
						$updatedFilePath = str_replace('../cirs', '../', $nmim['file_path']); 
                        if (file_exists($updatedFilePath)) {
                            if (unlink($updatedFilePath)) {
								$response['file_status'] = "File deleted successfully.";
								unset($nmims[$key]);	
							}else {
                                $response['file_status'] = "Error deleting file.";
                            }
                        } else {
                            $response['file_status'] = "File does not exist.";
							unset($nmims[$key]);	
						}
					}
				}

				$nmims = json_encode($nmims);

				if ($stmt2 = $db->prepare("UPDATE companies SET nmim=? WHERE id=?")) {
					$stmt2->bind_param('ss', $nmims, $id);
					
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
						$stmt2->close();
						$db->close();

						echo json_encode(
							array(
								"status"=> "failed", 
								"message"=> $stmt2->error
							)
						);
					}
				} 
				else{
					$db->close();

					echo json_encode(
						array(
							"status"=> "failed", 
							"message"=> "Somethings wrong"
						)
					);
				}
			}else{
				$updatedFilePath = str_replace('../cirs', '../', $nmims[0]['file_path']); 
				if (file_exists($updatedFilePath)) {
					if (unlink($updatedFilePath)) {
						$response['file_status'] = "File deleted successfully.";
						unset($nmims[$key]);	
					}else {
						$response['file_status'] = "Error deleting file.";
					}
				} else {
					$response['file_status'] = "File does not exist.";
					unset($nmims[$key]);	
				}


				if ($stmt2 = $db->prepare("UPDATE companies SET nmim=null WHERE id=?")) {
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
						$stmt2->close();
						$db->close();

						echo json_encode(
							array(
								"status"=> "failed", 
								"message"=> $stmt2->error
							)
						);
					}
				} 
				else{
					$db->close();
					
					echo json_encode(
						array(
							"status"=> "failed", 
							"message"=> "Somethings wrong"
						)
					);
				}
			}            
		}
	}	
}

?>