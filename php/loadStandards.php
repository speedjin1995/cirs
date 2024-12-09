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
    standard.standard_avg_temp like '%".$searchValue."%' OR
    standard.relative_humidity like '%".$searchValue."%' OR
    capacity.name like '%".$searchValue."%' OR
    units.units like '%".$searchValue."%')
  ";
}

# Order by column
if ($columnName == 'unit'){
  $columnName = "units.units";
}else if ($columnName == 'capacity'){
  $columnName = "capacity.name";
}else {
  $columnName = "standard.". $columnName;
} 

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from standard, capacity, units WHERE standard.capacity = capacity.id AND standard.unit = units.id AND standard.deleted = '0'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM standard, capacity, units WHERE standard.capacity = capacity.id AND standard.unit = units.id AND standard.deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select standard.*, capacity.name, units.units from standard, capacity, units WHERE standard.capacity = capacity.id AND standard.unit = units.id AND standard.deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage; 
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "id"=>$row['id'],
    "standard_avg_temp"=>$row['standard_avg_temp'],
    "relative_humidity"=>$row['relative_humidity'] ?? '',
    "name"=>$row['name'],
    "units"=>$row['units'],
    "variance"=>$row['variance'],
    "test_1"=>$row['test_1'],
    "test_2"=>$row['test_2'],
    "test_3"=>$row['test_3'],
    "test_4"=>$row['test_4'],
    "test_5"=>$row['test_5'],
    "test_6"=>$row['test_6'],
    "test_7"=>$row['test_7'],
    "test_8"=>$row['test_8'],
    "test_9"=>$row['test_9'],
    "test_10"=>$row['test_10']
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