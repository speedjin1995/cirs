<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();
$uid = $_SESSION['userID'];

$status = '';
$type = '';
$cancelReason = '';
$otherReason = '';

if(isset($_POST['status']) && $_POST['status']!=null && $_POST['status']!=""){
	$status = $_POST['status'];
}

if(isset($_POST['type']) && $_POST['type']!=null && $_POST['type']!=""){
	$type = $_POST['type'];
}

if(isset($_POST['cancellationReason']) && $_POST['cancellationReason']!=null && $_POST['cancellationReason']!=""){
	$cancelReason = $_POST['cancellationReason'];
}
if(isset($_POST['otherReason']) && $_POST['otherReason']!=null && $_POST['otherReason']!=""){
	$otherReason = $_POST['otherReason'];
}

if ($type == 'MULTI'){
	if(isset($_POST['id']) && $_POST['id']!=null && $_POST['id']!=""){
		if(is_array($_POST['id'])){
			$ids = implode(",", $_POST['id']);
		}else{
			$ids = $_POST['id'];
		}
		$id_list = explode(",", $ids); 
		$updateDt = date('Y-m-d H:i:s');
	
		if ($status == "DELETE"){
			$del = 1;
			if ($stmt2 = $db->prepare("UPDATE stamping SET deleted=?, updated_datetime=? WHERE id IN ($ids)")) {
				$stmt2->bind_param('ss', $del, $updateDt);
				
				if($stmt2->execute()){
					// DELETE Stamping System Log
					foreach ($id_list as $id) {
						if ($insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id) 
						VALUES (?, ?, ?)")){
							$action = "DELETE";
							$insert_stmt3->bind_param('sss', $action, $uid, $id);
							$insert_stmt3->execute();
							$insert_stmt3->close();
						}
					}
	
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
	
			if ($stmt2 = $db->prepare("UPDATE stamping SET status=?, reason_id=?, other_reason=?, updated_datetime=? WHERE id IN ($ids)")) {
				$stmt2->bind_param('ssss', $del, $cancelReason, $otherReason, $updateDt);
				
				if($stmt2->execute()){
					// Cancel Stamping System Log
					foreach ($id_list as $id) {
						if ($insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id, cancel_id, remark) 
						VALUES (?, ?, ?, ?, ?)")){
							$action = "CANCELLED";
							$insert_stmt3->bind_param('sssss', $action, $uid, $id, $cancelReason, $otherReason);
							$insert_stmt3->execute();
							$insert_stmt3->close();
						}
					}
					
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
}else{
	if(isset($_POST['id']) && $_POST['id']!=null && $_POST['id']!=""){
		$id = $_POST['id'];
		$updateDt = date('Y-m-d H:i:s');
	
		if ($status == "DELETE"){
			$del = 1;
			if ($stmt2 = $db->prepare("UPDATE stamping SET deleted=?, updated_datetime=? WHERE id=?")) {
				$stmt2->bind_param('sss', $del,$updateDt, $id);
				
				if($stmt2->execute()){
					// DELETE Stamping System Log
					if ($insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id) 
					VALUES (?, ?, ?)")){
						$action = "DELETE";
						$insert_stmt3->bind_param('sss', $action, $uid, $_POST['id']);
						$insert_stmt3->execute();
						$insert_stmt3->close();
					}
	
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
	
			if ($stmt2 = $db->prepare("UPDATE stamping SET status=?, reason_id=?, other_reason=?, updated_datetime=? WHERE id=?")) {
				$stmt2->bind_param('sssss', $del, $cancelReason, $otherReason, $updateDt, $id);
				
				if($stmt2->execute()){
					// Cancel Stamping System Log
					if ($insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id, cancel_id, remark) 
					VALUES (?, ?, ?, ?, ?)")){
						$action = "CANCELLED";
						$insert_stmt3->bind_param('sssss', $action, $uid, $_POST['id'], $cancelReason, $otherReason);
						$insert_stmt3->execute();
						$insert_stmt3->close();
					}
	
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
}

# Old Code

// if(!isset($_SESSION['userID'])){
// 	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
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
