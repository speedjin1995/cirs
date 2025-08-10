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
    
    $type = $_GET['type'];
    if($type == 'Stamping'){
        $fileName = "Stamping_" . $_GET['fromDate'] . "-" . $_GET['toDate'] . ".xls";
        $select_stmt = $db->prepare("SELECT * FROM stamping WHERE deleted = 0 AND status = 'Complete' ".$searchQuery." ORDER BY stamping_date ASC");
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

        if($type == 'Stamping'){
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
                            <td colspan='25' style='font-size:16px;font-weight:bold;'>Overall Search Filters</td>
                        </tr>
                        <tr>
                            <th class='header'>No</th>
                            <th class='header'>Date</th>
                            <th class='header'>Expire Date</th>
                            <th class='header'>Direct Customer / Reseller</th>
                            <th class='header'>Customer</th>
                            <th class='header'>Stamping Type</th>
                            <th class='header'>Validator</th>
                            <th class='header'>Cawangan</th>
                            <th class='header'>Machine Type</th>
                            <th class='header'>Jenis Alat</th>
                            <th class='header' colspan='2' style='border-bottom: 1px solid black;'>Capacity</th>
                            <th class='header'>No Daftar (Lama)</th>
                            <th class='header'>No Daftar (Baru)</th>
                            <th class='header'>Borang (E)</th>
                            <th class='header'>Borang (D)</th>
                            <th class='header'>Validator Invoice</th>
                            <th class='header'>Unit Price</th>
                            <th class='header'>Certificate Price</th>
                            <th class='header'>Total Amount Without SST</th>
                            <th class='header'>SST 8%</th>
                            <th class='header'>Sub Total With SST</th>
                            <th class='header'>Rebate %</th>
                            <th class='header'>Total Rebate Amount</th>
                            <th class='header'>Total Nett Amount</th>
                        </tr>
                        <tr>
                            <td colspan='10'></td>
                            <td class='header'>SINGLE</td>
                            <td class='header'>MULTI</td>
                            <td colspan='13'></td>
                        </tr>
                        ";

            $no = 1;
            $totalUnitPrice = 0;
            $totalCertPrice = 0;
            $totalAmount = 0;
            $totalSST = 0;
            $totalSubTotalSST = 0;
            $totalRebate = 0;
            $totalRebateAmount = 0;
            $totalSubTotalAmount = 0;
            $totalMetroJob = 0;
            $totalDeMetroJob = 0;
            $totalMetroAccount = 0;
            $totalDeMetroAccount = 0;
            $totalMetroRebatePerc = 0;
            $totalDeMetroRebatePerc = 0;
            $totalMetroRebateAmt = 0;
            $totalDeMetroRebateAmt = 0;
            while ($row = $result->fetch_assoc()) {
                $stampingDate = new DateTime($row['stamping_date']);
                $formattedStampingDate = $stampingDate->format('d-m-Y');
                $dueDate = new DateTime($row['due_date']);
                $formattedDueDate = $dueDate->format('d-m-Y');
                $totalUnitPrice += (float) $row['unit_price'] ?? 0;
                $totalCertPrice += (float) $row['cert_price'] ?? 0;
                $totalAmount += (float) $row['total_amount'] ?? 0;
                $totalSST += (float) $row['sst'] ?? 0;
                $totalSubTotalSST += (float) $row['subtotal_sst_amt'] ?? 0;
                $totalRebate += (float) $row['rebate'] ?? 0;
                $totalRebateAmount += (float) $row['rebate_amount'] ?? 0;
                $totalSubTotalAmount += (float) $row['subtotal_amount'] ?? 0;

                $excelData .= '
                            <tr>
                                <td class="body">'.$no.'</td>
                                <td class="body">'.$formattedStampingDate.'</td>
                                <td class="body">'.$formattedDueDate.'</td>
                                <td class="body">'.$row['type'].'</td>
                                <td class="body">'.searchCustNameById($row['customers'], $db).'</td>
                                <td class="body">'.$row['stamping_type'].'</td>
                                <td class="body">'.searchValidatorNameById($row['validate_by'], $db).'</td>
                                <td class="body">'.searchCountryById($row['make_in'], $db).'</td>
                                <td class="body">'.searchMachineNameById($row['machine_type'], $db).'</td>
                                <td class="body">'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                                <td class="body">'.searchCapacityNameById($row['capacity'], $db).'</td>
                                <td class="body">'.searchCapacityNameById($row['capacity_high'], $db).'</td>
                                <td class="body">'.$row['no_daftar_lama'].'</td>
                                <td class="body">'.$row['no_daftar_baru'].'</td>
                                <td class="body">'.$row['borang_e'].'</td>
                                <td class="body">'.$row['borang_d'].'</td>
                                <td class="body">'.$row['validator_invoice'].'</td>
                                <td class="body">'.'RM '.number_format(($row['unit_price'] ?? 0), 2).'</td>
                                <td class="body">'.'RM '.number_format(($row['cert_price'] ?? 0), 2).'</td>
                                <td class="body">'.'RM '.number_format(($row['total_amount'] ?? 0), 2).'</td>
                                <td class="body">'.'RM '.number_format(($row['sst'] ?? 0), 2).'</td>
                                <td class="body">'.'RM '.number_format(($row['subtotal_sst_amt'] ?? 0), 2).'</td>
                                <td class="body">'.($row['rebate'] ?? 0).' %</td>
                                <td class="body">'.'RM '.number_format(($row['rebate_amount'] ?? 0), 2).'</td>
                                <td class="body">'.'RM '.number_format(($row['subtotal_amount'] ?? 0), 2).'</td>
                            </tr>';

                // Summary Calculation
                if ($row['validate_by'] == 10) { // Metrology
                    if($row['rebate'] != null || $row['rebate'] != '' || $row['rebate'] != 0){
                        $totalMetroJob++;
                        $totalMetroAccount += (float) $row['subtotal_sst_amt'] ?? 0;
                        $totalMetroRebatePerc += (float) $row['rebate'] ?? 0;
                        $totalMetroRebateAmt += (float) $row['rebate_amount'] ?? 0;
                    }
                } elseif ($row['validate_by'] == 9) { // DE Metrology
                    if($row['rebate'] != null || $row['rebate'] != '' || $row['rebate'] != 0){
                        $totalDeMetroJob++;
                        $totalDeMetroAccount += (float) $row['subtotal_sst_amt'] ?? 0;
                        $totalDeMetroRebatePerc += (float) $row['rebate'] ?? 0;
                        $totalDeMetroRebateAmt += (float) $row['rebate_amount'] ?? 0;
                    }
                }
                $no++;
            }

            // Sub Total Row
            $excelData .= '
                        <tr>
                            <td colspan="16" class="body"></td>
                            <td class="body" style="font-weight: bold;">Total</td>
                            <td class="body" style="font-weight: bold;">'.'RM '.number_format($totalUnitPrice, 2).'</td>
                            <td class="body" style="font-weight: bold;">'.'RM '.number_format($totalCertPrice, 2).'</td>
                            <td class="body" style="font-weight: bold;">'.'RM '.number_format($totalAmount, 2).'</td>
                            <td class="body" style="font-weight: bold;">'.'RM '.number_format($totalSST, 2).'</td>
                            <td class="body" style="font-weight: bold;">'.'RM '.number_format($totalSubTotalSST, 2).'</td>
                            <td class="body" style="font-weight: bold;">'.$totalRebate.' %</td>
                            <td class="body" style="font-weight: bold;">'.'RM '.number_format($totalRebateAmount, 2).'</td>
                            <td class="body" style="font-weight: bold;">'.'RM '.number_format($totalSubTotalAmount, 2).'</td>
                        </tr>
                        </table><br>
                        ';

            // Bottom Summary Row
            $excelData .= '
            <table>
                <tr>
                    <td style="border:none;" colspan="20"></td>
                    <td class="header" colspan="5" style="border: 1px solid black;">SUMMARY VALIDATOR REBATE</td>
                </tr>
                <tr>
                    <td style="border:none;" colspan="20"></td>
                    <td class="header" style="border:1px solid black;">VALIDATOR</td>
                    <td class="header" style="border:1px solid black;">TOTAL JOB</td>
                    <td class="header" style="border:1px solid black;">TOTAL ACCOUNT</td>
                    <td class="header" style="border:1px solid black;">TOTAL % REBATE</td>
                    <td class="header" style="border:1px solid black;">TOTAL REBATE AMOUNT</td>
                </tr>
                <tr>
                    <td style="border:none;" colspan="20"></td>
                    <td class="body" style="border:1px solid black;">METROLOGY</td>
                    <td class="body" style="border:1px solid black;">'.$totalMetroJob.'</td>
                    <td class="body" style="border:1px solid black;">'.number_format($totalMetroAccount, 2).'</td>
                    <td class="body" style="border:1px solid black;">'.number_format($totalMetroRebatePerc, 2).' %</td>
                    <td class="body" style="border:1px solid black;">RM '.number_format($totalMetroRebateAmt, 2).'</td>
                </tr>
                <tr>
                    <td style="border:none;" colspan="20"></td>
                    <td class="body" style="border:1px solid black;">DE METROLOGY</td>
                    <td class="body" style="border:1px solid black;">'.$totalDeMetroJob.'</td>
                    <td class="body" style="border:1px solid black;">RM '.number_format($totalDeMetroAccount, 2).'</td>
                    <td class="body" style="border:1px solid black;">'.number_format($totalDeMetroRebatePerc, 2).' %</td>
                    <td class="body" style="border:1px solid black;">RM '.number_format($totalDeMetroRebateAmt, 2).'</td>
                </tr>
            ';
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