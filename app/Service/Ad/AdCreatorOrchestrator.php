<?php

declare(strict_types=1);

namespace App\Service\Ad\Creator;

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
class AdCreatorOrchestrator
{
    /**
     * @var IAdCreator[]
     */
    private $creators = [];
    
    /**
     * @var AdCreateService
     */
    private $adCreateService;
    
    public function __construct(Traversable $editors, AdCreateService $adCreateService)
    {
        $this->creators        = iterator_to_array($editors);
        $this->adCreateService = $adCreateService;
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
     * @param string $adType
     * @param array  $input
     *
     * @return Ad
     * @throws InvalidAdTypeInputException
     * @throws InvalidAdTypeProvidedException
     * @throws Throwable
     */
    public function create(string $adType, array $input): Ad
    {
        foreach ($this->getCreators() as $creator) {
            if (false === $creator->supports($adType)) {
                continue;
            }
            
            if (false === isset($input[$creator->getInputKey()])) {
                throw new InvalidAdTypeInputException();
            }
            
            DB::beginTransaction();
            try {
                $input['type'] = $adType;
                $ad = $creator->create($input);
            } catch (Throwable $exception) {
                // delete images from S3
                DB::rollBack();
                throw $exception;
            }
            
            DB::commit();
            
            return $ad;
        }
        
        throw new InvalidAdTypeProvidedException();
    }
    
    /**
     * Get the value of the finders property.
     *
     * @return IAdCreator[]
     */
    public function getCreators(): array
    {
        return $this->creators;
    }
}
