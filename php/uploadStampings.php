<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['userID'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    foreach ($data as $row) {
        $type = !empty($row['Type']) ? $row['Type'] : null;
        $companyBranch = !empty($row['CompanyBranch']) ? searchCompanyBranchIdByName($row['CompanyBranch'], $db) : null;
        $dealer = !empty($row['Dealer']) ? searchDealerIdByName($row['Dealer'], $db) : null;
        $dealerBranch = !empty($row['DealerBranch']) ? $row['DealerBranch'] : null;
        $customerType = !empty($row['CustomerType']) ? $row['CustomerType'] : null;
        $customers = !empty($row['Customers']) ? searchCustIdByName($row['Customers'], $db) : null;
        $branch = !empty($row['CustomerBranch']) ? searchCustomerBranchIdByName($row['CustomerBranch'], $db) : null;
        $brands = !empty($row['Brand']) ? searchBrandIdByName($row['Brand'], $db) : null;
        $machineType = !empty($row['MachineType']) ? searchMachineIdByName($row['MachineType'], $db) : null;
        $models = !empty($row['Model']) ? searchModelIdByName($row['Model'], $db) : null;
        $makeIn = !empty($row['MakeIn']) ? searchCountryIdByName($row['MakeIn'], $db) : null;
        $capacity = !empty($row['Capacity']) ? searchCapacityIdByName($row['Capacity'], $db) : null;
        $serialNo = !empty($row['SerialNo']) ? $row['SerialNo'] : null;
        $assignTo = !empty($row['AssignTo']) ? searchStaffIdByName($row['AssignTo'], $db) : null;
        $assignTo2 = !empty($row['AssignTo2']) ? searchStaffIdByName($row['AssignTo2'], $db) : null;
        $assignTo3 = !empty($row['AssignTo3']) ? searchStaffIdByName($row['AssignTo3'], $db) : null;
        $ownershipStatus = !empty($row['OwnershipStatus']) ? $row['OwnershipStatus'] : null;
        $validateBy = !empty($row['ValidateBy']) ? searchValidatorIdByName($row['ValidateBy'], $db) : null;
        $cawangan = !empty($row['Cawangan']) ? searchStateIdByName($row['Cawangan'], $db) : null;
        $jenisAlat = !empty($row['JenisAlat']) ? searchJenisAlatIdByName($row['JenisAlat'], $db) : null;
        $machineName = !empty($row['MachineName']) ? searchMachinenameIdByName($row['MachineName'], $db) : null;
        $machineLocation = !empty($row['MachineLocation']) ? $row['MachineLocation'] : null;
        $machineArea = !empty($row['MachineArea']) ? $row['MachineArea'] : null;
        $machineSerialNo = !empty($row['MachineSerialNo']) ? $row['MachineSerialNo'] : null;
        $trade = !empty($row['Trade']) ? $row['Trade'] : null;
        $noDaftarBaru = !empty($row['NoDaftarBaru']) ? $row['NoDaftarBaru'] : null;
        $pinKeselamatan = !empty($row['PinKeselamatan']) ? $row['PinKeselamatan'] : null;
        $siriKeselamatan = !empty($row['SiriKeselamatan']) ? $row['SiriKeselamatan'] : null;
        $sealNoBaru = !empty($row['SealNoBaru']) ? $row['SealNoBaru'] : null;
        $pegawaiContact = !empty($row['PegawaiContact']) ? $row['PegawaiContact'] : null;
        $includeCert = !empty($row['IncludeCert']) ? $row['IncludeCert'] : null;
        $certNo = !empty($row['CertNo']) ? $row['CertNo'] : null;
        $borangD = !empty($row['BorangD']) ? $row['BorangD'] : null;
        $invoiceNo = !empty($row['InvoiceNo']) ? $row['InvoiceNo'] : null;
        $invoicePaymentType = !empty($row['InvoicePaymentType']) ? $row['InvoicePaymentType'] : null;
        $invoicePaymentRef = !empty($row['InvoicePaymentRef']) ? $row['InvoicePaymentRef'] : null;
        $notificationPeriod = !empty($row['NotificationPeriod']) ? $row['NotificationPeriod'] : null;
        $cashBill = !empty($row['CashBill']) ? $row['CashBill'] : null;
        $stampingDate = !empty($row['StampingDate']) ? DateTime::createFromFormat('d/m/Y', $row['StampingDate'])->format('Y-m-d H:i:s') : null;
        $dueDate = !empty($row['DueDate']) ? DateTime::createFromFormat('d/m/Y', $row['DueDate'])->format('Y-m-d H:i:s') : null;
        $pic = !empty($row['PIC']) ? $row['PIC'] : null;
        $customerPic = !empty($row['CustomerPIC']) ? $row['CustomerPIC'] : null;
        $quotationNo = !empty($row['QuotationNo']) ? $row['QuotationNo'] : null;
        $quotationDate = !empty($row['QuotationDate']) ? DateTime::createFromFormat('d/m/Y', $row['QuotationDate'])->format('Y-m-d H:i:s') : null;
        $purchaseNo = !empty($row['PurchaseNo']) ? $row['PurchaseNo'] : null;
        $purchaseDate = !empty($row['PurchaseDate']) ? DateTime::createFromFormat('d/m/Y', $row['PurchaseDate'])->format('Y-m-d H:i:s') : null;
        $remarks = !empty($row['Remarks']) ? $row['Remarks'] : null;
        $internalRemark = !empty($row['InternalRemark']) ? $row['InternalRemark'] : null;
        $validatorInvoice = !empty($row['ValidatorInvoice']) ? $row['ValidatorInvoice'] : null;
        $unitPrice = !empty($row['UnitPrice']) ? $row['UnitPrice'] : 0.00;
        $certPrice = !empty($row['CertPrice']) ? $row['CertPrice'] : 0.00;
        $totalAmount = !empty($row['TotalAmount']) ? $row['TotalAmount'] : 0.00;
        $sst = !empty($row['SST']) ? $row['SST'] : 0.00;
        $subtotalSstAmount = !empty($row['SubtotalSSTAmount']) ? $row['SubtotalSSTAmount'] : 0.00;
        $rebate = !empty($row['Rebate']) ? $row['Rebate'] : 0.00;
        $rebateAmount = !empty($row['RebateAmount']) ? $row['RebateAmount'] : 0.00;
        $subtotalAmount = !empty($row['SubtotalAmount']) ? $row['SubtotalAmount'] : 0.00;
        $log = !empty($row['Log']) ? $row['Log'] : null;
        $products = !empty($row['Products']) ? $row['Products'] : null;
        $stampingType = !empty($row['StampingType']) ? $row['StampingType'] : null;
        $labourCharge = !empty($row['LabourCharge']) ? $row['LabourCharge'] : 0.00;
        $stampfeeLabelCharge = !empty($row['StampFeeLabourCharge']) ? $row['StampFeeLabourCharge'] : 0.00;
        $intRoundUp = !empty($row['IntRoundUp']) ? $row['IntRoundUp'] : 0.00;
        $totalCharges = !empty($row['TotalCharges']) ? $row['TotalCharges'] : 0.00;

        // Initialize empty variables for ext
        $jenis_penunjuk = null;
        $steelyard = null;
        $bilangan_kaunterpois = null;
        $nilais = null;
        $alat_type = null;
        $bentuk_dulang = null;
        $class = null;
        $questions = null;
        $batu_ujian = null;
        $batu_ujian_lain = null;
        $penandaan_batu_ujian = null;
        $nilai_jangka = null;
        $nilai_jangka_other = null;
        $diperbuat_daripada = null;
        $diperbuat_daripada_other = null;
        $pam_no = null;
        $kelulusan_bentuk = null;
        $jenama = null;
        $jenama_other = null;
        $kadar_pengaliran = null;
        $bentuk_penunjuk = null;
        $nilai_jangkaan_maksimum = null;
        $bahan_pembuat = null;
        $bahan_pembuat_other = null;

        if ($insert_stmt = $db->prepare("INSERT INTO stamping (type, company_branch, dealer, dealer_branch, customer_type, customers, branch, brand, machine_type, model, make_in, capacity, serial_no, assignTo, assignTo2, assignTo3, ownership_status, validate_by, cawangan, jenis_alat, machine_name, machine_location, machine_area, machine_serial_no, trade, no_daftar_baru, pin_keselamatan, siri_keselamatan, seal_no_baru, pegawai_contact, include_cert, cert_no, borang_d, invoice_no, invoice_payment_type, invoice_payment_ref, notification_period, cash_bill, stamping_date, due_date, pic, customer_pic, quotation_no, quotation_date, purchase_no, purchase_date, remarks, internal_remark, validator_invoice, unit_price, cert_price, total_amount, sst, subtotal_sst_amt, rebate, rebate_amount, subtotal_amount, log, products, stamping_type, labour_charge, stampfee_labourcharge, int_round_up, total_charges) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss', 
                $type, $companyBranch, $dealer, $dealerBranch, $customerType, $customers, $branch, $brands, $machineType, $models, $makeIn, $capacity, $serialNo, $assignTo, $assignTo2, $assignTo3, $ownershipStatus, $validateBy, $cawangan, $jenisAlat, $machineName, $machineLocation, $machineArea, $machineSerialNo, $trade, $noDaftarBaru, $pinKeselamatan, $siriKeselamatan, $sealNoBaru, $pegawaiContact, $includeCert, $certNo, $borangD, $invoiceNo, $invoicePaymentType, $invoicePaymentRef, $notificationPeriod, $cashBill, $stampingDate, $dueDate, $pic, $customerPic, $quotationNo, $quotationDate, $purchaseNo, $purchaseDate, $remarks, $internalRemark, $validatorInvoice, $unitPrice, $certPrice, $totalAmount, $sst, $subtotalSstAmount, $rebate, $rebateAmount, $subtotalAmount, $log, $products, $stampingType, $labourCharge, $stampfeeLabelCharge, $intRoundUp, $totalCharges);
            $insert_stmt->execute();
            $stampId = $insert_stmt->insert_id;
            $insert_stmt->close();

            // Insert into stamping_ext table
            if ($jenisAlat == '2') { // ATP
                $jenis_penunjuk = !empty($row['JenisPenunjuk']) ? $row['JenisPenunjuk'] : null;
            } elseif ($jenisAlat == '23') { // ATP (MOTORCAR)
                $steelyard = !empty($row['HadTerimaSteelyard(kg)']) ? $row['HadTerimaSteelyard(kg)'] : null;
                $bilangan_kaunterpois = !empty($row['BilanganKaunterpois(biji)']) ? $row['BilanganKaunterpois(biji)'] : null;

                $nilais = [
                    [
                        "no" => 1,
                        "nilai" => !empty($row['NilaiBeratKaunterpois1(kg)']) ? $row['NilaiBeratKaunterpois1(kg)'] : null,
                    ],
                    [
                        "no" => 2,
                        "nilai" => !empty($row['NilaiBeratKaunterpois2(kg)']) ? $row['NilaiBeratKaunterpois2(kg)'] : null,
                    ],
                    [
                        "no" => 3,
                        "nilai" => !empty($row['NilaiBeratKaunterpois3(kg)']) ? $row['NilaiBeratKaunterpois3(kg)'] : null,
                    ],
                    [
                        "no" => 4,
                        "nilai" => !empty($row['NilaiBeratKaunterpois4(kg)']) ? $row['NilaiBeratKaunterpois4(kg)'] : null,
                    ],
                    [
                        "no" => 5,
                        "nilai" => !empty($row['NilaiBeratKaunterpois5(kg)']) ? $row['NilaiBeratKaunterpois5(kg)'] : null,
                    ],
                    [
                        "no" => 6,
                        "nilai" => !empty($row['NilaiBeratKaunterpois6(kg)']) ? $row['NilaiBeratKaunterpois6(kg)'] : null,
                    ]
                ];

                $nilais = json_encode($nilais, JSON_PRETTY_PRINT);
            } else if ($jenisAlat == '5' || $jenisAlat == '18') { // ATN
                $alat_type = !empty($row['JenisAlatType']) ? $row['JenisAlatType'] : null;
                $bentuk_dulang = !empty($row['BentukDulang']) ? $row['BentukDulang'] : null;
            } else if ($jenisAlat == '6') { // ATE
                $class = !empty($row['Class']) ? $row['Class'] : null;
            } else if ($jenisAlat == '14') { // SLL
                $alat_type = !empty($row['JenisAlatType']) ? $row['JenisAlatType'] : null;
                $questions = [
                    [
                        "no" => 1,
                        "answer" => !empty($row['Question1']) ? $row['Question1'] : null,
                    ],
                    [
                        "no" => 2,
                        "answer" => !empty($row['Question2']) ? $row['Question2'] : null,
                    ],
                    [
                        "no" => 3,
                        "answer" => !empty($row['Question3']) ? $row['Question3'] : null,
                    ],
                    [
                        "no" => 4,
                        "answer" => !empty($row['Question4']) ? $row['Question4'] : null,
                    ],
                    [
                        "no" => 5.1,
                        "answer" => !empty($row['Question5.1']) ? $row['Question5.1'] : null,
                    ],
                    [
                        "no" => 5.2,
                        "answer" => !empty($row['Question5.2']) ? $row['Question5.2'] : null,
                    ],
                    [
                        "no" => 6,
                        "answer" => !empty($row['Question6']) ? $row['Question6'] : null,
                    ],
                    [
                        "no" => 7,
                        "answer" => !empty($row['Question7']) ? $row['Question7'] : null,
                    ],
                ];

                $questions = json_encode($questions, JSON_PRETTY_PRINT);
            } else if ($jenisAlat == '7') { // BTU
                $batu_ujian = !empty($row['BatuUjian']) ? $row['BatuUjian'] : null;
                $batu_ujian_lain = !empty($row['BatuUjianLain']) ? $row['BatuUjianLain'] : null;
                $penandaan_batu_ujian = !empty($row['PenandaanPadaBatuUjian']) ? $row['PenandaanPadaBatuUjian'] : null;
            } else if ($jenisAlat == '10') { // AUTO Packer
                $jenis_penunjuk = !empty($row['JenisPenunjuk']) ? $row['JenisPenunjuk'] : null;
            } else if ($jenisAlat == '12') { // SIA
                $nilai_jangka = !empty($row['NilaiJangkaMaksima']) ? $row['NilaiJangkaMaksima'] : null;
                $nilai_jangka_other = !empty($row['NilaiJangkaMaksimaOther']) ? $row['NilaiJangkaMaksimaOther'] : null;
                $diperbuat_daripada = !empty($row['DiperbuatDaripada']) ? $row['DiperbuatDaripada'] : null;
                $diperbuat_daripada_other = !empty($row['DiperbuatDaripadaOther']) ? $row['DiperbuatDaripadaOther'] : null;
            } else if ($jenisAlat == '11') { // BAP
                $pam_no = null;
                $kelulusan_bentuk = null;
                $jenama = null;
                $jenama_other = null;
                $alat_type = null;
                $kadar_pengaliran = null;
                $bentuk_penunjuk = null;

                $pam_no = !empty($row['PamNo']) ? $row['PamNo'] : null;
                $kelulusan_bentuk = !empty($row['NoKelulusanBentuk']) ? $row['NoKelulusanBentuk'] : null;
                $jenama = !empty($row['Jenama/NamaPembuat']) ? $row['Jenama/NamaPembuat'] : null;
                $jenama_other = !empty($row['Jenama/NamaPembuatOther']) ? $row['Jenama/NamaPembuatOther'] : null;
                $alat_type = !empty($row['AlatType']) ? $row['AlatType'] : null;
                $kadar_pengaliran = !empty($row['KadarPengaliran']) ? $row['KadarPengaliran'] : null;
                $bentuk_penunjuk = !empty($row['BentukPenunjukHarga/Kuantiti']) ? $row['BentukPenunjukHarga/Kuantiti'] : null;
            } else if ($jenisAlat == '13') { // SIC
                $nilai_jangkaan_maksimum = !empty($row['NilaiJangkaMaksimum(Kapasiti)']) ? $row['NilaiJangkaMaksimum(Kapasiti)'] : null;
                $bahan_pembuat = !empty($row['BahanPembuat']) ? $row['BahanPembuat'] : null;
                $bahan_pembuat_other = !empty($row['BahanPembuatOther']) ? $row['BahanPembuatOther'] : null;
            }

            $sql = "INSERT INTO stamping_ext (
                stamp_id, jenis_penunjuk, steelyard, bilangan_kaunterpois, nilais, alat_type,
                bentuk_dulang, class, questions, batu_ujian, batu_ujian_lain,
                penandaan_batu_ujian, nilai_jangka, nilai_jangka_other,
                diperbuat_daripada, diperbuat_daripada_other, pam_no, kelulusan_bentuk,
                jenama, jenama_other, kadar_pengaliran, bentuk_penunjuk,
                nilai_jangkaan_maksimum, bahan_pembuat, bahan_pembuat_other
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            if ($insert_ext_stmt = $db->prepare($sql)){
                $insert_ext_stmt->bind_param("sssssssssssssssssssssssss", 
                    $stampId, $jenis_penunjuk, $steelyard, $bilangan_kaunterpois, $nilais, $alat_type,
                    $bentuk_dulang, $class, $questions, $batu_ujian, $batu_ujian_lain,
                    $penandaan_batu_ujian, $nilai_jangka, $nilai_jangka_other,
                    $diperbuat_daripada, $diperbuat_daripada_other, $pam_no, $kelulusan_bentuk,
                    $jenama, $jenama_other, $kadar_pengaliran, $bentuk_penunjuk,
                    $nilai_jangkaan_maksimum, $bahan_pembuat, $bahan_pembuat_other
                );
                $insert_ext_stmt->execute();
                $insert_ext_stmt->close();
            }

            // Prepare insert statement for stamping_log
            $insertLogQuery = "INSERT INTO stamping_log (
                action, user_id, item_id
            ) VALUES (?, ?, ?)";

            if ($insert_log_stmt = $db->prepare($insertLogQuery)) {
                $action = 'INSERT';
                $insert_log_stmt->bind_param('sss', $action, $uid, $stampId);
                $insert_log_stmt->execute();
                $insert_log_stmt->close();
            }
        }
    }

    $db->close();

    echo json_encode(
        array(
            "status"=> "success", 
            "message"=> "Added Successfully!!" 
        )
    );
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>
