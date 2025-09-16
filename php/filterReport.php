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
$type = 'Stamping';
if($_POST['type'] != null && $_POST['type'] != '' && $_POST['type'] != '-'){
  $type = $_POST['type'];
}

## Search 
$searchQuery = " ";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d 00:00:00');

  if ($type == 'Stamping'){
    $searchQuery = " and s.stamping_date >= '".$fromDateTime."'";
  } else if ($type == 'Other'){
    $searchQuery = " and o.last_calibration_date >= '".$fromDateTime."'";
  } else if ($type == 'Inhouse'){
    $searchQuery = " and a.validation_date >= '".$fromDateTime."'";
  }
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d 23:59:59');
  if ($type == 'Stamping'){
    $searchQuery .= " and s.stamping_date <= '".$toDateTime."'";
  } else if ($type == 'Other'){
    $searchQuery .= " and o.last_calibration_date <= '".$toDateTime."'";
  } else if ($type == 'Inhouse'){
    $searchQuery .= " and a.validation_date <= '".$toDateTime."'";
  }
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
  if ($type == 'Stamping'){
    $searchQuery .= " and s.customers = '".$_POST['customer']."'";
  } else if ($type == 'Other'){
    $searchQuery .= " and o.customer = '".$_POST['customer']."'";
  } else if ($type == 'Inhouse'){
    $searchQuery .= " and a.customer = '".$_POST['customer']."'";
  }
}

if($_POST['validator'] != null && $_POST['validator'] != '' && $_POST['validator'] != '-'){
  if ($type == 'Stamping'){
    $searchQuery .= " and s.validate_by = '".$_POST['validator']."'";
  } else if ($type == 'Other'){
    $searchQuery .= " and o.validate_by = '".$_POST['validator']."'";
  } else if ($type == 'Inhouse'){
    $searchQuery .= " and a.validate_by = '".$_POST['validator']."'";
  }
}

if($_POST['branch'] != null && $_POST['branch'] != '' && $_POST['branch'] != '-'){
  if ($type == 'Stamping'){
    $searchQuery .= " and s.company_branch = '".$_POST['branch']."'";
  } else if ($type == 'Other'){
    $searchQuery .= " and o.company_branch = '".$_POST['branch']."'";
  } else if ($type == 'Inhouse'){
    $searchQuery .= " and a.company_branch = '".$_POST['branch']."'";
  }
}

// if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
//   if($_POST['status'] == '6'){
//     $searchQuery .= " and s.stamping_type = 'NEW'";
//   }
// 	else if($_POST['status'] == '7'){
//     $searchQuery .= " and s.stamping_type = 'RENEWAL'";
//   }
// }

// # Order By column
// if ($columnName == 'customers'){
//   $columnName = " order by c.customer_name ".$columnSortOrder;
// }else if ($columnName == 'brand_model'){
//   $columnName = " order by m.model ".$columnSortOrder;
// }else if ($columnName == 'capacity'){
//   $columnName = " order by cap.name ".$columnSortOrder;
// }else if ($columnName == 'jenis_alat'){
//   $columnName = " order by a.alat ".$columnSortOrder;
// }else {
//   $columnName = " order by s.". $columnName.' '.$columnSortOrder;
// } 

