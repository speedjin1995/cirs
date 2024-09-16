<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['companyId'])){
	$id = filter_input(INPUT_POST, 'companyId', FILTER_SANITIZE_STRING);


    if ($sql = $db->prepare("SELECT nmim FROM companies WHERE id = ?")) {
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
                $nmims = json_decode($row['nmim'], true); 
                if (is_array($nmims)) {
                    foreach ($nmims as $nmim) {
                        $file_name = basename($nmim['file_path']);
                        $data[] = array(
                            $nmim['nmimDetail'],
                            $nmim['nmimApprNo'],
                            $nmim['nmimApprDt'],
                            $nmim['nmimExpDt'],
                            '<a href="' . $nmim['file_path'] . '" download="' . $file_name . '">
                                <i class="fa fa-file-pdf-o" style="font-size:150%;color:red"></i>
                            </a>'
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