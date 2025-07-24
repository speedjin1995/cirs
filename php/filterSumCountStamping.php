<?php
## Database configuration
require_once 'db_connect.php';
require_once 'requires/lookup.php';

## Read value
// $draw = $_POST['draw'];
// $row = $_POST['start'];
// $rowperpage = $_POST['length']; // Rows display per page
// $columnIndex = $_POST['order'][0]['column']; // Column index
// $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
// $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
// $searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery = " and stamping_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and stamping_date <= '".$toDateTime."'";
}

## Fetch records
$empQuery = "SELECT validate_by, status, COUNT(*) AS count FROM stamping WHERE deleted = 0 AND validate_by IN (9,10)".$searchQuery." GROUP BY status, validate_by order by validate_by";
$empRecords = mysqli_query($db, $empQuery);
$data = array();

## Process results into pivot format
$pivotData = [
  "Pending" => [],
  "Complete" => [],
  "Cancelled" => []
];

$validateByList = [9, 10]; 

while ($row = mysqli_fetch_assoc($empRecords)) {
  $validateBy = $row['validate_by'];
  $status = $row['status'];
  $pivotData[$status][$validateBy] = $row['count'];
}

## Format data for DataTable
$data = [
  [
    "pending_metrology" => $pivotData['Pending']['10'] ?? "0",
    "complete_metrology" => $pivotData['Complete']['10'] ?? "0",
    "cancel_metrology" => $pivotData['Cancelled']['10'] ?? "0",
    "pending_demetrology" => $pivotData['Pending']['9'] ?? "0",
    "complete_demetrology" => $pivotData['Complete']['9'] ?? "0",
    "cancel_demetrology" => $pivotData['Cancelled']['9'] ?? "0"
  ]
];

## Response
$response = array(
  "aaData" => $data
);

echo json_encode($response);

$db->close();

?>