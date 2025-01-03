<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
require_once '../vendor/autoload.php'; 

use setasign\Fpdi\Fpdi;

class PDFWithEllipse extends \setasign\Fpdi\Fpdi {
    // Custom Ellipse function using Bézier curves
    function Ellipse($x, $y, $rx, $ry, $style='D', $fillColor = [255, 255, 255]) {
        $k = $this->k;
        $hp = $this->h;
        $op = ($style == 'F') ? 'f' : (($style == 'FD' || $style == 'DF') ? 'B' : 'S');
        $lx = 4 / 3 * (M_SQRT2 - 1) * $rx;
        $ly = 4 / 3 * (M_SQRT2 - 1) * $ry;
    
        // Set fill color if provided (assuming it's in RGB format)
        $this->SetFillColor($fillColor[0], $fillColor[1], $fillColor[2]);
    
        // Start the path
        $this->_out(sprintf('%.2F %.2F m', ($x + $rx) * $k, ($hp - $y) * $k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', 
            ($x + $rx) * $k, ($hp - ($y - $ly)) * $k,
            ($x + $lx) * $k, ($hp - ($y - $ry)) * $k,
            $x * $k, ($hp - ($y - $ry)) * $k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', 
            ($x - $lx) * $k, ($hp - ($y - $ry)) * $k,
            ($x - $rx) * $k, ($hp - ($y - $ly)) * $k,
            ($x - $rx) * $k, ($hp - $y) * $k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', 
            ($x - $rx) * $k, ($hp - ($y + $ly)) * $k,
            ($x - $lx) * $k, ($hp - ($y + $ry)) * $k,
            $x * $k, ($hp - ($y + $ry)) * $k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', 
            ($x + $lx) * $k, ($hp - ($y + $ry)) * $k,
            ($x + $rx) * $k, ($hp - ($y + $ly)) * $k,
            ($x + $rx) * $k, ($hp - $y) * $k));
    
        // Output the path with fill or stroke
        $this->_out($op);
    }    
}

$compids = '1';
$compname = 'SYNCTRONIX TECHNOLOGY (M) SDN BHD';
$compcert = 'PBJ000167/2019';
$compexp = '2027-09-28';
$compiemail = 'admin@synctronix.com.my';

$select_stmt2 = $db->prepare("SELECT * FROM companies WHERE id = '1'");
$select_stmt2->execute();
$result2 = $select_stmt2->get_result();

if ($res2 = $result2->fetch_assoc()) {
    $compname = $res2['name'];
    $compaddress = str_replace(["\r", "\n"], '', $res2['address']);
    $compcert = $res2['certno_lesen'];
    $compexp = $res2['tarikh_luput'];
    $noDaftarSyarikat = $res2['old_roc'];
    $companySignature = $res2['signature'];
}

// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
}

