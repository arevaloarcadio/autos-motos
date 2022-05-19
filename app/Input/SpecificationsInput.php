<?php

declare(strict_types=1);

namespace App\Input;

use App\Enum\Data\SpecificationNameEnum;
use Illuminate\Http\Request;

/**
 * @package App\Input
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class SpecificationsInput
{
    private $specifications = [];

    public function __construct(Request $request)
    {
        $this->specifications = array_filter(
            $request->only(array_keys(SpecificationNameEnum::getAll())),
            function ($value) {
                return ! ($value === null) || ! ($value === '');
            }
        );
    }

    public function getSpecifications(): array
    {
        return $this->specifications;
    }

    public function hasSpecifications(): bool
    {
        return count($this->specifications) > 0;
    }

    public function getValueByKey(string $key): ?string
    {
        return $this->specifications[$key] ?? null;
    }
}
