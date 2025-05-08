<?php

namespace App\Console\Commands;

use App\Http\Controllers\FursController;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Log;

class SyncFurs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-furs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get data from furs and write it to a file.';

    /**
     * Execute the console command.
     * @throws BindingResolutionException
     */
    public function handle(): void
    {
        $fursController = app()->make(FursController::class);

        $fursController->sync();
    }
}
