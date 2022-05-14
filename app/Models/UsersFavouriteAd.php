<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersFavouriteAd extends Model
{
    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;
    
    protected $table = "user_favourite_ads";
    protected $fillable = [
    'id',
    'user_id',
    'ad_id'
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
