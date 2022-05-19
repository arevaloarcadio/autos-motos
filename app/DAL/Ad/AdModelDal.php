<?php

declare(strict_types=1);

namespace App\DAL\Ad;


use App\DAL\AbstractEloquentDal;
use App\Models\AdModel;

/**
 * Defines the data access layer operations for ad model.
 *
 * @package App\DAL\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdModelDal extends AbstractEloquentDal
{
	
	public function getModel(): string
	{
		return AdModel::class;
	}
}
