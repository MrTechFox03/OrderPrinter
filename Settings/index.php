<?php
function addTextSettings($i)
{
    //todo Voeg meer variabele toe
    $options = array("title", "description", "variant", "amount");
    ?>
    <div style="border: black solid 1px">
        <label>Geselecteerde text inhoud:
            <select name="text<?php echo $i ?>">
                <?php
                foreach ($options as $option) {
                    ?>
                    <option><?php echo $option ?></option>
                    <?php
                }
                ?>
            </select>
        </label><br>
        <label>
            X:
            <input name="x<?php echo $i ?>" type="number" onchange="notifyPreview()">
        </label><br>
        <label>
            Y:
            <input name="y<?php echo $i ?>" type="number" onchange="notifyPreview()">
        </label><br>
        <label>
            Fonttype:
            <input name="fontType<?php echo $i ?>" type="text" onchange="notifyPreview()">
        </label><br>
        <label>
            Fontkleur:
            <input name="fontColor<?php echo $i ?>" type="text" onchange="notifyPreview()">
        </label><br>
        <label>
            Fontgrootte:
            <input name="fontSize<?php echo $i ?>" type="number" onchange="notifyPreview()">
        </label><br>
        <label>
            Uitlijning:
            <select name="align<?php echo $i ?>" onchange="notifyPreview()">
                <option value="L">Links</option>
                <option value="C">Midden</option>
                <option value="R">Rechts</option>
            </select>
        </label><br>
    </div>
    <?php
}

?>
    <!DOCTYPE html>
    <html>
    <script src="../LabelPreviewer.js"></script>
    <script>
        function notifyPreview() {
            if (!checkInputsFilled())
                return;
            const formData = new FormData();
            formData.append('preview', 'preview');
            fetch("index.php", {
                method: 'POST',
                body: formData
            }) .then((response) => response.json())
                .then((data) => console.log(data));
        }

        function checkInputsFilled() {
            // Get all input elements on the page
            const inputs = document.getElementsByTagName('input');

            // Check if all input elements have a value
            for (let i = 0; i < inputs.length; i++) {

                if (inputs[i].id === "countSettingsBoxes" || inputs[i].id === "nameOfSettings") {
                    continue;
                }
                if (inputs[i].value === '') {
                    // If any input element is empty, return false
                    return false;
                }
            }

            // If all input elements have a value, return true
            return true;
        }
        function makeLabelPreview() {
            console.log("test")
            base_image = new Image();
            base_image.src = '../img/logo-dark.png';


            let label = createLabel(
                <?php echo $_POST["width"] ?>,
                <?php echo $_POST["height"] ?>,
                <?php echo json_encode(makeTextBoxesArray()) ?>,
                base_image,
                <?php echo 50 ?>,
                <?php echo 50 ?>,
                <?php echo 50 ?>,
                <?php echo 200 ?>
            )
            let labelPlace = document.getElementById("label");
            labelPlace.src = label;
            console.log(label.src)
        }
    </script>
    <span style="padding-right:50px; font-size:200%">Instellingen</span><br/>
    <form action="index.php" method="post">
        <label>
            Hoeveel textboxen wil je hebben? <br>
            <input name="addSettingsBox" type="number" id="countSettingsBoxes">
            <button>OK</button>
        </label><br>
        <label>
            Naam of instellingen:
            <input type="text" name="name" id="nameOfSettings">
        </label><br>
        <label>
            Hoogte:
            <input type="number" name="height" onchange="notifyPreview()">
        </label><br>
        <label>
            Breedte:
            <input type="number" name="width" onchange="notifyPreview()">
        </label><br>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST["addSettingsBox"])) {
                for ($i = 1; $i <= $_POST["addSettingsBox"]; $i++) {
                    addTextSettings($i);
                }
                ?>
                <script>
                    document.getElementById("countSettingsBoxes").value = <?php echo $_POST["addSettingsBox"] ?>
                </script>
                <?php
            }
        }
        ?>
        <input class="button" type="submit" name="preview" value="Preview maken">
        <input class="button" type="submit" name="submit" value="Instellingen versturen">
    </form>
    <div id="label"></div>
    <style>
        body {
            background-color: beige;
            font-family: Arial, serif;
            font-size: 20px;
        }
    </style>
    </html>
<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["preview"])) {
        ?>
        <script>
            makeLabelPreview()
        </script>
        <?php
    }


    if (isset($_POST["submit"])) {

        $file = fopen("../SettingsFiles/" . $_POST["name"] . ".php", 'w');

        $text = "array(";
        for ($i = 1; $i <= $_POST["addSettingsBox"]; $i++) {
            $text .= "'" . $_POST["text" . $i] . "' => array(
                'align' => " . "'" . $_POST["align" . $i] . "',
                'fontType' => " . "'" . $_POST["fontType" . $i] . "',
                'fontColor' => " . "'" . $_POST["fontColor" . $i] . "',
                'fontSize' => " . "'" . $_POST["fontSize" . $i] . "',
                'x' => " . "'" . $_POST["x" . $i] . "',
                'y' => " . "'" . $_POST["y" . $i] . "'),";
        }
        $text = substr($text, 0, -1);
        $text .= ");";

        $content = "
            <?php
function getLabelHeight() {
    return " . $_POST["height"] . ";
}
function getLabelWidth()
{
    return " . $_POST["width"] . ";
}

function getTextSettings()
{

    return " . $text . "
}
function getImageSettings()
{
    return array(
        'src' => 'img/logo-dark.png',
        'x' => 50,
        'y' => 50,
        'width' => 50,
        'height' => 50
    );
}
";

        fwrite($file, $content);

        fclose($file);
    }
}

function makeTextBoxesArray()
{
    $array = array();
    for ($i = 1; $i <= $_POST["addSettingsBox"]; $i++) {
        $array[] = array(
            'text' => 'Dit is een testregel',
            'align' => $_POST["align" . $i],
            'fontType' => $_POST["fontType" . $i],
            'fontColor' => $_POST["fontColor" . $i],
            'fontSize' => $_POST["fontSize" . $i],
            'x' => $_POST["x" . $i],
            'y' => $_POST["y" . $i]);
    }
    return $array;
}

?>