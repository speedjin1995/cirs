<?php
require_once "db_connect.php";

session_start();

if (isset($_POST['hypermarket'])) {
    $hypermarket = filter_input(INPUT_POST, 'hypermarket', FILTER_SANITIZE_STRING);

    // Query the zones table to check the value of the zones column
    $check_zones_stmt = $db->prepare("SELECT * FROM customers WHERE dealer=?");
    $check_zones_stmt->bind_param('s', $hypermarket);

    // Check if the zones column contains "-"
    if ($check_zones_stmt->execute()) {
        $result = $check_zones_stmt->get_result();
        $message = array();

        while ($row = $result->fetch_assoc()) {
            $message[] = array(
                'id' => $row['id'],
                'name' => $row['customer_name']
            );
        }

        echo json_encode(
            array(
                "status" => "success",
                "message" => $message
            )
        );
    } 
    else {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => "Something went wrong"
            )
        );
    }

    $check_zones_stmt->close();
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