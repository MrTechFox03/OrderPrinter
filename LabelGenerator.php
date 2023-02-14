<?php
function makePDFLabel($title, $description, $amount)
{
    $pdf = new PDF('l', 'mm', [54, 25]);
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(false);
    $pdf->AddFont('Courgette-Regular', '', 'Courgette-Regular.php');


    $pdf->SetFont('Courgette-Regular', '', 12);
    $pdf->CellFit(54, 7, $title, 0, 2, 'C', false, "", false, false);
    $pdf->SetFont('Courgette-Regular', '', 6);
    $pdf->MultiCell(54, 3, $description, 0, 'C');
    $pdf->SetY(17);
    $pdf->SetX(0);
    $pdf->Cell(54, 7, $amount, 0, 2, 'C');

    return $pdf;
}

function makeZPLLabel($title, $description, $amount)
{
    return "'^XA^CF0,40^FO0,15^FB406,1,0,C,0^FD".$title."\&^FS^CF0,20^FO10,55^FB386,5,0,C,0^FD".$description."\&^FS^FO0,165^FB406,1,0,C,0^FD".$amount."\&^FS^XZ'";
    //return '^XA^CF0,40^FO0,15^FB406,1,0,C,0^FD'.$title.'\&^FS^CF0,20^FO10,55^FB386,5,0,C,0^FD'.$description.'\&^FS^FO0,165^FB406,1,0,C,0^FD'.$amount.'\&^FS^XZ';
}


function makeLabel($title, $description, $amount)
{
    // Set the image width and height
    $width = 540;
    $height = 250;

    // Create a new image
    $image = imagecreatetruecolor($width, $height);

    // Set the background color
    $backgroundColor = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $backgroundColor);

    // Set the text color
    $textColor = imagecolorallocate($image, 0, 0, 0);

    // Get the font path
    $font = 'Courgette-Regular.ttf';

    // Set the title font size
    $titleFontSize = 12;
    // Get the title text size
    $titleTextBox = imagettfbbox($titleFontSize, 0, $font, $title);
    // Get the title text width and height
    $titleTextWidth = $titleTextBox[2] - $titleTextBox[0];
    $titleTextHeight = $titleTextBox[1] - $titleTextBox[7];
    // Calculate the title text x and y position
    $titleTextX = ($width - $titleTextWidth) / 2;
    $titleTextY = 20 + $titleTextHeight;

    // Add the title text to the image
    imagettftext($image, $titleFontSize, 0, $titleTextX, $titleTextY, $textColor, $font, $title);

    // Set the description font size
    $descriptionFontSize = 6;
    // Get the description text size
    $descriptionTextBox = imagettfbbox($descriptionFontSize, 0, $font, $description);
    // Get the description text width and height
    $descriptionTextWidth = $descriptionTextBox[2] - $descriptionTextBox[0];
    $descriptionTextHeight = $descriptionTextBox[1] - $descriptionTextBox[7];
    // Calculate the description text x and y position
    $descriptionTextX = ($width - $descriptionTextWidth) / 2;
    $descriptionTextY = 60 + $descriptionTextHeight;

    // Add the description text to the image
    imagettftext($image, $descriptionFontSize, 0, $descriptionTextX, $descriptionTextY, $textColor, $font, $description);

    // Set the amount font size
    $amountFontSize = 12;
    // Get the amount text size
    $amountTextBox = imagettfbbox($amountFontSize, 0, $font, $amount);
    // Get the amount text width and height
    $amountTextWidth = $amountTextBox[2] - $amountTextBox[0];
    $amountTextHeight = $amountTextBox[1] - $amountTextBox[7];
    // Calculate the amount text x and y position
    $amountTextX = ($width - $amountTextWidth) / 2;
    $amountTextY = 100 + $amountTextHeight;

    // Add the amount text to the image
    imagettftext($image, $amountFontSize, 0, $amountTextX, $amountTextY, $textColor, $font, $amount);
    return $image;
}
