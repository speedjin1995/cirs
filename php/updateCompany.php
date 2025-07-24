<?php
require_once 'db_connect.php';

if(isset($_POST['new_roc'], $_POST['name'], $_POST['address'])){
	$new_roc = filter_input(INPUT_POST, 'new_roc', FILTER_SANITIZE_STRING);
	$old_roc = null;
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
	$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
	$phone = null;
	$fax = null;
	$person_incharge = null;
	$contact_no = null;
	$email = null;
	$lesen_type = null;
	$certno_lesen = null;
	$certats_serialno = null;
	$failno = null;
	$bless_serahanno = null;
	$resitno = null;
	$tarikh_kuatkuasa = null;
	$tarikh_luput = null;
	$tarikh_dikeluarkan = null;
	$engineer_name = null;
	$engineer_ic = null;
	$engineer_position = null;
	$engineer_contact = null;
	$id = '1';

	if($_POST['old_roc'] != null && $_POST['old_roc'] != ""){
		$old_roc = filter_input(INPUT_POST, 'old_roc', FILTER_SANITIZE_STRING);
	}

	if($_POST['phone'] != null && $_POST['phone'] != ""){
		$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
	}

	if($_POST['fax'] != null && $_POST['fax'] != ""){
		$fax = filter_input(INPUT_POST, 'fax', FILTER_SANITIZE_STRING);
	}

	if($_POST['person_incharge'] != null && $_POST['person_incharge'] != ""){
		$person_incharge = filter_input(INPUT_POST, 'person_incharge', FILTER_SANITIZE_STRING);
	}

	if($_POST['contact_no'] != null && $_POST['contact_no'] != ""){
		$contact_no = filter_input(INPUT_POST, 'contact_no', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['email'] != null && $_POST['email'] != ""){
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
	}

	if($_POST['lesen_type'] != null && $_POST['lesen_type'] != ""){
		$lesen_type = filter_input(INPUT_POST, 'lesen_type', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['certno_lesen'] != null && $_POST['certno_lesen'] != ""){
		$certno_lesen = filter_input(INPUT_POST, 'certno_lesen', FILTER_SANITIZE_STRING);
	}

	if($_POST['certats_serialno'] != null && $_POST['certats_serialno'] != ""){
		$certats_serialno = filter_input(INPUT_POST, 'certats_serialno', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['failno'] != null && $_POST['failno'] != ""){
		$failno = filter_input(INPUT_POST, 'failno', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['bless_serahanno'] != null && $_POST['bless_serahanno'] != ""){
		$bless_serahanno = filter_input(INPUT_POST, 'bless_serahanno', FILTER_SANITIZE_STRING);
	}

	if($_POST['resitno'] != null && $_POST['resitno'] != ""){
		$resitno = filter_input(INPUT_POST, 'resitno', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['tarikh_kuatkuasa'] != null && $_POST['tarikh_kuatkuasa'] != ""){
		$tarikh_kuatkuasa = filter_input(INPUT_POST, 'tarikh_kuatkuasa', FILTER_SANITIZE_STRING);
	}

	if($_POST['tarikh_luput'] != null && $_POST['tarikh_luput'] != ""){
		$tarikh_luput = filter_input(INPUT_POST, 'tarikh_luput', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['tarikh_dikeluarkan'] != null && $_POST['tarikh_dikeluarkan'] != ""){
		$tarikh_dikeluarkan = filter_input(INPUT_POST, 'tarikh_dikeluarkan', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['stamp_prefer_validator'] != null && $_POST['stamp_prefer_validator'] != ""){
		$stamp_prefer_validator = filter_input(INPUT_POST, 'stamp_prefer_validator', FILTER_SANITIZE_STRING);
	}
	
	// if($_POST['engineer_name'] != null && $_POST['engineer_name'] != ""){
	// 	$engineer_name = filter_input(INPUT_POST, 'engineer_name', FILTER_SANITIZE_STRING);
	// }
	
	// if($_POST['engineer_ic'] != null && $_POST['engineer_ic'] != ""){
	// 	$engineer_ic = filter_input(INPUT_POST, 'engineer_ic', FILTER_SANITIZE_STRING);
	// }
	
	// if($_POST['engineer_position'] != null && $_POST['engineer_position'] != ""){
	// 	$engineer_position = filter_input(INPUT_POST, 'engineer_position', FILTER_SANITIZE_STRING);
	// }
	
	// if($_POST['engineer_contact'] != null && $_POST['engineer_contact'] != ""){
	// 	$engineer_contact = filter_input(INPUT_POST, 'engineer_contact', FILTER_SANITIZE_STRING);
	// }

	// if ($stmt2 = $db->prepare("UPDATE companies SET new_roc=?, old_roc=?, name=?, address=?, phone=?, fax=?, person_incharge=?, contact_no=?, email=?, lesen_type=?, certno_lesen=?, certats_serialno=?, failno=?, bless_serahanno=?, resitno=?, tarikh_kuatkuasa=?, tarikh_luput=?, tarikh_dikeluarkan=?, engineer_name=?, engineer_ic=?, engineer_position=?, engineer_contact=? WHERE id=?")) {
	// 	$stmt2->bind_param('sssssssssssssssssssssss', $new_roc, $old_roc, $name, $address, $phone, $fax, $person_incharge, $contact_no, $email, $lesen_type, $certno_lesen, $certats_serialno, $failno, $bless_serahanno, $resitno, $tarikh_kuatkuasa, $tarikh_luput, $tarikh_dikeluarkan, $engineer_name, $engineer_ic, $engineer_position, $engineer_contact, $id);
	if ($stmt2 = $db->prepare("UPDATE companies SET new_roc=?, old_roc=?, name=?, address=?, phone=?, fax=?, person_incharge=?, contact_no=?, email=?, lesen_type=?, certno_lesen=?, certats_serialno=?, failno=?, bless_serahanno=?, resitno=?, tarikh_kuatkuasa=?, tarikh_luput=?, tarikh_dikeluarkan=?, stamp_prefer_validator=? WHERE id=?")) {
		$stmt2->bind_param('ssssssssssssssssssss', $new_roc, $old_roc, $name, $address, $phone, $fax, $person_incharge, $contact_no, $email, $lesen_type, $certno_lesen, $certats_serialno, $failno, $bless_serahanno, $resitno, $tarikh_kuatkuasa, $tarikh_luput, $tarikh_dikeluarkan, $stamp_prefer_validator, $id);
		
		if($stmt2->execute()){
			$uploadAttachment = null;
			$uploadInhouseAttachment = null;
			if(isset($_FILES['uploadAttachment']) && $_FILES['uploadAttachment']!=null && $_FILES['uploadAttachment']!=""){
				$uploadAttachment = $_FILES['uploadAttachment'];

				$ds = DIRECTORY_SEPARATOR;
				$storeFolder = '../uploads/signature';
				$dataJson = '';
				if($uploadAttachment['error'] === 0){
					# Delete Existing File
					if(isset($_POST['signFilePath']) && $_POST['signFilePath']!=null && $_POST['signFilePath']!=""){
						$signFilePath = $_POST['signFilePath'];
						if (file_exists($signFilePath)) {
							unlink($signFilePath);
						}
					}

					$timestamp = time();
					$uploadDir = '../uploads/signature/'; // Directory to store uploaded files
					$filename = $timestamp . '_' . basename($_FILES['uploadAttachment']['name']);
					$uploadFile = dirname(__DIR__, 2) . '/' . $uploadDir . $filename;
					$tempFile = $_FILES['uploadAttachment']['tmp_name'];

					// Move the uploaded file to the target directory
					if (move_uploaded_file($tempFile, $uploadFile)) {
						$signatureFilePath = $uploadDir . $filename;
						// Update certificate data in the database
						if ($stmt3 = $db->prepare("INSERT INTO files (filename, filepath) VALUES (?, ?)")) {
							$stmt3->bind_param('ss', $filename, $signatureFilePath);
							$stmt3->execute();
							$fid = $stmt3->insert_id;
							$stmt3->close();
							
							if ($stmtf = $db->prepare("UPDATE companies SET signature=? WHERE id=?")) {
    							$stmtf->bind_param('ss', $fid, $id);
    							$stmtf->execute();
    							$stmtf->close();
    						}
						} 
					} 
				}
			}

			if(isset($_FILES['uploadInhouseAttachment']) && $_FILES['uploadInhouseAttachment']!=null && $_FILES['uploadInhouseAttachment']!=""){
				$uploadInhouseAttachment = $_FILES['uploadInhouseAttachment'];
				$ds = DIRECTORY_SEPARATOR;
				$storeFolder = '../uploads/inhouseLetterHead';
				$dataJson = '';
				if($uploadInhouseAttachment['error'] === 0){
					# Delete Existing File
					if(isset($_POST['inhouseFilePath']) && $_POST['inhouseFilePath']!=null && $_POST['inhouseFilePath']!=""){
						$inhouseFilePath = $_POST['inhouseFilePath'];
						if (file_exists($inhouseFilePath)) {
							unlink($inhouseFilePath);
						}
					}

					$timestamp = time();
					$uploadDir = '../uploads/inhouseLetterHead/'; // Directory to store uploaded files
					$filename = $timestamp . '_' . basename($_FILES['uploadInhouseAttachment']['name']);
					$uploadFile = dirname(__DIR__, 2) . '/' . $uploadDir . $filename;
					$tempFile = $_FILES['uploadInhouseAttachment']['tmp_name'];

					// Move the uploaded file to the target directory
					if (move_uploaded_file($tempFile, $uploadFile)) { 
						$inhouseFilePath = $uploadDir . $filename;
						// Update certificate data in the database
						if ($stmt4 = $db->prepare("INSERT INTO files (filename, filepath) VALUES (?, ?)")) {
							$stmt4->bind_param('ss', $filename, $inhouseFilePath);
							$stmt4->execute();
							$fid = $stmt4->insert_id;
							$stmt4->close();
							
							if ($stmtf2 = $db->prepare("UPDATE companies SET inhouse=? WHERE id=?")) {
    							$stmtf2->bind_param('ss', $fid, $id);
    							$stmtf2->execute();
    							$stmtf2->close();
    						}
						} 
					} 
				}
			}

			echo json_encode(
				array(
					"status"=> "success", 
					"message"=> "Your company profile is updated successfully!" 
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