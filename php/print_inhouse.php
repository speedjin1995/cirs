<?php

require_once 'db_connect.php';
require_once 'requires/lookup.php';
 
if(isset($_POST['id'])){
    $id = $_POST['id'];
    $todayDate = date('d/m/Y');
    $todayDate2 = date('d M Y');
    $today = date("Y-m-d 00:00:00");

    $company_stmt = $db->prepare("SELECT * FROM companies");
    if($company_stmt) {
        $company_stmt->execute();
        $result2 = $company_stmt->get_result();
        while ($row2 = $result2->fetch_assoc()) {
            $certno_lesen = $row2['certno_lesen'];
            $bless_serahanno = $row2['bless_serahanno'];
            $failno = $row2['failno'];
            $person_incharge = $row2['person_incharge'];
            $nmims = json_decode($row2['nmim'], true);
            $latestRecord = end($nmims);
            $nmim = $latestRecord['nmimApprNo'];
        }
    }


    // $placeholders = implode(',', array_fill(0, count($arrayOfId), '?'));
    $select_stmt = $db->prepare("SELECT a.*, b.standard_avg_temp, b.relative_humidity, b.unit, b.variance FROM inhouse_validations a LEFT JOIN standard b ON a.capacity = b.capacity WHERE a.id=?");

    // Check if the statement is prepared successfully
    if ($select_stmt) {
        // Bind variables to the prepared statement
        $select_stmt->bind_param('s', $id);
        $select_stmt->execute();
        $result = $select_stmt->get_result();

        $num_records = $result->num_rows;
        $totalRecords = $num_records;
        $total_pages = ceil($num_records / 10);
        $recordsPerPage = 10;
        $startIndex = 0;
        $pages = 0;
        $message = '';

        $rows = array();
            $count = 1;
            // $validator = '2';

            while ($row = $result->fetch_assoc()) {
                $company = searchCustNameById($row['customer'],$db);
                $branch = $row['branch'];
                $autoFormNo = $row['auto_form_no'];
                $validationDate = formatDate($row['validation_date']);
                $dueDate =  new DateTime($row['validation_date']);
                $dueDate->modify('+1 year');
                $dueDate->modify('-1 day');
                $nextDueDate = $dueDate->format('d M Y');
                $calibrationDate = formatDate($row['last_calibration_date']);
                $expiredDate = formatDate($row['expired_date']);
                $machine = searchMachineNameById($row['machines'],$db);
                $model = searchModelNameById($row['model'],$db);
                $size = searchSizeNameById($row['size'],$db);
                $manufacturer = $row['manufacturing'];
                $serialNo = $row['unit_serial_no'];
                $autoFormNo = $row['auto_form_no'];
                $capacity = searchCapacityNameById($row['capacity'],$db);
                $calibrator = searchStaffNameById($row['calibrator'],$db);
                $tests = json_decode($row['tests'], true);
                $stdAvgTemp = $row['standard_avg_temp'];
                $relHumid = $row['relative_humidity'];
                $variance = $row['variance'];

                if(!empty($row['unit'])){
                    $unit = searchUnitNameById($row['unit'],$db);
                }else{
                    $unit_stmt = $db->prepare("SELECT b.units FROM capacity a JOIN units b ON a.units = b.id WHERE a.id = ?");
                    if($unit_stmt){
                        $unit_stmt->bind_param('s',$row['capacity']);
                        $unit_stmt->execute();
                        $unitResult = $unit_stmt->get_result();
                        $unitRow = $unitResult->fetch_assoc();
                        $unit = $unitRow['units'];
                    }
                }

                if(!empty($branch)){
                    $branch_stmt = $db->prepare("SELECT * FROM branches WHERE id=?");
                    if($branch_stmt){
                        $branch_stmt->bind_param('s',$branch);
                        $branch_stmt->execute();
                        $branchResult = $branch_stmt->get_result();

                        while($branchRow = $branchResult->fetch_assoc()){
                            $address1 = $branchRow['address'];
                            $address2 = $branchRow['address2'];
                            $address3 = $branchRow['address3'];
                            $address4 = $branchRow['address4'];
                            $pic = $branchRow['pic'];
                            $officeNo = $branchRow['office_no'];
                        }
                        
                    }
                }

                $count++;

                if($count > 10){
                    $count = 1;
                }
            }
            $message = '<!DOCTYPE html>
                        <html lang="en">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>DX Weighing | CIRS</title>

                            <link rel="icon" href="assets/logoSmall.png" type="image">
                            <!-- Font Awesome Icons -->
                            <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
                            <!-- IonIcons -->
                            <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
                            <!-- Theme style -->
                            <link rel="stylesheet" href="dist/css/adminlte.min.css">
                            <!-- Google Font: Source Sans Pro -->
                            <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
                            <!-- daterange picker -->
                            <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
                            <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
                            <!-- iCheck for checkboxes and radio inputs -->
                            
                            <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
                            <!-- Bootstrap Color Picker -->
                            <link rel="stylesheet" href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
                            <!-- Select2 -->
                            <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
                            <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
                            <!-- Bootstrap4 Duallistbox -->
                            <link rel="stylesheet" href="plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
                            <!-- Toastr -->
                            <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
                            <link rel="stylesheet" href="dist/css/adminlte.min.css?v=3.2.0">
                            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
                            
                            <style>
                                .dotted-line {
                                    border: none;        /* Remove default border */
                                    border-top: 1px dotted #000; /* Dotted line */
                                    width: 100%;        /* Full width */
                                    margin: 20px 0;    /* Spacing above and below */
                                }
                            </style>
                        </head>
                        <body>
                            <div class="container mt-4 mb-4">
                            <div class="header">
                                <img src="dist/img/inhouse-header.png" alt="DX Weighing Solution" width="100%" height="50%">
                            </div>';

                $message .= '<table class="mt-4 mb-4">
                            <tbody>
                                <tr style="visibility: hidden;">
                                    <th width="10%">column 1</th>
                                    <th width="25%">column 2</th>
                                    <th width="10%">column 3</th>
                                    <th width="10%">column 4</th>
                                </tr>
                                <tr>
                                    <td class="align-top"><b>To Company:</b></td>
                                    <td class="align-top"><b>' . $company . '</b></td>
                                    <td class="align-top"><b>Certificate No:</b></td>
                                    <td class="align-top">'. $autoFormNo .'</td>
                                </tr>
                                <tr>
                                    <td class="align-top"><b>Address:</b></td>
                                    <td class="align-top">
                                        <div class="row">
                                            <div class="col-12" id="address-line1">'. ($address1 ?? '').'</div>
                                            <div class="col-12" id="address-line2">'. ($address2 ?? '').'</div>
                                            <div class="col-12" id="address-line3">'. ($address3 ?? '').'</div>
                                            <div class="col-12" id="address-line4">'. ($address4 ?? '').'</div>
                                            <div class="col-12" id="contact">Tel: '. ($officeNo ?? '').'</div>
                                        </div>
                                    </td>
                                    <td class="align-top align-right">
                                        <div class="row">
                                            <div class="col-12">&nbsp;</div>
                                            <div class="col-12"><b>Date of Issue:</b></div>
                                            <div class="col-12"><b>Date of Calibration:</b></div>
                                            <div class="col-12"><b>Next Due Date:</b></div>
                                        </div>
                                    </td>
                                    <td class="align-top">
                                        <div class="row">
                                            <div class="col-12">&nbsp;</div>
                                            <div class="col-12" id="doi">'. $validationDate .'</div>
                                            <div class="col-12" id="doc">'. $calibrationDate .'</div>
                                            <div class="col-12" id="ndd" style="color:red">'. $expiredDate .'</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>';

            $message .= '<table class="table" style="table-layout: fixed;">
                        <tbody>
                            <tr style="border: 1px solid black;">
                                <td colspan="2" class="align-top" style="border: none;" width="50%">
                                    <div class="row">
                                        <div class="col-12" id="machines"><b>Instruments:</b> '. $machine .'</div>
                                        <div class="col-12" id="manufacturer"><b>Manufacturer:</b> '. $manufacturer.' </div>
                                        <div class="col-12" id="capacity"><b>Capacity:</b> '. $capacity .'</div>
                                    </div>
                                </td>
                                <td colspan="2" class="align-top" style="border: none;" width="50%">
                                    <div class="row">
                                        <div class="col-12" id="machines"><b>Model No:</b> '. $model .' </div>
                                        <div class="col-12" id="serialNo"><b>Serial No:</b> '. $serialNo .' </div>
                                        <div class="col-12" id="size"><b>Structure Size:</b> '. $size .' </div>
                                    </div>
                                </td>
                            </tr>
                            <tr style="border: 1px solid black;">
                                <td colspan="2" class="align-top" style="border: none;">
                                    <div class="row">
                                        <div class="col-12"><b>Instrument Condition When Received:</b> </div>
                                        <div class="col-12"><b>Instrument Condition When Returned:</b> </div>
                                    </div>
                                </td>
                                <td colspan="2" class="align-top" style="border: none;">
                                    <div class="row">
                                        <div class="col-12" id="machineCondReceived">Physically in good condition.</div>
                                        <div class="col-12" id="machineCondReturned">
                                            <ol class="pl-3">
                                                <li>Calibrated and test servicable.</li>
                                                <li>Calibration due date requested by customer.</li>
                                                <li>The user should be awae that there are a number of factors that may cause this instrument to drift out of calibration before the specified calibration interval has expired.</li>
                                            </ol>
                                            
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr style="border: 1px solid black;">
                                <td colspan="2" class="align-top" style="border: none;"><b>Average Temperature:</b> ('. $stdAvgTemp .')</td>
                                <td colspan="2" class="align-top" style="border: none;"><b>Average Relative Humidity:</b> ('. $relHumid .')</td>
                            </tr>
                            <tr style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;">
                                <th style="padding: .5rem; border: none; text-align: center;">Setting Value Of Standard</th>
                                <th style="padding: .5rem; border: none; text-align: center;">As Received Under Calibration</th>
                                <th style="padding: .5rem; border: none; text-align: center;">Variance +/- ('. $variance . " " . $unit .')</th>
                                <th style="padding: .5rem; border: none; text-align: center;">Reading After Adjustment</th>
                            </tr>';

                $testCount = count($tests);

                if($testCount > 0){
                    for ($i=0; $i < $testCount; $i++) { 
                        $tests = $tests[$i];
                        $countTests = count($tests);
                        for ($j=0; $j < $countTests; $j++) {
                            $item = $tests[$j]; 
                            $standardValue = $item['standardValue']; 
                            $calibrationReceived = $item['calibrationReceived'];
                            $variance = $item['variance'];
                            $afterAdjustReading = $item['afterAdjustReading'];
                            if ($j == 9){
                                $message .= '<tr style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; padding-bottom: 2%">
                                                <td style="border: none;text-align: center; padding: 0 0 3% 0;">'. $standardValue .' - '. $unit .'</td>
                                                <td style="border: none;text-align: center; padding: 0 0 3% 0;">'. $calibrationReceived .' - '. $unit .'</td>
                                                <td style="border: none;text-align: center; padding: 0 0 3% 0;">'. $variance .'</td>
                                                <td style="border: none;text-align: center; padding: 0 0 3% 0;">'. $afterAdjustReading .' - '. $unit .'</td>
                                            </tr>';
                            }else{
                                $message .= '<tr style="border-left: 1px solid black; border-right: 1px solid black;">
                                                <td style="border: none;text-align: center; padding:0;">'. $standardValue .' - '. $unit .'</td>
                                                <td style="border: none;text-align: center; padding:0;">'. $calibrationReceived .' - '. $unit .'</td>
                                                <td style="border: none;text-align: center; padding:0;">'. $variance .'</td>
                                                <td style="border: none;text-align: center; padding:0;">'. $afterAdjustReading .' - '. $unit .'</td>
                                            </tr>';
                            }
                            
                        }
                    }    
                }
                
            $message .= '<tr style="border: 1px solid black;padding-top:5%">
                            <td colspan="2" class="align-top" style="border: none;">
                                <div class="row">
                                    <div class="col-12" id="calibratedDt"><b>Date Calibrated:</b> '. $validationDate .'</div>
                                    <div class="col-12" id="calibratedBy"><b>Calibrated By:</b> '. $calibrator .'</div>
                                    <div class="col-12" id="standardUsedInstru"><b>Standard Used Instrument:</b> Standard.Test Weight</div>
                                </div>
                            </td>
                            <td colspan="2" class="align-top" style="border: none;">
                                <div class="row">
                                    <div class="col-12" id="nextDueDt" style="color:red"><b style="color:black">Next Due Date:</b> '. $nextDueDate .'</div>
                                    <div class="col-12" id="calibrationStickerNo"><b>Calibration Sticker No:</b> '. $autoFormNo .'</div>
                                    <div class="col-12" id="sirimTrace"><b>SIRIM Traceability: '. $nmim.'</b></div>
                                </div>
                            </td>
                        </tr> 
                        </tbody>
                    </table>';               

            $message .= '<table class="mt-1 mb-4" width="100%">
                            <tbody>
                                <tr style="visibility: hidden;">
                                    <th width="30%">column 1</th>
                                    <th width="45%">column 2</th>
                                    <th width="25%">column 3</th>
                                </tr>
                                <tr>
                                    <td class="align-top"><b>Licensing of Membaiki & Menjual</b></td>
                                    <td class="align-top">:&nbsp;&nbsp;&nbsp;'. $certno_lesen .'</td>
                                </tr>
                                <tr>
                                    <td class="align-top"><b>Weighing Licensing of KPDN</b></td>
                                    <td class="align-top">:&nbsp;&nbsp;&nbsp;'. $failno .'</td>
                                </tr>
                                <tr>
                                    <td class="align-top"><b>Raj.Transaksi</b></td>
                                    <td class="align-top">:&nbsp;&nbsp;&nbsp;'. $bless_serahanno .'</td>
                                    <td>
                                        <div class="text-center" style="width: 100%;">
                                            <hr class="dotted-line">
                                        </div>
                                        <div class="text-center" style="width: 100%;">
                                            <span><b>Approved Signature</b></span>
                                        </div>
                                        <div class="text-center"style="width: 100%;">
                                            <span>'. $person_incharge .'</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <span class="font-weight-bold" style="color: red;">The Uncertainties are for a confidence probability of approximately 95%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <span class="mt-5">This is to confirm that we have perfomed the Service & Calibration for the above Weighing Equipment with our Standard. Weights that has been certified by <b> METROLOGY CORPORATION MALAYSIAN SDN. BHD. & SIRIM SST Malaysia. </b></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>';
                            
                    // // Move to the next page
                    // $startIndex += $recordsPerPage;
                    // $pages++;

                    // // Add a page break if more records are available
                    // if ($startIndex < $totalRecords) {
                    //     $message .= '<div class="page-break"></div>';
                    // }  
    
        $message .= '</body></html>';

        // Fetch each row
        $select_stmt->close();

        // Return the results as JSON
        echo json_encode(array('status' => 'success', 'message' => $message));
    } 
    else {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Statement preparation failed"
            ));
    }
}
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}

function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('d M Y');
}


?>