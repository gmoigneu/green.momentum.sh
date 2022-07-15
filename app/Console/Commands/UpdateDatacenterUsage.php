<?php

namespace App\Console\Commands;

use App\Jobs\UpdateEnergyRecordsJob;
use App\Models\Datacenter;
use Illuminate\Console\Command;
use Illuminate\Queue\Jobs\Job;

class UpdateDatacenterUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usage:update {datacenter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update datacenter usage';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $datacenter = Datacenter::findOrFail($this->argument('datacenter'));

        var_dump($datacenter);

        UpdateEnergyRecordsJob::dispatchSync($datacenter);

        return 0;
    }
}
