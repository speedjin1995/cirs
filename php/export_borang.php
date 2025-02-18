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
    $todayDate3 = date('d.m.Y');
    $todayDate4 = date('d/m/Y - h:i:s A');
    $today = date("Y-m-d 00:00:00");
    $userId = '';

    if(isset($_POST['userid']) && $_POST['userid']!=null && $_POST['userid']!=""){
		$userId = searchStaffNameById($_POST['userid'], $db);
	}

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
        $total_pages = ceil($num_records / 6);
        $recordsPerPage = 6;
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
                        <th style="font-size:12px;">STAMPING DATE</th>
                        <th style="font-size:12px;">NAME OF PURCHASE WITH ADDRESS</th>
                        <th style="font-size:12px;">ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS</th>
                        <th style="font-size:12px;" width="12%">CAPACITY</th>
                        <th style="font-size:12px;">QTY</th>
                        <th style="font-size:12px;">NO. DAFTAR LAMA</th>
                        <th style="font-size:12px;">NO. DAFTAR BARU</th>
                        <th style="font-size:12px;">CERTIFICATE NO./ NO. SIRI PELEKAT KESELAMATAN</th>
                    </tr>';

            while ($row = $result->fetch_assoc()) {
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

                $stampingDate = new DateTime($row['stamping_date']);
                $formattedStampingDate = $stampingDate->format('d-m-Y');
              
                $message .= '<tr>
                                <td style="font-size:12px;">'.$formattedStampingDate.'</td>
                                <td style="font-size:12px;"><b>'.searchCustNameById($row['customers'], $db).'</b><br>'.$address1.' '.$address2.' '.$address3.' '.$address4.'</td>
                                <td style="font-size:12px;">'.searchBrandNameById($row['brand'], $db).'<br>'.searchModelNameById($row['model'], $db).'<br>'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                                <td style="font-size:12px;">'.searchCapacityNameById($row['capacity'], $db).'</td>
                                <td style="font-size:12px;">1</td>
                                <td style="font-size:12px;">'.$row['no_daftar_lama'].'</td>
                                <td style="font-size:12px;">'.$row['no_daftar_baru'].'</td>
                                <td style="font-size:12px;">'.$row['siri_keselamatan'].'</td>
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
                        <th style="font-size:12px;">BRG E BIL NO.</th>
                        <th style="font-size:12px;">STAMPING DATE</th>
                        <th style="font-size:12px;">NAME OF PURCHASE WITH ADDRESS</th>
                        <th style="font-size:12px;">ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS</th>
                        <th style="font-size:12px;">CAPACITY</th>
                        <th style="font-size:12px;">LIST NO. (STMP. NO.)</th>
                        <th style="font-size:12px;">NO. DAFTAR LAMA</th>
                        <th style="font-size:12px;">NO. DAFTAR BARU</th>
                        <th style="font-size:12px;">DETAILS OF REPAIR</th>
                        <th style="font-size:12px;">CERTIFICATE NO./ NO. SIRI PELEKAT KESELAMATAN</th>
                        <th style="font-size:12px;">FEE</th>
                    </tr>';

            while ($row = $result->fetch_assoc()) {
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

                $stampingDate = new DateTime($row['stamping_date']);
                $formattedStampingDate = $stampingDate->format('d-m-Y');

                $message .= '<tr>
                        <td style="font-size:12px;">'.$row['borang_e'].'</td>
                        <td style="font-size:12px;">'.$formattedStampingDate.'</td>
                        <td style="font-size:12px;"><b>'.searchCustNameById($row['customers'], $db).'</b><br>'.$address1.' '.$address2.' '.$address3.' '.$address4.'</td>
                        <td style="font-size:12px;">'.searchBrandNameById($row['brand'], $db).'<br>'.searchModelNameById($row['model'], $db).'<br>'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                        <td style="font-size:12px;">'.searchCapacityNameById($row['capacity'], $db).'</td>
                        <td style="font-size:12px;">'.$row['pin_keselamatan'].'</td>
                        <td style="font-size:12px;">'.$row['no_daftar_lama'].'</td>
                        <td style="font-size:12px;">'.$row['no_daftar_baru'].'</td>
                        <td style="font-size:12px;">SERVICE / STMP</td>
                        <td style="font-size:12px;">'.$row['siri_keselamatan'].'</td>';

                if($row['cert_price'] != 0){
                    $message .= '<td style="padding-left: 0.5%" width="7%">RM '.number_format(floatval($row['unit_price']), 2, '.', '').'<br>RM '.number_format(floatval($row['cert_price']), 2, '.', '').' (Laporan)</td>
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
                                <td style="font-size:12px;padding-left: 0.5%">'.$indexCount.'</td>
                                <td style="font-size:12px;padding-left: 0.5%">'.$jenisAlat.'</td>
                                <td style="font-size:12px;padding-left: 0.5%">'.searchCapacityNameById($row['capacity'], $db).'</td>
                                <td style="font-size:12px;">'.searchBrandNameById($row['brand'], $db).'<br>'.searchModelNameById($row['model'], $db).'</td>
                                <td style="font-size:12px;padding-left: 0.5%">'.$row['serial_no'].'</td>
                                <td style="font-size:12px;padding-left: 0.5%"><b>'.searchCustNameById($row['customers'], $db).'</b><br>'.$address1.' '.$address2.' '.$address3.' '.$address4.'</td>
                                <td style="font-size:12px;padding-left: 0.5%"></td>
                                <td style="font-size:12px;padding-left: 0.5%">'.$row['no_daftar_lama'].'</td>
                                <td style="font-size:12px;padding-left: 0.5%">'.$row['no_daftar_baru'].'</td>
                                <td style="font-size:12px;padding-left: 0.5%">'.$row['siri_keselamatan'].'</td>';
                                
                                if ($row['cert_price'] != 0) {
                                    $rows[$rowCount] .= '<td style="font-size:12px;padding-left: 0.5%">RM '.number_format(floatval($row['unit_price']), 2, '.', '').'<br>RM '.number_format(floatval($row['cert_price']), 2, '.', '').' (Report)</td>';
                                } else {
                                    $rows[$rowCount] .= '<td style="font-size:12px;padding-left: 0.5%">RM '.number_format(floatval($row['unit_price']), 2, '.', '').'</td>';
                                }
                                
                    $rows[$rowCount] .= '</tr>';

                    $totalAmt += floatval($row['unit_price']) + floatval($row['cert_price']);
                    
                    $count++;
                    $rowCount++;
                    $indexCount++;
    
                    if($count > 6){
                        $count = 1;
                    }
                }
                
                $sst = $totalAmt * (8/100);
                $subTotalAmt = $totalAmt + $sst;
                
                if ($count <= 6 && count($rows) % 6 != 0) {
                    $remainingRows = 6 - (count($rows) % 6);
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
                                        <p>NAMA SYARIKAT PEMILIK / PEMBAIK : <br><br><b>'.$companyName.' ('.$companyOldRoc.')</b><br>
                                        ALAMAT : '.$companyAddress.'<br>
                                        Tel. : '.$companyTel.'     Fax. : '.$companyFax.'</p><br>';
    
                                    $message .= '<p><b>Pengurus Cawangan : <span style="font-size: 14px">' . $validatorFilter . ' ' . $cawanganFilter . '</span></b><br>';
                                        
                                    // if($validatorFilter == 'METROLOGY'){
                                    //     $message .= 'Metrology Corporation Malaysia Sdn. Bhd.</p>';
                                    // }
                                    // elseif($validatorFilter == 'DE METROLOGY'){
                                    //     $message .= 'De Metrology Corporation Malaysia Sdn. Bhd.</p>';
                                    // }

                                    $message .= '</td>
                                    <td valign="top" align="center" width="20%">';
                                    if($validatorFilter == 'METROLOGY'){
                                        $message .= '<img src="https://cirs.syncweigh.com/assets/metrology.jpeg" width="80%" height="auto" style="margin-left: -20%; margin-top: 15%;"/>';
                                    }
                                    elseif($validatorFilter == 'DE METROLOGY'){
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
    
                        <p style="text-align: center; font-weight:bold;">BUTIRAN SENARAI ALAT-ALAT TIMBANG DAN SUKAT UNTUK PENGUJIAN DAN PENENTUSAHAN</p>
                        <table class="table-bordered" style="border-left: none; border-bottom: none;">
                            <tbody>
                                <tr>
                                    <th style="font-size:12px;">Bil.</th>
                                    <th style="font-size:12px;">Jenis Alat</th>
                                    <th style="font-size:12px;">Had Terima</th>
                                    <th style="font-size:12px;">Jenama</th>
                                    <th style="font-size:12px;" width="10%">No. Siri Alat</th>
                                    <th style="font-size:12px;">Nama Dan Alamat Pemilik</th>
                                    <th style="font-size:12px;" width="5%">Kod</th>
                                    <th style="font-size:12px;" width="10%">No. Daftar Lama</th>
                                    <th style="font-size:12px;" width="10%">No. Daftar Baru</th>
                                    <th style="font-size:12px;">No. Siri Pelekat Keselamatan</th>
                                    <th style="font-size:12px;" width="8%">Fi / Bayaran</th>
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
                            $message .= '<img src="' . $companySignature . '" style="margin-left:30%; padding-top:6%" width="30%" height="auto"/>';
                        }else{
                            $message .= '<div style="margin-left:30%; padding-top:6%; width:30%; height:auto; background-color:transparent;"></div>';
                        }
                        
                        $message .= '
                                    </td>
                                    <td width="40%"></td>
                                    <td width="30%"></td>
                                </tr>
                                <tr>
                                    <td width="30%" style="border-top: 1px solid black; text-align: center; padding-top: 5px;">
                                        <div>'.$userId.'</div>
                                        <div>Tandatangan Pemilik / Pembaik</div>
                                    </td>
                                    <td width="40%"></td>
                                    <td width="30%" style="border-top: 1px solid black; text-align: center; padding-top: 5px;">
                                        Pengesahan Pegawai Penentusan
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align: center;"><span style="margin-left: 100px;">Printed: '.$todayDate4.'</span></td>
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