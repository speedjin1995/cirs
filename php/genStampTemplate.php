<?php
require_once 'db_connect.php';

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

                $headers = ['Type','Company Branch','Dealer','Dealer Branch','Customer Type','Customers','Brand','Machine Type','Model','Make In','Capacity','Serial No','Assign To','Assign To 2','Assign To 3','Ownership Status','Validator Lama','Validate By','Cawangan','Jenis Alat','Machine Name','Machine Location','Machine Area','Machine Serial No','Trade','No Daftar Lama','No Daftar Baru','Pin Keselamatan','Siri Keselamatan','Include Cert','Borang D','Borang E','Borang E Date','Invoice No','Invoice Payment Type','Invoice Payment Ref','Notification Period','Cash Bill','Stamping Date','Last Year Stamping Date','Due Date','PIC','Customer PIC','Quotation No','Quotation Date','Purchase No','Purchase Date','Remarks','Internal Remark','Validator Invoice','Unit Price','Cert Price','Total Amount','SST','Subtotal SST Amount','Rebate','Rebate Amount','Subtotal Amount','Log','Products','Stamping Type','Branch','Labour Charge','Stamp Fee Labour Charge','Int Round Up','Total Charges','Seal No Lama','Seal No Baru','Pegawai Contact','Cert No'];

                $excelData = implode("\t", array_values($headers)) . "\n";

                $values = [$record['type'], $record['company_branch'],$record['dealer'], $record['dealer_branch'], $record['customer_type'], $record['customers'],
                $record['brand'], $record['machine_type'], $record['model'], $record['make_in'], $record['capacity'], $record['serial_no'], $record['assignTo'], $record['assignTo2'], $record['assignTo3'], $record['ownership_status'], $record['validator_lama'], $record['validate_by'],
                $record['cawangan'], $record['jenis_alat'], $record['machine_name'], $record['machine_location'], $record['machine_area'], $record['machine_serial_no'], $record['trade'], $record['no_daftar_lama'], $record['no_daftar_baru'], $record['pin_keselamatan'], $record['siri_keselamatan'], $record['include_cert'], $record['borang_d'], $record['borang_e'], $record['borang_e_date'], $record['invoice_no'], $record['invoice_payment_type'], $record['invoice_payment_ref'], $record['notification_period'], $record['cash_bill'], $record['stamping_date'], $record['last_year_stamping_date'], $record['due_date'], $record['pic'], $record['customer_pic'], $record['quotation_no'], $record['quotation_date'], $record['purchase_no'], $record['purchase_date'], $record['remarks'], $record['internal_remark'], $record['validator_invoice'], $record['unit_price'], $record['cert_price'], $record['total_amount'], $record['sst'], $record['subtotal_sst_amt'], $record['rebate'], $record['rebate_amount'], $record['subtotal_amount'],$record['log'],$record['products'],$record['stamping_type'],$record['branch'],$record['labour_charge'],$record['stampfee_labourcharge'],$record['int_round_up'],$record['total_charges'],$record['seal_no_lama'],$record['seal_no_baru'],$record['pegawai_contact'],$record['cert_no']];

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