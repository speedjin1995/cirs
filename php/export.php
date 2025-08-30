<?php

require_once 'db_connect.php';
require_once 'requires/lookup.php';

$fileName = 'nothing.xls';
$excelData = '';

function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 
 
if(isset($_GET['type'])){
    $searchQuery = '';

    if($_GET["type"] == '6'){
        $fileName = "Jadual6_" . date('Y-m-d') . ".xls";
    }
    else if($_GET["type"] == '7'){
        $fileName = "Jadual7_" . date('Y-m-d') . ".xls";
    }
    else if($_GET['type'] == 'cancelledStamp') {
        $fileName = "CancelledStamping_" . date('Y-m-d'). ".xls";
    } 
    else{
        $fileName = "Panjang_" . date('Y-m-d') . ".xls";
    } 

    if($_GET['fromDate'] != null && $_GET['fromDate'] != ''){
        $dateTime = DateTime::createFromFormat('d/m/Y', $_GET['fromDate']);
        $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
        $searchQuery = " and stamping_date >= '".$fromDateTime."'";
    }
    
    if($_GET['toDate'] != null && $_GET['toDate'] != ''){
        $dateTime = DateTime::createFromFormat('d/m/Y', $_GET['toDate']);
        $toDateTime = $dateTime->format('Y-m-d 23:59:59');
        $searchQuery .= " and stamping_date <= '".$toDateTime."'";
    }
    
    if($_GET['id'] != null && $_GET['id'] != ''){
        $ids = $_GET['id'];
        $searchQuery .= " and id IN (".$ids.")";
    }
    
    $driver = $_GET['type'];
    $todayDate = date('d/m/Y');
    $todayDate2 = date('d M Y');
    $today = date("Y-m-d 00:00:00");

    if($_GET['type'] == 'cancelledStamp'){
        if($_GET['stamps'] != null && $_GET['stamps'] != '' && $_GET['stamps'] != '-'){
            $stamps = $_GET['stamps'];
            $searchQuery .= " and id IN ($stamps)";
        }

        $select_stmt = $db->prepare("SELECT * FROM stamping WHERE status = 'Cancelled'".$searchQuery);
    }else{
        if($_GET['customer'] != null && $_GET['customer'] != '' && $_GET['customer'] != '-'){
            $searchQuery .= " and customers = '".$_GET['customer']."'";
        }
    
        $test = "SELECT * FROM stamping where status = 'Complete'".$searchQuery;
        $select_stmt = $db->prepare("SELECT * FROM stamping WHERE status = 'Complete'".$searchQuery);
    }

    // Check if the statement is prepared successfully
    if ($select_stmt) {
        // Bind variables to the prepared statement
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
            $excelData = "
                            <style>
                                .header {
                                    text-align:center;
                                    font-weight:bold;
                                    white-space:pre-wrap;
                                    border-bottom: none;
                                    border-top: none;
                                }  
                                .body {
                                    text-align: center;
                                }
                            </style>
                        ";
            $excelData .= "<table border='1'>";
            $excelData .= "
                        <tr>
                            <td colspan='16' class='header'>JADUAL 6</td>
                        </tr>
                        <tr>
                            <td colspan='16' class='header'>AKTA TIMBANG DAN SUKAT 1972</td>
                        </tr>
                        <tr>
                            <td colspan='16' class='header'>PERATURAN-PERATURAN TIMBANG DAN SUKAT 1981</td>
                        </tr>
                        <tr>
                            <td colspan='16' class='header'>(PERATURAN 35)</td>
                        </tr>
                        <tr>
                            <td colspan='16' class='header'>DAFTAR TIMBANG, SUKAT DAN ALAT TIMBANG SUKAT YANG DIJUAL/DIBUAT</td>
                        </tr>
                        <tr>
                            <th>DATE</th>
                            <th>ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS</th>
                            <th>MODEL</th>
                            <th>JENIS ALAT</th>
                            <th>CAPACITY</th>
                            <th>QUANTITY</th>
                            <th>VALIDATOR BY (LAMA)</th>
                            <th>NO. DAFTAR (LAMA)</th>
                            <th>SEAL NO. (LAMA)</th>
                            <th>VALIDATOR BY (BARU)</th>
                            <th>NO. DAFTAR (BARU)</th>
                            <th>SEAL NO. (BARU)</th>
                            <th>CERTIFICATE NO./ NO. SIRI PELEKAT KESELAMATAN</th>
                            <th>COMPANY BRANCH</th>
                            <th>NAME OF PURCHASE</th>
                            <th>ADDRESS</th>
                        </tr>
                        ";

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

                $custAddress = $address1 . ' ' . $address2 . ' ' . $address3 . ' ' . $address4;
                $stampingDate = new DateTime($row['stamping_date']);
                $formattedStampingDate = $stampingDate->format('d/m/Y');

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

                $excelData .= '<tr>
                                <td class="body">'.$formattedStampingDate.'</td>
                                <td class="body">'.searchBrandNameById($row['brand'], $db).'</td>
                                <td class="body">'.searchModelNameById($row['model'], $db).'</td>
                                <td class="body">'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                                <td class="body">'.$capacity.'</td>
                                <td class="body">1</td>
                                <td class="body">'.searchValidatorNameById($row['validator_lama'], $db).'</td>
                                <td class="body">'.$noDaftarLama.'</td>
                                <td class="body">'.$row['seal_no_lama'].'</td>
                                <td class="body">'.searchValidatorNameById($row['validate_by'], $db).'</td>
                                <td class="body">'.$noDaftarBaru.'</td>
                                <td class="body">'.$row['seal_no_baru'].'</td>
                                <td class="body">'.$siriKeselamatan.'</td>
                                <td class="body">'.searchCompanyBranchById($row['company_branch'], $db).'</td>
                                <td class="body">'.searchCustNameById($row['customers'], $db).'</td>
                                <td class="body">'.$custAddress.'</td>
                            </tr>';
            }
        }
        else if($driver == '7'){
            $excelData = "
                            <style>
                                .header {
                                    text-align:center;
                                    font-weight:bold;
                                    white-space:pre-wrap;
                                    border-bottom: none;
                                    border-top: none;
                                }  
                                .body {
                                    text-align: center;
                                }
                            </style>
                        ";
            $excelData .= "<table border='1'>";
            $excelData .= "
                        <tr>
                            <td colspan='17' class='header'>JADUAL 7</td>
                        </tr>
                        <tr>
                            <td colspan='17' class='header'>AKTA TIMBANG DAN SUKAT 1972</td>
                        </tr>
                        <tr>
                            <td colspan='17' class='header'>PERATURAN-PERATURAN TIMBANG DAN SUKAT 1981</td>
                        </tr>
                        <tr>
                            <td colspan='17' class='header'>(PERATURAN 35)</td>
                        </tr>
                        <tr>
                            <td colspan='17' class='header'>DAFTAR TIMBANG, SUKAT DAN ALAT TIMBANG SUKAT YANG TELAH DIBAIKI</td>
                        </tr>
                        <tr>
                            <th>BRG (E) <br> BIL NO.</th>
                            <th>BRG (E) <br> DATE</th>
                            <th>STAMPING DATE</th>
                            <th>NAME OF PURCHASE WITH ADDRESS</th>
                            <th>ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS</th>
                            <th>MODEL</th>
                            <th>JENIS ALAT</th>
                            <th>CAPACITY</th>
                            <th>LIST NO. <br>(STMP. NO.)</th>
                            <th>NO. DAFTAR <br> (LAMA)</th>
                            <th>SEAL NO. <br> (LAMA)</th>
                            <th>NO. DAFTAR <br>(BARU)</th>
                            <th>SEAL NO. <br>(BARU)</th>
                            <th>COMPANY BRANCH</th>
                            <th>DETAILS OF REPAIR</th>
                            <th>CERTIFICATE NO./ <br> NO. SIRI PELEKAT KESELAMATAN</th>
                            <th>FEE (RM)</th>
                        </tr>
                        ";

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

                if ($row['borang_e_date'] == null || $row['borang_e_date'] == '0000-00-00 00:00:00'){
                    $formattedBorangEDate = '';
                } else {
                    $borangEDate = new DateTime($row['borang_e_date']);
                    $formattedBorangEDate = $borangEDate->format('d-m-Y');
                }

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

                $excelData .= '<tr>
                                <td class="body">'.$borangE.'</td>
                                <td class="body">'.$formattedBorangEDate.'</td>
                                <td class="body">'.$formattedStampingDate.'</td>
                                <td class="body"><b>'.searchCustNameById($row['customers'], $db).'</b><br>'.$address1.' '.$address2.' '.$address3.' '.$address4.'</td>
                                <td class="body">'.searchBrandNameById($row['brand'], $db).'</td>
                                <td class="body">'.searchModelNameById($row['model'], $db).'</td>
                                <td class="body">'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                                <td class="body">'.$capacity.'</td>
                                <td class="body">'.$row['pin_keselamatan'].'</td>
                                <td class="body">'.$noDaftarLama.'</td>
                                <td class="body">'.$row['seal_no_lama'].'</td>
                                <td class="body">'.$noDaftarBaru.'</td>
                                <td class="body">'.$row['seal_no_baru'].'</td>
                                <td class="body">'.searchCompanyBranchById($row['company_branch'], $db).'</td>
                                <td class="body">SERVICE / STMP</td>
                                <td class="body">'.$siriKeselamatan.'</td>';

                                if($row['cert_price'] != 0){
                                    $excelData .= '<td class="body">RM '.number_format(floatval($row['unit_price']), 2, '.', '').'<br>RM '.number_format(floatval($row['cert_price']), 2, '.', '').'</td>
                                    </tr>';
                                }else{
                                    $excelData .= '<td class="body">RM '.number_format(floatval($row['unit_price']), 2, '.', '').'</td>
                                    </tr>';
                                }


                $excelData .= '</tr>';

                // $lineData = array('', $todayDate2, searchBrandNameById($row['brand'], $db).'\n'.searchModelNameById($row['model'], $db).'\n'.searchAlatNameById($row['jenis_alat'], $db), 
                // searchCapacityNameById($row['capacity'], $db), $row['pin_keselamatan'], $row['no_daftar'], 'SERVICE / STMP', $row['siri_keselamatan'], searchCustNameById($row['customers'], $db),
                // $address1.' '.$address2.' '.$address3.' '.$address4, $row['unit_price']);
                
                // array_walk($lineData, 'filterData'); 
                // $excelData .= implode("\t", array_values($lineData)) . "\n"; 
            }
        }
        else if($driver == 'cancelledStamp'){
            $fields = array('VALIDATOR', 'CUSTOMERS', 'BRANDS', 'ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS', 'MODEL', 
            'CAPACITY', 'SERIAL NO.', 'NEXT DUE DATE', 'UPDATED DATE', 'STATUS');
            $excelData = implode("\t", array_values($fields)) . "\n"; 

            while ($row = $result->fetch_assoc()) {
                $branch = $row['branch'];

                $lineData = array(searchValidatorNameById($row['validate_by'], $db), searchCustNameById($row['customers'], $db), searchBrandNameById($row['brand'], $db),searchMachineNameById($row['machine_type'], $db), searchModelNameById($row['model'], $db), searchCapacityNameById($row['capacity'], $db), $row['serial_no'], $row['due_date'], $row['updated_datetime'], $row['status']);
                
                array_walk($lineData, 'filterData'); 
                $excelData .= implode("\t", array_values($lineData)) . "\n"; 
            }
        }
        else{
            $rows = array();
            $count = 1;
            $validator = '2';
            $totalAmt = 0;

            $excelData = "
                            <style>
                                .header {
                                    text-align:center;
                                    font-weight:bold;
                                    white-space:pre-wrap;
                                    border-bottom: none;
                                    border-top: none;
                                }  
                                .body {
                                    text-align: center;
                                }
                            </style>
                        ";
            $excelData .= "<table border='1' style='border-bottom:none; border-right:none;'>";
            $excelData .= "
                        <tr>
                            <td rowspan='2' colspan='13' class='header'>BUTIRAN SENARAI ALAT-ALAT TIMBANG DAN SUKAT UNTUK PENGUJIAN DAN PENENTUSAHAN</td>
                        </tr>
                        <tr></tr>
                        <tr>
                            <th>VALIDATOR BY</th>
                            <th>CAWANGAN</th>
                            <th>JENIS ALAT</th>
                            <th>HAD TERIMA</th>
                            <th>JENAMA</th>
                            <th>NO. SIRI ALAT</th>
                            <th>NAMA PEMILIK</th>
                            <th>ADDRESS</th>
                            <th>KOD</th>
                            <th>NO. DAFTAR (LAMA)</th>
                            <th>NO. DAFTAR (BARU)</th>
                            <th>NO. SIRI PELEKAT KESELAMATAN</th>
                            <th>FEE/BAYARAN (RM)</th>
                            <th style='border-top:none;border-bottom:none;'></th>
                        </tr>
                        ";

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

                $custAddress = $address1 . ' ' . $address2 . ' ' . $address3 . ' ' . $address4;
                $stampingDate = new DateTime($row['stamping_date']);
                $formattedStampingDate = $stampingDate->format('d-m-Y');
                $totalAmt += floatval($row['unit_price']) + floatval($row['cert_price']);

                $excelData .= '<tr>
                                <td class="body">'.searchValidatorNameById($row['validate_by'], $db).'</td>
                                <td class="body">'.searchStateNameById($row['cawangan'], $db).'</td>
                                <td class="body">'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                                <td class="body">'.searchCapacityNameById($row['capacity'], $db).'</td>
                                <td class="body">'.searchBrandNameById($row['brand'], $db).'<br>'.searchModelNameById($row['model'], $db).'</td>
                                <td class="body">'.$row['serial_no'].'</td>
                                <td class="body">'.searchCustNameById($row['customers'], $db).'</td>
                                <td class="body">'.$custAddress.'</td>
                                <td class="body"></td>
                                <td class="body">'.$row['no_daftar_lama'].'</td>
                                <td class="body">'.$row['no_daftar_baru'].'</td>
                                <td class="body">'.$row['siri_keselamatan'].'</td>';

                                if ($row['cert_price'] != 0) {
                                    $excelData .= '<td class="body">RM '.number_format(floatval($row['unit_price']), 2, '.', '').'<br>RM '.number_format(floatval($row['cert_price']), 2, '.', '').'</td>';
                                } else {
                                    $excelData .= '<td class="body">RM '.number_format(floatval($row['unit_price']), 2, '.', '').'</td>';
                                }

                // $lineData = array($count, searchAlatNameById($row['jenis_alat'], $db), searchCapacityNameById($row['capacity'], $db), 
                // searchBrandNameById($row['brand'], $db).'\n'.searchModelNameById($row['model'], $db).'\n'.searchAlatNameById($row['jenis_alat'], $db), 
                // $row['serial_no'], searchCustNameById($row['customers'], $db).'\n'.$address1.' '.$address2.' '.$address3.' '.$address4, 
                // $row['no_daftar'], $row['siri_keselamatan'], $row['unit_price']);
                
                // array_walk($lineData, 'filterData'); 
                // $excelData .= implode("\t", array_values($lineData)) . "\n"; 
                // $count++;
            }

            $sst = $totalAmt * (8/100);
            $subTotalAmt = $totalAmt + $sst;

            $excelData .= '<tr>
                            <td class="body" colspan="11" style="border:none"></td>
                            <td class="body">SUB TOTAL</td>
                            <td class="body">RM ' . number_format(floatval($totalAmt), 2, '.', '') . '</td>
                        </tr>';

            $excelData .= '<tr>
                            <td class="body" colspan="11" style="border:none"></td>
                            <td class="body">SST 8%</td>
                            <td class="body">RM ' . number_format(floatval($sst), 2, '.', '') . '</td>
                        </tr>';

            $excelData .= '<tr>
                            <td class="body" colspan="11" style="border:none"></td>
                            <td class="body">TOTAL AMOUNT</td>
                            <td class="body">RM ' . number_format(floatval($subTotalAmt), 2, '.', '') . '</td>
                        </tr>';

            $excelData .= '<tr>
                        </tr>';
        } 

        $excelData .= "</table>";
        // Fetch each row
        $select_stmt->close();
        $db->close();
    } 
    else {
        $select_stmt->close();
        $db->close();
        $excelData .= 'No records found...'. "\n"; 
    }
}
else{
    $excelData .= 'No records found...'. "\n"; 
}

// Headers for download 
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$fileName\"");
 
// Render excel data 
echo $excelData; 
 
exit;
?>