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

if(isset($_GET['userID'], $_GET["file"])){
    if($_GET["file"] == 'ATK'){
        $fillFile = 'forms/ATK_FORM.pdf';
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
                        /*$fdf = str_replace('{Address1}', searchCustNameById($res['customers'], $db), $fdf);
                    $fdf = str_replace('{Address2}', $address1.' '.$address2.' '.$address3.' '.$address4, $fdf);
                    $fdf = str_replace('{Stamping_Address1}', $address1.' '.$address2, $fdf);
                    $fdf = str_replace('{Stamping_Address2}', $address3.' '.$address4, $fdf);
                    $fdf = str_replace('{Company_Name}', $compname, $fdf);
                    $fdf = str_replace('{No_Lesen}', $compcert, $fdf);
                    $fdf = str_replace('{Tarikh_Tamat_Lesen}', $compexp, $fdf);
                    $fdf = str_replace('{Nama_Wakil_Pembaik}', searchStaffNameById($res['pic'], $db), $fdf);
                    $fdf = str_replace('{No_KP}', searchStaffICById($res['pic'], $db), $fdf);
                    $fdf = str_replace('{Penentusahan_Baru}', $res['penentusan_baru'], $fdf);
                    $fdf = str_replace('{Penentusahan_Semula}', $res['penentusan_semula'], $fdf);
                    $fdf = str_replace('{NoKelulusan_MSPK}', $res['kelulusan_mspk'], $fdf);
                    $fdf = str_replace('{Pembuat _Negara_Asal}', searchCountryById($res['platform_country'], $db), $fdf);
                    $fdf = str_replace('{Jenama}', searchBrandNameById($res['brand'], $db), $fdf);
                    $fdf = str_replace('{Model#1}', searchModelNameById($res['model'], $db), $fdf);
                    $fdf = str_replace('{No_Siri}', $res['indicator_serial'], $fdf);
                    $fdf = str_replace('{Pembuat_Negara_Asal_2}', searchCountryById($res['platform_country'], $db), $fdf);
                    $fdf = str_replace('{Jenis_Steel_Concrete}', $res['jenis_pelantar'], $fdf);
                    $fdf = str_replace('{Lainlain_butiran}', $res['load_cell_no'], $fdf);
                    $fdf = str_replace('{Pembuat_Negara_Asal_3}', searchCountryById($res['load_cell_country'], $db), $fdf);
                    $fdf = str_replace('{Bilangan_Load_Cell}', $res['load_cell_no'], $fdf);*/

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

                        $pdf->SetXY(50, 145); // Adjust for {NoKelulusan_MSPK}
                        $pdf->Write(0, $res['kelulusan_mspk']);

                        $pdf->SetXY(75, 165); // Adjust for {Pembuat_Negara_Asal}
                        $pdf->Write(0, searchCountryById($res['platform_country'], $db));

                        $pdf->SetXY(155, 165); // Adjust for {Jenama}
                        $pdf->Write(0, searchBrandNameById($res['brand'], $db));

                        $pdf->SetXY(50, 173); // Adjust for {Model#1}
                        $pdf->Write(0, searchModelNameById($res['model'], $db));

                        $pdf->SetXY(155, 173); // Adjust for {No_Siri}
                        $pdf->Write(0, $res['indicator_serial']);

                        $pdf->SetXY(75, 186); // Adjust for {Pembuat_Negara_Asal_2}
                        $pdf->Write(0, searchCountryById($res['platform_country'], $db));

                        $pdf->SetXY(170, 186); // Adjust for {Jenis_Steel_Concrete}
                        $pdf->Write(0, $res['jenis_pelantar']);

                        /*$pdf->SetXY(50, 280); // Adjust for {Lainlain_butiran}
                        $pdf->Write(0, $res['load_cell_no']);*/

                        $pdf->SetXY(70, 212); // Adjust for {Pembuat_Negara_Asal_3}
                        $pdf->Write(0, searchCountryById($res['load_cell_country'], $db));

                        $pdf->SetXY(153, 212); // Adjust for {Bilangan_Load_Cell}
                        $pdf->Write(0, $res['load_cell_no']);
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

        $pdf->Output('D', 'filled_form.pdf');
    }
    else{

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