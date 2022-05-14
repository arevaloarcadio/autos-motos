<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdImage extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'ad_id',
        'path',
        'is_external',
        'order_index',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/ad-images/'.$this->getKey());
    }

     public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * @return HasMany
     */
    public function versions()
    {
        return $this->hasMany(AdImageVersion::class);
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getVersionPathByName(string $name): ?string
    {
        $version = $this->versions->firstWhere('name', $name);

        if ($version instanceof AdImageVersion) {
            return $version->url;
        }

        return $this->getUrlAttribute();
    }

    /**
     * @return string
     */
    /*public function getUrlAttribute(): string
    {
        if (1 === $this->is_external) {
            return $this->path;
        }

        return Storage::disk('s3')->url($this->path);
    }*/
}
