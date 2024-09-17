<?php
require_once "db_connect.php";
require_once 'requires/lookup.php';

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['capacity'], $_POST['unit'])){
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
    $unit = filter_input(INPUT_POST, 'unit', FILTER_SANITIZE_STRING);
    $unitString = searchUnitNameById($unit, $db);

    $division = null;
    $dunit = null;

    if(isset($_POST['division']) && $_POST['division'] != null && $_POST['division'] != ''){
        $division = filter_input(INPUT_POST, 'division', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['unitD']) && $_POST['unitD'] != null && $_POST['unitD'] != ''){
        $dunit = filter_input(INPUT_POST, 'unitD', FILTER_SANITIZE_STRING);
        $dunitString = searchUnitNameById($dunit, $db);
    }

    $capacityName = $capacity.$unitString.' X '.$division.$dunitString;

    if($_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE `capacity` SET `name`=?, `capacity`=?, `units`=?, `division`=?, `division_unit`=? WHERE id=?")) {
            $update_stmt->bind_param('ssssss', $capacityName, $capacity, $unit, $division, $dunit, $_POST['id']);
            
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
        if ($insert_stmt = $db->prepare("INSERT INTO `capacity` (`name`, `capacity`, `units`, `division`, `division_unit`) VALUES (?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssss', $capacityName, $capacity, $unit, $division, $dunit);
            
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