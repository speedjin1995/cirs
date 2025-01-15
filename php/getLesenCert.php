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
                        // $file_name = basename($lesenCert['file_path']);
                        $file_path = '';

                        if (!empty($lesenCert['file_path'])){
                            $file_path = $lesenCert['file_path'];
                        }

                        $data[] = array(
                            $lesenCert['lesenCertDetail'],
                            $lesenCert['lesenCertSerialNo'],
                            $lesenCert['lesenCertApprDt'],
                            $lesenCert['lesenCertExpDt'],
                            '<div class="row">'
                            .
                            //Download Button
                            '<div class="col-2">
                                <a href="' . $file_path . '" target="_blank" class="btn btn-success btn-sm" role="button">
                                    <i class="fa fa-file-pdf-o"></i>
                                </a>
                            </div>'
                            . 
                            //Edit Button
                            '<div class="col-2"><button title="edit" type="button" id="editLesenCert" name="editLesenCert" onclick="editLesenCert(' . $id . ', ' . $lesenCert['id'] . ')" class="btn btn-warning btn-sm">
                                <i class="fas fa-pen"></i>
                            </button></div>'
                            . 
                            //Delete Button
                            '<div class="col-2"><button title="delete" type="button" id="deleteLesenCert" name="deleteLesenCert"  onclick="deleteLesenCert(' . $id . ', ' . $lesenCert['id'] . ')"" class="btn btn-danger btn-sm">X</button></div>'
                            .
                            '</div>'
                            ,
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