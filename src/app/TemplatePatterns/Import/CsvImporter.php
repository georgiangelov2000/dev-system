<?php

namespace App\TemplatePatterns\Import;

abstract class CsvImporter
{
    protected function parseCSV($file){
        $data = [];
        $headers = [];

        if (($handle = fopen($file, "r")) !== false) {
            // Read the first row as headers
            $headers = fgetcsv($handle, 1000, ",");
            
            // Read the rest of the rows as data
            while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                // Combine headers with row data
                $rowData = array_combine($headers, $row);
                $data[] = $rowData;
            }
            fclose($handle);
        }

        return $data;
    } 

    abstract protected function initValidation(array $data):array;
}
