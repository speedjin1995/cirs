<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}
else{
    $userId = $_SESSION['userID'];
}

if(isset($_POST['code'], $_POST['name'], $_POST['address'], $_POST['address2'], $_POST['phone'], $_POST['email'])){
    $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
	$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $address2 = filter_input(INPUT_POST, 'address2', FILTER_SANITIZE_STRING);
    $address3 = "";
    $address4 = "";
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

    $pic = null;
    $picContact = null;

    # Branches
    $branchAddress1 = $_POST['branch_address1'] ?? [];
    $branchAddress2 = $_POST['branch_address2'] ?? [];
    $branchAddress3 = $_POST['branch_address3'] ?? [];
    $branchAddress4 = $_POST['branch_address4'] ?? [];
    $branchName = $_POST['branch_name'] ?? [];
    $mapUrl = $_POST['map_url'] ?? [];
    $branchid = $_POST['branch_id'] ?? [];

    $branchPhone = $_POST['branchPhone'] ?? [];
    $branchEmail = $_POST['branchEmail'] ?? [];
    $branchPic = $_POST['branchPic'] ?? [];
    $branchPicContact = $_POST['branchPicContact'] ?? [];

    $deletedShip = $_POST['deletedShip'] ?? [];

    if(isset($_POST['address3']) && $_POST['address3'] != null && $_POST['address3'] != ""){
        $address3 = filter_input(INPUT_POST, 'address3', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['address4']) && $_POST['address4'] != null && $_POST['address4'] != ""){
        $address4 = filter_input(INPUT_POST, 'address4', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['pic']) && $_POST['pic'] != null && $_POST['pic'] != ""){
        $pic = filter_input(INPUT_POST, 'pic', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['picContact']) && $_POST['picContact'] != null && $_POST['picContact'] != ""){
        $picContact = filter_input(INPUT_POST, 'picContact', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE dealer SET customer_code=?, customer_name=?, customer_address=?, address2=?, address3=?, address4=?, customer_phone=?, customer_email=?, pic=?, pic_contact=? WHERE id=?")) {
            $update_stmt->bind_param('sssssssssss', $code, $name, $address, $address2, $address3, $address4, $phone, $email, $pic, $picContact, $_POST['id']);
            
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
                $update_stmt->close();

                if ($insert_stmt3 = $db->prepare("UPDATE reseller_branches SET deleted = ? WHERE reseller_id = ?")) {
                    // Bind parameters using variables (which are references)
                    $insert_stmt3->bind_param('ss', $del, $_POST['id']);
                    $insert_stmt3->execute(); // Execute the statement
                    $insert_stmt3->close(); // Close the insert statement for this loop iteration
                }

                // Loop through the addresses and insert into branches
                for ($i = 0; $i < count($branchAddress1); $i++) {
                    if (!in_array($i, $deletedShip)) {
                        $addr1 = $branchAddress1[$i] ?? '';
                        $addr2 = $branchAddress2[$i] ?? '';
                        $addr3 = $branchAddress3[$i] ?? '';
                        $addr4 = $branchAddress4[$i] ?? '';
                        $branchNameValue = isset($branchName[$i]) ? $branchName[$i] : '';
                        $mapUrlValue = isset($mapUrl[$i]) ? $mapUrl[$i] : '';
                        $branchPhoneValue = isset($branchPhone[$i]) ? $branchPhone[$i] : '';
                        $branchEmailValue = isset($branchEmail[$i]) ? $branchEmail[$i] : '';
                        $branchPicValue = isset($branchPic[$i]) ? $branchPic[$i] : '';
                        $branchPicContactVAlue = isset($branchPicContact[$i]) ? $branchPicContact[$i] : '';

                        if ($insert_stmt2 = $db->prepare("INSERT INTO reseller_branches (reseller_id, address, address2, address3, address4, branch_name, map_url, office_no, email, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                            $insert_stmt2->bind_param('sssssssssss', $_POST['id'], $addr1, $addr2, $addr3, $addr4, $branchNameValue, $mapUrlValue, $branchPhoneValue, $branchEmailValue, $branchPicValue, $branchPicContactVAlue);
                            $insert_stmt2->execute();
                            $insert_stmt2->close();
                        }
                    }
                }


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
        if ($insert_stmt = $db->prepare("INSERT INTO dealer (customer_code, customer_name, customer_address, address2, address3, address4, customer_phone, customer_email, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssssssss', $code, $name, $address, $address2, $address3, $address4, $phone, $email, $pic, $picContact);
            
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
                $invid = $insert_stmt->insert_id; // Get the inserted reseller ID
                $insert_stmt->close();

                // Loop through the addresses and insert into reseller_branches
                for ($i = 0; $i < count($branchAddress1); $i++) {
                    // Only insert if the index is not in the deletedShip array
                    if (!in_array($i, $deletedShip)) {
                        // Assign array elements to variables to pass as references
                        $addr1 = $branchAddress1[$i] ?? '';
                        $addr2 = $branchAddress2[$i] ?? '';
                        $addr3 = $branchAddress3[$i] ?? '';
                        $addr4 = $branchAddress4[$i] ?? '';
                        $branchNameValue = isset($branchName[$i]) ? $branchName[$i] : '';
                        $mapUrlValue = isset($mapUrl[$i]) ? $mapUrl[$i] : '';
                        $branchPhoneValue = isset($branchPhone[$i]) ? $branchPhone[$i] : '';
                        $branchEmailValue = isset($branchEmail[$i]) ? $branchEmail[$i] : '';
                        $branchPicValue = isset($branchPic[$i]) ? $branchPic[$i] : '';
                        $branchPicContactVAlue = isset($branchPicContact[$i]) ? $branchPicContact[$i] : '';

                        if ($insert_stmt2 = $db->prepare("INSERT INTO reseller_branches (reseller_id, address, address2, address3, address4, branch_name, map_url, office_no, email, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                            $insert_stmt2->bind_param('sssssssssss', $_POST['id'], $addr1, $addr2, $addr3, $addr4, $branchNameValue, $mapUrlValue, $branchPhoneValue, $branchEmailValue, $branchPicValue, $branchPicContactVAlue);
                            $insert_stmt2->execute();
                            $insert_stmt2->close();
                        }
                    }
                }

                $db->close();
                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );
            }
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