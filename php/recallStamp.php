<?php
require_once 'db_connect.php';

session_start();
$uid = $_SESSION['userID'];

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
}

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$del = "Pending";
	$reasonId = NULL;
	$otherReason = NULL;
	
	if ($stmt2 = $db->prepare("UPDATE stamping SET status=?, reason_id=?, other_reason=? WHERE id=?")) {
		$stmt2->bind_param('ssss', $del, $reasonId, $otherReason, $id);
		
		if($stmt2->execute()){
			// RECALL Stamping System Log
			if ($insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id) 
			VALUES (?, ?, ?)")){
				$action = "RECALLED";
				$insert_stmt3->bind_param('sss', $action, $uid, $id);
				$insert_stmt3->execute();
				$insert_stmt3->close();
			}

			$stmt2->close();
			$db->close();
			
			echo json_encode(
    	        array(
    	            "status"=> "success", 
    	            "message"=> "Recalled"
    	        )
    	    );
		} else{
		    $stmt2->close();
		    echo json_encode(
    	        array(
    	            "status"=> "failed", 
    	            "message"=> $stmt2->error
    	        )
    	    );
		}
	} 
	else{
	    echo json_encode(
	        array(
	            "status"=> "failed", 
	            "message"=> "Somthings wrong"
	        )
	    );
	}
} 
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}
?>
