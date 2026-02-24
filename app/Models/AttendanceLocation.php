<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceLocation extends Model
{
    protected $fillable = [
        'location_name',
        'school_latitude',
        'school_longitude',
        'geofence_radius',
        'late_threshold',
        'full_day_hours',
        'working_day_start',
        'working_day_end',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'school_latitude' => 'decimal:8',
        'school_longitude' => 'decimal:8',
        'full_day_hours' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function staff(): HasMany
    {
        return $this->hasMany(staff::class);
    }

    protected static function booted(): void
    {
        static::saving(function ($location) {
            if ($location->is_default) {

                static::where('id', '!=', $location->id)->update(['is_default' => false]);
            }
        });
    }

    /**
     * Calculate distance from school using Haversine formula
     */
    public function distanceFrom(float $userLat, float $userLng): float
    {
        $earthRadius = 6371000; // In meters
        $lat1 = deg2rad($this->school_latitude);
        $lat2 = deg2rad($userLat);
        $lng1 = deg2rad($this->school_longitude);
        $lng2 = deg2rad($userLng);

        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;

        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlng / 2) ** 2;
        $c = 2 * asin(sqrt($a));

        return $earthRadius * $c; // Distance in meters
    }

    /**
     * Check if coordinates are within geofence
     */
    public function isWithinGeofence(float $userLat, float $userLng): bool
    {
        return $this->distanceFrom($userLat, $userLng) <= $this->geofence_radius;
    }
}
