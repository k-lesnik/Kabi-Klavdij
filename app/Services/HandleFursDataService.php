<?php

namespace App\Services;

use App\Jobs\WriteFursDataToFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class HandleFursDataService
{
    /**
     * The URL of the ZIP file to download.
     *
     * @var string
     */
    protected string $zipFileUrl = 'https://fu.gov.si/fileadmin/prenosi/DURS_zavezanci_PO_csv.zip';

    /**
     * The local path where the ZIP file should be saved.
     *
     * @var string
     */
    protected string $localZipPath = 'storage/app/furs_data.zip';

    /**
     * The local path where the extracted CSV content should be saved as a TXT file.
     *
     * @var string
     */
    protected string $outputTxtFilePath = 'storage/app/furs_data.txt';

    /**
     * The name of the CSV file inside the ZIP archive.
     *
     * @var string
     */
    protected string $csvFileNameInZip = 'DURS_zavezanci_PO.csv';

    /**
     * Downloads the ZIP file from the specified URL and saves it locally.
     *
     * @return bool True if the download and save were successful, false otherwise.
     */
    public function downloadAndSaveZipFile(): bool
    {
        Log::info('HandleFursDataService: Attempting to download ZIP file from: ' . $this->zipFileUrl);

        try {
            $response = Http::timeout(30)->withOptions(['verify' => false])->get($this->zipFileUrl);

            if ($response->successful()) {
                $directory = dirname($this->localZipPath);
                if (!File::isDirectory($directory)) {
                    File::makeDirectory($directory, 0755, true, true);
                }

                File::put($this->localZipPath, $response->body());

                Log::info('HandleFursDataService: ZIP file downloaded and saved to: ' . $this->localZipPath);
                return true;
            } else {
                Log::error('HandleFursDataService: Failed to download ZIP file. Status code: ' . $response->status());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('HandleFursDataService: An error occurred during ZIP file download: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extracts the specified CSV file from the downloaded ZIP archive and saves its content to a TXT file.
     *
     * @return bool True if the extraction and saving were successful, false otherwise.
     */
    public function extractCsvAndSaveToTxt(): bool
    {
        Log::info('HandleFursDataService: Attempting to extract CSV (without header, first 1000 rows) from: ' . $this->localZipPath . ' and queue for writing to: ' . $this->outputTxtFilePath);

        if (!File::exists($this->localZipPath)) {
            Log::error('HandleFursDataService: ZIP file not found at: ' . $this->localZipPath);
            return false;
        }

        $zip = new ZipArchive();
        if ($zip->open($this->localZipPath) === true) {
            $csvContent = $zip->getFromName($this->csvFileNameInZip);
            $zip->close();

            if ($csvContent !== false) {
                $lines = explode("\n", $csvContent);

                if (count($lines) > 0) {
                    unset($lines[0]);
                }

                $dataRows = array_slice($lines, 0, 1000);
                $chunks = array_chunk($dataRows, 100);

                File::put($this->outputTxtFilePath, '');

                foreach ($chunks as $chunk) {
                    WriteFursDataToFile::dispatch($chunk, $this->outputTxtFilePath);
                }

                return true;
            } else {
                Log::error('HandleFursDataService: CSV file not found inside the ZIP archive: ' . $this->csvFileNameInZip);
                return false;
            }
        } else {
            Log::error('HandleFursDataService: Failed to open the ZIP archive at: ' . $this->localZipPath);
            return false;
        }
    }
}
