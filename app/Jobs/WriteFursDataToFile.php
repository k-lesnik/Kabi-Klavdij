<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class WriteFursDataToFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rows;
    protected $filePath;

    /**
     * Create a new job instance.
     *
     * @param array $rows
     * @param string $filePath
     */
    public function __construct(array $rows, string $filePath)
    {
        $this->rows = $rows;
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('WriteFursDataToFile job started with ' . count($this->rows) . ' rows.');

        $contentToWrite = implode("\n", $this->rows) . "\n"; // Directly implode the array of string rows

        File::append($this->filePath, $contentToWrite);

        Log::info('WriteFursDataToFile job appended ' . count($this->rows) . ' rows to ' . $this->filePath);
        Log::info('WriteFursDataToFile job finished.');
    }
}
