<?php
## Database configuration
require_once 'db_connect.php';

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
   $searchQuery = " AND capacity like '%".$searchValue."%'";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from products, machines, capacity, alat, validators WHERE products.machine_type = machines.id AND products.jenis_alat = alat.id AND products.capacity = capacity.id AND products.validator = validators.id AND products.deleted = '0'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM products, machines, capacity, alat, validators WHERE products.machine_type = machines.id AND products.jenis_alat = alat.id AND products.capacity = capacity.id AND products.validator = validators.id AND capacity.deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select products.*, machines.machine_type, capacity.name as capacity, alat.alat, validators.validator from products, machines, capacity, alat, validators WHERE products.machine_type = machines.id AND products.jenis_alat = alat.id AND products.capacity = capacity.id AND products.validator = validators.id AND capacity.deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array( 
      "counter"=>$counter,
      "id"=>$row['id'],
      "name"=>$row['name'],
      "machine_type"=>$row['machine_type'],
      "capacity"=>$row['capacity'],
      "alat"=>$row['alat'],
      "validator"=>$row['validator'],
      "price"=>$row['price']
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