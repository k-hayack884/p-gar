<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherDaily extends Model
{
    /**
     * G4: `date` is an Asia/Tokyo calendar day, not a UTC-converted timestamp.
     *
     * G1: forecast upserts must never replace an existing actual row
     * (`is_forecast = false`). Weather retrieval is implemented in its own
     * milestone and must preserve this invariant.
     */
    protected $guarded = [];
}
