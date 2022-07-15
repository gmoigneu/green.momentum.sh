<?php

namespace App\Jobs;

use App\Models\Datacenter;
use App\Models\Usage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class UpdateEnergyRecordsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected String $endpoint;
    protected String $token;
    protected Datacenter $datacenter;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($datacenter)
    {
        $this->endpoint = "https://api.co2signal.com/v1/latest";
        $this->token = config("app.co2");

        if(!is_null($datacenter)) {
            $this->datacenter = $datacenter;
        } else {
            $this->datacenter = Datacenter::orderBy('updated_at', 'asc')->first();
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::withOptions([
            'debug' => true,
        ])->accept('application/json')->withHeaders([
            'auth-token' => $this->token,
        ])->get($this->endpoint, [
            'lon' => $this->datacenter->long,
            'lat' => $this->datacenter->lat
        ]);

        if($response->successful()) {
            $usage = Usage::create([
                'countryCode' => $response->json('countryCode'),
                'carbonIntensity' => $response->json('data.carbonIntensity'),
                'fossilFuelPercentage' => $response->json('data.fossilFuelPercentage'),
                'units'  => $response->json('units.carbonIntensity'),
                'datacenter_id' => $this->datacenter->id
            ]);
            $this->datacenter->touch();
        } else {
            die('Error');
        }
    }
}
