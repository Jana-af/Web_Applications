<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FileService;

class AutoCheckOutFiles extends Command
{
    protected $signature = 'files:auto-checkout';
    protected $description = 'Automatically check out files after a specific timeout';

    private FileService $fileService;

    public function __construct(FileService $fileService)
    {
        parent::__construct();
        $this->fileService = $fileService;
    }

    public function handle()
    {
        $count = $this->fileService->autoCheckOut();
        $this->info("Automatically checked out {$count} file(s).");
    }
}
