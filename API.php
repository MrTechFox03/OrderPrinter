<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['orderId'])) {
        $orderId = $_POST['orderId'];
        $orders = json_decode(fetchAnOrderWithId($orderId));
        if (empty($orders->orders[0])) {
            echo json_encode("not found");
        } else {
            $productsLink = $orders->orders[0]->products->resource->link;
            echo json_encode($productsLink);
        }
    }
    if (isset($_POST['productsLink'])) {
        $json = getRequest($_POST['productsLink']);
        echo $json;
    }
    if (isset($_POST['productId'])){
        echo fetchProductWithId($_POST['productId']);
    }
    if (isset($_POST['productsPage'])){
        echo fetchProductsWithPageNumber($_POST['productsPage']);
    }
}

function fetchAnOrderWithId($id)
{
    $url = 'https://api.webshopapp.com/nl/orders.json?number=' . $id;
    return getRequest($url);
}

function fetchOrdersWithPageNumber($pageNumber)
{
    if ($pageNumber == null)
        $pageNumber = 1;
    $url = 'https://api.webshopapp.com/nl/orders.json?page=' . $pageNumber . '.json';
    return getRequest($url);
}

function fetchProductsWithPageNumber($pageNumber)
{
    $url = 'https://api.webshopapp.com/nl/products.json?limit=250&page=' . $pageNumber;
    return getRequest($url);
}

function fetchProductWithId($id){
    $url = 'https://api.webshopapp.com/nl/products/' . $id . '.json';
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
