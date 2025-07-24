<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.php";</script>';
}
else{
    $userId = $_SESSION['userID'];
}

if(isset($_POST['name'])){
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $address2 = filter_input(INPUT_POST, 'address2', FILTER_SANITIZE_STRING);
    $address3 = "";
    $address4 = "";
    $customerMapUrl = "";
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

    $branchAddress1 = $_POST['branch_address1'] ?? [];
    $branchAddress2 = $_POST['branch_address2'] ?? [];
    $branchAddress3 = $_POST['branch_address3'] ?? [];
    $branchAddress4 = $_POST['branch_address4'] ?? [];
    $branchName = $_POST['branch_name'] ?? [];
    $branchCode = $_POST['branch_code'] ?? [];
    $mapUrl = $_POST['map_url'] ?? [];
    $branchid = $_POST['branch_id'] ?? [];
    $branchPhone = $_POST['branchPhone'] ?? [];
    $branchEmail = $_POST['branchEmail'] ?? [];
    $branchPic = $_POST['branchPic'] ?? [];
    $branchPicContact = $_POST['branchPicContact'] ?? [];
    $deletedShip = $_POST['deletedShip'] ?? [];

    //$address3 = "";
    //$address4 = "";
    $otherCode = null;
    $pic = null;
    $picContact = null;
    $code = null;
    $email = null;
    $phone = null;
    $dealer = null;

    if(isset($_POST['otherCode']) && $_POST['otherCode'] != null && $_POST['otherCode'] != ""){
        $otherCode = filter_input(INPUT_POST, 'otherCode', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['address3']) && $_POST['address3'] != null && $_POST['address3'] != ""){
        $address3 = filter_input(INPUT_POST, 'address3', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['address4']) && $_POST['address4'] != null && $_POST['address4'] != ""){
        $address4 = filter_input(INPUT_POST, 'address4', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['customer_map_url']) && $_POST['customer_map_url'] != null && $_POST['customer_map_url'] != ""){
        $customerMapUrl = $_POST['customer_map_url'] ?? [];
    }

    if(isset($_POST['code'] ) && $_POST['code'] != null && $_POST['code'] != ""){
        $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['email'] ) && $_POST['email'] != null && $_POST['email'] != ""){
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['phone'] ) && $_POST['phone'] != null && $_POST['phone'] != ""){
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['dealer'] ) && $_POST['dealer'] != null && $_POST['dealer'] != ""){
        $dealer = filter_input(INPUT_POST, 'dealer', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['pic']) && $_POST['pic'] != null && $_POST['pic'] != ""){
        $pic = filter_input(INPUT_POST, 'pic', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['picContact']) && $_POST['picContact'] != null && $_POST['picContact'] != ""){
        $picContact = filter_input(INPUT_POST, 'picContact', FILTER_SANITIZE_STRING);
    }
    
    if(isset($_POST['id'] ) && $_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE customers SET dealer=?, customer_code=?, other_code=?, customer_name=?, customer_address=?, address2=?, address3=?, address4=?, map_url=?, customer_phone=?, customer_email=?, pic=?, pic_contact=? WHERE id=?")) {
            $update_stmt->bind_param('ssssssssssssss', $dealer, $code, $otherCode, $name, $address, $address2, $address3, $address4, $customerMapUrl, $phone, $email, $pic, $picContact, $_POST['id']);
            
            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                $update_stmt->close();
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );
            }
            else{
                $update_stmt->close();
                $del = '1';

                if ($insert_stmt3 = $db->prepare("UPDATE branches SET deleted = ? WHERE customer_id = ?")) {
                    // Bind parameters using variables (which are references)
                    $insert_stmt3->bind_param('ss', $del, $_POST['id']);
                    $insert_stmt3->execute(); // Execute the statement
                    $insert_stmt3->close(); // Close the insert statement for this loop iteration
                }

                if(count($branchAddress1) > 0){
                    // Loop through the addresses and insert into branches
                    for ($i = 0; $i < count($branchAddress1); $i++) {
                        if (!in_array($i, $deletedShip)) {
                            $addr1 = $branchAddress1[$i] ?? '';
                            $addr2 = $branchAddress2[$i] ?? '';
                            $addr3 = $branchAddress3[$i] ?? '';
                            $addr4 = $branchAddress4[$i] ?? '';
                            $branchNameValue = isset($branchName[$i]) ? $branchName[$i] : '';
                            $branchCodeValue = isset($branchCode[$i]) ? $branchCode[$i] : '';
                            $mapUrlValue = isset($mapUrl[$i]) ? $mapUrl[$i] : '';
                            $branchPhoneValue = isset($branchPhone[$i]) ? $branchPhone[$i] : '';
                            $branchEmailValue = isset($branchEmail[$i]) ? $branchEmail[$i] : '';
                            $branchPicValue = isset($branchPic[$i]) ? $branchPic[$i] : '';
                            $branchPicContactVAlue = isset($branchPicContact[$i]) ? $branchPicContact[$i] : '';


                            if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4, branch_code, branch_name, map_url, office_no, email, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                                $insert_stmt2->bind_param('ssssssssssss', $_POST['id'], $addr1, $addr2, $addr3, $addr4, $branchCodeValue, $branchNameValue, $mapUrlValue, $branchPhoneValue, $branchEmailValue, $branchPicValue, $branchPicContactVAlue);
                                $insert_stmt2->execute();
                                $insert_stmt2->close();
                            } 
                            else {
                                echo "Error preparing statement for address $i: " . $db->error;
                            }
                        }
                    }
                }
                else{
                    $addr1 = $address ?? '';
                    $addr2 = $address2 ?? '';
                    $addr3 = $address3 ?? '';
                    $addr4 = $address4 ?? '';
                    $branchNameValue = 'HQ';
                    $branchCodeValue = '';
                    $mapUrlValue = isset($customerMapUrl) ? $customerMapUrl : '';
                    $branchPhoneValue = isset($phone) ? $phone : '';
                    $branchEmailValue = isset($email) ? $email : '';
                    $branchPicValue = isset($pic) ? $pic : '';
                    $branchPicContactVAlue = isset($picContact) ? $picContact : '';


                    if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4, branch_code, branch_name, map_url, office_no, email, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                        $insert_stmt2->bind_param('ssssssssssss', $_POST['id'], $addr1, $addr2, $addr3, $addr4, $branchCodeValue, $branchNameValue, $mapUrlValue, $branchPhoneValue, $branchEmailValue, $branchPicValue, $branchPicContactVAlue);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
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
        # to generate customer code
        $custNameFirstLetter = substr($name, 0, 1);
        $firstChar = $custNameFirstLetter;
        $code = 'C-'.strtoupper($custNameFirstLetter);

        $customerQuery = "SELECT * FROM customers WHERE customer_code LIKE '%$code%' ORDER BY customer_code DESC";
        $customerDetail = mysqli_query($db, $customerQuery);
        $customerRow = mysqli_fetch_assoc($customerDetail);

        $customerCode = null;
        $codeSeq = null;
        $count = '';

        if(!empty($customerRow)){
            $customerCode = $customerRow['customer_code'];
            preg_match('/\d+/', $customerCode, $matches);
            $codeSeq = (int)$matches[0]; 
            $nextSeq = $codeSeq+1;
            $count = str_pad($nextSeq, 4, '0', STR_PAD_LEFT); 
            $code.=$count;
        }
        else{
            $nextSeq = 1;
            $count = str_pad($nextSeq, 4, '0', STR_PAD_LEFT); 
            $code.=$count;
        } 
            
        if ($insert_stmt = $db->prepare("INSERT INTO customers (dealer, customer_code, other_code, customer_name, customer_address, address2, address3, address4, map_url, customer_phone, customer_email, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssssssss', $dealer, $code, $otherCode, $name, $address, $address2, $address3, $address4, $customerMapUrl, $phone, $email, $pic, $picContact);
            
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                $insert_stmt->close(); // Close the first insert statement
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $insert_stmt->error
                    )
                );
            }
            else{
                $invid = $insert_stmt->insert_id; // Get the inserted customer ID
                $insert_stmt->close(); // Close the first insert statement

                // Loop through the addresses and insert into branches
                if(count($branchAddress1) > 0){
                    for ($i = 0; $i < count($branchAddress1); $i++) {
                        // Only insert if the index is not in the deletedShip array
                        if (!in_array($i, $deletedShip)) {
                            // Assign array elements to variables to pass as references
                            $addr1 = $branchAddress1[$i] ?? '';
                            $addr2 = $branchAddress2[$i] ?? '';
                            $addr3 = $branchAddress3[$i] ?? '';
                            $addr4 = $branchAddress4[$i] ?? '';
                            $branchNameValue = isset($branchName[$i]) ? $branchName[$i] : '';
                            $branchCodeValue = isset($branchCode[$i]) ? $branchCode[$i] : '';
                            $mapUrlValue = isset($mapUrl[$i]) ? $mapUrl[$i] : '';
                            $branchPhoneValue = isset($branchPhone[$i]) ? $branchPhone[$i] : '';
                            $branchEmailValue = isset($branchEmail[$i]) ? $branchEmail[$i] : '';
                            $branchPicValue = isset($branchPic[$i]) ? $branchPic[$i] : '';
                            $branchPicContactVAlue = isset($branchPicContact[$i]) ? $branchPicContact[$i] : '';
    
                            if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4, branch_code, branch_name, map_url, office_no, email, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                                $insert_stmt2->bind_param('ssssssssssss', $invid, $addr1, $addr2, $addr3, $addr4, $branchCodeValue, $branchNameValue, $mapUrlValue, $branchPhoneValue, $branchEmailValue, $branchPicValue, $branchPicContactVAlue);
                                $insert_stmt2->execute();
                                $insert_stmt2->close();
                            }
                        }
                    }
                }
                else{
                    $addr1 = $address ?? '';
                    $addr2 = $address2 ?? '';
                    $addr3 = $address3 ?? '';
                    $addr4 = $address4 ?? '';
                    $branchNameValue = 'HQ';
                    $branchCodeValue = '';
                    $mapUrlValue = isset($customerMapUrl) ? $customerMapUrl : '';
                    $branchPhoneValue = isset($phone) ? $phone : '';
                    $branchEmailValue = isset($email) ? $email : '';
                    $branchPicValue = isset($pic) ? $pic : '';
                    $branchPicContactVAlue = isset($picContact) ? $picContact : '';

                    if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4, branch_code, branch_name, map_url, office_no, email, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                        $insert_stmt2->bind_param('ssssssssssss', $invid, $addr1, $addr2, $addr3, $addr4, $branchCodeValue, $branchNameValue, $mapUrlValue, $branchPhoneValue, $branchEmailValue, $branchPicValue, $branchPicContactVAlue);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );
            }

            $db->close();
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
