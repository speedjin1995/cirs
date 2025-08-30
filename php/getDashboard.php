<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['startDate'], $_POST['endDate'])){
    ## Search 
    $stampingSearchQuery = " ";
    $otherSearchQuery = " ";
    $inhouseSearchQuery = " ";

    if($_POST['startDate'] != null && $_POST['startDate'] != ''){
        $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['startDate']);
        $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
        $stampingSearchQuery .= " and stamping_date >= '".$fromDateTime."'";
        $otherSearchQuery .= " and last_calibration_date >= '".$fromDateTime."'";
        $inhouseSearchQuery .= " and validation_date >= '".$fromDateTime."'";
    }

    if($_POST['endDate'] != null && $_POST['endDate'] != ''){
        $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['endDate']);
        $toDateTime = $dateTime->format('Y-m-d 23:59:59');
        $stampingSearchQuery .= " and stamping_date <= '".$toDateTime."'";
        $otherSearchQuery .= " and last_calibration_date <= '".$toDateTime."'";
        $inhouseSearchQuery .= " and validation_date <= '".$toDateTime."'";
    }
    
    $allData = array();
    
    // Stamping Chart
    if ($stamping_select_stmt = $db->prepare("SELECT status, COUNT(*) as count FROM stamping WHERE deleted='0'".$stampingSearchQuery." GROUP BY status")) {
        if ($stamping_select_stmt->execute()) {
            $stamping_result = $stamping_select_stmt->get_result();
            $stampingData = array();
            while ($row = $stamping_result->fetch_assoc()) {
                $stampingData[$row['status']] = $row['count'];
            }
            $allData['stamping'] = $stampingData;
        }
        $stamping_select_stmt->close();
    }

    // Other Validation Chart
    if ($other_select_stmt = $db->prepare("SELECT status, COUNT(*) as count FROM other_validations WHERE deleted='0'".$otherSearchQuery." GROUP BY status")) {
        if ($other_select_stmt->execute()) {
            $other_result = $other_select_stmt->get_result();
            $otherData = array();
            while ($row = $other_result->fetch_assoc()) {
                $otherData[$row['status']] = $row['count'];
            }
            $allData['other'] = $otherData;
        }
        $other_select_stmt->close();
    }

    // Inhouse Validation Chart
    if ($inhouse_select_stmt = $db->prepare("SELECT status, COUNT(*) as count FROM inhouse_validations WHERE deleted='0'".$inhouseSearchQuery." GROUP BY status")) {
        if ($inhouse_select_stmt->execute()) {
            $inhouse_result = $inhouse_select_stmt->get_result();
            $inhouseData = array();
            while ($row = $inhouse_result->fetch_assoc()) {
                $inhouseData[$row['status']] = $row['count'];
            }
            $allData['inhouse'] = $inhouseData;
        }
        $inhouse_select_stmt->close();
    } 
    
    $db->close();

    echo json_encode(
        array(
            "status" => "success",
            "message" => $allData
        )
    );

}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Parameter"
        )
    ); 
}
