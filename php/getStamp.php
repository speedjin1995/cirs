<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM stamping WHERE id=?")) {
        $update_stmt->bind_param('s', $id);
        
        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                )); 
        }
        else{
            $result = $update_stmt->get_result();
            $message = array();
            
            if ($row = $result->fetch_assoc()) {
                $message['id'] = $row['id'];
                $message['type'] = $row['type'];
                $message['dealer'] = $row['dealer'];
                $message['customer_type'] = $row['customer_type'];
                $message['customers'] = $row['customers'];
                $message['address1'] = $row['address1'];
                $message['address2'] = $row['address2'];
                $message['address3'] = $row['address3'];
                $message['address4'] = $row['address4'];
                $message['branch'] = $row['branch'];
                $message['products'] = $row['products'];
                $message['stampType'] = $row['stamping_type'];
                $message['brand'] = $row['brand'];
                $message['machine_type'] = $row['machine_type'];
                $message['model'] = $row['model'];
                $message['capacity'] = $row['capacity'];
                $message['serial_no'] = $row['serial_no'];
                $message['validate_by'] = $row['validate_by'];
                $message['jenis_alat'] = $row['jenis_alat'];
                $message['no_daftar'] = $row['no_daftar'];
                $message['pin_keselamatan'] = $row['pin_keselamatan'];
                $message['siri_keselamatan'] = $row['siri_keselamatan'];
                $message['include_cert'] = $row['include_cert'];
                $message['borang_d'] = $row['borang_d'];
                $message['invoice_no'] = $row['invoice_no'];
                $message['cash_bill'] = $row['cash_bill'];
                $message['stamping_date'] = $row['stamping_date'];
                $message['due_date'] = $row['due_date'];
                $message['pic'] = $row['pic'];
                $message['customer_pic'] = $row['customer_pic'];
                $message['quotation_no'] = $row['quotation_no'];
                $message['quotation_date'] = $row['quotation_date'];
                $message['purchase_no'] = $row['purchase_no'];
                $message['purchase_date'] = $row['purchase_date'];
                $message['remarks'] = $row['remarks'];
                $message['log'] = json_decode($row['log'], true);
                $message['unit_price'] = $row['unit_price'];
                $message['cert_price'] = $row['cert_price'];
                $message['total_amount'] = $row['total_amount'];
                $message['sst'] = $row['sst'];
                $message['subtotal_amount'] = $row['subtotal_amount'];
                $message['status'] = $row['status'];
                $message['existing_id'] = $row['existing_id'];

                if(($row['validate_by'] == '10' || $row['validate_by'] == '9') && $row['jenis_alat'] == '1'){
                    if ($update_stmt2 = $db->prepare("SELECT * FROM stamping_ext WHERE stamp_id=?")) {
                      $update_stmt2->bind_param('s', $row['id']);
                  
                      if($update_stmt2->execute()) {
                        $result2 = $update_stmt2->get_result();
                  
                        if($row2 = $result2->fetch_assoc()) {
                          $message['penentusan_baru'] = $row2['penentusan_baru'] ?? '';
                          $message['penentusan_semula'] = $row2['penentusan_semula'] ?? '';
                          $message['kelulusan_mspk'] = $row2['kelulusan_mspk'] ?? '';
                          $message['no_kelulusan'] = $row2['no_kelulusan'] ?? '';
                          $message['indicator_serial'] = $row2['indicator_serial'] ?? '';
                          $message['platform_country'] = $row2['platform_country'] ?? '';
                          $message['platform_type'] = $row2['platform_type'];
                          $message['size'] = $row2['size'] ?? '';
                          $message['jenis_pelantar'] = $row2['jenis_pelantar'] ?? '';
                          $message['other_info'] = $row2['other_info'] ?? '';
                          $message['load_cell_country'] = $row2['load_cell_country'] ?? '';
                          $message['load_cell_no'] = $row2['load_cell_no'] ?? '';
                          $message['load_cells_info'] = json_decode($row2['load_cells_info'], true);
                        }
                      }
                    }
                  }
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
        }
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