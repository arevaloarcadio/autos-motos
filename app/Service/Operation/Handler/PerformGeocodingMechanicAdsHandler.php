<?php

declare(strict_types=1);

namespace App\Service\Operation\Handler;

use App\Enum\Ad\GeocodingStatusEnum;
use App\Enum\Operation\OperationNameEnum;
use App\Models\MechanicAd;
use App\Models\Operation;
use Geocoder\Laravel\ProviderAndDumperAggregator as Geocoder;
use Geocoder\Provider\GoogleMaps\Model\GoogleAddress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

/**
 * @package App\Service\Operation\Handler
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class PerformGeocodingMechanicAdsHandler implements IOperationHandler
{
    private const DEFAULT_LIMIT = 1;

    /**
     * @var Geocoder
     */
    private $geocoder;

    public function __construct(Geocoder $geocoder)
    {
        $this->geocoder = $geocoder;
    }


    public function canHandle(Operation $operation): bool
    {
        return $operation->name === OperationNameEnum::PERFORM_GEOCODING_MECHANIC_ADS;
    }

    public function handle(Operation $operation): Operation
    {
        $context = $this->validateContext($operation->context);

        $successCounter = 0;
        $failCounter = 0;
        $counter = 0;
        $limit = $context['limit'] ?? self::DEFAULT_LIMIT;
        MechanicAd::query()
                  ->whereNull(['latitude', 'longitude', 'geocoding_status'])
                  ->orderBy('created_at', 'DESC')
                  ->chunkById(
                      50,
                      function (Collection $ads) use ($limit, &$counter, &$successCounter, &$failCounter) {
                          /** @var MechanicAd $ad */
                          foreach ($ads as $ad) {
                              if ($counter === $limit) {
                                  return false;
                              }

                              $this->performGeocoding($ad);

                              $ad->save();
                              $counter++;

                              if ($ad->geocoding_status === GeocodingStatusEnum::SUCCESSFUL) {
                                  $successCounter++;
                                  continue;
                              }

                              $failCounter++;
                          }

                          sleep(1); // To prevent hitting the Google API rate limit.

                          return true;
                      }
                  );

        $operation->status_text = sprintf(
            'Success count: %d ads; Fail count: %d ads.',
            $successCounter,
            $failCounter
        );

        return $operation;
    }

    public function performGeocoding(MechanicAd $ad): MechanicAd
    {
        /** @var GoogleAddress|null $result */
        $result = $this->geocoder->limit(1)->geocode($ad->getFullAddressAttribute())->get()->first();
        if (null === $result) {
            $ad->geocoding_status = GeocodingStatusEnum::NO_RESULTS;

            return $ad;
        }

        $ad->latitude = strval($result->getCoordinates()->getLatitude());
        $ad->longitude = strval($result->getCoordinates()->getLongitude());
        $ad->geocoding_status = GeocodingStatusEnum::SUCCESSFUL;

        return $ad;
    }

    protected function validateContext(?array $context): array
    {
        if (null === $context) {
            return [];
        }

        return Validator::make(
            $context,
            [
                'limit' => ['nullable', 'integer'],
            ]
        )->validate();
    }
}
