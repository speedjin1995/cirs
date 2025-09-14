<?php
require_once 'db_connect.php';

session_start();

$uid = '';

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
}else{
    $uid = $_SESSION['userID'];
}

if(isset($_POST['id'])){
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING); 

    // Set complete restamping column to Y
    if ($update_stmt = $db->prepare("UPDATE stamping SET restamping = 'Y' WHERE id = ?")) {
        $update_stmt->bind_param('s', $id);

        if (!$update_stmt->execute()) {
            $update_stmt->close();

            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Failed to update record"
                )); 
        }
        else{
            $update_stmt->close();

            if ($select_stmt = $db->prepare("SELECT * FROM stamping WHERE id = ?")) {
                $select_stmt->bind_param('s', $id);

                if (!$select_stmt->execute()) {
                    $select_stmt->close();

                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Failed to get records"
                        )); 
                }
                else{
                    $result = $select_stmt->get_result();
                    $message = array();

                    $select_stmt->close();
                    
                    if ($record = $result->fetch_assoc()) {
                        // Prepare insert statement for stamping
                        $insertQuery = "INSERT INTO stamping (
                            type, company_branch, dealer, dealer_branch, customer_type, customers, branch, products, brand, machine_type, model, make_in, 
                            capacity, capacity_high, assignTo, assignTo2, assignTo3, serial_no, validator_lama, validate_by, jenis_alat, machine_name, machine_location, machine_area, machine_serial_no, trade, no_daftar, no_daftar_lama, no_daftar_baru, 
                            pin_keselamatan, siri_keselamatan, include_cert, borang_d, borang_e, borang_e_date, cawangan, invoice_no, notification_period, cash_bill, stamping_type, last_year_stamping_date, stamping_date, 
                            due_date, pic, customer_pic, quotation_no, quotation_date, purchase_no, purchase_date, remarks, internal_remark, validator_invoice, log, 
                            unit_price, cert_price, total_amount, sst, subtotal_sst_amt, rebate, rebate_amount, subtotal_amount, labour_charge, stampfee_labourcharge, int_round_up, total_charges, seal_no_lama, seal_no_baru, pegawai_contact, cert_no, ownership_status, invoice_payment_type, invoice_payment_ref, reason_id, other_reason, existing_id, status
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; //76

                        $insertStmt = $db->prepare($insertQuery);

                        // Prepare insert statement for stamping_ext
                        $insertExtQuery = "INSERT INTO stamping_ext (
                            stamp_id, penentusan_baru, penentusan_semula, kelulusan_mspk, no_kelulusan, indicator_serial, 
                            platform_country, platform_type, size, jenis_pelantar, jenis_penunjuk, alat_type, questions, 
                            steelyard, bilangan_kaunterpois, nilais, bentuk_dulang, class, batu_ujian, batu_ujian_lain, other_info, 
                            load_cell_country, load_cell_no, load_cells_info, penandaan_batu_ujian, btu_info, btu_box_info, nilai_jangka,
                            nilai_jangka_other, diperbuat_daripada, diperbuat_daripada_other, pam_no, kelulusan_bentuk, kadar_pengaliran,
                            bentuk_penunjuk, jenama, jenama_other, nilai_jangkaan_maksimum, bahan_pembuat, bahan_pembuat_other
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                        $insertExtStmt = $db->prepare($insertExtQuery);

                        // Prepare insert statement for stamping_log
                        $insertLogQuery = "INSERT INTO stamping_log (
                            action, user_id, item_id
                        ) VALUES (?, ?, ?)";

                        $insertLogStmt = $db->prepare($insertLogQuery);

                        $insertStmt->execute([
                            $record['type'], $record['company_branch'],$record['dealer'], $record['dealer_branch'], $record['customer_type'], $record['customers'],
                            $record['branch'], $record['products'], $record['brand'], $record['machine_type'], $record['model'], $record['make_in'],
                            $record['capacity'], $record['capacity_high'], $record['assignTo'], $record['assignTo2'], $record['assignTo3'], $record['serial_no'], $record['validator_lama'], $record['validate_by'],
                            $record['jenis_alat'], $record['machine_name'], $record['machine_location'], $record['machine_area'], $record['machine_serial_no'], $record['trade'], $record['no_daftar'], $record['no_daftar_lama'], $record['no_daftar_baru'], $record['pin_keselamatan'], $record['siri_keselamatan'], 
                            $record['include_cert'], $record['borang_d'], $record['borang_e'], $record['borang_e_date'], $record['cawangan'], null, $record['notification_period'], $record['cash_bill'], 'NEW', null, 
                            null, null, $record['pic'], $record['customer_pic'], null, null, null, null, $record['remarks'], $record['internal_remark'], $record['validator_invoice'],
                            $record['log'], $record['unit_price'], $record['cert_price'], $record['total_amount'], $record['sst'], $record['subtotal_sst_amt'], $record['rebate'], $record['rebate_amount'], $record['subtotal_amount'], $record['labour_charge'], $record['stampfee_labourcharge'], $record['int_round_up'], $record['total_charges'], $record['seal_no_lama'], $record['seal_no_baru'], $record['pegawai_contact'], $record['cert_no'], 'OWN', null, null,
                            $record['reason_id'], $record['other_reason'], $record['id'], 'Pending'
                        ]);
        
                        // Get the last inserted ID
                        $newStampId = $insertStmt->insert_id;
                        $insertStmt->close();
                        
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
                                $extRecord['load_cell_no'], $extRecord['load_cells_info'],
                                $extRecord['penandaan_batu_ujian'], $extRecord['btu_info'],
                                $extRecord['btu_box_info'], $extRecord['nilai_jangka'], $extRecord['nilai_jangka_other'], $extRecord['diperbuat_daripada'],
                                $extRecord['diperbuat_daripada_other'], $extRecord['pam_no'], $extRecord['kelulusan_bentuk'], $extRecord['kadar_pengaliran'],
                                $extRecord['bentuk_penunjuk'], $extRecord['jenama'], $extRecord['jenama_other'], $extRecord['nilai_jangkaan_maksimum'],
                                $extRecord['bahan_pembuat'], $extRecord['bahan_pembuat_other']
                            ]);

                            $insertExtStmt->close();
                        }
                        $extStmt->close();

                        // Insert for Log
                        $action = "INSERT";
                        $insertLogStmt->execute([
                            $action, $uid, $newStampId
                        ]);
                        
                        $insertLogStmt->close();
                        
                        echo json_encode(
                            array(
                                "status"=> "success", 
                                "message"=> "Added Successfully!!" 
                            )
                        );
                    }
                }
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

    $db->close();
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