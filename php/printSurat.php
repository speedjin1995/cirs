<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require_once 'db_connect.php';
require_once 'requires/lookup.php';
 
if(isset($_POST['id'], $_POST['printType'], $_POST['printSuratDate'])){
    if ($_POST['printType'] == 'SINGLE') {
        $id = $_POST['id'];
        $printDate = $_POST['printSuratDate'];

        $companyQuery = "SELECT * FROM companies WHERE id = '1'";
        $companyDetail = mysqli_query($db, $companyQuery);
        $companyRow = mysqli_fetch_assoc($companyDetail);

        if(!empty($companyRow)){
            $companyName = $companyRow['name'];
            $companyOldRoc = $companyRow['old_roc'];
            $companyAddress = $companyRow['address'];
            $companyTel = $companyRow['phone'];
            $companyFax = $companyRow['fax'];
            $companyEmail = $companyRow['email'];
            $companySignature = $companyRow['signature'];
        }

        $select_stmt = $db->prepare("SELECT * FROM stamping WHERE id = ?");

        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $select_stmt->bind_param('s', $id);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $message = '';

            while ($row = $result->fetch_assoc()) {
                $branchId = $row['company_branch'];
                $branchAddress = '';
                $branchTel = '';
                $branchFax = '';

                if ($branchId){
                    $branchQuery = "SELECT * FROM company_branches WHERE id = '$branchId' AND deleted = 0";
                    $branchDetail = mysqli_query($db, $branchQuery);
                    $branchRow = mysqli_fetch_assoc($branchDetail);

                    if(!empty($branchRow)){
                        $branchAddress = $branchRow['address_line_1'].' '.$branchRow['address_line_2'].' '.$branchRow['address_line_3'].' '.$branchRow['address_line_4'];
                        $branchTel = $branchRow['pic_contact'];
                        $branchFax = $branchRow['office_no'];
                        $branchEmail = $branchRow['email'];
                    }
                }

                $capacityId = $row['capacity'];
                $capacityType = '';
                $capacityName = '';
                if (isset($row['capacity']) && $row['capacity'] != ''){
                    $capacityQuery = "SELECT * FROM capacity WHERE id = $capacityId";
                    $capacityDetail = mysqli_query($db, $capacityQuery);
                    $capacityRow = mysqli_fetch_assoc($capacityDetail);
                    if(!empty($capacityRow)){
                        $capacityType = $capacityRow['range_type'];
                        $capacityName = $capacityRow['name'];
                    }
                }

                $message .= 
                '
                    <html>
                    <head>
                    <meta charset="utf-8">
                    <title>A4 Document</title>
                    <!-- Bootstrap 4 CDN -->
                    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
                    <style>
                        body {
                            background: #f0f0f0;
                            font-size: 16pt; /* Increase base font size */
                            line-height: 1.6;
                        }
                        .table-dark-border th,
                        .table-dark-border td {
                            border: 2px solid #000 !important; /* Dark bold borders */
                        }
                        .table-dark-border {
                            border: 2px solid #000 !important;
                            font-size: 14pt;
                        }
                        .table-dark-border thead th {
                            font-size: 15pt;
                            text-align: center;
                        }
                        .table-dark-border td {
                            vertical-align: middle;
                        }
                        .hide {
                            visibility:hidden;
                        }
                    </style>
                    </head>
                    <body style="background:#f0f0f0;">
                        <div class="container-fluid py-5" style="padding-left: 100px; padding-right: 50px;">
                            <div class="text-center mb-4">
                                <h2 class="font-weight-bold mb-1">'.$companyName.'<small style="font-size: 12pt">(Co. No. '.$companyOldRoc.')</small></h2>
                                <p style="margin:0;">'.$branchAddress.'<br>
                                Tel : '.$branchTel.' Fax : '.$branchFax.'<br>
                                Email : '.$companyEmail.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Website : www.spwt.my
                                </p>
                            </div>

                            <hr class="border-dark mb-4">

                            <div class="row mb-4">
                                <div class="col-8">
                                    <p class="mb-0 font-weight-bold">METROLOGY CORPORATION (M) SDN. BHD.</p>
                                    <p class="mb-0">
                                        Cawangan Alor Setar<br>
                                        KM10 Jalan Sultanah Sambungan,<br>
                                        05350 Alor Setar, <br>
                                        Kedah.
                                    </p>
                                    <br>
                                    <p class="mb-0">
                                        <b><u>U.P : En. Sufian (011-1981 3100)</u></b>
                                    </p>
                                    <p class="mb-0">
                                        Tel : 604 - 734 7692<br>
                                        Fax : 604 - 740 7666
                                    </p>
                                </div>
                                <div class="col-4">
                                    <br>
                                    <p class="mb-0">Tarikh : '.$printDate.'</p>
                                    <p class="mb-0">Ruj. Kami : MCMAS/MEI-2025</p>
                                </div>
                            </div>

                            <p class="mb-3">Tuan,</p>
                            
                            <p class="mb-3">
                                Per : <span class="font-weight-bold text-uppercase"><u>Pengesahan / Penetusan Timbang Kenderaan Pada Bulan Mei 2025</u></span>
                            </p>

                            <p>Merujuk kepada perkara tersebut di atas, kami ingin memohon pengesahan jabatan tuan tentang tarikh dan masa untuk kerja-kerja penetusan penimbang seperti di bawah :-</p>

                            <table class="table table-dark-border w-100 mb-4">
                                <thead>
                                    <tr>
                                        <th style="width:10%;">No.</th>
                                        <th style="width:70%;">Pelanggan Dan Alamat Timbang</th>
                                        <th style="width:20%;">Capacity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1.</td>
                                        <td>'.searchCustNameById($row['customers'], $db).'<br>'.searchCustomerBranchAddressById($row['branch'], $db).'</td>
                                        <td class="font-weight-bold text-center">'.$capacityName.'</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2.</td>
                                        <td class="hide">(NAMA CUSTOMER)<br>(ALAMAT SITE CUSTOMER)</td>
                                        <td class="font-weight-bold text-center hide">60 Tonnes</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">3.</td>
                                        <td class="hide">(NAMA CUSTOMER)<br>(ALAMAT SITE CUSTOMER)</td>
                                        <td class="font-weight-bold text-center hide">40 Tonnes</td>
                                    </tr>
                                </tbody>
                            </table>

                            <p>Kami dengan sukacitanya berharap pihak tuan dapat mengesahkan jadual penetusan semula kepada kami.</p>
                            <p>Sekian Terima Kasih.</p>
                            <p>Yang Benar,</p>
                            <p class="font-weight-bold mb-4">SP WEIGHING SYSTEMS SDN. BHD.</p>

                            <br>
                            <p class="mt-2">..............................................................<br>
                                Syaswani<br>
                                012-5201218
                            </p>

                        </div>
                    </body>
                    </html>
                ';
            }

            // Return the results as JSON
            echo json_encode(array('status' => 'success', 'message' => $message));
        } 
        else {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Statement preparation failed"
                ));
        }

        $select_stmt->close();
    } else {
        $printDate = $_POST['printSuratDate'];
        $companyBranch = '';

        if (isset($_POST['companyBranch'])) {
            $companyBranch = $_POST['companyBranch'];
        }

        $branchAddress = '';
        $branchTel = '';
        $branchFax = '';

        $branchQuery = "SELECT * FROM company_branches WHERE id = '$companyBranch'";
        $branchDetail = mysqli_query($db, $branchQuery);
        $branchRow = mysqli_fetch_assoc($branchDetail);

        if(!empty($branchRow)){
            $branchAddress = $branchRow['address_line_1'].' '.$branchRow['address_line_2'].' '.$branchRow['address_line_3'].' '.$branchRow['address_line_4'];
            $branchTel = $branchRow['pic_contact'];
            $branchFax = $branchRow['office_no'];
        }

        $selectedIds = $_POST['id'];
        $arrayOfId = explode(",", $selectedIds);
        $placeholders = implode(',', array_fill(0, count($arrayOfId), '?'));

        $companyQuery = "SELECT * FROM companies WHERE id = '1'";
        $companyDetail = mysqli_query($db, $companyQuery);
        $companyRow = mysqli_fetch_assoc($companyDetail);

        if(!empty($companyRow)){
            $companyName = $companyRow['name'];
            $companyOldRoc = $companyRow['old_roc'];
            $companyAddress = $companyRow['address'];
            $companyTel = $companyRow['phone'];
            $companyFax = $companyRow['fax'];
            $companyEmail = $companyRow['email'];
            $companySignature = $companyRow['signature'];
        }

        $select_stmt = $db->prepare("SELECT * FROM stamping WHERE id IN ($placeholders) ORDER BY FIELD(id, $selectedIds)");

        // Check if the statement is prepared successfully
        if ($select_stmt) {
            // Bind variables to the prepared statement
            $types = str_repeat('i', count($arrayOfId)); // Assuming the IDs are integers
            $select_stmt->bind_param($types, ...$arrayOfId);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            
            // Collect all data first
            $allData = [];
            while ($row = $result->fetch_assoc()) {
                $capacityId = $row['capacity'];
                $capacityName = '';
                if (isset($row['capacity']) && $row['capacity'] != ''){
                    $capacityQuery = "SELECT * FROM capacity WHERE id = $capacityId";
                    $capacityDetail = mysqli_query($db, $capacityQuery);
                    $capacityRow = mysqli_fetch_assoc($capacityDetail);
                    if(!empty($capacityRow)){
                        $capacityName = $capacityRow['name'];
                    }
                }
                
                $allData[] = [
                    'customer_name' => searchCustNameById($row['customers'], $db),
                    'customer_address' => searchCustomerBranchAddressById($row['branch'], $db),
                    'capacity' => $capacityName
                ];
            }
            
            // Split data into chunks of 3 rows for page breaks
            $chunks = array_chunk($allData, 3);
            $message = '';

            foreach ($chunks as $chunkIndex => $chunk) {
                $message .= '
                <html>
                <head>
                <meta charset="utf-8">
                <title>A4 Document</title>
                <!-- Bootstrap 4 CDN -->
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
                <style>
                    body {
                    background: #f0f0f0;
                        font-size: 16pt; /* Increase base font size */
                        line-height: 1.6;
                    }
                    .table-dark-border th,
                    .table-dark-border td {
                        border: 2px solid #000 !important; /* Dark bold borders */
                    }
                    .table-dark-border {
                        border: 2px solid #000 !important;
                        font-size: 14pt;
                    }
                    .table-dark-border thead th {
                        font-size: 15pt;
                        text-align: center;
                    }
                    .table-dark-border td {
                        vertical-align: middle;
                    }
                </style>
                </head>
                <body style="background:#f0f0f0;">
                    <div class="container-fluid py-5" style="padding-left: 100px; padding-right: 50px;">
                        <div class="text-center mb-4">
                            <h2 class="font-weight-bold mb-1">'.$companyName.'<small style="font-size: 12pt">(Co. No. '.$companyOldRoc.')</small></h2>
                            <p style="margin:0;">'.$companyAddress.'<br>
                            Tel : '.$companyTel.' / 622 5797 Fax : '.$companyFax.'<br>
                            Email : '.$companyEmail.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Website : www.spwt.my
                            </p>
                        </div>

                        <hr class="border-dark mb-4">

                        <div class="row mb-4">
                            <div class="col-8">
                                <p class="mb-0 font-weight-bold">METROLOGY CORPORATION (M) SDN. BHD.</p>
                                <p class="mb-0">
                                    Cawangan Alor Setar<br>
                                    KM10 Jalan Sultanah Sambungan,<br>
                                    05350 Alor Setar, <br>
                                    Kedah.
                                </p>
                                <br>
                                <p class="mb-0">
                                    <b><u>U.P : En. Sufian (011-1981 3100)</u></b>
                                </p>
                                <p class="mb-0">
                                    Tel : 604 - 734 7692<br>
                                    Fax : 604 - 740 7666
                                </p>
                            </div>
                            <div class="col-4">
                                <br>
                                <p class="mb-0">Tarikh : '.$printDate.'</p>
                                <p class="mb-0">Ruj. Kami : MCMAS/MEI-2025</p>
                            </div>
                        </div>

                        <p class="mb-3">Tuan,</p>
                        
                        <p class="mb-3">
                            Per : <span class="font-weight-bold text-uppercase"><u>Pengesahan / Penetusan Timbang Kenderaan Pada Bulan Mei 2025</u></span>
                        </p>

                        <p>Merujuk kepada perkara tersebut di atas, kami ingin memohon pengesahan jabatan tuan tentang tarikh dan masa untuk kerja-kerja penetusan penimbang seperti di bawah :-</p>

                        <table class="table table-dark-border w-100 mb-4">
                            <thead>
                                <tr>
                                    <th style="width:10%;">No.</th>
                                    <th style="width:70%;">Pelanggan Dan Alamat Timbang</th>
                                    <th style="width:20%;">Capacity</th>
                                </tr>
                            </thead>
                            <tbody>';
                
                // Add table rows for this chunk (maximum 3 rows)
                $startNumber = ($chunkIndex * 3) + 1;
                for ($i = 0; $i < 3; $i++) {
                    $rowNumber = $startNumber + $i;
                    if (isset($chunk[$i])) {
                        // Real data
                        $message .= '
                                <tr>
                                    <td class="text-center">'.$rowNumber.'.</td>
                                    <td>'.$chunk[$i]['customer_name'].'<br>'.$chunk[$i]['customer_address'].'</td>
                                    <td class="font-weight-bold text-center">'.$chunk[$i]['capacity'].'</td>
                                </tr>';
                    } else {
                        // Empty row to maintain table structure
                        $message .= '
                                <tr>
                                    <td class="text-center">'.$rowNumber.'.</td>
                                    <td>&nbsp;</td>
                                    <td class="text-center">&nbsp;</td>
                                </tr>';
                    }
                }
                
                $message .= '
                            </tbody>
                        </table>

                        <p>Kami dengan sukacitanya berharap pihab tuan dapat mengesahkan jadual penetusan semula kepada kami.</p>
                        <p>Sekian Terima Kasih.</p>
                        <p>Yang Benar,</p>
                        <p class="font-weight-bold mb-4">SP WEIGHING SYSTEMS SDN. BHD.</p>

                        <br>
                        <p class="mt-2">..............................................................<br>
                            Syaswani<br>
                            012-5201218
                        </p>

                    </div>
                </body>
                </html>

                ';
            }            // Return the results as JSON
            echo json_encode(array('status' => 'success', 'message' => $message));
        } 
        else {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Statement preparation failed"
                ));
        }

        $select_stmt->close();
    }
    $db->close();
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