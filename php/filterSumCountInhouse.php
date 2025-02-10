<?php
## Database configuration
require_once 'db_connect.php';
require_once 'requires/lookup.php';

## Search 
$searchQuery = " ";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery = " and validation_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and validation_date <= '".$toDateTime."'";
}

## Fetch records
$empQuery = "SELECT validate_by, status, COUNT(*) AS count FROM inhouse_validations WHERE deleted = 0".$searchQuery." GROUP BY status";
$empRecords = mysqli_query($db, $empQuery);
$data = array();

## Process results into pivot format
$pivotData = [
  "Pending" => [],
  "Complete" => [],
  "Cancelled" => []
];

$validateByList = [15]; 

while ($row = mysqli_fetch_assoc($empRecords)) {
  $validateBy = $row['validate_by'];
  $status = $row['status'];
  $pivotData[$status][$validateBy] = $row['count'];
}

## Format data for DataTable
$data = [
  [
    "pending" => $pivotData['Pending']['15'] ?? "0",
    "complete" => $pivotData['Complete']['15'] ?? "0",
    "cancel" => $pivotData['Cancelled']['15'] ?? "0"
  ]
];

## Response
$response = array(
  "aaData" => $data
);

echo json_encode($response);

?>