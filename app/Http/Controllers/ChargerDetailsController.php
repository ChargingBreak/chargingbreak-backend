<?php

namespace App\Http\Controllers;

use App\Charger;

class ChargerDetailsController extends Controller
{
    public function __invoke($name) {
        return Charger::where('location_id', $name)
            ->with('places')
            ->firstOrFail();
    }
}
