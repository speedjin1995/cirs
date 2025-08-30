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

            $capacity = $row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '';

            $data[] = array( 
                "no"=>$counter,
                "id"=>$row['id'],
                "customers"=>$row['customers'] != null ? searchCustNameById($row['customers'], $db) : '',
                "full_address2"=>$address1.'<br>'.$address2.'<br>'.$address3.'<br>'.$address4,
                "brand"=>$row['brand'] != null ? searchBrandNameById($row['brand'], $db) : '',
                "model"=>$row['model'] != null  ? searchModelNameById($row['model'], $db) : '',
                "jenis_alat"=>$row['jenis_alat'] != null ? searchAlatNameById($row['jenis_alat'], $db) : '', 
                "machine_type"=>$row['machine_type'] != null ? searchMachineNameById($row['machine_type'], $db) : '',
                "serial_no"=>$row['serial_no'] ?? '',
                "validate_by"=>$row['validate_by'] != null ? searchValidatorNameById($row['validate_by'], $db) : '',
                "capacity"=>$capacity,
                "quantity"=>'1',
                "no_daftar_lama"=>$row['no_daftar_lama'] ?? '',
                "no_daftar_baru"=>$row['no_daftar_baru'] ?? '',
                "pin_keselamatan"=>$row['pin_keselamatan'] ?? '',
                "siri_keselamatan"=>$row['siri_keselamatan'] ?? '',
                "unit_price"=>$row['unit_price'] ?? '',
                "cert_price"=>$row['cert_price'] ?? '',     
                "borang_d"=>$row['borang_d'] ?? '',       
                "borang_e"=>$row['borang_e'] ?? '',  
                "borang_e_date"=>$row['borang_e_date'] != null ? convertDatetimeToDate($row['borang_e_date']) : '',
                "stamping_date"=>$row['stamping_date'] != null ? convertDatetimeToDate($row['stamping_date']) : '',
                "due_date"=>$row['due_date'] != null ? convertDatetimeToDate($row['due_date']) : '',
                "batch_no"=>'',
                "reason"=>'SERVICE / STMP'
            );
        
            $counter++;
        }

        ## Response
        $response = array(
            "status"=> "success",
            "aaData" => $data
        );

        $select_stmt->close(); // Close the prepared statement
        $db->close();
        echo json_encode($response);

    }else{
        $select_stmt->close(); // Close the prepared statement
        $db->close();
        
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