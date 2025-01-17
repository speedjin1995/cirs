<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
}

if(isset($_POST['duplicateNo'], $_POST['id'])){
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $duplicateNo = filter_input(INPUT_POST, 'duplicateNo', FILTER_SANITIZE_STRING);
    $allSuccess = false;

    if ($select_stmt = $db->prepare("SELECT * FROM stamping WHERE id = ?")) {
        $select_stmt->bind_param('s', $id);

        if (!$select_stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Failed to get records"
                )); 
        }
        else{
            $result = $select_stmt->get_result();
            $message = array();
            
            if ($record = $result->fetch_assoc()) {
                // Prepare insert statement for stamping
                $insertQuery = "INSERT INTO stamping (
                    type, dealer, dealer_branch, customer_type, customers, branch, products, brand, machine_type, model, 
                    capacity, capacity_high, assignTo, serial_no, validate_by, jenis_alat, trade, no_daftar, no_daftar_lama, no_daftar_baru, 
                    pin_keselamatan, siri_keselamatan, include_cert, borang_d, borang_e, cawangan, invoice_no, cash_bill, stamping_type, last_year_stamping_date, stamping_date, 
                    due_date, pic, customer_pic, quotation_no, quotation_date, purchase_no, purchase_date, remarks, log, 
                    unit_price, cert_price, total_amount, sst, subtotal_amount, reason_id, other_reason, existing_id, status, 
                    renewed, duplicate
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $insertStmt = $db->prepare($insertQuery);

                // Prepare insert statement for stamping_ext
                $insertExtQuery = "INSERT INTO stamping_ext (
                    stamp_id, penentusan_baru, penentusan_semula, kelulusan_mspk, no_kelulusan, indicator_serial, 
                    platform_country, platform_type, size, jenis_pelantar, jenis_penunjuk, alat_type, questions, 
                    steelyard, bilangan_kaunterpois, nilais, bentuk_dulang, class, batu_ujian, batu_ujian_lain, other_info, 
                    load_cell_country, load_cell_no, load_cells_info
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $insertExtStmt = $db->prepare($insertExtQuery);

                for($i=0; $i<(int)$duplicateNo; $i++){
                    $insertStmt->execute([
                        $record['type'], $record['dealer'], $record['dealer_branch'], $record['customer_type'], $record['customers'],
                        $record['branch'], $record['products'], $record['brand'], $record['machine_type'], $record['model'],
                        $record['capacity'], $record['capacity_high'], $record['assignTo'], null, $record['validate_by'],
                        $record['jenis_alat'], $record['trade'], null, null, null, null, null, 
                        $record['include_cert'], null, null, $record['cawangan'], null, null, 'RENEWAL', null, 
                        null, null, $record['pic'], $record['customer_pic'], null, null, null, null, $record['remarks'], 
                        $record['log'], $record['unit_price'], $record['cert_price'], $record['total_amount'], $record['sst'], $record['subtotal_amount'], 
                        $record['reason_id'], $record['other_reason'], $record['id'], 'Pending', 'N', 'Y'
                    ]);
    
                    // Get the last inserted ID
                    $newStampId = $insertStmt->insert_id;
    
                    // Fetch related records from stamping_ext
                    $extQuery = "SELECT * FROM stamping_ext WHERE stamp_id = ?";
                    $extStmt = $db->prepare($extQuery);
                    $extStmt->bind_param('s', $record['id']);
                    $extStmt->execute();
    
                    $result2 = $extStmt->get_result();
    
                    if ($extRecord = $result2->fetch_assoc()) {
                        $insertExtStmt->execute([
                            $newStampId, $extRecord['penentusan_baru'], $extRecord['penentusan_semula'], $extRecord['kelulusan_mspk'],
                            $extRecord['no_kelulusan'], $extRecord['indicator_serial'], $extRecord['platform_country'], $extRecord['platform_type'],
                            $extRecord['size'], $extRecord['jenis_pelantar'], $extRecord['jenis_penunjuk'], $extRecord['alat_type'],
                            $extRecord['questions'], $extRecord['steelyard'], $extRecord['bilangan_kaunterpois'], $extRecord['nilais'], $extRecord['bentuk_dulang'], 
                            $extRecord['class'], $extRecord['batu_ujian'], $extRecord['batu_ujian_lain'], $extRecord['other_info'], $extRecord['load_cell_country'],
                            $extRecord['load_cell_no'], $extRecord['load_cells_info']
                        ]);
                    }
                }

                $db->close();
				
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Added Successfully!!" 
					)
				);
            }
        }
    }
    else{
        echo json_encode(
            array(
                "status"=> "failed", 
                "message"=> "Something went wrong!"
            )
        );
    }
}
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}
?>