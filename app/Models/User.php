<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
//use Illuminate\Notifications\Notifiable;

class User extends Authenticatable  implements JWTSubject
{
    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;
    //use Notifiable;
    
    protected $fillable = [
        'first_name',
        'last_name',
        'mobile_number',
        'landline_number',
        'whatsapp_number',
        'email',
        'email_verified_at',
        'password',
        'dealer_id',
    
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    
    ];
    
    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/users/'.$this->getKey());
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    /**
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    public function hasRole(string $role): bool
    {
        return $this->roles->contains('name', $role);
    }

    /**
     * @return array
     */
    public function toAuthUserOutput(): array
    {
        $roles = collect($this->roles)->map(
            function (Role $role) {
                return $role->name;
            }
        );

        return [
            'id'         => $this->id,
            'email'      => $this->email,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'roles'      => $roles,
        ];
    }

     public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
