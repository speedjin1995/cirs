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

    if ($update_stmt = $db->prepare("SELECT * FROM other_validations WHERE id=?")) {
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
                        $pic = $branchRow['pic'];
                        $pic_phone = $branchRow['pic_contact'];
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

                $validationDate = '';
                if(isset($row['validation_date']) && $row['validation_date'] != ''){
                    $validationDate = $row['validation_date'];
                    $validationDate = DateTime::createFromFormat('Y-m-d', $validationDate)->format('Y-m-d H:i:s');
                }

                if($format == 'EXPANDABLE'){
                    $message['id'] = $row['id'];
                    $message['type'] = $row['type'];
                    $message['dealer'] = $row['dealer'] != null ? searchResellerNameById($row['dealer'], $db) : '';
                    $message['reseller_address1'] = $reseller_address1;
                    $message['reseller_address2'] = $reseller_address2;
                    $message['reseller_address3'] = $reseller_address3;
                    $message['reseller_address4'] = $reseller_address4;
                    $message['reseller_pic'] = $reseller_pic;
                    $message['reseller_pic_phone'] = $reseller_pic_phone;
                    $message['validate_by'] = $row['validate_by'];
                    $message['customer_type'] = $row['customer_type'];
                    $message['customer'] = $row['customer'] != null ? searchCustNameById($row['customer'], $db) : '';
                    $message['address1'] = $address1;
                    $message['address2'] = $address2;
                    $message['address3'] = $address3;
                    $message['address4'] = $address4;
                    $message['pic'] = $pic;
                    $message['pic_phone'] = $pic_phone;
                    $message['branch'] = $row['branch'];
                    $message['auto_form_no'] = $row['auto_form_no'];
                    $message['machines'] = $row['machines'] != null ? searchMachineNameById($row['machines'], $db) : '';
                    $message['unit_serial_no'] = $row['unit_serial_no'];
                    $message['manufacturing'] = $row['manufacturing'];
                    $message['alat'] = $row['jenis_alat'] != null ? searchAlatNameById(($row['jenis_alat']), $db) : '';
                    $message['brand'] = $row['brand'] != null ? searchBrandNameById($row['brand'], $db) : ''; 
                    $message['model'] = $row['model'] != null ? searchModelNameById($row['model'], $db) : '';
                    $message['capacity'] = $row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '';
                    $message['size'] = $row['size'] != null ? searchSizeNameById($row['size'], $db) : '';
                    $message['lastCalibrationDate'] = $row['last_calibration_date'];
                    $message['expiredCalibrationDate'] = $row['expired_calibration_date'];
                    $message['certFilePath1'] = $row['cert_file_path1'] != null ? $row['cert_file_path1'] : '';
                    $message['certFilePath2'] = $row['cert_file_path2'] != null ? $row['cert_file_path2'] : '';
                    $message['certFilePath3'] = $row['cert_file_path3'] != null ? $row['cert_file_path3'] : '';
                    $message['certFilePath4'] = $row['cert_file_path4'] != null ? $row['cert_file_path4'] : '';
                    $message['certFilePath5'] = $row['cert_file_path5'] != null ? $row['cert_file_path5'] : '';
                    // $message['calibrations'] = ($row['calibrations'] != null) ? json_decode($row['calibrations'], true) : [];
                    $message['validation_date'] = $validationDate;
                    $message['status'] = $row['status'];
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
                    $message['lastCalibrationDate'] = $row['last_calibration_date'];
                    $message['expiredCalibrationDate'] = $row['expired_calibration_date'];
                    $message['certFilePath1'] = $row['cert_file_path1'] != null ? $row['cert_file_path1'] : '';
                    $message['certFilePath2'] = $row['cert_file_path2'] != null ? $row['cert_file_path2'] : '';
                    $message['certFilePath3'] = $row['cert_file_path3'] != null ? $row['cert_file_path3'] : '';
                    $message['certFilePath4'] = $row['cert_file_path4'] != null ? $row['cert_file_path4'] : '';
                    $message['certFilePath5'] = $row['cert_file_path5'] != null ? $row['cert_file_path5'] : '';
                    // $message['calibrations'] = ($row['calibrations'] != null) ? json_decode($row['calibrations'], true) : [];
                    $message['validation_date'] = $validationDate;
                    $message['status'] = $row['status'];
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