<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'town_id','municipality_id','state_id','cluster_id','tag','ssid','ssid_password','latitude','longitude','description'
    ];

    /**
     * Get the cluster associated with the location.
     */
    public function cluster()
    {
        return $this->belongsTo(Tag::class, 'cluster_id');
    }
}
