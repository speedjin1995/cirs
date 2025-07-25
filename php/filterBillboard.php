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
$searchQuery = " ";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $fromDate = new DateTime($_POST['fromDate']);
  $fromDateTime = date_format($fromDate,"Y-m-d H:i:s");
  $searchQuery = " and weight.inCDateTime >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $toDate = new DateTime($_POST['toDate']);
  $toDateTime = date_format($toDate,"Y-m-d H:i:s");
	$searchQuery .= " and weight.inCDateTime <= '".$toDateTime."'";
}

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
	$searchQuery .= " and weight.status = '".$_POST['status']."'";
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and weight.customer = '".$_POST['customer']."'";
}

if($_POST['vehicle'] != null && $_POST['vehicle'] != '' && $_POST['vehicle'] != '-'){
	$searchQuery .= " and weight.vehicleNo like '%".$_POST['vehicle']."%'";
}

if($_POST['invoice'] != null && $_POST['invoice'] != ''){
	$searchQuery .= " and weight.invoiceNo like '%".$_POST['invoice']."%'";
}

if($_POST['batch'] != null && $_POST['batch'] != ''){
	$searchQuery .= " and weight.batchNo like '%".$_POST['batch']."%'";
}

if($_POST['product'] != null && $_POST['product'] != '' && $_POST['product'] != '-'){
	$searchQuery .= " and weight.productName = '".$_POST['product']."'";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from weight, vehicles, packages, lots, customers, products, status, units WHERE weight.package = packages.id AND weight.productName = products.id AND status.id=weight.status AND units.id=weight.unitWeight AND weight.deleted = '0' AND weight.pStatus = 'Complete'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from weight, vehicles, packages, lots, customers, products, status, units WHERE weight.package = packages.id AND weight.productName = products.id AND status.id=weight.status AND units.id=weight.unitWeight AND weight.deleted = '0' AND weight.pStatus = 'Complete'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select weight.id, weight.serialNo, weight.vehicleNo, weight.lotNo, weight.batchNo, weight.invoiceNo, weight.deliveryNo, users.name,
weight.purchaseNo, weight.customer, products.product_name, packages.packages, weight.unitWeight, weight.tare, weight.totalWeight, weight.actualWeight, 
weight.supplyWeight, weight.varianceWeight, weight.currentWeight, units.units, weight.moq, weight.dateTime, weight.unitPrice, weight.totalPrice, weight.remark, 
weight.status as Status, status.status, weight.manual, weight.manualVehicle, weight.manualOutgoing, weight.reduceWeight, weight.outGDateTime, weight.inCDateTime, 
weight.pStatus, weight.variancePerc, weight.transporter from weight, packages, products, units, status, users 
WHERE weight.package = packages.id AND users.id = weight.created_by AND weight.pStatus = 'Complete' AND weight.productName = products.id AND status.id=weight.status AND 
units.id=weight.unitWeight AND weight.deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;
$sales = 0;
$salesWeight = 0;
$salesTare = 0;
$salesNett = 0;
$purchase = 0;
$purchaseWeight = 0;
$purchaseTare = 0;
$purchaseNett = 0;
$local = 0;
$localWeight = 0;
$localTare = 0;
$localNett = 0;

while($row = mysqli_fetch_assoc($empRecords)) {
  $manual = '';
  $customer = '';
  $customerP = '';
  $customerA = '';
    
  if($row['manual'] == '1'){
    $manual = "** This is manual weighing!";
  }

  if($row['Status'] != '1' && $row['Status'] != '2'){
		$customer = $row['customer'];
	}
	else{
    $cid = $row['customer'];

		if ($update_stmt = $db->prepare("SELECT * FROM customers WHERE id=?")) {
      $update_stmt->bind_param('s', $cid);
      
      // Execute the prepared query.
      if ($update_stmt->execute()) {
        $result = $update_stmt->get_result();
          
        if ($row2 = $result->fetch_assoc()) {
          $customer = $row2['customer_name'];
          $customerP = $row2['customer_phone'];
          $customerA = $row2['customer_address'];
        }
      }
      $update_stmt->close();
    }
	}

  if($row['outGDateTime'] == null || $row['outGDateTime'] == ''){
    $outGDateTime = '-';
  }
  else{
    $dateOut = new DateTime($row['outGDateTime']);
    $outGDateTime = date_format($dateOut,"d/m/Y H:i:s A");
  }

  if($row['inCDateTime'] == null || $row['inCDateTime'] == ''){
    $outGDateTime = '-';
  }
  else{
    $dateInt = new DateTime($row['inCDateTime']);
    $inCDateTime = date_format($dateInt,"d/m/Y H:i:s A");
  }

  if($row['Status'] == '1'){
    $sales++;
    $salesWeight += (float)$row['currentWeight'];
    $salesTare += (float)$row['tare'];
    $salesNett += (float)$row['actualWeight'];
  }
  else if($row['Status'] == '2'){
    $purchase++;
    $purchaseWeight += (float)$row['currentWeight'];
    $purchaseTare += (float)$row['tare'];
    $purchaseNett += (float)$row['actualWeight'];
  }
  else if($row['Status'] == '3'){
    $local++;
    $localWeight += (float)$row['currentWeight'];
    $localTare += (float)$row['tare'];
    $localNett += (float)$row['actualWeight'];
  }

  $supplyWeight = 0.00;
  if($row['supplyWeight'] != null && $row['supplyWeight'] != ''){
    $supplyWeight = (float)$row['supplyWeight'];
  }

  $reduceWeight = 0.00;
  if($row['reduceWeight'] != null && $row['reduceWeight'] != ''){
    $reduceWeight = (float)$row['reduceWeight'];
  }

  $variancePerc = 0.00;
  if($row['variancePerc'] != null && $row['variancePerc'] != ''){
    $variancePerc = (float)$row['variancePerc'];
  }

  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "serialNo"=>$row['serialNo'],
    "veh_number"=>$row['vehicleNo'],
    "lots_no"=>$row['lotNo'],
    "batchNo"=>$row['batchNo'],
    "invoiceNo"=>$row['invoiceNo'],
    "deliveryNo"=>$row['deliveryNo'],
    "purchaseNo"=>$row['purchaseNo'],
    "userName"=>$row['name'],
    "customer_name"=>$customer,
    "customer_phone"=>$customerP,
    "customer_address"=>$customerA,
    "product_name"=>$row['product_name'],
    "packages"=>$row['packages'],
    "unitWeight"=>$row['unitWeight'],
    "supplyWeight"=>number_format($supplyWeight, 2),
    "varianceWeight"=>number_format((float)$row['varianceWeight'], 2),
    "tare"=>number_format((float)$row['tare'], 2),
    "totalWeight"=>number_format((float)$row['totalWeight'], 2),
    "actualWeight"=>number_format((float)$row['actualWeight'], 2),
    "currentWeight"=>number_format((float)$row['currentWeight'], 2),
    "unit"=>$row['units'],
    "moq"=>$row['moq'],
    "dateTime"=>$row['dateTime'],
    "unitPrice"=>number_format((float)$row['unitPrice'], 2),
    "totalPrice"=>number_format((float)$row['totalPrice'], 2),
    "remark"=>$row['remark'],
    "status"=>$row['status'],
    "manual"=>$manual,
    "manualVehicle"=>$row['manualVehicle'],
    "manualOutgoing"=>$row['manualOutgoing'],
    "reduceWeight"=>number_format($reduceWeight, 2),
    "outGDateTime"=>$outGDateTime,
    "inCDateTime"=>$inCDateTime,
    "pStatus"=>$row['pStatus'],
    "variancePerc"=> number_format($variancePerc, 2),
    "transporter_name"=> $row['transporter']
  );

  $counter++;
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
  "salesTotal" => $sales,
  "salesWeight" => number_format($salesWeight, 2),
  "salesTare" => number_format($salesTare, 2),
  "salesNet" => number_format($salesNett, 2),
  "purchaseTotal" => $purchase,
  "purchaseWeight" => number_format($purchaseWeight, 2),
  "purchaseTare" => number_format($purchaseTare, 2),
  "purchaseNet" => number_format($purchaseNett, 2),
  "localTotal" => $local,
  "localWeight" => number_format($localWeight, 2),
  "localTare" => number_format($localTare, 2),
  "localNet" => number_format($localNett, 2)
);

echo json_encode($response);

$db->close();

?>