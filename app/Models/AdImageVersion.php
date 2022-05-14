<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdImageVersion extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'ad_image_id',
        'name',
        'path',
        'is_external',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/ad-image-versions/'.$this->getKey());
    }

    public function adImage()
    {
        return $this->belongsTo(AdImage::class);
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
