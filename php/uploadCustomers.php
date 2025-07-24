<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['userID'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    foreach ($data as $rows) {
        $OtherCodeAutoCountetc = !empty($rows['OtherCodeAutoCountetc']) ? $rows['OtherCodeAutoCountetc'] : null;
        $CompanyName = !empty($rows['CompanyName']) ? trim($rows['CompanyName']) : '';
        $Address1 = !empty($rows['Address1']) ? $rows['Address1'] : '';
        $Address2 = !empty($rows['Address2']) ? $rows['Address2'] : '';
        $Address3 = !empty($rows['Address3']) ? $rows['Address3'] : '';
        $Address4 = !empty($rows['Address4']) ? $rows['Address4'] : '';
        $Phone = !empty($rows['Phone']) ? $rows['Phone'] : '';
        $PIC = !empty($rows['PIC']) ? $rows['PIC'] : '';
        $PICContact = !empty($rows['PICContact']) ? $rows['PICContact'] : null;
        $Email = !empty($rows['Email']) ? $rows['Email'] : null;
        $MAPURL = !empty($rows['MAPURL']) ? $rows['MAPURL'] : null;
        $UnderDealer = ($rows['UnderDealer'] != 'DIRECT CUSTOMER') ? trim($rows['UnderDealer']) : '';

        if($UnderDealer != null && $UnderDealer != ''){
            $UnderDealer = searchDealerIdByName($UnderDealer, $db);
        }

        $cid = searchCustIdByName($CompanyName, $db);

        if($cid != null){
            $count = '1';

            if($misc_stmt = $db->prepare("SELECT COUNT(*) AS branchCodeCount FROM branches WHERE customer_id=? AND deleted = '0'")){
                $misc_stmt->bind_param('s', $custNameFirstLetter);
                $misc_stmt->execute();
                $resultw = $misc_stmt->get_result();
                $roww = $resultw->fetch_assoc();
                $count = $row['branchCodeCount'];
                $misc_stmt->close();
            }

            if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4, branch_code, branch_name, map_url, office_no, email, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $branchC = $OtherCodeAutoCountetc.'-1';
                $insert_stmt2->bind_param('ssssssssssss', $cid, $Address1, $Address2, $Address3, $Address4, $branchC, $branchC, $MAPURL, $Phone, $Email, $PIC, $PICContact);
                $insert_stmt2->execute();
                $insert_stmt2->close();
            }
        }
        else{
            $custNameFirstLetter = substr($CompanyName, 0, 1);
            $firstChar = $custNameFirstLetter;
            $code = $firstChar;

            if($misc_stmt = $db->prepare("SELECT * FROM miscellaneous WHERE code='customer' AND description=?")){
                $misc_stmt->bind_param('s', $custNameFirstLetter);
                $misc_stmt->execute();
                $result = $misc_stmt->get_result();

                if ($row = $result->fetch_assoc()){
                    $charSize = strlen($row['value']);
                    $misValue = $row['value'];
                    
                    $code = 'C-'.strtoupper($custNameFirstLetter);
                    for($i=0; $i<(4-(int)$charSize); $i++){
                        $code.='0';  // S0000
                    }

                    $code.=$misValue;
                    $misValue++;

                    if($updmisc_stmt = $db->prepare("UPDATE miscellaneous SET value=? WHERE code='customer' AND description=?")){
                        $updmisc_stmt->bind_param('ss', $misValue, $firstChar);
                        $updmisc_stmt->execute();
                        $updmisc_stmt->close();
                    }
                }

                $misc_stmt->close();
            }

            if ($insert_stmt = $db->prepare("INSERT INTO customers (dealer, customer_code, other_code, customer_name, customer_address, address2, address3, address4, map_url, customer_phone, customer_email, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('sssssssssssss', $UnderDealer, $code, $OtherCodeAutoCountetc, $CompanyName, $Address1, $Address2, $Address3, $Address4, $MAPURL, $Phone, $Email, $PIC, $PICContact);
                $insert_stmt->execute();
                $invid = $insert_stmt->insert_id; // Get the inserted reseller ID
                $insert_stmt->close();

                if(isset($rows['BranchCode']) && !empty($rows['BranchCode']) && $rows['BranchCode'] != ''){
                    $addr1 = $rows['BranchAddress1'] ?? '';
                    $addr2 = $rows['BranchAddress2'] ?? '';
                    $addr3 = $rows['BranchAddress3'] ?? '';
                    $addr4 = $rows['BranchAddress4'] ?? '';
                    $branchNameValue = isset($rows['BranchName']) ? $rows['BranchName'] : '';
                    $branchCodeValue = isset($rows['BranchCode']) ? $rows['BranchCode'] : '';
                    $mapUrlValue = isset($rows['BranchMAPURL']) ? $rows['BranchMAPURL'] : '';
                    $branchPhoneValue = isset($rows['BranchPhone']) ? $rows['BranchPhone'] : '';
                    $branchEmailValue = isset($rows['BranchEmail']) ? $rows['BranchEmail'] : '';
                    $branchPicValue = isset($rows['BranchPIC']) ? $rows['BranchPIC'] : '';
                    $branchPicContactVAlue = isset($rows['BranchPICContact']) ? $rows['BranchPICContact'] : '';

                    if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4, branch_code, branch_name, map_url, office_no, email, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                        $insert_stmt2->bind_param('ssssssssssss', $invid, $addr1, $addr2, $addr3, $addr4, $branchCodeValue, $branchNameValue, $mapUrlValue, $branchPhoneValue, $branchEmailValue, $branchPicValue, $branchPicContactVAlue);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }
                else{
                    if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4, branch_code, branch_name, map_url, office_no, email, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                        $branchC = $OtherCodeAutoCountetc.'-1';
                        $branchN = 'HQ';
                        $insert_stmt2->bind_param('ssssssssssss', $invid, $Address1, $Address2, $Address3, $Address4, $branchC, $branchN, $MAPURL, $Phone, $Email, $PIC, $PICContact);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }
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
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>
