<?php

function makePreview($zpl, $fileName, $width, $height)
{
    $width = $width*0.0393700787;
    $height = $height*0.0393700787;

    //$zpl = "^xa^cfa,50^fo100,100^fdHello World^fs^xz";

    $curl = curl_init();
// adjust print density (8dpmm), label width (4 inches), label height (6 inches), and label index (0) as necessary
    curl_setopt($curl, CURLOPT_URL, "http://api.labelary.com/v1/printers/8dpmm/labels/".$height."x".$width."/0/");
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $zpl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: application/pdf")); // omit this line to get PNG images back
    $result = curl_exec($curl);

    if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200) {
        $file = fopen("./pdfs/".$fileName.".pdf", "w"); // change file name for PNG images
        fwrite($file, $result);
        fclose($file);

    } else {
        print_r("Error: $result");
    }

    curl_close($curl);

    echo '<embed src="./pdfs/'.$fileName.'.pdf" type="application/pdf" height="100%"/><br>';
}
?>


