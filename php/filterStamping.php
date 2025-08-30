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
  $searchQuery = " and s.stamping_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
	$searchQuery .= " and s.stamping_date <= '".$toDateTime."'";
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and s.customers = '".$_POST['customer']."'";
}

if($_POST['machineType'] != null && $_POST['machineType'] != '' && $_POST['machineType'] != '-'){
	$searchQuery .= " and s.machine_type = '".$_POST['machineType']."'";
}

if($_POST['validator'] != null && $_POST['validator'] != '' && $_POST['validator'] != '-'){
	$searchQuery .= " and s.validate_by = '".$_POST['validator']."'";
}

if($_POST['brand'] != null && $_POST['brand'] != '' && $_POST['brand'] != '-'){
	$searchQuery .= " and s.brand = '".$_POST['brand']."'";
} 

if($_POST['daftarLama'] != null && $_POST['daftarLama'] != '' && $_POST['daftarLama'] != '-'){
	$searchQuery .= " and s.no_daftar_lama like '%".$_POST['daftarLama']."%'";
}

if($_POST['daftarBaru'] != null && $_POST['daftarBaru'] != '' && $_POST['daftarBaru'] != '-'){
	$searchQuery .= " and s.no_daftar_baru like '%".$_POST['daftarBaru']."%'";
}

if($_POST['borang'] != null && $_POST['borang'] != '' && $_POST['borang'] != '-'){
  $searchQuery .= " and s.borang_d like '%".$_POST['borang']."%'";
}

if($_POST['serial'] != null && $_POST['serial'] != '' && $_POST['serial'] != '-'){
  $searchQuery .= " and s.serial_no like '%".$_POST['serial']."%'";
}

if($_POST['quotation'] != null && $_POST['quotation'] != '' && $_POST['quotation'] != '-'){
  $searchQuery .= " and s.quotation_no like '%".$_POST['quotation']."%'";
}

if($_POST['branch'] != null && $_POST['branch'] != '' && $_POST['branch'] != '-'){
	$searchQuery .= " and s.company_branch = '".$_POST['branch']."'";
}

if($searchValue != ''){
  $searchQuery .= " and 
  (c.customer_name like '%".$searchValue."%' OR 
    b.brand like '%".$searchValue."%' OR
    m.machine_type like '%".$searchValue."%' OR 
    cap.name like '%".$searchValue."%' OR
    v.validator like '%".$searchValue."%' OR
    s.serial_no like '%".$searchValue."%' OR
    s.borang_d like '%".$searchValue."%' OR
    s.no_daftar_lama like '%".$searchValue."%' OR
    s.no_daftar_baru like '%".$searchValue."%' OR
    s.borang_e like '%".$searchValue."%'
  )";
}

$searchQuery .= " and s.deleted = 0";

