<?php
require "../FilterData.php";
require "../API.php";
require "../LabelGenerator.php";
require '../LabelPreview.php';

?>

<!DOCTYPE html>
<html lang="nl">
<body style="background-color: #b3b3b3">
<script type="text/javascript" src="../lib/BrowserPrint-3.0.216.min.js"></script>
<script type="text/javascript" src="../LabelPreviewer.js"></script>
<script>
    let base_image = new Image();
    base_image.src = '../img/logo-dark.png';
    let product;
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
//Get the default device from the application as a first step. Discovery takes longer to complete.
        BrowserPrint.getDefaultDevice("printer", function (device) {

//Add device to list of devices and to html select element
            selected_device = device;
            devices.push(device);
            var html_select = document.getElementById("selected_device");
            var option = document.createElement("option");
            option.text = device.name;
            html_select.add(option);

//Discover any other devices available to the application
            BrowserPrint.getLocalDevices(function (device_list) {
                for (var i = 0; i < device_list.length; i++) {
//Add device to list of devices and to html select element
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

    /**
     * For sendAllImages the error is printed many times. Therefore a boolean to print the alert one time.
     */
    function sendAllImages(imageUrls) {
        let succes = true;
        for (let imageUrl in imageUrls) {
            succes = selected_device.convertAndSendFile(imageUrl, undefined, errorCallbackForSendMultipleLabelToPrinter)
        }
        if (!succes)
            alert("Er is iets mis gegaan.\nKijk of de printer gevonden is.\nAnders kijk of de labels goed gegenereerd zijn");
    }

    function sendImage(imageUrl) {
        selected_device.convertAndSendFile(imageUrl, undefined, errorCallbackForSendOneLabelToPrinter)
    }

    let errorCallbackForSendOneLabelToPrinter = function (errorMessage) {
        console.log("Error: " + errorMessage);
        alert("Er is iets mis gegaan.\nKijk of de printer gevonden is.\nAnders kijk of de labels goed gegenereerd zijn");
    }

    let errorCallbackForSendMultipleLabelToPrinter = function (errorMessage) {
        console.log("Error: " + errorMessage);
        return false;
    }

    function printLabels(button) {
        const formData = new FormData();
        formData.append('productsLink', product);
        fetch('../API.php', {
            method: 'POST',
            body: formData
        }).then(response => response.json())
            .then(data => {
                let products = data["orderProducts"];

                for (let i in products) {
                    const formData = new FormData();
                    formData.append('productsLink', products[i]["product"]["resource"]["link"]);
                    fetch('../API.php', {
                        method: 'POST',
                        body: formData
                    }).then(response => response.json())
                        .then(data => {
                                let label;

                                const title = [];
                                title['text'] = products[i]["productTitle"];
                                title['align'] = 'L';
                                title['fontType'] = 'Times New Roman';
                                title['fontSize'] = 30;
                                title['fontColor'] = "black";
                                title['x'] = 15;
                                title['y'] = 50;
                                title['fontExtra'] = "bold";


                                const variant = [];
                                variant['text'] = products[i]["variantTitle"];
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

                                let content = data["product"]["content"];
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
                                    //ORD03961 proberen
                                    for (let j = 0; j < products[i]["quantityOrdered"]; j++) {
                                        sendImage(label.src);
                                    }
                                }
                                if (button === "download") {
                                    downloadImage(label.src, products[i]["productTitle"]);
                                }
                            }
                        ).catch(error => {
                        console.error(error);
                    });
                }
            }).catch(error => {
            console.error(error);
        });
    }
</script>

<button style="float: left; position: fixed;" ><a style="text-decoration: none; color: black;" href="../Product">Product zoeken</a></button>
<form>
    <label for="selected_device">Geselecteerde printer:</label>
    <select id="selected_device" onchange=onDeviceSelected(this);></select>
</form>
<form>
    <span class="available" id="foundMessage">🔴</span>
    <input type="text" id="orderId" name="orderId" value="ORD0" oninput="">
    <script>
        var input = document.getElementById("orderId");

        input.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                if (this.value.length === 8) {
                    printLabels("print");
                }
            }
        });

        elem = document.getElementById("orderId")
        elem.addEventListener('input', function () {
            if (this.value.length === 8) {
                const formData = new FormData();
                formData.append('orderId', elem.value);
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

    input[type=text] {
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

    input[type=button]:hover:disabled, {
        background-color: lightgrey;
    }
    input[type=button]:disabled {
        opacity: 0.5;
        cursor: default;
    }
</style>