if ($type == 'Stamping') {
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
                            WHERE s.deleted=0 ".$searchQuery);
  $records = mysqli_fetch_assoc($sel);
  $totalRecordwithFilter = $records['allcount'];

  ## Fetch records
  $empQuery = "SELECT s.* FROM stamping s 
                LEFT JOIN customers c ON s.customers = c.id 
                LEFT JOIN brand b ON s.brand = b.id 
                LEFT JOIN machines m ON s.machine_type = m.id 
                LEFT JOIN capacity cap ON s.capacity = cap.id
                LEFT JOIN validators v ON s.validate_by = v.id
                WHERE s.deleted=0 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
} elseif ($type == 'Other') {
  ## Total number of records without filtering
  $sel = mysqli_query($db,"select count(*) as allcount FROM other_validations");
  $records = mysqli_fetch_assoc($sel);
  $totalRecords = $records['allcount'];

  ## Total number of record with filtering
  $sel = mysqli_query($db,"select count(*) as allcount FROM other_validations o
                            LEFT JOIN customers c ON o.customer = c.id 
                            LEFT JOIN brand b ON o.brand = b.id 
                            LEFT JOIN machines m ON o.machines = m.id 
                            LEFT JOIN capacity cap ON o.capacity = cap.id 
                            LEFT JOIN validators v ON o.validate_by = v.id
                            WHERE o.deleted=0 ".$searchQuery);
  $records = mysqli_fetch_assoc($sel);
  $totalRecordwithFilter = $records['allcount'];

  ## Fetch records
  $empQuery = "SELECT o.* FROM other_validations o
                LEFT JOIN customers c ON o.customer = c.id 
                LEFT JOIN brand b ON o.brand = b.id 
                LEFT JOIN machines m ON o.machines = m.id 
                LEFT JOIN capacity cap ON o.capacity = cap.id
                LEFT JOIN validators v ON o.validate_by = v.id
                WHERE o.deleted=0 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
} else if ($type == 'Inhouse') {
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
                        WHERE a.deleted=0 ".$searchQuery);

  $records = mysqli_fetch_assoc($sel);
  $totalRecordwithFilter = $records['allcount'];

  ## Fetch records
  $empQuery = "SELECT a.*, b.standard_avg_temp, b.relative_humidity ,b.unit FROM inhouse_validations a 
                    LEFT JOIN standard b ON a.capacity = b.capacity AND b.deleted = 0
                    LEFT JOIN customers c ON a.customer = c.id AND c.deleted = 0
                    LEFT JOIN brand ON a.brand = brand.id AND brand.deleted = 0
                    LEFT JOIN machines m ON a.machines = m.id AND m.deleted = 0
                    LEFT JOIN capacity cap ON a.capacity = cap.id AND cap.deleted = 0
                    LEFT JOIN users u ON a.calibrator = u.id AND u.deleted = 0
                    WHERE a.deleted=0 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage; 
}

// var_dump($empQuery);
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;
while($row = mysqli_fetch_assoc($empRecords)) {
  $rowData = array(
    "no"=>$counter,
    "brand"=>$row['brand'] != null ? searchBrandNameById($row['brand'], $db) : '',
    "validate_by"=>$row['validate_by'] != null ? searchValidatorNameById($row['validate_by'], $db) : '',
    "capacity"=> $row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '',
    "status"=>$row['status'],
  );
  
  if ($type == 'Stamping') {
    $stampingDate = new DateTime($row['stamping_date']);
    $formattedStampingDate = $stampingDate->format('d-m-Y');
    $rowData = array_merge($rowData, array(
      "customers" => $row['customers'] != null ? searchCustNameById($row['customers'], $db) : '',
      "machine_type" => $row['machine_type'] != null ? searchMachineNameById($row['machine_type'], $db) : '',
      "serial_no" => $row['serial_no'] ?? '',
      "jenis_alat" => $row['jenis_alat'] != null ? searchAlatNameById($row['jenis_alat'], $db) : '',
      "stamping_date" => $formattedStampingDate ?? '',
      "due_date" => $row['due_date'] ?? '',
      "no_daftar_lama" => $row['no_daftar_lama'] ?? '',
      "no_daftar_baru" => $row['no_daftar_baru'] ?? '',
    ));
  } else if ($type == 'Other') {
    $rowData = array_merge($rowData, array(
      "customer"=>$row['customer'] != null ? searchCustNameById($row['customer'], $db) : '',
      "machines"=>$row['machines'] != null ? searchMachineNameById($row['machines'], $db) : '',
      "auto_form_no"=>$row['auto_form_no'] ?? '',
      "last_calibration_date"=>$row['last_calibration_date'] != null ? convertDatetimeToDate($row['last_calibration_date']) : '',
      "expired_calibration_date"=>$row['expired_calibration_date'] != null ? convertDatetimeToDate($row['expired_calibration_date']) : '',
    )); 
  } else if ($type == 'Inhouse') {
    $rowData = array_merge($rowData, array(
      "customer"=>$row['customer'] != null ? searchCustNameById($row['customer'], $db) : '',
      "machines"=>$row['machines'] != null ? searchMachineNameById($row['machines'], $db) : '',
      "auto_cert_no"=>$row['auto_cert_no'] ?? '',
      "validation_date"=>$row['validation_date'] != null ? convertDatetimeToDate($row['validation_date']) : '',
      "expired_date"=>$row['expired_date'] != null ? convertDatetimeToDate($row['expired_date']) : '',
      "calibrator"=>$row['size'] != null ? searchStaffNameById($row['calibrator'], $db) : '',
    ));
  }
  $data[] = $rowData;
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