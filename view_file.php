<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "login.php";</script>';
}
else{
    if (isset($_GET['file']) && !empty($_GET['file'])) {
        // Sanitize and retrieve the file ID from the query parameter
        $fileId = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_STRING);
    
        // Prepare and execute a query to check if the file exists in the database
        $stmt = $db->prepare("SELECT filepath FROM files WHERE id = ?");
        $stmt->bind_param('s', $fileId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($row = $result->fetch_assoc()) {
            // File exists in the database
            $baseUploadDir = realpath(dirname(__DIR__, 1));
            $filePath = $row['filepath'];
    
            // Construct the full file path
            $filePath = $baseUploadDir . '/' . $filePath;
    
            if (file_exists($filePath)) {
                // Output the file content with appropriate headers
                header('Content-Type: ' . mime_content_type($filePath));
                header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
                readfile($filePath); // Read and output the file content
                exit;
            } 
            else {
                // File does not exist on the server
                echo 'File not found!!.';
            }
        } 
        else {
            // File ID not found in the database
            echo 'File not found!!.';
        }
    } else {
        // No file ID provided in the request
        echo 'Invalid file request.';
    }
    
}
?>
