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
   $searchQuery .= " AND (`name` like '%".$searchValue."%')";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from `company_branches`");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from `company_branches` WHERE deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from company_branches WHERE deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "counter"=>$counter,
    "id"=>$row['id'],
    "branch_code"=>$row['branch_code'],
    "branch_name"=>$row['branch_name'],
    "map_url"=>$row['map_url'],
    "address" => '<a href="'.$row['map_url'].'" target="_blank">
                    '.$row['address_line_1'].' '.$row['address_line_2'].' '.$row['address_line_3'].' '.$row['address_line_4'].' '.$row['address_line_5'].'
                    <br><i class="fa-solid fa-location-dot"></i>
                  </a>',
    "pic"=>$row['pic'].'<br>'. 'Tel: ' . $row['pic_contact'],
    "email"=>$row['email'],
    "office_no"=>$row['office_no'],
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

$db->close(); // Close database connection
echo json_encode($response);

?>