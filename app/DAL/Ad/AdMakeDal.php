<?php

declare(strict_types=1);

namespace App\DAL\Ad;

use App\DAL\AbstractEloquentDal;
use App\Models\AdMake;

/**
 * Defines the data access layer operations for ad make.
 *
 * @package App\DAL\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdMakeDal extends AbstractEloquentDal
{
	public function getModel(): string
	{
		return AdMake::class;
	}
}
