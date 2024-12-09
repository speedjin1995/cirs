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
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (
    name like '%".$searchValue."%' OR
    range_type like '%".$searchValue."%' OR
    capacity like '%".$searchValue."%' OR 
    division like '%".$searchValue."%'
  )";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from capacity WHERE deleted = '0'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM capacity WHERE deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from capacity WHERE deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array( 
      "counter"=>$counter,
      "id"=>$row['id'],
      "name"=>$row['name'],
      "range_type"=>$row['range_type'],
      "capacity"=>$row['capacity'],
      "units"=>searchUnitNameById($row['units'], $db),
      "division"=>$row['division'] ?? '',
      "division_unit"=>$row['division_unit'] != null ? searchUnitNameById($row['division_unit'], $db) : '',
      "capacity2"=>$row['capacity2'],
      "units2"=>$row['units2'] != null ? searchUnitNameById($row['units2'], $db) : '',
      "division2"=>$row['division2'] ?? '',
      "division_unit2"=>$row['division_unit2'] != null ? searchUnitNameById($row['division_unit2'], $db) : '',
    );

    $counter++;
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
  'empRecords' => $empQuery
);

echo json_encode($response);

?>