<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
}

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$del = "0";
	if ($stmt2 = $db->prepare("UPDATE users SET deleted=? WHERE id=?")) {
		$stmt2->bind_param('ss', $del , $id);
		
		if($stmt2->execute()){			
			echo json_encode(
    	        array(
    	            "status"=> "success", 
    	            "message"=> "Deleted"
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
	
	$stmt2->close();
	$db->close();

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
