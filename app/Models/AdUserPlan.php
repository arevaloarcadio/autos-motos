<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdUserPlan extends Model
{
    protected $fillable = [
        'plan_user_id',
        'ad_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/ad-user-plans/'.$this->getKey());
    }
}
