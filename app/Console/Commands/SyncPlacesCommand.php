<?php

namespace App\Console\Commands;

use PharData;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use GeoJson\GeoJson;
use GeoJson\Feature\Feature;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Charger;
use App\PlaceSpider;
use App\Place;

class SyncPlacesCommand extends Command
{
    const PLACES_URL = 'https://s3.amazonaws.com/placescraper-results/runs/2019-08-16-02-28-56/output.tar.gz';
    const MAX_CHARGER_TO_PLACE_DISTANCE_METERS = 250;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:places';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes places from alltheplaces.xyz';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', -1);

        $this->download();
        $this->extract();
        $this->process();
    }

    private function getPlacesFolder(): string {
        return storage_path('places');
    }

    private function download()
    {
        $filename = storage_path('places.tar.gz');

        if (!file_exists($filename)) {
            $this->info('Downloading places data...');
            file_put_contents($filename, fopen(self::PLACES_URL, 'r'));
        } else {
            $this->info('Skipping download, file already exists');
        }
    }

    private function extract()
    {
        $filenameCompressed = storage_path('places.tar.gz');
        $filenameTar = storage_path('places.tar');
        $placesFolder = $this->getPlacesFolder();

        if (!file_exists($placesFolder)) {
            $this->info('Extracting places data...');
            $compressedFile = new PharData($filenameCompressed);
            $compressedFile->decompress();

            $tarFile = new PharData($filenameTar);
            $tarFile->extractTo($placesFolder);

            @unlink($filenameTar);
        } else {
            $this->info('Skipping extraction, folder already exists');
        }
    }

    private function process()
    {
        $placesFolder = $this->getPlacesFolder();

        $directory = new RecursiveDirectoryIterator($this->getPlacesFolder());
        $iterator = new RecursiveIteratorIterator($directory);

        foreach ($iterator as $filename) {
            if (Str::endsWith($filename->getFilename(), '.geojson')) {
                $this->processPlaceFile($filename->getRealPath());
            }
        }
    }

    private function processPlaceFile(string $filename)
    {
        $this->info($filename);

        $json = @json_decode(file_get_contents($filename));

        if (!$json) {
            $this->error('Skipping. No valid data');
            return;
        }

        $collection = GeoJson::jsonUnserialize($json);
        foreach ($collection as $feature) {
            if (!$feature->getGeometry()) {
                continue;
            }

            $coords = $feature->getGeometry()->getCoordinates();
            $point = new Point($coords[1], $coords[0]);

            $nearbyCharger = Charger::distanceSphere('coordinate', $point, self::MAX_CHARGER_TO_PLACE_DISTANCE_METERS)->first();

            if ($nearbyCharger) {
                $this->storePlace($feature, $point, $nearbyCharger);
            }
        }
    }

    private function storePlace(Feature $feature, Point $placePoint, Charger $charger)
    {
        $properties = $feature->getProperties();

        $spider = PlaceSpider::where('name', $properties['@spider'])->first();
        if (!$spider) {
            $spider = new PlaceSpider();
            $spider->name = $properties['@spider'];
            $spider->display_name = Str::ucfirst($spider->name);
            $spider->save();
        }

        $hours = $properties['opening_hours'] ?? null;

        if (is_array($hours)) {
            $hours = implode('; ', $hours);
        }

        $place = Place::firstOrNew(['place_id' => $feature->getId()]);
        $place->spider_id = $spider->id;
        $place->charger_id = $charger->id;
        $place->distance_meters = intval($this->calculateDistance($placePoint, $charger->coordinate));
        $place->name = $properties['name'] ?? null;
        $place->address_full = $properties['addr:full'] ?? null;
        $place->address_housenumber = $properties['addr:housenumber'] ?? null;
        $place->address_street = $properties['addr:street'] ?? null;
        $place->address_city = $properties['addr:city'] ?? null;
        $place->address_state = $properties['addr:state'] ?? null;
        $place->address_zip = $properties['addr:postcode'] ?? null;
        $place->address_country = $properties['addr:country'] ?? null;
        $place->coordinate = $placePoint;
        $place->phone = $properties['phone'] ?? null;
        $place->website = $properties['website'] ?? null;
        $place->opening_hours = $hours;
        $place->save();
    }

    private function calculateDistance(Point $pointA, Point $pointB)
    {
        $earthRadius = 6378137.0;

        $latFrom = deg2rad($pointA->getLat());
        $lonFrom = deg2rad($pointA->getLng());
        $latTo = deg2rad($pointB->getLat());
        $lonTo = deg2rad($pointB->getLng());

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
