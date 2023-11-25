<?php

namespace App\Http\Controllers;

use App\Factory\FactoryImportAdapter;
use App\Factory\Views\ImportView;
use App\Http\Requests\CSVRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    private $importView;

    /**
     * Constructor to initialize class dependencies.
     *
     * @param ImportView $importView
     * @param SupplierCsvImporter $csvImporter
     */
    public function __construct(ImportView $importView)
    {
        $this->importView = $importView;
    }

    /**
     * @param string $type
     * 
     * @return [type]
     */
    public function index(string $type)
    {
        return $this->importView->redirectToView($type);
    }

    /**
     * Handles the request to import data from a CSV file.
     *
     * @param CSVRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CSVRequest $request)
    {
        // We start a database transaction
        DB::beginTransaction();
        try {
            // Validate and retrieve the data from the request
            $data = $request->validated();

            // Build the class name dynamically
            $classBuilder =  FactoryImportAdapter::select($data['type']);
            // Check if the class exists
            if ($classBuilder && class_exists(get_class($classBuilder))) {
                // Instantiate the class and initialize validation
                $instance = $classBuilder;
            } else {
                // If the class does not exist, handle the error as needed
                return back()->withInput()->with('error', 'Invalid CSV importer type.');
            }

            // Process the data from the CSV file
            $csvData = $instance->parseCSV($data['file']);
            
            // We check if the CSV file contains data
            if (empty($csvData)) {
                // If not, we return the user with an error message
                return back()->withInput()->with(['error' => 'There is no data in the file.']);
            };

            $validationData = $instance->initValidation(array_change_key_case($csvData));

            // // Check for validation errors
            // if (isset($validationData['error'])) {
            //     // If there are errors, return the user with the error
            //     return back()->withInput()->with($validationData);
            // }

            // If there are no errors, we commit the transaction to the database
            DB::commit();
        } catch (\Exception $e) {
            // If an error occurs, log the error, rollback the transaction, and return the user with an error message            DB::rollback();
            return back()->withInput()->with('error', $e->getMessage());
        }

        // If everything is successful, we redirect the user to the provider list with a success message
        return redirect()->back()->with('success', 'Data imported successfully.');
    }
}
