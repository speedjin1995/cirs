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
  $searchQuery = " and stamping_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and stamping_date <= '".$toDateTime."'";
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and customers = '".$_POST['customer']."'";
}

if($_POST['validator'] != null && $_POST['validator'] != '' && $_POST['validator'] != '-'){
	$searchQuery .= " and validate_by = '".$_POST['validator']."'";
}

if($_POST['cawangan'] != null && $_POST['cawangan'] != '' && $_POST['cawangan'] != '-'){
	$searchQuery .= " and cawangan = '".$_POST['cawangan']."'";
}

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
  if($_POST['status'] == '6'){
    $searchQuery .= " and stamping_type = 'NEW'";
  }
	else if($_POST['status'] == '7'){
    $searchQuery .= " and stamping_type = 'RENEWAL'";
  }
}

$searchQuery .= " and status = 'Complete'";

if($searchValue != ''){
  $searchQuery = " and (purchase_no like '%".$searchValue."%' OR
  quotation_no like '%".$searchValue."%' OR
  invoice_no like '%".$searchValue."%' OR
  cash_bill like '%".$searchValue."%')";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM stamping WHERE deleted = '0'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM stamping WHERE deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT * FROM stamping WHERE deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage; 
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
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

  $stampingDate = new DateTime($row['stamping_date']);
  $formattedStampingDate = $stampingDate->format('d-m-Y');

  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "customers"=>$row['customers'] != null ? searchCustNameById($row['customers'], $db) : '',
    "address1" => $address1 ?? '',
    "address2" => $address2 ?? '',
    "address3" => $address3 ?? '',
    "address4"=>$address4 ?? '',
    "picontact"=> $pic ?? '',
    "pic_phone"=> $pic_phone ?? '',
    "full_address"=>$address1.' '.$address2.' '.$address3.' '.$address4,
    "full_address2"=>$address1.'<br>'.$address2.'<br>'.$address3.'<br>'.$address4,
    "brand"=>$row['brand'] != null ? searchBrandNameById($row['brand'], $db) : '',
    "machine_type"=>$row['machine_type'] != null ? searchMachineNameById($row['machine_type'], $db) : '',
    "model"=>$row['model'] != null  ? searchModelNameById($row['model'], $db) : '',
    "capacity"=>$row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '',
    "serial_no"=>$row['serial_no'] ?? '',
    "validator"=>$row['validate_by'] != null ? searchValidatorNameById($row['validate_by'], $db) : '',
    "jenis_alat"=>$row['jenis_alat'] != null ? searchAlatNameById($row['jenis_alat'], $db) : '', 
    "cash_bill"=>$row['cash_bill'] ?? '',
    "invoice_no"=>$row['invoice_no'] ?? '',
    "stamping_date"=>$formattedStampingDate ?? '',
    "due_date"=>$row['due_date'] ?? '',
    "pic"=>$row['pic'] != null ? searchStaffNameById($row['pic'], $db) : '',
    "customer_pic"=>$row['customer_pic'] ?? '',
    "quotation_date"=>$row['quotation_date'] ?? '',
    "quotation_no"=>$row['quotation_no'] ?? '',
    "purchase_no"=>$row['purchase_no'] ?? '',
    "purchase_date"=>$row['purchase_date'] ?? '',
    "unit_price"=>$row['unit_price'] ?? '',
    "cert_price"=>$row['cert_price'] ?? '',
    "total_amount"=>$row['total_amount'] ?? '',
    "sst"=>$row['sst'] ?? '',
    "subtotal_amount"=>$row['subtotal_amount'] ?? '',
    "log"=> json_decode($row['log'], true),
    "status"=>$row['status'],
    "remarks"=>$row['remarks'] ?? '',
    "no_daftar"=>$row['no_daftar'] ?? '',
    "pin_keselamatan"=>$row['pin_keselamatan'] ?? '',
    "siri_keselamatan"=>$row['siri_keselamatan'] ?? '',
    "borang_d"=>$row['borang_d'] ?? '',
    "borang_e"=>$row['borang_e'] ?? '',
    "assignTo"=>$row['assignTo'] != null ? searchStaffNameById($row['assignTo'], $db) : '',
    "quantity"=>'1',
    "batch_no"=>'',
    "reason"=>'SERVICE / STMP'
  );

  $counter++;
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
  "query" => $empQuery
);

echo json_encode($response);

?>