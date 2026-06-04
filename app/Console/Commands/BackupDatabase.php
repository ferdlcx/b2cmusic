<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the MySQL database to SQL file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = "backup-" . date('Y-m-d-H-i-s') . ".sql";
        $storagePath = storage_path('backups');

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $command = sprintf(
            'mysqldump --user="%s" --password="%s" --host="%s" "%s" > "%s"',
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_HOST'),
            env('DB_DATABASE'),
            $storagePath . '/' . $filename
        );

        $returnVar = NULL;
        $output  = NULL;
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info('Database backup completed successfully. ' . $filename);
            Log::info('Database backup completed successfully. ' . $filename);
        } else {
            $this->error('Database backup failed.');
            Log::error('Database backup failed. Ensure mysqldump is in PATH.');
        }
    }
}
