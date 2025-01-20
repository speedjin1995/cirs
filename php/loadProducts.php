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
  $searchQuery .= " and 
  (a.alat like '%".$searchValue."%' OR
    c.name like '%".$searchValue."%' OR
    v.validator like '%".$searchValue."%' OR
    p.price like '%".$searchValue."%'
  )";
}

$searchQuery .= " and a.deleted = 0 and c.deleted = 0 and v.deleted = 0";

# Order by column
if ($columnName == 'jenis_alat'){
  $columnName = "a.alat";
}else if ($columnName == 'capacity'){
  $columnName = "c.name";
}else if ($columnName == 'validator'){
  $columnName = "v.validator";
}else {
  $columnName = "p.". $columnName;
} 

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from products WHERE deleted = '0'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM products p
                    JOIN alat a ON p.jenis_alat = a.id
                    JOIN capacity c ON p.capacity = c.id 
                    JOIN validators v ON p.validator = v.id
                    WHERE p.deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select p.* from products p 
              JOIN alat a ON p.jenis_alat = a.id
              JOIN capacity c ON p.capacity = c.id 
              JOIN validators v ON p.validator = v.id
              WHERE p.deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array( 
      "counter"=>$counter,
      "id"=>$row['id'],
      //"name"=>$row['name'] ?? '',
      //"machine_type"=>$row['machine_type'] != null ? searchMachineNameById($row['machine_type'], $db) : '',
      "capacity"=>$row['capacity'] != null ? searchCapacityById($row['capacity'], $db) : '',
      "jenis_alat"=>$row['jenis_alat'] != null ? searchAlatNameById($row['jenis_alat'], $db) : '',
      "validator"=>$row['validator'] != null ? searchValidatorNameById($row['validator'], $db) : '',
      "type"=>$row['type'],
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