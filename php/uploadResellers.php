<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['userID'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    foreach ($data as $row) {
        $OtherCodeAutoCountetc = !empty($row['OtherCodeAutoCountetc']) ? $row['OtherCodeAutoCountetc'] : null;
        $CompanyName = !empty($row['CompanyName']) ? trim($row['CompanyName']) : '';

        if(searchCustIdByName($CompanyName, $db) != null){

        }
        else{
            $Address1 = !empty($row['Address1']) ? $row['Address1'] : '';
            $Address2 = !empty($row['Address2']) ? $row['Address2'] : '';
            $Address3 = !empty($row['Address3']) ? $row['Address3'] : '';
            $Address4 = !empty($row['Address4']) ? $row['Address4'] : '';
            $Phone = !empty($row['Phone']) ? $row['Phone'] : '';
            $PIC = !empty($row['PIC']) ? $row['PIC'] : '';
            $PICContact = !empty($row['PICContact']) ? $row['PICContact'] : null;
            $Email = !empty($row['Email']) ? $row['Email'] : null;
            $MAPURL = !empty($row['MAPURL']) ? $row['MAPURL'] : null;
    
            if ($insert_stmt = $db->prepare("INSERT INTO stamping (customers, brand, descriptions, model, capacity, serial_no, validate_by, stamping_no, invoice_no, stamping_date, due_date, pic) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('ssssssssssss', $customers, $brands, $machineTypes, $models, $capacitys, $serials, $validators, $stampings, $invoices, $stampDates, $dueDates, $uid);
                $insert_stmt->execute();
                $insert_stmt->close(); // Close the statement after execution
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
