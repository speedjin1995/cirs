<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['companyId']) && isset($_POST['lesenCertId'])){
	$companyId = filter_input(INPUT_POST, 'companyId', FILTER_SANITIZE_STRING);
	$lesenCertId = filter_input(INPUT_POST, 'lesenCertId', FILTER_SANITIZE_STRING);

    if ($sql = $db->prepare("SELECT lesen_cert FROM companies WHERE id = ?")) {
        $sql->bind_param('s', $companyId);
        
        // Execute the prepared query.
        if (!$sql->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                )); 
        }
        else{
            $result = $sql->get_result();                 
            $data = array(); 
            
            while ($row = $result->fetch_assoc()) {
                $lesenCerts = json_decode($row['lesen_cert'], true); 
                if (is_array($lesenCerts)) {
                    foreach ($lesenCerts as $lesenCert) {
                        if ($lesenCert['id'] == $lesenCertId) { // Check if the lesen_cert id match with $lesenCertId
                            $data['lesenCertDetail'] = $lesenCert['lesenCertDetail'];
                            $data['lesenCertSerialNo'] = $lesenCert['lesenCertSerialNo'];
                            $data['lesenCertApprDt'] = $lesenCert['lesenCertApprDt'];
                            $data['lesenCertExpDt'] = $lesenCert['lesenCertExpDt'];
                            $data['lesenCertFilePath'] = $lesenCert['file_path'];
                        }
                    }
                }
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $data
                ));   
        }
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>