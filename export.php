<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xml;

if (isset($_POST['export']) && isset($_POST['data'])) {
    $data = json_decode($_POST['data'], true);
    $format = $_POST['format'];
    $filename = 'exported.' . $format;

    if ($format == 'csv') {
        $file = fopen('exports/' . $filename, 'w');
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    } elseif ($format == 'xls' || $format == 'xlsx') {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($data, NULL, 'A1');
        $writer = new Xlsx($spreadsheet);
        $writer->save('exports/' . $filename);
    } elseif ($format == 'xml') {
        $xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
        function array_to_xml($data, &$xml_data) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $subnode = $xml_data->addChild("item$key");
                    array_to_xml($value, $subnode);
                } else {
                    $xml_data->addChild("$key", htmlspecialchars("$value"));
                }
            }
        }
        array_to_xml($data, $xml_data);
        $xml_data->asXML('exports/' . $filename);
    } elseif ($format == 'json') {
        file_put_contents('exports/' . $filename, json_encode($data));
    }

    echo json_encode(['status' => 'success', 'file' => $filename]);
}
?>
