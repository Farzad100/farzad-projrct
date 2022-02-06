<?php

require 'gate.php'; 

rrmdir(TEMPS_FOLDER, 0);

die('done');

//Delete Folder with all contents in it
function rrmdir($dir, $delete_main_folder = 1)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir")
                    rrmdir($dir . "/" . $object);
                else unlink($dir . "/" . $object);
            }
        }
        reset($objects);
        if ($delete_main_folder == 1) rmdir($dir);
        return true;
    }
    return false;
}
