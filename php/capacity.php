<?php
require_once "db_connect.php";
require_once 'requires/lookup.php';

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['range_type'], $_POST['capacity'], $_POST['unit'])){
    $range_type = filter_input(INPUT_POST, 'range_type', FILTER_SANITIZE_STRING);
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
    $unit = filter_input(INPUT_POST, 'unit', FILTER_SANITIZE_STRING);
    $unitString = searchUnitNameById($unit, $db);

    $division = null;
    $dunit = null;
    $dunitString = '';
    $capacity2 = null;
    $unit2 = null;
    $unitString2 = '';
    $division2 = null;
    $dunit2 = null;
    $dunitString2 = '';
    
    if(isset($_POST['division']) && $_POST['division'] != null && $_POST['division'] != ''){
        $division = filter_input(INPUT_POST, 'division', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['unitD']) && $_POST['unitD'] != null && $_POST['unitD'] != ''){
        $dunit = filter_input(INPUT_POST, 'unitD', FILTER_SANITIZE_STRING);
        $dunitString = searchUnitNameById($dunit, $db);
    }

    if(isset($_POST['capacity2']) && $_POST['capacity2'] != null && $_POST['capacity2'] != ''){
        $capacity2 = filter_input(INPUT_POST, 'capacity2', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['unit2']) && $_POST['unit2'] != null && $_POST['unit2'] != ''){
        $unit2 = filter_input(INPUT_POST, 'unit2', FILTER_SANITIZE_STRING);
        $unitString2 = searchUnitNameById($unit2, $db);
    }

    if(isset($_POST['division2']) && $_POST['division2'] != null && $_POST['division2'] != ''){
        $division2 = filter_input(INPUT_POST, 'division2', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['unitD2']) && $_POST['unitD2'] != null && $_POST['unitD2'] != ''){
        $dunit2 = filter_input(INPUT_POST, 'unitD2', FILTER_SANITIZE_STRING);
        $dunitString2 = searchUnitNameById($dunit2, $db);
    }

    if($range_type == 'SINGLE'){
        $capacityName = $capacity.$unitString.' X '.$division.$dunitString;
    }
    else{
        $capacityName = $capacity.$unitString.'X'.$division.$dunitString.' ~ '.$capacity2.$unitString2.'X'.$division2.$dunitString2;
    }

    if($_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE `capacity` SET range_type=?, `name`=?, `capacity`=?, `units`=?, `division`=?, `division_unit`=?, `capacity2`=?, `units2`=?, `division2`=?, `division_unit2`=? WHERE id=?")) {
            $update_stmt->bind_param('sssssssssss', $range_type, $capacityName, $capacity, $unit, $division, $dunit, $capacity2, $unit2, $division2, $dunit2, $_POST['id']);
            
            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );
            }
            else{
                $update_stmt->close();
                $db->close();
                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }
        }
    }
    else{
        if ($insert_stmt = $db->prepare("INSERT INTO `capacity` (range_type, `name`, `capacity`, `units`, `division`, `division_unit`, `capacity2`, `units2`, `division2`, `division_unit2`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssssssss', $range_type, $capacityName, $capacity, $unit, $division, $dunit, $capacity2, $unit2, $division2, $dunit2);
            
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $insert_stmt->error
                    )
                );
            }
            else{
                $insert_stmt->close();
                $db->close();
                
                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );
            }
        }
        else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Something goes wrong when create capacity"
                )
            );
        }
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