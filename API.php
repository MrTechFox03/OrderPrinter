<?php
function fetchAnOrderWithId($id){
    $url = 'https://api.webshopapp.com/nl/orders/' . $id . '.json';
    return getRequest($url);
}

function fetchOrdersWithPageNumber($pageNumber){
    if ($pageNumber == null)
        $pageNumber = 1;
    $url = 'https://api.webshopapp.com/nl/orders.json?page=' . $pageNumber . '.json';
    return getRequest($url);
}


function getRequest($url)
{
    $apiKey = "79fa822521904854874faa60d523540e";
    $apiSecret = "f032e15faf8e85d4b848e652017630ed";

    $headers = [
        'Authorization: Basic ' . base64_encode($apiKey . ':' . $apiSecret),
        'Content-Type: application/json'
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 'GET');

    $response = curl_exec($ch);

    curl_close($ch);
    return $response;
}
