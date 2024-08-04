<?php

require_once 'db_connect.php';
require_once 'requires/lookup.php';
 
if(isset($_POST['id'], $_POST['driver'])){
    $selectedIds = $_POST['id'];
    $arrayOfId = explode(",", $selectedIds);
    $driver = filter_input(INPUT_POST, 'driver', FILTER_SANITIZE_STRING);
    $todayDate = date('d/m/Y');
    $todayDate2 = date('d M Y');
    $today = date("Y-m-d 00:00:00");

    $placeholders = implode(',', array_fill(0, count($arrayOfId), '?'));
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
        $total_pages = ceil($num_records / 10);
        $recordsPerPage = 10;
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
            $count = 1;

            while ($row = $result->fetch_assoc()) {
                $rows[] = '<tr>
                        <td>'.$count.'</td>
                        <td>'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                        <td>'.searchCapacityNameById($row['capacity'], $db).'</td>
                        <td>'.searchBrandNameById($row['brand'], $db).'<br>'.searchModelNameById($row['model'], $db).'</td>
                        <td>'.$row['serial_no'].'</td>
                        <td>'.searchCustNameById($row['customers'], $db).'<br>'.$row['address1'].' '.$row['address2'].' '.$row['address3'].'</td>
                        <td></td>
                        <td>'.$row['no_daftar'].'</td>
                        <td>'.$row['siri_keselamatan'].'</td>
                        <td>'.$row['unit_price'].'</td>
                    </tr>';

                $count++;

                if($count > 10){
                    $count = 1;
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
                                <td style="vertical-align: left;">
                                    <p>NAMA SYARIKAT PEMILIK / PEMBAIK : DX WEIGHING SOLUTION (M) SDN BHD (1284580-M)<br>
                                    ALAMAT : NO. 34, JALAN BAGAN 1,<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp; TAMAN BAGAN, 13400 BUTTERWORTH.<br>
                                    Tel. : 04-332 5822     Fax. : 04-331 5822</p><br>
                                    <p>Pengurus Cawangan :<br>
                                    Metrology Corporation Malaysia Sdn. Bhd.</p>
                                </td>
                                <td style="vertical-align: right;">
                                    <p>Penentusahan Dalam / Luar Pejabat</p>
                                    <p>Tarikh : 19.06.2024</p>
                                    <table class="table-bordered">
                                        <tbody>
                                            <tr><th>Alat</th><th>Jum. Alat</th><th>Bayaran</th></tr>
                                            <tr><td> s</td><td></td><td></td></tr>
                                            <tr><td> s</td><td></td><td></td></tr>
                                            <tr><td> s</td><td></td><td></td></tr>
                                            <tr><td>Jumlah</td><td></td><td></td></tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="text-align: center;">BUTIRAN SENARAI ALAT-ALAT TIMBANG DAN SUKAT UNTUK PENGUJIAN DAN PENENTUSAHAN</p>
                    <table class="table-bordered">
                        <tbody>
                            <tr>
                                <th>Bil.</th>
                                <th>Jenis Alat</th>
                                <th>Had Terima</th>
                                <th>Jenama</th>
                                <th>No. Siri Alat</th>
                                <th>Nama Dan Alamat Pemilik</th>
                                <th>Kod</th>
                                <th>No. Daftar</th>
                                <th>No. Siri Pelekat Keselamatan</th>
                                <th>Fi / Bayaran</th>
                            </tr>';

                        for ($i = $startIndex; $i < $startIndex + $recordsPerPage && $i < $totalRecords; $i++) {
                            $message .= $rows[$i];
                        }
                        

                    $message .= '</tbody></table>';

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