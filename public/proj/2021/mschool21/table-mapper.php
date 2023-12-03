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

        $elemA = $row[2];
        $elemB = $row[3];
        $elemC = $row[4];
        $text = $row[5];
        $textA = $row[6];
        $textB = $row[7];
        $textC = $row[8];
        $isEnd = $row[10];

        $isEnd = mb_strtolower($isEnd);
        $isEnd = $isEnd[0] . $isEnd[1];
        $isEnd = 'да' === $isEnd;

        if ($isEnd) {
            $A = 'end' . ++$endCnt;
            $B = $C = null;
            $textA = $textB = $textC = null;
        }

        $mapping[$mappedName] = array_filter([
            'text' => $text,
            'textA' => $textA,
            'textB' => $textB,
            'textC' => $textC,
            'A' => $elemA,
            'B' => $elemB,
            'C' => $elemC,
        ]);
    }

    fclose($h);

    file_put_contents('mapping.json', json_encode($mapping, JSON_UNESCAPED_UNICODE));
}
