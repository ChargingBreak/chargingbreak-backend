<?php

namespace App\Http\Controllers;

use App\Charger;

class ChargersController extends Controller
{
    public function __invoke() {
        return Charger::where('status', 'OPEN')->get()->map(function ($charger) {
            $obj = $charger->toArray();

            $obj['coordinate'] = [
                $charger->coordinate->getLat(),
                $charger->coordinate->getLng()
            ];

            return collect($obj)->only(['location_id', 'name', 'coordinate']);
        });
    }
}
