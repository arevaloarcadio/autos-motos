<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

      protected $fillable =  ['id', 'name', 'email', 'phone', 'city', 'code_postal', 'whatsapp', 'country_id', 'user_id'];
}
