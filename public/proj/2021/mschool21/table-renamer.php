<?php

if (($h = fopen('table.csv', 'r')) !== FALSE) {
    $mapping = [];
    $endCnt = 0;

    while (($row = fgetcsv($h, 1000, ',')) !== FALSE) {
        $origName = $row[0] ?? null;
        $mappedName = $row[1] ?? null;

        if (6 !== strlen($mappedName)) {
            continue;
        }

        $origName .= '.mp4';
        $mappedName .= '.mp4';

        if (file_exists($origName)) {
            rename($origName, $mappedName);
        }
    }

    fclose($h);
}
