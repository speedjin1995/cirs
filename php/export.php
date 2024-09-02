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
    
    if($_GET['customer'] != null && $_GET['customer'] != '' && $_GET['customer'] != '-'){
        $searchQuery .= " and customers = '".$_GET['customer']."'";
    }
    

    $driver = $_GET['type'];
    $todayDate = date('d/m/Y');
    $todayDate2 = date('d M Y');
    $today = date("Y-m-d 00:00:00");

    $select_stmt = $db->prepare("SELECT * FROM stamping WHERE status = 'Complete'".$searchQuery);

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
            $fields = array('DATE', 'ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS', 'CAPACITY', 'QUANTITY', 'REGISTER NO.', 
            'CERTIFICATE NO./ NO. SIRI PELEKAT KESELAMATAN', 'NAME OF PURCHASE', 'ADDRESS'); 
                
            $excelData = implode("\t", array_values($fields)) . "\n"; 
            while ($row = $result->fetch_assoc()) {
                $lineData = array($todayDate2, searchBrandNameById($row['brand'], $db).'\n'.searchModelNameById($row['model'], $db).'\n'.searchAlatNameById($row['jenis_alat'], $db), 
                searchCapacityNameById($row['capacity'], $db), '1', $row['no_daftar'], $row['siri_keselamatan'], searchCustNameById($row['customers'], $db),
                $row['address1'].' '.$row['address2'].' '.$row['address3']);
                
                array_walk($lineData, 'filterData'); 
                $excelData .= implode("\t", array_values($lineData)) . "\n"; 
            }
        }
        else if($driver == '7'){
            $fields = array('BRG E BIL NO.', 'DATE', 'ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS', 'CAPACITY', 'LIST NO. (STMP. NO.)', 
            'REGISTER NO. (BARU / LAMA)', 'DETAILS OF REPAIR', 'CERTIFICATE NO./ NO. SIRI PELEKAT KESELAMATAN', 'NAME OF PURCHASE', 'ADDRESS', 
            'FEE');
            $excelData = implode("\t", array_values($fields)) . "\n"; 

            while ($row = $result->fetch_assoc()) {
                $lineData = array('', $todayDate2, searchBrandNameById($row['brand'], $db).'\n'.searchModelNameById($row['model'], $db).'\n'.searchAlatNameById($row['jenis_alat'], $db), 
                searchCapacityNameById($row['capacity'], $db), $row['pin_keselamatan'], $row['no_daftar'], 'SERVICE / STMP', $row['siri_keselamatan'], searchCustNameById($row['customers'], $db),
                $row['address1'].' '.$row['address2'].' '.$row['address3'], $row['unit_price']);
                
                array_walk($lineData, 'filterData'); 
                $excelData .= implode("\t", array_values($lineData)) . "\n"; 
            }
        }
        else{
            $rows = array();
            $count = 1;
            $validator = '2';

            $fields = array('Bil.', 'Jenis Alat', 'Had Terima', 'Jenama', 'No. Siri Alat', 
                'Nama Dan Alamat Pemilik', 'Kod', 'No. Daftar', 'No. Siri Pelekat Keselamatan',  
                'Fi / Bayaran');
            $excelData = implode("\t", array_values($fields)) . "\n"; 

            while ($row = $result->fetch_assoc()) {
                $validator = $row['validate_by'];

                $lineData = array($count, searchAlatNameById($row['jenis_alat'], $db), searchCapacityNameById($row['capacity'], $db), 
                searchBrandNameById($row['brand'], $db).'\n'.searchModelNameById($row['model'], $db).'\n'.searchAlatNameById($row['jenis_alat'], $db), 
                $row['serial_no'], searchCustNameById($row['customers'], $db).'\n'.$row['address1'].' '.$row['address2'].' '.$row['address3'], 
                $row['no_daftar'], $row['siri_keselamatan'], $row['unit_price']);
                
                array_walk($lineData, 'filterData'); 
                $excelData .= implode("\t", array_values($lineData)) . "\n"; 
                $count++;
            }
        } 

        // Fetch each row
        $select_stmt->close();
    } 
    else {
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