<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotedSimpleAd extends Model
{
	use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;
    use HasFactory;
}