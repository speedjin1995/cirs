<?php
require_once "db_connect.php";
require_once 'requires/lookup.php';

session_start();

if(isset($_POST['id'])){
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $type = '';

    if (isset($_POST['type']) && $_POST['type'] != ''){
        $type = $_POST['type'];
    }

    if ($type == 'Stamping'){ 
        if ($update_stmt = $db->prepare("SELECT * FROM stamping_status_log WHERE stamp_id=? ORDER BY occurred_at ASC")) {
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
                $data = array();
                $counter = 1;

                while ($row = $result->fetch_assoc()) {
                    // Build the data array with necessary information
                    $data[] = array(
                        "id" => $row['id'],
                        "status" => $row['status'],
                        "status_remark" => $row['status_remark'] ?? '',
                        "created_by" => searchStaffNameById($row['created_by'], $db),
                        "occurred_at" => $row['occurred_at']
                    );
                    $counter++;
                }

                $update_stmt->close();
                
                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => $data
                    )
                );  
            }

            $db->close();
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