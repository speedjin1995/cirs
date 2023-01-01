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
$sel = mysqli_query($db,"select count(*) as allcount FROM inquiry, machines, brand, model, size, capacity, users WHERE inquiry.machine_type = machines.id AND inquiry.brand = brand.id AND inquiry.model=model.id AND inquiry.size=size.id AND inquiry.capacity=capacity.id AND inquiry.pic_attend=users.id");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM inquiry, machines, brand, model, size, capacity, users WHERE inquiry.machine_type = machines.id AND inquiry.brand = brand.id AND inquiry.model=model.id AND inquiry.size=size.id AND inquiry.capacity=capacity.id AND inquiry.pic_attend=users.id ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT inquiry.id, inquiry.calling_datetime, inquiry.case_no, inquiry.case_status, inquiry.issues, inquiry.company_name, inquiry.contact_no, inquiry.updated_datetime, users.name FROM inquiry, machines, brand, model, size, capacity, users WHERE inquiry.machine_type = machines.id AND inquiry.brand = brand.id AND inquiry.model=model.id AND inquiry.size=size.id AND inquiry.capacity=capacity.id AND inquiry.pic_attend=users.id ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $issues = join(",", json_decode($row['issues'], true));

  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "calling_datetime"=>$row['calling_datetime'],
    "case_no"=>$row['case_no'],
    "case_status"=>$row['case_status'],
    "issues"=>$issues,
    "company_name"=>$row['company_name'],
    "contact_no"=>$row['contact_no'],
    "updated_datetime"=>$row['updated_datetime'],
    "name"=>$row['name']
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