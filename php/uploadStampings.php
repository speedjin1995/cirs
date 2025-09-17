<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['userID'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    $rowCount = 1;
    $errorArray = [];
    foreach ($data as $row) {
        $type = !empty($row['Type']) ? $row['Type'] : null;
        $companyBranch = !empty($row['CompanyBranch']) ? searchCompanyBranchIdByName($row['CompanyBranch'], $db) : null;
        $dealer = !empty($row['Dealer']) ? searchDealerIdByName($row['Dealer'], $db) : null;
        $customerType = !empty($row['CustomerType']) ? $row['CustomerType'] : null;
        $customers = !empty($row['Customers']) ? searchCustIdByName($row['Customers'], $db) : null;
        $otherCode = !empty($row['OtherCode']) ? $row['OtherCode'] : null;
        $brands = !empty($row['Brand']) ? searchBrandIdByName($row['Brand'], $db) : null;
        $machineType = !empty($row['MachineType']) ? searchMachineIdByName($row['MachineType'], $db) : null;
        $models = !empty($row['Model']) ? searchModelIdByName($row['Model'], $db) : null;
        $makeIn = !empty($row['MakeIn']) ? searchCountryIdByName($row['MakeIn'], $db) : null;
        $capacity = !empty($row['Capacity']) ? searchCapacityIdByName($row['Capacity'], $db) : null;
        $serialNo = !empty($row['SerialNo']) ? $row['SerialNo'] : null;
        $assignTo = !empty($row['AssignTo']) ? searchStaffIdByName($row['AssignTo'], $db) : null;
        $assignTo2 = !empty($row['AssignTo2']) ? searchStaffIdByName($row['AssignTo2'], $db) : null;
        $assignTo3 = !empty($row['AssignTo3']) ? searchStaffIdByName($row['AssignTo3'], $db) : null;
        $ownershipStatus = !empty($row['OwnershipStatus']) ? ($row['OwnershipStatus'] === 'Rental Unit' ? 'RENT' : ($row['OwnershipStatus'] === 'Customer Unit' ? 'OWN' : $row['OwnershipStatus'])) : null;
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
        $pegawaiContact = !empty($row['PegawaiContact']) ? searchOfficeIdByName($row['PegawaiContact'], $db) : null;
        $includeCert = !empty($row['IncludeCert']) ? $row['IncludeCert'] : null;
        $certNo = !empty($row['CertNo']) ? $row['CertNo'] : null;
        $borangD = !empty($row['BorangD']) ? $row['BorangD'] : null;
        $borangE = !empty($row['BorangE']) ? $row['BorangE'] : null;
        $borangEDate = !empty($row['BorangEDate']) ? DateTime::createFromFormat('d/m/Y', $row['BorangEDate'])->format('Y-m-d H:i:s') : null;
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

        if (isset($type) && !empty($type) && $type != ''){
            if ($type == 'RESELLER') {
                if ($dealer == null || $dealer == ''){
                    $errorArray[] = "Row ".$rowCount.": Dealer is required or not found for RESELLER type.";
                    continue; // Skip to the next row
                }else{
                    $dealerBranch = searchResellerBranchByResellerId($dealer, $db);
                }
            }
        }else{
            $errorArray[] = "Row ".$rowCount.": Type is required.";
            continue; // Skip to the next row
        } 

        if ($companyBranch == null || $companyBranch == ''){
            $errorArray[] = "Row ".$rowCount.": Company Branch is required or not found.";
            continue; // Skip to the next row
        }

        if (isset($customerType) && !empty($customerType) && $customerType != ''){
            if ($customerType == 'NEW') {
                $branchName = !empty($row['CustomerBranch']) ? $row['CustomerBranch'] : null;
                $branchAddressLine1 = !empty($row['AddressLine1']) ? $row['AddressLine1'] : null;
                $branchAddressLine2 = !empty($row['AddressLine2']) ? $row['AddressLine2'] : null;
                $branchAddressLine3 = !empty($row['AddressLine3']) ? $row['AddressLine3'] : null;
                $branchAddressLine4 = !empty($row['AddressLine4']) ? $row['AddressLine4'] : null;
                $branchTel = !empty($row['Tel']) ? $row['Tel'] : null;
                $branchEmail = !empty($row['Email']) ? $row['Email'] : null;

                if (isset($customers) && !empty($customers) && $customers != ''){
                    $errorArray[] = "Row ".$rowCount.": Customer already exists, change Customer Type to EXISTING.";
                    continue; // Skip to the next row
                }

                if ((!empty($record['Customers']) && $record['Customers'] != '') || (!empty($branchAddressLine1) && $branchAddressLine1 != '')){
                    // Processing to insert new customer and branch
                    $custNameFirstLetter = substr($row['Customers'], 0, 1);
                    $firstChar = $custNameFirstLetter;
                    $code = 'C-'.strtoupper($custNameFirstLetter);

                    $customerQuery = "SELECT * FROM customers WHERE customer_code LIKE '%$code%' ORDER BY customer_code DESC";
                    $customerDetail = mysqli_query($db, $customerQuery);
                    $customerRow = mysqli_fetch_assoc($customerDetail);
            
                    $customerCode = null;
                    $codeSeq = null;
                    $count = '';
            
                    if(!empty($customerRow)){
                        $customerCode = $customerRow['customer_code'];
                        preg_match('/\d+/', $customerCode, $matches);
                        $codeSeq = (int)$matches[0]; 
                        $nextSeq = $codeSeq+1;
                        $count = str_pad($nextSeq, 4, '0', STR_PAD_LEFT); 
                        $code.=$count;
                    }
                    else{
                        $nextSeq = 1;
                        $count = str_pad($nextSeq, 4, '0', STR_PAD_LEFT); 
                        $code.=$count;
                    } 
            
                    // Customer does not exist, create a new customer
                    if ($insert_stmt = $db->prepare("INSERT INTO customers (customer_name, customer_code, customer_address, address2, address3, address4, customer_phone, customer_email, customer_status, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                        $customer_status = 'CUSTOMERS';
                        $insert_stmt->bind_param('ssssssssssss', $record['Customers'], $code, $branchAddressLine1, $branchAddressLine2, $branchAddressLine3, $branchAddressLine4, $branchTel, $branchEmail, $customer_status, $pic, $customerPic, $otherCode);
                        
                        if ($insert_stmt->execute()) {
                            $customers = $insert_stmt->insert_id;
                            $customerType = 'EXISTING';

                            if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4, branch_name, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
                                $insert_stmt2->bind_param('ssssssss', $customers, $branchAddressLine1, $branchAddressLine2, $branchAddressLine3, $branchAddressLine4, $branchName, $pic, $customerPic);
                                $insert_stmt2->execute();
                                $branch = $insert_stmt2->insert_id;
                                $insert_stmt2->close();
                            } 
                        } else {
                            $errorArray[] = "Row ".$rowCount.": Failed to insert new customer.";
                            continue; // Skip to the next row
                        }

                        $insert_stmt->close();
                    }
                }else{
                    $errorArray[] = "Row ".$rowCount.": Customer Name && Address Line 1 is required for new customer branch.";
                    continue; // Skip to the next row
                }
            }else{
                $branch = !empty($row['CustomerBranch']) ? searchCustomerBranchIdByName($row['CustomerBranch'], $customers, $db) : null;
            }
        }else{
            $errorArray[] = "Row ".$rowCount.": Customer Type is required.";
            continue; // Skip to the next row
        } 

        if ($models == null || $models == ''){
            $errorArray[] = "Row ".$rowCount.": Model is required or not found.";
            continue; // Skip to the next row
        }

        if ($brands == null || $brands == ''){
            $errorArray[] = "Row ".$rowCount.": Brand is required or not found.";
            continue; // Skip to the next row
        }

        if ($serialNo == null || $serialNo == ''){
            $errorArray[] = "Row ".$rowCount.": Serial No is required.";
            continue; // Skip to the next row
        }

        if ($makeIn == null || $makeIn == ''){
            $errorArray[] = "Row ".$rowCount.": Make In is required.";
            continue; // Skip to the next row
        }

        if ($machineType == null || $machineType == ''){
            $errorArray[] = "Row ".$rowCount.": Machine Type is required.";
            continue; // Skip to the next row
        }

        if ($jenisAlat == null || $jenisAlat == ''){
            $errorArray[] = "Row ".$rowCount.": Jenis Alat is required.";
            continue; // Skip to the next row
        }

        if ($trade == null || $trade == ''){
            $errorArray[] = "Row ".$rowCount.": Trade is required.";
            continue; // Skip to the next row
        }

        if ($capacity == null || $capacity == ''){
            $errorArray[] = "Row ".$rowCount.": Capacity is required.";
            continue; // Skip to the next row
        }

        if ($assignTo == null || $assignTo == ''){
            $errorArray[] = "Row ".$rowCount.": Assign To is required.";
            continue; // Skip to the next row
        }

        if ($ownershipStatus == null || $ownershipStatus == ''){
            $errorArray[] = "Row ".$rowCount.": Ownership Status is required.";
            continue; // Skip to the next row
        }

        if ($stampingType == null || $stampingType == ''){
            $errorArray[] = "Row ".$rowCount.": Stamping Type is required.";
            continue; // Skip to the next row
        }

        if ($validateBy == null || $validateBy == ''){
            $errorArray[] = "Row ".$rowCount.": Validate By is required.";
            continue; // Skip to the next row
        }

        if ($cawangan == null || $cawangan == ''){
            $errorArray[] = "Row ".$rowCount.": Cawangan is required.";
            continue; // Skip to the next row
        }

        if ($includeCert == null || $includeCert == ''){
            $errorArray[] = "Row ".$rowCount.": Include Cert is required.";
            continue; // Skip to the next row
        }

        if ($notificationPeriod == null || $notificationPeriod == ''){
            $errorArray[] = "Row ".$rowCount.": Notification Period is required.";
            continue; // Skip to the next row
        }

        if ($unitPrice == null || $unitPrice == ''){
            $errorArray[] = "Row ".$rowCount.": Unit Price is required.";
            continue; // Skip to the next row
        }

        if ($insert_stmt = $db->prepare("INSERT INTO stamping (type, company_branch, dealer, dealer_branch, customer_type, customers, branch, brand, machine_type, model, make_in, capacity, serial_no, assignTo, assignTo2, assignTo3, ownership_status, validate_by, cawangan, jenis_alat, machine_name, machine_location, machine_area, machine_serial_no, trade, no_daftar_baru, pin_keselamatan, siri_keselamatan, seal_no_baru, pegawai_contact, include_cert, cert_no, borang_d, borang_e, borang_e_date, invoice_no, invoice_payment_type, invoice_payment_ref, notification_period, cash_bill, stamping_date, due_date, pic, customer_pic, quotation_no, quotation_date, purchase_no, purchase_date, remarks, internal_remark, validator_invoice, unit_price, cert_price, total_amount, sst, subtotal_sst_amt, rebate, rebate_amount, subtotal_amount, log, products, stamping_type, labour_charge, stampfee_labourcharge, int_round_up, total_charges) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss', 
                $type, $companyBranch, $dealer, $dealerBranch, $customerType, $customers, $branch, $brands, $machineType, $models, $makeIn, $capacity, $serialNo, $assignTo, $assignTo2, $assignTo3, $ownershipStatus, $validateBy, $cawangan, $jenisAlat, $machineName, $machineLocation, $machineArea, $machineSerialNo, $trade, $noDaftarBaru, $pinKeselamatan, $siriKeselamatan, $sealNoBaru, $pegawaiContact, $includeCert, $certNo, $borangD, $borangE, $borangEDate, $invoiceNo, $invoicePaymentType, $invoicePaymentRef, $notificationPeriod, $cashBill, $stampingDate, $dueDate, $pic, $customerPic, $quotationNo, $quotationDate, $purchaseNo, $purchaseDate, $remarks, $internalRemark, $validatorInvoice, $unitPrice, $certPrice, $totalAmount, $sst, $subtotalSstAmount, $rebate, $rebateAmount, $subtotalAmount, $log, $products, $stampingType, $labourCharge, $stampfeeLabelCharge, $intRoundUp, $totalCharges);
            $insert_stmt->execute();
            $stampId = $insert_stmt->insert_id;
            $insert_stmt->close();

            // Insert into stamping_ext table
            if (str_contains($jenisAlat, 'ATP (MOTORCAR)')) { // ATP (MOTORCAR)
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
            } else if (str_contains($jenisAlat, 'ATP')) { // ATP
                $jenis_penunjuk = !empty($row['JenisPenunjuk']) ? $row['JenisPenunjuk'] : null;
            } else if (str_contains($jenisAlat, 'ATN')) { // ATN
                $alat_type = !empty($row['JenisAlatType']) ? $row['JenisAlatType'] : null;
                $bentuk_dulang = !empty($row['BentukDulang']) ? $row['BentukDulang'] : null;
            } else if (str_contains($jenisAlat, 'ATE')) { // ATE
                $class = !empty($row['Class']) ? $row['Class'] : null;
            } else if (str_contains($jenisAlat, 'SLL')) { // SLL
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
            } else if (str_contains($jenisAlat, 'BTU')) { // BTU
                $batu_ujian = !empty($row['BatuUjian']) ? $row['BatuUjian'] : null;
                $batu_ujian_lain = !empty($row['BatuUjianLain']) ? $row['BatuUjianLain'] : null;
                $penandaan_batu_ujian = !empty($row['PenandaanPadaBatuUjian']) ? $row['PenandaanPadaBatuUjian'] : null;
            } else if (str_contains($jenisAlat, 'SIA')) { // SIA
                $nilai_jangka = !empty($row['NilaiJangkaMaksima']) ? $row['NilaiJangkaMaksima'] : null;
                $nilai_jangka_other = !empty($row['NilaiJangkaMaksimaOther']) ? $row['NilaiJangkaMaksimaOther'] : null;
                $diperbuat_daripada = !empty($row['DiperbuatDaripada']) ? $row['DiperbuatDaripada'] : null;
                $diperbuat_daripada_other = !empty($row['DiperbuatDaripadaOther']) ? $row['DiperbuatDaripadaOther'] : null;
            } else if (str_contains($jenisAlat, 'BAP')) { // BAP
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
            } else if (str_contains($jenisAlat, 'SIC')) { // SIC
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

        $rowCount++;
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
