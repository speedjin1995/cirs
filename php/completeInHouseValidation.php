<?php
require_once 'db_connect.php';

session_start();

$uid = $_SESSION['userID'];

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
}

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$del = "Complete";
	
	if ($stmt2 = $db->prepare("UPDATE inhouse_validations SET status=? WHERE id=?")) {
		$stmt2->bind_param('ss', $del , $id);
		
		if($stmt2->execute()){
			// Insert Other Validation System Log
			if ($insert_stmt3 = $db->prepare("INSERT INTO inhouse_log (action, user_id, item_id) 
			VALUES (?, ?, ?)")){
				$action = "COMPLETE";
				$insert_stmt3->bind_param('sss', $action, $uid, $id);
				$insert_stmt3->execute();
				$insert_stmt3->close();
			}
			$stmt2->close();
			$db->close();
			
			echo json_encode(
    	        array(
    	            "status"=> "success", 
    	            "message"=> "Completed"
    	        )
    	    );
		} else{
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
