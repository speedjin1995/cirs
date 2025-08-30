<?php
## Database configuration
require_once 'db_connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";
if($searchValue != ''){
   $searchQuery .= " AND officer_name like '%".$searchValue."%' OR officer_contact like '%".$searchValue."%' OR officer_position like '%".$searchValue."%'";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from validator_officers, validators, state WHERE validator_officers.officer_company = validators.id AND validator_officers.officer_cawangan = state.id");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from validator_officers, validators, state WHERE validator_officers.officer_company = validators.id AND validator_officers.officer_cawangan = state.id AND validator_officers.deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select validator_officers.*, validators.validator AS validator, state.state AS cawangan from validator_officers, validators, state WHERE validator_officers.officer_company = validators.id AND validator_officers.officer_cawangan = state.id AND validator_officers.deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array( 
      "counter"=>$counter,
      "id"=>$row['id'],
      "officer_name"=>$row['officer_name'],
      "officer_contact"=>$row['officer_contact'],
      "officer_position"=>$row['officer_position'],
      "officer_company"=>$row['validator'],
      "officer_cawangan"=>$row['cawangan']
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