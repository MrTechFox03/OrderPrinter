<!DOCTYPE html>
<html>
<body>
<?php

$products =array(
    array("naam" => "Winter Sweets", "omschrijving" => "Een zoete, maar ook kruidige witte thee. Bijzonder lekkere combinatie van groene thee, witte thee, en oolong met aardbei, kokossnippers en kardemom. Geniet van deze heerlijke smaken combinatie in de vorm van een losse thee!"),
    array("naam" => "Kir Royal", "omschrijving" => "Lemongrass en cassis geven deze witte thee een frisse en tegelijkertijd fruitige smaak! Een lekker zachte thee die niet snel nabittert. Een heerlijke losse thee om even te relaxen."),

);
$label='';
foreach ($products as $product) {
    $naam=$product["naam"];
$label.='^XA'; //nieuw label
$label.='^CF0,40'; //font 0 and the height of the characters to 40 dots.
$label.='^FO20,35'; //X position is set to 5 dots, and the Y position is set to 15 dots.
$label.='^FB406,1,0,C,0'; // field (406 dots), the number of lines (1), justification (0 for left-justified), orientation (C for centered), and the rotation angle (0 degrees).
$label.='^FD'; //data
$label.=$product["naam"];
$label.='^FS'; //dataend
$label.='^CF0,20'; //font 0 and the height of the characters to 20 dots.
$label.='^FO20,75'; ////X position is set to 15 dots, and the Y position is set to 55 dots.
$label.='^FB386,6,0,C,0';
$label.='^FD';
$label.=$product["omschrijving"];
$label.='^FS';
$label.='^FO20,215';
$label.='^FB416,1,0,C,0';
$label.='^FD';
$label.='100 gram';
$label.='^FS'; //dataend
$label.='^XZ'; //label end
?> <input type="submit" value="Label" onclick="writeToSelectedPrinter('<?php echo $label; ?>')"> <?php echo $naam;?><br><br>

<?php
}
?>


<br>
<script type="text/javascript" src="./lib/BrowserPrint-3.0.216.min.js"></script>
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
    Order ID:
    <input type="number" name="orderId" value="235217584"><br><br>
    <input type="submit" value="Send File">
</form>
<input type="submit" value="Print" onclick="writeToSelectedPrinter()">
<input type="submit" value="Test" onclick="writeToSelectedPrinter()">
<br/><br/>
</body>
</html>