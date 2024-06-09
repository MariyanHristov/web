<?php

// Include Composer autoloader
require 'vendor/autoload.php';

// Import PhpSpreadsheet classes
use PhpOffice\PhpSpreadsheet\IOFactory;

// Function to read data from a CSV file
function readCsv($file) {
    // Debugging - Log the file being read
    error_log('Reading CSV file: ' . $file);

    // Read CSV file and return data
    return array_map('str_getcsv', file($file));
}

// Function to read data from an Excel file (XLS or XLSX)
function readXls($file) {
    // Debugging - Log the file being read
    error_log('Reading Excel file: ' . $file);

    // Load Excel file and extract data from active sheet
    $spreadsheet = IOFactory::load($file);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    // Remove the first row (header row) from the sheet data
    array_shift($sheetData);

    return $sheetData;
}


// Function to read data from an XML file
function readXml($file) {
    // Debugging - Log the file being read
    error_log('Reading XML file: ' . $file);

    // Load XML file and convert to associative array
    $xml = simplexml_load_file($file);

    // Check if the XML was loaded successfully
    if ($xml === false) {
        // Failed to load XML file
        error_log('Failed to load XML file: ' . $file);
        return []; // Return an empty array
    }

    // Flatten XML structure into a nested array
    $data = [];
    flattenXml($xml, $data);

    return $data;
}

// Recursive function to flatten XML structure into a nested array
function flattenXml($xml, &$data) {
    foreach ($xml->children() as $element) {
        // If element has children, recursively flatten them
        if ($element->count() > 0) {
            $nestedData = [];
            flattenXml($element, $nestedData);
            $data[] = $nestedData;
        } else {
            // Add element value to the array
            $data[] = (string)$element;
        }
    }
}




// Function to read data from a JSON file
function readJson($file) {
    // Debugging - Log the file being read
    error_log('Reading JSON file: ' . $file);

    // Read JSON file and decode data
    return json_decode(file_get_contents($file), true);
}

// Function to filter data based on criteria
function filterData($data, $criteria) {
    // Debugging - Log the filtering criteria
    error_log('Filtering data with criteria: ' . $criteria);

    // Example: filtering rows where the first column value matches the criteria
    return array_filter($data, function($row) use ($criteria) {
        return $row[0] == $criteria;
    });
}

// Function to join two datasets
function joinData($data1, $data2) {
    // Debugging - Log the number of rows in each dataset
    error_log('Joining data. Dataset 1 rows: ' . count($data1) . ', Dataset 2 rows: ' . count($data2));

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

// Function to union two datasets
function unionData($data1, $data2) {
    // Debugging - Log the number of rows in each dataset
    error_log('Unioning data. Dataset 1 rows: ' . count($data1) . ', Dataset 2 rows: ' . count($data2));

    // Concatenate two datasets
    return array_merge($data1, $data2);
}

// Check if the request contains required parameters (action and file)
if (isset($_POST['action']) && isset($_POST['file'])) {
    // Retrieve action and file parameters from the request
    $action = $_POST['action'];
    $file = $_POST['file'];

    // Debugging - Log the action and file parameters
    error_log('Received request. Action: ' . $action . ', File: ' . $file);

    // Initialize data variable
    $data = [];

    // Determine the file type based on its extension
    $extension = pathinfo($file, PATHINFO_EXTENSION);

    // Read the file based on its extension
    if ($extension == 'csv') {
        $data = readCsv($file);
    } elseif ($extension == 'xls' || $extension == 'xlsx') {
        $data = readXls($file);
    } elseif ($extension == 'xml') {
        $data = readXml($file);
    } elseif ($extension == 'json') {
        $data = readJson($file);
    }

    // Debugging - Log the data read from the file
    error_log('Data read from file: ' . print_r($data, true));

    // Perform action based on the request
    if ($action == 'filter') {
        $filteredData = filterData($data, 'Alice'); // Example criteria
        echo json_encode($filteredData);
    } elseif ($action == 'join') {
        $file2 = 'uploads/sample2.csv'; // Example second file for joining
        $data2 = readCsv($file2);
        $joinedData = joinData($data, $data2);
        echo json_encode($joinedData);
    } elseif ($action == 'union') {
        $file2 = 'uploads/sample2.csv'; // Example second file for union
        $data2 = readCsv($file2);
        $unionedData = unionData($data, $data2);
        echo json_encode($unionedData);
    } elseif ($action == 'load') {
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Invalid Action parameter']);
    }
} else {
    echo json_encode(['error' => 'Missing parameters']);
}

?>
