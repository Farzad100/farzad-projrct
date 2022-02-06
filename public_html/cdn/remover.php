<?php

require 'gate.php';

$address = $input['address'];

if (!file_exists(DOX_RELPATH . $address)) die('done');
if (unlink(DOX_RELPATH . $address)) die('done');
die('err');
