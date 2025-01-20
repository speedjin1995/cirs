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
if($searchValue != ''){
  $searchQuery .= " and (
    customer_code like '%".$searchValue."%' or 
    other_code like '%".$searchValue."%' or 
    customer_name like '%".$searchValue."%' or 
    customer_phone like '%".$searchValue."%' or
    customer_email like '%".$searchValue."%' or
    customer_address like '%".$searchValue."%'
  )";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from customers");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
  $pricing = array();

  if ($update_stmt2 = $db->prepare("SELECT * FROM branches WHERE customer_id=? AND deleted = '0'")) {
    $update_stmt2->bind_param('s', $row['id']);

    if($update_stmt2->execute()) {
      $result2 = $update_stmt2->get_result();

      while($row2 = $result2->fetch_assoc()) {
        $pricing[] = array(
          "branchid" => $row2['id'],
          "branchcode" => $row2['branch_code'],
          "branchname" => $row2['branch_name'],
          "mapurl" => $row2['map_url'],
          "address1" => $row2['address'],
          "address2" => $row2['address2'],
          "address3" => $row2['address3'],
          "address4" => $row2['address4'],
        );
      }
    }
  }
  
  if(isset($row['map_url']) && !empty($row['map_url'])){
    $customerAddress = '<a href="'.$row['map_url'].'" target="_blank">' .$row['customer_address']." ". $row['address2'] ." ". $row['address3']. ' <i class="fa fa-map-marker"></i></a>';
  } else {
    $customerAddress = $row['customer_address']." ". $row['address2'] ." ". $row['address3'];
  }

  $data[] = array( 
    "id"=>$row['id'],
    "customer_code"=>$row['customer_code'],
    "other_code"=>$row['other_code'],
    "customer_name"=>$row['customer_name'],
    // "customer_address"=>$row['customer_address']." ". $row['address2'] ." ". $row['address3'],
    "customer_phone"=>$row['customer_phone'],
    "customer_email"=>$row['customer_email'],
    "customer_address"=>$customerAddress,
    "log"=> $pricing
  );
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