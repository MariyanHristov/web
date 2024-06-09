<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function readCsv($file) {
    return array_map('str_getcsv', file($file));
}

function readXls($file) {
    $spreadsheet = IOFactory::load($file);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    return $sheetData;
}

function readXml($file) {
    $xml = simplexml_load_file($file);
    $json = json_encode($xml);
    return json_decode($json, TRUE);
}

function readJson($file) {
    return json_decode(file_get_contents($file), true);
}

function filterData($data, $criteria) {
    // Example: filtering rows where the first column value is 'Alice'
    return array_filter($data, function($row) use ($criteria) {
        return $row[0] == $criteria;
    });
}

function joinData($data1, $data2) {
    // Simple join on the first column of both datasets
    $result = [];
    foreach ($data1 as $row1) {
        foreach ($data2 as $row2) {
            if ($row1[0] == $row2[0]) {
                $result[] = array_merge($row1, $row2);
            }
        }
    }
    return $result;
}

function unionData($data1, $data2) {
    return array_merge($data1, $data2);
}

if (isset($_POST['action']) && isset($_POST['file'])) {
    $action = $_POST['action'];
    $file = $_POST['file'];
    $data = [];

    // Read file
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    if ($extension == 'csv') {
        $data = readCsv($file);
    } elseif ($extension == 'xls' || $extension == 'xlsx') {
        $data = readXls($file);
    } elseif ($extension == 'xml') {
        $data = readXml($file);
    } elseif ($extension == 'json') {
        $data = readJson($file);
    }

    // Perform action
    if ($action == 'filter') {
        $data = filterData($data, 'Alice');  // Example criteria
    } elseif ($action == 'join') {
        $file2 = 'uploads/sample2.csv';  // Example second file for joining
        $data2 = readCsv($file2);
        $data = joinData($data, $data2);
    } elseif ($action == 'union') {
        $file2 = 'uploads/sample2.csv';  // Example second file for union
        $data2 = readCsv($file2);
        $data = unionData($data, $data2);
    }

    echo json_encode($data);
}
?>
