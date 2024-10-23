<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$status = '';
if(isset($_POST['status']) && $_POST['status']!=null && $_POST['status']!=""){
	$status = $_POST['status'];
}

if(isset($_POST['cancellationReason']) && $_POST['cancellationReason']!=null && $_POST['cancellationReason']!=""){
	$cancelReason = $_POST['cancellationReason'];
}
if(isset($_POST['otherReason']) && $_POST['otherReason']!=null && $_POST['otherReason']!=""){
	$otherReason = $_POST['otherReason'];
}

if(isset($_POST['id']) && $_POST['id']!=null && $_POST['id']!=""){
	$id = $_POST['id'];
	$updateDt = date('Y-m-d H:i:s');

	if ($status == "DELETE"){
		$del = 1;

		if ($stmt2 = $db->prepare("UPDATE other_validations SET deleted=?, update_datetime=? WHERE id=?")) {
			$stmt2->bind_param('sss', $del, $updateDt, $id);
			
			if($stmt2->execute()){
				$stmt2->close();
				$db->close();
				
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
	}else{
		$del = "Cancelled";

		if ($stmt2 = $db->prepare("UPDATE other_validations SET status=?, reason_id=?, other_reason=?, update_datetime=? WHERE id=?")) {
			$stmt2->bind_param('sssss', $del, $cancelReason, $otherReason, $updateDt, $id);
			
			if($stmt2->execute()){
				$stmt2->close();
				$db->close();
				
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Cancelled"
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
	
}

# Old Code

// if(!isset($_SESSION['userID'])){
// 	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
// }

// if(isset($_POST['userID'])){
// 	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
// 	$del = "Cancelled";
	
// 	if ($stmt2 = $db->prepare("UPDATE stamping SET status=? WHERE id=?")) {
// 		$stmt2->bind_param('ss', $del , $id);
		
// 		if($stmt2->execute()){
// 			$stmt2->close();
// 			$db->close();
			
// 			echo json_encode(
//     	        array(
//     	            "status"=> "success", 
//     	            "message"=> "Deleted"
//     	        )
//     	    );
// 		} else{
// 		    echo json_encode(
//     	        array(
//     	            "status"=> "failed", 
//     	            "message"=> $stmt2->error
//     	        )
//     	    );
// 		}
// 	} 
// 	else{
// 	    echo json_encode(
// 	        array(
// 	            "status"=> "failed", 
// 	            "message"=> "Somthings wrong"
// 	        )
// 	    );
// 	}
// } 
// else{
//     echo json_encode(
//         array(
//             "status"=> "failed", 
//             "message"=> "Please fill in all the fields"
//         )
//     ); 
// }

?>
