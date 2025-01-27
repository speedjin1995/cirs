<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['jenisAlat'])){
	$jenisAlat = filter_input(INPUT_POST, 'jenisAlat', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM size WHERE JSON_CONTAINS(alat, ?)")) {
        $jenisAlat = json_encode($jenisAlat);
        $update_stmt->bind_param('s', $jenisAlat);
        
        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                )); 
        }
        else{
            $result = $update_stmt->get_result();
            $message = array();
            
            while ($row = $result->fetch_assoc()) {
                $sizeRow = [];
                $sizeRow['id'] = $row['id'];
                $sizeRow['size'] = $row['size'];
                $sizeRow['alat'] = $row['alat'];
                $message[] = $sizeRow;
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
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