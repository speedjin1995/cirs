<?php
// Database connection
$host = 'srv605.hstgr.io';
//$host = 'localhost';
$dbname = 'u664110560_cirs';
$username = 'u664110560_cirs';
$password = 'Aa@111222333';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the first and last day of the next month
    $firstDayNextMonth = date('Y-m-01 00:00:00', strtotime('first day of next month'));
    $lastDayNextMonth = date('Y-m-t 23:59:59', strtotime('last day of next month'));

    // Query to select records where due_date is in the next month
    $query = "SELECT * FROM stamping WHERE status IN ('Complete', 'Cancelled') AND due_date BETWEEN :firstDay AND :lastDay";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':firstDay', $firstDayNextMonth);
    $stmt->bindParam(':lastDay', $lastDayNextMonth);
    $stmt->execute();

    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare insert statement
    $insertQuery = "INSERT INTO stamping (customer_type, customers, address1, address2, address3, brand, machine_type, model, capacity, serial_no, 
                    validate_by, jenis_alat, no_daftar, pin_keselamatan, siri_keselamatan, include_cert, borang_d, invoice_no, cash_bill, 
                    stamping_date, due_date, pic, customer_pic, quotation_no, quotation_date, purchase_no, purchase_date, remarks, 
                    unit_price, cert_price, total_amount, sst, subtotal_amount, products, existing_id, status, stamping_type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $pdo->prepare($insertQuery);

    // Loop through the records and insert them as new records
    foreach ($records as $record) {
        $insertStmt->execute([
            $record['customer_type'], $record['customers'], $record['address1'], $record['address2'], $record['address3'],
            $record['brand'], $record['machine_type'], $record['model'], $record['capacity'], $record['serial_no'],
            $record['validate_by'], $record['jenis_alat'], $record['no_daftar'], $record['pin_keselamatan'], $record['siri_keselamatan'],
            $record['include_cert'], $record['borang_d'], $record['invoice_no'], $record['cash_bill'], $record['stamping_date'],
            $record['due_date'], $record['pic'], $record['customer_pic'], $record['quotation_no'], $record['quotation_date'],
            $record['purchase_no'], $record['purchase_date'], $record['remarks'], $record['unit_price'], $record['cert_price'],
            $record['total_amount'], $record['sst'], $record['subtotal_amount'], $record['products'], $record['id'], 'Pending', 'RENEWAL'
        ]);
    }

    echo "Records copied successfully.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$pdo = null;