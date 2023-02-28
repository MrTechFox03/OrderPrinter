<!DOCTYPE html>
<html lang="nl">
<body>

<script type="text/javascript" src="lib/BrowserPrint-3.0.216.min.js"></script>
<script>
    window.onload = setup;
    var selected_device;
    var devices = [];

    function removeAllChildNodes(parent) {
        while (parent.firstChild) {
            parent.removeChild(parent.firstChild);
        }
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

    function sendImage(imageUrl) {
        selected_device.convertAndSendFile(imageUrl, undefined, errorCallbackForSendOneLabelToPrinter)
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

    let errorCallbackForSendOneLabelToPrinter = function (errorMessage) {
        console.log("Error: " + errorMessage);
        alert("Er is iets mis gegaan.\nKijk of de printer gevonden is.\nAnders kijk of de labels goed gegenereerd zijn");
    }

    let errorCallbackForSendMultipleLabelToPrinter = function (errorMessage) {
        console.log("Error: " + errorMessage);
        return false;
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
<span style="font-size:75%">Selecteer de juiste printer. Voer hierna het gewenste ordernummer in.
    Hierna klik op 'OK' om een overzicht te krijgen van de labels. Het overzicht wat naar voren komt heeft verschillende functies.
    'Print alle labels' knop print alle labels uit op jouw printer. Door op een label voorbeeld te drukken, kun je een enkele label afdrukken.
    Niet tevreden met de text op de label? Verander de text in het tabel en druk op de verversknop naast de 'Print alle labels' knop.</span><br>
<label for="selected_device">Geselecteerde printer:</label>
<select id="selected_device" onchange=onDeviceSelected(this);></select><br>


<form action="index.php" method="post">
    <label>Geselecteerde instellingen:
        <select name="settings">
            <?php
            include "lib/dir.php";
            $files = getListOfFiles();
            foreach ($files as $file) {
                ?>
                <option><?php echo $file ?></option>
                <?php
            }
            ?>
        </select>
        <button style="padding: 1px 10px;" id="goToSettings">⚙</button>
        <script>
            goToSettings = document.getElementById("goToSettings");
            goToSettings.addEventListener("click", function (event) {
                event.preventDefault();
                window.location.href = 'settings/index.php';
            });
        </script>
    </label><br>
    <label>
        Ordernummer:
        <input type="text" name="orderId" value="ORD03834">
        <input class="button" type="submit" name="submit" value="OK">
    </label>
    <br>
    <label>
        Height:
        <input type="number" name="height" value="51" id="height"
    </label>
    <br>
    <label>
        Width:
        <input type="number" name="width" value="54"
    </label>
    <br>
</form>
<br>
<table id=previewTable></table>
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

$labelsJson = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['orderId'];
    $settings = $_POST['settings'];
    include "SettingsFiles/" . $settings . ".php";
    $width = getLabelWidth();
    $height = getLabelHeight();

    if (isset($_POST['submit'])) {
        $labelsJson = retrieveJson($orderId);
        $labelsJson = deleteData($labelsJson);
    }

    if (isset($_POST['refresh'])) {
        $labelsJson = json_decode($_POST['refresh'], true);
    }

    //todo make it possible to add static text box with labelsJson


    makeTable($labelsJson, $height);

    $newLabelsZPL = printLabels($labelsJson, $width, $height);
}

function deleteData($labelsJson)
{
    $newLabelsJson = array();
    foreach ($labelsJson as $item) {
        $label = array();
        //todo make array of keys variable and visable to make settings about it
        $label['amount'] = $item['amount'];
        $label['title'] = $item['title'];
        $label['description'] = $item['description'];
        $label['variant'] = $item['variant'];
        $newLabelsJson[] = $label;
    }
    return $newLabelsJson;
}

function makeTable($labelsJson, $height)
{
    if ($labelsJson == null) {
        return;
    }
    ?>
    <script>
        let labels = JSON.parse('<?php echo json_encode($labelsJson); ?>');
        const refreshLabelPreviews = document.createElement("button");
        refreshLabelPreviews.value = JSON.stringify(labels);

        const table = document.getElementById("previewTable");

        //Create all keys as header
        const headerRow = table.insertRow();
        for (let key in labels[0]) {
            const headerCell = headerRow.insertCell();
            headerCell.classList.add("header");
            headerCell.textContent = key.charAt(0).toUpperCase() + key.slice(1);
        }
        const labelPreviewHeaderCell = headerRow.insertCell();
        labelPreviewHeaderCell.classList.add("header");

        //todo make every cell can remake the label preview
        for (let i in labels) {
            const dataRow = table.insertRow();
            for (let j in labels[i]) {
                const dataCell = dataRow.insertCell();
                dataCell.style.textAlign = 'center';

                let elem;

                if (j.toLocaleLowerCase() === "aantal" || j.toLocaleLowerCase() === "amount") {
                    elem = document.createElement('p');
                    elem.appendChild(document.createTextNode(labels[i][j]))
                } else {
                    elem = document.createElement('textarea');

                    elem.style.width = '95%';
                    elem.value = labels[i][j];
                    elem.style.height = '<?php echo ($height * 8) . "px"?>'
                    elem.style.overflow = "hidden";
                    elem.style.resize = 'none';
                    elem.addEventListener("change", function () {
                        labels[i][j] = elem.value;
                        refreshLabelPreviews.value = JSON.stringify(labels);
                    });
                }


                elem.style.padding = '5px';
                elem.style.fontSize = '1.3em';
                dataCell.appendChild(elem);
            }
            const labelPreview = dataRow.insertCell();
            labelPreview.id = "labelPreview" + i;
            labelPreview.classList.add("labelPreview")

            const printButton = dataRow.insertCell();
            printButton.id = "printButton" + i;

        }
    </script>
    <?php

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
        let allLabelPreviews = [];
    </script>
    <?php
    $allLabels = array();
    if ($labelsJson != null) {
        foreach ($labelsJson as $index => $labelJson) {
            $textSettings = getTextSettings();
            $label = array();
            foreach ($textSettings as $j => $textSetting) {
                $textSetting['text'] = $labelJson[$j];
                $label[] = $textSetting;
            }
            $image = getImageSettings();
            ?>
            <script type="text/javascript" src="LabelPreviewer.js"></script>
            <script>
                base_image = new Image();
                base_image.src = '<?php echo $image['src']?>';
                imageSettings =<?php echo json_encode($image)?>;

                base_image.onload = function () {
                    let labelImg = createLabel(
                            <?php echo $width ?>,
                            <?php echo $height ?>,
                            <?php echo json_encode($label) ?>,
                            base_image,
                            imageSettings.x,
                            imageSettings.y,
                            imageSettings.width,
                            imageSettings.height
                        )
                    ;
                    labelImg.addEventListener('click', function (event) {
                        event.preventDefault();
                        sendImage(labelImg.src);
                    });
                    labelImg.classList.add("labelImg");

                    allLabelPreviews.push(labelImg.src);

                    const cellForLabelImage = document.getElementById("labelPreview" + <?php echo $index ?>)
                    cellForLabelImage.appendChild(labelImg);
                }

            </script>
        <?php } ?>
        <script>
            const sendImagesButton = document.createElement("button");
            sendImagesButton.textContent = "Print alle labels";
            sendImagesButton.addEventListener('click', function (event) {
                event.preventDefault();
                sendAllImages(allLabelPreviews);
            });
            labelPreviewHeaderCell.appendChild(sendImagesButton);


            refreshLabelPreviews.classList.add("refresh");
            refreshLabelPreviews.type = "refresh";
            refreshLabelPreviews.name = "refresh";
            refreshLabelPreviews.textContent = "↺";
            labelPreviewHeaderCell.appendChild(refreshLabelPreviews);
        </script>
        <?php
        return $allLabels;
    }
}

?>
<style>
    body {
        background-color: beige;
        font-family: Arial, serif;
        font-size: 20px;
    }

    input.button {
        border-radius: 5px;
        border: 1px solid grey;
        font-size: 18px;
        cursor: pointer;
        margin: 5px;
    }

    button {
        border-radius: 15px;
        border: 1px solid grey;
        font-size: 20px;
        cursor: pointer;
        margin: 5px;
        padding: 10px 25px;

    }

    button.refresh {
        font-size: 25px;
        padding: 4px 12px 8px 12px;
    }

    input, select {
        border-radius: 5px;
        border: 1px solid grey;
        font-size: 20px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        border-left: black 1px solid;
        border-right: black 1px solid;
    }

    td.header {
        background-color: #0c2d63;
        color: white;
        text-align: center;
        font-size: 1.8em;
        border-top: black 1px solid;
        border-bottom: black 2px solid;
        padding: 10px;
    }

    td {
        text-align: center;
    }

    tr:nth-child(even) {
        background-color: #b3b3b3;
    }

    tr:nth-child(odd) {
        background-color: #dedede;
    }

    .labelPreview {
        padding: 10px;
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
</style>
