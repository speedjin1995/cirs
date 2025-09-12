<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';

session_start();
$uid = $_SESSION['userID'];

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
}

$del = "Complete";

if(isset($_POST['isMulti']) && $_POST['isMulti']!=null && $_POST['isMulti']!=""){
	$isMulti = $_POST['isMulti'];
}

if ($isMulti == 'Y'){
	if(isset($_POST['userID']) && $_POST['userID']!=null && $_POST['userID']!=""){
		$updateDt = date('Y-m-d H:i:s');

		if(is_array($_POST['userID'])){
			$ids = $_POST['userID'];
		}else{
			$ids = [$_POST['userID']];
		}

		$errorsArray = array();
		$validIds = array();

		$idListStr = implode(",", $ids);
		$empQuery = "select * from stamping WHERE id IN ($idListStr)";
		$empRecords = mysqli_query($db, $empQuery);
		while($row = mysqli_fetch_assoc($empRecords)) {
			if (empty($row['stamping_date']) || empty($row['due_date']) || empty($row['siri_keselamatan']) || empty($row['borang_d']) || empty($row['borang_e'])) {
				$customer = searchCustNameById($row['customers'], $db);
				$brand = searchBrandNameById($row['brand'], $db);
				$machineType = searchMachineNameById($row['machine_type'], $db);
				$serialNo = $row['serial_no'];
				$validator = searchValidatorNameById($row['validate_by'], $db);

				$errorsArray[] = sprintf(
					"Missing data for Customer: %s | Brand: %s | Machine Type: %s | Serial No: %s | Validator: %s",
					$customer ?: 'N/A',
					$brand ?: 'N/A',
					$machineType ?: 'N/A',
					$serialNo ?: 'N/A',
					$validator ?: 'N/A'
				);
			}else{
				$validIds[] = $row['id']; // keep only valid
			}
		}

		$response = [
			"status"  => "failed",
			"message" => "Unknown error"
		];

		if (!empty($validIds)) {
			$validIdsStr = implode(",", $validIds);

			if ($stmt2 = $db->prepare("UPDATE stamping SET status=?, updated_datetime=? WHERE id IN ($validIdsStr)")) {
				$stmt2->bind_param('ss', $del, $updateDt);

				if ($stmt2->execute()) {
					// COMPLETE Stamping System Log
					foreach ($validIds as $id) {
						if ($insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id) VALUES (?, ?, ?)")) {
							$action = "COMPLETE";
							$insert_stmt3->bind_param('sss', $action, $uid, $id);
							$insert_stmt3->execute();
							$insert_stmt3->close();
						}
					}

					$response = [
						"status"  => "success",
						"message" => "Completed"
					];
				} else {
					$response = [
						"status"  => "failed",
						"message" => $stmt2->error
					];
				}

				$stmt2->close();
				$db->close();
			} else {
				$response = [
					"status"  => "failed",
					"message" => "Something went wrong"
				];
			}
		}

		// If there are validation errors, override response or append them
		if (!empty($errorsArray)) {
			$response["status"]  = "error";
			$response["message"] = "Some records were not updated due to missing required fields.";
			$response["errors"]  = $errorsArray;
		}

		echo json_encode($response);
	}
}else{
	if(isset($_POST['userID']) && $_POST['userID']!=null && $_POST['userID']!=""){
		$id = $_POST['userID'];
		if ($stmt2 = $db->prepare("UPDATE stamping SET status=? WHERE id=?")) {
			$stmt2->bind_param('ss', $del , $id);
			
			if($stmt2->execute()){
				// COMPLETE Stamping System Log
				if ($insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id) 
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
				$stmt2->close();
				$db->close();

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
?>
