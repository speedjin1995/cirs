<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
}

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$del = "1";
	
	if ($update_stmt = $db->prepare("UPDATE roles SET deleted=? WHERE id=?")) {
		$update_stmt->bind_param('ss', $del , $id);
		
		if($update_stmt->execute()){
			$update_stmt->close();
			$db->close();
			
			echo json_encode(
    	        array(
    	            "status"=> "success", 
    	            "message"=> "Deleted"
    	        )
    	    );
		} else{
			$update_stmt->close();
			$db->close();

		    echo json_encode(
    	        array(
    	            "status"=> "failed", 
    	            "message"=> $update_stmt->error
    	        )
    	    );
		}
	} 
	else{
		$db->close();
		
	    echo json_encode(
	        array(
	            "status"=> "failed", 
	            "message"=> "Somethings wrong"
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
