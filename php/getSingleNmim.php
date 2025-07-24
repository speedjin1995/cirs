<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['companyId']) && isset($_POST['nmimId'])){
	$companyId = filter_input(INPUT_POST, 'companyId', FILTER_SANITIZE_STRING);
	$nmimId = filter_input(INPUT_POST, 'nmimId', FILTER_SANITIZE_STRING);

    if ($sql = $db->prepare("SELECT nmim FROM companies WHERE id = ?")) {
        $sql->bind_param('s', $companyId);
        
        // Execute the prepared query.
        if (!$sql->execute()) {
            $sql->close();
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
                $nmims = json_decode($row['nmim'], true); 
                if (is_array($nmims)) {
                    foreach ($nmims as $nmim) {
                        if ($nmim['id'] == $nmimId) { // Check if the nmim id match with $nmimId
                            $data['nmimDetail'] = $nmim['nmimDetail'];
                            $data['nmimApprNo'] = $nmim['nmimApprNo'];
                            $data['nmimApprDt'] = $nmim['nmimApprDt'];
                            $data['nmimExpDt'] = $nmim['nmimExpDt'];
                            $data['nmimFilePath'] = $nmim['file_path'];
                        }
                    }
                }
            }
            
            $sql->close();
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $data
                ));   
        }
        $db->close();
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