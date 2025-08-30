<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.php";</script>';
}

if(isset($_POST['name'])){
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
	$contactNo = null;
	$position = null;
	$validator = null;
	$cawangan = null;

    if(isset($_POST['contactNo']) && $_POST['contactNo']!=null && $_POST['contactNo']!=""){
		$contactNo = $_POST['contactNo'];
	}

	if(isset($_POST['position']) && $_POST['position']!=null && $_POST['position']!=""){
		$position = $_POST['position'];
	}

	if(isset($_POST['validator']) && $_POST['validator']!=null && $_POST['validator']!=""){
		$validator = $_POST['validator'];
	}

	if(isset($_POST['cawangan']) && $_POST['cawangan']!=null && $_POST['cawangan']!=""){
		$cawangan = $_POST['cawangan'];
	}

    if($_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE validator_officers SET officer_name=?, officer_contact=?, officer_position=?, officer_company=?, officer_cawangan=? WHERE id=?")) {
            $update_stmt->bind_param('ssssss', $name, $contactNo, $position, $validator, $cawangan, $_POST['id']);
            
            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );
            }
            else{                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }
            $update_stmt->close();
        }
    }
    else{
        if ($insert_stmt = $db->prepare("INSERT INTO validator_officers (officer_name, officer_contact, officer_position, officer_company, officer_cawangan) VALUES (?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssss', $name, $contactNo, $position, $validator, $cawangan);

            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $insert_stmt->error
                    )
                );
            }
            else{
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );
            }

            $insert_stmt->close();
        }
    }

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