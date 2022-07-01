<?php

namespace Database\Seeders;

use App\Models\Datacenter;
use App\Models\Provider;
use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $providers = [
            'alibaba',
            'aws',
            'azure',
            'digitalocean',
            'google',
            'interoute',
            'oracle',
            'platformsh',
            'rackspace',
            'upcloud'
        ];



        foreach ($providers as $providerCode) {

            $source = json_decode(file_get_contents('https://raw.githubusercontent.com/GuGuss/cloud-providers/master/app/providers/'.$providerCode.'.json'), true);

            dump($providerCode);
            dump($source['name']);

            $provider = Provider::create([
                'code' => $providerCode,
                'name' => $source['name']
            ]);
            $provider->save();

            foreach ($source['regions'] as $sourceRegion) {

                $datacenter = Datacenter::create([
                    'provider_code' => $sourceRegion['providerCode'],
                    'provider_id' => $provider->id,
                    'provider_code_api' => (array_key_exists('providerCodeApi', $sourceRegion)) ? $sourceRegion['providerCodeApi'] : null,
                    'city' => $sourceRegion['city'],
                    'country_code' => $sourceRegion['country'],
                    'facilities' => $sourceRegion['facilities'],
                    'lat' => $sourceRegion['locationLat'],
                    'long' => $sourceRegion['locationLong'],
                    'planned' => (array_key_exists('planned', $sourceRegion)) ? $sourceRegion['planned'] : false,
                    'special' => (array_key_exists('special', $sourceRegion)) ? $sourceRegion['special'] : false
                ]);

                $datacenter->save();

                if(array_key_exists('regions', $sourceRegion)) {
                    $regionsToSync = [];
                    foreach ($sourceRegion['regions'] as $r) {
                        $region = Region::firstOrCreate(['name' => $r]);
                        $regionsToSync[] = $region->id;
                    }

                    $datacenter->regions()->sync($regionsToSync);
                    $datacenter->save();
                }
            }
        }


    }
}
