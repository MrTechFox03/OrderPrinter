<?php
function getListOfProductsOfOrderWithId($jsonOrderEncoded) {
    $jsonOrderDecoded = json_decode($jsonOrderEncoded, true);

    $orderTest = $jsonOrderDecoded["orders"];
    if ($orderTest == null) {
        var_dump("Geen order gevonden!");
        return;
    }
    $order = $orderTest[0];
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
        $filteredObject["variant"] = $productOrder["variantTitle"];
        //todo bespreken welke quantity het moet worden:
        //"quantityOrdered": 1
        //"quantityInvoiced": 1
        //"quantityShipped": 1
        $filteredObject["amount"] = $productOrder["quantityInvoiced"];
        $listOfProductsInformation[] = $filteredObject;
    }
    return $listOfProductsInformation;
}