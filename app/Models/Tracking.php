<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'device_id','location_id','user_id','api_token','installation_date','image_device','image_indoor','image_outdoor','status'
    ];
    
    /**
     * Get the device associated with the tracking.
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get the location associated with the tracking.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

}
