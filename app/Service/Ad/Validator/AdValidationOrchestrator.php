<?php

declare(strict_types=1);

namespace App\Service\Ad\Validator;

use App\Exceptions\InvalidAdTypeInputException;
use App\Exceptions\InvalidAdTypeProvidedException;
use App\Models\Ad;
use App\Service\Ad\AdCreateService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;
use Traversable;

/**
 * @package App\Service\Ad\Creator
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdValidationOrchestrator
{
    /**
     * @var IAdValidator[]
     */
    private $validators = [];

    public function __construct(Traversable $validators)
    {
        $this->validators = iterator_to_array($validators);
    }

    /**
     * @param string $adType
     *
     * @return View
     * @throws InvalidAdTypeProvidedException
     */
    public function presentForm(string $adType): View
    {
        foreach ($this->getCreators() as $creator) {
            if (false === $creator->supports($adType)) {
                continue;
            }

            return $creator->presentForm();
        }

        throw new InvalidAdTypeProvidedException();
    }

    /**
     * @param string   $adType
     * @param array    $input
     * @param int|null $step
     * @param bool     $isUpdate
     *
     * @return array
     * @throws InvalidAdTypeProvidedException
     */
    public function validate(string $adType, array $input, ?int $step = null, bool $isUpdate = false): array
    {
        foreach ($this->getValidators() as $validator) {
            if (false === $validator->supports($adType)) {
                continue;
            }

            return $validator->validate($input, $step, $isUpdate);
        }

        throw new InvalidAdTypeProvidedException();
    }

    /**
     * @return IAdValidator[]
     */
    public function getValidators(): array
    {
        return $this->validators;
    }
}
