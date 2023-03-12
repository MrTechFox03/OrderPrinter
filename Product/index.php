<?php
require "../FilterData.php";
require "../API.php";
require "../LabelGenerator.php";
require '../LabelPreview.php';

$labelsJson = "";
?>

<!DOCTYPE html>
<html lang="nl">
<body style="background-color: #b3b3b3">
<script type="text/javascript" src="../lib/BrowserPrint-3.0.216.min.js"></script>
<script type="text/javascript" src="../LabelPreviewer.js"></script>
<script>
    let titleId;
    let base_image = new Image();
    base_image.src = '../img/logo-dark.png';
    window.onload = setup;
    var selected_device;
    var devices = [];
    let order;

    function downloadImage(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function setup() {
        BrowserPrint.getDefaultDevice("printer", function (device) {
            selected_device = device;
            devices.push(device);
            var html_select = document.getElementById("selected_device");
            var option = document.createElement("option");
            option.text = device.name;
            html_select.add(option);
            BrowserPrint.getLocalDevices(function (device_list) {
                for (var i = 0; i < device_list.length; i++) {
                    var device = device_list[i];
                    if (!selected_device || device.uid !== selected_device.uid) {
                        devices.push(device);
                        var option = document.createElement("option");
                        option.text = device.name;
                        option.value = device.uid;
                        html_select.add(option);
                    }
                }
            }, function () {
                alert("Printer (nog) niet gevonden")
            }, "printer");
        }, function (error) {
            console.log(error);
            alert("Printer (nog) niet gevonden")
        })
    }

    function onDeviceSelected(selected) {
        for (let i = 0; i < devices.length; ++i) {
            if (selected.value === devices[i].uid) {
                selected_device = devices[i];
                return;
            }
        }
    }

    function sendImage(imageUrl) {
        selected_device.convertAndSendFile(imageUrl, undefined, errorCallbackForSendOneLabelToPrinter)
    }

    let errorCallbackForSendOneLabelToPrinter = function (errorMessage) {
        console.log("Error: " + errorMessage);
        alert("Er is iets mis gegaan.\nKijk of de printer gevonden is.\nAnders kijk of de labels goed gegenereerd zijn");
    }

    async function getAllTitlesAndIds() {
        let i = 1;
        titleId = new Map();

        let products = await fetchProductsWithPageNumber(i);
        while (products.length !== 0){
            i++;
            for (let j in products){
                titleId.set(products[j]['title'], products[j]['id'])
            }
            products = await fetchProductsWithPageNumber(i);
        }

        let datalist = document.getElementById("titles")
        for (let [key, value] of titleId) {
            const option = document.createElement('option');
            option.value = key;
            datalist.appendChild(option);
        }

        document.getElementById("titleInput").disabled = false;
    }

    function fetchProductsWithPageNumber(pageNumber) {
        const formData = new FormData();
        formData.append('productsPage', pageNumber);
        return fetch('../API.php', {
            method: 'POST',
            body: formData
        }).then(response => response.json())
            .then(data =>
                data["products"]
            ).catch(error => {
                console.error(error);
            });
    }

    function printLabels(button) {
        let label;

        //todo alleen wanner value verandert
        //todo website. breedte hooghte omruilen
        const title = [];
        title['text'] = product['product']['title'];
        title['align'] = 'L';
        title['fontType'] = 'Times New Roman';
        title['fontSize'] = 30;
        title['fontColor'] = "black";
        title['x'] = 15;
        title['y'] = 50;
        title['fontExtra'] = "bold";


        const variant = [];
        variant['text'] = document.getElementById("variant").value;
        variant['align'] = 'L';
        variant['fontType'] = 'Times New Roman';
        variant['fontSize'] = 25;
        variant['fontColor'] = "black";
        variant['x'] = 15;
        variant['y'] = 300;
        variant['fontExtra'] = "";

        const website = [];
        website['text'] = "www.losse - t.nl";
        website['align'] = 'C';
        website['fontType'] = 'arial';
        website['fontSize'] = 23;
        website['fontColor'] = "black";
        website['x'] = 0;
        website['y'] = 440;
        website['fontExtra'] = 'bold';

        let content = product["product"]["content"];
        let pattern = /Ingrediënten:\s*(.*)/i;

        if (content.match(pattern) !== null) {
            let matches = content.match(pattern);
            let ingredientsString = matches[1];
            let strippedString = ingredientsString.replace(/(<([^>]+)>)/gi, "");
            const ingredients = [];
            ingredients['text'] = strippedString;
            ingredients['align'] = 'L';
            ingredients['fontType'] = 'Times New Roman';
            ingredients['fontSize'] = 20;
            ingredients['fontColor'] = "black";
            ingredients['x'] = 15;
            ingredients['y'] = 150;
            ingredients['fontExtra'] = "";

            const ingredientenDisplay = [];
            ingredientenDisplay['text'] = "Ingredienten:";
            ingredientenDisplay['align'] = 'L';
            ingredientenDisplay['fontType'] = 'Times New Roman';
            ingredientenDisplay['fontSize'] = 25;
            ingredientenDisplay['fontColor'] = "black";
            ingredientenDisplay['x'] = 15;
            ingredientenDisplay['y'] = 120;
            ingredientenDisplay['fontExtra'] = "";

            label = createLabel(
                51,
                57,
                [
                    title,
                    ingredientenDisplay,
                    ingredients,
                    variant,
                    website
                ],
                base_image,
                55,
                350,
                300,
                75
            );
        } else {
            label = createLabel(
                51,
                57,
                [
                    title,
                    variant,
                    website
                ],
                base_image,
                55,
                350,
                300,
                75
            );
        }

        if (button === "preview") {
            let l = document.body.appendChild(label);
            l.style.margin = "5px";
            l.classList.add("labelImg");
            l.onclick = function () {
                sendImage(label.src);
            }
        }
        if (button === "print") {
            sendImage(label.src);
        }
        if (button === "download") {
            downloadImage(label.src, product['product']["title"]);
        }
    }

    function loadProductId(){
        if (titleId === undefined)
            document.getElementById("titleInput").disabled = true;

        let titleInput = document.getElementById("titleInput");
        let productId = document.getElementById("productId");
        productId.value = titleId.get(titleInput.value);
        let foundMessage = document.getElementById("foundMessage");
        if (productId.value === "undefined"){
            foundMessage.innerText = "🔴";
            document.getElementById("print").disabled = true;
            document.getElementById("download").disabled = true;
            document.getElementById("preview").disabled = true;
            productId.value = "";
        } else {
            foundMessage.innerText = "🟢";
            document.getElementById("print").disabled = false;
            document.getElementById("download").disabled = false;
            document.getElementById("preview").disabled = false;
        }
    }

    function loadProduct(e){
        if (e.value.length === 9) {
            const formData = new FormData();
            formData.append('productId', elem.value);
            fetch('../API.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
                .then(data => {
                    let foundMessage = document.getElementById("foundMessage");
                    if (data === "not found") {
                        foundMessage.innerText = "🔴";
                        document.getElementById("print").disabled = true;
                        document.getElementById("download").disabled = true;
                        document.getElementById("preview").disabled = true;
                    } else {
                        foundMessage.innerText = "🟢";
                        product = data;
                        document.getElementById("print").disabled = false;
                        document.getElementById("download").disabled = false;
                        document.getElementById("preview").disabled = false;
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        } else {
            let foundMessage = document.getElementById("foundMessage");
            foundMessage.innerText = "🔴";
            document.getElementById("print").disabled = true;
            document.getElementById("download").disabled = true;
            document.getElementById("preview").disabled = true;
        }
    }
</script>

<form>
    <label for="selected_device">Geselecteerde printer:</label>
    <select id="selected_device" onchange=onDeviceSelected(this);></select>
</form>
<form>
    <button style="float: left; margin:0;" ><a style="text-decoration: none; color: black;" href="../Simple">Order zoeken</a></button>
    <span class="available" id="foundMessage">🔴</span>
    <input onclick="getAllTitlesAndIds()" class="input" type="button" name="refresh" value="Voorgestelden ophalen" id="refresh">
    <input class="input" list="titles" onchange="loadProductId()" id="titleInput" placeholder="Product naam..." disabled>
    <datalist id="titles">
    </datalist>
    <input type="text" id="productId" name="productId" placeholder="Product id...">
    <input type="text" id="variant" name="variant" placeholder="Extra veld...">
    <script>
        // Get the input field
        var input = document.getElementById("productId");

        // Execute a function when the user presses a key on the keyboard
        input.addEventListener("keypress", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                if (this.value.length === 8) {
                    printLabels("print");
                }
            }
        });

        elem = document.getElementById("productId")
        elem.addEventListener('input', function () {
            loadProduct(this);
        })
    </script>
    <input onclick="printLabels('print')" class="input" type="button" name="print" value="🖶" disabled id="print">
    <input onclick="printLabels('preview')" class="input" type="button" name="preview" value="Preview" disabled
           id="preview">
    <input onclick="printLabels('download')" class="input" type="button" name="download" value="💾" disabled
           id="download">
</form>
<div id="preview">
</div>
</body>
</html>
<style>
    body {
        font-family: Arial, serif;
        font-size: 25px;
    }

    span {
        font-size: 35px;
    }

    .labelImg {
        box-shadow: 0 0 3px black;
        transition: all .4s ease;
        transform: scale(1) perspective(0)
    }

    .labelImg:hover {
        transition: all .4s ease;
        box-shadow: 0 0 20px black;
        cursor: pointer;
        transform: scale(1.05) perspective(1px)
    }

    form {
        text-align: center;
    }

    input[type=text], input {
        font-size: 25px;
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid black;
        border-radius: 20px;
        box-sizing: border-box;
    }


    input[type=button], button {
        font-size: 25px;
        border: 2px solid black;
        padding: 9px 20px;
        margin: 8px 0;
        color: black;
        border-radius: 20px;
        cursor: pointer;
        background-color: lightgrey;
    }

    button:hover{
        background-color: grey;
    }

    input[type=button]:hover {
        background-color: grey;
    }

    input[type=button]:hover:disabled {
        background-color: lightgrey;
    }

    input[type=button]:disabled {
        opacity: 0.5;
        cursor: default;
    }
</style>

