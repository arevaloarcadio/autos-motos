<?php
declare(strict_types=1);

namespace App\Service\Ad\Validator;

/**
 * @package App\Service\Ad\Validator
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
interface IAdValidator
{
    public function supports(string $adType): bool;

    public function validate(array $input, ?int $step = null, bool $isUpdate = false): array;
}
