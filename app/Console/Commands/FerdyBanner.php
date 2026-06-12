<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FerdyBanner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ferdy:ready';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print Ferdy deployment ASCII banner';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=====================================================");
        $this->info("  ______ ______ _____  _______     __");
        $this->info(" |  ____|  ____|  __ \\|  __ \\ \\   / /");
        $this->info(" | |__  | |__  | |__) | |  | \\ \\_/ / ");
        $this->info(" |  __| |  __| |  _  /| |  | |\\   /  ");
        $this->info(" | |    | |____| | \\ \\| |__| | | |   ");
        $this->info(" |_|    |______|_|  \\_\\_____/  |_|   ");
        $this->info("                                     ");
        $this->info("=====================================================");
        $this->info("       SISTEM DJUDASMS SIAP DIJALANKAN, BOSS!        ");
        $this->info("=====================================================");
        $this->info("");
    }
}
