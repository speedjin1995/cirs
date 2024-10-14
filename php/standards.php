<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['satemperature'], $_POST['capacity'], $_POST['unitsHidden'], $_POST['variance'], $_POST['tester1'], 
$_POST['tester2'], $_POST['tester3'], $_POST['tester4'], $_POST['tester5'], $_POST['tester6'], $_POST['tester7'], 
$_POST['tester8'], $_POST['tester9'], $_POST['tester10'])){
    $satemperature = filter_input(INPUT_POST, 'satemperature', FILTER_SANITIZE_STRING);
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
    $units = filter_input(INPUT_POST, 'unitsHidden', FILTER_SANITIZE_STRING);
    $variance = filter_input(INPUT_POST, 'variance', FILTER_SANITIZE_STRING);

    $relHumidity = null;

    if(isset($_POST['relHumidity']) && $_POST['relHumidity'] != null && $_POST['relHumidity'] != ''){
        $relHumidity = filter_input(INPUT_POST, 'relHumidity', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['tester1']) && $_POST['tester1'] != null && $_POST['tester1'] != ''){
        $tester1 = filter_input(INPUT_POST, 'tester1', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['tester2']) && $_POST['tester2'] != null && $_POST['tester2'] != ''){
        $tester2 = filter_input(INPUT_POST, 'tester2', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['tester3']) && $_POST['tester3'] != null && $_POST['tester3'] != ''){
        $tester3 = filter_input(INPUT_POST, 'tester3', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['tester4']) && $_POST['tester4'] != null && $_POST['tester4'] != ''){
        $tester4 = filter_input(INPUT_POST, 'tester4', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['tester5']) && $_POST['tester5'] != null && $_POST['tester5'] != ''){
        $tester5 = filter_input(INPUT_POST, 'tester5', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['tester6']) && $_POST['tester6'] != null && $_POST['tester6'] != ''){
        $tester6 = filter_input(INPUT_POST, 'tester6', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['tester7']) && $_POST['tester7'] != null && $_POST['tester7'] != ''){
        $tester7 = filter_input(INPUT_POST, 'tester7', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['tester8']) && $_POST['tester8'] != null && $_POST['tester8'] != ''){
        $tester8 = filter_input(INPUT_POST, 'tester8', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['tester9']) && $_POST['tester9'] != null && $_POST['tester9'] != ''){
        $tester9 = filter_input(INPUT_POST, 'tester9', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['tester10']) && $_POST['tester10'] != null && $_POST['tester10'] != ''){
        $tester10 = filter_input(INPUT_POST, 'tester10', FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE standard SET standard_avg_temp=?, relative_humidity=?, capacity=?, unit=?, variance=?, test_1=?, test_2=?, test_3=?, test_4=?, test_5=?, test_6=?, test_7=?, test_8=?, test_9=?, test_10=? WHERE id=?")) {
            $update_stmt->bind_param('ssssssssssssssss', $satemperature, $relHumidity, $capacity, $units, $variance, $tester1, $tester2, $tester3, $tester4, $tester5, $tester6, $tester7, $tester8, $tester9, $tester10, $_POST['id']);
            
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
        if ($insert_stmt = $db->prepare("INSERT INTO standard (standard_avg_temp, relative_humidity, capacity, unit, variance, test_1, test_2, test_3, test_4, test_5, test_6, test_7, test_8, test_9, test_10) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssssssssss', $satemperature, $relHumidity, $capacity, $units, $variance, $tester1, $tester2, $tester3, $tester4, $tester5, $tester6, $tester7, $tester8, $tester9, $tester10);
            
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