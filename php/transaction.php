<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.php";</script>';
}

if(isset($_POST['transactions'])){
    $transactionsArray = json_decode($_POST['transactions'], true);
    $success = true;
    $message = array();

    for($i=0; $i<count($transactionsArray); $i++){
        if ($select_stmt = $db->prepare("SELECT COUNT(*) FROM model WHERE model = ?")) {
            $select_stmt->bind_param('s', $transactionsArray[$i]['brand']);
            
            // Execute the prepared query.
            if (! $select_stmt->execute()) {
                $success = false;
            }
            else{
                $result = $select_stmt->get_result();

                if($row = $result->fetch_assoc()){
                    if($row['COUNT(*)'] == '0'){
                        if ($insert_stmt = $db->prepare("INSERT INTO model (model) VALUES (?)")) {
                            $insert_stmt->bind_param('s', $transactionsArray[$i]['brand']);
                            
                            // Execute the prepared query.
                            if (! $insert_stmt->execute()) {
                                $success = false;
                            }
                        }
                    }
                }
            }
        }
    }

    if($success){
        echo json_encode(
            array(
                "status"=> "success", 
                "message"=> "Added Successfully!!" 
            )
        );
    }
    else{
        echo json_encode(
            array(
                "status"=> "failed", 
                "message"=> "failed to insert transactions"
            )
        );
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