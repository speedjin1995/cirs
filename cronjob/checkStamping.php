<?php
$config = include(dirname(__DIR__, 3) . '/db_config.php');

// Database connection
$host = $config['host'];
$dbname = $config['database'];
$username = $config['username'];
$password = $config['password'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the first and last day of the next month
    $firstDayNextMonth = date('Y-m-01 00:00:00', strtotime('first day of next month'));
    $lastDayNextMonth = date('Y-m-t 23:59:59', strtotime('last day of next month'));

    // Query to select records where due_date is in the next month
    $query = "SELECT * FROM stamping WHERE status = 'Complete' AND due_date BETWEEN :firstDay AND :lastDay AND renewed='N' AND deleted='0'";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':firstDay', $firstDayNextMonth);
    $stmt->bindParam(':lastDay', $lastDayNextMonth);
    $stmt->execute();

    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($records)) {
        echo "No records found for processing.";
        exit;
    }

    // Prepare insert statement for stamping
    $insertQuery = "INSERT INTO stamping (
        type, dealer, dealer_branch, customer_type, customers, branch, products, brand, machine_type, model, 
        capacity, capacity_high, assignTo, serial_no, validate_by, jenis_alat, trade, no_daftar, no_daftar_lama, no_daftar_baru, 
        pin_keselamatan, siri_keselamatan, include_cert, borang_d, borang_e, cawangan, invoice_no, cash_bill, stamping_type, last_year_stamping_date, stamping_date, 
        due_date, pic, customer_pic, quotation_no, quotation_date, purchase_no, purchase_date, remarks, log, 
        unit_price, cert_price, total_amount, sst, subtotal_amount, reason_id, other_reason, existing_id, status, 
        renewed
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $insertStmt = $pdo->prepare($insertQuery);

    // Prepare insert statement for stamping_ext
    $insertExtQuery = "INSERT INTO stamping_ext (
        stamp_id, penentusan_baru, penentusan_semula, kelulusan_mspk, no_kelulusan, indicator_serial, 
        platform_country, platform_type, size, jenis_pelantar, jenis_penunjuk, alat_type, questions, 
        steelyard, bilangan_kaunterpois, nilais, bentuk_dulang, class, batu_ujian, batu_ujian_lain, other_info, 
        load_cell_country, load_cell_no, load_cells_info
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $insertExtStmt = $pdo->prepare($insertExtQuery);

    // Begin transaction
    $pdo->beginTransaction();

    try {
        $processedIds = [];
        foreach ($records as $record) {
            // Insert into stamping
            $insertStmt->execute([
                $record['type'], $record['dealer'], $record['dealer_branch'], $record['customer_type'], $record['customers'],
                $record['branch'], $record['products'], $record['brand'], $record['machine_type'], $record['model'],
                $record['capacity'], $record['capacity_high'], $record['assignTo'], $record['serial_no'], $record['validate_by'],
                $record['jenis_alat'], $record['trade'], $record['no_daftar_baru'], $record['no_daftar_baru'], null, null, null, 
                $record['include_cert'], null, null, $record['cawangan'], null, null, 'RENEWAL', $record['stamping_date'], 
                $record['due_date'], null, $record['pic'], $record['customer_pic'], null, null, null, null, $record['remarks'], 
                $record['log'], $record['unit_price'], $record['cert_price'], $record['total_amount'], $record['sst'], $record['subtotal_amount'], 
                $record['reason_id'], $record['other_reason'], $record['id'], 'Pending', 'N'
            ]);

            // Get the last inserted ID
            $newStampId = $pdo->lastInsertId();

            // Fetch related records from stamping_ext
            $extQuery = "SELECT * FROM stamping_ext WHERE stamp_id = :oldStampId";
            $extStmt = $pdo->prepare($extQuery);
            $extStmt->bindParam(':oldStampId', $record['id']);
            $extStmt->execute();

            $extRecords = $extStmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($extRecords)) {
                foreach ($extRecords as $extRecord) {
                    $insertExtStmt->execute([
                        $newStampId, $extRecord['penentusan_baru'], $extRecord['penentusan_semula'], $extRecord['kelulusan_mspk'],
                        $extRecord['no_kelulusan'], $extRecord['indicator_serial'], $extRecord['platform_country'], $extRecord['platform_type'],
                        $extRecord['size'], $extRecord['jenis_pelantar'], $extRecord['jenis_penunjuk'], $extRecord['alat_type'],
                        $extRecord['questions'], $extRecord['steelyard'], $extRecord['bilangan_kaunterpois'], $extRecord['nilais'], $extRecord['bentuk_dulang'], 
                        $extRecord['class'], $extRecord['batu_ujian'], $extRecord['batu_ujian_lain'], $extRecord['other_info'], $extRecord['load_cell_country'],
                        $extRecord['load_cell_no'], $extRecord['load_cells_info']
                    ]);
                }
            }

            // Add to processed IDs
            $processedIds[] = $record['id'];
        }

        // Update the old stamping records to set renewed = 'Y'
        if (!empty($processedIds)) {
            $updateQuery = "UPDATE stamping SET renewed = 'Y' WHERE id IN (" . implode(',', $processedIds) . ")";
            $pdo->exec($updateQuery);
        }

        // Commit transaction
        $pdo->commit();
        echo count($processedIds)." Records processed successfully.";
    } 
    catch (Exception $e) {
        // Rollback transaction in case of error
        $pdo->rollBack();
        throw $e;
    }
} 
catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$pdo = null;
?>