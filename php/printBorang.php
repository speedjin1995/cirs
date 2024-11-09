<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
require_once '../vendor/autoload.php'; 

use setasign\Fpdi\Fpdi;

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

    if($file == 'ATK' && $validator == 'METROLOGY'){
        $fillFile = 'forms/metrology/ATK_FORM.pdf';
        $pdf = new Fpdi();
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
                        $pdf->SetXY(25, 60); // Adjust these coordinates for each field
                        $pdf->Write(0, searchCustNameById($res['customers'], $db)); // {Address1}

                        $pdf->SetXY(25, 65); // Adjust for {Address2}
                        $pdf->Write(0, $address1.' '.$address2);

                        $pdf->SetXY(25, 80); // Adjust for {Stamping_Address1}
                        $pdf->Write(0, $address1.' '.$address2);

                        $pdf->SetXY(25, 85); // Adjust for {Stamping_Address2}
                        $pdf->Write(0, $address3.' '.$address4);

                        $pdf->SetXY(75, 100); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(55, 105); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(160, 105); // Adjust for {Tarikh_Tamat_Lesen}
                        $pdf->Write(0, $compexp);

                        $pdf->SetXY(75, 110); // Adjust for {Nama_Wakil_Pembaik}
                        $pdf->Write(0, searchStaffNameById($res['pic'], $db));

                        $pdf->SetXY(150, 110); // Adjust for {No_KP}
                        $pdf->Write(0, searchStaffICById($res['pic'], $db));

                        $pdf->SetXY(75, 125); // Adjust for {Penentusahan_Baru}
                        $pdf->Write(0, $res['penentusan_baru']);

                        $pdf->SetXY(160, 125); // Adjust for {Penentusahan_Semula}
                        $pdf->Write(0, $res['penentusan_semula']);

                        if($res['kelulusan_mspk'] == 'YES'){
                            $pdf->SetXY(47, 147); // Adjust for {NoKelulusan_MSPK}
                            $pdf->Write(0, '/');
                        }
                        else{
                            $pdf->SetXY(47, 153); // Adjust for {NoKelulusan_MSPK}
                            $pdf->Write(0, '/');
                        }
                        

                        $pdf->SetXY(75, 166); // Adjust for {Pembuat_Negara_Asal}
                        $pdf->Write(0, searchCountryById($res['platform_country'], $db));

                        $pdf->SetXY(155, 166); // Adjust for {Jenama}
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db));

                        $pdf->SetXY(50, 173); // Adjust for {Model#1}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(155, 173); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['indicator_serial']);

                        $pdf->SetXY(75, 186); // Adjust for {Pembuat_Negara_Asal_2}
                        $pdf->Write(0, searchCountryById($res['platform_country'], $db));

                        $pdf->SetXY(160, 186); // Adjust for {Jenis_Steel_Concrete}
                        $pdf->Write(0, $res['platform_type']);

                        $pdf->SetXY(75, 192); // Adjust for {size}
                        $pdf->Write(0, searchSizeNameById($res['size'], $db));

                        if($res['jenis_pelantar'] == 'Pit'){
                            $pdf->SetXY(150, 193); // Adjust for {Jenis_Steel_Concrete}
                            $pdf->Write(0, '----------');
                        }
                        else{
                            $pdf->SetXY(140, 193); // Adjust for {Jenis_Steel_Concrete}
                            $pdf->Write(0, '----------');
                        }

                        $pdf->SetXY(70, 212); // Adjust for {Pembuat_Negara_Asal_3}
                        $pdf->Write(0, searchCountryById($res['load_cell_country'], $db));

                        $pdf->SetXY(153, 212); // Adjust for {Bilangan_Load_Cell}
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

                            $pdf->SetXY(140, 226 + $count); // Adjust for {Bilangan_Load_Cell}
                            $pdf->Write(0, $loadcells[$i]['loadCellSerial']);

                            $count += 6;
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

        $pdf->Output('D', 'filled_ATK_form.pdf');
    }
    else if($file == 'ATK' && $validator == 'DE METROLOGY'){
        $fillFile = 'forms/DE_Metrology/DMSB_ATK.pdf';
        $pdf = new Fpdi();
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
                        $pdf->SetXY(25, 70); // Adjust these coordinates for each field
                        $pdf->Write(0, searchCustNameById($res['customers'], $db)); // {Address1}

                        $pdf->SetXY(25, 75); // Adjust for {Address2}
                        $pdf->Write(0, $address1.' '.$address2.' '.$address3.' '.$address4);

                        $pdf->SetXY(25, 95); // Adjust for {Stamping_Address1}
                        $pdf->Write(0, $address1.' '.$address2);

                        $pdf->SetXY(25, 100); // Adjust for {Stamping_Address2}
                        $pdf->Write(0, $address3.' '.$address4);

                        $pdf->SetXY(75, 125); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(75, 130); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(75, 135); // Adjust for {Tarikh_Tamat_Lesen}
                        $pdf->Write(0, $compexp);

                        $pdf->SetXY(75, 140); // Adjust for {Nama_Wakil_Pembaik}
                        $pdf->Write(0, searchStaffNameById($res['pic'], $db));

                        $pdf->SetXY(75, 145); // Adjust for {No_KP}
                        $pdf->Write(0, searchStaffICById($res['pic'], $db));

                        $pdf->SetXY(75, 170); // Adjust for {Penentusahan_Baru}
                        $pdf->Write(0, $res['penentusan_baru']);

                        $pdf->SetXY(75, 175); // Adjust for {Penentusahan_Semula}
                        $pdf->Write(0, $res['penentusan_semula']);

                        if($res['kelulusan_mspk'] == 'YES'){
                            $pdf->SetXY(35, 205); // Adjust for {NoKelulusan_MSPK}
                            $pdf->Write(0, '/');
                        }
                        else{
                            $pdf->SetXY(35, 210); // Adjust for {NoKelulusan_MSPK}
                            $pdf->Write(0, '/');
                        }

                        $pdf->SetXY(75, 230); // Adjust for {Pembuat_Negara_Asal}
                        $pdf->Write(0, searchCountryById($res['platform_country'], $db));

                        $pdf->SetXY(75, 235); // Adjust for {Jenama}
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db));

                        $pdf->SetXY(75, 240); // Adjust for {Model#1}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(75, 245); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['indicator_serial']);
                    }
                    else if ($pageNo == 2){
                        $pdf->SetXY(75, 30); // Adjust for {Pembuat_Negara_Asal_2}
                        $pdf->Write(0, searchCountryById($res['platform_country'], $db));

                        $pdf->SetXY(75, 35); // Adjust for {Jenis_Steel_Concrete}
                        $pdf->Write(0, $res['platform_type']);

                        $pdf->SetXY(75, 40); // Adjust for {size}
                        $pdf->Write(0, searchSizeNameById($res['size'], $db));

                        if($res['jenis_pelantar'] == 'Pit'){
                            $pdf->SetXY(57, 48); // Adjust for {Jenis_Steel_Concrete}
                            $pdf->Write(0, '----------');
                        }
                        else{
                            $pdf->SetXY(47, 48); // Adjust for {Jenis_Steel_Concrete}
                            $pdf->Write(0, '----------');
                        }

                        $pdf->SetXY(75, 75); // Adjust for {Pembuat_Negara_Asal_3}
                        $pdf->Write(0, searchCountryById($res['load_cell_country'], $db));

                        $pdf->SetXY(75, 80); // Adjust for {Bilangan_Load_Cell}
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

        $pdf->Output('D', 'filled_ATK_form.pdf');
    }
    else if($file == 'ATE' && $validator == 'DE METROLOGY'){
        $fillFile = 'forms/DE_Metrology/DMSB_ATE.pdf';
        $pdf = new Fpdi();
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
                        $pdf->SetXY(25, 70); // Adjust these coordinates for each field
                        $pdf->Write(0, searchCustNameById($res['customers'], $db)); // {Address1}

                        $pdf->SetXY(25, 75); // Adjust for {Address2}
                        $pdf->Write(0, $address1.' '.$address2.' '.$address3.' '.$address4);

                        $pdf->SetXY(25, 95); // Adjust for {Stamping_Address1}
                        $pdf->Write(0, $address1.' '.$address2);

                        $pdf->SetXY(25, 100); // Adjust for {Stamping_Address2}
                        $pdf->Write(0, $address3.' '.$address4);

                        $pdf->SetXY(75, 125); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(75, 130); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(75, 135); // Adjust for {Tarikh_Tamat_Lesen}
                        $pdf->Write(0, $compexp);

                        $pdf->SetXY(75, 140); // Adjust for {Nama_Wakil_Pembaik}
                        $pdf->Write(0, searchStaffNameById($res['pic'], $db));

                        $pdf->SetXY(75, 145); // Adjust for {No_KP}
                        $pdf->Write(0, searchStaffICById($res['pic'], $db));

                        $pdf->SetXY(75, 170); // Adjust for {Penentusahan_Baru}
                        $pdf->Write(0, $res['penentusan_baru']);

                        $pdf->SetXY(75, 175); // Adjust for {Penentusahan_Semula}
                        $pdf->Write(0, $res['penentusan_semula']);

                        if($res['kelulusan_mspk'] == 'YES'){
                            $pdf->SetXY(35, 205); // Adjust for {NoKelulusan_MSPK}
                            $pdf->Write(0, '/');
                        }
                        else{
                            $pdf->SetXY(35, 210); // Adjust for {NoKelulusan_MSPK}
                            $pdf->Write(0, '/');
                        }

                        $pdf->SetXY(75, 230); // Adjust for {Pembuat_Negara_Asal}
                        $pdf->Write(0, searchCountryById($res['platform_country'], $db));

                        $pdf->SetXY(75, 235); // Adjust for {Jenama}
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db));

                        $pdf->SetXY(75, 240); // Adjust for {Model#1}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(75, 245); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['indicator_serial']);
                    }
                    else if ($pageNo == 2){
                        $pdf->SetXY(75, 30); // Adjust for {Pembuat_Negara_Asal_2}
                        $pdf->Write(0, searchCountryById($res['platform_country'], $db));

                        $pdf->SetXY(75, 35); // Adjust for {Jenis_Steel_Concrete}
                        $pdf->Write(0, $res['platform_type']);

                        $pdf->SetXY(75, 40); // Adjust for {size}
                        $pdf->Write(0, searchSizeNameById($res['size'], $db));

                        if($res['jenis_pelantar'] == 'Pit'){
                            $pdf->SetXY(57, 48); // Adjust for {Jenis_Steel_Concrete}
                            $pdf->Write(0, '----------');
                        }
                        else{
                            $pdf->SetXY(47, 48); // Adjust for {Jenis_Steel_Concrete}
                            $pdf->Write(0, '----------');
                        }

                        $pdf->SetXY(75, 75); // Adjust for {Pembuat_Negara_Asal_3}
                        $pdf->Write(0, searchCountryById($res['load_cell_country'], $db));

                        $pdf->SetXY(75, 80); // Adjust for {Bilangan_Load_Cell}
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

        $pdf->Output('D', "filled_".$_GET['file']."_form.pdf");
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
                        $pdf->Write(0, searchJenisAlatNameByid($res['jenis_alat'], $db).' - '. searchMachineNameById($res['machine_type'], $db));

                        $pdf->SetXY(140.942 , 110.294-3); // Adjust for {Model}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetFont('Arial', '', 8);
                        $pdf->SetXY(46.648, 127.570); // Adjust for {company name}
                        $pdf->Write(0, $compname);
                        $pdf->SetFont('Arial', '', 10);

                        $pdf->SetXY(22.648, 145.570-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(22.648, 165.570-2); // Adjust for {No_Daftar_Syarikat}
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

                        $pdf->Image($companySignature, 37.648, 209.637, 20);  // Adjust for company signature

                        $pdf->SetXY(140.141 , 205.637); // Adjust for {tarikh}
                        $pdf->Write(0, $currentDateTime);

                        $pdf->SetXY(146.141 , 215.637); // Adjust for {Cawangan}
                        $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        $pdf->SetXY(156.141 , 225.637); // Adjust for {no_penentusahan}
                        $pdf->Write(0, $res['no_daftar']);
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
                        $pdf->Write(0, '('.searchBrandNameById($res['brand'], $db).')'); 

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(145.803, 93.956-3, 50, 15, 'F');  
                        
                        $pdf->SetXY(142.803, 93.956-1); // Adjust for nama pembuat
                        $pdf->Write(0, '('.searchCountryNameById($res['platform_country'], $db).')'); 

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

                        $pdf->SetXY(22.648 , 168.637-2); // Adjust for {no_daftar}
                        $pdf->Write(0, $noDaftarSyarikat);

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 124.118+10, 168.637-7, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 155.141+10, 168.637-7, 8);
                        }

                        $pdf->Image($companySignature, 30.648, 192.637, 20);  // Adjust for company signature

                        $pdf->SetXY(126.243 , 184.902-2); // Adjust for {tarikh}
                        $pdf->Write(0, $currentDateTime);

                        $pdf->SetXY(132.582 , 193.027-2); // Adjust for {Cawangan}
                        $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        $pdf->SetXY(143.884 , 201.153-2); // Adjust for {no_penentusahan}
                        $pdf->Write(0, $res['no_daftar']);

                        
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
        $fillFile = 'forms/metrology/ATN_FORM.pdf';

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
                        $pdf->SetXY(120.635, 59.669-2); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

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

                        $pdf->SetXY(22.648, 132.490-2); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(130.942 , 120.294-2); // Adjust for {Model}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(22.648, 150.570-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(126.872, 128.420-2); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['indicator_serial']);

                        $pdf->SetXY(22.648 , 168.637-2); // Adjust for {no_daftar}
                        $pdf->Write(0, $res['no_daftar']);

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 124.118+10, 168.637-5, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 155.141+10, 168.637-5, 8);
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

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                
                    // Fill in the fields for the current page
                    $pdf->SetFont('Arial', '', 8);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->SetXY(131.704, 84.865-1); // Adjust for Jenama
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db)); 

                        $pdf->SetXY(22.648, 103.063-2); // Adjust for Customer Name
                        $pdf->Write(0, searchCustNameById($res['customers'], $db));

                        $pdf->SetXY(22.648, 107.133-2); // Adjust for {3. Alamat Pemilik Address 1 & 2}
                        $pdf->Write(0, $address1.' '.$address2);

                        $pdf->SetXY(22.648, 111.188-2); // Adjust for {3. Alamat Pemilik Address 3}
                        $pdf->Write(0, $address3);
                        
                        $pdf->SetXY(22.648, 115.258-2); // Adjust for {3. Alamat Pemilik Address 4}
                        $pdf->Write(0, $address4);

                        $pdf->SetXY(22.648, 132.490-2); // Adjust for {Company_Name}
                        $pdf->Write(0, $compname);

                        $pdf->SetXY(130.942 , 120.294-2); // Adjust for {Model}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(22.648, 150.570-2); // Adjust for {No_Lesen}
                        $pdf->Write(0, $compcert);

                        $pdf->SetXY(126.872, 128.420-2); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['indicator_serial']);

                        $pdf->SetXY(22.648 , 168.637-2); // Adjust for {no_daftar}
                        $pdf->Write(0, $res['no_daftar']);

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 124.118+10, 168.637-5, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 155.141+10, 168.637-5, 8);
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

        $pdf->Output('D', "filled_".$_GET['file']."_form.pdf");
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

                        $pdf->Image($companySignature, 37.648, 197.637, 20);  // Adjust for company signature

                        $pdf->SetXY(115.141 , 196.637); // Adjust for {tarikh}
                        $pdf->Write(0, $currentDateTime);

                        $pdf->SetXY(125.141 , 204.637); // Adjust for {Cawangan}
                        $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        $pdf->SetXY(134.141 , 212.637); // Adjust for {no_penentusahan}
                        $pdf->Write(0, $res['no_daftar']);
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
                        $pdf->Write(0, '('.searchBrandNameById($res['brand'], $db).')'); 

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(145.803, 93.956-3, 50, 15, 'F');  
                        
                        $pdf->SetXY(142.803, 93.956-1); // Adjust for nama pembuat
                        $pdf->Write(0, '('.searchCountryNameById($res['platform_country'], $db).')'); 

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
                        $pdf->Write(0, $res['indicator_serial']);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(145.803, 136.545-3, 55, 10, 'F');  

                        $pdf->SetXY(145.803, 136.545-1); // Adjust for {Had_Terima}
                        $pdf->Write(0, $capacityValue);

                        $pdf->SetFillColor(255, 255, 255);  // cover up unneccesary text
                        $pdf->Rect(145.803, 150.570-3, 55, 10, 'F');  

                        $pdf->SetXY(141.803, 149.570); // Adjust for {Senggatan}
                        $pdf->Write(0, $capacityDivision);

                        $pdf->SetXY(22.648 , 168.637-2); // Adjust for {no_daftar}
                        $pdf->Write(0, $noDaftarSyarikat);

                        // Adjust for {Keadaan Alat}
                        if ($res['stamping_type'] == 'NEW'){
                            $pdf->Image($tickImage, 124.118+10, 168.637-7, 8); 
                        }elseif ($res['stamping_type'] == 'RENEWAL'){
                            $pdf->Image($tickImage, 155.141+10, 168.637-7, 8);
                        }

                        $pdf->Image($companySignature, 30.648, 192.637, 20);  // Adjust for company signature

                        $pdf->SetXY(126.243 , 184.902-2); // Adjust for {tarikh}
                        $pdf->Write(0, $currentDateTime);

                        $pdf->SetXY(132.582 , 193.027-2); // Adjust for {Cawangan}
                        $pdf->Write(0, searchStateNameById($res['cawangan'], $db));

                        $pdf->SetXY(143.884 , 201.153-2); // Adjust for {no_penentusahan}
                        $pdf->Write(0, $res['no_daftar']);

                        
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