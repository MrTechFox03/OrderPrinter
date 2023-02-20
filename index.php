<!DOCTYPE html>
<html>
<body style="background: lightgrey">

<script type="text/javascript" src="lib/BrowserPrint-3.0.216.min.js"></script>
<script>
    window.onload = setup;
    var selected_device;
    var devices = [];

    //235217584

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
                alert("Error getting local devices")
            }, "printer");
        }, function (error) {
            alert(error);
        })
    }

    function sendImage(imageUrl) {
        selected_device.convertAndSendFile(imageUrl, undefined, errorCallback)
    }

    function printAllLabels(labels) {
        for (let label in labels) {
            console.log(label);
        }
    }

    let errorCallback = function (errorMessage) {
        alert("Error: " + errorMessage);
    }

    function onDeviceSelected(selected) {
        for (var i = 0; i < devices.length; ++i) {
            if (selected.value === devices[i].uid) {
                selected_device = devices[i];
                return;
            }
        }
    }

</script>
<span style="padding-right:50px; font-size:200%">Simple Label Printer</span><br/>
<span style="font-size:75%">Zebra Browser Printer is needed for this website.</span><br><br>
<br>
Selected Device:
<select id="selected_device" onchange=onDeviceSelected(this);></select>

<form action="index.php" method="post">
    <!-- todo make inputfields to adjust labels -->
    <label>
        Order ID:
        <input type="text" name="orderId" value="ORD03703">
    </label>
    <br>
    <label>
        Height:
        <input type="number" name="height" value="25"
    </label>
    <br>
    <label>
        Width:
        <input type="number" name="width" value="54"
    </label>
    <br>
    <input type="submit" name="submit" value="Retreive Labels">
</form>
<br>
<div id="preview">
</div>
</body>
</html>
<?php
require "FilterData.php";
require "API.php";
require "LabelGenerator.php";
require('fpdf.php');
require('PDF.php');
require "CreateLabel.php";
require 'LabelPreview.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $orderId = $_POST['orderId'];
    $width = $_POST['width'];
    $height = $_POST['height'];

    $labelsJson = retrieveJson($orderId);
    $newLabelsZPL = printLabels($labelsJson, $width, $height);
}

function retrieveJson($orderId)
{
    $orderJson = fetchAnOrderWithId($orderId);
    return getListOfProductsOfOrderWithId($orderJson);
}

function printLabels($labelsJson, $width, $height)
{
    ?>
    <script>
        const parent = document.getElementById("preview");
    </script>
    <?php
    $allLabels = array();
    if ($labelsJson != null) {
        foreach ($labelsJson as $labelJson) {
            $title = array(
                'text' => $labelJson["title"],
                'align' => 'L',
                'fontType' => 'Arial Bold',
                'fontColor' => 'Black',
                'fontSize' => 40,
                'x' => 5,
                'y' => 50
            );
            $description = array(
                'text' => $labelJson["description"],
                'align' => 'L',
                'fontType' => 'Arial',
                'fontColor' => 'Black',
                'fontSize' => 20,
                'x' => 5,
                'y' => 150
            );
            $variant = array(
                'text' => $labelJson["amount"],
                'align' => 'C',
                'fontType' => 'Arial',
                'fontColor' => 'Black',
                'fontSize' => 20,
                'x' => 0,
                'y' => 400
            );
            $label = json_encode(array($title, $description, $variant));
            $image = array(
                'src' => 'logo-dark.png',
                'x' => 50,
                'y' => 50,
                'width' => 50,
                'height' => 50
            );
            ?>
            <script type="text/javascript" src="LabelPreviewer.js"></script>
            <script>
                parent.appendChild(document.createElement("p").appendChild(document.createTextNode('<?php echo $labelJson["title"] ?>')));
                parent.appendChild(document.createElement("br"));
                base_image = new Image();
                base_image.src = '<?php echo $image["src"] ?>';
                base_image.onload = function () {
                    let labelImg = createLabel(
                        <?php echo $height ?>,
                        <?php echo $width ?>,
                        <?php echo $label ?>,
                        base_image,
                        <?php echo $image["x"] ?>,
                        <?php echo $image["y"] ?>,
                        <?php echo $image["width"] ?>,
                        <?php echo $image["height"] ?>
                    );
                    parent.appendChild(labelImg);

                    const sendJpgButtonSrc = document.createElement("input");
                    sendJpgButtonSrc.type = "button";
                    sendJpgButtonSrc.value = "Print label";
                    sendJpgButtonSrc.onclick = function () {
                        sendImage(labelImg.src);
                    };

                    // add the buttons to the HTML document
                    parent.appendChild(document.createElement("br"));
                    parent.appendChild(sendJpgButtonSrc);
                    parent.appendChild(document.createElement("br"));
                    parent.appendChild(document.createElement("br"));

                }

            </script>
            <?php

        }
        //todo print all labels at ones
        return $allLabels;
    }
}

?>