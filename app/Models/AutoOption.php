<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoOption extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'internal_name',
        'slug',
        'parent_id',
        'ad_type',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/auto-options/'.$this->getKey());
    }

    /**
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(AutoOption::class, 'parent_id')->orderBy('internal_name', 'asc');
    }

    /**
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(AutoOption::class, 'parent_id');
    }

    /**
     * @return string
     */
    protected function getSluggableField(): string
    {
        return 'internal_name';
    }
}
