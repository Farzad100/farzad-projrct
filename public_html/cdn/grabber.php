<?php

require 'gate.php';

set_time_limit(0);

$id = $input['id'];
$address = $input['address'];
$size = $input['size'];
$filename = $input['filename'];

$last_slash = strrpos($address, '/');
if (is_numeric($last_slash)) {
    $folders = substr($address, 0, $last_slash);
    if (!is_dir(DOX_RELPATH . $folders)) mkdir(DOX_RELPATH . $folders, 0755, true);
}

$file_path = DOX_RELPATH . $address;

if (file_exists($file_path)) {
    if (checksum($file_path, $size)) die('done-' . $id);
    else unlink($file_path);
}

$fp = fopen($file_path, 'w+');
$ch = curl_init(MAIN_HOST . $filename);
curl_setopt($ch, CURLOPT_TIMEOUT, 45);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . TOKEN]);
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$res = curl_exec($ch);
curl_close($ch);
fclose($fp);

if (checksum($file_path, $size)) die('done-' . $id);
die('checksum');


function checksum($file, $size)
{
    return ceil(filesize($file) / 1024) == $size;
}
