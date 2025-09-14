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
    $type = $_GET['type'];

    if($_GET['fromDate'] != null && $_GET['fromDate'] != ''){
        $dateTime = DateTime::createFromFormat('d/m/Y', $_GET['fromDate']);
        $fromDateTime = $dateTime->format('Y-m-d 00:00:00');
        if ($type == 'Stamping'){
            $searchQuery = " and s.stamping_date >= '".$fromDateTime."'";
        } else if ($type == 'Other'){
            $searchQuery = " and o.last_calibration_date >= '".$fromDateTime."'";
        } else if ($type == 'Inhouse'){
            $searchQuery = " and a.validation_date >= '".$fromDateTime."'";
        }
    }
    if($_GET['toDate'] != null && $_GET['toDate'] != ''){
        $dateTime = DateTime::createFromFormat('d/m/Y', $_GET['toDate']);
        $toDateTime = $dateTime->format('Y-m-d 23:59:59');
        if ($type == 'Stamping'){
            $searchQuery .= " and s.stamping_date <= '".$toDateTime."'";
        } else if ($type == 'Other'){
            $searchQuery .= " and o.last_calibration_date <= '".$toDateTime."'";
        } else if ($type == 'Inhouse'){
            $searchQuery .= " and a.validation_date <= '".$toDateTime."'";
        }
    }

    if($_GET['customer'] != null && $_GET['customer'] != '' && $_GET['customer'] != '-'){
        if ($type == 'Stamping'){
            $searchQuery .= " and s.customers = '".$_GET['customer']."'";
        } else if ($type == 'Other'){
            $searchQuery .= " and o.customer = '".$_GET['customer']."'";
        } else if ($type == 'Inhouse'){
            $searchQuery .= " and a.customer = '".$_GET['customer']."'";
        }
    }

    if($_GET['validator'] != null && $_GET['validator'] != '' && $_GET['validator'] != '-'){
        if ($type == 'Stamping'){
            $searchQuery .= " and s.validate_by = '".$_GET['validator']."'";
        } else if ($type == 'Other'){
            $searchQuery .= " and o.validate_by = '".$_GET['validator']."'";
        } else if ($type == 'Inhouse'){
            $searchQuery .= " and a.validate_by = '".$_GET['validator']."'";
        }
    }

    if($_GET['branch'] != null && $_GET['branch'] != '' && $_GET['branch'] != '-'){
        if ($type == 'Stamping'){
            $searchQuery .= " and s.company_branch = '".$_GET['branch']."'";
        } else if ($type == 'Other'){
            $searchQuery .= " and o.company_branch = '".$_GET['branch']."'";
        } else if ($type == 'Inhouse'){
            $searchQuery .= " and a.company_branch = '".$_GET['branch']."'";
        }
    }

    if($type == 'Stamping'){
        $fileName = "Stamping_" . $_GET['fromDate'] . "-" . $_GET['toDate'] . ".xls";
        $select_stmt = $db->prepare("SELECT * FROM stamping s WHERE deleted = 0 ".$searchQuery." ORDER BY stamping_date ASC");
    } elseif ($type == 'Other'){
        $fileName = "Other_Validation_" . $_GET['fromDate'] . "-" . $_GET['toDate'] . ".xls";
        $select_stmt = $db->prepare("SELECT * FROM other_validations o WHERE deleted = 0 ".$searchQuery." ORDER BY last_calibration_date ASC");
    } else if ($type == 'Inhouse'){
        $fileName = "Inhouse_Validation_" . $_GET['fromDate'] . "-" . $_GET['toDate'] . ".xls";
        $select_stmt = $db->prepare("SELECT * FROM inhouse_validations a WHERE deleted = 0 ".$searchQuery." ORDER BY validation_date ASC");
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
                            <td colspan='36' style='font-size:16px;font-weight:bold;'>Overall Search Filters</td>
                        </tr>
                        <tr>
                            <th class='header' rowspan='2'>No</th>
                            <th class='header' rowspan='2'>Stamping Date</th>
                            <th class='header' rowspan='2'>Expire Date</th>
                            <th class='header' rowspan='2'>Last Year Stamping Date</th>
                            <th class='header' rowspan='2'>Company Branch</th>
                            <th class='header' rowspan='2'>Direct Customer / Reseller</th>
                            <th class='header' rowspan='2'>Reseller</th>
                            <th class='header' rowspan='2'>Customer</th>
                            <th class='header' rowspan='2'>Stamping Type</th>
                            <th class='header' rowspan='2'>Brand</th>
                            <th class='header' rowspan='2'>Model</th>
                            <th class='header' rowspan='2'>Machine Type</th>
                            <th class='header' rowspan='2'>Validator</th>
                            <th class='header' rowspan='2'>Jenis Alat</th>
                            <th class='header' rowspan='2'>Make In</th>
                            <th class='header' rowspan='2'>Cawangan</th>
                            <th class='header' colspan='2' style='border-bottom: 1px solid black;'>Capacity</th>
                            <th class='header' rowspan='2'>Machine Serial No</th>
                            <th class='header' rowspan='2'>Machine Name</th>
                            <th class='header' rowspan='2'>Machine Location Area</th>
                            <th class='header' rowspan='2'>Machine Area</th>
                            <th class='header' rowspan='2'>No Daftar (Lama)</th>
                            <th class='header' rowspan='2'>No Daftar (Baru)</th>
                            <th class='header' rowspan='2'>Seal No (Lama)</th>
                            <th class='header' rowspan='2'>Seal No (Baru)</th>
                            <th class='header' rowspan='2'>Borang (E)</th>
                            <th class='header' rowspan='2'>Borang (E) Date</th>
                            <th class='header' rowspan='2'>Borang (D)</th>
                            <th class='header' rowspan='2'>Siri Keselamatan</th>
                            <th class='header' rowspan='2'>Nama Pegawai / Contact</th>
                            <th class='header' rowspan='2'>Certificate No</th>
                            <th class='header' rowspan='2'>Assigned To Technician 1</th>
                            <th class='header' rowspan='2'>Assigned To Technician 2</th>
                            <th class='header' rowspan='2'>Assigned To Technician 3</th>
                            <th class='header' rowspan='2'>Status</th>
                        </tr>
                        <tr>
                            <td class='header'>SINGLE</td>
                            <td class='header'>MULTI</td>
                        </tr>
                        ";

            $no = 1;
            while ($row = $result->fetch_assoc()) {
                $formattedStampingDate = !empty($row['stamping_date']) ? (new DateTime($row['stamping_date']))->format('d-m-Y') : '';
                $formattedDueDate = !empty($row['due_date']) ? (new DateTime($row['due_date']))->format('d-m-Y') : '';
                $formattedLastYearStampingDate = !empty($row['last_year_stamping_date']) ? (new DateTime($row['last_year_stamping_date']))->format('d-m-Y') : '';
                $formattedBorangEDate = !empty($row['borang_e_date']) ? (new DateTime($row['borang_e_date']))->format('d-m-Y') : '';

                $excelData .= '
                            <tr>
                                <td class="body">'.$no.'</td>
                                <td class="body">'.$formattedStampingDate.'</td>
                                <td class="body">'.$formattedDueDate.'</td>
                                <td class="body">'.$formattedLastYearStampingDate.'</td>
                                <td class="body">'.searchCompanyBranchAddressById($row['company_branch'], $db).'</td>
                                <td class="body">'.$row['type'].'</td>
                                <td class="body">'.searchResellerAddressById($row['dealer'], $db).'</td>
                                <td class="body"><b>'.searchCustNameById($row['customers'], $db).'</b><br>'.searchCustomerBranchAddressById2($row['branch'], $db).'</td>
                                <td class="body">'.$row['stamping_type'].'</td>
                                <td class="body">'.searchBrandNameById($row['brand'], $db).'</td>
                                <td class="body">'.searchModelNameById($row['model'], $db).'</td>
                                <td class="body">'.searchMachineNameById($row['machine_type'], $db).'</td>
                                <td class="body">'.searchValidatorNameById($row['validate_by'], $db).'</td>
                                <td class="body">'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                                <td class="body">'.searchCountryById($row['make_in'], $db).'</td>
                                <td class="body">'.searchStateNameById($row['cawangan'], $db).'</td>
                                <td class="body">'.searchCapacityNameById($row['capacity'], $db).'</td>
                                <td class="body">'.searchCapacityNameById($row['capacity_high'], $db).'</td>
                                <td class="body">'.$row['serial_no'].'</td>
                                <td class="body">'.searchMachinenameNameById($row['machine_name'], $db).'</td>
                                <td class="body">'.$row['machine_location'].'</td>
                                <td class="body">'.$row['machine_area'].'</td>
                                <td class="body">'.$row['no_daftar_lama'].'</td>
                                <td class="body">'.$row['no_daftar_baru'].'</td>
                                <td class="body">'.$row['seal_no_lama'].'</td>
                                <td class="body">'.$row['seal_no_baru'].'</td>
                                <td class="body">'.$row['borang_e'].'</td>
                                <td class="body">'.$formattedBorangEDate.'</td>
                                <td class="body">'.$row['borang_d'].'</td>
                                <td class="body">'.$row['siri_keselamatan'].'</td>
                                <td class="body">'.$row['pegawai_contact'].'</td>
                                <td class="body">'.$row['cert_no'].'</td>
                                <td class="body">'.searchStaffNameById($row['assignTo'], $db).'</td>
                                <td class="body">'.searchStaffNameById($row['assignTo2'], $db).'</td>
                                <td class="body">'.searchStaffNameById($row['assignTo3'], $db).'</td>
                                <td class="body">'.$row['status'].'</td>
                            </tr>';
                $no++;
            }
        }else if($type == 'Other'){
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
                            <td colspan='18' style='font-size:16px;font-weight:bold;'>Overall Search Filters</td>
                        </tr>
                        <tr>
                            <th class='header'>No</th>
                            <th class='header'>Last Calibration Date</th>
                            <th class='header'>Expired Calibration Date</th>
                            <th class='header'>Company Branch</th>
                            <th class='header'>Direct Customer / Reseller</th>
                            <th class='header'>Reseller</th>
                            <th class='header'>Customer</th>
                            <th class='header'>Validator</th>
                            <th class='header'>Machines / Instruments</th>
                            <th class='header'>Jenis Alat</th>
                            <th class='header'>Brand</th>
                            <th class='header'>Model</th>
                            <th class='header'>Capacity</th>
                            <th class='header'>Unit Serial No</th>
                            <th class='header'>Manufacturing</th>
                            <th class='header'>Structure Size</th>
                            <th class='header'>Certificate No</th>
                            <th class='header'>Status</th>
                        </tr>
                        ";

            $no = 1;
            while ($row = $result->fetch_assoc()) {
                $lastCalibrationDate = !empty($row['last_calibration_date']) ? (new DateTime($row['last_calibration_date']))->format('d-m-Y') : '';
                $expiredCalibrationDate = !empty($row['expired_calibration_date']) ? (new DateTime($row['expired_calibration_date']))->format('d-m-Y') : '';

                $excelData .= '
                            <tr>
                                <td class="body">'.$no.'</td>
                                <td class="body">'.$lastCalibrationDate.'</td>
                                <td class="body">'.$expiredCalibrationDate.'</td>
                                <td class="body">'.searchCompanyBranchAddressById($row['company_branch'], $db).'</td>
                                <td class="body">'.$row['type'].'</td>
                                <td class="body">'.searchResellerAddressById($row['dealer'], $db).'</td>
                                <td class="body"><b>'.searchCustNameById($row['customer'], $db).'</b><br>'.searchCustomerBranchAddressById2($row['branch'], $db).'</td>
                                <td class="body">'.searchValidatorNameById($row['validate_by'], $db).'</td>
                                <td class="body">'.searchMachineNameById($row['machines'], $db).'</td>
                                <td class="body">'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                                <td class="body">'.searchBrandNameById($row['brand'], $db).'</td>
                                <td class="body">'.searchModelNameById($row['model'], $db).'</td>
                                <td class="body">'.searchCapacityNameById($row['capacity'], $db).'</td>
                                <td class="body">'.$row['unit_serial_no'].'</td>
                                <td class="body">'.$row['manufacturing'].'</td>
                                <td class="body">'.searchSizeNameById($row['size'], $db).'</td>
                                <td class="body">'.$row['auto_form_no'].'</td>
                                <td class="body">'.$row['status'].'</td>
                            </tr>';
                $no++;
            }
        } else if ($type == 'Inhouse'){
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
                            <td colspan='22' style='font-size:16px;font-weight:bold;'>Overall Search Filters</td>
                        </tr>
                        <tr>
                            <th class='header'>No</th>
                            <th class='header'>Validation Date</th>
                            <th class='header'>Expired Date</th>
                            <th class='header'>Company Branch</th>
                            <th class='header'>Direct Customer / Reseller</th>
                            <th class='header'>Reseller</th>
                            <th class='header'>Customer</th>
                            <th class='header'>Auto Form No</th>
                            <th class='header'>Validator</th>
                            <th class='header'>Machines / Instruments</th>
                            <th class='header'>Jenis Alat</th>
                            <th class='header'>Manufacturing</th>
                            <th class='header'>Brand</th>
                            <th class='header'>Unit Serial No</th>
                            <th class='header'>Capacity</th>
                            <th class='header'>Model</th>
                            <th class='header'>Structure Size</th>
                            <th class='header'>Auto Certificate No / Sticker No</th>
                            <th class='header'>Inhouse Calibrator1</th>
                            <th class='header'>Inhouse Calibrator2</th>
                            <th class='header'>Inhouse Calibrator3</th>
                            <th class='header'>Status</th>
                        </tr>
                        ";

            $no = 1;
            while ($row = $result->fetch_assoc()) {
                $validationDate = !empty($row['validation_date']) ? (new DateTime($row['validation_date']))->format('d-m-Y') : '';
                $expiredDate = !empty($row['expired_date']) ? (new DateTime($row['expired_date']))->format('d-m-Y') : '';

                $excelData .= '
                            <tr>
                                <td class="body">'.$no.'</td>
                                <td class="body">'.$validationDate.'</td>
                                <td class="body">'.$expiredDate.'</td>
                                <td class="body">'.searchCompanyBranchAddressById($row['company_branch'], $db).'</td>
                                <td class="body">'.$row['type'].'</td>
                                <td class="body">'.searchResellerAddressById($row['dealer'], $db).'</td>
                                <td class="body"><b>'.searchCustNameById($row['customer'], $db).'</b><br>'.searchCustomerBranchAddressById2($row['branch'], $db).'</td>
                                <td class="body">'.$row['auto_form_no'].'</td>
                                <td class="body">'.searchValidatorNameById($row['validate_by'], $db).'</td>
                                <td class="body">'.searchMachineNameById($row['machines'], $db).'</td>
                                <td class="body">'.searchAlatNameById($row['jenis_alat'], $db).'</td>
                                <td class="body">'.$row['manufacturing'].'</td>
                                <td class="body">'.searchBrandNameById($row['brand'], $db).'</td>
                                <td class="body">'.$row['unit_serial_no'].'</td>
                                <td class="body">'.searchCapacityNameById($row['capacity'], $db).'</td>
                                <td class="body">'.searchModelNameById($row['model'], $db).'</td>
                                <td class="body">'.searchSizeNameById($row['size'], $db).'</td>
                                <td class="body">'.$row['auto_cert_no'].'</td>
                                <td class="body">'.searchStaffNameById($row['calibrator'], $db).'</td>
                                <td class="body">'.searchStaffNameById($row['calibrator2'], $db).'</td>
                                <td class="body">'.searchStaffNameById($row['calibrator3'], $db).'</td>
                                <td class="body">'.$row['status'].'</td>
                            </tr>';
                $no++;
            }
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