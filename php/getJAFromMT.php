<?php
require_once "db_connect.php";
require_once 'requires/lookup.php';

session_start();

if(isset($_POST['id'])){
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    if ($update_stmt = $db->prepare("SELECT * FROM machines WHERE id=? AND deleted = '0'")) {
        $update_stmt->bind_param('s', $id);
        
        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            $update_stmt->close();
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
                $modelRow = [];
                $modelRow['id'] = $row['jenis_alat'];
                $modelRow['jenis_alat'] = searchAlatNameById($row['jenis_alat'], $db);
                $message[] = $modelRow;
            }

            $update_stmt->close();
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
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