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
$searchQuery = "";
/*if($searchValue != ''){
  $searchQuery = " and (inquiry.case_no like '%".$searchValue."%' or inquiry.vehicleNo like '%".$searchValue."%' )";
}*/

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM stamping, machines, brand, model, capacity, customers, validators WHERE stamping.descriptions = machines.id AND stamping.brand = brand.id AND stamping.model=model.id AND stamping.capacity=capacity.id AND stamping.customers=customers.id AND stamping.validate_by=validators.id");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM stamping, machines, brand, model, capacity, customers, validators WHERE stamping.descriptions = machines.id AND stamping.brand = brand.id AND stamping.model=model.id AND stamping.capacity=capacity.id AND stamping.customers=customers.id AND stamping.validate_by=validators.id ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT stamping.*, machines.machine_type, brand.brand, model.model, capacity.capacity, customers.customer_name, validators.validator FROM stamping, machines, brand, model, capacity, customers, validators WHERE stamping.descriptions = machines.id AND stamping.brand = brand.id AND stamping.model=model.id AND stamping.capacity=capacity.id AND stamping.customers=customers.id AND stamping.validate_by=validators.id ".$searchQuery;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $remarks = json_decode($row['remarks'], true);

  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "customer_name"=>$row['customer_name'],
    "brand"=>$row['brand'],
    "machine_type"=>$row['machine_type'],
    "model"=>$row['model'],
    "capacity"=>$row['capacity'],
    "serial_no"=>$row['serial_no'],
    "validator"=>$row['validator'],
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

echo json_encode($response);

?>