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

    // Query to select records for copying
    $query = "SELECT * FROM other_validations WHERE status = 'Valid' AND deleted = 0";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($records)) {
        echo "No records found for processing.";
        exit;
    }

    // Prepare the insert query
    $insertQuery = "INSERT INTO other_validations (
        type, dealer, dealer_branch, validate_by, customer_type, customer, branch, auto_form_no, 
        machines, unit_serial_no, manufacturing, brand, model, capacity, size, calibrations, 
        last_calibration_date, expired_calibration_date, cert_file_path1, cert_file_path2, cert_file_path3, 
        cert_file_path4, cert_file_path5, validation_date, status, created_datetime, update_datetime, 
        reason_id, other_reason, existing_id, deleted, renewed, jenis_alat
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )";
    $insertStmt = $pdo->prepare($insertQuery);

    // Begin transaction
    $pdo->beginTransaction();

    try {
        $processedIds = [];
        foreach ($records as $record) {
            // Insert the copied record with modifications
            $insertStmt->execute([
                $record['type'], $record['dealer'], $record['dealer_branch'], $record['validate_by'], 
                $record['customer_type'], $record['customer'], $record['branch'], $record['auto_form_no'], 
                $record['machines'], $record['unit_serial_no'], $record['manufacturing'], $record['brand'], 
                $record['model'], $record['capacity'], $record['size'], $record['calibrations'], 
                $record['last_calibration_date'], $record['expired_calibration_date'], $record['cert_file_path1'], 
                $record['cert_file_path2'], $record['cert_file_path3'], $record['cert_file_path4'], 
                $record['cert_file_path5'], null, // Reset validation_date
                'Pending', // Change status to Pending
                date('Y-m-d H:i:s'), // New created_datetime
                date('Y-m-d H:i:s'), // New update_datetime
                $record['reason_id'], $record['other_reason'], $record['id'], // Link to existing record
                '0', // Set deleted to 0
                'N', // Set renewed to 1
                $record['jenis_alat']
            ]);

            // Add to processed IDs
            $processedIds[] = $record['id'];
        }

        // Optional: Update original records (e.g., mark as copied or update status)
        if (!empty($processedIds)) {
            $updateQuery = "UPDATE other_validations SET renewed = 'Y' WHERE id IN (" . implode(',', $processedIds) . ")";
            $pdo->exec($updateQuery);
        }

        // Commit transaction
        $pdo->commit();
        echo count($processedIds) . " Records copied successfully.";
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $pdo->rollBack();
        throw $e;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$pdo = null;
?>
