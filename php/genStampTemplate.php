<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';

session_start();

$uid = '';

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
}else{
    $uid = $_SESSION['userID'];
}

if(isset($_POST['duplicateNo'], $_POST['id'])){
    $id = $_POST['id'];
    $duplicateNo = $_POST['duplicateNo'];
    
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
            
            if ($record = $result->fetch_assoc()) {
                // Excel file name for download 
                $fileName = "Stamping-data.xls";

                $capacityId = $record['capacity'];
                $capacityType = '';
                $capacityName = '';
                if (isset($record['capacity']) && $record['capacity'] != ''){
                    $capacityQuery = "SELECT * FROM capacity WHERE id = $capacityId";
                    $capacityDetail = mysqli_query($db, $capacityQuery);
                    $capacityRow = mysqli_fetch_assoc($capacityDetail);
                    if(!empty($capacityRow)){
                        $capacityType = $capacityRow['range_type'];
                        $capacityName = $capacityRow['name'];
                    }
                }

                $headers = ['Type','Company Branch','Dealer','Dealer Branch','Customer Type','Customers','Brand','Machine Type','Model','Make In','Capacity','Serial No','Assign To','Assign To 2','Assign To 3','Ownership Status','Validator Lama','Validate By','Cawangan','Jenis Alat','Machine Name','Machine Location','Machine Area','Machine Serial No','Trade','No Daftar Lama','No Daftar Baru','Pin Keselamatan','Siri Keselamatan','Seal No Lama','Seal No Baru','Pegawai Contact','Include Cert','Cert No','Borang D','Borang E','Borang E Date','Invoice No','Invoice Payment Type','Invoice Payment Ref','Notification Period','Cash Bill','Stamping Date','Last Year Stamping Date','Due Date','PIC','Customer PIC','Quotation No','Quotation Date','Purchase No','Purchase Date','Remarks','Internal Remark','Validator Invoice','Unit Price','Cert Price','Total Amount','SST','Subtotal SST Amount','Rebate','Rebate Amount','Subtotal Amount','Log','Products','Stamping Type','Branch','Labour Charge','Stamp Fee Labour Charge','Int Round Up','Total Charges'];
                $values = [$record['type'], searchCompanyBranchById($record['company_branch'], $db), searchResellerNameById($record['dealer'], $db), $record['dealer_branch'], $record['customer_type'], searchCustNameById($record['customers'], $db),
                searchBrandNameById($record['brand'], $db), searchMachineNameById($record['machine_type'], $db), searchModelNameById($record['model'], $db), searchCountryById($record['make_in'], $db), $capacityName, $record['serial_no'], searchStaffNameById($record['assignTo'], $db), searchStaffNameById($record['assignTo2'], $db), searchStaffNameById($record['assignTo3'], $db), $record['ownership_status'], searchValidatorNameById($record['validator_lama'], $db), searchValidatorNameById($record['validate_by'], $db),
                $record['cawangan'], searchJenisAlatNameByid($record['jenis_alat'], $db), searchMachinenameNameById($record['machine_name'], $db), $record['machine_location'], $record['machine_area'], $record['machine_serial_no'], $record['trade'], $record['no_daftar_lama'], $record['no_daftar_baru'], $record['pin_keselamatan'], $record['siri_keselamatan'], $record['seal_no_lama'], $record['seal_no_baru'], $record['pegawai_contact'], $record['include_cert'], $record['cert_no'], $record['borang_d'], $record['borang_e'], ($record['borang_e_date'] != null ? convertDatetimeToDate($record['borang_e_date']) : ''), $record['invoice_no'], $record['invoice_payment_type'], $record['invoice_payment_ref'], $record['notification_period'], $record['cash_bill'], $record['stamping_date'] != null ? convertDatetimeToDate($record['stamping_date']) : '', $record['last_year_stamping_date'] != null ? convertDatetimeToDate($record['last_year_stamping_date']) : '', $record['due_date'] != null ? convertDatetimeToDate($record['due_date']) : '', $record['pic'], $record['customer_pic'], $record['quotation_no'], $record['quotation_date'], $record['purchase_no'], $record['purchase_date'], $record['remarks'], $record['internal_remark'], $record['validator_invoice'], $record['unit_price'], $record['cert_price'], $record['total_amount'], $record['sst'], $record['subtotal_sst_amt'], $record['rebate'], $record['rebate_amount'], $record['subtotal_amount'], $record['log'], $record['products'], $record['stamping_type'], $record['branch'], $record['labour_charge'], $record['stampfee_labourcharge'], $record['int_round_up'], $record['total_charges']];

                // Fetch related records from stamping_ext
                $extQuery = "SELECT * FROM stamping_ext WHERE stamp_id = ?";
                $extStmt = $db->prepare($extQuery);
                $extStmt->bind_param('s', $record['id']);
                $extStmt->execute();

                $result2 = $extStmt->get_result();

                if ($extRecord = $result2->fetch_assoc()) {
                    if ($record['jenis_alat'] == '2') { // ATP
                        $headers = array_merge($headers, ['Jenis Penunjuk']);
                        $values = array_merge($values, [$extRecord['jenis_penunjuk']]);
                    } else if ($record['jenis_alat'] == '23') {  // ATP (MOTORCAR)
                        $headers = array_merge($headers, ['Had Terima Steelyard (kg)', 'Bilangan Kaunterpois (biji)', 'Nilai Berat Kaunterpois 1 (kg)', 'Nilai Berat Kaunterpois 2 (kg)', 'Nilai Berat Kaunterpois 3 (kg)', 'Nilai Berat Kaunterpois 4 (kg)', 'Nilai Berat Kaunterpois 5 (kg)', 'Nilai Berat Kaunterpois 6 (kg)']);

                        $nilais = json_decode($extRecord['nilais'], true);
                        $values = array_merge($values, [$extRecord['steelyard'], $extRecord['bilangan_kaunterpois'], $nilais[0]['nilai'], $nilais[1]['nilai'], $nilais[2]['nilai'], $nilais[3]['nilai'], $nilais[4]['nilai'], $nilais[5]['nilai']]);
                    } else if ($record['jenis_alat'] == '5' || $record['jenis_alat'] == '18') {  // ATN
                        $headers = array_merge($headers, ['Jenis Alat Type', 'Bentuk Dulang']);
                        $values = array_merge($values, [$extRecord['alat_type'], $extRecord['bentuk_dulang']]);
                    } else if ($record['jenis_alat'] == '6') {  // ATE
                        $headers = array_merge($headers, ['Class']);
                        $values = array_merge($values, [$extRecord['class']]);
                    } else if ($record['jenis_alat'] == '14') {  // SLL
                        $headers = array_merge($headers, ['Jenis Alat Type', 'Question 01', 'Question 02', 'Question 03', 'Question 04', 'Question 05', 'Question 06', 'Question 07']);

                        $questions = json_decode($extRecord['questions'], true);
                        $values = array_merge($values, [$extRecord['alat_type'], $questions[0]['answer'], $questions[1]['answer'], $questions[2]['answer'], $questions[3]['answer'], $questions[4]['answer'], $questions[5]['answer'], $questions[6]['answer']]);
                    } else if ($record['jenis_alat'] == '7') { // BTU
                        $headers = array_merge($headers, ['Batu Ujian', 'Batu Ujian Lain', 'Penandaan Pada Batu Ujian']);
                        $values = array_merge($values, [$extRecord['batu_ujian'], $extRecord['batu_ujian_lain'], $extRecord['penandaan_batu_ujian']]);
                    } else if ($record['jenis_alat'] == '10') { // AUTO Packer
                        $headers = array_merge($headers, ['Jenis Penunjuk']);
                        $values = array_merge($values, [$extRecord['jenis_penunjuk']]);
                    } else if ($record['jenis_alat'] == '12') { // SIA
                        $headers = array_merge($headers, ['Nilai Jangka Maksima', 'Nilai Jangka Maksima Other', 'Diperbuat Daripada', 'Diperbuat Daripada Other']);
                        $values = array_merge($values, [$extRecord['nilai_jangka'], $extRecord['nilai_jangka_other'], $extRecord['diperbuat_daripada'], $extRecord['diperbuat_daripada_other']]);
                    } else if ($record['jenis_alat'] == '11') { // BAP
                        $headers = array_merge($headers, ['Pam No', 'No Kelulusan Bentuk', 'Jenama / Nama Pembuat', 'Jenama / Nama Pembuat Other', 'Alat Type', 'Kadar Pengaliran', 'Bentuk Penunjuk Harga/Kuantiti']);
                        $values = array_merge($values, [$extRecord['pam_no'], $extRecord['kelulusan_bentuk'], $extRecord['jenama'], $extRecord['jenama_other'], $extRecord['alat_type'], $extRecord['kadar_pengaliran'], $extRecord['bentuk_penunjuk']]);
                    } else if ($record['jenis_alat'] == '13') { // SIC
                        $headers = array_merge($headers, ['Nilai Jangka Maksimum (Kapasiti)', 'Bahan Pembuat', 'Bahan Pembuat Other']);
                        $values = array_merge($values, [$extRecord['nilai_jangkaan_maksimum'], $extRecord['bahan_pembuat'], $extRecord['bahan_pembuat_other']]);
                    }
                }

                $excelData = implode("\t", array_values($headers)) . "\n";
                for($i=0; $i<(int)$duplicateNo; $i++){
                    $lineData = array_merge($values);
                    $excelData .= implode("\t", array_values($lineData)) . "\n";
                }

                // Headers for download 
                header("Content-Type: application/vnd.ms-excel"); 
                header("Content-Disposition: attachment; filename=\"$fileName\""); 
                
                // Render excel data 
                echo $excelData;
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

    $select_stmt->close();
    $db->close();
    exit;
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