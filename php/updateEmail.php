<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$id = $_SESSION['userID'];
}

if(isset($_POST['type'], $_POST['emailTitle'], $_POST['emailBody'])){
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
	$emailTitle = filter_input(INPUT_POST, 'emailTitle', FILTER_SANITIZE_STRING);
	$emailBody = $_POST['emailBody'];

	$emailCC = null;
	if($_POST['emailCC'] != null && $_POST['emailCC'] != ""){
		$emailCC = filter_input(INPUT_POST, 'emailCC', FILTER_SANITIZE_STRING);
	}

	# Query to check if record exist
	$emailSetupQuery = "SELECT * FROM email_setup WHERE type = '$type'"; 
	$emailSetupDetail = mysqli_query($db, $emailSetupQuery);
	$emailSetupRow = mysqli_fetch_assoc($emailSetupDetail);

	if($emailSetupRow == NULL){
		if ($insert_stmt = $db->prepare("INSERT INTO email_setup (type) 
		VALUES (?)")){
			$insert_stmt->bind_param('s', $type);
			$insert_stmt->execute();
			$insert_stmt->close();
		}

	}

	if ($stmt2 = $db->prepare("UPDATE email_setup SET email_cc=?, email_title=?, email_body=? WHERE type=?")) {
		$stmt2->bind_param('ssss', $emailCC, $emailTitle, $emailBody, $type);
		
		if($stmt2->execute()){
			$stmt2->close();
			$db->close();
			
			echo json_encode(
				array(
					"status"=> "success", 
					"message"=> "Your email template is updated successfully!" 
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
