<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);

session_start();

$uid = $_SESSION['userID'];

if(isset($_POST['id']) && $_POST['status'] != null){


	//Updated datetime
	$currentDateTime = date('Y-m-d H:i:s');

	if ($update_stmt = $db->prepare("UPDATE stamping SET status=?, updated_datetime=? WHERE id=?")){
			$update_stmt->bind_param('ssi', $_POST['status'], $currentDateTime, $_POST['id']);
		
			// Execute the prepared query.
			if (! $update_stmt->execute()){
				echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> $update_stmt->error
					)
				);
			} 

			$update_stmt->close();

            echo json_encode(
				array(
					"status" => "success",
					"message" => "Updated Successfully!!"
				)
			);
	}

} 
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Error occur when update status"
        )
    );     
}

?>