<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersFavouriteAd extends Model
{
     use \App\Traits\TraitUuid;
    protected $fillable = [
    
    ];
    
    
    protected $dates = [
    
    ];
    public $timestamps = false;
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/users-favourite-ads/'.$this->getKey());
    }
}
