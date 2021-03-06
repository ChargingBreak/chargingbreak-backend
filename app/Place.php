<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class Place extends Model
{
    use SpatialTrait;

    public $fillable = ['place_id'];
    public $spatialFields = ['coordinate'];

    public $with = ['spider'];

    public function spider() {
        return $this->belongsTo(PlaceSpider::class);
    }
}