if(isset($_GET['userID'], $_GET["file"], $_GET["validator"])){
    $id = $_GET['userID'];
    $file = $_GET['file'];
    $validator = $_GET['validator'];
    $tickImage = '../assets/tick.png';
    $currentDateTime = date('d/m/Y - h:i:sA');  // Format: DD/MM/YYYY - HH:MM:SS AM/PM
    $currentDate = date('d/m/Y');  // Format: DD/MM/YYYY
    $currentTime = date('h:i:sA');  // Format: HH:MM:SS AM/PM

    if($file == 'ATK' && $validator == 'METROLOGY'){
        $fillFile = 'forms/Metrology/ATK_FORM.pdf';
        $pdf = new PDFWithEllipse();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");

        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';
            
            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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
                
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $pdf->AddPage();
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Helvetica', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->SetFont('', 'B', 10); // B stands for Bold
                        $pdf->SetXY(24.599, 61); // Adjust these coordinates for each field
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));

                        // Continue writing the rest of the text in normal font
                        $pdf->SetFont('', '', 10); // Normal
                        $pdf->SetXY($pdf->GetX(), 61); // Adjust Y coordinate if needed
                        $pdf->Write(0, ', ' . $address1 . ' ' . $address2);

                        // $pdf->Write(0, searchCustNameById($res['customers'], $db). ', '. $address1.' '.$address2); // {Address1}

                        $pdf->SetXY(24.599, 67); // Adjust these coordinates for each field
                        $pdf->Write(0, $address3.' '.$address4);

                        $pdf->SetXY(75, 100); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(50, 106); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(155, 106); // Adjust for {Tarikh_Tamat_Lesen}
                        $pdf->Write(0, $compexp);

                        $pdf->SetXY(72, 112); // Adjust for {Nama_Wakil_Pembaik}
                        $pdf->Write(0, searchStaffNameById($res['assignTo'], $db));

                        $pdf->SetXY(135, 112); // Adjust for {No_KP}
                        $pdf->Write(0, searchStaffICById($res['assignTo'], $db));

                        $pdf->SetXY(68, 126.5); // Adjust for {Penentusahan_Baru}
                        $pdf->Write(0, $res['penentusan_baru']);

                        $pdf->SetXY(158, 126.5); // Adjust for {Penentusahan_Semula}
                        $pdf->Write(0, $res['penentusan_semula']);

                        if($res['kelulusan_mspk'] == 'YES'){
                            $pdf->Image($tickImage, 47, 144, 6); // Adjust for {NoKelulusan_MSPK}
                        }
                        else{
                            $pdf->Image($tickImage, 47, 149, 6); // Adjust for {NoKelulusan_MSPK}
                        }
                        

                        $pdf->SetXY(71, 167.5); // Adjust for {Pembuat_Negara_Asal}
                        $pdf->Write(0, searchCountryById($res['platform_country'], $db));

                        $pdf->SetXY(133, 167.5); // Adjust for {Jenama}
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db));

                        $pdf->SetXY(42, 173); // Adjust for {Model#1}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(135, 173); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['indicator_serial']);

                        $pdf->SetXY(75, 187); // Adjust for {Pembuat_Negara_Asal_2}
                        $pdf->Write(0, searchCountryById($res['platform_country'], $db));

                        $pdf->SetXY(160, 187); // Adjust for {Jenis_Steel_Concrete}
                        $pdf->Write(0, $res['platform_type']);

                        $pdf->SetXY(75, 193); // Adjust for {size}
                        $pdf->Write(0, searchSizeNameById($res['size'], $db));

                        if($res['jenis_pelantar'] == 'Pit'){
                            $pdf->Ellipse(146, 193, 4, 3, 'D', [200, 255, 200]);
                        }
                        else{
                            $pdf->Ellipse(157, 193.5, 7, 3, 'D', [200, 255, 200]);
                        }

                        $pdf->SetXY(72, 213); // Adjust for {Pembuat_Negara_Asal_3}
                        $pdf->Write(0, searchCountryById($res['load_cell_country'], $db));

                        $pdf->SetXY(151, 213); // Adjust for {Bilangan_Load_Cell}
                        $pdf->Write(0, $res['load_cell_no']);

                        $count = 0;
                        for($i=0; $i<count($loadcells); $i++){
                            $pdf->SetXY(24, 226 + $count); // Adjust for {Bilangan_Load_Cell}
                            $pdf->Write(0, $loadcells[$i]['no']);

                            $pdf->SetXY(31, 226 + $count); // Adjust for {Bilangan_Load_Cell}
                            $pdf->Write(0, $loadcells[$i]['loadCellBrand']);

                            $pdf->SetXY(72, 226 + $count); // Adjust for {Bilangan_Load_Cell}
                            $pdf->Write(0, $loadcells[$i]['loadCellModel']);

                            $pdf->SetXY(111, 226 + $count); // Adjust for {Bilangan_Load_Cell}
                            $pdf->Write(0, $loadcells[$i]['loadCellCapacity']);

                            $pdf->SetXY(130, 226 + $count); // Adjust for {Bilangan_Load_Cell}
                            $pdf->Write(0, $loadcells[$i]['loadCellSerial']);

                            $count += 6;
                        }
                    }else if ($pageNo == 2){
                        $capacity = $res['capacity'];
                        $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                        $capacityDetail = mysqli_query($db, $capacityQuery);
                        $capacityRow = mysqli_fetch_assoc($capacityDetail);

                        if ($capacityRow['range_type'] == 'SINGLE'){
                            if (($capacityRow['capacity'] == "10000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "10" && $capacityRow['units'] == "1") && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 40.865, 6);
                            }
                            elseif (($capacityRow['capacity'] == "20000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "20" && $capacityRow['units'] == "1") && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 45.865, 6);
                            }
                            elseif (($capacityRow['capacity'] == "30000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "30" && $capacityRow['units'] == "1") && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 50.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "40000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "40" && $capacityRow['units'] == "1") && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 56.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "40000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "40" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 61.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "50000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "50" && $capacityRow['units'] == "1") && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 66.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "50000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "50" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 71.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "60000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "60" && $capacityRow['units'] == "1") && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 77.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "60000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "60" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 82.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "70000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "70" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 87.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "70000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "70" && $capacityRow['units'] == "1") && $capacityRow['division'] == '50' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 93.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "80000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "80" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 98.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "80000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "80" && $capacityRow['units'] == "1") && $capacityRow['division'] == '50' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 103.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "90000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "90" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 108.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "90000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "90" && $capacityRow['units'] == "1") && $capacityRow['division'] == '50' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 113.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "100000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "100" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 118.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "100000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "100" && $capacityRow['units'] == "1") && $capacityRow['division'] == '50' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 124.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "110000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "110" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 129.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "110000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "110" && $capacityRow['units'] == "1") && $capacityRow['division'] == '50' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 134.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "120000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "120" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 139.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "120000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "120" && $capacityRow['units'] == "1") && $capacityRow['division'] == '50' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 78.159, 144.865, 6);
                            }
                            else{
                                $pdf->SetFont('Helvetica', '', 10);
                                // Capacity
                                $pdf->SetXY(28, 159);
                                $pdf->Write(0, $capacityRow['capacity']. ' ' . searchUnitNameById($capacityRow['units'], $db));

                                // Division
                                $pdf->SetXY(54, 159);
                                $pdf->Write(0, $capacityRow['division']. ' ' . searchUnitNameById($capacityRow['division_unit'], $db));

                                $pdf->Image($tickImage, 78, 156, 6);

                            }
                        }else{
                            if ($capacityRow['capacity'] == "20" && $capacityRow['units'] == "1" && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "40" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '20' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 40.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "30" && $capacityRow['units'] == "1" && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "60" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '20' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 45.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "40" && $capacityRow['units'] == "1" && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "80" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '20' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 50.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "40" && $capacityRow['units'] == "1" && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "80" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '50' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 56.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "50" && $capacityRow['units'] == "1" && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "100" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '20' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 61.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "50" && $capacityRow['units'] == "1" && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "100" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '50' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 66.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "60" && $capacityRow['units'] == "1" && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "120" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '20' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 71.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "60" && $capacityRow['units'] == "1" && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "120" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '50' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 77.865, 6);
                            }
                        }

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 38, 200, 35);  // Adjust for company signature
                        }
                    }
                }
            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'ATK' && $validator == 'DE METROLOGY'){
        $fillFile = 'forms/DE_Metrology/DMSB_ATK.pdf';
        $pdf = new PDFWithEllipse();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping, stamping_ext WHERE stamping.id = stamping_ext.stamp_id AND stamping.id = '".$_GET['userID']."'");

        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';
            
            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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
                
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $pdf->AddPage();
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Helvetica', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->SetFont('', 'B', 10); // B stands for Bold
                        $pdf->SetXY(20, 69); // Adjust these coordinates for each field
                        $pdf->Write(0, searchCustNameById($res['customers'], $db)); // {Address1}
                        $pdf->SetFont('', '', 10); // B stands for Bold

                        $pdf->SetXY(20, 74); // Adjust for {Address2}
                        $pdf->Write(0, $address1.' '.$address2);

                        $pdf->SetXY(20, 79); // Adjust for {Address2}
                        $pdf->Write(0, $address3.' '.$address4);

                        $pdf->SetXY(20, 95); // Adjust for {Stamping_Address1}
                        $pdf->Write(0, $address1.' '.$address2);

                        $pdf->SetXY(20, 100); // Adjust for {Stamping_Address2}
                        $pdf->Write(0, $address3.' '.$address4);

                        $pdf->SetXY(68, 126); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(45, 132); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(60, 137); // Adjust for {Tarikh_Tamat_Lesen}
                        $pdf->Write(0, $compexp);

                        $pdf->SetXY(58, 142); // Adjust for {Nama_Wakil_Pembaik}
                        $pdf->Write(0, searchStaffNameById($res['assignTo'], $db));

                        $pdf->SetXY(38, 147); // Adjust for {No_KP}
                        $pdf->Write(0, searchStaffICById($res['assignTo'], $db));

                        $pdf->SetXY(60, 168); // Adjust for {Penentusahan_Baru}
                        $pdf->Write(0, $res['penentusan_baru']);

                        $pdf->SetXY(64, 173.5); // Adjust for {Penentusahan_Semula}
                        $pdf->Write(0, $res['penentusan_semula']);

                        if($res['kelulusan_mspk'] == 'YES'){
                            $pdf->SetXY(35, 205); // Adjust for {NoKelulusan_MSPK}
                            $pdf->Image($tickImage, 35, 202, 6);
                        }
                        else{
                            $pdf->SetXY(35, 210); // Adjust for {NoKelulusan_MSPK}
                            $pdf->Image($tickImage, 35, 208, 6);
                        }

                        $pdf->SetXY(66, 231); // Adjust for {Pembuat_Negara_Asal}
                        $pdf->Write(0, searchCountryById($res['platform_country'], $db));

                        $pdf->SetXY(40, 236.5); // Adjust for {Jenama}
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db));

                        $pdf->SetXY(36, 242); // Adjust for {Model#1}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(40, 247); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['indicator_serial']);
                    }
                    else if ($pageNo == 2){
                        $pdf->SetXY(66, 32); // Adjust for {Pembuat_Negara_Asal_2}
                        $pdf->Write(0, searchCountryById($res['platform_country'], $db));

                        $pdf->SetXY(66, 37); // Adjust for {Jenis_Steel_Concrete}
                        $pdf->Write(0, $res['platform_type']);

                        $pdf->SetXY(72, 42.5); // Adjust for {size}
                        $pdf->Write(0, searchSizeNameById($res['size'], $db));

                        if($res['jenis_pelantar'] == 'Pit'){
                            $pdf->Ellipse(53, 48, 4, 3, 'D', [200, 255, 200]);
                        }
                        else{
                            $pdf->Ellipse(64, 48, 7, 3, 'D', [200, 255, 200]);
                        }

                        $pdf->SetXY(68, 74); // Adjust for {Pembuat_Negara_Asal_3}
                        $pdf->Write(0, searchCountryById($res['load_cell_country'], $db));

                        $pdf->SetXY(60, 79); // Adjust for {Bilangan_Load_Cell}
                        $pdf->Write(0, $res['load_cell_no']);

                        $count = 0;
                        for($i=0; $i<count($loadcells); $i++){
                            $pdf->SetXY(37, 103 + $count); // Adjust for {Bilangan_Load_Cell}
                            $pdf->Write(0, $loadcells[$i]['loadCellBrand']);

                            $pdf->SetXY(77, 103 + $count); // Adjust for {Bilangan_Load_Cell}
                            $pdf->Write(0, $loadcells[$i]['loadCellModel']);

                            $pdf->SetXY(115, 103 + $count); // Adjust for {Bilangan_Load_Cell}
                            $pdf->Write(0, $loadcells[$i]['loadCellCapacity']);

                            $pdf->SetXY(153, 103 + $count); // Adjust for {Bilangan_Load_Cell}
                            $pdf->Write(0, $loadcells[$i]['loadCellSerial']);

                            $count += 10;
                        }
                    }
                    else if ($pageNo == 3){
                        $capacity = $res['capacity'];
                        $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                        $capacityDetail = mysqli_query($db, $capacityQuery);
                        $capacityRow = mysqli_fetch_assoc($capacityDetail);

                        if ($capacityRow['range_type'] == 'SINGLE'){
                            if (($capacityRow['capacity'] == "10000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "10" && $capacityRow['units'] == "1") && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 44.865, 6);
                            }
                            elseif (($capacityRow['capacity'] == "20000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "20" && $capacityRow['units'] == "1") && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 49.865, 6);
                            }
                            elseif (($capacityRow['capacity'] == "30000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "30" && $capacityRow['units'] == "1") && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 55.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "40000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "40" && $capacityRow['units'] == "1") && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 61.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "40000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "40" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 67.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "50000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "50" && $capacityRow['units'] == "1") && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 72.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "50000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "50" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 78.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "60000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "60" && $capacityRow['units'] == "1") && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 84.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "60000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "60" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 90.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "70000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "70" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 96.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "70000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "70" && $capacityRow['units'] == "1") && $capacityRow['division'] == '50' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 102.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "80000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "80" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 107.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "80000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "80" && $capacityRow['units'] == "1") && $capacityRow['division'] == '50' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 113.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "90000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "90" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 119.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "90000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "90" && $capacityRow['units'] == "1") && $capacityRow['division'] == '50' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 125.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "100000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "100" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 130.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "100000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "100" && $capacityRow['units'] == "1") && $capacityRow['division'] == '50' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 136.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "110000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "110" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 142.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "110000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "110" && $capacityRow['units'] == "1") && $capacityRow['division'] == '50' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 148.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "120000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "120" && $capacityRow['units'] == "1") && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 153.865, 6);
                            }
                            elseif(($capacityRow['capacity'] == "120000" && $capacityRow['units'] == "2" || $capacityRow['capacity'] == "120" && $capacityRow['units'] == "1") && $capacityRow['division'] == '50' && $capacityRow['division_unit'] == '2'){
                                $pdf->Image($tickImage, 82.159, 159.865, 6);
                            }
                            else{
                                $pdf->SetFont('Helvetica', '', 10);
                                // Capacity
                                $pdf->SetXY(28, 175);
                                $pdf->Write(0, $capacityRow['capacity']. ' ' . searchUnitNameById($capacityRow['units'], $db));

                                // Division
                                $pdf->SetXY(60, 175);
                                $pdf->Write(0, $capacityRow['division']. ' ' . searchUnitNameById($capacityRow['division_unit'], $db));

                                $pdf->Image($tickImage, 82, 171, 6);

                            }
                        }else{
                            if ($capacityRow['capacity'] == "20" && $capacityRow['units'] == "1" && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "40" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '20' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 44.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "30" && $capacityRow['units'] == "1" && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "60" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '20' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 50.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "40" && $capacityRow['units'] == "1" && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "80" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '20' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 55.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "40" && $capacityRow['units'] == "1" && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "80" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '50' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 61.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "50" && $capacityRow['units'] == "1" && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "100" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '20' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 67.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "50" && $capacityRow['units'] == "1" && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "100" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '50' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 72.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "60" && $capacityRow['units'] == "1" && $capacityRow['division'] == '10' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "120" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '20' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 78.865, 6);
                            }
                            elseif($capacityRow['capacity'] == "60" && $capacityRow['units'] == "1" && $capacityRow['division'] == '20' && $capacityRow['division_unit'] == '2' && $capacityRow['capacity2'] == "120" && $capacityRow['units2'] == "1" && $capacityRow['division2'] == '50' && $capacityRow['division_unit2'] == '2'){
                                $pdf->Image($tickImage, 178.159, 84.865, 6);
                            }
                        }

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 27, 215, 40);  // Adjust for company signature
                        }
                    }
                }
            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_de_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'ATP' && $validator == 'METROLOGY'){
        $fillFile = 'forms/Metrology/ATP_FORM.pdf';

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->SetXY(27, 24); 
                        $pdf->Write(0, $currentDate);

                        $pdf->Image($tickImage, 72.526, 60.865, 8);  // Adjust for Perdagangan

                        $pdf->SetXY(115.704, 57.5); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

                        $pdf->SetXY(127.704, 65); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->SetXY(14.648, 90.063); // Adjust for Customer Name
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 8);

                        $pdf->SetXY(14.648, 98.133); // Adjust for {3. Alamat Pemilik Address 1}
                        $pdf->Write(0, $address1);

                        $pdf->SetXY(14.648, 102.188); // Adjust for {3. Alamat Pemilik Address 2}
                        $pdf->Write(0, $address2);
                        
                        $pdf->SetXY(14.648, 105.258); // Adjust for {3. Alamat Pemilik Address 3 & 4}
                        $pdf->Write(0, $address3);

                        $pdf->SetXY(14.648, 108.258); // Adjust for {3. Alamat Pemilik Address 3 & 4}
                        $pdf->Write(0, $address4);

                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(120.872, 89.258); // Adjust for {Model}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetFont('Arial', '', 8);
                        $pdf->SetXY(22.648, 118.570); // Adjust for {company name}
                        $pdf->Write(0, $compname);
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(22.648, 143.570-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(22.648, 170.570); // Adjust for {No_Daftar_Syarikat}
                        $pdf->Write(0, $noDaftarSyarikat);

                        $pdf->SetXY(114.872, 97.258); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['serial_no']);

                        $pdf->SetXY(123.872, 106.570); // Adjust for {Had_Terima}
                        $pdf->Write(0, $capacityValue);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(174.872, 106.570, 10, 10, 'F'); 

                        $pdf->SetXY(125.872, 115.258); // Adjust for {Senggatan}
                        $pdf->Write(0, $capacityDivision);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(176.872, 108.570, 10, 10, 'F'); 

                        # Adjust for {Jenis Penunjuk}
                        if ($res['jenis_penunjuk'] == 'DIGITAL'){
                            $pdf->Image($tickImage, 109.141, 127.637, 8);
                        }elseif ($res['jenis_penunjuk'] == 'DAIL'){
                            $pdf->Image($tickImage, 134.141, 127.637, 8);
                        }

                        # Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 109.141, 170.637, 8);
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 134.141, 170.637, 8);
                        }

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 27.648, 187.637, 38.5);  // Adjust for company signature
                        }

                        // $pdf->SetXY(115.141 , 196.637); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $pdf->SetXY(125.141 , 204.637); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(134.141 , 212.637); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'ATP' && $validator == 'DE METROLOGY'){
        $fillFile = 'forms/DE_Metrology/DMSB_ATP.pdf';

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->Image($tickImage, 72.159, 55.865, 8); // Adjust for Kegunaan Alat

                        $pdf->SetXY(135.121, 53.954-2); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 
                        
                        $pdf->SetXY(147.112, 63.103-2); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetXY(15.666, 88.465-2); // Adjust for Customer Name
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(15.666, 94.901-2); // Adjust for {3. Alamat Pemilik Address 1 & 2}
                        $pdf->Write(0, $address1);

                        $pdf->SetXY(15.666, 101.418-2); // Adjust for {3. Alamat Pemilik Address 3}
                        $pdf->Write(0, $address2);
                        
                        $pdf->SetXY(15.666, 107.855-2); // Adjust for {3. Alamat Pemilik Address 4}
                        $pdf->Write(0, $address3 . ' ' . $address4);

                        $pdf->SetXY(15.666, 126.474-2); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        // $pdf->SetXY(132.084, 111.188-1.5); // Adjust for {jenis_alat}
                        // $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - '. searchMachineNameById($res['machine_type'], $db));

                        $pdf->SetXY(137.577, 72.413-2); // Adjust for {Model}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(15.666, 144.725-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(133.147, 81.900-2); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['serial_no']);

                        $pdf->SetXY(143.051, 93.842-2); // Adjust for {Had_Terima}
                        $pdf->Write(0, $capacityValue);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(170.852, 93.842-2, 40, 10, 'F');

                        $pdf->SetXY(143.677+2, 106.795-2); // Adjust for {Senggatan}
                        $pdf->Write(0, $capacityDivision);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(173.019, 106.795-2, 40, 10, 'F');

                        $pdf->SetXY(15.666, 162.670-2); // Adjust for {no_daftar}
                        $pdf->Write(0, $noDaftarSyarikat);

                        # Adjust for {Jenis Penunjuk}
                        if ($res['jenis_penunjuk'] == 'DIGITAL'){
                            $pdf->Image($tickImage, 132.422+12, 126.394-7, 8);
                        }elseif ($res['jenis_penunjuk'] == 'DAIL'){
                            $pdf->Image($tickImage, 163.876+12, 126.394-7, 8);
                        }

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 130.101+12, 144.677-7, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 164.035+12, 144.677-7, 8);
                        }

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 29.648, 188.637, 38.5);  // Adjust for company signature
                        }

                        // $pdf->SetXY(131.445, 162.670-2); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $pdf->SetXY(138.428, 172.029-2); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(150.788, 181.001-2); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);

                        
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_de_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'ATS' && $validator == 'METROLOGY'){
        $fillFile = 'forms/Metrology/ATS_FORM.pdf';

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->SetXY(29, 15); 
                        $pdf->Write(0, $currentDate); 

                        $pdf->Image($tickImage, 58.526, 74.865-2, 8);  // Adjust for Perdagangan

                        $pdf->SetXY(140.704, 67.5-1); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

                        $pdf->SetXY(150.704, 76.5); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->SetXY(18.648, 103.063-1); // Adjust for Customer Name
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(18.648, 107.133); // Adjust for {3. Alamat Pemilik Address 1}
                        $pdf->Write(0, $address1);

                        $pdf->SetXY(18.648, 111.188+1); // Adjust for {3. Alamat Pemilik Address 2}
                        $pdf->Write(0, $address2);
                        
                        $pdf->SetXY(18.648, 117.258); // Adjust for {3. Alamat Pemilik Address 3 & 4}
                        $pdf->Write(0, $address3 . ' ' . $address4);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(140.942, 90.294, 20, 10, 'F'); 
                        
                        $pdf->SetXY(140.942 , 100.294-2.4); // Adjust for {Jenis_alat}
                        $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - ');
                        
                        $pdf->SetXY(140.942 , 104.294-2.4); // Adjust for {Jenis_alat}
                        $pdf->Write(0, searchMachineNameById($res['machine_type'], $db));

                        $pdf->SetXY(140.942 , 110.294-3); // Adjust for {Model}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(46.648, 127.570); // Adjust for {company name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(16.648, 145.570-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(16.648, 165.570-2); // Adjust for {No_Daftar_Syarikat}
                        $pdf->Write(0, $noDaftarSyarikat);

                        $pdf->SetXY(140.872, 117.258); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['serial_no']);

                        $pdf->SetXY(148.872, 147.570); // Adjust for {Had_Terima}
                        $pdf->Write(0, $capacityValue);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(185.872, 147.570, 10, 10, 'F'); 

                        $pdf->SetXY(149.872, 157.258); // Adjust for {Senggatan}
                        $pdf->Write(0, $capacityDivision);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(190.872, 157.570, 10, 10, 'F'); 

                        # Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 125.141, 175.637, 8);
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 171.141, 175.637, 8);
                        }
                        
                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 28, 198, 42);  // Adjust for company signature
                        }

                        // $pdf->SetXY(140.141 , 205.637); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $pdf->SetXY(146.141 , 215.637); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(156.141 , 225.637); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'ATS' && $validator == 'DE METROLOGY'){
        $fillFile = 'forms/DE_Metrology/DMSB_ATS.pdf';

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions

                        $pdf->Image($tickImage, 65.159, 67.865, 8); // Adjust for Kegunaan Alat

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(130.635, 59.669-3, 70, 30, 'F');

                        $pdf->SetXY(130.635, 59.669-1); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(145.803, 93.956-3, 50, 15, 'F');  
                        
                        $pdf->SetXY(142.803, 93.956-1); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetXY(22.648, 103.063-2); // Adjust for Customer Name
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(22.648, 107.133-2); // Adjust for {3. Alamat Pemilik Address 1 & 2}
                        $pdf->Write(0, $address1);

                        $pdf->SetXY(22.648, 111.188-2); // Adjust for {3. Alamat Pemilik Address 3}
                        $pdf->Write(0, $address2);
                        
                        $pdf->SetXY(22.648, 115.258-2); // Adjust for {3. Alamat Pemilik Address 4}
                        $pdf->Write(0, $address3 . ' ' . $address4);

                        $pdf->SetXY(21.648, 132.490-2); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(132.084, 111.188-3, 40, 10, 'F');

                        $pdf->SetXY(132.084, 111.188-1.5); // Adjust for {jenis_alat}
                        $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - '. searchMachineNameById($res['machine_type'], $db));

                        $pdf->SetXY(130.942 , 120.294-2); // Adjust for {Model}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(22.648, 150.570-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(126.872, 128.420-2); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['serial_no']);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(145.803, 136.545-3, 55, 10, 'F');  

                        $pdf->SetXY(145.803, 136.545-1); // Adjust for {Had_Terima}
                        $pdf->Write(0, $capacityValue);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(145.803, 150.570-3, 55, 10, 'F');  

                        $pdf->SetXY(141.803, 149.570); // Adjust for {Senggatan}
                        $pdf->Write(0, $capacityDivision);

                        $pdf->SetXY(22.648 , 168.637-2); // Adjust for {no_daftar_syarikat}
                        $pdf->Write(0, $noDaftarSyarikat);

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 124.118+10, 168.637-7, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 155.141+10, 168.637-7, 8);
                        }

                        // $pdf->SetFillColor(0, 0, 0);  // cover up unneccesary text
                        // $pdf->Rect(16.938, 205.223-3, 50, 2, 'F');  
                        
                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 24, 184.223, 35);  // Adjust for company signature
                        }

                        // $pdf->SetXY(126.243 , 184.902-2); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $pdf->SetXY(132.582 , 193.027-2); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(143.884 , 201.153-2); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);

                        
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_de_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'ATE' && $validator == 'METROLOGY'){
        $fillFile = 'forms/Metrology/ATE_FORM.pdf';

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->SetXY(29, 20); 
                        $pdf->Write(0, $currentDate);

                        $pdf->Image($tickImage, 60.526, 68.865, 8);  // Adjust for Perdagangan

                        $pdf->SetXY(133.704, 63.5); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

                        $pdf->SetXY(142.704, 72.5); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->SetXY(16.648, 98.063); // Adjust for Customer Name
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(16.648, 107.133); // Adjust for {3. Alamat Pemilik Address 1}
                        $pdf->Write(0, $address1);

                        $pdf->SetXY(16.648, 115.188); // Adjust for {3. Alamat Pemilik Address 2}
                        $pdf->Write(0, $address2);
                        
                        $pdf->SetXY(16.648, 123.258); // Adjust for {3. Alamat Pemilik Address 3 & 4}
                        $pdf->Write(0, $address3 . ' ' . $address4);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(132.942, 87.294, 20, 5, 'F'); 
                        
                        $pdf->SetXY(134.942, 90.294); // Adjust for {Jenis_alat}
                        $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - '. searchMachineNameById($res['machine_type'], $db));

                        $pdf->SetXY(134.942 , 98.294); // Adjust for {Model}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(16.648, 140.570); // Adjust for {company name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(16.648, 157.570); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(16.648, 183.570); // Adjust for {No_Daftar_Syarikat}
                        $pdf->Write(0, $noDaftarSyarikat);

                        $pdf->SetXY(129.872, 106.258); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['serial_no']);

                        # Adjust for {Klass}
                        if ($res['class'] == 'II'){
                            $pdf->Image($tickImage, 145.141, 111.637, 8);
                        }elseif ($res['class'] == 'I'){
                            $pdf->Image($tickImage, 184.141, 111.637, 8);
                        }

                        $pdf->SetXY(138.872, 158.570); // Adjust for {Had_Terima}
                        $pdf->Write(0, $capacityValue);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(190.872, 152.570, 10, 10, 'F'); 

                        $pdf->SetXY(135.872, 167.258); // Adjust for {Senggatan}
                        $pdf->Write(0, $capacityDivision);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(190.872, 162.570, 10, 10, 'F'); 

                        # Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 145.141, 175.637, 8);
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 176.141, 175.637, 8);
                        }

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 30, 199, 35);  // Adjust for company signature
                        }

                        // $pdf->SetXY(126.141 , 201.5); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $pdf->SetXY(136.141 , 210.637); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(146.141 , 218.637); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'ATE' && $validator == 'DE METROLOGY'){
        $fillFile = 'forms/DE_Metrology/DMSB_ATE.pdf';

        $pdf = new PDFWithEllipse();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions

                        $pdf->Image($tickImage, 73.355, 75.048, 8); // Adjust for Kegunaan Alat

                        $pdf->SetXY(134.471, 71.272-2); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 
                        
                        $pdf->SetXY(145.686, 81.048-2); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetXY(19.668, 112.668-2); // Adjust for Customer Name
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(19.668, 118.725-2); // Adjust for {3. Alamat Pemilik Address 1 & 2}
                        $pdf->Write(0, $address1. ' ' .$address2);

                        $pdf->SetXY(19.668, 124.738-2); // Adjust for {3. Alamat Pemilik Address 3}
                        $pdf->Write(0, $address3);
                        
                        $pdf->SetXY(19.668 , 130.795-2); // Adjust for {3. Alamat Pemilik Address 4}
                        $pdf->Write(0, $address4);

                        $pdf->SetXY(14.982, 151.965-2); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(137.949, 91.843-3, 40, 5, 'F');

                        $pdf->SetXY(137.949, 91.843-1); // Adjust for {jenis_alat}
                        $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - '. searchMachineNameById($res['machine_type'], $db));

                        $pdf->SetXY(137.694, 101.843-2); // Adjust for {Model}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(14.982, 170.931-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(133.541, 112.668-2); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['serial_no']);

                        if ($res['class'] == 'II'){
                            $pdf->Image($tickImage, 129.422+12, 123.891, 12);
                        }else{
                            $pdf->Image($tickImage, 175.3, 123.891, 12);
                        }
                        
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(141.878, 146.418-2); // Adjust for {Had_Terima}
                        $pdf->Write(0, $capacityValue);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(167.830, 146.418-3, 10, 5, 'F');

                        // $pdf->SetXY(143.677+2, 106.795-2); // Adjust for {Senggatan}
                        // $pdf->Write(0, $capacityDivision);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(173.019, 106.795-2, 40, 10, 'F');

                        $pdf->SetXY(14.982, 187.694-2); // Adjust for {no_daftar}
                        $pdf->Write(0, $noDaftarSyarikat);

                        # Adjust for {Jenis Penunjuk}
                        if ($res['jenis_penunjuk'] == 'DIGITAL'){
                            $pdf->Image($tickImage, 132.422+12, 126.394-7, 8);
                        }elseif ($res['jenis_penunjuk'] == 'DAIL'){
                            $pdf->Image($tickImage, 163.876+12, 126.394-7, 8);
                        }

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 130.101+10, 168.677, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 164.035+10, 168.677, 8);
                        }

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 29.648, 199.637, 35.5);  // Adjust for company signature
                        }

                        // $pdf->SetXY(131.982, 191.832-2); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $pdf->SetXY(138.459, 200.228-2); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(150.004, 208.609-2); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);

                        
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_de_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'BTU' && $validator == 'METROLOGY'){
        $fillFile = 'forms/Metrology/BU_FORM.pdf';

        $pdf = new PDFWithEllipse();

        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                    $pdf->SetMargins(0, 0, 0); // Remove all margins
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->SetXY(27, 27); 
                        $pdf->Write(0, $currentDate);

                        $pdf->Image($tickImage, 88, 87, 8); 

                        $pdf->SetXY(135.902, 81.823-2); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetXY(124.182, 70.241-2); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

                        $pdf->SetXY(17.282, 124.716-2); // Adjust for Customer Name
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(17.282, 133.307-2); // Adjust for {3. Alamat Pemilik Address 1}
                        $pdf->Write(0, $address1);

                        $pdf->SetXY(17.282, 142.080-2); // Adjust for {3. Alamat Pemilik Address 2}
                        $pdf->Write(0, $address2);
                        
                        $pdf->SetXY(17.282, 149.871-2); // Adjust for {3. Alamat Pemilik Address 3 & 4}
                        $pdf->Write(0, $address3 . ' ' . $address4);

                        $pdf->SetXY(17.282, 166.599-2); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(17.282, 179.528-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(17.282, 191.873-2); // Adjust for {no_daftar}
                        $pdf->Write(0, $noDaftarSyarikat);

                        $pdf->SetXY(131.197, 170.163-2); // Adjust for {Nilai Jangkaan}
                        $pdf->Write(0, searchCapacityNameById($capacity,$db));

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(177.350, 170.163-4, 20, 7, 'F');

                        # Adjust for {Alat Type}
                        if ($res['batu_ujian'] == 'BESI_TUANGAN'){
                            $pdf->Image($tickImage, 148.238+22, 108.866-7, 8);
                        }elseif ($res['batu_ujian'] == 'TEMBAGA'){
                            $pdf->Image($tickImage, 148.238+22, 121.516-7, 8);
                        }elseif ($res['batu_ujian'] == 'NIKARAT'){
                            $pdf->Image($tickImage, 148.238+22, 133.995-7, 8);
                        }elseif ($res['batu_ujian'] == 'OTHER'){
                            $pdf->SetXY(128.526, 146.034-2); // Adjust for {Nilai Jangkaan}
                            $pdf->Write(0, $res['batu_ujian_lain']);                                 
                            $pdf->Image($tickImage, 148.238+22, 146.034-7, 8);
                        }

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 119.989+12, 187.292-7, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 158.178+12, 187.292-7, 8);
                        }

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 24.644, 215.922-4, 40.6);  // Adjust for company signature
                        }

                        // $pdf->SetXY(138.188, 215.438-2); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $pdf->SetXY(138.188, 224.002-2); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(118.188, 236.567-2); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'BTU' && $validator == 'DE METROLOGY'){
        $fillFile = 'forms/DE_Metrology/DMSB_BTU.pdf';

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->Image($tickImage, 57.395+12, 73.823-7, 8); 

                        $pdf->SetXY(153.902, 73.823-2); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetXY(142.182, 64.241-2); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

                        $pdf->SetXY(19.282, 102.516-2); // Adjust for Customer Name
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(19.282, 107.307-2); // Adjust for {3. Alamat Pemilik Address 1}
                        $pdf->Write(0, $address1);

                        $pdf->SetXY(19.282, 112.080-2); // Adjust for {3. Alamat Pemilik Address 2}
                        $pdf->Write(0, $address2);
                        
                        $pdf->SetXY(19.282, 116.871-2); // Adjust for {3. Alamat Pemilik Address 3 & 4}
                        $pdf->Write(0, $address3 . ' ' . $address4);

                        $pdf->SetXY(19.282, 136.599-2); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(19.282, 155.728-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(149.197, 146.163-2); // Adjust for {Nilai Jangkaan}
                        $pdf->Write(0, searchCapacityNameById($capacity,$db));

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(185.350, 146.163-4, 30, 7, 'F');

                        # Adjust for {Alat Type}
                        if ($res['batu_ujian'] == 'BESI_TUANGAN'){
                            $pdf->Image($tickImage, 154.238+22, 92.866-7, 8);
                        }elseif ($res['batu_ujian'] == 'TEMBAGA'){
                            $pdf->Image($tickImage, 154.238+22, 102.516-7, 8);
                        }elseif ($res['batu_ujian'] == 'NIKARAT'){
                            $pdf->Image($tickImage, 154.238+22, 111.995-7, 8);
                        }elseif ($res['batu_ujian'] == 'OTHER'){
                            $pdf->SetXY(135.526, 127.034-2); // Adjust for {Nilai Jangkaan}
                            $pdf->Write(0, $res['batu_ujian_lain']);                                 
                            $pdf->Image($tickImage, 154.238+22, 127.034-7, 8);
                        }
                         
                        $pdf->SetXY(19.282, 174.873-2); // Adjust for {no_daftar}
                        $pdf->Write(0, $noDaftarSyarikat);

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 127.989+12, 165.292-7, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 164.178+12, 165.292-7, 8);
                        }

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 21.644, 196.922-4, 40.6);  // Adjust for company signature
                        }

                        // $pdf->SetXY(148.188, 184.438-2); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $pdf->SetXY(155.596, 194.002-2); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(168.805, 203.567-2); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_de_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'ATN' && $validator == 'METROLOGY'){
        $fillFile = 'forms/Metrology/ATN_FORM.pdf';

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->SetXY(29, 10); 
                        $pdf->Write(0, $currentDate);

                        $pdf->SetXY(120.635, 59.669-2); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

                        $pdf->SetXY(125.635, 68.669-2); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->SetXY(8.648, 99.063-2); // Adjust for Customer Name
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(8.648, 103.133-2); // Adjust for {3. Alamat Pemilik Address 1}
                        $pdf->Write(0, $address1);

                        $pdf->SetXY(8.648, 107.588-2); // Adjust for {3. Alamat Pemilik Address 2}
                        $pdf->Write(0, $address2);
                        
                        $pdf->SetXY(8.648, 112.258-2); // Adjust for {3. Alamat Pemilik Address 3}
                        $pdf->Write(0, $address3);
                        
                        $pdf->SetXY(8.648, 116.258-2); // Adjust for {3. Alamat Pemilik Address 4}
                        $pdf->Write(0, $address4);

                        $pdf->SetXY(27.648, 137.490); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(33.648, 143); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        // Adjust for {Jenis_Alat}
                        if ($res['alat_type'] == 'PEDESTAL'){
                            $pdf->Image($tickImage, 127.118+10, 121.637-5, 8); 
                        }elseif ($res['alat_type'] == 'SUSPENDED'){
                            $pdf->Image($tickImage, 180.141+10, 121.637-5, 8);
                        }

                        // Adjust for {Bentuk_Dulang}
                        if ($res['bentuk_dulang'] == 'MANGKUK'){
                            $pdf->Image($tickImage, 127.118+10, 183.637-5, 8); 
                        }elseif ($res['bentuk_dulang'] == 'NON-MANGKUK'){
                            $pdf->Image($tickImage, 180.141+10, 183.637-5, 8);
                        }

                        $pdf->SetXY(120.118, 192.637); // Adjust for {Had_Terima}
                        $pdf->Write(0, $capacityValue);

                        $pdf->SetXY(182.141, 192.637); // Adjust for {Senggatan}
                        $pdf->Write(0, $capacityDivision);

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 127.118+10, 208.637-5, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 180.141+10, 208.637-5, 8);
                        }

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 17.648, 182.637, 40.6);  // Adjust for company signature
                        }

                        // $pdf->SetXY(112.243 , 228.902-2); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $state = explode(" ", searchStateNameById($res['cawangan'], $db));
                        // if (count($state) > 1){
                        //     $pdf->SetXY(182.582 , 225.027-2); // Adjust for {Cawangan}
                        //     $pdf->Write(0, $state[0]);

                        //     $pdf->SetXY(182.582 , 229.027-2); // Adjust for {Cawangan}
                        //     $pdf->Write(0, $state[1]);
                        // }else{
                        //     $pdf->SetXY(182.582 , 229.027-2); // Adjust for {Cawangan}
                        //     $pdf->Write(0, $state[0]);
                        // }

                        // $pdf->SetFont('Arial', '', 10);
                        // $pdf->SetXY(134.884 , 233.153-2); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'ATN' && $validator == 'DE METROLOGY'){
        $fillFile = 'forms/DE_Metrology/DMSB_ATN.pdf';

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(121.068, 50.067-3, 200.852-121.068, 81.911-50.067, 'F');  
                        
                        $pdf->SetXY(121.068, 50.067-1); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

                        $pdf->Image($tickImage, 49.343+10, 69.911-5, 8); 
                        
                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(136.829, 87.915-3, 60, 20, 'F');  

                        $pdf->SetXY(134.829, 87.915-1); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->SetXY(9.147, 107.211-2); // Adjust for Customer Name
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(9.147, 113.309-2); // Adjust for {3. Alamat Pemilik Address 1}
                        $pdf->Write(0, $address1);

                        $pdf->SetXY(9.147, 119.407-2); // Adjust for {3. Alamat Pemilik Address 2}
                        $pdf->Write(0, $address2);

                        $pdf->SetXY(9.147, 125.505-2); // Adjust for {3. Alamat Pemilik Address 3}
                        $pdf->Write(0, $address3);
                        
                        $pdf->SetXY(9.147, 131.603-2); // Adjust for {3. Alamat Pemilik Address 4}
                        $pdf->Write(0, $address4);
                        
                        $pdf->SetXY(19.902, 150.398-2); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(26.198, 156.496-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        // Adjust for {Jenis_Alat}
                        if ($res['alat_type'] == 'PEDESTAL'){
                            $pdf->Image($tickImage, 127.118+10, 126.637-5, 8); 
                        }elseif ($res['alat_type'] == 'SUSPENDED'){
                            $pdf->Image($tickImage, 180.141+10, 126.637-5, 8);
                        }

                        // Adjust for {Bentuk_Dulang}
                        if ($res['bentuk_dulang'] == 'MANGKUK'){
                            $pdf->Image($tickImage, 127.118+10, 173.637-5, 8); 
                        }elseif ($res['bentuk_dulang'] == 'NON-MANGKUK'){
                            $pdf->Image($tickImage, 180.141+10, 173.637-5, 8);
                        }

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(122.297, 201.535-3, 9, 15, 'F');  

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(136.829, 201.535-3, 60, 20, 'F');  

                        $pdf->SetXY(136.829, 201.535-1); // Adjust for {Had_Terima}
                        $pdf->Write(0, '('.$capacityValue.')');

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(120.143, 218.980-3, 5, 15, 'F');  

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(136.829, 218.980-3, 40, 20, 'F');  

                        $pdf->SetXY(130.829, 218.980-1); // Adjust for {Senggatan}
                        $pdf->Write(0, '('.$capacityDivision.')');

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 130.155+10, 245.724-7, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 162.299+10, 245.724-7, 8);
                        }

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 17.648, 184.637, 40.6);  // Adjust for company signature
                        }

                        // $pdf->SetXY(24.256, 239.641-2); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $pdf->SetXY(31.583, 245.724-2); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(43.051, 251.822-2); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_de_metrology_".$_GET['file']."_form.pdf");
    }
    // else if($file == 'ATL' && $validator == 'METROLOGY'){
    //     $fillFile = 'forms/Metrology/ATL_FORM.pdf';

    //     $pdf = new PDFWithEllipse();

    //     $pageCount = $pdf->setSourceFile($fillFile);

    //     $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
    //     // Check if the statement is prepared successfully
    //     if ($select_stmt) {
    //         // Bind variables to the prepared statement
    //         $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
    //         $select_stmt->execute();
    //         $result = $select_stmt->get_result();
    //         $message = '';

    //         if ($res = $result->fetch_assoc()) {
    //             $branch = $res['branch'];
    //             $loadcells = json_decode($res['load_cells_info'], true);
    //             $branchQuery = "SELECT * FROM branches WHERE id = $branch";
    //             $branchDetail = mysqli_query($db, $branchQuery);
    //             $branchRow = mysqli_fetch_assoc($branchDetail);

    //             $address1 = null;
    //             $address2 = null;
    //             $address3 = null;
    //             $address4 = null;
    //             $pic = null;
    //             $pic_phone = null;

    //             if(!empty($branchRow)){
    //                 $address1 = $branchRow['address'];
    //                 $address2 = $branchRow['address2'];
    //                 $address3 = $branchRow['address3'];
    //                 $address4 = $branchRow['address4'];
    //                 $pic = $branchRow['pic'];
    //                 $pic_phone = $branchRow['pic_contact'];
    //             }

    //             $capacity = $res['capacity'];
    //             $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
    //             $capacityDetail = mysqli_query($db, $capacityQuery);
    //             $capacityRow = mysqli_fetch_assoc($capacityDetail);

    //             $capacityValue = null;
    //             $capacityDivision = null;

    //             if(!empty($capacityRow)){
    //                 $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
    //                 $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
    //             }

    //             for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    //                 $templateId = $pdf->importPage($pageNo);
    //                 $size = $pdf->getTemplateSize($templateId);
    //                 $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    //                 $pdf->useTemplate($templateId);
    //                 $pdf->SetMargins(0, 0, 0); // Remove all margins
                
    //                 // Fill in the fields for the current page
    //                 $pdf->SetFont('Arial', '', 10);
                    
    //                 // Example field placements for each page (you'll adjust these according to your PDF)
    //                 if ($pageNo == 1) {
    //                     $pdf->SetFont('Arial', 'B', 10);
    //                     $pdf->SetXY(55.374, 53.063); // Adjust for Customer Name
    //                     $pdf->Write(0, searchCustNameById($res['customers'], $db));
    //                     $pdf->SetFont('Arial', '', 8);

    //                     $pdf->SetXY(55.374, 60.063); // Adjust for {Alamat Pemilik}
    //                     $pdf->Write(0, $address1 . ' ' . $address2 . ' ' . $address3 . ' ' . $address4);

    //                     $pdf->SetFont('Arial', 'B', 10);
    //                     $pdf->SetXY(55.374, 68.063); // Adjust for Company Name
    //                     $pdf->Write(0, $compname);

    //                     $pdf->SetFont('Arial', '', 8);
    //                     $pdf->SetXY(55.374, 75.063); // Adjust for Company address
    //                     $pdf->Write(0, $compaddress);

    //                     $pdf->SetFont('Arial', '', 10);

    //                     $pdf->SetXY(30.374, 100.872-2); // Adjust for {jenis_alat}
    //                     $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - '. searchModelNameById($res['model'], $db));

    //                     $pdf->SetXY(40.374, 85.872-2); // Adjust for {No_Lesen}
    //                     $pdf->Write(0, $compcert);

    //                     $pdf->SetXY(115.656, 105.080-2); // Adjust for {Nilai Jangkaan}
    //                     $pdf->Write(0, searchCapacityNameById($capacity,$db));

    //                     $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
    //                     $pdf->Rect(160.609, 105.080-2, 10, 7, 'F');

    //                     $pdf->SetXY(32.158, 107.106); // Adjust for {nama pembuat}
    //                     $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

    //                     # Adjust for {Keadaan Alat}
    //                     if ($res['stamping_type'] == 'NEW'){
    //                         $pdf->Image($tickImage, 116.253+22, 85.946-7, 8);
    //                     }elseif ($res['stamping_type'] == 'RENEWAL'){
    //                         $pdf->Image($tickImage, 159.854+22, 85.946-7, 8);
    //                     }

    //                     # Adjust for {Alat Type}
    //                     if ($res['alat_type'] == 'KERAS'){
    //                         $pdf->Image($tickImage, 116.253+22, 93.946-7, 8);
    //                     }elseif ($res['alat_type'] == 'LOGAM'){
    //                         $pdf->Image($tickImage, 159.854+22, 93.946-7, 8);
    //                     }

    //                     $pdf->SetXY(24.158, 123.106); // Adjust for {tarikh}
    //                     $pdf->Write(0, $currentDate);

    //                     $pdf->SetXY(24.158, 127.106); // Adjust for {tarikh}
    //                     $pdf->Write(0, $currentTime);

    //                     $pdf->SetXY(76.255, 123.106); // Adjust for {Cawangan}
    //                     $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

    //                     $pdf->SetXY(154.095, 123.106); // Adjust for {no_penentusahan}
    //                     $pdf->Write(0, $res['no_daftar']);
                        
    //                     $questions = json_decode($res['questions'], true);

    //                     $pdf->SetDrawColor(0, 0, 0); // Black outline
    //                     $pdf->SetLineWidth(0.5); // Set line width

    //                     if($questions[0]['answer'] == 'YA'){
    //                         $pdf->Ellipse(173.736, 147.537, 5, 3, 'D', [200, 255, 200]);
    //                     }elseif ($questions[0]['answer'] == 'TIDAK'){
    //                         $pdf->Ellipse(183.736, 147.537, 5, 3, 'D', [200, 255, 200]); 
    //                     }

    //                     if($questions[1]['answer'] == 'YA'){
    //                         $pdf->Ellipse(173.736, 160.537, 5, 3, 'D', [200, 255, 200]);
    //                     }elseif ($questions[1]['answer'] == 'TIDAK'){
    //                         $pdf->Ellipse(183.736, 160.537, 5, 3, 'D', [200, 255, 200]); 
    //                     }

    //                     if($questions[2]['answer'] == 'YA'){
    //                         $pdf->Ellipse(173.736, 168.537, 5, 3, 'D', [200, 255, 200]);
    //                     }elseif ($questions[2]['answer'] == 'TIDAK'){
    //                         $pdf->Ellipse(183.736, 168.537, 5, 3, 'D', [200, 255, 200]); 
    //                     }

    //                     if($questions[3]['answer'] == 'YA'){
    //                         $pdf->Ellipse(173.736, 180.537, 5, 3, 'D', [200, 255, 200]);
    //                     }elseif ($questions[3]['answer'] == 'TIDAK'){
    //                         $pdf->Ellipse(183.736, 180.537, 5, 3, 'D', [200, 255, 200]); 
    //                     }

    //                     if($questions[4]['answer'] == 'YA'){
    //                         $pdf->Ellipse(173.736, 205.537, 5, 3, 'D', [200, 255, 200]);
    //                     }elseif ($questions[4]['answer'] == 'TIDAK'){
    //                         $pdf->Ellipse(183.736, 205.537, 5, 3, 'D', [200, 255, 200]); 
    //                     }

    //                     if($questions[5]['answer'] == 'YA'){
    //                         $pdf->Ellipse(173.736, 217.537, 5, 3, 'D', [200, 255, 200]);
    //                     }elseif ($questions[5]['answer'] == 'TIDAK'){
    //                         $pdf->Ellipse(183.736, 217.537, 5, 3, 'D', [200, 255, 200]); 
    //                     }

    //                     if($questions[6]['answer'] == 'YA'){
    //                         $pdf->Ellipse(173.736, 229.537, 5, 3, 'D', [200, 255, 200]);
    //                     }elseif ($questions[6]['answer'] == 'TIDAK'){
    //                         $pdf->Ellipse(183.736, 229.537, 5, 3, 'D', [200, 255, 200]); 
    //                     }

    //                     if($questions[7]['answer'] == 'YA'){
    //                         $pdf->Ellipse(173.736, 241.537, 5, 3, 'D', [200, 255, 200]);
    //                     }elseif ($questions[7]['answer'] == 'TIDAK'){
    //                         $pdf->Ellipse(183.736, 241.537, 5, 3, 'D', [200, 255, 200]); 
    //                     }
    //                 }
    //             }

    //         }
    //     }
    //     else{
    //         echo json_encode(
    //             array(
    //                 "status"=> "failed", 
    //                 "message"=> "Failed to get the data"
    //             )
    //         ); 
    //     }

    //     $pdf->Output('D', "filled_metrology_".$_GET['file']."_form.pdf");
    // }
    // else if($file == 'ATL' && $validator == 'DE METROLOGY'){
    //     $fillFile = 'forms/DE_Metrology/DMSB_ATL.pdf';

    //     $pdf = new Fpdi();
    //     $pageCount = $pdf->setSourceFile($fillFile);

    //     $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
    //     // Check if the statement is prepared successfully
    //     if ($select_stmt) {
    //         // Bind variables to the prepared statement
    //         $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
    //         $select_stmt->execute();
    //         $result = $select_stmt->get_result();
    //         $message = '';

    //         if ($res = $result->fetch_assoc()) {
    //             $branch = $res['branch'];
    //             $loadcells = json_decode($res['load_cells_info'], true);
    //             $branchQuery = "SELECT * FROM branches WHERE id = $branch";
    //             $branchDetail = mysqli_query($db, $branchQuery);
    //             $branchRow = mysqli_fetch_assoc($branchDetail);

    //             $address1 = null;
    //             $address2 = null;
    //             $address3 = null;
    //             $address4 = null;
    //             $pic = null;
    //             $pic_phone = null;

    //             if(!empty($branchRow)){
    //                 $address1 = $branchRow['address'];
    //                 $address2 = $branchRow['address2'];
    //                 $address3 = $branchRow['address3'];
    //                 $address4 = $branchRow['address4'];
    //                 $pic = $branchRow['pic'];
    //                 $pic_phone = $branchRow['pic_contact'];
    //             }

    //             $capacity = $res['capacity'];
    //             $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
    //             $capacityDetail = mysqli_query($db, $capacityQuery);
    //             $capacityRow = mysqli_fetch_assoc($capacityDetail);

    //             $capacityValue = null;
    //             $capacityDivision = null;

    //             if(!empty($capacityRow)){
    //                 $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
    //                 $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
    //             }

    //             for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    //                 $templateId = $pdf->importPage($pageNo);
    //                 $size = $pdf->getTemplateSize($templateId);
    //                 $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    //                 $pdf->useTemplate($templateId);
                
    //                 // Fill in the fields for the current page
    //                 $pdf->SetFont('Arial', '', 10);
                    
    //                 // Example field placements for each page (you'll adjust these according to your PDF)
    //                 if ($pageNo == 1) {
    //                     // Fill in the fields at the appropriate positions
    //                     $pdf->SetXY(38.411, 104.333-2); // Adjust for nama pembuat
    //                     $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

    //                     $pdf->SetXY(65.378, 59.201-2); // Adjust for Customer Name
    //                     $pdf->SetFont('Arial', 'B', 10);
    //                     $pdf->Write(0, searchCustNameById($res['customers'], $db));
    //                     $pdf->SetFont('Arial', '', 10);

    //                     $pdf->SetXY(9.371 , 65.217-2); // Adjust for {3. Alamat Pemilik Address 1 & 2}
    //                     $pdf->Write(0, $address1 . ' ' . $address2);
                        
    //                     $pdf->SetXY(15.666, 71.188-2); // Adjust for {3. Alamat Pemilik Address 3 & 4}
    //                     $pdf->Write(0, $address3 . ' ' . $address4);

    //                     $pdf->SetXY(35.374, 87.872-2); // Adjust for {jenis_alat}
    //                     $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - '. searchModelNameById($res['model'], $db));

    //                     $pdf->SetXY(43.790, 79.352-2); // Adjust for {No_Lesen}
    //                     $pdf->Write(0, $compcert);

    //                     $pdf->SetXY(37.656, 96.080-2); // Adjust for {Nilai Jangkaan}
    //                     $pdf->Write(0, searchCapacityNameById($capacity,$db));

    //                     $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
    //                     $pdf->Rect(74.609, 96.080-2, 30, 7, 'F');

    //                     # Adjust for {Alat Type}
    //                     if ($res['alat_type'] == 'KERAS'){
    //                         $pdf->Image($tickImage, 127.253+22, 87.946-7, 8);
    //                     }elseif ($res['alat_type'] == 'LOGAM'){
    //                         $pdf->Image($tickImage, 136.854+22, 87.946-7, 8);
    //                     }

    //                     // Adjust for {Keadaan Alat}
    //                     if ($res['stamping_type'] == 'NEW'){
    //                         $pdf->Image($tickImage, 148.749, 79.352-7, 8); 
    //                     }elseif ($res['stamping_type'] == 'RENEWAL'){
    //                         $pdf->Image($tickImage, 192.251, 79.352-7, 8);
    //                     }

    //                     $pdf->SetFont('Arial', '', 9);
    //                     $pdf->SetXY(24.158, 119.106-2); // Adjust for {tarikh}
    //                     $pdf->Write(0, $currentDateTime);
    //                     $pdf->SetFont('Arial', '', 10);

    //                     $pdf->SetXY(86.255, 119.106-2); // Adjust for {Cawangan}
    //                     $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

    //                     $pdf->SetXY(171.095, 119.106-2); // Adjust for {no_penentusahan}
    //                     $pdf->Write(0, $res['no_daftar']);

    //                     $questions = json_decode($res['questions'], true);

    //                     if($questions[0]['answer'] == 'YA'){
    //                         $pdf->Image($tickImage, 140.736+10, 135.537-7, 8);
    //                     }elseif ($questions[0]['answer'] == 'TIDAK'){
    //                         $pdf->Image($tickImage, 170.621+10, 135.537-7, 8);
    //                     }

    //                     if($questions[1]['answer'] == 'YA'){
    //                         $pdf->Image($tickImage, 140.736+10, 156.147-7, 8);
    //                     }elseif ($questions[1]['answer'] == 'TIDAK'){
    //                         $pdf->Image($tickImage, 170.621+10, 156.147-7, 8);
    //                     }

    //                     if($questions[2]['answer'] == 'YA'){
    //                         $pdf->Image($tickImage, 140.736+10, 164.326-7, 8);
    //                     }elseif ($questions[2]['answer'] == 'TIDAK'){
    //                         $pdf->Image($tickImage, 170.621+10, 164.326-7, 8);
    //                     }

    //                     if($questions[3]['answer'] == 'YA'){
    //                         $pdf->Image($tickImage, 140.736+10, 176.684-7, 8);
    //                     }elseif ($questions[3]['answer'] == 'TIDAK'){
    //                         $pdf->Image($tickImage, 170.621+10, 176.684-7, 8);
    //                     }

    //                     if($questions[4]['answer'] == 'YA'){
    //                         $pdf->Image($tickImage, 140.736+10, 203.694-7, 8);
    //                     }elseif ($questions[4]['answer'] == 'TIDAK'){
    //                         $pdf->Image($tickImage, 170.621+10, 203.694-7, 8);
    //                     }

    //                     if($questions[5]['answer'] == 'YA'){
    //                         $pdf->Image($tickImage, 140.736+10, 228.409-7, 8);
    //                     }elseif ($questions[5]['answer'] == 'TIDAK'){
    //                         $pdf->Image($tickImage, 170.621+10, 228.409-7, 8);
    //                     }

    //                     if($questions[6]['answer'] == 'YA'){
    //                         $pdf->Image($tickImage, 140.736+10, 240.692-7, 8);
    //                     }elseif ($questions[6]['answer'] == 'TIDAK'){
    //                         $pdf->Image($tickImage, 170.621+10, 240.692-7, 8);
    //                     }

    //                     if($questions[7]['answer'] == 'YA'){
    //                         $pdf->Image($tickImage, 140.736+10, 261.228-7, 8);
    //                     }elseif ($questions[7]['answer'] == 'TIDAK'){
    //                         $pdf->Image($tickImage, 170.621+10, 261.228-7, 8);
    //                     }

    //                 }
    //             }

    //         }
    //     }
    //     else{
    //         echo json_encode(
    //             array(
    //                 "status"=> "failed", 
    //                 "message"=> "Failed to get the data"
    //             )
    //         ); 
    //     }

    //     $pdf->Output('D', "filled_de_metrology_".$_GET['file']."_form.pdf");
    // }
    // else if($file == 'ATP-AUTO MACHINE' && $validator == 'METROLOGY'){
    //     $fillFile = 'forms/Metrology/ATP_FORM.pdf';

    //     $pdf = new Fpdi();
    //     $pageCount = $pdf->setSourceFile($fillFile);

    //     $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
    //     // Check if the statement is prepared successfully
    //     if ($select_stmt) {
    //         // Bind variables to the prepared statement
    //         $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
    //         $select_stmt->execute();
    //         $result = $select_stmt->get_result();
    //         $message = '';

    //         if ($res = $result->fetch_assoc()) {
    //             $branch = $res['branch'];
    //             $loadcells = json_decode($res['load_cells_info'], true);
    //             $branchQuery = "SELECT * FROM branches WHERE id = $branch";
    //             $branchDetail = mysqli_query($db, $branchQuery);
    //             $branchRow = mysqli_fetch_assoc($branchDetail);

    //             $address1 = null;
    //             $address2 = null;
    //             $address3 = null;
    //             $address4 = null;
    //             $pic = null;
    //             $pic_phone = null;

    //             if(!empty($branchRow)){
    //                 $address1 = $branchRow['address'];
    //                 $address2 = $branchRow['address2'];
    //                 $address3 = $branchRow['address3'];
    //                 $address4 = $branchRow['address4'];
    //                 $pic = $branchRow['pic'];
    //                 $pic_phone = $branchRow['pic_contact'];
    //             }

    //             $capacity = $res['capacity'];
    //             $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
    //             $capacityDetail = mysqli_query($db, $capacityQuery);
    //             $capacityRow = mysqli_fetch_assoc($capacityDetail);

    //             $capacityValue = null;
    //             $capacityDivision = null;

    //             if(!empty($capacityRow)){
    //                 $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
    //                 $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
    //             }

    //             for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    //                 $templateId = $pdf->importPage($pageNo);
    //                 $size = $pdf->getTemplateSize($templateId);
    //                 $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    //                 $pdf->useTemplate($templateId);
                
    //                 // Fill in the fields for the current page
    //                 $pdf->SetFont('Arial', '', 10);
                    
    //                 // Example field placements for each page (you'll adjust these according to your PDF)
    //                 if ($pageNo == 1) {
    //                     // Fill in the fields at the appropriate positions
    //                     $pdf->Image($tickImage, 57.526, 65.865, 8);  // Adjust for Perdagangan

    //                     $pdf->SetXY(125.704, 59.5); // Adjust for Jenama
    //                     $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

    //                     $pdf->SetXY(135.704, 68); // Adjust for nama pembuat
    //                     $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

    //                     $pdf->SetFont('Arial', 'B', 10);
    //                     $pdf->SetXY(21.648, 97.063); // Adjust for Customer Name
    //                     $pdf->Write(0, searchCustNameById($res['customers'], $db));
    //                     $pdf->SetFont('Arial', '', 10);

    //                     $pdf->SetXY(21.648, 106.133); // Adjust for {3. Alamat Pemilik Address 1}
    //                     $pdf->Write(0, $address1);

    //                     $pdf->SetXY(21.648, 115.188); // Adjust for {3. Alamat Pemilik Address 2}
    //                     $pdf->Write(0, $address2);
                        
    //                     $pdf->SetXY(21.648, 124.258); // Adjust for {3. Alamat Pemilik Address 3 & 4}
    //                     $pdf->Write(0, $address3 . ' ' . $address4);

    //                     $pdf->SetXY(127.872, 76.258); // Adjust for {Model}
    //                     $pdf->Write(0, searchModelNameById($res['model'], $db));

    //                     $pdf->SetXY(21.648, 147.570); // Adjust for {company name}
    //                     $pdf->Write(0, $compname);

    //                     $pdf->SetXY(21.648, 162.570); // Adjust for {No_Lesen}
    //                     $pdf->Write(0, $compcert);

    //                     $pdf->SetXY(21.648, 188.570); // Adjust for {No_Daftar_Syarikat}
    //                     $pdf->Write(0, $noDaftarSyarikat);

    //                     $pdf->SetXY(122.872, 85.258); // Adjust for {No_Siri}
    //                     $pdf->Write(0, $res['serial_no']);

    //                     $pdf->SetXY(130.872, 93.570); // Adjust for {Had_Terima}
    //                     $pdf->Write(0, $capacityValue);

    //                     $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
    //                     $pdf->Rect(182.872, 92.570, 10, 20, 'F'); 

    //                     $pdf->SetXY(135.872, 111.258); // Adjust for {Senggatan}
    //                     $pdf->Write(0, $capacityDivision);

    //                     $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
    //                     $pdf->Rect(182.872, 110.570, 10, 6, 'F'); 

    //                     # Adjust for {Keadaan Alat}
    //                     if ($res['stamping_type'] == 'NEW'){
    //                         $pdf->Image($tickImage, 147.141, 179.637, 8);
    //                     }elseif ($res['stamping_type'] == 'RENEWAL'){
    //                         $pdf->Image($tickImage, 174.141, 177.637, 8);
    //                     }

    //                     $nilais = json_decode($res['nilais'], true);
    //                     $pdf->SetXY(116.872, 136.458); // Adjust for {nilai1}
    //                     $pdf->Write(0, $nilais[0]['nilai']);
    //                     $pdf->SetXY(116.872, 141.958); // Adjust for {nilai2}
    //                     $pdf->Write(0, $nilais[1]['nilai']);
    //                     $pdf->SetXY(116.872, 148.558); // Adjust for {nilai3}
    //                     $pdf->Write(0, $nilais[2]['nilai']);
    //                     $pdf->SetXY(116.872, 155.258); // Adjust for {nilai4}
    //                     $pdf->Write(0, $nilais[3]['nilai']);
    //                     $pdf->SetXY(116.872, 162.258); // Adjust for {nilai5}
    //                     $pdf->Write(0, $nilais[4]['nilai']);
    //                     $pdf->SetXY(116.872, 171.637); // Adjust for {nilai6}
    //                     $pdf->Write(0, $nilais[5]['nilai']);

    //                     $pdf->Image($companySignature, 35.648, 202.637, 30);  // Adjust for company signature

    //                     $pdf->SetXY(122.141 , 205.637); // Adjust for {tarikh}
    //                     $pdf->Write(0, $currentDateTime);

    //                     $pdf->SetXY(128.141 , 214.637); // Adjust for {Cawangan}
    //                     $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

    //                     $pdf->SetXY(142.141 , 223.637); // Adjust for {no_penentusahan}
    //                     $pdf->Write(0, $res['no_daftar']);
    //                 }
    //             }

    //         }
    //     }
    //     else{
    //         echo json_encode(
    //             array(
    //                 "status"=> "failed", 
    //                 "message"=> "Failed to get the data"
    //             )
    //         ); 
    //     }

    //     $pdf->Output('D', "filled_metrology_".$_GET['file']."_form.pdf");
    // }
    // else if($file == 'ATP-AUTO MACHINE' && $validator == 'DE METROLOGY'){
    //     $fillFile = 'forms/DE_Metrology/DMSB_ATP.pdf';

    //     $pdf = new Fpdi();
    //     $pageCount = $pdf->setSourceFile($fillFile);

    //     $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
    //     // Check if the statement is prepared successfully
    //     if ($select_stmt) {
    //         // Bind variables to the prepared statement
    //         $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
    //         $select_stmt->execute();
    //         $result = $select_stmt->get_result();
    //         $message = '';

    //         if ($res = $result->fetch_assoc()) {
    //             $branch = $res['branch'];
    //             $loadcells = json_decode($res['load_cells_info'], true);
    //             $branchQuery = "SELECT * FROM branches WHERE id = $branch";
    //             $branchDetail = mysqli_query($db, $branchQuery);
    //             $branchRow = mysqli_fetch_assoc($branchDetail);

    //             $address1 = null;
    //             $address2 = null;
    //             $address3 = null;
    //             $address4 = null;
    //             $pic = null;
    //             $pic_phone = null;

    //             if(!empty($branchRow)){
    //                 $address1 = $branchRow['address'];
    //                 $address2 = $branchRow['address2'];
    //                 $address3 = $branchRow['address3'];
    //                 $address4 = $branchRow['address4'];
    //                 $pic = $branchRow['pic'];
    //                 $pic_phone = $branchRow['pic_contact'];
    //             }

    //             $capacity = $res['capacity'];
    //             $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
    //             $capacityDetail = mysqli_query($db, $capacityQuery);
    //             $capacityRow = mysqli_fetch_assoc($capacityDetail);

    //             $capacityValue = null;
    //             $capacityDivision = null;

    //             if(!empty($capacityRow)){
    //                 $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
    //                 $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
    //             }

    //             for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    //                 $templateId = $pdf->importPage($pageNo);
    //                 $size = $pdf->getTemplateSize($templateId);
    //                 $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    //                 $pdf->useTemplate($templateId);
                
    //                 // Fill in the fields for the current page
    //                 $pdf->SetFont('Arial', '', 10);
                    
    //                 // Example field placements for each page (you'll adjust these according to your PDF)
    //                 if ($pageNo == 1) {
    //                     // Fill in the fields at the appropriate positions

    //                     $pdf->Image($tickImage, 72.159, 55.865, 8); // Adjust for Kegunaan Alat

    //                     $pdf->SetXY(135.121, 53.954-2); // Adjust for Jenama
    //                     $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 
                        
    //                     $pdf->SetXY(147.112, 63.103-2); // Adjust for nama pembuat
    //                     $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

    //                     $pdf->SetXY(15.666, 88.465-2); // Adjust for Customer Name
    //                     $pdf->SetFont('Arial', 'B', 10);
    //                     $pdf->Write(0, searchCustNameById($res['customers'], $db));
    //                     $pdf->SetFont('Arial', '', 10);

    //                     $pdf->SetXY(15.666, 94.901-2); // Adjust for {3. Alamat Pemilik Address 1 & 2}
    //                     $pdf->Write(0, $address1);

    //                     $pdf->SetXY(15.666, 101.418-2); // Adjust for {3. Alamat Pemilik Address 3}
    //                     $pdf->Write(0, $address2);
                        
    //                     $pdf->SetXY(15.666, 107.855-2); // Adjust for {3. Alamat Pemilik Address 4}
    //                     $pdf->Write(0, $address3 . ' ' . $address4);

    //                     $pdf->SetXY(15.666, 126.474-2); // Adjust for {Company_Name}
    //                     $pdf->Write(0, $compname);

    //                     // $pdf->SetXY(132.084, 111.188-1.5); // Adjust for {jenis_alat}
    //                     // $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - '. searchMachineNameById($res['machine_type'], $db));

    //                     $pdf->SetXY(137.577, 72.413-2); // Adjust for {Model}
    //                     $pdf->Write(0, searchModelNameById($res['model'], $db));

    //                     $pdf->SetXY(15.666, 144.725-2); // Adjust for {No_Lesen}
    //                     $pdf->Write(0, $compcert);

    //                     $pdf->SetXY(133.147, 81.900-2); // Adjust for {No_Siri}
    //                     $pdf->Write(0, $res['serial_no']);

    //                     $pdf->SetXY(143.051, 93.842-2); // Adjust for {Had_Terima}
    //                     $pdf->Write(0, $capacityValue);

    //                     $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
    //                     $pdf->Rect(170.852, 93.842-2, 40, 10, 'F');

    //                     $pdf->SetXY(143.677+2, 106.795-2); // Adjust for {Senggatan}
    //                     $pdf->Write(0, $capacityDivision);

    //                     $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
    //                     $pdf->Rect(173.019, 106.795-2, 40, 10, 'F');

    //                     $pdf->SetXY(15.666, 162.670-2); // Adjust for {no_daftar}
    //                     $pdf->Write(0, $noDaftarSyarikat);

    //                     # Adjust for {Jenis Penunjuk}
    //                     if ($res['jenis_penunjuk'] == 'DIGITAL'){
    //                         $pdf->Image($tickImage, 132.422+12, 126.394-7, 8);
    //                     }elseif ($res['jenis_penunjuk'] == 'DAIL'){
    //                         $pdf->Image($tickImage, 163.876+12, 126.394-7, 8);
    //                     }

    //                     // Adjust for {Keadaan Alat}
    //                     if ($res['stamping_type'] == 'NEW'){
    //                         $pdf->Image($tickImage, 130.101+12, 144.677-7, 8); 
    //                     }elseif ($res['stamping_type'] == 'RENEWAL'){
    //                         $pdf->Image($tickImage, 164.035+12, 144.677-7, 8);
    //                     }

    //                     $pdf->Image($companySignature, 29.648, 188.637, 38.5);  // Adjust for company signature

    //                     $pdf->SetXY(131.445, 162.670-2); // Adjust for {tarikh}
    //                     $pdf->Write(0, $currentDateTime);

    //                     $pdf->SetXY(138.428, 172.029-2); // Adjust for {Cawangan}
    //                     $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

    //                     $pdf->SetXY(150.788, 181.001-2); // Adjust for {no_penentusahan}
    //                     $pdf->Write(0, $res['no_daftar']);

                        
    //                 }
    //             }

    //         }
    //     }
    //     else{
    //         echo json_encode(
    //             array(
    //                 "status"=> "failed", 
    //                 "message"=> "Failed to get the data"
    //             )
    //         ); 
    //     }

    //     $pdf->Output('D', "filled_de_metrology_".$_GET['file']."_form.pdf");
    // }
    else if($file == 'ATP-AUTO MACHINE' && $validator == 'METROLOGY'){
        $fillFile = 'forms/Metrology/ATP_FORM.pdf';

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->SetXY(27, 24); 
                        $pdf->Write(0, $currentDate); 

                        $pdf->Image($tickImage, 72.526, 60.865, 8);  // Adjust for Perdagangan

                        $pdf->SetXY(115.704, 57.5); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

                        $pdf->SetXY(127.704, 65); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetFont('Arial', 'B', 8);
                        $pdf->SetXY(14.648, 90.063); // Adjust for Customer Name
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 8);

                        $pdf->SetXY(14.648, 98.133); // Adjust for {3. Alamat Pemilik Address 1}
                        $pdf->Write(0, $address1);

                        $pdf->SetXY(14.648, 102.188); // Adjust for {3. Alamat Pemilik Address 2}
                        $pdf->Write(0, $address2);
                        
                        $pdf->SetXY(14.648, 105.258); // Adjust for {3. Alamat Pemilik Address 3 & 4}
                        $pdf->Write(0, $address3);

                        $pdf->SetXY(14.648, 108.258); // Adjust for {3. Alamat Pemilik Address 3 & 4}
                        $pdf->Write(0, $address4);

                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(120.872, 89.258); // Adjust for {Model}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetFont('Arial', '', 8);
                        $pdf->SetXY(22.648, 118.570); // Adjust for {company name}
                        $pdf->Write(0, $compname);
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(22.648, 143.570-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(22.648, 170.570); // Adjust for {No_Daftar_Syarikat}
                        $pdf->Write(0, $noDaftarSyarikat);

                        $pdf->SetXY(114.872, 97.258); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['serial_no']);

                        $pdf->SetXY(123.872, 106.570); // Adjust for {Had_Terima}
                        $pdf->Write(0, $capacityValue);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(174.872, 106.570, 10, 10, 'F'); 

                        $pdf->SetXY(125.872, 115.258); // Adjust for {Senggatan}
                        $pdf->Write(0, $capacityDivision);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(176.872, 108.570, 10, 10, 'F'); 

                        # Adjust for {Jenis Penunjuk}
                        if ($res['jenis_penunjuk'] == 'DIGITAL'){
                            $pdf->Image($tickImage, 109.141, 127.637, 8);
                        }elseif ($res['jenis_penunjuk'] == 'DAIL'){
                            $pdf->Image($tickImage, 134.141, 127.637, 8);
                        }

                        # Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 109.141, 170.637, 8);
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 134.141, 170.637, 8);
                        }

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 29.648, 187.637, 38.5);  // Adjust for company signature
                        }

                        // $pdf->SetXY(115.141 , 196.637); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $pdf->SetXY(125.141 , 204.637); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(134.141 , 212.637); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'ATP-AUTO MACHINE' && $validator == 'DE METROLOGY'){
        $fillFile = 'forms/DE_Metrology/DMSB_ATP.pdf';

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions

                        $pdf->Image($tickImage, 72.159, 55.865, 8); // Adjust for Kegunaan Alat

                        $pdf->SetXY(135.121, 53.954-2); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 
                        
                        $pdf->SetXY(147.112, 63.103-2); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetXY(15.666, 88.465-2); // Adjust for Customer Name
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(15.666, 94.901-2); // Adjust for {3. Alamat Pemilik Address 1 & 2}
                        $pdf->Write(0, $address1);

                        $pdf->SetXY(15.666, 101.418-2); // Adjust for {3. Alamat Pemilik Address 3}
                        $pdf->Write(0, $address2);
                        
                        $pdf->SetXY(15.666, 107.855-2); // Adjust for {3. Alamat Pemilik Address 4}
                        $pdf->Write(0, $address3 . ' ' . $address4);

                        $pdf->SetXY(15.666, 126.474-2); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        // $pdf->SetXY(132.084, 111.188-1.5); // Adjust for {jenis_alat}
                        // $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - '. searchMachineNameById($res['machine_type'], $db));

                        $pdf->SetXY(137.577, 72.413-2); // Adjust for {Model}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(15.666, 144.725-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(133.147, 81.900-2); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['serial_no']);

                        $pdf->SetXY(143.051, 93.842-2); // Adjust for {Had_Terima}
                        $pdf->Write(0, $capacityValue);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(170.852, 93.842-2, 40, 10, 'F');

                        $pdf->SetXY(143.677+2, 106.795-2); // Adjust for {Senggatan}
                        $pdf->Write(0, $capacityDivision);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(173.019, 106.795-2, 40, 10, 'F');

                        $pdf->SetXY(15.666, 162.670-2); // Adjust for {no_daftar}
                        $pdf->Write(0, $noDaftarSyarikat);

                        # Adjust for {Jenis Penunjuk}
                        if ($res['jenis_penunjuk'] == 'DIGITAL'){
                            $pdf->Image($tickImage, 132.422+12, 126.394-7, 8);
                        }elseif ($res['jenis_penunjuk'] == 'DAIL'){
                            $pdf->Image($tickImage, 163.876+12, 126.394-7, 8);
                        }

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 130.101+12, 144.677-7, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 164.035+12, 144.677-7, 8);
                        }

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 29.648, 188.637, 38.5);  // Adjust for company signature
                        }

                        // $pdf->SetXY(131.445, 162.670-2); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $pdf->SetXY(138.428, 172.029-2); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(150.788, 181.001-2); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);

                        
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_de_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'SLL' && $validator == 'METROLOGY'){
        $fillFile = 'forms/Metrology/ATL_FORM.pdf';

        $pdf = new PDFWithEllipse();

        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                    $pdf->SetMargins(0, 0, 0); // Remove all margins
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        $pdf->SetFont('Arial', 'B', 10);

                        $pdf->SetXY(23, 20); 
                        $pdf->Write(0, $currentDate);

                        $pdf->SetXY(55.374, 53.063); // Adjust for Customer Name
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 8);

                        $pdf->SetXY(55.374, 60.063); // Adjust for {Alamat Pemilik}
                        $pdf->Write(0, $address1 . ' ' . $address2 . ' ' . $address3 . ' ' . $address4);

                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->SetXY(55.374, 68.063); // Adjust for Company Name
                        $pdf->Write(0, $compname);

                        $pdf->SetFont('Arial', '', 8);
                        $pdf->SetXY(55.374, 75.063); // Adjust for Company address
                        $pdf->Write(0, $compaddress);

                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(30.374, 100.872-2); // Adjust for {jenis_alat}
                        $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - '. searchModelNameById($res['model'], $db));

                        $pdf->SetXY(40.374, 85.872-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(115.656, 105.080-2); // Adjust for {Nilai Jangkaan}
                        $pdf->Write(0, searchCapacityNameById($capacity,$db));

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(160.609, 105.080-2, 10, 7, 'F');

                        $pdf->SetXY(32.158, 107.106); // Adjust for {nama pembuat}
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        # Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 116.253+22, 85.946-7, 8);
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 159.854+22, 85.946-7, 8);
                        }

                        # Adjust for {Alat Type}
                        if ($res['alat_type'] == 'KERAS'){
                            $pdf->Image($tickImage, 116.253+22, 93.946-7, 8);
                        }elseif ($res['alat_type'] == 'LOGAM'){
                            $pdf->Image($tickImage, 159.854+22, 93.946-7, 8);
                        }

                        // $pdf->SetXY(24.158, 123.106); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDate);

                        // $pdf->SetXY(24.158, 127.106); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentTime);

                        // $pdf->SetXY(76.255, 123.106); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(154.095, 123.106); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);
                        
                        $questions = json_decode($res['questions'], true);

                        $pdf->SetDrawColor(0, 0, 0); // Black outline
                        $pdf->SetLineWidth(0.5); // Set line width

                        if($questions[0]['answer'] == 'YA'){
                            $pdf->Ellipse(173.736, 147.537, 5, 3, 'D', [200, 255, 200]);
                        }elseif ($questions[0]['answer'] == 'TIDAK'){
                            $pdf->Ellipse(183.736, 147.537, 5, 3, 'D', [200, 255, 200]); 
                        }

                        if($questions[1]['answer'] == 'YA'){
                            $pdf->Ellipse(173.736, 160.537, 5, 3, 'D', [200, 255, 200]);
                        }elseif ($questions[1]['answer'] == 'TIDAK'){
                            $pdf->Ellipse(183.736, 160.537, 5, 3, 'D', [200, 255, 200]); 
                        }

                        if($questions[2]['answer'] == 'YA'){
                            $pdf->Ellipse(173.736, 168.537, 5, 3, 'D', [200, 255, 200]);
                        }elseif ($questions[2]['answer'] == 'TIDAK'){
                            $pdf->Ellipse(183.736, 168.537, 5, 3, 'D', [200, 255, 200]); 
                        }

                        if($questions[3]['answer'] == 'YA'){
                            $pdf->Ellipse(173.736, 180.537, 5, 3, 'D', [200, 255, 200]);
                        }elseif ($questions[3]['answer'] == 'TIDAK'){
                            $pdf->Ellipse(183.736, 180.537, 5, 3, 'D', [200, 255, 200]); 
                        }

                        if($questions[4]['answer'] == 'YA'){
                            $pdf->Ellipse(173.736, 205.537, 5, 3, 'D', [200, 255, 200]);
                        }elseif ($questions[4]['answer'] == 'TIDAK'){
                            $pdf->Ellipse(183.736, 205.537, 5, 3, 'D', [200, 255, 200]); 
                        }

                        if($questions[5]['answer'] == 'YA'){
                            $pdf->Ellipse(173.736, 217.537, 5, 3, 'D', [200, 255, 200]);
                        }elseif ($questions[5]['answer'] == 'TIDAK'){
                            $pdf->Ellipse(183.736, 217.537, 5, 3, 'D', [200, 255, 200]); 
                        }

                        if($questions[6]['answer'] == 'YA'){
                            $pdf->Ellipse(173.736, 229.537, 5, 3, 'D', [200, 255, 200]);
                        }elseif ($questions[6]['answer'] == 'TIDAK'){
                            $pdf->Ellipse(183.736, 229.537, 5, 3, 'D', [200, 255, 200]); 
                        }

                        if($questions[7]['answer'] == 'YA'){
                            $pdf->Ellipse(173.736, 241.537, 5, 3, 'D', [200, 255, 200]);
                        }elseif ($questions[7]['answer'] == 'TIDAK'){
                            $pdf->Ellipse(183.736, 241.537, 5, 3, 'D', [200, 255, 200]); 
                        }
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'SLL' && $validator == 'DE METROLOGY'){
        $fillFile = 'forms/DE_Metrology/DMSB_SLL.pdf';

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->SetXY(38.411, 104.333-2); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetXY(65.378, 59.201-2); // Adjust for Customer Name
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(9.371 , 65.217-2); // Adjust for {3. Alamat Pemilik Address 1 & 2}
                        $pdf->Write(0, $address1 . ' ' . $address2);
                        
                        $pdf->SetXY(15.666, 71.188-2); // Adjust for {3. Alamat Pemilik Address 3 & 4}
                        $pdf->Write(0, $address3 . ' ' . $address4);

                        $pdf->SetXY(35.374, 87.872-2); // Adjust for {jenis_alat}
                        $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - '. searchModelNameById($res['model'], $db));

                        $pdf->SetXY(43.790, 79.352-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(37.656, 96.080-2); // Adjust for {Nilai Jangkaan}
                        $pdf->Write(0, searchCapacityNameById($capacity,$db));

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(74.609, 96.080-2, 30, 7, 'F');

                        # Adjust for {Alat Type}
                        if ($res['alat_type'] == 'KERAS'){
                            $pdf->Image($tickImage, 127.253+22, 87.946-7, 8);
                        }elseif ($res['alat_type'] == 'LOGAM'){
                            $pdf->Image($tickImage, 136.854+22, 87.946-7, 8);
                        }

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 148.749, 79.352-7, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 192.251, 79.352-7, 8);
                        }

                        // $pdf->SetFont('Arial', '', 9);
                        // $pdf->SetXY(24.158, 119.106-2); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);
                        // $pdf->SetFont('Arial', '', 10);

                        // $pdf->SetXY(86.255, 119.106-2); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(171.095, 119.106-2); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);

                        $questions = json_decode($res['questions'], true);

                        if($questions[0]['answer'] == 'YA'){
                            $pdf->Image($tickImage, 140.736+10, 135.537-7, 8);
                        }elseif ($questions[0]['answer'] == 'TIDAK'){
                            $pdf->Image($tickImage, 170.621+10, 135.537-7, 8);
                        }

                        if($questions[1]['answer'] == 'YA'){
                            $pdf->Image($tickImage, 140.736+10, 156.147-7, 8);
                        }elseif ($questions[1]['answer'] == 'TIDAK'){
                            $pdf->Image($tickImage, 170.621+10, 156.147-7, 8);
                        }

                        if($questions[2]['answer'] == 'YA'){
                            $pdf->Image($tickImage, 140.736+10, 164.326-7, 8);
                        }elseif ($questions[2]['answer'] == 'TIDAK'){
                            $pdf->Image($tickImage, 170.621+10, 164.326-7, 8);
                        }

                        if($questions[3]['answer'] == 'YA'){
                            $pdf->Image($tickImage, 140.736+10, 176.684-7, 8);
                        }elseif ($questions[3]['answer'] == 'TIDAK'){
                            $pdf->Image($tickImage, 170.621+10, 176.684-7, 8);
                        }

                        if($questions[4]['answer'] == 'YA'){
                            $pdf->Image($tickImage, 140.736+10, 203.694-7, 8);
                        }elseif ($questions[4]['answer'] == 'TIDAK'){
                            $pdf->Image($tickImage, 170.621+10, 203.694-7, 8);
                        }

                        if($questions[5]['answer'] == 'YA'){
                            $pdf->Image($tickImage, 140.736+10, 228.409-7, 8);
                        }elseif ($questions[5]['answer'] == 'TIDAK'){
                            $pdf->Image($tickImage, 170.621+10, 228.409-7, 8);
                        }

                        if($questions[6]['answer'] == 'YA'){
                            $pdf->Image($tickImage, 140.736+10, 240.692-7, 8);
                        }elseif ($questions[6]['answer'] == 'TIDAK'){
                            $pdf->Image($tickImage, 170.621+10, 240.692-7, 8);
                        }

                        if($questions[7]['answer'] == 'YA'){
                            $pdf->Image($tickImage, 140.736+10, 261.228-7, 8);
                        }elseif ($questions[7]['answer'] == 'TIDAK'){
                            $pdf->Image($tickImage, 170.621+10, 261.228-7, 8);
                        }

                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_de_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'ATS (H)' && $validator == 'METROLOGY'){
        $fillFile = 'forms/Metrology/ATS_FORM.pdf';

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->SetXY(29, 15); 
                        $pdf->Write(0, $currentDate);

                        $pdf->Image($tickImage, 58.526, 74.865-2, 8);  // Adjust for Perdagangan

                        $pdf->SetXY(140.704, 67.5-1); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

                        $pdf->SetXY(150.704, 76.5); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->SetXY(18.648, 103.063-1); // Adjust for Customer Name
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(18.648, 107.133); // Adjust for {3. Alamat Pemilik Address 1}
                        $pdf->Write(0, $address1);

                        $pdf->SetXY(18.648, 111.188+1); // Adjust for {3. Alamat Pemilik Address 2}
                        $pdf->Write(0, $address2);
                        
                        $pdf->SetXY(18.648, 117.258); // Adjust for {3. Alamat Pemilik Address 3 & 4}
                        $pdf->Write(0, $address3 . ' ' . $address4);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(140.942, 90.294, 20, 10, 'F'); 
                        
                        $pdf->SetXY(140.942 , 100.294-2.4); // Adjust for {Jenis_alat}
                        $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - ');
                        
                        $pdf->SetXY(140.942 , 104.294-2.4); // Adjust for {Jenis_alat}
                        $pdf->Write(0, searchMachineNameById($res['machine_type'], $db));

                        $pdf->SetXY(140.942 , 110.294-3); // Adjust for {Model}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(46.648, 127.570); // Adjust for {company name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(16.648, 145.570-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(16.648, 165.570-2); // Adjust for {No_Daftar_Syarikat}
                        $pdf->Write(0, $noDaftarSyarikat);

                        $pdf->SetXY(140.872, 117.258); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['serial_no']);

                        $pdf->SetXY(148.872, 147.570); // Adjust for {Had_Terima}
                        $pdf->Write(0, $capacityValue);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(185.872, 147.570, 10, 10, 'F'); 

                        $pdf->SetXY(149.872, 157.258); // Adjust for {Senggatan}
                        $pdf->Write(0, $capacityDivision);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(190.872, 157.570, 10, 10, 'F'); 

                        # Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 125.141, 175.637, 8);
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 171.141, 175.637, 8);
                        }

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 28, 198, 42);  // Adjust for company signature
                        }

                        // $pdf->SetXY(140.141 , 205.637); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $pdf->SetXY(146.141 , 215.637); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(156.141 , 225.637); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_metrology_".$_GET['file']."_form.pdf");
    }
    else if($file == 'ATS (H)' && $validator == 'DE METROLOGY'){
        $fillFile = 'forms/DE_Metrology/DMSB_ATS.pdf';

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fillFile);

        $select_stmt = $db->prepare("SELECT * FROM stamping A LEFT JOIN stamping_ext B ON A.id = B.stamp_id WHERE A.id = ?");
        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id); // 'i' indicates the type of $id (integer)
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            if ($res = $result->fetch_assoc()) {
                $branch = $res['branch'];
                $loadcells = json_decode($res['load_cells_info'], true);
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

                $capacity = $res['capacity'];
                $capacityQuery = "SELECT * FROM capacity WHERE id = $capacity";
                $capacityDetail = mysqli_query($db, $capacityQuery);
                $capacityRow = mysqli_fetch_assoc($capacityDetail);

                $capacityValue = null;
                $capacityDivision = null;

                if(!empty($capacityRow)){
                    $capacityValue = $capacityRow['capacity'] . searchUnitNameById($capacityRow['units'], $db);
                    $capacityDivision = $capacityRow['division'] . searchUnitNameById($capacityRow['division_unit'], $db);
                }

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 10);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions

                        $pdf->Image($tickImage, 65.159, 67.865, 8); // Adjust for Kegunaan Alat

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(130.635, 59.669-3, 70, 30, 'F');

                        $pdf->SetXY(130.635, 59.669-1); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(145.803, 93.956-3, 50, 15, 'F');  
                        
                        $pdf->SetXY(142.803, 93.956-1); // Adjust for nama pembuat
                        $pdf->Write(0, searchCountryNameById($res['platform_country'], $db)); 

                        $pdf->SetXY(22.648, 103.063-2); // Adjust for Customer Name
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(22.648, 107.133-2); // Adjust for {3. Alamat Pemilik Address 1 & 2}
                        $pdf->Write(0, $address1);

                        $pdf->SetXY(22.648, 111.188-2); // Adjust for {3. Alamat Pemilik Address 3}
                        $pdf->Write(0, $address2);
                        
                        $pdf->SetXY(22.648, 115.258-2); // Adjust for {3. Alamat Pemilik Address 4}
                        $pdf->Write(0, $address3 . ' ' . $address4);

                        $pdf->SetXY(21.648, 132.490-2); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(132.084, 111.188-3, 40, 10, 'F');

                        $pdf->SetXY(132.084, 111.188-1.5); // Adjust for {jenis_alat}
                        $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - '. searchMachineNameById($res['machine_type'], $db));

                        $pdf->SetXY(130.942 , 120.294-2); // Adjust for {Model}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(22.648, 150.570-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(126.872, 128.420-2); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['serial_no']);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(145.803, 136.545-3, 55, 10, 'F');  

                        $pdf->SetXY(145.803, 136.545-1); // Adjust for {Had_Terima}
                        $pdf->Write(0, $capacityValue);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(145.803, 150.570-3, 55, 10, 'F');  

                        $pdf->SetXY(141.803, 149.570); // Adjust for {Senggatan}
                        $pdf->Write(0, $capacityDivision);

                        $pdf->SetXY(22.648 , 168.637-2); // Adjust for {no_daftar_syarikat}
                        $pdf->Write(0, $noDaftarSyarikat);

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 124.118+10, 168.637-7, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 155.141+10, 168.637-7, 8);
                        }

                        // $pdf->SetFillColor(0, 0, 0);  // cover up unneccesary text
                        // $pdf->Rect(16.938, 205.223-3, 50, 2, 'F');  

                        if (isset($companySignature) && $companySignature!=null && $companySignature!=""){
                            $pdf->Image($companySignature, 24, 184.223, 35);  // Adjust for company signature
                        }

                        // $pdf->SetXY(126.243 , 184.902-2); // Adjust for {tarikh}
                        // $pdf->Write(0, $currentDateTime);

                        // $pdf->SetXY(132.582 , 193.027-2); // Adjust for {Cawangan}
                        // $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        // $pdf->SetXY(143.884 , 201.153-2); // Adjust for {no_penentusahan}
                        // $pdf->Write(0, $res['no_daftar']);

                        
                    }
                }

            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to get the data"
                )
            ); 
        }

        $pdf->Output('D', "filled_de_metrology_".$_GET['file']."_form.pdf");
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