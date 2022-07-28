<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;

    protected $fillable = [
        'plan_id',
        'user_id',
        'file',
        'name',
        'email',
        'phone',
        'country'
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    /*
    protected $appends = ['resource_url'];

    public function getResourceUrlAttribute()
    {
        return url('/admin/receipts/'.$this->getKey());
    }
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
