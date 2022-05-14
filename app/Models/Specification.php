<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;
    
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
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
        return url('/admin/specifications/'.$this->getKey());
    }
    
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Specification::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Specification::class, 'parent_id')->orderBy('name', 'asc');;
    }

    protected function getSluggableField(): string
    {
        return 'name';
    }
}
