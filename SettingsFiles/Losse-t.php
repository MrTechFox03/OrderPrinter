<?php
function getLabelHeight(){
    return 51;
}
function getLabelWidth(){
    return 54;
}

function getTextSettings(){
    $title = array(
        'align' => 'L',
        'fontType' => 'Arial Bold',
        'fontColor' => 'Black',
        'fontSize' => 40,
        'x' => 5,
        'y' => 50
    );
    $description = array(
        'align' => 'C',
        'fontType' => 'Arial',
        'fontColor' => 'Black',
        'fontSize' => 20,
        'x' => 5,
        'y' => 150
    );
    $variant = array(
        'align' => 'C',
        'fontType' => 'Arial',
        'fontColor' => 'Black',
        'fontSize' => 20,
        'x' => 0,
        'y' => 400
    );
    return array('title' => $title, 'description' => $description, 'variant' => $variant);
}
function getImageSettings(){
    return array(
        'src' => 'img/logo-dark.png',
        'x' => 50,
        'y' => 50,
        'width' => 200,
        'height' => 50
    );
}