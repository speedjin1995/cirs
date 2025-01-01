<?php

require_once 'db_connect.php';
require_once 'requires/lookup.php';
 
if(isset($_POST['driver']) && !empty($_POST['ids'])){
    $searchQuery = '';

    // if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
    //     $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['fromDate']);
    //     $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
    //     $searchQuery = " and stamping_date >= '".$fromDateTime."'";
    // }
    
    // if($_POST['toDate'] != null && $_POST['toDate'] != ''){
    //     $dateTime = DateTime::createFromFormat('d/m/Y', $_POST['toDate']);
    //     $toDateTime = $dateTime->format('Y-m-d 23:59:59');
    //     $searchQuery .= " and stamping_date <= '".$toDateTime."'";
    // }
    
    // if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
    //     $searchQuery .= " and customers = '".$_POST['customer']."'";
    // }

    
    if($_POST['ids'] != null && $_POST['ids'] != ''){
        // Sanitize the values and prepare them for SQL
        $sanitized_ids = array_map(function($id) {
            return "'" . addslashes($id) . "'";
        }, $_POST['ids']);

        // Convert the sanitized array into a string
        $id_list = implode(",", $sanitized_ids);
        
        $searchQuery .= " and id IN ($id_list)";
    }

    $driver = $_POST['driver'];
    $todayDate = date('d/m/Y');
    $todayDate2 = date('d M Y');
    $today = date("Y-m-d 00:00:00");

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

    $select_stmt = $db->prepare("SELECT * FROM stamping WHERE status = 'Complete'".$searchQuery);

    // Check if the statement is prepared successfully
    if ($select_stmt) {
        // Bind variables to the prepared statement
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        $num_records = $result->num_rows;
        $totalRecords = $num_records;
        $total_pages = ceil($num_records / 7);
        $recordsPerPage = 7;
        $startIndex = 0;
        $pages = 0;
        $message = '';

        // $alatQuery = "SELECT * FROM branches WHERE id = $branch";
        // $alatDetail = mysqli_query($db, $alatQuery);
        // $alatRow = mysqli_fetch_assoc($alatDetail);
        

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
                        padding-left: 0.50rem;
                        padding-right: 0.50rem;
                        padding-left: 0.4rem;
                        padding-right: 0.4rem;
                        text-align: center;
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
                        <th>STAMPING DATE</th>
                        <th>NAME OF PURCHASE</th>
                        <th>ADDRESS</th>
                        <th>ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS</th>
                        <th width="12%">CAPACITY</th>
                        <th>QUANTITY</th>
                        <th>REGISTER NO.</th>
                        <th>CERTIFICATE NO./ NO. SIRI PELEKAT KESELAMATAN</th>
                    </tr>';

            while ($row = $result->fetch_assoc()) {
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

                $stampingDate = new DateTime($row['stamping_date']);
                $formattedStampingDate = $stampingDate->format('d-m-Y');
              
                $message .= '<tr>
                                <td>'.$formattedStampingDate.'</td>
                                <td>'.searchCustNameById($row['customers'], $db).'</td>
                                <td>'.$address1.' '.$address2.' '.$address3.' '.$address4.'</td>
                                <td>'.searchBrandNameById($row['brand'], $db).'<br>'.searchModelNameById($row['model'], $db).'<br>'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                                <td>'.searchCapacityNameById($row['capacity'], $db).'</td>
                                <td>1</td>
                                <td>'.$row['no_daftar'].'</td>
                                <td>'.$row['siri_keselamatan'].'</td>
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
                        padding-left: 0.50rem;
                        padding-right: 0.50rem;
                        padding-left: 0.4rem;
                        padding-right: 0.4rem;
                        text-align: center;
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
                        <th>STAMPING DATE</th>
                        <th>NAME OF PURCHASE</th>
                        <th>ADDRESS</th>
                        <th>ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS</th>
                        <th>CAPACITY</th>
                        <th>LIST NO. (STMP. NO.)</th>
                        <th>REGISTER NO. (BARU / LAMA)</th>
                        <th>DETAILS OF REPAIR</th>
                        <th>CERTIFICATE NO./ NO. SIRI PELEKAT KESELAMATAN</th>
                        <th>FEE</th>
                    </tr>';

            while ($row = $result->fetch_assoc()) {
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

                $stampingDate = new DateTime($row['stamping_date']);
                $formattedStampingDate = $stampingDate->format('d-m-Y');

                $message .= '<tr>
                        <td>'.$row['borang_e'].'</td>
                        <td>'.$formattedStampingDate.'</td>
                        <td>'.searchCustNameById($row['customers'], $db).'</td>
                        <td>'.$address1.' '.$address2.' '.$address3.' '.$address4.'</td>
                        <td>'.searchBrandNameById($row['brand'], $db).'<br>'.searchModelNameById($row['model'], $db).'<br>'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                        <td>'.searchCapacityNameById($row['capacity'], $db).'</td>
                        <td>'.$row['pin_keselamatan'].'</td>
                        <td>'.$row['no_daftar'].'</td>
                        <td>SERVICE / STMP</td>
                        <td>'.$row['siri_keselamatan'].'</td>';

                if($row['cert_price'] != 0){
                    $message .= '<td style="padding-left: 0.5%" width="7%">RM '.number_format(floatval($row['unit_price']), 2, '.', '').'<br>RM '.number_format(floatval($row['cert_price']), 2, '.', '').'</td>
                    </tr>';
                }else{
                    $message .= '<td style="padding-left: 0.5%" width="7%">RM '.number_format(floatval($row['unit_price']), 2, '.', '').'</td>
                    </tr>';
                }
                        
            }

            $message .= '</tbody></table>';
        }
        else{
            if(empty($_POST['validator']) && empty($_POST['cawangan'])){
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> "Please select a validator & cawangan."
                    )
                ); 
                exit;
            }elseif(empty($_POST['validator'])){
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> "Please select a validator."
                    )
                );
                exit;
            }elseif(empty($_POST['cawangan'])){
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> "Please select a cawangan."
                    )
                );
                exit;
            }else{
                $validatorFilter = searchValidatorNameById($_POST['validator'], $db);
                $cawanganFilter = searchStateNameById($_POST['cawangan'], $db);
            
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
                                <td style="padding-left: 0.5%">'.$row['no_daftar'].'</td>
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
    
                                    $message .= '<p>Pengurus Cawangan : <span style="font-size: 14px; font-weight: bold;">' . $validatorFilter . ' ' . $cawanganFilter . '</span><br>';
                                        
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
                                        <p>Tarikh : 23.06.2024</p>
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
                                    <th>No. Daftar</th>
                                    <th>No. Siri Pelekat Keselamatan</th>
                                    <th width="8%">Fi / Bayaran</th>
                                </tr>';
                        
                            for ($i = $startIndex; $i < $startIndex + $recordsPerPage; $i++) {
                                $message .= $rows[$i];
                            }
                            
                            if ($startIndex + $recordsPerPage >= $num_records) {
                                $message .= '<tr>
                                                <td colspan="7" style="border-left: none; border: none;"></td>
                                                <td colspan="2">Total Amount</td>
                                                <td>RM ' . number_format(floatval($totalAmt), 2, '.', '') . '</td>
                                            </tr>';
                                $message .= '<tr>
                                                <td colspan="7" style="border-left: none; border: none;"></td>
                                                <td colspan="2">SST8%</td>
                                                <td> RM ' . number_format(floatval($sst), 2, '.', '') . '</td>
                                            </tr>';
                                $message .= '<tr>
                                                <td colspan="7" style="border-left: none; border: none;"></td>
                                                <td colspan="2">Sub Total Amount</td>
                                                <td>RM ' . number_format(floatval($subTotalAmt), 2, '.', '') . '</td>
                                            </tr>';

                            }
                        
                        $message .= '
                            <table width="100%" style="padding: 10px;">
                                <tr>
                                    <td width="40%">
                                        <img src="'.$companySignature.'" style="margin-left:30%; padding-top:10%" width="30%" height="auto"/>
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