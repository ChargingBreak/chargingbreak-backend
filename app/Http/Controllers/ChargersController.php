<?php

namespace App\Http\Controllers;

use App\Charger;

class ChargersController extends Controller
{
    public function __invoke() {
        return Charger::where('status', 'OPEN')->get();
    }
}
