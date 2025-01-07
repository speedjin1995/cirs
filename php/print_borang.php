<?php

require_once 'db_connect.php';
require_once 'requires/lookup.php';
 
if(isset($_POST['id'], $_POST['driver'], $_POST['cawanganBorang'], $_POST['validatorBorang'])){
    $selectedIds = $_POST['id'];
    $arrayOfId = explode(",", $selectedIds);
    $driver = filter_input(INPUT_POST, 'driver', FILTER_SANITIZE_STRING);
    $cawangan = searchStateNameById(filter_input(INPUT_POST, 'cawanganBorang', FILTER_SANITIZE_STRING), $db);
    $validatorFilter = searchValidatorNameById(filter_input(INPUT_POST, 'validatorBorang', FILTER_SANITIZE_STRING), $db);
    $todayDate = date('d/m/Y');
    $todayDate2 = date('d M Y');
    $todayDate3 = date('d.m.Y');
    $today = date("Y-m-d 00:00:00");

    $placeholders = implode(',', array_fill(0, count($arrayOfId), '?'));

    $companyQuery = "SELECT * FROM companies WHERE id = '1'";
    $companyDetail = mysqli_query($db, $companyQuery);
    $companyRow = mysqli_fetch_assoc($companyDetail);

    if(!empty($companyRow)){
        $companyName = $companyRow['name'];
        $companyOldRoc = $companyRow['old_roc'];
        $companyAddress = $companyRow['address'];
        $companyTel = $companyRow['phone'];
        $companyFax = $companyRow['fax'];
        $companySignature = $companyRow['signature'];
    }

    $select_stmt = $db->prepare("SELECT * FROM stamping WHERE id IN ($placeholders)");

    // Check if the statement is prepared successfully
    if ($select_stmt) {
        // Bind variables to the prepared statement
        $types = str_repeat('i', count($arrayOfId)); // Assuming the IDs are integers
        $select_stmt->bind_param($types, ...$arrayOfId);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        $num_records = $result->num_rows;
        $totalRecords = $num_records;
        $total_pages = ceil($num_records / 7);
        $recordsPerPage = 7;
        $startIndex = 0;
        $pages = 0;
        $message = '';

        if($driver == '6'){
            $message = '<html>
            <head>
                <style>
                    @media print {
                        @page {
                            margin-left: 0.5in;
                            margin-right: 0.5in;
                            margin-top: 0.1in;
                            margin-bottom: 0.1in;
                        }
                        
                    } 
                            
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        
                    } 
                    
                    .table th, .table td {
                        padding: 0.70rem;
                        vertical-align: top;
                        border-top: 1px solid #dee2e6;
                        
                    } 
                    
                    .table-bordered {
                        border: 1px solid #000000;
                        
                    } 
                    
                    .table-bordered th, .table-bordered td {
                        border: 1px solid #000000;
                        font-family: sans-serif;
                        font-size: 12px;
                        
                    } 
                    
                    .row {
                        display: flex;
                        flex-wrap: wrap;
                        margin-top: 20px;
                        margin-right: -15px;
                        margin-left: -15px;
                        
                    } 
                    
                    .col-md-4{
                        position: relative;
                        width: 33.333333%;
                    }

                    .bottom-table {
                        position: fixed;
                        bottom: 0;
                        width: 100%;
                    }
                </style>
            </head>
            <body>
            <table class="table-bordered">
                <tbody>
                    <tr>
                        <th colspan="8" style="vertical-align: middle;">
                            JADUAL 6<br>AKTA TIMBANG DAN SUKAT 1972<br>PERATURAN-PERATURAN TIMBANG DAN SUKAT 1981<br>(PERATURAN 35)<br>DAFTAR TIMBANG , SUKAT DAN ALAT TIMBANG SUKAT YANG DIJUAL/DIBUAT
                        </th>
                    </tr>
                    <tr>
                        <th>DATE</th>
                        <th>ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS</th>
                        <th>CAPACITY</th>
                        <th>QUANTITY</th>
                        <th>REGISTER NO.</th>
                        <th>CERTIFICATE NO./ NO. SIRI PELEKAT KESELAMATAN</th>
                        <th>NAME OF PURCHASE</th>
                        <th>ADDRESS</th>
                    </tr>';

            while ($row = $result->fetch_assoc()) {
                $message .= '<tr>
                        <td>'.$todayDate2.'</td>
                        <td>'.searchBrandNameById($row['brand'], $db).'<br>'.searchModelNameById($row['model'], $db).'<br>'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                        <td>'.searchCapacityNameById($row['capacity'], $db).'</td>
                        <td>1</td>
                        <td>'.$row['no_daftar'].'</td>
                        <td>'.$row['siri_keselamatan'].'</td>
                        <td>'.searchCustNameById($row['customers'], $db).'</td>
                        <td>'.$row['address1'].' '.$row['address2'].' '.$row['address3'].'</td>
                    </tr>';
            }

            $message .= '</tbody></table>';
        }
        else if($driver == '7'){
            $message = '<html>
            <head>
                <style>
                    @media print {
                        @page {
                            margin-left: 0.5in;
                            margin-right: 0.5in;
                            margin-top: 0.1in;
                            margin-bottom: 0.1in;
                        }
                        
                    } 
                            
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        
                    } 
                    
                    .table th, .table td {
                        padding: 0.70rem;
                        vertical-align: top;
                        border-top: 1px solid #dee2e6;
                        
                    } 
                    
                    .table-bordered {
                        border: 1px solid #000000;
                        
                    } 
                    
                    .table-bordered th, .table-bordered td {
                        border: 1px solid #000000;
                        font-family: sans-serif;
                        font-size: 12px;
                        
                    } 
                    
                    .row {
                        display: flex;
                        flex-wrap: wrap;
                        margin-top: 20px;
                        margin-right: -15px;
                        margin-left: -15px;
                        
                    } 
                    
                    .col-md-4{
                        position: relative;
                        width: 33.333333%;
                    }

                    .bottom-table {
                        position: fixed;
                        bottom: 0;
                        width: 100%;
                    }
                </style>
            </head>
            <body>
            <table class="table-bordered">
                <tbody>
                    <tr>
                        <th colspan="11" style="vertical-align: middle;">
                            JADUAL 7<br>AKTA TIMBANG DAN SUKAT 1972<br>PERATURAN-PERATURAN TIMBANG DAN SUKAT 1981<br>(PERATURAN 35)<br>DAFTAR TIMBANG , SUKAT DAN ALAT TIMBANG SUKAT YANG DIJUAL/DIBUAT
                        </th>
                    </tr>
                    <tr>
                        <th>BRG E BIL NO.</th>
                        <th>DATE</th>
                        <th>ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS</th>
                        <th>CAPACITY</th>
                        <th>LIST NO. (STMP. NO.)</th>
                        <th>REGISTER NO. (BARU / LAMA)</th>
                        <th>DETAILS OF REPAIR</th>
                        <th>CERTIFICATE NO./ NO. SIRI PELEKAT KESELAMATAN</th>
                        <th>NAME OF PURCHASE</th>
                        <th>ADDRESS</th>
                        <th>FEE</th>
                    </tr>';

            while ($row = $result->fetch_assoc()) {
                $message .= '<tr>
                        <td></td>
                        <td>'.$todayDate2.'</td>
                        <td>'.searchBrandNameById($row['brand'], $db).'<br>'.searchModelNameById($row['model'], $db).'<br>'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                        <td>'.searchCapacityNameById($row['capacity'], $db).'</td>
                        <td>'.$row['pin_keselamatan'].'</td>
                        <td>'.$row['no_daftar'].'</td>
                        <td>SERVICE / STMP</td>
                        <td>'.$row['siri_keselamatan'].'</td>
                        <td>'.searchCustNameById($row['customers'], $db).'</td>
                        <td>'.$row['address1'].' '.$row['address2'].' '.$row['address3'].'</td>
                        <td>'.$row['unit_price'].'</td>
                    </tr>';
            }

            $message .= '</tbody></table>';
        }
        else{
            $rows = array();
            $totalAmt = 0;
            $sst = 0;
            $subTotalAmt = 0;
            $rowCount = count($rows);
            $count = 1;
            $indexCount = 1;
            $validator = '2';                
            $jenisAlatData = [];

            while ($row = $result->fetch_assoc()) {
                $validator = $row['validate_by'];
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

                $jenisAlat = searchAlatNameById($row['jenis_alat'], $db);
                $totalPrice = $row['total_amount'];

                // Initialize the data for this jenis_alat if it doesn't exist yet
                if (!isset($jenisAlatData[$jenisAlat])) {
                    $jenisAlatData[$jenisAlat] = [
                        'total_price' => 0,
                        'count' => 0
                    ];
                }

                // Increment the count for the current jenis_alat
                $jenisAlatData[$jenisAlat]['total_price'] += $totalPrice;
                $jenisAlatData[$jenisAlat]['count']++;

                $rows[] = '<tr style="height: 30px;">
                            <td style="padding-left: 0.5%">'.$indexCount.'</td>
                            <td style="padding-left: 0.5%">'.$jenisAlat.'</td>
                            <td style="padding-left: 0.5%">'.searchCapacityNameById($row['capacity'], $db).'</td>
                            <td style="padding-left: 0.5%">'.searchBrandNameById($row['brand'], $db).'<br>'.searchModelNameById($row['model'], $db).'</td>
                            <td style="padding-left: 0.5%">'.$row['serial_no'].'</td>
                            <td style="padding-left: 0.5%">'.searchCustNameById($row['customers'], $db).'<br>'.$address1.' '.$address2.' '.$address3.' '.$address4.'</td>
                            <td style="padding-left: 0.5%"></td>
                            <td style="padding-left: 0.5%">'.$row['no_daftar_lama'].'</td>
                            <td style="padding-left: 0.5%">'.$row['no_daftar_baru'].'</td>
                            <td style="padding-left: 0.5%">'.$row['siri_keselamatan'].'</td>';
                            
                            if ($row['cert_price'] != 0) {
                                $rows[$rowCount] .= '<td style="padding-left: 0.5%">RM '.number_format(floatval($row['unit_price']), 2, '.', '').'<br>RM '.number_format(floatval($row['cert_price']), 2, '.', '').'</td>';
                            } else {
                                $rows[$rowCount] .= '<td style="padding-left: 0.5%">RM '.number_format(floatval($row['unit_price']), 2, '.', '').'</td>';
                            }
                            
                $rows[$rowCount] .= '</tr>';

                $totalAmt += floatval($row['unit_price']) + floatval($row['cert_price']);
                
                $count++;
                $rowCount++;
                $indexCount++;

                if($count > 7){
                    $count = 1;
                }
            }
            
            $sst = $totalAmt * (8/100);
            $subTotalAmt = $totalAmt + $sst;
            
            if ($count <= 7 && count($rows) % 7 != 0) {
                $remainingRows = 7 - (count($rows) % 7);
                for ($i = 0; $i < $remainingRows; $i++) {
                    $rows[] = '<tr style="height: 30px;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>';
                }
            }

            $message = '<html>
                    <head>
                        <style>
                            @media print {
                                @page {
                                    margin-left: 0.5in;
                                    margin-right: 0.5in;
                                    margin-top: 0.1in;
                                    margin-bottom: 0.1in;
                                }
                                
                            } 
                                    
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                
                            } 
                            
                            .table th, .table td {
                                padding: 0.70rem;
                                vertical-align: top;
                                border-top: 1px solid #dee2e6;
                                
                            } 
                            
                            .table-bordered {
                                border: 1px solid #000000;
                                
                            } 
                            
                            .table-bordered th, .table-bordered td {
                                border: 1px solid #000000;
                                font-family: sans-serif;
                                font-size: 12px;
                                
                            } 
                            
                            .row {
                                display: flex;
                                flex-wrap: wrap;
                                margin-top: 20px;
                                margin-right: -15px;
                                margin-left: -15px;
                                
                            } 
                            
                            .col-md-4{
                                position: relative;
                                width: 33.333333%;
                            }
    
                            .bottom-table {
                                position: fixed;
                                bottom: 0;
                                width: 100%;
                            }
    
                            .page-break {
                                page-break-before: always;
                            }
                        </style>
                    </head>
                    <body>';
    
                    while ($startIndex < $num_records) {
                        $message .= '<table>
                            <tbody>
                                <tr>
                                    <td style="vertical-align: left;" width="50%">
                                        <p>NAMA SYARIKAT PEMILIK / PEMBAIK : <br>'.$companyName.' ('.$companyOldRoc.')<br>
                                        ALAMAT : '.$companyAddress.'<br>
                                        Tel. : '.$companyTel.'     Fax. : '.$companyFax.'</p><br>';
    
                                    $message .= '<p>Pengurus Cawangan : <span style="font-size: 14px; font-weight: bold;">' . $validatorFilter . ' ' . $cawangan . '</span><br>';
                                        
                                    if($validator == '2'){
                                        $message .= 'Metrology Corporation Malaysia Sdn. Bhd.</p>';
                                    }
                                    elseif($validator == '3'){
                                        $message .= 'De Metrology Corporation Malaysia Sdn. Bhd.</p>';
                                    }
                                        
                                    $message .= '</td>
                                    <td valign="top" align="center" width="20%">';
                                    if($validator == '2'){
                                        $message .= '<img src="https://cirs.syncweigh.com/assets/metrology.jpeg" width="50%" height="auto" />';
                                    }
                                    elseif($validator == '3'){
                                        $message .= '<img src="https://cirs.syncweigh.com/assets/DMCM.jpeg" width="50%" height="auto" />';
                                    }
                                        
                                    $message .= '</td>
                                    <td style="vertical-align: right;">
                                        <p>Penentusahan Dalam / Luar Pejabat</p>
                                        <p>Tarikh : '.$todayDate3.'</p>
                                        <table class="table-bordered">
                                            <tbody>
                                                <tr><th width="20%">Alat</th><th width="20%">Jum. Alat</th><th width="20%">Bayaran</th></tr>';
                                            
                                            $count = 0;
                                            $totalPrice = 0;
                                            foreach ($jenisAlatData as $key => $value) {
                                                $alatCount = floatval($value['count']);
                                                $alatTotalPrice = floatval($value['total_price']);
    
                                                $message .= '<tr><td style="padding-left: 1%; text-align: center;">'.$key.'</td><td style="padding-left: 1%; text-align: center;">'.$alatCount.'</td><td style="padding-left: 1%; text-align: center;">RM '.number_format($alatTotalPrice, 2, '.', '').'</td>';
                                                $count += $alatCount;
                                                $totalPrice += $alatTotalPrice;
                                            }
                                    $message .= '<tr><td style="padding-left: 1%; text-align: center;">Jumlah</td><td style="padding-left: 1%; text-align: center;">'.$count.'</td><td style="padding-left: 1%; text-align: center;">RM '. number_format(floatval($totalPrice), 2, '.', '') .'</td></tr></tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
    
                        <p style="text-align: center;">BUTIRAN SENARAI ALAT-ALAT TIMBANG DAN SUKAT UNTUK PENGUJIAN DAN PENENTUSAHAN</p>
                        <table class="table-bordered" style="border-left: none; border-bottom: none;">
                            <tbody>
                                <tr>
                                    <th>Bil.</th>
                                    <th>Jenis Alat</th>
                                    <th>Had Terima</th>
                                    <th>Jenama</th>
                                    <th width="10%">No. Siri Alat</th>
                                    <th>Nama Dan Alamat Pemilik</th>
                                    <th>Kod</th>
                                    <th>No. Daftar Lama</th>
                                    <th>No. Daftar Baru</th>
                                    <th>No. Siri Pelekat Keselamatan</th>
                                    <th width="8%">Fi / Bayaran</th>
                                </tr>';
                        
                            for ($i = $startIndex; $i < $startIndex + $recordsPerPage; $i++) {
                                $message .= $rows[$i];
                            }
                            
                            if ($startIndex + $recordsPerPage >= $num_records) {
                                $message .= '<tr>
                                                <td colspan="8" style="border-left: none; border: none;"></td>
                                                <td colspan="2">Total Amount</td>
                                                <td>RM ' . number_format(floatval($totalAmt), 2, '.', '') . '</td>
                                            </tr>';
                                $message .= '<tr>
                                                <td colspan="8" style="border-left: none; border: none;"></td>
                                                <td colspan="2">SST8%</td>
                                                <td> RM ' . number_format(floatval($sst), 2, '.', '') . '</td>
                                            </tr>';
                                $message .= '<tr>
                                                <td colspan="8" style="border-left: none; border: none;"></td>
                                                <td colspan="2">Sub Total Amount</td>
                                                <td>RM ' . number_format(floatval($subTotalAmt), 2, '.', '') . '</td>
                                            </tr>';

                            }
                        
                        $message .= '
                            <table width="100%" style="padding: 10px;">
                                <tr>
                                    <td width="40%">';
                        
                        if (isset($companySignature) && $companySignature!=null && $companySignature!="") {
                            $message .= '<img src="' . $companySignature . '" style="margin-left:30%; padding-top:10%" width="30%" height="auto"/>';
                        }else{
                            $message .= '<div style="margin-left:30%; padding-top:10%; width:30%; height:auto; background-color:transparent;"></div>';
                        }
                        
                        $message .= '
                                    </td>
                                    <td width="40%"></td>
                                    <td width="30%"></td>
                                </tr>
                                <tr>
                                    <td width="30%" style="border-top: 1px solid black; text-align: center; padding-top: 5px;">
                                        Tandatangan Pemilik / Pembaik
                                    </td>
                                    <td width="40%"></td>
                                    <td width="30%" style="border-top: 1px solid black; text-align: center; padding-top: 5px;">
                                        Pengesahan Pegawai Penentusan
                                    </td>
                                </tr>
                            </table>';
    
                        // Move to the next page
                        $startIndex += $recordsPerPage;
                        $pages++;
    
                        // Add a page break if more records are available
                        if ($startIndex < $totalRecords) {
                            $message .= '<div class="page-break"></div>';
                        }
                }
            }    
    
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

?>