<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['userID'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    foreach ($data as $row) { var_dump($row);die;
        $customers = !empty($row['Customer']) ? searchCust($row['Customer'], $db) : null;
        $brands = !empty($row['Brand']) ? searchBrand($row['Brand'], $db) : null;
        $models = !empty($row['Model']) ? searchModel($row['Model'], $db) : null;
        $machineTypes = !empty($row['Desc']) ? searchMachine($row['Desc'], $db) : null;
        $capacitys = !empty($row['Capacity']) ? searchCapacity($row['Capacity'], $db) : null;
        $validators = !empty($row['Validator']) ? searchValidator($row['Validator'], $db) : null;
        $serials = !empty($row['Serial']) ? $row['Serial'] : null;
        $stampings = !empty($row['Stmp']) ? $row['Stmp'] : null;
        $invoices = !empty($row['Inv_No']) ? $row['Inv_No'] : null;
        $stampDates = !empty($row['StmpDate']) ? DateTime::createFromFormat('d/m/Y', $row['StmpDate'])->format('Y-m-d H:i:s') : null;
        $dueDates = !empty($row['DueDate']) ? DateTime::createFromFormat('d/m/Y', $row['DueDate'])->format('Y-m-d H:i:s') : null;

        if ($insert_stmt = $db->prepare("INSERT INTO stamping (customers, brand, descriptions, model, capacity, serial_no, validate_by, stamping_no, invoice_no, stamping_date, due_date, pic) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssssssssss', $customers, $brands, $machineTypes, $models, $capacitys, $serials, $validators, $stampings, $invoices, $stampDates, $dueDates, $uid);
            $insert_stmt->execute();
            $insert_stmt->close(); // Close the statement after execution
        }
    }

    $db->close();

    echo json_encode(
        array(
            "status"=> "success", 
            "message"=> "Added Successfully!!" 
        )
    );
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}

function searchCust($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM customers WHERE customer_name=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchBrand($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM brand WHERE brand=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchModel($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM model WHERE model=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchMachine($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM machines WHERE machine_type=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCapacity($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM capacity WHERE capacity=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchValidator($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM validators WHERE validator=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}
?>
