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

// if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
// 	$searchQuery .= " and customer = '".$_POST['customer']."'";
// }

// if($_POST['validator'] != null && $_POST['validator'] != '' && $_POST['validator'] != '-'){
// 	$searchQuery .= " and validate_by = '".$_POST['validator']."'";
// }


// if($searchValue != ''){
//   $searchQuery = " and (purchase_no like '%".$searchValue."%' OR
//   quotation_no like '%".$searchValue."%' OR
//   invoice_no like '%".$searchValue."%' OR
//   cash_bill like '%".$searchValue."%')";
// }

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM inhouse_validations");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM inhouse_validations WHERE status = 'Pending'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$validationQuery = "SELECT * FROM inhouse_validations WHERE status = 'Pending'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

$validationRecords = mysqli_query($db, $validationQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($validationRecords)) {
  $branch = $row['branch'];
  $branchQuery = "SELECT * FROM branches WHERE id = $branch";
  $branchDetail = mysqli_query($db, $branchQuery);
  $branchRow = mysqli_fetch_assoc($branchDetail);

  $address1 = null;
  $address2 = null;
  $address3 = null;
  $address4 = null;
  $pic = null;
  $pic_phone = null;

  if(!empty($branchRow)){
    $address1 = $branchRow['address'];
    $address2 = $branchRow['address2'];
    $address3 = $branchRow['address3'];
    $address4 = $branchRow['address4'];
    $pic = $branchRow['pic'];
    $pic_phone = $branchRow['pic_contact'];
  }

  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "validate_by"=>$row['validate_by'] != null ? searchValidatorNameById($row['validate_by'], $db) : '',
    "customer_code"=>$row['customer'] != null ? searchCustCodeById($row['customer'], $db) : '',
    "customer"=>$row['customer'] != null ? searchCustNameById($row['customer'], $db) : '',
    "address1"=>$address1,
    "address2"=>$address2,
    "address3"=>$address3,
    "address4"=>$address4,
    "pic"=>$pic,
    "pic_phone"=>$pic_phone,
    "auto_form_no"=>$row['auto_form_no'] ?? '',
    "machines"=>$row['machines'] != null ? searchMachineNameById($row['machines'], $db) : '',
    "unit_serial_no"=>$row['unit_serial_no'] ?? '',
    "manufacturing"=>$row['manufacturing'] ?? '',
    "brand"=>$row['brand'] != null ? searchBrandNameById($row['brand'], $db) : '',
    "model"=>$row['model'] != null  ? searchModelNameById($row['model'], $db) : '',
    "capacity"=>$row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '',
    "size"=>$row['size'] != null ? searchSizeNameById($row['size'], $db) : '',
    "calibrator"=>$row['size'] != null ? searchStaffNameById($row['calibrator'], $db) : '',
    "lastCalibrationDate"=>$row['last_calibration_date'] ?? '',
    "expiredDate"=>$row['expired_date'] ?? '',
    "autoCertNo"=>$row['auto_cert_no'] ?? '',
    "validation_date"=>$row['validation_date'] ?? '',
    "status"=>$row['status'] ?? '',
    "tests"=>json_decode($row['tests'], true) ?? '',
    "updated_datetime"=>$row['update_datetime'] ?? ''
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