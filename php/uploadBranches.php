<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['userID'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    $errorArray = [];
    foreach ($data as $row) {
        $BranchCode = (isset($row['BranchCode']) && !empty($row['BranchCode']) && $row['BranchCode'] !== '' && $row['BranchCode'] !== null) ? trim($row['BranchCode']) : '';
        $BranchName = (isset($row['BranchName']) && !empty($row['BranchName']) && $row['BranchName'] !== '' && $row['BranchName'] !== null) ? trim($row['BranchName']) : '';
        $Address1 = (isset($row['Address1']) && !empty($row['Address1']) && $row['Address1'] !== '' && $row['Address1'] !== null) ? trim($row['Address1']) : '';
        $Address2 = (isset($row['Address2']) && !empty($row['Address2']) && $row['Address2'] !== '' && $row['Address2'] !== null) ? trim($row['Address2']) : '';
        $Address3 = (isset($row['Address3']) && !empty($row['Address3']) && $row['Address3'] !== '' && $row['Address3'] !== null) ? trim($row['Address3']) : '';
        $Address4 = (isset($row['Address4']) && !empty($row['Address4']) && $row['Address4'] !== '' && $row['Address4'] !== null) ? trim($row['Address4']) : '';
        $PIC = (isset($row['PIC']) && !empty($row['PIC']) && $row['PIC'] !== '' && $row['PIC'] !== null) ? trim($row['PIC']) : '';
        $PICContact = (isset($row['PICContact']) && !empty($row['PICContact']) && $row['PICContact'] !== '' && $row['PICContact'] !== null) ? trim($row['PICContact']) : '';
        $Email = (isset($row['Email']) && !empty($row['Email']) && $row['Email'] !== '' && $row['Email'] !== null) ? trim($row['Email']) : '';
        $OfficeNo = (isset($row['OfficeNo']) && !empty($row['OfficeNo']) && $row['OfficeNo'] !== '' && $row['OfficeNo'] !== null) ? trim($row['OfficeNo']) : '';
        $MAPURL = (isset($row['MAPURL']) && !empty($row['MAPURL']) && $row['MAPURL'] !== '' && $row['MAPURL'] !== null) ? trim($row['MAPURL']) : '';

        if ($checkDuplicate = $db->prepare("SELECT id FROM company_branches WHERE branch_code = ? AND branch_name = ? AND deleted = '0'")) {
            $checkDuplicate->bind_param('ss', $BranchCode, $BranchName);
            $checkDuplicate->execute();
            $result = $checkDuplicate->get_result();

            if($result->num_rows > 0) {
                $errMsg = "Company Branch: ".$BranchCode." - ".$BranchName." already exists.";
                $errorArray[] = $errMsg;
                continue;
            }
            $checkDuplicate->close();
        }

        if($BranchName != null && $BranchName != '' && $Address1 != null && $Address1 != ''){
            if ($insert_stmt = $db->prepare("INSERT INTO `company_branches` (`branch_code`, `branch_name`, `address_line_1`, `address_line_2`, `address_line_3`, `address_line_4`, `map_url`, `pic`, `pic_contact`, `office_no`, `email`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('sssssssssss', $BranchCode, $BranchName, $Address1, $Address2, $Address3, $Address4, $MAPURL, $PIC, $PICContact, $OfficeNo, $Email);
                $insert_stmt->execute();
                $insert_stmt->close();
            }
        }
    }

    $db->close();

    if (!empty($errorArray)){
        echo json_encode(
            array(
                "status"=> "error", 
                "message"=> $errorArray 
            )
        );
    }else{
        echo json_encode(
            array(
                "status"=> "success", 
                "message"=> "Added Successfully!!" 
            )
        );
    }
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>
