<?php

declare(strict_types=1);

namespace App\Service\Operation\Handler;

use App\Enum\Ad\GeocodingStatusEnum;
use App\Enum\Operation\OperationNameEnum;
use App\Models\AutoAd;
use App\Models\DealerShowRoom;
use App\Models\Operation;
use Geocoder\Laravel\ProviderAndDumperAggregator as Geocoder;
use Geocoder\Provider\GoogleMaps\Model\GoogleAddress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

/**
 * @package App\Service\Operation\Handler
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class PerformGeocodingAutoAdsHandler
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
        return $operation->name === OperationNameEnum::PERFORM_GEOCODING_AUTO_ADS;
    }

    public function handle(Operation $operation): Operation
    {
        $context = $this->validateContext($operation->context);

        $successCounter = 0;
        $failCounter = 0;
        $counter = 0;
        $limit = $context['limit'] ?? self::DEFAULT_LIMIT;
        AutoAd::query()
              ->with('dealerShowRoom')
              ->whereNull(['latitude', 'longitude', 'geocoding_status'])
              ->orderBy('created_at', 'DESC')
              ->chunkById(
                  50,
                  function (Collection $ads) use ($limit, &$counter, &$successCounter, &$failCounter) {
                      /** @var AutoAd $ad */
                      foreach ($ads as $ad) {
                          if ($counter === $limit) {
                              return false;
                          }

                          if ($ad->dealerShowRoom instanceof DealerShowRoom &&
                              ! (null === $ad->dealerShowRoom->latitude) &&
                              ! (null === $ad->dealerShowRoom->longitude)) {
                              $ad->latitude = $ad->dealerShowRoom->latitude;
                              $ad->longitude = $ad->dealerShowRoom->longitude;

                              $ad->save();

                              continue;
                          }

                          $this->performGeocoding($ad);

                          if ($ad->dealerShowRoom instanceof DealerShowRoom &&
                              ! ($ad->latitude === null) &&
                              ! ($ad->longitude === null)) {
                              $ad->dealerShowRoom->latitude = $ad->latitude;
                              $ad->dealerShowRoom->longitude = $ad->longitude;

                              $ad->dealerShowRoom->save();
                          }

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

    public function performGeocoding(AutoAd $ad): AutoAd
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
