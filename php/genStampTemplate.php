<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';

// PhpSpreadsheet
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date;

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
    exit;
}
$uid = $_SESSION['userID'];

if(isset($_POST['duplicateNo'], $_POST['id'])){
    $id = $_POST['id'];
    $duplicateNo = (int)$_POST['duplicateNo'];

    if ($select_stmt = $db->prepare("SELECT * FROM stamping WHERE id = ?")) {
        $select_stmt->bind_param('s', $id);

        if (!$select_stmt->execute()) {
            echo json_encode(["status" => "failed","message" => "Failed to get records"]); 
            exit;
        }

        $result = $select_stmt->get_result();
        if ($record = $result->fetch_assoc()) {
            // file name
            $fileName = "Stamping-data.xlsx";

            // Capacity
            $capacityName = '';
            if (!empty($record['capacity'])){
                $capacityQuery = "SELECT * FROM capacity WHERE id = ".$record['capacity'];
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);
                if(!empty($capacityRow)){
                    $capacityName = $capacityRow['name'];
                }
            }

            // Jenis Alat
            $jenisAlat = searchJenisAlatNameByid($record['jenis_alat'], $db);

            // Base headers
            $headers = [
                'Type','Company Branch','Dealer','Dealer Branch','Customer Type','Customers','Customer Branch',
                'Brand','Machine Type','Model','Make In','Capacity','Serial No','Assign To','Assign To 2','Assign To 3',
                'Ownership Status','Validate By','Cawangan','Jenis Alat','Machine Name','Machine Location','Machine Area',
                'Machine Serial No','Trade','No Daftar Baru','Pin Keselamatan','Siri Keselamatan','Seal No Baru','Pegawai Contact',
                'Include Cert','Cert No','Borang D','Invoice No','Invoice Payment Type','Invoice Payment Ref','Notification Period',
                'Cash Bill','Stamping Date','Due Date','PIC','Customer PIC','Quotation No','Quotation Date','Purchase No',
                'Purchase Date','Remarks','Internal Remark','Validator Invoice','Unit Price','Cert Price','Total Amount',
                'SST','Subtotal SST Amount','Rebate','Rebate Amount','Subtotal Amount','Log','Products','Stamping Type',
                'Labour Charge','Stamp Fee Labour Charge','Int Round Up','Total Charges'
            ];

            // Base values
            $values = [
                $record['type'], 
                searchCompanyBranchById($record['company_branch'], $db), 
                searchResellerNameById($record['dealer'], $db), 
                $record['dealer_branch'], 
                $record['customer_type'], 
                searchCustNameById($record['customers'], $db), 
                searchCustomerBranchById($record['branch'], $db), 
                searchBrandNameById($record['brand'], $db), 
                searchMachineNameById($record['machine_type'], $db), 
                searchModelNameById($record['model'], $db), 
                searchCountryById($record['make_in'], $db), 
                $capacityName, 
                $record['serial_no'], 
                searchStaffNameById($record['assignTo'], $db), 
                searchStaffNameById($record['assignTo2'], $db), 
                searchStaffNameById($record['assignTo3'], $db), 
                $record['ownership_status'], 
                searchValidatorNameById($record['validate_by'], $db),
                searchStateNameById($record['cawangan'], $db), 
                $jenisAlat, 
                searchMachinenameNameById($record['machine_name'], $db), 
                $record['machine_location'], 
                $record['machine_area'], 
                $record['machine_serial_no'], 
                $record['trade'], 
                $record['no_daftar_baru'], 
                $record['pin_keselamatan'], 
                $record['siri_keselamatan'], 
                $record['seal_no_baru'], 
                $record['pegawai_contact'], 
                $record['include_cert'], 
                $record['cert_no'], 
                $record['borang_d'], 
                $record['invoice_no'], 
                $record['invoice_payment_type'], 
                $record['invoice_payment_ref'], 
                $record['notification_period'], 
                $record['cash_bill'], 
                $record['stamping_date'] ? Date::PHPToExcel(new DateTime($record['stamping_date'])) : '',
                $record['due_date'] ? Date::PHPToExcel(new DateTime($record['due_date'])) : '',
                $record['pic'], 
                $record['customer_pic'], 
                $record['quotation_no'], 
                $record['quotation_date'] ? Date::PHPToExcel(new DateTime($record['quotation_date'])) : '',
                $record['purchase_no'], 
                $record['purchase_date'] ? Date::PHPToExcel(new DateTime($record['purchase_date'])) : '',
                $record['remarks'], 
                $record['internal_remark'], 
                $record['validator_invoice'], 
                $record['unit_price'], 
                $record['cert_price'], 
                $record['total_amount'], 
                $record['sst'], 
                $record['subtotal_sst_amt'], 
                $record['rebate'], 
                $record['rebate_amount'], 
                $record['subtotal_amount'], 
                $record['log'], 
                $record['products'], 
                $record['stamping_type'], 
                $record['labour_charge'], 
                $record['stampfee_labourcharge'], 
                $record['int_round_up'], 
                $record['total_charges']
            ];

            // stamping_ext
            $extQuery = "SELECT * FROM stamping_ext WHERE stamp_id = ?";
            $extStmt = $db->prepare($extQuery);
            $extStmt->bind_param('s', $record['id']);
            $extStmt->execute();
            $result2 = $extStmt->get_result();

            if ($extRecord = $result2->fetch_assoc()) {
                if (str_contains($jenisAlat, 'ATP (MOTORCAR)')) { // ATP (MOTORCAR)
                    $headers = array_merge($headers, [
                        'Had Terima Steelyard (kg)', 'Bilangan Kaunterpois (biji)',
                        'Nilai Berat Kaunterpois 1 (kg)','Nilai Berat Kaunterpois 2 (kg)',
                        'Nilai Berat Kaunterpois 3 (kg)','Nilai Berat Kaunterpois 4 (kg)',
                        'Nilai Berat Kaunterpois 5 (kg)','Nilai Berat Kaunterpois 6 (kg)'
                    ]);
                    $nilais = json_decode($extRecord['nilais'], true);
                    $values = array_merge($values, [
                        $extRecord['steelyard'], 
                        $extRecord['bilangan_kaunterpois'],
                        $nilais[0]['nilai'] ?? '',$nilais[1]['nilai'] ?? '',
                        $nilais[2]['nilai'] ?? '',$nilais[3]['nilai'] ?? '',
                        $nilais[4]['nilai'] ?? '',$nilais[5]['nilai'] ?? ''
                    ]);
                } else if (str_contains($jenisAlat, 'ATP')) { // ATP
                    $headers[] = 'Jenis Penunjuk';
                    $values[] = $extRecord['jenis_penunjuk'];
                } else if (str_contains($jenisAlat, 'ATN')) { // ATN
                    $headers = array_merge($headers, ['Jenis Alat Type','Bentuk Dulang']);
                    $values = array_merge($values, [$extRecord['alat_type'],$extRecord['bentuk_dulang']]);
                } else if (str_contains($jenisAlat, 'ATE')) { // ATE
                    $headers[] = 'Class';
                    $values[] = $extRecord['class'];
                } else if (str_contains($jenisAlat, 'SLL')) { // SLL
                    $headers = array_merge($headers, ['Jenis Alat Type','Question 1','Question 2','Question 3','Question 4','Question 5.1','Question 5.2','Question 6','Question 7']);
                    $questions = json_decode($extRecord['questions'], true);
                    $values = array_merge($values, [
                        $extRecord['alat_type'],
                        $questions[0]['answer'] ?? '', $questions[1]['answer'] ?? '', $questions[2]['answer'] ?? '',
                        $questions[3]['answer'] ?? '', $questions[4]['answer'] ?? '', $questions[5]['answer'] ?? '',
                        $questions[6]['answer'] ?? '', $questions[7]['answer'] ?? ''
                    ]);
                } else if (str_contains($jenisAlat, 'BTU')) { // BTU
                    $headers = array_merge($headers, ['Batu Ujian','Batu Ujian Lain','Penandaan Pada Batu Ujian']);
                    $values = array_merge($values, [$extRecord['batu_ujian'],$extRecord['batu_ujian_lain'],$extRecord['penandaan_batu_ujian']]);
                } else if (str_contains($jenisAlat, 'SIA')) { // SIA
                    $headers = array_merge($headers, ['Nilai Jangka Maksima','Nilai Jangka Maksima Other','Diperbuat Daripada','Diperbuat Daripada Other']);
                    $values = array_merge($values, [$extRecord['nilai_jangka'],$extRecord['nilai_jangka_other'],$extRecord['diperbuat_daripada'],$extRecord['diperbuat_daripada_other']]);
                } else if (str_contains($jenisAlat, 'BAP')) { // BAP
                    $headers = array_merge($headers, ['Pam No','No Kelulusan Bentuk','Jenama / Nama Pembuat','Jenama / Nama Pembuat Other','Alat Type','Kadar Pengaliran','Bentuk Penunjuk Harga/Kuantiti']);
                    $values = array_merge($values, [$extRecord['pam_no'],$extRecord['kelulusan_bentuk'],$extRecord['jenama'],$extRecord['jenama_other'],$extRecord['alat_type'],$extRecord['kadar_pengaliran'],$extRecord['bentuk_penunjuk']]);
                } else if (str_contains($jenisAlat, 'SIC')) { // SIC
                    $headers = array_merge($headers, ['Nilai Jangka Maksimum (Kapasiti)','Bahan Pembuat','Bahan Pembuat Other']);
                    $values = array_merge($values, [$extRecord['nilai_jangkaan_maksimum'],$extRecord['bahan_pembuat'],$extRecord['bahan_pembuat_other']]);
                }
            }

            // Build spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Headers
            $col = 1;
            foreach($headers as $h){
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . '1', $h);
                $col++;
            }

            // Duplicate rows
            for($i=0; $i<$duplicateNo; $i++){
                $col=1; $row=$i+2;
                foreach($values as $idx=>$val){
                    $cellCoordinate = Coordinate::stringFromColumnIndex($col) . $row;
                    $sheet->setCellValue($cellCoordinate, $val);
                    // format Excel date
                    if(in_array($headers[$idx], ['Stamping Date','Due Date','Quotation Date','Purchase Date']) && $val!=''){
                        $sheet->getStyle($cellCoordinate)
                              ->getNumberFormat()
                              ->setFormatCode('dd/mm/yyyy');
                    }
                    $col++;
                }
            }

            // Download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=\"$fileName\"");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        }
    }
    $select_stmt->close();
    $db->close();
    exit;
}
else {
    echo json_encode(["status"=> "failed","message"=> "Please fill in all the fields"]); 
}
