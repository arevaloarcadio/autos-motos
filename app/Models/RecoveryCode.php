<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecoveryCode extends Model
{
	use \App\Traits\TraitUuid;
    use HasFactory;
}
