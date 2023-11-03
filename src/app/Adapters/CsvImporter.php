<?php

namespace App\Adapters;
use Illuminate\Http\UploadedFile;

abstract class CsvImporter
{

    /**
     * @param UploadedFile $file
     * 
     * @return [type]
     */
    public function parseCSV(UploadedFile $file){
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

    /**
     * @param array $data
     * 
     * @return array
     */
    abstract protected function initValidation(array $data):array;
}
