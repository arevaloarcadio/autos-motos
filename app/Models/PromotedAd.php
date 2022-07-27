<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotedAd extends Model
{

    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;
    
    protected $fillable = [
        'ad_id',
        'user_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/promoted-ads/'.$this->getKey());
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function plan()
    {
        return $this->belongsTo(User::class);
    }
}
