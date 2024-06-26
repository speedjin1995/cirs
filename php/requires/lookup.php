<?php
// Id by Name
function searchCustIdByName($value, $db) {
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

function searchBrandIdByName($value, $db) {
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

function searchModelIdByName($value, $db) {
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

function searchMachineIdByName($value, $db) {
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

function searchCapacityIdByName($value, $db) {
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

function searchValidatorIdByName($value, $db) {
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

// Name by Id
function searchCustNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM customers WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['customer_name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchBrandNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM brand WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['brand'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchModelNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM model WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['model'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchMachineNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM machines WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['machine_type'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCapacityNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM capacity WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['capacity'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchValidatorNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM validators WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['validator'];
        }
        $select_stmt->close();
    }

    return $id;
}

?>