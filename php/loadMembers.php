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
   $searchQuery = " and (users.name like '%".$searchValue."%' or 
        users.username like '%".$searchValue."%' or
        roles.role_name like'%".$searchValue."%' or
        company_branches.branch_name like'%".$searchValue."%' ) ";
}

$searchQuery .= " and users.role_code != 'SUPER_ADMIN'";

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from users");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"SELECT COUNT(*) AS allcount FROM users INNER JOIN roles ON users.role_code = roles.role_code LEFT JOIN company_branches ON users.branch = company_branches.id".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT users.*, roles.role_name, company_branches.branch_name FROM users INNER JOIN roles ON users.role_code = roles.role_code LEFT JOIN company_branches ON users.branch = company_branches.id".$searchQuery." order by deleted, ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
    $created_date = '-';
    
    if($row['created_date'] != null && $row['created_date'] != ""){
      $created_date = date("d-m-Y", strtotime($row['created_date']));
    }
    
    $data[] = array( 
      "id"=>$row['id'],
      "name"=>$row['name'],
      "username"=>$row['username'],
      "ic_number"=>$row['ic_number'],
      "designation"=>$row['designation'],
      "contact_number"=>$row['contact_number'],
      "role_name"=>$row['role_name'],
      "branch_name"=>$row['branch_name'],
      "created_date"=>$created_date,
      "deleted"=>$row['deleted'],
      "status"=>($row['deleted'] == '0') ? 'Active' : 'Inactive'
    );
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