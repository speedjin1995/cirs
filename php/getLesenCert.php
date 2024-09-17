<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['companyId'])){
	$id = filter_input(INPUT_POST, 'companyId', FILTER_SANITIZE_STRING);


    if ($sql = $db->prepare("SELECT lesen_cert FROM companies WHERE id = ?")) {
        $sql->bind_param('s', $id);
        
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
                        $file_name = basename($lesenCert['file_path']);
                        $data[] = array(
                            $lesenCert['lesenCertDetail'],
                            $lesenCert['lesenCertSerialNo'],
                            $lesenCert['lesenCertApprDt'],
                            $lesenCert['lesenCertExpDt'],
                            '<a href="' . $lesenCert['file_path'] . '" download="' . $file_name . '">
                                <i class="fa fa-file-pdf-o" style="font-size:150%;color:red"></i>
                            </a>'
                            . 
                            '<button class="btn" id="editLesenCert" name="editLesenCert" onclick="editLesenCert(' . $id . ', ' . $lesenCert['id'] . ')">
                                <i class="fa fa-edit" style="font-size:150%;"></i>
                            </button>',
                            $lesenCert['id']
                        );
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