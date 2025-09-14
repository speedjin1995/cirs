<?php
require_once 'db_connect.php';

session_start();
$uid = $_SESSION['userID'];

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
}

$status = '';
if(isset($_POST['status']) && $_POST['status']!=null && $_POST['status']!=""){
	$status = $_POST['status'];
}

if(isset($_POST['id']) && $_POST['id']!=null && $_POST['id']!=""){
	$id = $_POST['id'];

	if ($status == 'Complete'){
		$del = "Pending";
		$renewed = 'N';
	}

	if ($stmt2 = $db->prepare("UPDATE stamping SET status=?, renewed=? WHERE id=?")) {
		$stmt2->bind_param('sss', $del, $renewed, $id);

		if($stmt2->execute()){
			// RECALL Stamping System Log
			if ($insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id) 
			VALUES (?, ?, ?)")){
				$action = "REVERTED";
				$insert_stmt3->bind_param('sss', $action, $uid, $id);
				$insert_stmt3->execute();
				$insert_stmt3->close();
			}

			echo json_encode(
    	        array(
    	            "status"=> "success", 
    	            "message"=> "Reverted"
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
