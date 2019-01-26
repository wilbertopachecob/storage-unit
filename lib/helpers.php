<?php
function isFileIncluded(string $fileA): bool
{
    $includedFiles = get_included_files();
    $files = array_map(function ($val) {
        $file = basename($val);
        return $file;
    }, $includedFiles);

    return in_array($fileA, $files);
}
