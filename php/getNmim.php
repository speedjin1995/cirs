<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['companyId'])){
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
	$id = filter_input(INPUT_POST, 'companyId', FILTER_SANITIZE_STRING);

    if ($sql = $db->prepare("SELECT nmim FROM companies WHERE id = ?")) {
        $sql->bind_param('s', $id);
        
        // Execute the prepared query.
        if (!$sql->execute()) {
            $sql->close();
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                )); 
        }
        else{
            $result = $sql->get_result();                 
            $data = array(); 
            
            if ($row = $result->fetch_assoc()) {
                $nmims = json_decode($row['nmim'], true); 
                if (is_array($nmims)) {
                    $totalRecords = count($nmims);  // Get total number of records
            
                    // Slice array to return only required data for the current page
                    $nmims = array_slice($nmims, $start, $length);
                    
                    foreach ($nmims as $nmim) {
                        $file_path = !empty($nmim['file_path']) ? $nmim['file_path'] : '';
                        
                        $data[] = array(
                            'nmimDetail' => $nmim['nmimDetail'],
                            'nmimApprNo' => $nmim['nmimApprNo'],
                            'nmimApprDt' => $nmim['nmimApprDt'],
                            'nmimExpDt' => $nmim['nmimExpDt'],
                            'id' => $nmim['id'],
                            'file_path' => $file_path,
                            'companyId' => $id
                        );
                    }
                }
            }
            
            $sql->close();

            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            
            echo json_encode($response);  
        }

        $db->close();
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>