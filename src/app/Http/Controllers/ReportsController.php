<?php

namespace App\Http\Controllers;

use App\Services\ReportsService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportData;

class ReportsController extends Controller
{

    protected $reportsService;
    protected $validatedData;

    public function __construct(ReportsService $reportsService)
    {
        $this->reportsService = $reportsService;
    }

    public function index()
    {
        return view('reports.index');
    }

    public function takeReport(Request $request)
    {

        $this->validatedData = $request->validate([
            'type_export' => 'required|string',
            'data_export' => 'required|string',
            'options' => 'required|integer',  // Changed the rule to array
            'month' => 'required|string',
            'data_sub_export' => 'string'
        ]);

        dd($this->validatedData);

        $result = $this->reportsService->generateReports(
            $this->validatedData['options'],
            $this->validatedData['data_export'],
            $this->validatedData['month'],
            $this->validatedData['data_sub_export']
        );

        $type = null;

        if ($this->validatedData['type_export'] === 'xlsx') {
            $type = \Maatwebsite\Excel\Excel::XLSX;
            $fileName = 'report.xlsx';
        } elseif ($this->validatedData['type_export'] === 'csv') {
            $type = \Maatwebsite\Excel\Excel::CSV;
            $fileName = 'report.csv';
        }

        $excelData = Excel::raw(new ExportData($result['data'], $result['headings']), $type);
        
        $storagePath = storage_path('app/public/reports');

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0777, true); // Create the directory if it doesn't exist
        }

        $fileName = $this->getUniqueFileName($storagePath, $fileName); // Get a unique file name
        $filePath = $storagePath . '/' . $fileName;
        file_put_contents($filePath, $excelData);

        return response()->json(['download_url' => url('storage/reports/' . $fileName)]);
    }


    protected function getUniqueFileName($storagePath, $fileName)
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);

        $index = 1;
        $uniqueFileName = $fileName;

        while (file_exists($storagePath . '/' . $uniqueFileName)) {
            $uniqueFileName = $baseName . '_' . $index . '.' . $extension;
            $index++;
        }

        return $uniqueFileName;
    }
}