# Order by column
if ($columnName == 'customers'){
  $columnName = "c.customer_name";
}else if ($columnName == 'brand'){
  $columnName = "b.". $columnName;
}else if ($columnName == 'machines'){
  $columnName = "m.machine_type";
}else if ($columnName == 'validate_by'){
  $columnName = "v.validator";
}else if ($columnName == 'capacity'){
  $columnName = "cap.name";
}else {
  $columnName = "s.". $columnName;
} 

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM stamping");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM stamping s 
                          LEFT JOIN customers c ON s.customers = c.id 
                          LEFT JOIN brand b ON s.brand = b.id 
                          LEFT JOIN machines m ON s.machine_type = m.id 
                          LEFT JOIN capacity cap ON s.capacity = cap.id 
                          LEFT JOIN validators v ON s.validate_by = v.id
                          WHERE s.status = 'Complete'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT s.* FROM stamping s 
              LEFT JOIN customers c ON s.customers = c.id 
              LEFT JOIN brand b ON s.brand = b.id 
              LEFT JOIN machines m ON s.machine_type = m.id 
              LEFT JOIN capacity cap ON s.capacity = cap.id
              LEFT JOIN validators v ON s.validate_by = v.id
              WHERE s.status = 'Complete'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $branch = null;
  $address1 = null;
  $address2 = null;
  $address3 = null;
  $address4 = null;
  $pic = null;
  $pic_phone = null;

  if($row['branch'] != null && $row['branch'] != ''){
    $branch = $row['branch'];
    $branchQuery = "SELECT * FROM branches WHERE id = $branch";
    $branchDetail = mysqli_query($db, $branchQuery);
    $branchRow = mysqli_fetch_assoc($branchDetail);
    
    if(!empty($branchRow)){
      $address1 = $branchRow['address'];
      $address2 = $branchRow['address2'];
      $address3 = $branchRow['address3'];
      $address4 = $branchRow['address4'];
      $pic = $branchRow['pic'];
      $pic_phone = $branchRow['pic_contact'];
    }
  }

  // $capacity = '';
  // $count = 1;
  // if (searchAlatNameById($row['jenis_alat'], $db) == 'BTU - (BOX)'){
  //   $id = $row['id']; 
  //   $stampExtQuery = "SELECT * FROM stamping_ext WHERE stamp_id = $id";
  //   $stampDetail = mysqli_query($db, $stampExtQuery);
  //   $stampRow = mysqli_fetch_assoc($stampDetail);
    
  //   if(!empty($stampRow)){
  //     if (!empty($stampRow['btu_box_info'])){
  //       $btuBox = json_decode($stampRow['btu_box_info'], true);
  //       foreach ($btuBox as $btu) {
  //         $capacity .= $count.'.'.searchCapacityUnitById($btu['penandaanBatuUjian'], $db). '<br>';

  //         $count++;
  //       }
  //     }
  //   }
  // }else{
  //   $capacity = $row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '';
  // }

  $capacity = $row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '';


  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "customers"=>$row['customers'] != null ? searchCustNameById($row['customers'], $db) : '',
    "branch" => $branch,
    "address1" => $address1 ?? '',
    "address2" => $address2 ?? '',
    "address3" => $address3 ?? '',
    "address4"=>$address4 ?? '',
    "picontact"=> $pic ?? '',
    "pic_phone"=> $pic_phone ?? '',
    "brand"=>$row['brand'] != null ? searchBrandNameById($row['brand'], $db) : '',
    "machine_type"=>$row['machine_type'] != null ? searchMachineNameById($row['machine_type'], $db) : '',
    "model"=>$row['model'] != null  ? searchModelNameById($row['model'], $db) : '',
    "capacity"=>$capacity,
    "capacity_high"=>$row['capacity_high'] != null ? searchCapacityNameById($row['capacity_high'], $db) : '',
    "serial_no"=>$row['serial_no'] ?? '',
    "validate_by"=>$row['validate_by'] != null ? searchValidatorNameById($row['validate_by'], $db) : '',
    "jenis_alat"=>$row['jenis_alat'] != null ? searchAlatNameById($row['jenis_alat'], $db) : '', 
    "cash_bill"=>$row['cash_bill'] ?? '',
    "invoice_no"=>$row['invoice_no'] ?? '',
    "stamping_type"=>$row['stamping_type'] ?? '',
    "stamping_date"=>$row['stamping_date'] != null ? convertDatetimeToDate($row['stamping_date']) : '',
    "last_year_stamping_date"=>$row['last_year_stamping_date'] != null ? convertDatetimeToDate($row['last_year_stamping_date']) : '',
    "due_date"=>$row['due_date'] != null ? convertDatetimeToDate($row['due_date']) : '',
    "dueDate"=>$row['due_date'] != null ? $row['due_date'] : '',
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
    "no_daftar_lama"=>$row['no_daftar_lama'] ?? '',
    "no_daftar_baru"=>$row['no_daftar_baru'] ?? '',
    "pin_keselamatan"=>$row['pin_keselamatan'] ?? '',
    "siri_keselamatan"=>$row['siri_keselamatan'] ?? '',
    "borang_d"=>$row['borang_d'] ?? '',
    "created_datetime"=>$row['created_datetime'] != null ? convertDatetimeToDate($row['created_datetime']) : '',
    "updated_datetime"=>$row['updated_datetime'] != null ? convertDatetimeToDate($row['updated_datetime']) : ''
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