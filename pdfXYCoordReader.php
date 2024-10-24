<?php
require 'vendor/autoload.php'; // Load Composer's autoloader

use Smalot\PdfParser\Parser as PdfParser;

// Conversion factor from points to millimeters
const POINTS_TO_MM = 0.35278;

// Path to your PDF file
$fillFile = 'php/forms/DMSB_ATS.pdf'; // Change this to your actual PDF file path

// Create a new instance of the PDF Parser
$pdfParser = new PdfParser();
$pdf = $pdfParser->parseFile($fillFile);

// Get the pages from the PDF
$pages = $pdf->getPages();

echo("<h2>Words with Coordinates (in mm):</h2><pre>");

foreach ($pages as $pageIndex => $page) {

    echo("<h3>Page: ". ($pageIndex + 1) ."</h3><pre>");
    // Get the data with coordinates
    $dataTm = $page->getDataTm();

    // PDF page height (usually in points) for A4 page (portrait mode)
    $pageHeightInPoints = 842; // For A4 page at 72 DPI

    foreach ($dataTm as $wordData) {
        // Each $wordData contains the coordinates and the word
        if (isset($wordData[0]) && isset($wordData[1])) {
            // Extracting the coordinates
            $coordinates = $wordData[0];
            $text = $wordData[1];

            // The coordinates are in the format of [x1, y1, x2, y2, width, height]
            $x = $coordinates[4]; // X coordinate in points
            $y = $coordinates[5]; // Y coordinate in points

            // Adjust Y to match the PDF coordinate system (origin at bottom-left)
            $y = $pageHeightInPoints - $y;

            // Convert X and Y coordinates from points to millimeters
            $x_mm = $x * POINTS_TO_MM;
            $y_mm = $y * POINTS_TO_MM;

            // Display the word along with its converted coordinates
            echo "Word: " . htmlspecialchars($text) . " | X: " . number_format($x_mm, 3) . " mm | Y: " . number_format($y_mm, 3) . " mm\n";
        }
    }
    echo("</pre>");
}
?>
