<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt2 = $db->prepare("SELECT * FROM branches WHERE id=?")) {
        $update_stmt2->bind_param('s', $id);

        if($update_stmt2->execute()) {
            $result2 = $update_stmt2->get_result();
            $message = array();

            if($row2 = $result2->fetch_assoc()) {
                $message["branchid"] = $row2['id'];
                $message["name"] = $row2['branch_name'];
                $message["address1"] = $row2['address'];
                $message["address2"] = $row2['address2'];
                $message["address3"] = $row2['address3'];
                $message["address4"] = $row2['address4'];
            }

            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                )
            );   
        }
        $update_stmt2->close();
    }
    $db->close();

}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
        )
    ); 
}
?>