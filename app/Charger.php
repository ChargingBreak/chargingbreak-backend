<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class Charger extends Model
{
    use SpatialTrait;

    public $fillable = ['location_id'];
    public $spatialFields = ['coordinate'];
}
