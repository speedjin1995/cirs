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
$empQuery = "SELECT validate_by, status, COUNT(*) AS count FROM other_validations WHERE deleted = 0 AND validate_by IN (1,5)".$searchQuery." GROUP BY status, validate_by order by validate_by";
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
  $validateBy = $row['validate_by'];
  $status = $row['status'];
  $pivotData[$status][$validateBy] = $row['count'];
}

## Format data for DataTable
$data = [
  [
    "pending_procal" => $pivotData['Pending']['1'] ?? "0",
    "complete_procal" => $pivotData['Complete']['1'] ?? "0",
    "cancel_procal" => $pivotData['Cancelled']['1'] ?? "0",
    "pending_sirim" => $pivotData['Pending']['5'] ?? "0",
    "complete_sirim" => $pivotData['Complete']['5'] ?? "0",
    "cancel_sirim" => $pivotData['Cancelled']['5'] ?? "0"
  ]
];

## Response
$response = array(
  "aaData" => $data
);

echo json_encode($response);

?>