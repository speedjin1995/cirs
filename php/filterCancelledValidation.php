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

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
  $searchQuery = " and validation_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and validation_date <= '".$toDateTime."'";
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and customer = '".$_POST['customer']."'";
}

if($_POST['validator'] != null && $_POST['validator'] != '' && $_POST['validator'] != '-'){
	$searchQuery .= " and validate_by = '".$_POST['validator']."'";
}

if($_POST['autoFormNo'] != null && $_POST['autoFormNo'] != '' && $_POST['autoFormNo'] != '-'){
	$searchQuery .= " and auto_form_no = '".$_POST['autoFormNo']."'";
}

// if($searchValue != ''){
//   $searchQuery = " and (purchase_no like '%".$searchValue."%' OR
//   quotation_no like '%".$searchValue."%' OR
//   invoice_no like '%".$searchValue."%' OR
//   cash_bill like '%".$searchValue."%')";
// }

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM other_validations");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM other_validations WHERE status = 'Cancelled'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT * FROM other_validations WHERE status = 'Cancelled'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $branch = $row['branch'];
  // $branchQuery = "SELECT * FROM branches WHERE id = $branch";
  // $branchDetail = mysqli_query($db, $branchQuery);
  // $branchRow = mysqli_fetch_assoc($branchDetail);

  // $address1 = null;
  // $address2 = null;
  // $address3 = null;
  // $address4 = null;
  // $pic = null;
  // $pic_phone = null;

  // if(!empty($branchRow)){
  //   $address1 = $branchRow['address'];
  //   $address2 = $branchRow['address2'];
  //   $address3 = $branchRow['address3'];
  //   $address4 = $branchRow['address4'];
  //   $pic = $branchRow['pic'];
  //   $pic_phone = $branchRow['pic_contact'];
  // }

  $createdDate = DateTime::createFromFormat('Y-m-d H:i:s', $row['created_datetime']);
  $updatedDate = DateTime::createFromFormat('Y-m-d H:i:s', $row['update_datetime']);

  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "validation_date"=>$row['validation_date'] ?? '',
    "customer"=>$row['customer'] != null ? searchCustNameById($row['customer'], $db) : '',
    "brand"=>$row['brand'] != null ? searchBrandNameById($row['brand'], $db) : '',
    "machines"=>$row['machines'] != null ? searchMachineNameById($row['machines'], $db) : '',
    "validate_by"=>$row['validate_by'] != null ? searchValidatorNameById($row['validate_by'], $db) : '',
    "capacity"=>$row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '',
    "auto_form_no"=>$row['auto_form_no'] ?? '',
    "last_calibration_date"=>$row['last_calibration_date'] ?? '',
    "expired_calibration_date"=>$row['expired_calibration_date'] ?? '',
    // "address1"=>$address1,
    // "address2"=>$address2,
    // "address3"=>$address3,
    // "address4"=>$address4,
    // "pic"=>$pic,
    // "pic_phone"=>$pic_phone,
    // "unit_serial_no"=>$row['unit_serial_no'] ?? '',
    // "manufacturing"=>$row['manufacturing'] ?? '',
    // "model"=>$row['model'] != null  ? searchModelNameById($row['model'], $db) : '',
    // "size"=>$row['size'] != null ? searchSizeNameById($row['size'], $db) : '',
    // "calibrations"=>json_decode($row['calibrations'], true) ?? '',
    "status"=>$row['status'] ?? '',
    // "created_datetime"=> $createdDate->format('Y-m-d') ?? '',
    // "updated_datetime"=> $updatedDate->format('Y-m-d') ?? ''
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