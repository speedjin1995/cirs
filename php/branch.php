<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.php";</script>';
}

if(isset($_POST['branchName'], $_POST['addressLine1'])){
    $branchName = filter_input(INPUT_POST, 'branchName', FILTER_SANITIZE_STRING);
    $addressLine1 = filter_input(INPUT_POST, 'addressLine1', FILTER_SANITIZE_STRING);

    $branchCode = null;
    $addressLine2 = null;
    $addressLine3 = null;
    $addressLine4 = null;
    $mapUrl = null;
    $pic = null;
    $picContact = null;
    $officeNo = null;
    $email = null;

    if(isset($_POST['branchCode']) && $_POST['branchCode'] != null && $_POST['branchCode'] != ''){
        $branchCode = filter_input(INPUT_POST, 'branchCode', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['addressLine2']) && $_POST['addressLine2'] != null && $_POST['addressLine2'] != ''){
        $addressLine2 = filter_input(INPUT_POST, 'addressLine2', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['addressLine3']) && $_POST['addressLine3'] != null && $_POST['addressLine3'] != ''){
        $addressLine3 = filter_input(INPUT_POST, 'addressLine3', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['addressLine4']) && $_POST['addressLine4'] != null && $_POST['addressLine4'] != ''){
        $addressLine4 = filter_input(INPUT_POST, 'addressLine4', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['mapUrl']) && $_POST['mapUrl'] != null && $_POST['mapUrl'] != ''){
        $mapUrl = filter_input(INPUT_POST, 'mapUrl', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['pic']) && $_POST['pic'] != null && $_POST['pic'] != ''){
        $pic = filter_input(INPUT_POST, 'pic', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['picContact']) && $_POST['picContact'] != null && $_POST['picContact'] != ''){
        $picContact = filter_input(INPUT_POST, 'picContact', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['officeNo']) && $_POST['officeNo'] != null && $_POST['officeNo'] != ''){
        $officeNo = filter_input(INPUT_POST, 'officeNo', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['email']) && $_POST['email'] != null && $_POST['email'] != ''){
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    }

    if($_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE `company_branches` SET `branch_code`=?, `branch_name`=?, `address_line_1`=?, `address_line_2`=?, `address_line_3`=?, `address_line_4`=?, `map_url`=?, `pic`=?, `pic_contact`=?, `office_no`=?, `email`=? WHERE id=?")) {
            $update_stmt->bind_param('ssssssssssss', $branchCode, $branchName, $addressLine1, $addressLine2, $addressLine3, $addressLine4, $mapUrl, $pic, $picContact, $officeNo, $email, $_POST['id']);
            
            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                $update_stmt->close();
                $db->close();

                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );
            }
            else{
                $update_stmt->close();
                $db->close();
                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }
        }
    }
    else{
        if ($insert_stmt = $db->prepare("INSERT INTO `company_branches` (`branch_code`, `branch_name`, `address_line_1`, `address_line_2`, `address_line_3`, `address_line_4`, `map_url`, `pic`, `pic_contact`, `office_no`, `email`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssssss', $branchCode, $branchName, $addressLine1, $addressLine2, $addressLine3, $addressLine4, $mapUrl, $pic, $picContact, $officeNo, $email);
            
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                $insert_stmt->close();
                $db->close();

                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $insert_stmt->error
                    )
                );
            }
            else{
                $insert_stmt->close();
                $db->close();
                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );
            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Something goes wrong when create branch"
                )
            );
        }
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