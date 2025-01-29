<?php
require_once "db_connect.php";
require_once 'requires/lookup.php';

session_start();

if(isset($_POST['validationId'])){
	$id = filter_input(INPUT_POST, 'validationId', FILTER_SANITIZE_STRING); 
    $format = 'MODAL';

    if (isset($_POST['format']) && $_POST['format'] != ''){
        $format = $_POST['format'];
    }

    if ($update_stmt = $db->prepare("SELECT a.*, b.standard_avg_temp, b.relative_humidity ,b.unit, b.variance, b.test_1, b.test_2, b.test_3, b.test_4, b.test_5, b.test_6, b.test_7, b.test_8, b.test_9, b.test_10 FROM inhouse_validations a LEFT JOIN standard b ON a.capacity = b.capacity and b.deleted='0' WHERE a.id=?")) {
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
                      $pic = $branchRow['pic'];
                      $pic_phone = $branchRow['pic_contact'];
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

                if($format == 'EXPANDABLE'){
                    $message['id'] = $row['id'];
                    $message['type'] = $row['type'] ?? '';
                    $message['dealer'] = $row['dealer'] != null ? searchResellerNameById($row['dealer'], $db) : '';
                    $message['reseller_address1'] = $reseller_address1;
                    $message['reseller_address2'] = $reseller_address2;
                    $message['reseller_address3'] = $reseller_address3;
                    $message['reseller_address4'] = $reseller_address4;
                    $message['reseller_pic'] = $reseller_pic;
                    $message['reseller_pic_phone'] = $reseller_pic_phone;
                    $message['dealer_branch'] = $row['dealer_branch'] ?? '';
                    $message['validate_by'] = $row['validate_by'] ?? '';
                    $message['customer_type'] = $row['customer_type'] ?? ''; 
                    $message['customer'] = $row['customer'] != null ? searchCustNameById($row['customer'], $db) : '';
                    $message['branch'] = $row['branch'] ?? '';
                    $message['address1'] = $address1;
                    $message['address2'] = $address2;
                    $message['address3'] = $address3;
                    $message['address4'] = $address4;
                    $message['pic'] = $pic;
                    $message['pic_phone'] = $pic_phone;
                    $message['auto_form_no'] = $row['auto_form_no'] ?? '';
                    $message['machines'] = $row['machines'] != null ? searchMachineNameById($row['machines'], $db) : '';
                    $message['alat'] = $row['jenis_alat'] != null ? searchAlatNameById($row['jenis_alat'], $db) : '';
                    $message['unit_serial_no'] = $row['unit_serial_no'];
                    $message['manufacturing'] = $row['manufacturing'];
                    $message['brand'] = $row['brand'] != null ? searchBrandNameById($row['brand'], $db) : '';
                    $message['model'] = $row['model'] != null ? searchModelNameById($row['model'], $db) : '';
                    $message['capacity'] = $row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '';
                    $message['size'] = $row['size'] != null ? searchSizeNameById($row['size'], $db) : '';
                    $message['calibrator'] = $row['calibrator'] != null ? searchStaffNameById($row['calibrator'], $db) : '';
                    $message['expired_date'] = $row['expired_date'] ?? '';
                    $message['auto_cert_no'] = $row['auto_cert_no'] ?? '';
                    $message['tests'] = ($row['tests'] != null) ? json_decode($row['tests'], true) : [];
                    $message['validation_date'] = $row['validation_date'] ?? '';
                    $message['status'] = $row['status'] ?? '';

                    //standard table
                    $message['capacityUnit'] = $row['unit'] != null ? searchUnitNameById($row['unit'], $db) : '';
                    $message['variance'] = $row['variance'] ?? '';
                    $message['standard_avg_temp'] = $row['standard_avg_temp'] ?? '';
                    $message['relative_humidity'] = $row['relative_humidity'] ?? '';
                    $message['test_1'] = $row['test_1'] ?? '';
                    $message['test_2'] = $row['test_2'] ?? '';
                    $message['test_3'] = $row['test_3'] ?? '';
                    $message['test_4'] = $row['test_4'] ?? '';
                    $message['test_5'] = $row['test_5'] ?? '';
                    $message['test_6'] = $row['test_6'] ?? '';
                    $message['test_7'] = $row['test_7'] ?? '';
                    $message['test_8'] = $row['test_8'] ?? '';
                    $message['test_9'] = $row['test_9'] ?? '';
                    $message['test_10'] = $row['test_10'] ?? '';
                }else{
                    $message['id'] = $row['id'];
                    $message['type'] = $row['type'];
                    $message['dealer'] = $row['dealer'];
                    $message['dealer_branch'] = $row['dealer_branch'];
                    $message['validate_by'] = $row['validate_by'];
                    $message['customer_type'] = $row['customer_type'];
                    $message['customer'] = $row['customer'];
                    $message['branch'] = $row['branch'];
                    $message['auto_form_no'] = $row['auto_form_no'];
                    $message['machines'] = $row['machines'];
                    $message['unit_serial_no'] = $row['unit_serial_no'];
                    $message['manufacturing'] = $row['manufacturing'];
                    $message['brand'] = $row['brand'];
                    $message['model'] = $row['model'];
                    $message['capacity'] = $row['capacity'];
                    $message['size'] = $row['size'];
                    $message['calibrator'] = $row['calibrator'];
                    $message['last_calibration_date'] = $row['last_calibration_date'];
                    $message['expired_date'] = $row['expired_date'];
                    $message['auto_cert_no'] = $row['auto_cert_no'];
                    $message['tests'] = ($row['tests'] != null) ? json_decode($row['tests'], true) : [];
                    $message['validation_date'] = $row['validation_date'];
                    $message['status'] = $row['status'];

                    //standard table
                    $message['capacityUnit'] = $row['unit'] != null ? searchUnitNameById($row['unit'], $db) : '';
                    $message['variance'] = $row['variance'] ?? '';
                    $message['standard_avg_temp'] = $row['standard_avg_temp'] ?? '';
                    $message['relative_humidity'] = $row['relative_humidity'] ?? '';
                    $message['test_1'] = $row['test_1'] ?? '';
                    $message['test_2'] = $row['test_2'] ?? '';
                    $message['test_3'] = $row['test_3'] ?? '';
                    $message['test_4'] = $row['test_4'] ?? '';
                    $message['test_5'] = $row['test_5'] ?? '';
                    $message['test_6'] = $row['test_6'] ?? '';
                    $message['test_7'] = $row['test_7'] ?? '';
                    $message['test_8'] = $row['test_8'] ?? '';
                    $message['test_9'] = $row['test_9'] ?? '';
                    $message['test_10'] = $row['test_10'] ?? '';
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