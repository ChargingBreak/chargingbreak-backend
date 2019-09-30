<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use App\Charger;

class SyncChargersCommand extends Command
{
    const CHARGERS_URL = 'https://supercharge.info/service/supercharge/allSites';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:chargers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes chargers from supercharge.info';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $chargers = json_decode(file_get_contents(self::CHARGERS_URL), true);

        foreach ($chargers as $entry) {
            if (!isset($entry['locationId'])) {
                continue;
            }

            $charger = Charger::firstOrNew(['location_id' => $entry['locationId']]);
            $charger->location_id = $entry['locationId'] ?? null;
            $charger->name = $entry['name'] ?? null;
            $charger->status = $entry['status'] ?? null;
            $charger->address_street = $entry['address']['street'] ?? null;
            $charger->address_city = $entry['address']['city'] ?? null;
            $charger->address_state = $entry['address']['state'] ?? null;
            $charger->address_zip = $entry['address']['zip'] ?? null;
            $charger->address_country_id = $entry['address']['countryId'] ?? null;
            $charger->address_country = $entry['address']['country'] ?? null;
            $charger->address_region_id = $entry['address']['regionId'] ?? null;
            $charger->address_region = $entry['address']['region'] ?? null;
            $charger->coordinate = new Point($entry['gps']['latitude'] ?? 0, $entry['gps']['longitude'] ?? 0);
            $charger->date_opened = $entry['dateOpened'] ?? null;
            $charger->stall_count = $entry['stallCount'] ?? null;
            $charger->counted = $entry['counted'] ?? null;
            $charger->elevation_meters = $entry['elevationMeters'] ?? null;
            $charger->power_kilowatt = $entry['powerKilowatt'] ?? null;
            $charger->solar_canopy = $entry['solarCanopy'] ?? null;
            $charger->battery = $entry['battery'] ?? null;
            $charger->status_days = $entry['statusDays'] ?? null;
            $charger->save();
        }

        info(Charger::count() . ' total chargers');
    }
}
