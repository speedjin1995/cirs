<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.php";</script>';
}

if(isset($_POST['size']) && isset($_POST['jenisAlat'])){
    $size = filter_input(INPUT_POST, 'size', FILTER_SANITIZE_STRING);
    $jenisAlat = null;

    if(isset($_POST['jenisAlat']) && $_POST['jenisAlat']!=null && $_POST['jenisAlat']!=""){
        $jenisAlat = json_encode($_POST['jenisAlat']);
    }

    if($_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE size SET size=?, alat=? WHERE id=?")) {
            $update_stmt->bind_param('sss', $size, $jenisAlat, $_POST['id']);
            
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
        if ($insert_stmt = $db->prepare("INSERT INTO size (size, alat) VALUES (?, ?)")) {
            $insert_stmt->bind_param('ss', $size, $jenisAlat);
            
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