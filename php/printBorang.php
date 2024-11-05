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
        $fillFile = 'forms/metrology/ATS_FORM.pdf';

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
                    $pdf->SetFont('Helvetica', '', 8);
                    
                    // Example field placements for each page (you'll adjust these according to your PDF)
                    if ($pageNo == 1) {
                        // Fill in the fields at the appropriate positions
                        $pdf->SetXY(22.648, 103.063-2); // Adjust these coordinates for each field
                        $pdf->Write(0, searchCustNameById($res['customers'], $db)); // {3. Nama}

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