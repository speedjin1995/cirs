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
                            '<div class="row">'
                            .
                            //Download Button
                            '<div class="col-2">
                                <a href="' . $nmim['file_path'] . '" target="_blank" class="btn btn-success btn-sm" role="button">
                                    <i class="fa fa-file-pdf-o"></i>
                                </a>
                            </div>'
                            . 
                            //Edit Button
                            '<div class="col-2"><button title="edit" type="button" id="editNmim" name="editNmim" onclick="editNmim(' . $id . ', ' . $nmim['id'] . ')" class="btn btn-warning btn-sm">
                                <i class="fas fa-pen"></i>
                            </button></div>'
                            . 
                            //Delete Button
                            '<div class="col-2"><button title="delete" type="button" id="deleteNmim" name="deleteNmim"  onclick="deleteNmim(' . $id . ', ' . $nmim['id'] . ')"" class="btn btn-danger btn-sm">X</button></div>'
                            .
                            '</div>'
                            ,
                            $nmim['id']
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