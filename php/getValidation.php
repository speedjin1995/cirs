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
                $branch = $row['branch'];
                $branchQuery = "SELECT * FROM branches WHERE id = $branch";
                $branchDetail = mysqli_query($db, $branchQuery);
                $branchRow = mysqli_fetch_assoc($branchDetail);
              
                $address1 = null;
                $address2 = null;
                $address3 = null;
                $address4 = null;
                $pic = null;
                $pic_phone = null;
              
                if(!empty($branchRow)){
                  $address1 = $branchRow['address'];
                  $address2 = $branchRow['address2'];
                  $address3 = $branchRow['address3'];
                  $address4 = $branchRow['address4'];
                  $pic = $branchRow['pic'];
                  $pic_phone = $branchRow['pic_contact'];
                }

                $validationDate = '';
                if(isset($row['validation_date']) && $row['validation_date'] != ''){
                    $validationDate = $row['validation_date'];
                    $validationDate = DateTime::createFromFormat('Y-m-d', $validationDate)->format('d/m/Y');
                }

                if($format == 'EXPANDABLE'){
                    $message['id'] = $row['id'];
                    $message['type'] = $row['type'];
                    $message['dealer'] = $row['dealer'];
                    $message['dealer_branch'] = $row['dealer_branch'];
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
                    $message['brand'] = $row['brand'] != null ? searchBrandNameById($row['brand'], $db) : '';
                    $message['model'] = $row['model'] != null ? searchModelNameById($row['model'], $db) : '';
                    $message['capacity'] = $row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '';
                    $message['size'] = $row['size'] != null ? searchSizeNameById($row['size'], $db) : '';
                    $message['lastCalibrationDate'] = $row['last_calibration_date'];
                    $message['expiredCalibrationDate'] = $row['expired_calibration_date'];
                    $message['certFilePath'] = $row['cert_file_path'];
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
                    $message['certFilePath'] = $row['cert_file_path'];
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