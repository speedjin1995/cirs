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
  $searchQuery .= " and 
  (a.alat like '%".$searchValue."%' OR
    m.machine_type like '%".$searchValue."%'
  )";
}

# Order by column
if ($columnName == 'jenis_alat'){
  $columnName = "a.alat";
}else {
  $columnName = "m.". $columnName;
} 

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from machines WHERE deleted = '0'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from machines m JOIN alat a ON m.jenis_alat = a.id WHERE m.deleted = '0' and a.deleted = '0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select m.* from machines m JOIN alat a ON m.jenis_alat = a.id WHERE m.deleted = '0' and a.deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;

while($row = mysqli_fetch_assoc($empRecords)) {
  $jenis_alat = '-';

  if($row['jenis_alat'] != null && $row['jenis_alat'] != ''){
    if ($update_stmt2 = $db->prepare("SELECT * FROM alat WHERE id=?")) {
      $update_stmt2->bind_param('s', $row['jenis_alat']);
  
      if($update_stmt2->execute()) {
        $result2 = $update_stmt2->get_result();
  
        if($row2 = $result2->fetch_assoc()) {
          $jenis_alat = $row2['alat'];
        }
      }

      $update_stmt2->close();
    }
  }
  

  $data[] = array( 
    "counter"=>$counter,
    "id"=>$row['id'],
    "machine_type"=>$row['machine_type'],
    "jenis_alat"=>$jenis_alat
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