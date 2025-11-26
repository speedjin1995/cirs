<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);

session_start();

$uid = $_SESSION['userID'];

if (isset($_POST['brand'], $_POST['machineType'], $_POST['jenisAlat'], $_POST['model'], $_POST['makeIn'], $_POST['capacity'], $_POST['serial'], $_POST['trade'])) {
    $brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
    $makeIn = filter_input(INPUT_POST, 'makeIn', FILTER_SANITIZE_STRING);
    $machineType = filter_input(INPUT_POST, 'machineType', FILTER_SANITIZE_STRING);
    $jenisAlat = filter_input(INPUT_POST, 'jenisAlat', FILTER_SANITIZE_STRING);
    $model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
    $serial = filter_input(INPUT_POST, 'serial', FILTER_SANITIZE_STRING);
    $trade = filter_input(INPUT_POST, 'trade', FILTER_SANITIZE_STRING);


    $ownershipStatus = null;
    $machineName = null;
    $machineLocation = null;
    $machineArea = null;
    $machineSerialNo = null;
    $product = null;
    $dealer = null;

    $logs = array();

    if (isset($_POST['product']) && $_POST['product'] != null && $_POST['product'] != "") {
        $product = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_STRING);
    } else {
        /* TJW SHOULD E NO NEED??
        if ($select_stmtP = $db->prepare("SELECT id FROM products WHERE machine_type=? AND jenis_alat=? AND capacity=? AND validator=?")) {
            $select_stmtP->bind_param('ssss', $machineType, $jenisAlat, $capacity, $validator);
            $select_stmtP->execute();
            $resultP = $select_stmtP->get_result();

            if ($rowP = $resultP->fetch_assoc()) {
                $product = $rowP['id'];
            } 
            else {
                if(isset($_POST['unitPrice']) && $_POST['unitPrice']!=null && $_POST['unitPrice']!="" && $_POST['unitPrice']!="0.00"){
                    // Customer does not exist, create a new customer
                    if ($insert_stmtP = $db->prepare("INSERT INTO products (name, machine_type, jenis_alat, capacity, validator, price) VALUES (?, ?, ?, ?, ?, ?)")) {
                        $pname = 'product'.$machineType.$jenisAlat.$capacity.$validator;
                        $insert_stmtP->bind_param('ssssss', $pname , $machineType, $jenisAlat, $capacity, $validator, $_POST['unitPrice']);

                        if ($insert_stmtP->execute()) {
                            $product = $insert_stmtP->insert_id;
                        } 

                        $insert_stmtP->close();
                    }
                }
            }

            $select_stmtP->close();
        }*/
    }

    if (isset($_POST['jenisAlatName']) && $_POST['jenisAlatName'] != null && $_POST['jenisAlatName'] != "") {
        $jenisAlatName = $_POST['jenisAlatName'];
    }

    if (isset($_POST['ownershipStatus']) && $_POST['ownershipStatus'] != null && $_POST['ownershipStatus'] != "") {
        $ownershipStatus = $_POST['ownershipStatus'];
    }

    if (isset($_POST['machineName']) && $_POST['machineName'] != null && $_POST['machineName'] != "") {
        $machineName = $_POST['machineName'];
    }

    if (isset($_POST['machineLocation']) && $_POST['machineLocation'] != null && $_POST['machineLocation'] != "") {
        $machineLocation = $_POST['machineLocation'];
    }

    if (isset($_POST['machineArea']) && $_POST['machineArea'] != null && $_POST['machineArea'] != "") {
        $machineArea = $_POST['machineArea'];
    }

    if (isset($_POST['machineSerialNo']) && $_POST['machineSerialNo'] != null && $_POST['machineSerialNo'] != "") {
        $machineSerialNo = $_POST['machineSerialNo'];
    }

    if (isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != '') {
        //Updated datetime
        $currentDateTime = date('Y-m-d H:i:s');

        if (
            $update_stmt = $db->prepare("UPDATE stamping SET brand=?, machine_type=?, model=?, make_in=?, capacity=?, serial_no=?, ownership_status=?, jenis_alat=?, machine_name=?, machine_location=?, machine_area=?, machine_serial_no=?, trade=?
		, log=?, updated_datetime=? WHERE id=?")
        ) {
            $data = json_encode($logs);
            $update_stmt->bind_param(
                'sssssssssssssssi',
                $brand,
                $machineType,
                $model,
                $makeIn,
                $capacity,
                $serial,
                $ownershipStatus,
                $jenisAlat,
                $machineName,
                $machineLocation,
                $machineArea,
                $machineSerialNo,
                $trade,
                $data,
                $currentDateTime,
                $_POST['id']
            );

            // Execute the prepared query.
            if (!$update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status" => "failed",
                        "message" => $update_stmt->error
                    )
                );
            } else {
                $stampingId = $_POST['id'];

                // For ATK Additional fields
                if (str_contains($jenisAlatName, 'ATK')) {
                    $penentusan_semula = null;
                    $kelulusan_mspk = null;
                    $no_kelulusan = null;
                    $indicator_serial = null;
                    $platform_country = null;
                    $platform_type = null;
                    $size = null;
                    $jenis_pelantar = null;
                    $others = null;
                    $load_cell_country = null;
                    $load_cell_no = null;
                    $load_cells_info = [];

                    $no = $_POST['no'] ?? [];
                    $loadCells = $_POST['loadCells'] ?? [];
                    $loadCellBrand = $_POST['loadCellBrand'] ?? [];
                    $loadCellModel = $_POST['loadCellModel'] ?? [];
                    $loadCellCapacity = $_POST['loadCellCapacity'] ?? [];
                    $loadCellSerial = $_POST['loadCellSerial'] ?? [];

                    if (isset($no) && $no != null && count($no) > 0) {
                        for ($i = 0; $i < count($no); $i++) {
                            $load_cells_info[] = array(
                                "no" => $no[$i],
                                "loadCells" => $loadCells[$i],
                                "loadCellBrand" => $loadCellBrand[$i],
                                "loadCellModel" => $loadCellModel[$i],
                                "loadCellCapacity" => $loadCellCapacity[$i],
                                "loadCellSerial" => $loadCellSerial[$i]
                            );
                        }
                    }

                    if (isset($_POST['penentusanBaru']) && $_POST['penentusanBaru'] != null && $_POST['penentusanBaru'] != "") {
                        $penentusan_baru = $_POST['penentusanBaru'];
                    }

                    if (isset($_POST['penentusanBaru']) && $_POST['penentusanBaru'] != null && $_POST['penentusanBaru'] != "") {
                        $penentusan_baru = $_POST['penentusanBaru'];
                    }

                    if (isset($_POST['penentusanSemula']) && $_POST['penentusanSemula'] != null && $_POST['penentusanSemula'] != "") {
                        $penentusan_semula = $_POST['penentusanSemula'];
                    }

                    if (isset($_POST['kelulusanMSPK']) && $_POST['kelulusanMSPK'] != null && $_POST['kelulusanMSPK'] != "") {
                        $kelulusan_mspk = $_POST['kelulusanMSPK'];
                    }

                    if (isset($_POST['noMSPK']) && $_POST['noMSPK'] != null && $_POST['noMSPK'] != "") {
                        $no_kelulusan = $_POST['noMSPK'];
                    }

                    if (isset($_POST['noSerialIndicator']) && $_POST['noSerialIndicator'] != null && $_POST['noSerialIndicator'] != "") {
                        $indicator_serial = $_POST['noSerialIndicator'];
                    }

                    if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
                        $platform_country = $_POST['platformCountry'];
                    }

                    if (isset($_POST['platformType']) && $_POST['platformType'] != null && $_POST['platformType'] != "") {
                        $platform_type = $_POST['platformType'];
                    }

                    if (isset($_POST['size']) && $_POST['size'] != null && $_POST['size'] != "" && $_POST['size'] != '-') {
                        $size = $_POST['size'];
                    }

                    if (isset($_POST['jenisPelantar']) && $_POST['jenisPelantar'] != null && $_POST['jenisPelantar'] != "") {
                        $jenis_pelantar = $_POST['jenisPelantar'];
                    }

                    if (isset($_POST['others']) && $_POST['others'] != null && $_POST['others'] != "") {
                        $others = $_POST['others'];
                    }

                    if (isset($_POST['loadCellCountry']) && $_POST['loadCellCountry'] != null && $_POST['loadCellCountry'] != "") {
                        $load_cell_country = $_POST['loadCellCountry'];
                    }

                    if (isset($_POST['noOfLoadCell']) && $_POST['noOfLoadCell'] != null && $_POST['noOfLoadCell'] != "") {
                        $load_cell_no = $_POST['noOfLoadCell'];
                    }

                    if (
                        $insert_stmt2 = $db->prepare("UPDATE stamping_ext SET penentusan_baru = ?, penentusan_semula = ?, kelulusan_mspk = ?, no_kelulusan = ?, indicator_serial = ?, platform_country = ?, platform_type = ?, 
					size = ?, jenis_pelantar = ?, other_info = ?, load_cell_country = ?, load_cell_no = ?, load_cells_info = ? WHERE stamp_id = ?")
                    ) {
                        $data = json_encode($load_cells_info);
                        $insert_stmt2->bind_param(
                            'ssssssssssssss',
                            $penentusan_baru,
                            $penentusan_semula,
                            $kelulusan_mspk,
                            $no_kelulusan,
                            $indicator_serial,
                            $platform_country,
                            $platform_type,
                            $size,
                            $jenis_pelantar,
                            $others,
                            $load_cell_country,
                            $load_cell_no,
                            $data,
                            $_POST['id']
                        );
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                // For ATS (H) Additional fields
                if (str_contains($jenisAlatName, 'ATS (H)')) {
                    $platform_country = null;

                    if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
                        $platform_country = $_POST['platformCountry'];
                    }

                    if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ? WHERE stamp_id = ?")) {
                        $insert_stmt2->bind_param('ss', $platform_country, $_POST['id']);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                // For ATS Additional fields
                if (str_contains($jenisAlatName, 'ATS') && !str_contains($jenisAlatName, 'ATS (H)')) {
                    $platform_country = null;

                    if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
                        $platform_country = $_POST['platformCountry'];
                    }

                    if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ? WHERE stamp_id = ?")) {
                        $insert_stmt2->bind_param('ss', $platform_country, $_POST['id']);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                // For ATP (MOTORCAR) Additional fields
                if (str_contains($jenisAlatName, 'ATP (MOTORCAR)')) {
                    $platform_country = null;
                    $jenis_penunjuk = null;

                    if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
                        $platform_country = $_POST['platformCountry'];
                    }

                    if (isset($_POST['steelyard']) && $_POST['steelyard'] != null && $_POST['steelyard'] != "") {
                        $steelyard = $_POST['steelyard'];
                    }

                    if (isset($_POST['bilanganKaunterpois']) && $_POST['bilanganKaunterpois'] != null && $_POST['bilanganKaunterpois'] != "") {
                        $bilanganKaunterpois = $_POST['bilanganKaunterpois'];
                    }

                    $nilais = [
                        [
                            "no" => 1,
                            "nilai" => $_POST['nilai1'] ?? null,
                        ],
                        [
                            "no" => 2,
                            "nilai" => $_POST['nilai2'] ?? null,
                        ],
                        [
                            "no" => 3,
                            "nilai" => $_POST['nilai3'] ?? null,
                        ],
                        [
                            "no" => 4,
                            "nilai" => $_POST['nilai4'] ?? null,
                        ],
                        [
                            "no" => 5,
                            "nilai" => $_POST['nilai5'] ?? null,
                        ],
                        [
                            "no" => 6,
                            "nilai" => $_POST['nilai6'] ?? null,
                        ]
                    ];

                    $nilaiString = json_encode($nilais, JSON_PRETTY_PRINT);

                    if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, steelyard = ?, bilangan_kaunterpois = ?, nilais = ? WHERE stamp_id = ?")) {
                        $insert_stmt2->bind_param('sssss', $platform_country, $steelyard, $bilanganKaunterpois, $nilaiString, $_POST['id']);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                // For AUTO_PACKER Additional fields
                if (str_contains($jenisAlatName, 'ATP-AUTO MACHINE')) {
                    $platform_country = null;
                    $jenis_penunjuk = null;

                    if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
                        $platform_country = $_POST['platformCountry'];
                    }

                    if (isset($_POST['jenis_penunjuk']) && $_POST['jenis_penunjuk'] != null && $_POST['jenis_penunjuk'] != "") {
                        $jenis_penunjuk = $_POST['jenis_penunjuk'];
                    }

                    if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, jenis_penunjuk=? WHERE stamp_id = ?")) {
                        $insert_stmt2->bind_param('sss', $platform_country, $jenis_penunjuk, $_POST['id']);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }

                    // $platform_country = null;

                    // if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
                    // 	$platform_country = $_POST['platformCountry'];
                    // }

                    // $nilais = [
                    // 	[
                    // 		"no" => 1,
                    // 		"nilai" => $_POST['nilai1'] ?? null,
                    // 	],
                    // 	[
                    // 		"no" => 2,
                    // 		"nilai" => $_POST['nilai2'] ?? null,
                    // 	],
                    // 	[
                    // 		"no" => 3,
                    // 		"nilai" => $_POST['nilai3'] ?? null,
                    // 	],
                    // 	[
                    // 		"no" => 4,
                    // 		"nilai" => $_POST['nilai4'] ?? null,
                    // 	],
                    // 	[
                    // 		"no" => 5,
                    // 		"nilai" => $_POST['nilai5'] ?? null,
                    // 	],
                    // 	[
                    // 		"no" => 6,
                    // 		"nilai" => $_POST['nilai6'] ?? null,
                    // 	]
                    // ];

                    // $nilaiString = json_encode($nilais, JSON_PRETTY_PRINT);

                    // if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country=?, nilais=? WHERE stamp_id = ?")){
                    // 	$insert_stmt2->bind_param('sss', $platform_country, $nilaiString, $_POST['id']);
                    // 	$insert_stmt2->execute();
                    // 	$insert_stmt2->close();
                    // }
                }

                // For ATP Additional fields
                if (str_contains($jenisAlatName, 'ATP') && !str_contains($jenisAlatName, 'ATP (MOTORCAR)') && !str_contains($jenisAlatName, 'ATP-AUTO MACHINE')) {
                    $platform_country = null;
                    $jenis_penunjuk = null;

                    if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
                        $platform_country = $_POST['platformCountry'];
                    }

                    if (isset($_POST['jenis_penunjuk']) && $_POST['jenis_penunjuk'] != null && $_POST['jenis_penunjuk'] != "") {
                        $jenis_penunjuk = $_POST['jenis_penunjuk'];
                    }

                    if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, jenis_penunjuk=? WHERE stamp_id = ?")) {
                        $insert_stmt2->bind_param('sss', $platform_country, $jenis_penunjuk, $_POST['id']);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                // For ATN Additional fields
                if ((str_contains($jenisAlatName, 'ATN'))) {
                    $platform_country = null;
                    $alat_type = null;
                    $bentuk_dulang = null;

                    if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
                        $platform_country = $_POST['platformCountry'];
                    }

                    if (isset($_POST['alat_type']) && $_POST['alat_type'] != null && $_POST['alat_type'] != "") {
                        $alat_type = $_POST['alat_type'];
                    }

                    if (isset($_POST['bentuk_dulang']) && $_POST['bentuk_dulang'] != null && $_POST['bentuk_dulang'] != "") {
                        $bentuk_dulang = $_POST['bentuk_dulang'];
                    }

                    if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, alat_type=?, bentuk_dulang=? WHERE stamp_id = ?")) {
                        $insert_stmt2->bind_param('ssss', $platform_country, $alat_type, $bentuk_dulang, $_POST['id']);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                // For ATE Additional fields
                if (str_contains($jenisAlatName, 'ATE')) {
                    $platform_country = null;
                    $class = null;
                    $bentuk_dulang = null;

                    if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
                        $platform_country = $_POST['platformCountry'];
                    }

                    if (isset($_POST['class']) && $_POST['class'] != null && $_POST['class'] != "") {
                        $class = $_POST['class'];
                    }

                    if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, class=? WHERE stamp_id = ?")) {
                        $insert_stmt2->bind_param('sss', $platform_country, $class, $_POST['id']);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                // For SLL Additional fields
                if (str_contains($jenisAlatName, 'SLL')) {
                    $platform_country = null;
                    $alat_type = null;

                    if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
                        $platform_country = $_POST['platformCountry'];
                    }

                    if (isset($_POST['alat_type']) && $_POST['alat_type'] != null && $_POST['alat_type'] != "") {
                        $alat_type = $_POST['alat_type'];
                    }

                    $questions = [
                        [
                            "no" => 1,
                            "answer" => $_POST['question1'] ?? null,
                        ],
                        [
                            "no" => 2,
                            "answer" => $_POST['question2'] ?? null,
                        ],
                        [
                            "no" => 3,
                            "answer" => $_POST['question3'] ?? null,
                        ],
                        [
                            "no" => 4,
                            "answer" => $_POST['question4'] ?? null,
                        ],
                        [
                            "no" => 5.1,
                            "answer" => $_POST['question5_1'] ?? null,
                        ],
                        [
                            "no" => 5.2,
                            "answer" => $_POST['question5_2'] ?? null,
                        ],
                        [
                            "no" => 6,
                            "answer" => $_POST['question6'] ?? null,
                        ],
                        [
                            "no" => 7,
                            "answer" => $_POST['question7'] ?? null,
                        ],
                    ];

                    $questionString = json_encode($questions, JSON_PRETTY_PRINT);

                    if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country=?, alat_type=?, questions=? WHERE stamp_id = ?")) {
                        $insert_stmt2->bind_param('ssss', $platform_country, $alat_type, $questionString, $_POST['id']);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                // For BTU (BOX) Additional fields
                if (str_contains($jenisAlatName, 'BTU - (BOX)')) {
                    $btu_info = [];

                    $noOfBtu = $_POST['noOfBtu'] ?? null;
                    $no = $_POST['no'] ?? [];
                    $batuUjian = $_POST['batuUjian'] ?? [];
                    $batuUjianLain = $_POST['batuUjianLain'] ?? [];
                    $penandaanBatuUjian = $_POST['penandaanBatuUjian'] ?? [];
                    $batuDaftarLama = $_POST['batuDaftarLama'] ?? [];
                    $batuDaftarBaru = $_POST['batuDaftarBaru'] ?? [];
                    $batuNoSiriPelekatKeselamatan = $_POST['batuNoSiriPelekatKeselamatan'] ?? [];
                    $batuBorangD = $_POST['batuBorangD'] ?? [];
                    $batuBorangE = $_POST['batuBorangE'] ?? [];

                    if (isset($no) && $no != null && count($no) > 0) {
                        for ($i = 0; $i < count($no); $i++) {
                            $btu_info[] = array(
                                "no" => $no[$i],
                                "batuUjian" => $batuUjian[$i],
                                "batuUjianLain" => $batuUjianLain[$i],
                                "penandaanBatuUjian" => $penandaanBatuUjian[$i],
                                "batuDaftarLama" => $batuDaftarLama[$i],
                                "batuDaftarBaru" => $batuDaftarBaru[$i],
                                "batuNoSiriPelekatKeselamatan" => $batuNoSiriPelekatKeselamatan[$i],
                                "batuBorangD" => $batuBorangD[$i],
                                "batuBorangE" => $batuBorangE[$i]
                            );
                        }
                    }

                    if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET btu_box_qty = ?, btu_box_info = ? WHERE stamp_id = ?")) {
                        $btuInfo = json_encode($btu_info);
                        $insert_stmt2->bind_param('ssi', $noOfBtu, $btuInfo, $_POST['id']);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                // For BTU Additional fields
                if (str_contains($jenisAlatName, 'BTU') && !str_contains($jenisAlatName, 'BTU - (BOX)')) {
                    $platform_country = null;
                    $penandaanBatuUjian = null;
                    $batuUjian = null;
                    $batuUjianLain = null;

                    if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
                        $platform_country = $_POST['platformCountry'];
                    }

                    if (isset($_POST['penandaanBatuUjian']) && $_POST['penandaanBatuUjian'] != null && $_POST['penandaanBatuUjian'] != "") {
                        $penandaanBatuUjian = $_POST['penandaanBatuUjian'];
                    }

                    if (isset($_POST['batuUjian']) && $_POST['batuUjian'] != null && $_POST['batuUjian'] != "") {
                        $batuUjian = $_POST['batuUjian'];
                    }

                    if (isset($_POST['batuUjianLain']) && $_POST['batuUjianLain'] != null && $_POST['batuUjianLain'] != "") {
                        $batuUjianLain = $_POST['batuUjianLain'];
                    }

                    if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, penandaan_batu_ujian = ?, batu_ujian = ?, batu_ujian_lain=? WHERE stamp_id = ?")) {
                        $insert_stmt2->bind_param('sssss', $platform_country, $penandaanBatuUjian, $batuUjian, $batuUjianLain, $_POST['id']);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                // For SIA Additional fields	
                if (str_contains($jenisAlatName, 'SIA')) {
                    $platform_country = null;
                    $nilaiJangka = null;
                    $nilaiJangkaOther = null;
                    $diperbuatDaripada = null;
                    $diperbuatDaripadaOther = null;

                    if (isset($_POST['platformCountry']) && $_POST['platformCountry'] != null && $_POST['platformCountry'] != "") {
                        $platform_country = $_POST['platformCountry'];
                    }

                    if (isset($_POST['nilaiJangka']) && $_POST['nilaiJangka'] != null && $_POST['nilaiJangka'] != "") {
                        $nilaiJangka = $_POST['nilaiJangka'];
                    }

                    if (isset($_POST['nilaiJangkaOther']) && $_POST['nilaiJangkaOther'] != null && $_POST['nilaiJangkaOther'] != "") {
                        $nilaiJangkaOther = $_POST['nilaiJangkaOther'];
                    }

                    if (isset($_POST['diperbuatDaripada']) && $_POST['diperbuatDaripada'] != null && $_POST['diperbuatDaripada'] != "") {
                        $diperbuatDaripada = $_POST['diperbuatDaripada'];
                    }

                    if (isset($_POST['diperbuatDaripadaOther']) && $_POST['diperbuatDaripadaOther'] != null && $_POST['diperbuatDaripadaOther'] != "") {
                        $diperbuatDaripadaOther = $_POST['diperbuatDaripadaOther'];
                    }

                    if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, nilai_jangka = ?, nilai_jangka_other=?, diperbuat_daripada=?, diperbuat_daripada_other=? WHERE stamp_id = ?")) {
                        $insert_stmt2->bind_param('ssssss', $platform_country, $nilaiJangka, $nilaiJangkaOther, $diperbuatDaripada, $diperbuatDaripadaOther, $_POST['id']);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                // For BAP Additional fields	
                if (str_contains($jenisAlatName, 'BAP')) {
                    $pamNo = null;
                    $kelulusanBentuk = null;
                    $alatType = null;
                    $kadarPengaliran = null;
                    $bentukPenunjuk = null;
                    $jenama = null;
                    $jenamaOther = null;

                    if (isset($_POST['pamNo']) && $_POST['pamNo'] != null && $_POST['pamNo'] != "") {
                        $pamNo = $_POST['pamNo'];
                    }

                    if (isset($_POST['kelulusanBentuk']) && $_POST['kelulusanBentuk'] != null && $_POST['kelulusanBentuk'] != "") {
                        $kelulusanBentuk = $_POST['kelulusanBentuk'];
                    }

                    if (isset($_POST['alatType']) && $_POST['alatType'] != null && $_POST['alatType'] != "") {
                        $alatType = $_POST['alatType'];
                    }

                    if (isset($_POST['kadarPengaliran']) && $_POST['kadarPengaliran'] != null && $_POST['kadarPengaliran'] != "") {
                        $kadarPengaliran = $_POST['kadarPengaliran'];
                    }

                    if (isset($_POST['bentukPenunjuk']) && $_POST['bentukPenunjuk'] != null && $_POST['bentukPenunjuk'] != "") {
                        $bentukPenunjuk = $_POST['bentukPenunjuk'];
                    }

                    if (isset($_POST['jenama']) && $_POST['jenama'] != null && $_POST['jenama'] != "") {
                        $jenama = $_POST['jenama'];
                    }

                    if (isset($_POST['jenamaOther']) && $_POST['jenamaOther'] != null && $_POST['jenamaOther'] != "") {
                        $jenamaOther = $_POST['jenamaOther'];
                    }

                    if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET pam_no = ?, kelulusan_bentuk = ?, alat_type=?, kadar_pengaliran=?, bentuk_penunjuk=?, jenama=?, jenama_other=? WHERE stamp_id = ?")) {
                        $insert_stmt2->bind_param('ssssssss', $pamNo, $kelulusanBentuk, $alatType, $kadarPengaliran, $bentukPenunjuk, $jenama, $jenamaOther, $_POST['id']);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                // For SIC Additional fields	
                if (str_contains($jenisAlatName, 'SIC')) {
                    $nilaiMaksimum = null;
                    $bahanPembuat = null;
                    $bahanPembuatOther = null;

                    if (isset($_POST['nilaiMaksimum']) && $_POST['nilaiMaksimum'] != null && $_POST['nilaiMaksimum'] != "") {
                        $nilaiMaksimum = $_POST['nilaiMaksimum'];
                    }

                    if (isset($_POST['bahanPembuat']) && $_POST['bahanPembuat'] != null && $_POST['bahanPembuat'] != "") {
                        $bahanPembuat = $_POST['bahanPembuat'];
                    }

                    if (isset($_POST['bahanPembuatOther']) && $_POST['bahanPembuatOther'] != null && $_POST['bahanPembuatOther'] != "") {
                        $bahanPembuatOther = $_POST['bahanPembuatOther'];
                    }

                    if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET nilai_jangkaan_maksimum = ?, bahan_pembuat = ?, bahan_pembuat_other = ? WHERE stamp_id = ?")) {
                        $insert_stmt2->bind_param('ssss', $nilaiMaksimum, $bahanPembuat, $bahanPembuatOther, $_POST['id']);
                        $insert_stmt2->execute();
                        $insert_stmt2->close();
                    }
                }

                // UPDATE Stamping System Log
                if (
                    $insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id) 
				VALUES (?, ?, ?)")
                ) {
                    $action = "UPDATE";
                    $insert_stmt3->bind_param('sss', $action, $uid, $_POST['id']);
                    $insert_stmt3->execute();
                    $insert_stmt3->close();
                }

                // Logic to save stamping status timeline
				$stmt = $db->prepare("SELECT quotation_no, quotation_attachment, purchase_no, serial_no, stamping_date, invoice_no, invoice_payment_type, invoice_payment_ref, validator_invoice FROM stamping WHERE id=?");
				$stmt->bind_param('i', $stampingId);
				$stmt->execute();
				$stmt->bind_result($quotation_no, $quotation_attachment, $purchase_no, $serial_no, $stamping_date, $invoice_no, $invoice_payment_type, $invoice_payment_ref, $validator_invoice);
				$stmt->fetch();
				$stmt->close();

                // Statuses and their conditions
                $statuses = [
                    1 => !empty($quotation_no),
                    2 => !empty($quotation_attachment),
                    3 => !empty($purchase_no),
                    4 => !empty($serial_no),
                    5 => !empty($stamping_date),
                    6 => !empty($invoice_no),
                    7 => (!empty($invoice_payment_type) && !empty($invoice_payment_ref)),
                    8 => !empty($validator_invoice),
                ];

                // Status descriptions (should match your miscellaneous table)
                $status_desc = [];
                if ($status_stmt = $db->prepare("SELECT * FROM miscellaneous WHERE code='stamping_status' AND deleted = 0")) {
                    $status_stmt->execute();
                    $result = $status_stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $status_desc[$row['value']] = $row['description'];
                    }
                    $status_stmt->close();
                }

                // Insert log for each status reached, in order
                $created_by = $_SESSION['userID'];
                $now = date('Y-m-d H:i:s');
                foreach ($statuses as $val => $reached) {
                    if ($reached) {
                        // Check if already logged
                        $check = $db->prepare("SELECT id FROM stamping_status_log WHERE stamp_id=? AND status=?");
                        $check->bind_param('is', $stampingId, $status_desc[$val]);
                        $check->execute();
                        $check->store_result();
                        if ($check->num_rows == 0) {
                            $insert = $db->prepare("INSERT INTO stamping_status_log (stamp_id, status, created_by, occurred_at) VALUES (?, ?, ?, ?)");
                            $insert->bind_param('isss', $stampingId, $status_desc[$val], $created_by, $now);
                            $insert->execute();
                            $insert->close();
                        }
                        $check->close();
                    }
                }

                echo json_encode(
                    array(
                        "status" => "success",
                        "message" => "Updated Successfully!!"
                    )
                );
            }

            $update_stmt->close();
        } else {
            $update_stmt->close();
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Error when creating query"
                )
            );
        }

    }
} else {
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Please fill in all the fields"
        )
    );
}

?>