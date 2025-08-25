<?php
## Database configuration
require_once 'db_connect.php';
require_once 'requires/lookup.php';

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['name']; // Column name
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

if($_POST['validator'] != null && $_POST['validator'] != '' && $_POST['validator'] != '-'){
	$searchQuery .= " and s.validate_by = '".$_POST['validator']."'";
}

if($_POST['cawangan'] != null && $_POST['cawangan'] != '' && $_POST['cawangan'] != '-'){
	$searchQuery .= " and s.cawangan = '".$_POST['cawangan']."'";
}

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
  if($_POST['status'] == '6'){
    $searchQuery .= " and s.stamping_type = 'NEW'";
  }
	else if($_POST['status'] == '7'){
    $searchQuery .= " and s.stamping_type = 'RENEWAL'";
  }
}

$searchQuery .= " and s.status = 'Complete'";

if($searchValue != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y', $searchValue);

  if ($dateTime){
    $formattedDate = $dateTime->format('d/m/Y');
    $searchQuery .= " and (
      c.customer_name like '%".$searchValue."%' OR 
      a.alat like '%".$searchValue."%' OR 
      m.model like '%".$searchValue."%' OR 
      cap.name like '%".$searchValue."%' OR
      s.no_daftar_lama like '%".$searchValue."%' OR
      s.no_daftar_baru like '%".$searchValue."%' OR
      s.borang_e like '%".$searchValue."%' OR
      s.pin_keselamatan like '%".$searchValue."%' OR
      s.siri_keselamatan like '%".$searchValue."%' OR
      s.serial_no like '%".$searchValue."%' OR
      s.borang_d like '%".$searchValue."%' OR
      s.borang_e like '%".$searchValue."%' OR
      DATE_FORMAT(s.stamping_date, '%d/%m/%Y') like '%".$formattedDate."%'
    )";
  }else{
    $searchQuery .= " and 
    (c.customer_name like '%".$searchValue."%' OR 
      a.alat like '%".$searchValue."%' OR 
      m.model like '%".$searchValue."%' OR 
      cap.name like '%".$searchValue."%' OR
      s.no_daftar_lama like '%".$searchValue."%' OR
      s.no_daftar_baru like '%".$searchValue."%' OR
      s.borang_e like '%".$searchValue."%' OR
      s.pin_keselamatan like '%".$searchValue."%' OR
      s.siri_keselamatan like '%".$searchValue."%' OR
      s.serial_no like '%".$searchValue."%' OR
      s.borang_d like '%".$searchValue."%' OR
      s.borang_e like '%".$searchValue."%'
    )";
  }

}

# Order By column
if ($columnName == 'customers'){
  $columnName = " order by c.customer_name ".$columnSortOrder;
}else if ($columnName == 'brand_model'){
  $columnName = " order by m.model ".$columnSortOrder;
}else if ($columnName == 'capacity'){
  $columnName = " order by cap.name ".$columnSortOrder;
}else if ($columnName == 'jenis_alat'){
  $columnName = " order by a.alat ".$columnSortOrder;
}else {
  $columnName = " order by s.". $columnName.' '.$columnSortOrder;
} 

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM stamping WHERE deleted = '0'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount FROM stamping s 
                          LEFT JOIN alat a ON s.jenis_alat = a.id 
                          LEFT JOIN customers c ON s.customers = c.id 
                          LEFT JOIN model m ON s.model = m.id 
                          LEFT JOIN capacity cap ON s.capacity = cap.id 
                          WHERE s.deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT s.* FROM stamping s 
              LEFT JOIN alat a ON s.jenis_alat = a.id 
              LEFT JOIN customers c ON s.customers = c.id 
              LEFT JOIN model m ON s.model = m.id 
              LEFT JOIN capacity cap ON s.capacity = cap.id
              WHERE s.deleted = '0'".$searchQuery.$columnName." limit ".$row.",".$rowperpage;
// var_dump($empQuery);
// $empQuery = "SELECT s.* FROM stamping s WHERE deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage; 
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

  $stampingDate = new DateTime($row['stamping_date']);
  $formattedStampingDate = $stampingDate->format('d-m-Y');

  if ($row['borang_e_date'] == null || $row['borang_e_date'] == '0000-00-00 00:00:00'){
    $formattedBorangEDate = '';
  } else {
    $borangEDate = new DateTime($row['borang_e_date']);
    $formattedBorangEDate = $borangEDate->format('d-m-Y');
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
    "capacity"=>$capacity,
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
    "no_daftar_lama"=>$row['no_daftar_lama'] ?? '',
    "no_daftar_baru"=>$row['no_daftar_baru'] ?? '',
    "pin_keselamatan"=>$row['pin_keselamatan'] ?? '',
    "siri_keselamatan"=>$row['siri_keselamatan'] ?? '',
    "borang_d"=>$row['borang_d'] ?? '',
    "borang_e"=>$row['borang_e'] ?? '',
    "borang_e_date"=>$formattedBorangEDate,
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

$db->close();

?>