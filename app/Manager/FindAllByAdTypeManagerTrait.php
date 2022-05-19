<?php
declare(strict_types=1);

namespace App\Manager;

/**
 * @package App\Manager
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
trait FindAllByAdTypeManagerTrait
{
    public function findAllByAdType(
        string $adType,
        string $orderBy = 'created_at',
        string $orderDirection = 'DESC'
    ) {
        return $this->findBy(
            [
                'ad_type' => strtoupper($adType),
            ],
            $orderBy,
            $orderDirection
        );
    }
}
