<?php

namespace App\Console\Commands;

use App\Imports\TelegramUserImport;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;

class ImportTelegramUserFromCSVFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-telegram-user-from-c-s-v-file-command {file_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Users from CSV file';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info(string: 'starting importing users');

        $file_path = $this->argument(key: 'file_path');

        Excel::import(import: new TelegramUserImport(), filePath: $file_path, readerType: ExcelExcel::CSV);

        $this->info(string: 'finished importing users');

    }
}
