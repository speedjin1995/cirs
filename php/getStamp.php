<?php
require_once "db_connect.php";
require_once 'requires/lookup.php';

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
    $format = 'MODAL';

    if (isset($_POST['format']) && $_POST['format'] != ''){
        $format = $_POST['format'];
    }

    $stampExtArray = [
        1,4,2,5,6,14,7,10,17,23,12,18,11,13,26
    ];

    if ($update_stmt = $db->prepare("SELECT * FROM stamping WHERE id=?")) {
        $update_stmt->bind_param('s', $id);
        
        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            $update_stmt->close();
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
                        if(isset($branchRow['pic']) && $branchRow['pic'] != ''){
                            $pic = $branchRow['pic'];
                        }
                        if(isset($branchRow['pic_contact']) && $branchRow['pic_contact'] != ''){
                            $pic_phone = $branchRow['pic_contact'];
                        }
                    }
                }
                
                $reseller_branch = null;
                $reseller_address1 = null;
                $reseller_address2 = null;
                $reseller_address3 = null;
                $reseller_address4 = null;
                $reseller_pic = null;
                $reseller_pic_phone = null;

                if($row['dealer_branch'] != null && $row['dealer_branch'] != ''){
                    $reseller_branch = $row['dealer_branch'];
                    $resellerQuery = "SELECT * FROM reseller_branches WHERE id = $reseller_branch";
                    $resellerDetail = mysqli_query($db, $resellerQuery);
                    $resellerRow = mysqli_fetch_assoc($resellerDetail);
                    
                    if(!empty($resellerRow)){
                        $reseller_address1 = $resellerRow['address'];
                        $reseller_address2 = $resellerRow['address2'];
                        $reseller_address3 = $resellerRow['address3'];
                        $reseller_address4 = $resellerRow['address4'];
                        $reseller_pic = $resellerRow['pic'];
                        $reseller_pic_phone = $resellerRow['pic_contact'];
                    }
                }

                $capacityId = $row['capacity'];
                $capacityType = '';
                $capacityName = '';
                if (isset($row['capacity']) && $row['capacity'] != ''){
                    $capacityQuery = "SELECT * FROM capacity WHERE id = $capacityId";
                    $capacityDetail = mysqli_query($db, $capacityQuery);
                    $capacityRow = mysqli_fetch_assoc($capacityDetail);
                    if(!empty($capacityRow)){
                        $capacityType = $capacityRow['range_type'];
                        $capacityName = $capacityRow['name'];
                    }
                }

                if($format == 'EXPANDABLE') {
                    $message['id'] = $row['id'];
                    $message['type'] = $row['type'];
                    $message['dealer'] = $row['dealer'] != null ? searchResellerNameById($row['dealer'], $db) : '';
                    $message['reseller_address1'] = $reseller_address1;
                    $message['reseller_address2'] = $reseller_address2;
                    $message['reseller_address3'] = $reseller_address3;
                    $message['reseller_address4'] = $reseller_address4;
                    $message['reseller_pic'] = $reseller_pic;
                    $message['reseller_pic_phone'] = $reseller_pic_phone;
                    $message['dealer_branch'] = $row['dealer_branch'];
                    $message['customer_type'] = $row['customer_type'];
                    $message['customers'] = $row['customers'] != null ? searchCustNameById($row['customers'], $db) : '';
                    $message['address1'] = $address1;
                    $message['address2'] = $address2;
                    $message['address3'] = $address3;
                    $message['address4'] = $address4;
                    $message['pic'] = $pic;
                    $message['pic_phone'] = $pic_phone;
                    $message['branch'] = $row['branch'];
                    $message['products'] = $row['products'];
                    $message['stampType'] = $row['stamping_type'];
                    $message['brand'] = $row['brand'] != null ? searchBrandNameById($row['brand'], $db) : '';
                    $message['machine_type'] = $row['machine_type'] != null ? searchMachineNameById($row['machine_type'], $db) : '';
                    $message['model'] = $row['model'] != null ? searchModelNameById($row['model'], $db) : '';
                    $message['make_in'] = searchCountryById($row['make_in'], $db) ?? '';
                    $message['capacity'] = $row['capacity'] != null ? $capacityName : '';
                    $message['capacity_range'] = $capacityType;
                    $message['assignTo'] = $row['assignTo'] != null ? searchStaffNameById($row['assignTo'], $db) : '';
                    $message['serial_no'] = $row['serial_no'];
                    $message['validate_by'] = searchValidatorNameById($row['validate_by'], $db) ?? '';
                    $message['jenis_alat'] = $row['jenis_alat'] != null ? searchJenisAlatNameByid($row['jenis_alat'], $db) : '';
                    $message['no_daftar_lama'] = $row['no_daftar_lama'] ?? '';
                    $message['no_daftar_baru'] = $row['no_daftar_baru'] ?? '';
                    $message['pin_keselamatan'] = $row['pin_keselamatan'];
                    $message['siri_keselamatan'] = $row['siri_keselamatan'] ?? '';
                    $message['include_cert'] = $row['include_cert'];
                    $message['borang_d'] = $row['borang_d'] ?? '';
                    $message['borang_e'] = $row['borang_e'] ?? '' . ($row['borang_e_date'] != null ? ' (' . convertDatetimeToDate($row['borang_e_date']) . ')' : '');
                    $message['invoice_no'] = $row['invoice_no'] ?? '';
                    $message['invoice_attachment'] = $row['invoice_attachment'] ?? '';
                    $message['invoice_filepath'] = searchFilePathById($row['invoice_attachment'], $db) ?? '';
                    $message['cash_bill'] = $row['cash_bill'] ?? '';
                    $message['stamping_date'] = $row['stamping_date'] != null ? convertDatetimeToDate($row['stamping_date']) : '';
                    $message['last_year_stamping_date'] = $row['last_year_stamping_date'] != null ? convertDatetimeToDate($row['last_year_stamping_date']) : '';
                    $message['due_date'] = $row['due_date'] != null ? convertDatetimeToDate($row['due_date']) : '';
                    $message['customer_pic'] = $row['customer_pic'];
                    $message['quotation_no'] = $row['quotation_no'] ?? '';
                    $message['quotation_attachment'] = $row['quotation_attachment'] ?? '';
                    $message['quotation_filepath'] = searchFilePathById($row['quotation_attachment'], $db) ?? '';
                    $message['quotation_date'] = $row['quotation_date'] != null ? convertDatetimeToDate($row['quotation_date']) : '';
                    $message['purchase_no'] = $row['purchase_no'] ?? '';
                    $message['purchase_date'] = $row['purchase_date'] != null ? convertDatetimeToDate($row['purchase_date']) : '';
                    $message['remarks'] = $row['remarks'] ?? '';
                    $message['log'] = json_decode($row['log'], true);
                    $message['validator_invoice'] = $row['validator_invoice'] ?? '';
                    $message['unit_price'] = 'RM ' . $row['unit_price'];
                    $message['cert_price'] = 'RM ' . $row['cert_price'];
                    $message['total_amount'] = 'RM ' . $row['total_amount'];
                    $message['sst'] = 'RM ' . $row['sst'];
                    $message['subtotal_sst_amt'] = 'RM ' . $row['subtotal_sst_amt'];
                    $message['rebate'] = $row['rebate'] . '%';
                    $message['rebate_amount'] = 'RM ' . $row['rebate_amount'];
                    $message['subtotal_amount'] = 'RM ' . $row['subtotal_amount'];
                    $message['labour_charge'] = 'RM ' . $row['labour_charge'];
                    $message['stampfee_labourcharge'] = 'RM ' . $row['stampfee_labourcharge'];
                    $message['int_round_up'] = $row['int_round_up'];
                    $message['total_charges'] = 'RM ' . $row['total_charges'];
                    $message['status'] = $row['status'];
                    $message['existing_id'] = $row['existing_id'];

                    # Create by & Update By
                    $createBy = '';
                    $modifiedBy = '';

                    $logQuery = "(SELECT * FROM stamping_log WHERE item_id = $id ORDER BY id ASC LIMIT 1) UNION (SELECT * FROM stamping_log WHERE item_id = $id ORDER BY id DESC LIMIT 1)";
                    $logDetail = mysqli_query($db, $logQuery);
                    
                    while($logRow = mysqli_fetch_assoc($logDetail)) {
                        $date = new DateTime($logRow['date']);
                        $formattedDate = $date->format("d/m/Y - h:i:sA");
                        if ($logRow['action'] == "INSERT"){
                            $createBy = searchStaffNameById($logRow['user_id'], $db). ' ' . $formattedDate;
                        }else{
                            $modifiedBy = searchStaffNameById($logRow['user_id'], $db). ' ' . $formattedDate;
                        }
                    }

                    $message['create_by'] = $createBy;
                    $message['modified_by'] = $modifiedBy;

                    if(($row['validate_by'] == '10' || $row['validate_by'] == '9') && in_array($row['jenis_alat'], $stampExtArray)){
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
                                    $message['platform_country'] = searchCountryById($row2['platform_country'], $db) ?? '';
                                    $message['platform_type'] = $row2['platform_type'];
                                    $message['size'] = searchSizeNameById($row2['size'], $db) ?? '';
                                    $message['jenis_pelantar'] = $row2['jenis_pelantar'] ?? '';
                                    $message['jenis_penunjuk'] = $row2['jenis_penunjuk'] ?? '';
                                    $message['load_cell_country'] = searchCountryById($row2['load_cell_country'], $db) ?? '';
                                    $message['load_cell_no'] = $row2['load_cell_no'] ?? '';
                                    $message['alat_type'] = $row2['alat_type'] ?? '';
                                    $message['bentuk_dulang'] = $row2['bentuk_dulang'] ?? '';
                                    $message['penandaan_batu_ujian'] = $row2['penandaan_batu_ujian'] ?? '';
                                    $message['batu_ujian'] = $row2['batu_ujian'] ?? '';
                                    $message['batu_ujian_lain'] = $row2['batu_ujian_lain'] ?? '';
                                    $message['class'] = $row2['class'] ?? '';
                                    $message['questions'] = json_decode($row2['questions'], true);
                                    $message['nilais'] = json_decode($row2['nilais'], true);
                                    $message['steelyard'] = $row2['steelyard'] ?? '';
                                    $message['bilangan_kaunterpois'] = $row2['bilangan_kaunterpois'] ?? '';
                                    $message['other_info'] = $row2['other_info'] ?? '';
                                    $message['nilai_jangka'] = $row2['nilai_jangka'] ?? '';
                                    $message['nilai_jangka_other'] = $row2['nilai_jangka_other'] ?? '';
                                    $message['diperbuat_daripada'] = $row2['diperbuat_daripada'] ?? '';
                                    $message['diperbuat_daripada_other'] = $row2['diperbuat_daripada_other'] ?? '';
                                    $message['pam_no'] = $row2['pam_no'] ?? '';
                                    $message['kelulusan_bentuk'] = $row2['kelulusan_bentuk'] ?? '';
                                    $message['kadar_pengaliran'] = $row2['kadar_pengaliran'] ?? '';
                                    $message['bentuk_penunjuk'] = $row2['bentuk_penunjuk'] ?? '';
                                    $message['jenama'] = $row2['jenama'] ?? '';
                                    $message['jenama_other'] = $row2['jenama_other'] ?? '';
                                    $message['nilai_jangkaan_maksimum'] = $row2['nilai_jangkaan_maksimum'] ?? '';
                                    $message['bahan_pembuat'] = $row2['bahan_pembuat'] ?? '';
                                    $message['bahan_pembuat_other'] = $row2['bahan_pembuat_other'] ?? '';

                                    if ($message['jenis_alat'] == 'BTU - (BOX)'){
                                        $btuBox = [];
                                        foreach (json_decode($row2['btu_box_info'], true) as $btu) {
                                            $penandaanBatuUjian = searchCapacityNameById($btu['penandaanBatuUjian'], $db);
    
                                            $btuBox[] = [
                                                "no" => $btu["no"],
                                                "batuUjian" => $btu["batuUjian"],
                                                "batuUjianLain" => $btu["batuUjianLain"],
                                                "penandaanBatuUjian" => $penandaanBatuUjian,
                                                "batuDaftarLama" => $btu["batuDaftarLama"],
                                                "batuDaftarBaru" => $btu["batuDaftarBaru"],
                                                "batuNoSiriPelekatKeselamatan" => $btu["batuNoSiriPelekatKeselamatan"],
                                                "batuBorangD" => $btu["batuBorangD"],
                                                "batuBorangE" => $btu["batuBorangE"],
                                            ];
                                        }
                                        $message['btu_box_info'] = $btuBox;
                                    }else if ($message['jenis_alat'] == 'ATK'){
                                        $loadCell = [];

                                        if ($row2['load_cells_info'] != null && $row2['load_cells_info'] != '') {
                                            foreach (json_decode($row2['load_cells_info'], true) as $atk) {
                                                $loadCells = searchLoadCellById($atk['loadCells'], $db);
                                                $loadCellCapacity = searchCapacityNameById($atk['loadCellCapacity'], $db);

                                                $loadCell[] = [
                                                "no" => $atk["no"],
                                                    "loadCells" => $loadCells,
                                                    "loadCellBrand" => $atk["loadCellBrand"],
                                                    "loadCellModel" => $atk["loadCellModel"],
                                                    "loadCellCapacity" => $loadCellCapacity,
                                                    "loadCellSerial" => $atk["loadCellSerial"]
                                                ];
                                            }
                                        }

                                        $message['load_cells_info'] = $loadCell;
                                    }
                                }
                            }
                            $update_stmt2->close();
                        }
                    }
                }else{
                    $message['id'] = $row['id'];
                    $message['type'] = $row['type'];
                    $message['company_branch'] = $row['company_branch'];
                    $message['dealer'] = $row['dealer'];
                    $message['dealer_branch'] = $row['dealer_branch'];
                    $message['customer_type'] = $row['customer_type'];
                    $message['customers'] = $row['customers'];
                    $message['branch'] = $row['branch'];
                    $message['products'] = $row['products'];
                    $message['stampType'] = $row['stamping_type'];
                    $message['brand'] = $row['brand'];
                    $message['machine_type'] = $row['machine_type'];
                    $message['model'] = $row['model'];
                    $message['make_in'] = $row['make_in'];
                    $message['capacity'] = $row['capacity'];
                    $message['capacity_range'] = $capacityType;
                    $message['assignTo'] = $row['assignTo'];
                    $message['serial_no'] = $row['serial_no'];
                    $message['validate_by'] = $row['validate_by'];
                    $message['cawangan'] = $row['cawangan'];
                    $message['jenis_alat'] = $row['jenis_alat'];
                    $message['trade'] = $row['trade'];
                    $message['no_daftar_lama'] = $row['no_daftar_lama'] ?? '';
                    $message['no_daftar_baru'] = $row['no_daftar_baru'] ?? '';
                    $message['pin_keselamatan'] = $row['pin_keselamatan'];
                    $message['siri_keselamatan'] = $row['siri_keselamatan'];
                    $message['include_cert'] = $row['include_cert'];
                    $message['borang_d'] = $row['borang_d'];
                    $message['borang_e'] = $row['borang_e'];
                    $message['borang_e_date'] = $row['borang_e_date'];
                    $message['notification_period'] = $row['notification_period'];
                    $message['invoice_no'] = $row['invoice_no'];
                    $message['invoice_attachment'] = $row['invoice_attachment'] ?? '';
                    $message['invoice_filepath'] = searchFilePathById($row['invoice_attachment'], $db) ?? '';
                    $message['cash_bill'] = $row['cash_bill'];
                    $message['stamping_date'] = $row['stamping_date'];
                    $message['last_year_stamping_date'] = $row['last_year_stamping_date'];
                    $message['due_date'] = $row['due_date'];
                    $message['pic'] = $row['pic'];
                    $message['customer_pic'] = $row['customer_pic'];
                    $message['quotation_no'] = $row['quotation_no'];
                    $message['quotation_date'] = $row['quotation_date'];
                    $message['quotation_attachment'] = $row['quotation_attachment'] ?? '';
                    $message['quotation_filepath'] = searchFilePathById($row['quotation_attachment'], $db) ?? '';
                    $message['purchase_no'] = $row['purchase_no'];
                    $message['purchase_date'] = $row['purchase_date'];
                    $message['remarks'] = $row['remarks'] ?? '';
                    $message['log'] = json_decode($row['log'], true);
                    $message['validator_invoice'] = $row['validator_invoice'] ?? '';
                    $message['unit_price'] = $row['unit_price'];
                    $message['cert_price'] = $row['cert_price'];
                    $message['total_amount'] = $row['total_amount'];
                    $message['sst'] = $row['sst'];
                    $message['subtotal_sst_amt'] = $row['subtotal_sst_amt'];
                    $message['rebate'] = $row['rebate'];
                    $message['rebate_amount'] = $row['rebate_amount'];
                    $message['subtotal_amount'] = $row['subtotal_amount'];
                    $message['labour_charge'] = $row['labour_charge'];
                    $message['stampfee_labourcharge'] = $row['stampfee_labourcharge'];
                    $message['int_round_up'] = $row['int_round_up'];
                    $message['total_charges'] = $row['total_charges'];
                    $message['status'] = $row['status'];
                    $message['existing_id'] = $row['existing_id'];

                    if(($row['validate_by'] == '10' || $row['validate_by'] == '9') && in_array($row['jenis_alat'], $stampExtArray)){
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
                                    $message['jenis_penunjuk'] = $row2['jenis_penunjuk'] ?? '';
                                    $message['alat_type'] = $row2['alat_type'] ?? '';
                                    $message['bentuk_dulang'] = $row2['bentuk_dulang'] ?? '';
                                    $message['class'] = $row2['class'] ?? '';
                                    $message['penandaan_batu_ujian'] = $row2['penandaan_batu_ujian'] ?? '';
                                    $message['batu_ujian'] = $row2['batu_ujian'] ?? '';
                                    $message['batu_ujian_lain'] = $row2['batu_ujian_lain'] ?? '';
                                    $message['questions'] = json_decode($row2['questions'], true);
                                    $message['steelyard'] = $row2['steelyard'] ?? '';
                                    $message['bilangan_kaunterpois'] = $row2['bilangan_kaunterpois'] ?? '';
                                    $message['nilais'] = json_decode($row2['nilais'], true);
                                    $message['other_info'] = $row2['other_info'] ?? '';
                                    $message['load_cell_country'] = $row2['load_cell_country'] ?? '';
                                    $message['load_cell_no'] = $row2['load_cell_no'] ?? '';
                                    $message['load_cells_info'] = json_decode($row2['load_cells_info'], true);
                                    $message['nilai_jangka'] = $row2['nilai_jangka'] ?? '';
                                    $message['nilai_jangka_other'] = $row2['nilai_jangka_other'] ?? '';
                                    $message['diperbuat_daripada'] = $row2['diperbuat_daripada'] ?? '';
                                    $message['diperbuat_daripada_other'] = $row2['diperbuat_daripada_other'] ?? '';
                                    $message['pam_no'] = $row2['pam_no'] ?? '';
                                    $message['kelulusan_bentuk'] = $row2['kelulusan_bentuk'] ?? '';
                                    $message['kadar_pengaliran'] = $row2['kadar_pengaliran'] ?? '';
                                    $message['bentuk_penunjuk'] = $row2['bentuk_penunjuk'] ?? '';
                                    $message['jenama'] = $row2['jenama'] ?? '';
                                    $message['jenama_other'] = $row2['jenama_other'] ?? '';
                                    $message['nilai_jangkaan_maksimum'] = $row2['nilai_jangkaan_maksimum'] ?? '';
                                    $message['bahan_pembuat'] = $row2['bahan_pembuat'] ?? '';
                                    $message['bahan_pembuat_other'] = $row2['bahan_pembuat_other'] ?? '';
                                    $message['btu_box_info'] = json_decode($row2['btu_box_info'], true);
                                }
                            }
                            $update_stmt2->close();
                        }
                    }
                }
            }
            
            $update_stmt->close();
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
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