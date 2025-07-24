<?php
## Database configuration
require_once 'db_connect.php';
require_once 'requires/lookup.php';

## Search 
$searchQuery = " ";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery = " and last_calibration_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and last_calibration_date <= '".$toDateTime."'";
}

## Fetch records
$empQuery = "SELECT status, COUNT(*) AS count FROM other_validations WHERE deleted = 0 AND validate_by IN (1,5)".$searchQuery." GROUP BY status order by status ASC";
$empRecords = mysqli_query($db, $empQuery);
$data = array();

## Process results into pivot format
$pivotData = [
  "Pending" => [],
  "Complete" => [],
  "Cancelled" => []
];

$validateByList = [1, 5]; 

while ($row = mysqli_fetch_assoc($empRecords)) {
  $status = $row['status'];
  $pivotData[$status] = $row['count'];
}

## Format data for DataTable
$data = [
  [
    "pending" => !empty($pivotData['Pending']) ? $pivotData['Pending'] : "0",
    "complete" => !empty($pivotData['Complete']) ? $pivotData['Complete'] : "0",
    "cancel" => !empty($pivotData['Cancelled']) ? $pivotData['Cancelled'] : "0",
  ]
];

## Response
$response = array(
  "aaData" => $data
);

echo json_encode($response);

$db->close();

?>