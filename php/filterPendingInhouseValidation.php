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
	$searchQuery .= " and auto_form_no LIKE '%".$_POST['autoFormNo']."%'";
}

if($searchValue != ''){
  $searchQuery .= " and 
  (c.customer_name like '%".$searchValue."%' OR 
    brand.brand like '%".$searchValue."%' OR
    m.machine_type like '%".$searchValue."%' OR 
    cap.name like '%".$searchValue."%' OR
    a.auto_cert_no like '%".$searchValue."%' OR
    u.name like '%".$searchValue."%'
  )";
}

// $searchQuery .= " and b.deleted = 0 and a.deleted = 0 AND c.deleted = 0 and brand.deleted = 0 and m.deleted = 0 and cap.deleted = 0 and u.deleted = 0";

# Order by column
if ($columnName == 'customer'){
  $columnName = "c.customer_name";
}else if ($columnName == 'brand'){
  $columnName = "brand.". $columnName;
}else if ($columnName == 'machines'){
  $columnName = "m.machine_type";
}else if ($columnName == 'calibrator'){
  $columnName = "u.name";
}else if ($columnName == 'capacity'){
  $columnName = "cap.name";
}else {
  $columnName = "a.". $columnName;
} 

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM inhouse_validations");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM inhouse_validations a
                          LEFT JOIN standard b ON a.capacity = b.capacity AND b.deleted = 0
                          LEFT JOIN customers c ON a.customer = c.id AND c.deleted = 0
                          LEFT JOIN brand ON a.brand = brand.id AND brand.deleted = 0
                          LEFT JOIN machines m ON a.machines = m.id AND m.deleted = 0
                          LEFT JOIN capacity cap ON a.capacity = cap.id AND cap.deleted = 0
                          LEFT JOIN users u ON a.calibrator = u.id AND u.deleted = 0
                          WHERE a.status = 'Pending'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$validationQuery = "SELECT a.*, b.standard_avg_temp, b.relative_humidity ,b.unit FROM inhouse_validations a 
                    LEFT JOIN standard b ON a.capacity = b.capacity AND b.deleted = 0
                    LEFT JOIN customers c ON a.customer = c.id AND c.deleted = 0
                    LEFT JOIN brand ON a.brand = brand.id AND brand.deleted = 0
                    LEFT JOIN machines m ON a.machines = m.id AND m.deleted = 0
                    LEFT JOIN capacity cap ON a.capacity = cap.id AND cap.deleted = 0
                    LEFT JOIN users u ON a.calibrator = u.id AND u.deleted = 0
                    WHERE status = 'Pending'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage; 

$validationRecords = mysqli_query($db, $validationQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($validationRecords)) {
  // $branch = $row['branch'];
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

  #added checking in the event where standard value table not setup
  if(empty($row['unit'])){
    $capacityId = $row['capacity'];
    $capacityQuery = "SELECT * FROM capacity WHERE id = $capacityId";
    $capacityDetail = mysqli_query($db, $capacityQuery);
    $capacityRow = mysqli_fetch_assoc($capacityDetail);

    $row['unit'] = $capacityRow['units'];
  }

  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "validation_date"=>$row['validation_date'] != null ? convertDatetimeToDate($row['validation_date']) : '',
    "customer"=>$row['customer'] != null ? searchCustNameById($row['customer'], $db) : '',
    "brand"=>$row['brand'] != null ? searchBrandNameById($row['brand'], $db) : '',
    "machines"=>$row['machines'] != null ? searchMachineNameById($row['machines'], $db) : '',
    "capacity"=>$row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '',
    "auto_cert_no"=>$row['auto_cert_no'] ?? '',
    "expired_date"=>$row['expired_date'] != null ? convertDatetimeToDate($row['expired_date']) : '',
    "expiredDate"=>$row['expired_date'] != null ? $row['expired_date'] : '',
    "calibrator"=>$row['size'] != null ? searchStaffNameById($row['calibrator'], $db) : '',
    "status"=>$row['status'] ?? '',
    "duplicate"=>$row['duplicate'] ?? 'N',
    // "validate_by"=>$row['validate_by'] != null ? searchValidatorNameById($row['validate_by'], $db) : '',
    // "customer_code"=>$row['customer'] != null ? searchCustCodeById($row['customer'], $db) : '',
    // "address1"=>$address1,
    // "address2"=>$address2,
    // "address3"=>$address3,
    // "address4"=>$address4,
    // "pic"=>$pic,
    // "pic_phone"=>$pic_phone,
    // "auto_form_no"=>$row['auto_form_no'] ?? '',
    // "unit_serial_no"=>$row['unit_serial_no'] ?? '',
    // "manufacturing"=>$row['manufacturing'] ?? '',
    // "model"=>$row['model'] != null  ? searchModelNameById($row['model'], $db) : '',
    // "size"=>$row['size'] != null ? searchSizeNameById($row['size'], $db) : '',
    // "lastCalibrationDate"=>$row['last_calibration_date'] ?? '',
    // "units"=> $row['unit'] != null ? searchUnitNameById($row['unit'], $db) : '',
    // "tests"=>json_decode($row['tests'], true) ?? '',
    // "updated_datetime"=>$row['update_datetime'] ?? '',
    // 'standard_avg_temp'=>$row['standard_avg_temp'],
    // 'relative_humidity'=>$row['relative_humidity'],
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

$db->close();

?>