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
        1,4,2,5,6,14,7,10,17,23,12,18,11
    ];

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
                $branch = $row['branch'];
                $address1 = '';
                $address2 = '';
                $address3 = '';
                $address4 = '';
                $pic = '';
                $pic_phone = '';

                if(isset($branch) && $branch != '' ){
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
                $reseller_branch = $row['dealer_branch'];
                $reseller_address1 = '';
                $reseller_address2 = '';
                $reseller_address3 = '';
                $reseller_address4 = '';
                $reseller_pic = '';
                $reseller_pic_phone = '';

                if(isset($reseller_branch) && $reseller_branch != ''){
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
                    $message['borang_e'] = $row['borang_e'] ?? '';
                    $message['invoice_no'] = $row['invoice_no'] ?? '';
                    $message['cash_bill'] = $row['cash_bill'] ?? '';
                    $message['stamping_date'] = $row['stamping_date'] != null ? convertDatetimeToDate($row['stamping_date']) : '';
                    $message['last_year_stamping_date'] = $row['last_year_stamping_date'] != null ? convertDatetimeToDate($row['last_year_stamping_date']) : '';
                    $message['due_date'] = $row['due_date'] != null ? convertDatetimeToDate($row['due_date']) : '';
                    $message['customer_pic'] = $row['customer_pic'];
                    $message['quotation_no'] = $row['quotation_no'] ?? '';
                    $message['quotation_date'] = $row['quotation_date'] != null ? convertDatetimeToDate($row['quotation_date']) : '';
                    $message['purchase_no'] = $row['purchase_no'] ?? '';
                    $message['purchase_date'] = $row['purchase_date'] != null ? convertDatetimeToDate($row['purchase_date']) : '';
                    $message['remarks'] = $row['remarks'];
                    $message['log'] = json_decode($row['log'], true);
                    $message['unit_price'] = $row['unit_price'];
                    $message['cert_price'] = $row['cert_price'];
                    $message['total_amount'] = $row['total_amount'];
                    $message['sst'] = $row['sst'];
                    $message['subtotal_amount'] = $row['subtotal_amount'];
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
                                    $message['platform_country'] = searchCountryById($row2['platform_country'], $db) ?? '';
                                    $message['platform_type'] = $row2['platform_type'];
                                    $message['size'] = $row2['size'] ?? '';
                                    $message['jenis_pelantar'] = $row2['jenis_pelantar'] ?? '';
                                    $message['jenis_penunjuk'] = $row2['jenis_penunjuk'] ?? '';
                                    $message['alat_type'] = $row2['alat_type'] ?? '';
                                    $message['bentuk_dulang'] = $row2['bentuk_dulang'] ?? '';
                                    $message['batu_ujian'] = $row2['batu_ujian'] ?? '';
                                    $message['batu_ujian_lain'] = $row2['batu_ujian_lain'] ?? '';
                                    $message['class'] = $row2['class'] ?? '';
                                    $message['questions'] = json_decode($row2['questions'], true);
                                    $message['nilais'] = json_decode($row2['nilais'], true);
                                    $message['steelyard'] = $row2['steelyard'] ?? '';
                                    $message['bilangan_kaunterpois'] = $row2['bilangan_kaunterpois'] ?? '';
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

                                }
                            }
                        }
                    }
                }else{
                    $message['id'] = $row['id'];
                    $message['type'] = $row['type'];
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
                    $message['invoice_no'] = $row['invoice_no'];
                    $message['cash_bill'] = $row['cash_bill'];
                    $message['stamping_date'] = $row['stamping_date'];
                    $message['last_year_stamping_date'] = $row['last_year_stamping_date'];
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
                                }
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