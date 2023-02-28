
            <?php
function getLabelHeight() {
    return 23;
}
function getLabelWidth()
{
    return 25;
}

function getTextSettings()
{

    return array('title' => array(
                'align' => 'L',
                'fontType' => 'Arial',
                'fontColor' => 'Black',
                'fontSize' => '20',
                'x' => '27',
                'y' => '12'),'description' => array(
                'align' => 'C',
                'fontType' => '12',
                'fontColor' => 'Grey',
                'fontSize' => '10',
                'x' => '214',
                'y' => '25'));
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
