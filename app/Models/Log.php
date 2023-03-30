<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  
class Log extends Model
{
    use HasFactory;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tracking_id', 'indoor_temperature', 'indoor_humidity','outdoor_temperature','outdoor_humidity'
    ];
    /**
     * Get the device associated with the tracking.
     */
    public function tracking()
    {
        return $this->belongsTo(Tracking::class);
    }


}