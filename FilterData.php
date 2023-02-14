<?php
function getListOfProductsOfOrderWithId($jsonOrderEncoded) {
    $jsonOrderDecoded = json_decode($jsonOrderEncoded, true);
    $order = $jsonOrderDecoded["order"];
    $customerName = $order["firstname"] . " " . $order["middlename"] . " " . $order["lastname"];
    $urlOfProducts = $order["products"]["resource"]["link"];

    $productsInOrderJson = json_decode(getRequest($urlOfProducts), true);
    $productsInOrder = $productsInOrderJson["orderProducts"];
    $listOfProductsInformation = array();

    foreach ($productsInOrder as $productOrder){
        $linkToProduct = $productOrder["product"]["resource"]["link"];
        $product = json_decode(getRequest($linkToProduct))->product;

        $product->description = str_replace(array("\r", "\n"), '', $product->description);

        $filteredObject["orderId"] = $order["id"];
        $filteredObject["customer"] = $customerName;
        $filteredObject["title"] = $product -> fulltitle;
        $filteredObject["description"] =  $product->description;
        $filteredObject["amount"] = $productOrder["variantTitle"];
        $listOfProductsInformation[] = $filteredObject;
    }
    return $listOfProductsInformation;
}