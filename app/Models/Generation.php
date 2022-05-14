<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Generation extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'name',
        'model_id',
        'year_begin',
        'year_end',
        'is_active',
        'ad_type',
        'external_id',
        'external_updated_at',
    
    ];
    
    
    protected $dates = [
        'external_updated_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/generations/'.$this->getKey());
    }

    public function model()
    {
        return $this->belongsTo(Model::class);
    }

    public function getYearEndAttribute(?int $value): int
    {
        return $value ?? Carbon::now()->year;
    }
}
