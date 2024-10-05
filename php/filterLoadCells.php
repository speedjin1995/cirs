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

// if($_POST['machineType'] != null && $_POST['machineType'] != '' && $_POST['machineType'] != '-'){
// 	$searchQuery .= " and load_cells.machine_type = '".$_POST['machineType']."'";
// }

// if($_POST['brand'] != null && $_POST['brand'] != '' && $_POST['brand'] != '-'){
// 	$searchQuery .= " and load_cells.brand = '".$_POST['brand']."'";
// }

// if($_POST['model'] != null && $_POST['model'] != '' && $_POST['model'] != '-'){
// 	$searchQuery .= " and load_cells.model = '".$_POST['model']."'";
// }

// if($_POST['jenisAlat'] != null && $_POST['jenisAlat'] != '' && $_POST['jenisAlat'] != '-'){
// 	$searchQuery .= " and load_cells.jenis_alat = '".$_POST['jenisAlat']."'";
// }

// if($_POST['madeIn'] != null && $_POST['madeIn'] != '' && $_POST['madeIn'] != '-'){
// 	$searchQuery .= " and load_cells.made_in = '".$_POST['madeIn']."'";
// }

// if($_POST['patternNo'] != null && $_POST['patternNo'] != '' && $_POST['patternNo'] != '-'){
// 	$searchQuery .= " and load_cells.pattern_no like '%".$_POST['patternNo']."%'";
// }

if($searchValue != ''){
  $searchQuery .= " AND (load_cells.part_no like '%".$searchValue."%'
   OR load_cells.load_cell like '%".$searchValue."%')";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM load_cells, machines, brand, model, alat, country 
WHERE load_cells.machine_type = machines.id AND load_cells.brand = brand.id AND load_cells.model = model.id 
AND load_cells.jenis_alat = alat.id AND load_cells.made_in = country.id AND load_cells.deleted = '0'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM load_cells, machines, brand, model, alat, country 
WHERE load_cells.machine_type = machines.id AND load_cells.brand = brand.id AND load_cells.model = model.id 
AND load_cells.jenis_alat = alat.id AND load_cells.made_in = country.id AND load_cells.deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT load_cells.*, brand.brand AS brand_name, model.model AS model_name, country.nicename 
FROM load_cells, brand, model, country WHERE load_cells.brand = brand.id AND load_cells.model = model.id 
AND load_cells.made_in = country.id AND load_cells.deleted = '0'".$searchQuery." 
order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;; 
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "part_no"=>$row['part_no'],
    "load_cell"=>$row['load_cell'] ?? '',
    "brand_name"=>$row['brand_name'] ?? '',
    "model_name"=>$row['model_name'] ?? '',
    "capacity"=> $row['capacity'] ?? '',
    "made_in"=>$row['made_in'] ?? '',
    "class"=>$row['class'] ?? '',
    "pattern_no"=>$row['pattern_no'] ?? '',
    "pattern_datetime"=>$row['pattern_datetime'] ?? '',
    "pattern_expiry"=>$row['pattern_expiry'] ?? '',
    "certificate"=>'<div class="col-2">
                        <a href="' . $row['certificate'] . '" target="_blank" class="btn btn-success btn-sm" role="button">
                            <i class="fa fa-file-pdf-o"></i>
                        </a>
                    </div>'
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