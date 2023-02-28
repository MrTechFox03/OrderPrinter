<?php
function getListOfFiles()
{
    $out = array();
    foreach (glob('SettingsFiles/*.php') as $filename) {
        $p = pathinfo($filename);
        $out[] = $p['filename'];
    }
    return $out;
}
