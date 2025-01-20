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
  $searchQuery .= " AND (
    brand.brand like '%".$searchValue."%' OR
    model.model like '%".$searchValue."%' OR
    country.name like '%".$searchValue."%')
  ";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from model, brand, country WHERE model.deleted = '0' and model.brand=brand.id and model.make=country.id");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from model, brand, country WHERE model.deleted = '0' and model.brand=brand.id and model.make=country.id".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select model.*, brand.brand as brand_name, country.name as iso3 from model, brand, country WHERE model.deleted = '0' and model.brand=brand.id and model.make=country.id".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array( 
      "counter"=>$counter,
      "id"=>$row['id'],
      "brand_name"=>$row['brand_name'],
      "model"=>$row['model'],
      "iso3"=>$row['iso3']
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

echo json_encode($response);

?>