<?php
## Database configuration
require_once 'db_connect.php';
require_once 'requires/lookup.php';

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

if(empty($months)){
  $columns = ["column_1"]; // Start with the first static column
  $columns[] = "last_column"; // Always add the last column for "Sub Total"
  $data = [];
}else{
  ## Fetch records
  $empQuery = "SELECT validate_by, DATE_FORMAT(stamping_date, '%b-%y') AS month, SUM(subtotal_amount) AS total_subtotal_amount, SUM(sst) AS total_sst FROM stamping WHERE deleted = 0 AND validate_by IN (9,10)".$searchQuery."and status = 'Complete' GROUP BY validate_by, month ORDER BY validate_by, stamping_date";
  $empRecords = mysqli_query($db, $empQuery);
  $data = array();

  // Individual Values
  $metroSubTotalCost = array_fill_keys($months, 0);
  $metroSubTotalSST = array_fill_keys($months, 0);
  $dmetroSubTotalCost = array_fill_keys($months, 0);
  $dmetroSubTotalSST = array_fill_keys($months, 0);

  // Sum Values
  $metroSumCost = array_fill_keys($months, 0);
  $metroSumSst = array_fill_keys($months, 0);
  $dmetroSumCost = array_fill_keys($months, 0);
  $dmetroSumSst = array_fill_keys($months, 0);

  while ($row = mysqli_fetch_assoc($empRecords)) {
    $monthYear = $row['month'];
    
    if ($row['validate_by'] == 10) {
      $metroSumCost[$monthYear] = (float) $row['total_subtotal_amount'];
      $metroSumSst[$monthYear] = (float) $row['total_sst'];
      $metroSubTotalCost[$monthYear] = "RM " . number_format((float) $row['total_subtotal_amount'], 2);
      $metroSubTotalSST[$monthYear] = "RM " . number_format((float) $row['total_sst'], 2);
    } elseif ($row['validate_by'] == 9) {
      $dmetroSumCost[$monthYear] = (float) $row['total_subtotal_amount'];
      $dmetroSumSst[$monthYear] = (float) $row['total_sst'];
      $dmetroSubTotalCost[$monthYear] = "RM " . number_format((float) $row['total_subtotal_amount'], 2);
      $dmetroSubTotalSST[$monthYear] = "RM " . number_format((float) $row['total_sst'], 2);
    }
  }

  ## Format data for DataTable
  $columns = ["column_1"]; // Start with the first static column
  foreach ($months as $index => $month) {
    $columns[] = "column_" . ($index + 2); // Create dynamic column names (column_2, column_3, etc.)
  }
  $columns[] = "last_column"; // Always add the last column for "Sub Total"

  // Check if there are extra months
  $metrologyMonthsQuery = array_keys($metroSumCost);
  $dmetrologyMonthsQuery = array_keys($dmetroSumCost);

  // Filter out any extra months from $metroSumCost that are not in $months
  $metroMonths = array_intersect($months, $metrologyMonthsQuery);  // Keep only the common months
  $dmetroMonths = array_intersect($months, $dmetrologyMonthsQuery);  // Keep only the common months

  // filter arrays for consistency with $months
  $metroSumCost = array_intersect_key($metroSumCost, array_flip($metroMonths));
  $metroSumSst = array_intersect_key($metroSumSst, array_flip($metroMonths));
  $metroSubTotalCost = array_intersect_key($metroSubTotalCost, array_flip($metroMonths));
  $metroSubTotalSST = array_intersect_key($metroSubTotalSST, array_flip($metroMonths));
  $dmetroSumCost = array_intersect_key($dmetroSumCost, array_flip($dmetroMonths));
  $dmetroSumSst = array_intersect_key($dmetroSumSst, array_flip($dmetroMonths));
  $dmetroSubTotalCost = array_intersect_key($dmetroSubTotalCost, array_flip($dmetroMonths));
  $dmetroSubTotalSST = array_intersect_key($dmetroSubTotalSST, array_flip($dmetroMonths));

  // Create data rows
  $data[] = array_merge(["column_1" => "Metrology"], array_combine(array_slice($columns, 1, -1), $months), ["last_column" => "Sub Total"]);
  $data[] = array_merge(["column_1" => "Sub Total Cost"], array_combine(array_slice($columns, 1, -1), array_values($metroSubTotalCost)), ["last_column" => "RM " . number_format((float) array_sum($metroSumCost), 2)]);
  $data[] = array_merge(["column_1" => "Sub Total SST"], array_combine(array_slice($columns, 1, -1), array_values($metroSubTotalSST)), ["last_column" => "RM " . number_format((float) array_sum($metroSumSst), 2)]);
  $data[] = array_merge(["column_1" => "DE Metrology"], array_combine(array_slice($columns, 1, -1), $months), ["last_column" => "Sub Total"]);
  $data[] = array_merge(["column_1" => "Sub Total Cost"], array_combine(array_slice($columns, 1, -1), array_values($dmetroSubTotalCost)), ["last_column" => "RM " . number_format((float) array_sum($dmetroSumCost), 2)]);
  $data[] = array_merge(["column_1" => "Sub Total SST"], array_combine(array_slice($columns, 1, -1), array_values($dmetroSubTotalSST)), ["last_column" => "RM " . number_format((float) array_sum($dmetroSumSst), 2)]);
}

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