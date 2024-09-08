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

if(isset($_POST['code'], $_POST['name'])){
    $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];
    $address3 = $_POST['address3'];
    $address4 = $_POST['address4'];
    $branchid = $_POST['branch_id'];
    $deletedShip = $_POST['deletedShip'] ?? [];
    $deletedBranch = $_POST['deletedBranch'] ?? [];

    //$address3 = "";
    //$address4 = "";
    $email = null;
    $phone = null;
    $dealer = null;

    if(isset($_POST['email'] ) && $_POST['email'] != null && $_POST['email'] != ""){
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['phone'] ) && $_POST['phone'] != null && $_POST['phone'] != ""){
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['dealer'] ) && $_POST['dealer'] != null && $_POST['dealer'] != ""){
        $dealer = filter_input(INPUT_POST, 'dealer', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['id'] ) && $_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE customers SET dealer=?, customer_code=?, customer_name=?, customer_phone=?, customer_email=? WHERE id=?")) {
            $update_stmt->bind_param('ssssss', $dealer, $code, $name, $phone, $email, $_POST['id']);
            
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
                $del = '1';

                if ($insert_stmt3 = $db->prepare("UPDATE branches SET deleted = ? WHERE customer_id = ?")) {
                    // Bind parameters using variables (which are references)
                    $insert_stmt3->bind_param('ss', $del, $_POST['id']);
                    $insert_stmt3->execute(); // Execute the statement
                    $insert_stmt3->close(); // Close the insert statement for this loop iteration
                }

                // Loop through the addresses and insert into branches
                for ($i = 0; $i < count($address1); $i++) {
                    if (!in_array($i, $deletedShip)) {
                        $addr1 = $address1[$i] ?? '';
                        $addr2 = $address2[$i] ?? '';
                        $addr3 = $address3[$i] ?? '';
                        $addr4 = $address4[$i] ?? '';

                        if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4) VALUES (?, ?, ?, ?, ?)")) {
                            $insert_stmt2->bind_param('sssss', $_POST['id'], $addr1, $addr2, $addr3, $addr4);
                            $insert_stmt2->execute();
                            $insert_stmt2->close();
                        } 
                        else {
                            echo "Error preparing statement for address $i: " . $db->error;
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
        if ($insert_stmt = $db->prepare("INSERT INTO customers (dealer, customer_code, customer_name, customer_phone, customer_email) VALUES (?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssss', $dealer, $code, $name, $phone, $email);
            
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
                $invid = $insert_stmt->insert_id; // Get the inserted customer ID
                $insert_stmt->close(); // Close the first insert statement

                // Loop through the addresses and insert into branches
                for ($i = 0; $i < count($address1); $i++) {
                    // Only insert if the index is not in the deletedShip array
                    if (!in_array($i, $deletedShip)) {
                        // Assign array elements to variables to pass as references
                        $addr1 = $address1[$i] ?? '';
                        $addr2 = $address2[$i] ?? '';
                        $addr3 = $address3[$i] ?? '';
                        $addr4 = $address4[$i] ?? '';

                        if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4) VALUES (?, ?, ?, ?, ?)")) {
                            // Bind parameters using variables (which are references)
                            $insert_stmt2->bind_param('issss', $invid, $addr1, $addr2, $addr3, $addr4);
                            $insert_stmt2->execute(); // Execute the statement
                            $insert_stmt2->close(); // Close the insert statement for this loop iteration
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