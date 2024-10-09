<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
require_once '../vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use mikehaertl\pdftk\FdfFile;
use mikehaertl\pdftk\Pdf;

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

if(isset($_POST['userID'], $_POST["file"])){
    if($_POST["file"] == 'ATK'){
        $fillFile = 'forms/ATK_FORM_Fillable.pdf';
        $fdfFile = 'forms/ATK_FORM_Fillable.fdf';
        $fdf = file_get_contents($fdfFile);

        if (!file_exists($fdfFile) || !file_exists($fillFile)){
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Failed to generate the borang"
                )
            ); 
        }
        else{
            $select_stmt = $db->prepare("SELECT * FROM stamping, stamping_ext WHERE stamping.id = stamping_ext.stamp_id AND stamping.id = '".$_POST['userID']."'");

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

                    $fdf = str_replace('{Address1}', searchCustNameById($res['customers'], $db), $fdf);
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
                    $fdf = str_replace('{Bilangan_Load_Cell}', $res['load_cell_no'], $fdf);

                    $tmpFdf = 'forms/'.$_POST['userID'].'.fdf';
                    file_put_contents($tmpFdf, $fdf);
                    exec('pdftk '.$fillFile.' fill_form '.$tmpFdf.' output '.'forms/'.$_POST['userID'].'_ATK.pdf');
                    chmod($tmpFdf,0755);
                    unlink($tmpFdf);
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
        }
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