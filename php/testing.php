<?php
require_once('../vendor/autoload.php'); // Include the autoload file for Composer

use setasign\Fpdi\Fpdi;

// Load the PDF file
$pdf = new Fpdi();

// Set the source file (the fillable PDF)
$pdf->setSourceFile('forms/ATK_FORM.pdf');

// Get the total number of pages in the PDF
$pageCount = $pdf->setSourceFile('forms/ATK_FORM.pdf');

// Define the data to fill in
$data = [
    'name' => 'John Doe',
    'address' => '123 Main St',
    'city' => 'Anytown',
    'state' => 'CA',
    'zip' => '12345',
];

// Loop through each page of the PDF
for ($i = 1; $i <= $pageCount; $i++) {
    // Import the page
    $tplIdx = $pdf->importPage($i);
    $pdf->AddPage();
    $pdf->useTemplate($tplIdx, 0, 0, 210); // Adjust the page size if necessary

    // Only fill in the fields for the first page
    if ($i === 1) {
        // Set font for filling data
        $pdf->SetFont('Arial', '', 12);
        
        // Fill the fields at specific coordinates on the first page
        $pdf->SetXY(50, 50); // Set position for name
        $pdf->Write(0, $data['name']);

        $pdf->SetXY(50, 60); // Set position for address
        $pdf->Write(0, $data['address']);

        $pdf->SetXY(50, 70); // Set position for city
        $pdf->Write(0, $data['city']);

        $pdf->SetXY(50, 80); // Set position for state
        $pdf->Write(0, $data['state']);

        $pdf->SetXY(50, 90); // Set position for zip
        $pdf->Write(0, $data['zip']);
    }
}

// Output the filled PDF to the browser for download
$pdf->Output('D', 'filled_form.pdf');
?>
