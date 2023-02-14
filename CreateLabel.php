<?php

function createLabel($product)
{
    $label = '^XA'; //nieuw label
    $label .= '^CF0,40'; //font 0 and the height of the characters to 40 dots.
    $label .= '^FO20,35'; //X position is set to 5 dots, and the Y position is set to 15 dots.
    $label .= '^FB406,1,0,C,0'; // field (406 dots), the number of lines (1), justification (0 for left-justified), orientation (C for centered), and the rotation angle (0 degrees).
    $label .= '^FD'; //data
    $label .= $product["title"];
    $label .= '^FS'; //dataend
    $label .= '^CF0,20'; //font 0 and the height of the characters to 20 dots.
    $label .= '^FO20,75'; ////X position is set to 15 dots, and the Y position is set to 55 dots.
    $label .= '^FB386,6,0,C,0';
    $label .= '^FD';
    $label .= $product["description"];
    $label .= '^FS';
    $label .= '^FO20,215';
    $label .= '^FB416,1,0,C,0';
    $label .= '^FD';
    $label .= '100 gram';
    $label .= '^FS'; //dataend
    $label .= '^XZ'; //label end

    return $label;
}

