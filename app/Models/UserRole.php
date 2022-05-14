<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;
    protected $primaryKey  = 'user_id';
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'role_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/user-roles/'.$this->getKey());
    }
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
