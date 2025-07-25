<?php

require_once 'db_connect.php';
require_once 'requires/lookup.php';
 
if(isset($_POST['id'], $_POST['driver'], $_POST['cawanganBorang'], $_POST['actualPrintDate'])){
    $selectedIds = $_POST['id'];
    $arrayOfId = explode(",", $selectedIds);
    $driver = filter_input(INPUT_POST, 'driver', FILTER_SANITIZE_STRING);
    $cawangan = searchStateNameById(filter_input(INPUT_POST, 'cawanganBorang', FILTER_SANITIZE_STRING), $db);
    $validator2 = filter_input(INPUT_POST, 'validatorBorang', FILTER_SANITIZE_STRING);

    if(isset($_POST['validatorBorang']) && $_POST['validatorBorang']!=null && $_POST['validatorBorang']!=""){
        $validatorFilter = searchValidatorNameById($validator2, $db);
	}else{
        echo json_encode(
            array(
                "status"=> "failed", 
                "message"=> "Please select a validator in searching."
            )
        );
        exit;
    }
    
    $actualPrintDate = filter_input(INPUT_POST, 'actualPrintDate', FILTER_SANITIZE_STRING);
    $actualPrintDateTime = DateTime::createFromFormat('d/m/Y', $actualPrintDate);
    $fromDateTime = $actualPrintDateTime->format('Y-m-d 00:00:00');
    $todayDate = date('d/m/Y');
    $todayDate2 = date('d M Y');
    $todayDate3 = date('d.m.Y');
    $todayDate4 = date('d/m/Y - h:i:s A');
    $today = date("Y-m-d 00:00:00");
    $userId = '';

    if(isset($_POST['userid']) && $_POST['userid']!=null && $_POST['userid']!=""){
		$userId = searchStaffNameById($_POST['userid'], $db);
	}

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

    $select_stmt = $db->prepare("SELECT * FROM stamping WHERE id IN ($placeholders) ORDER BY FIELD(id, $selectedIds)");

    // Check if the statement is prepared successfully
    if ($select_stmt) {
        // Bind variables to the prepared statement
        $types = str_repeat('i', count($arrayOfId)); // Assuming the IDs are integers
        $select_stmt->bind_param($types, ...$arrayOfId);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        $num_records = $result->num_rows;
        $totalRecords = $num_records;
        $recordsPerPage = 6;
        $total_pages = ceil($num_records / $recordsPerPage);
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
                        <th colspan="10" style="vertical-align: middle;">
                            JADUAL 6<br>AKTA TIMBANG DAN SUKAT 1972<br>PERATURAN-PERATURAN TIMBANG DAN SUKAT 1981<br>(PERATURAN 35)<br>DAFTAR TIMBANG , SUKAT DAN ALAT TIMBANG SUKAT YANG DIJUAL/DIBUAT
                        </th>
                    </tr>
                    <tr>
                        <th style="font-size:12px;">STAMPING DATE</th>
                        <th style="font-size:12px;">NAME OF PURCHASE WITH ADDRESS</th>
                        <th style="font-size:12px;">ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS</th>
                        <th style="font-size:12px;" width="12%">CAPACITY</th>
                        <th style="font-size:12px;">QTY</th>
                        <th style="font-size:12px;" width="10%">NO. DAFTAR LAMA</th>
                        <th style="font-size:12px;" width="10%">NO. DAFTAR BARU</th>
                        <th style="font-size:12px;">CERTIFICATE NO./ NO. SIRI PELEKAT KESELAMATAN</th>
                        <th style="font-size:12px;" width="10%">BRG D BIL NO.</th>
                        <th style="font-size:12px;" width="10%">BRG E BIL NO.</th>
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

                // Logic for BTU - (BOX)
                $capacity = '';
                $borangD = '';
                $borangE = '';
                $siriKeselamatan = '';
                $noDaftarLama = '';
                $noDaftarBaru = '';
                $count = 1;
                if (searchAlatNameById($row['jenis_alat'], $db) == 'BTU - (BOX)'){
                    $id = $row['id']; 
                    $stampExtQuery = "SELECT * FROM stamping_ext WHERE stamp_id = $id";
                    $stampDetail = mysqli_query($db, $stampExtQuery);
                    $stampRow = mysqli_fetch_assoc($stampDetail);
                    
                    if(!empty($stampRow)){
                      if (!empty($stampRow['btu_box_info'])){
                        $btuBox = json_decode($stampRow['btu_box_info'], true);
                        foreach ($btuBox as $btu) {
                            $capacity .= $count.'.'.searchCapacityUnitById($btu['penandaanBatuUjian'], $db). '<br>';
                            $borangD .= $count.'.'.$btu['batuBorangD'].'<br>';
                            $borangE .= $count.'.'.$btu['batuBorangE'].'<br>';
                            $siriKeselamatan .= $count.'.'.$btu['batuNoSiriPelekatKeselamatan'].'<br>';
                            $noDaftarLama .= $count.'.'.$btu['batuDaftarLama'].'<br>';
                            $noDaftarBaru .= $count.'.'.$btu['batuDaftarBaru'].'<br>';
                            $count++;
                        }
                      }
                    }
                }else{
                    $capacity = $row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '';
                    $siriKeselamatan = $row['siri_keselamatan'];
                    $noDaftarLama = $row['no_daftar_lama'];
                    $noDaftarBaru = $row['no_daftar_baru'];
                    $borangD = $row['borang_d'];
                    $borangE = $row['borang_e'];
                }
              
                $message .= '<tr>
                                <td style="font-size:12px;">'.$formattedStampingDate.'</td>
                                <td style="font-size:12px;"><b>'.searchCustNameById($row['customers'], $db).'</b><br>'.$address1.' '.$address2.' '.$address3.' '.$address4.'</td>
                                <td style="font-size:12px;">'.searchBrandNameById($row['brand'], $db).'<br>'.searchModelNameById($row['model'], $db).'<br>'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                                <td style="font-size:12px;">'.$capacity.'</td>
                                <td style="font-size:12px;">1</td>
                                <td style="font-size:12px;">'.$noDaftarLama.'</td>
                                <td style="font-size:12px;">'.$noDaftarBaru.'</td>
                                <td style="font-size:12px;">'.$siriKeselamatan.'</td>
                                <td style="font-size:12px;">'.$borangD.'</td>
                                <td style="font-size:12px;">'.$borangE.'</td>
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
                        <th colspan="12" style="vertical-align: middle;">
                            JADUAL 7<br>AKTA TIMBANG DAN SUKAT 1972<br>PERATURAN-PERATURAN TIMBANG DAN SUKAT 1981<br>(PERATURAN 35)<br>DAFTAR TIMBANG , SUKAT DAN ALAT TIMBANG SUKAT YANG DIJUAL/DIBUAT
                        </th>
                    </tr>
                    <tr>
                        <th style="font-size:12px;" width="8%">BRG D BIL NO.</th>
                        <th style="font-size:12px;" width="8%">BRG E BIL NO.</th>
                        <th style="font-size:12px;">STAMPING DATE</th>
                        <th style="font-size:12px;">NAME OF PURCHASE WITH ADDRESS</th>
                        <th style="font-size:12px;">ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS</th>
                        <th style="font-size:12px;">CAPACITY</th>
                        <th style="font-size:12px;">LIST NO. (STMP. NO.)</th>
                        <th style="font-size:12px;" width="10%">NO. DAFTAR LAMA</th>
                        <th style="font-size:12px;" width="10%">NO. DAFTAR BARU</th>
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

                // Logic for BTU - (BOX)
                $capacity = '';
                $borangD = '';
                $borangE = '';
                $siriKeselamatan = '';
                $noDaftarLama = '';
                $noDaftarBaru = '';
                $count = 1;
                if (searchAlatNameById($row['jenis_alat'], $db) == 'BTU - (BOX)'){
                    $id = $row['id']; 
                    $stampExtQuery = "SELECT * FROM stamping_ext WHERE stamp_id = $id";
                    $stampDetail = mysqli_query($db, $stampExtQuery);
                    $stampRow = mysqli_fetch_assoc($stampDetail);
                    
                    if(!empty($stampRow)){
                      if (!empty($stampRow['btu_box_info'])){
                        $btuBox = json_decode($stampRow['btu_box_info'], true);
                        foreach ($btuBox as $btu) {
                            $capacity .= $count.'.'.searchCapacityUnitById($btu['penandaanBatuUjian'], $db). '<br>';
                            $borangD .= $count.'.'.$btu['batuBorangD'].'<br>';
                            $borangE .= $count.'.'.$btu['batuBorangE'].'<br>';
                            $siriKeselamatan .= $count.'.'.$btu['batuNoSiriPelekatKeselamatan'].'<br>';
                            $noDaftarLama .= $count.'.'.$btu['batuDaftarLama'].'<br>';
                            $noDaftarBaru .= $count.'.'.$btu['batuDaftarBaru'].'<br>';
                            $count++;
                        }
                      }
                    }
                }else{
                    $capacity = $row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '';
                    $siriKeselamatan = $row['siri_keselamatan'];
                    $noDaftarLama = $row['no_daftar_lama'];
                    $noDaftarBaru = $row['no_daftar_baru'];
                    $borangD = $row['borang_d'];
                    $borangE = $row['borang_e'];
                }

                $message .= '<tr>
                        <td style="font-size:12px;">'.$borangD.'</td>
                        <td style="font-size:12px;">'.$borangE.'</td>
                        <td style="font-size:12px;">'.$formattedStampingDate.'</td>
                        <td style="font-size:12px;"><b>'.searchCustNameById($row['customers'], $db).'</b><br>'.$address1.' '.$address2.' '.$address3.' '.$address4.'</td>
                        <td style="font-size:12px;">'.searchBrandNameById($row['brand'], $db).'<br>'.searchModelNameById($row['model'], $db).'<br>'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                        <td style="font-size:12px;">'.$capacity.'</td>
                        <td style="font-size:12px;">'.$row['pin_keselamatan'].'</td>
                        <td style="font-size:12px;">'.$noDaftarLama.'</td>
                        <td style="font-size:12px;">'.$noDaftarBaru.'</td>
                        <td style="font-size:12px;">SERVICE / STMP</td>
                        <td style="font-size:12px;">'.$siriKeselamatan.'</td>';

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
            $rows = array();
            $totalAmt = 0;
            $sst = 0;
            $subTotalAmt = 0;
            $rowCount = count($rows);
            $count = 1;
            $indexCount = 1;
            $validator = '2';                
            $jenisAlatData = [];
            $updateIds = [];

            while ($row = $result->fetch_assoc()) {
                $validator = $row['validate_by'];
                array_push($updateIds, $row['id']);

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

                // Logic for BTU - (BOX)
                $capacity = '';
                $borangD = '';
                $borangE = '';
                $siriKeselamatan = '';
                $noDaftarLama = '';
                $noDaftarBaru = '';
                $count = 1;
                if ($jenisAlat == 'BTU - (BOX)'){
                    $id = $row['id']; 
                    $stampExtQuery = "SELECT * FROM stamping_ext WHERE stamp_id = $id";
                    $stampDetail = mysqli_query($db, $stampExtQuery);
                    $stampRow = mysqli_fetch_assoc($stampDetail);
                    
                    if(!empty($stampRow)){
                      if (!empty($stampRow['btu_box_info'])){
                        $btuBox = json_decode($stampRow['btu_box_info'], true);
                        foreach ($btuBox as $btu) {
                            $capacity .= $count.'.'.searchCapacityUnitById($btu['penandaanBatuUjian'], $db). '<br>';
                            $borangD .= $count.'.'.$btu['batuBorangD'].'<br>';
                            $borangE .= $count.'.'.$btu['batuBorangE'].'<br>';
                            $siriKeselamatan .= $count.'.'.$btu['batuNoSiriPelekatKeselamatan'].'<br>';
                            $noDaftarLama .= $count.'.'.$btu['batuDaftarLama'].'<br>';
                            $noDaftarBaru .= $count.'.'.$btu['batuDaftarBaru'].'<br>';
                            $count++;
                        }
                      }
                    }
                }else{
                    $capacity = $row['capacity'] != null ? searchCapacityNameById($row['capacity'], $db) : '';
                    $siriKeselamatan = $row['siri_keselamatan'];
                    $noDaftarLama = $row['no_daftar_lama'];
                    $noDaftarBaru = $row['no_daftar_baru'];
                    $borangD = $row['borang_d'];
                    $borangE = $row['borang_e'];
                }

                $rows[] = '<tr style="height: 30px;">
                            <td style="font-size:12px;">'.$indexCount.'</td>
                            <td style="font-size:12px;">'.$jenisAlat.'</td>
                            <td style="font-size:12px;">'.$capacity.'</td>
                            <td style="font-size:12px; padding-left:0.5%;">'.searchBrandNameById($row['brand'], $db).'<br>'.searchModelNameById($row['model'], $db).'</td>
                            <td style="font-size:12px; padding-left:0.5%;">'.$row['serial_no'].'</td>
                            <td style="font-size:12px;"><b>'.searchCustNameById($row['customers'], $db).'</b><br>'.$address1.' '.$address2.' '.$address3.' '.$address4.'</td>
                            <td style="font-size:12px;"></td>
                            <td style="font-size:12px; padding-left:0.5%;">'.$noDaftarLama.'</td>
                            <td style="font-size:12px; padding-left:0.5%;">'.$noDaftarBaru.'</td>
                            <td style="font-size:12px;">'.$siriKeselamatan.'</td>
                            <td style="font-size:12px;">'.$borangD.'</td>
                            <td style="font-size:12px;">'.$borangE.'</td>';
                            
                            if ($row['cert_price'] != 0) {
                                $rows[$rowCount] .= '<td style="padding-left: 0.5%">RM '.number_format(floatval($row['unit_price']), 2, '.', '').'<br>RM '.number_format(floatval($row['cert_price']), 2, '.', '').' (Laporan)</td>';
                            } else {
                                $rows[$rowCount] .= '<td style="padding-left: 0.5%">RM '.number_format(floatval($row['unit_price']), 2, '.', '').'</td>';
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
    
                                    $message .= '<p><b>Pengurus Cawangan : <span style="font-size: 14px">' . $validatorFilter . ' ' . $cawangan . '</span></b><br>';
                                        
                                    // if($validator == '2'){
                                    //     $message .= 'Metrology Corporation Malaysia Sdn. Bhd.</p>';
                                    // }
                                    // elseif($validator == '3'){
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
                                        <p>Tarikh : '.$actualPrintDate.'</p>
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
                        <table class="table-bordered" style="border-left: none; border-bottom: none; text-align:center;">
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
                                    <th style="font-size:12px;" width="7%">Borang D</th>
                                    <th style="font-size:12px;" width="7%">Borang E</th>
                                    <th style="font-size:12px;" width="8%">Fi / Bayaran</th>
                                </tr>';
                            
                            for ($i = $startIndex; $i < $startIndex + $recordsPerPage; $i++) {
                                $message .= $rows[$i];
                            }
                            
                            if ($startIndex + $recordsPerPage >= $num_records) {
                                $message .= '<tr>
                                                <td colspan="10" style="border-left: none; border: none;"></td>
                                                <td colspan="2">Total Amount</td>
                                                <td>RM ' . number_format(floatval($totalAmt), 2, '.', '') . '</td>
                                            </tr>';
                                $message .= '<tr>
                                                <td colspan="10" style="border-left: none; border: none;"></td>
                                                <td colspan="2">SST8%</td>
                                                <td> RM ' . number_format(floatval($sst), 2, '.', '') . '</td>
                                            </tr>';
                                $message .= '<tr>
                                                <td colspan="10" style="border-left: none; border: none;"></td>
                                                <td colspan="2">Sub Total Amount</td>
                                                <td>RM ' . number_format(floatval($subTotalAmt), 2, '.', '') . '</td>
                                            </tr>';

                            }
                        
                        $message .= '
                            <table width="100%" style="padding: 10px;">
                                <tr>
                                    <td width="40%">';
                        
                        if (isset($companySignature) && $companySignature!=null && $companySignature!="") {
                            $message .= '<img src="view_file.php?file=' . $companySignature . '" style="margin-left:30%; padding-top:6%" width="30%" height="auto"/>';
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

                $update_stmt = $db->prepare("UPDATE stamping SET stamping_date = '$fromDateTime', validate_by = '$validator2' WHERE id IN ($placeholders)");
                $update_stmt->bind_param($types, ...$arrayOfId);
                $update_stmt->execute();
                $update_stmt->close();
            }    
    
        $message .= '</body></html>';

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

    $select_stmt->close();
    $db->close();
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