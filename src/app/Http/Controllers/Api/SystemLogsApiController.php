<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class SystemLogsApiController extends Controller
{
    public function getData(Request $request)
    {
        $logFilePath = storage_path("logs/http_requests-" . date('Y-m-d') . ".log");
        
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);

        if (File::exists($logFilePath)) {
            $logContent = File::get($logFilePath);
            $logLines = explode("\n", $logContent);

            $logs = [];
            foreach ($logLines as $line) {
                $logParts = explode(" ", $line, 2);

                $logs[] = [
                    'timestamp' => $logParts[0] ?? '',
                    'message' => $logParts[1] ?? '',
                ];
            }

            $filteredRecords = count($logs);
            $totalRecords = $filteredRecords;
            
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $logs,
            ]);
        }
    }
}
