<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrimSpecification extends Model
{
    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;
    
    protected $fillable = [
        'trim_id',
        'specification_id',
        'value',
        'unit',
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
        return url('/admin/trim-specifications/'.$this->getKey());
    }
}
