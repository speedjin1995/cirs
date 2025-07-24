<?php
## Database configuration
require_once 'db_connect.php';
require_once 'requires/lookup.php';

## Search 
$searchQuery = " ";

if($_POST['status'] != null && $_POST['status'] != ''){
  if ($_POST['status'] == 'pending'){
    $searchQuery = " and status = 'Pending'";
  }elseif($_POST['status'] == 'complete'){
    $searchQuery = " and status = 'Complete'";
  }elseif ($_POST['status'] == 'cancel') {
    $searchQuery = " and status = 'Cancelled'";
  }
}

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery .= " and last_calibration_date >= '".$fromDateTime."'";  
  $fromDate = $dateTime;
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and last_calibration_date <= '".$toDateTime."'";
  $toDate = $dateTime;
}

$months = calcDateDifference($fromDate, $toDate);

if(empty($months)){
  $columns = ["column_1"]; // Start with the first static column
  $columns[] = "last_column"; // Always add the last column for "Sub Total"
  $data = [];
}else{
  ## Fetch records
  $empQuery = "SELECT validate_by, DATE_FORMAT(last_calibration_date, '%b-%y') AS month, COUNT(*) AS count FROM other_validations WHERE deleted = 0 AND validate_by IN (1,5)".$searchQuery." GROUP BY validate_by,month ORDER BY validate_by,last_calibration_date";
  $empRecords = mysqli_query($db, $empQuery);
  $data = array();

  // Individual Values
  $procalCount = array_fill_keys($months, 0);
  $sirimCount = array_fill_keys($months, 0);

  // Sum Values
  $procalTotalCount = array_fill_keys($months, 0);
  $sirimTotalCount = array_fill_keys($months, 0);

  while ($row = mysqli_fetch_assoc($empRecords)) {
    $monthYear = $row['month'];
    
    if ($row['validate_by'] == 1) {
      $procalCount[$monthYear] = (float) $row['count'];
    } elseif ($row['validate_by'] == 5) {
      $sirimCount[$monthYear] = (float) $row['count'];
    }
  }

  ## Format data for DataTable
  $columns = ["column_1"]; // Start with the first static column
  foreach ($months as $index => $month) {
    $columns[] = "column_" . ($index + 2); // Create dynamic column names (column_2, column_3, etc.)
  }
  $columns[] = "last_column"; // Always add the last column for "Sub Total"

  // Check if there are extra months
  $procalMonthsQuery = array_keys($procalCount);
  $sirimMonthsQuery = array_keys($sirimCount);

  // Filter out any extra months from $metroSumCost that are not in $months
  $procalMonths = array_intersect($months, $procalMonthsQuery);  // Keep only the common months
  $sirimMonths = array_intersect($months, $sirimMonthsQuery);  // Keep only the common months

  // filter arrays for consistency with $months
  $procalCount = array_intersect_key($procalCount, array_flip($procalMonths));
  $sirimCount = array_intersect_key($sirimCount, array_flip($sirimMonths));

  // Create data rows
  $data[] = array_merge(["column_1" => ""], array_combine(array_slice($columns, 1, -1), $months), ["last_column" => "Total"]);
  $data[] = array_merge(["column_1" => "Procal"], array_combine(array_slice($columns, 1, -1), array_values($procalCount)),  ["last_column" => (float) array_sum($procalCount)]);
  $data[] = array_merge(["column_1" => "Sirim"], array_combine(array_slice($columns, 1, -1), array_values($sirimCount)), ["last_column" => (float) array_sum($sirimCount)]);
}

## Response
$response = array(
  "columns" => $columns,
  "aaData" => $data
);

$db->close(); // Close database connection
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