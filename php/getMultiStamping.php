<?php
require_once "db_connect.php";
require_once 'requires/lookup.php';

session_start();

if(isset($_POST['selectedIds'])){
    $ids = implode(",", $_POST['selectedIds']);
    $select_stmt = $db->prepare("SELECT * FROM stamping A WHERE A.id IN ($ids)");

    // Check if the statement is prepared successfully
    if ($select_stmt) {
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        $message = '';
        $data = [];
        $counter = 1;

        while ($row = $result->fetch_assoc()) {
            $capacity = $row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '';

            $data[] = array( 
                "no"=>$counter,
                "id"=>$row['id'],
                "customers"=>$row['customers'] != null ? searchCustNameById($row['customers'], $db) : '',
                "brand"=>$row['brand'] != null ? searchBrandNameById($row['brand'], $db) : '',
                "model"=>$row['model'] != null  ? searchModelNameById($row['model'], $db) : '',
                "machine_type"=>$row['machine_type'] != null ? searchMachineNameById($row['machine_type'], $db) : '',
                "serial_no"=>$row['serial_no'] ?? '',
                "validate_by"=>$row['validate_by'] != null ? searchValidatorNameById($row['validate_by'], $db) : '',
                "capacity"=>$capacity,
                "quantity"=>'1',
                "no_daftar_lama"=>$row['no_daftar_lama'] ?? '',
                "no_daftar_baru"=>$row['no_daftar_baru'] ?? '',
                "siri_keselamatan"=>$row['siri_keselamatan'] ?? '',
                "stamping_date"=>$row['stamping_date'] != null ? convertDatetimeToDate($row['stamping_date']) : '',
                "due_date"=>$row['due_date'] != null ? convertDatetimeToDate($row['due_date']) : '',
            );
        
            $counter++;
        }

        ## Response
        $response = array(
            "status"=> "success",
            "aaData" => $data
        );

        echo json_encode($response);

    }else{
        echo json_encode(
            array(
                "status"=> "failed", 
                "message"=> "Failed to get the data"
            )
        ); 
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