<?php
## Database configuration
require_once 'db_connect.php';
require_once 'requires/lookup.php';

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$today = new DateTime("now");
$today->setTime(0, 0, 0);
$today->modify('+1 month');
$newDate = $today->format('Y-m-d 23:59:59');
$searchQuery = "AND status = 'Active' AND due_date <= '".$newDate."'";
/*if($searchValue != ''){
  $searchQuery = " and (inquiry.case_no like '%".$searchValue."%' or inquiry.vehicleNo like '%".$searchValue."%' )";
}*/

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM stamping");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM stamping ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT * FROM stamping ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $remarks = $row['remarks'] != null ? json_decode($row['remarks'], true) : [];

  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "customer_name"=>$row['customers'] != null ? searchCustNameById($row['customers'], $db) : '',
    "brand"=>$row['brand'] != null ? searchBrandNameById($row['brand'], $db) : '',
    "machine_type"=>$row['descriptions'] != null ? searchMachineNameById($row['descriptions'], $db) : '',
    "model"=>$row['model'] != null  ? searchModelNameById($row['model'], $db) : '',
    "capacity"=>$row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '',
    "serial_no"=>$row['serial_no'],
    "validator"=>$row['validate_by'] != null ? searchValidatorNameById($row['validate_by'], $db) : '',
    "stamping_no"=>$row['stamping_no'],
    "invoice_no"=>$row['invoice_no'],
    "stamping_date"=>$row['stamping_date'],
    "due_date"=>$row['due_date'],
    "customer_pic"=>$row['customer_pic'],
    "follow_up_date"=>$row['follow_up_date'],
    "quotation_no"=>$row['quotation_no'],
    "status"=>$row['status'],
    "remarks"=>$remarks
  );

  $counter++;
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

$db->close(); // Close database connection
echo json_encode($response);

?>