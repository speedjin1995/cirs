<?php
require_once 'db_connect.php';

// PhpSpreadsheet
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
    exit;
}

if(isset($_POST['jenisAlatDownload'], $_POST['jenisAlatName'])){
    $jenisAlat = filter_input(INPUT_POST, 'jenisAlatDownload', FILTER_SANITIZE_STRING);
    $jenisAlatName = filter_input(INPUT_POST, 'jenisAlatName', FILTER_SANITIZE_STRING);

    if (str_contains($jenisAlatName, 'ATP (MOTORCAR)')) {
		$file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'Stamping_ATP (MOTORCAR)_Template.xlsx';
        $filename = 'Stamping_ATP (MOTORCAR)_Template.xlsx';
    } elseif (str_contains($jenisAlatName, 'ATP')) {
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'Stamping_ATP_Template.xlsx';
        $filename = 'Stamping_ATP_Template.xlsx';
    } elseif (str_contains($jenisAlatName, 'ATN')) {
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'Stamping_ATN_Template.xlsx';
        $filename = 'Stamping_ATN_Template.xlsx';
    } elseif (str_contains($jenisAlatName, 'ATE')) {
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'Stamping_ATE_Template.xlsx';
        $filename = 'Stamping_ATE_Template.xlsx';
    } elseif (str_contains($jenisAlatName, 'BTU')) {
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'Stamping_BTU_Template.xlsx';
        $filename = 'Stamping_BTU_Template.xlsx';
    } elseif (str_contains($jenisAlatName, 'SIA')) {
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'Stamping_SIA_Template.xlsx';
        $filename = 'Stamping_SIA_Template.xlsx';
    } elseif (str_contains($jenisAlatName, 'BAP')) {
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'Stamping_BAP_Template.xlsx';
        $filename = 'Stamping_BAP_Template.xlsx';
    } elseif (str_contains($jenisAlatName, 'SIC')) {
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'Stamping_SIC_Template.xlsx';
        $filename = 'Stamping_SIC_Template.xlsx';
    } else {
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'Stamping Record Template.xlsx';
        $filename = 'Stamping Record Template.xlsx';
    }

    if (file_exists($file)) {
        // Load the existing template file
        $spreadsheet = IOFactory::load($file);

        // Download the file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } else {
        echo json_encode([
            "status"=> "failed", 
            "message"=> "Template file not found"
        ]);
        exit;
    }

} else {
    echo json_encode([
        "status"=> "failed", 
        "message"=> "Please fill in all the fields"
    ]);
    exit;
}