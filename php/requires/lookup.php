<?php
function searchCompanyBranchById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM company_branches WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['branch_name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCompanyBranchAddressById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM company_branches WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = '<b>'.$row['branch_name'].'</b><br>'.$row['address_line_1'].'<br>'.$row['address_line_2'].'<br>'.$row['address_line_3'].'<br>'.$row['address_line_4'];
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

function searchCustomerBranchAddressById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM branches WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['address'].' '.$row['address2'].' '.$row['address3'].' '.$row['address4'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCustomerBranchAddressById2($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM branches WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['address'].'<br>'.$row['address2'].'<br>'.$row['address3'].'<br>'.$row['address4'];
        }
        $select_stmt->close();
    }

    return $id;
}

// Customer Code by Id
function searchCustCodeById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM customers WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['customer_code'];
        }
        $select_stmt->close();
    }

    return $id;
}

// Reseller Name by Id
function searchResellerNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM dealer WHERE id=?")) {
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

function searchResellerAddressById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM dealer WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = '<b>'.$row['customer_name'].'</b><br>'.$row['customer_address'].'<br>'.$row['address2'].'<br>'.$row['address3'].'<br>'.$row['address4'];
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

function searchMachinenameNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM machine_names WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['machine_name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCapacityNameById($value, $db) {
    $id = '';

    if ($select_stmt = $db->prepare("SELECT * FROM capacity WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['name'];
        }
        $select_stmt->close();
    } 

    return $id;
}

function searchCapacityById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM capacity WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $idname = $row['name'];
            $id1 = explode("X",$idname)[0];
            $id = explode("x",$id1)[0];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCapacityUnitById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM capacity WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $capacity = $row['capacity'];
            $unit = searchUnitNameById($row['units'], $db);

            $id = $capacity.$unit;
        }
        $select_stmt->close();
    }

    return $id;
}

function searchAlatNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM alat WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['alat'];
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

function searchStaffNameById($value, $db) {
    $id = null;

    if ($value == null || $value == ''){
        return '-';
    }else if ($value == 0){
        return '*SYSTEM';
    }

    if ($select_stmt = $db->prepare("SELECT * FROM users WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchStaffICById($value, $db) {
    $id = '000000-00-0000';

    if ($select_stmt = $db->prepare("SELECT * FROM users WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['ic_number'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchJenisAlatNameByid($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM alat WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['alat'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCountryById($value, $db) {
    $id = '';

    if ($select_stmt = $db->prepare("SELECT * FROM country WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchUnitNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM units WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['units'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchSizeNameById($value, $db) {
    $id = '';

    if ($select_stmt = $db->prepare("SELECT * FROM size WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['size'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCountryNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM country WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function convertDatetimeToDate($datetime){
    $date = new DateTime($datetime);
  
    return $date->format('d/m/Y'); 
}

function searchStateNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM state WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['state'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchReasonById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM reasons WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['reason'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchFilePathById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM files WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['filepath'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchLoadCellById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM load_cells WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['load_cell'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCustomerBranchById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM branches WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['branch_name'];
        }
        $select_stmt->close();
    }

    return $id;
}

################### Upload Stamping Lookup ###################
// Id by Name
function searchCompanyBranchIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM company_branches WHERE branch_name=? AND deleted='0'")) {
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

function searchCustomerBranchIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM branches WHERE branch_name=? AND deleted='0'")) {
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


function searchCustIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM customers WHERE customer_name=? AND deleted='0'")) {
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

function searchDealerIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM dealer WHERE customer_name=? AND deleted='0'")) {
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

function searchCountryIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM country WHERE name=? AND deleted='0'")) {
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

    if ($select_stmt = $db->prepare("SELECT * FROM brand WHERE brand=? AND deleted='0'")) {
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

    if ($select_stmt = $db->prepare("SELECT * FROM model WHERE model=? AND deleted='0'")) {
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

    if ($select_stmt = $db->prepare("SELECT * FROM machines WHERE machine_type=? AND deleted='0'")) {
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

    if ($select_stmt = $db->prepare("SELECT * FROM capacity WHERE name=? AND deleted='0'")) {
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

function searchJenisAlatIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM alat WHERE alat=? AND deleted='0'")) {
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

    if ($select_stmt = $db->prepare("SELECT * FROM validators WHERE validator=? AND deleted='0'")) {
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

function searchStaffIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM users WHERE name=? AND deleted='0'")) {
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

function searchStateIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM state WHERE state=? AND deleted='0'")) {
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

function searchMachinenameIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM machine_names WHERE machine_name=? AND deleted='0'")) {
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