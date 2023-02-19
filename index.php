<!DOCTYPE html>
<html>
<body>

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

    function writeToSelectedPrinter(dataToWrite) {
        selected_device.send(dataToWrite, undefined, errorCallback);
    }

    function printAllLabels(labels){
        console.log("hier")
        for (let label in labels){
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
        <input type="text" name="orderId" value="ORD00146">
    </label>
    <br>
    <label>
        Height:
        <input type="number" name="height" value="51"
    </label>
    <br>
    <label>
        Width:
        <input type="number" name="width" value="57"
    </label>
    <br>
    <input type="submit" name="submit" value="Retreive Labels">
</form>
<br>
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

function printLabels($labelsJson, $width ,$height)
{
    $allLabels = array();
    if ($labelsJson != null){
    foreach ($labelsJson as $labelJson) {
        //todo implement variables instead of static
        $label = createLabel($labelJson);
        ?>
        <?php echo $labelJson["title"]; ?><br><input type="button" value="Print Label"  onclick="writeToSelectedPrinter('<?php echo $label; ?>')"> <br><?php
        makePreview($label, $labelJson["title"], $width ,$height);
        $allLabels[] = $label;
    }
    //todo print all labels at ones
    return $allLabels;
    }
}
?>