<?php

require 'gate.php';

$address = $input['address'];
if (strpos($address, '---') !== false) {
    $x = explode('---', $address);
    $address = $x[0];
    $id = $x[1];
} else {
    $id = null;
}

$file_path = DOX_RELPATH . $address;

if (file_exists($file_path)) {
    $format = str_replace('.', '', substr($address, -4));
    $filename = TEMPS_FOLDER . ($id ? $id . '-' : '') . md5($file_path) . '.' . $format;
    if (file_exists($filename)) die($filename);
    copy($file_path, $filename);
    die($filename);
}
die('file_not_found');
