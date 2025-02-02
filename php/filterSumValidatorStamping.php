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
  $fromDate = $dateTime;
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and stamping_date <= '".$toDateTime."'";
  $toDate = $dateTime;
}

$months = calcDateDifference($fromDate, $toDate);

## Fetch records
$empQuery = "SELECT validate_by, status, COUNT(*) AS count FROM stamping WHERE deleted = 0 AND validate_by IN (9,10)".$searchQuery." GROUP BY status, validate_by order by validate_by";
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
  
}

## Format data for DataTable
$columns = ["column_1"]; // Start with the first static column
foreach ($months as $index => $month) {
    $columns[] = "column_" . ($index + 2); // Create dynamic column names (column_2, column_3, etc.)
}
$columns[] = "last_column"; // Always add the last column for "Sub Total"

// Create data rows
$data[] = array_merge(["column_1" => "Metrology"], array_combine(array_slice($columns, 1, -1), $months), ["last_column" => "Sub Total"]);
$data[] = array_merge(["column_1" => "Sub Total Cost"], array_fill_keys(array_slice($columns, 1, -1), ""), ["last_column" => ""]);
$data[] = array_merge(["column_1" => "Sub Total SST"], array_fill_keys(array_slice($columns, 1, -1), ""), ["last_column" => ""]);
$data[] = array_merge(["column_1" => "DE Metrology"], array_combine(array_slice($columns, 1, -1), $months), ["last_column" => "Sub Total"]);
$data[] = array_merge(["column_1" => "Sub Total Cost"], array_fill_keys(array_slice($columns, 1, -1), ""), ["last_column" => ""]);
$data[] = array_merge(["column_1" => "Sub Total SST"], array_fill_keys(array_slice($columns, 1, -1), ""), ["last_column" => ""]);


## Response
$response = array(
  "columns" => $columns,
  "aaData" => $data
);

echo json_encode($response);


function calcDateDifference($fromDate, $toDate){
  // Define the interval of 1 month
  $interval = new DateInterval('P1M'); // P1M means a period of 1 month

  // Create a DatePeriod object
  $period = new DatePeriod($fromDate, $interval, $toDate);

  $months = [];
  foreach ($period as $date) {
      $months[] = $date->format('M-y'); // Store each month in 'YYYY-MM' format
  }

  return $months;
}

?>