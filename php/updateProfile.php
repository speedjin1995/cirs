<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$id = $_SESSION['userID'];
}

if(isset($_POST['userName'], $_POST['userEmail'], $_POST['emailAddr'])){
	$name = filter_input(INPUT_POST, 'userName', FILTER_SANITIZE_STRING);
	$username = filter_input(INPUT_POST, 'userEmail', FILTER_SANITIZE_STRING);
	$emailAddr = filter_input(INPUT_POST, 'emailAddr', FILTER_SANITIZE_STRING);
	
	if ($stmt2 = $db->prepare("UPDATE users SET name=?, username=?, email=? WHERE id=?")) {
		$stmt2->bind_param('ssss', $name, $username, $emailAddr, $id);
		
		if($stmt2->execute()){			
			echo json_encode(
				array(
					"status"=> "success", 
					"message"=> "Your profile is updated successfully!" 
				)
			);
		} else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> $stmt->error
				)
			);
		}
	} 
	else{
		echo json_encode(
			array(
				"status"=> "failed", 
				"message"=> "Something went wrong!"
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
            "message"=> "Please fill in all fields"
        )
    ); 
}
?>
