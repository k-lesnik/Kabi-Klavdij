<?php

namespace App\Http\Controllers;

use App\Services\HandleFursDataService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FursController extends Controller
{
    protected string $outputTxtFilePath;
    protected HandleFursDataService $handleFursDataService;

    /**
     * Constructor to inject the HandleFursDataService.
     *
     * @param HandleFursDataService $handleFursDataService
     */
    public function __construct(HandleFursDataService $handleFursDataService)
    {
        $this->outputTxtFilePath = storage_path('app/furs_data.txt');
        $this->handleFursDataService = $handleFursDataService;
    }

    public function index(): object
    {
        if (File::exists($this->outputTxtFilePath)) {
            $fileContent = File::get($this->outputTxtFilePath);

            $rows = explode("\n", $fileContent);
            $data = [];
            foreach ($rows as $row) {
                if (!empty(trim($row))) {
                    $data[] = str_getcsv($row, ";", '"', '');
                }
            }
            return view('furs.index', ['fursData' => $data]);
        } else {
            return view('furs.index', ['fursData' => []])->with('error', 'Data file not found.');
        }
    }

    public function sync(): void
    {
        try {
            $this->handleFursDataService->downloadAndSaveZipFile();
            $this->handleFursDataService->extractCsvAndSaveToTxt();
        } catch (\Exception $e) {
            Log::error('FursController@sync: An unexpected error occurred while using the HandleFursDataService: ' . $e->getMessage());
        }

        Log::info('Furs synchronization process completed by the controller at: ' . now());
    }
}
