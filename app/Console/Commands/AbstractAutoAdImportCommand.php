<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enum\Ad\AdTypeEnum;
use App\Enum\Ad\ImageProcessingStatusEnum;
use App\Enum\Core\ApprovalStatusEnum;
use App\Manager\Market\MarketManager;
use App\Models\Ad;
use App\Models\CarBodyType;
use App\Models\CarFuelType;
use App\Models\CarTransmissionType;
use App\Models\Dealer;
use App\Models\DealerShowRoom;
use App\Models\Market;
use App\Models\User;
use App\Output\ImportAdInfoOutput;
use App\Output\ImportSellerInfoOutput;
use App\Output\ImportSellerOutput;
use App\Service\Ad\AdDeleteService;
use App\Service\Ad\Creator\AdCreatorOrchestrator;
use App\Service\Dealer\DealerService;
use App\Service\Dealer\DealerShowRoomService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use SimpleXMLElement;

/**
 * @package App\Console\Commands\Import
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 *
 * DON'T USE THIS YET! IT'S STILL A WORK IN PROGRESS.
 */
abstract class AbstractAutoAdImportCommand extends Command
{
    private MarketManager $marketManager;
    private DealerService $dealerService;
    private DealerShowRoomService $dealerShowRoomService;
    private AdCreatorOrchestrator $adCreator;
    private AdDeleteService $adDeleteService;

    private int $totalAdsCounter = 0;
    private int $successfulAdsCounter = 0;
    private int $updatedAdsCounter = 0;
    private int $skippedAdsCounter = 0;
    private int $erroredAdsCounter = 0;
    private int $newDealersCreated = 0;
    private int $adsCount = 0;
    private int $localAdCounter = 0;
    private array $importedSellersIds = [];
    private array $importedAdsIds = [];

    public function __construct(
        MarketManager $marketManager,
        DealerService $dealerService,
        DealerShowRoomService $dealerShowRoomService,
        AdCreatorOrchestrator $adCreator,
        AdDeleteService $adDeleteService
    ) {
        parent::__construct();
        $this->marketManager         = $marketManager;
        $this->dealerService         = $dealerService;
        $this->dealerShowRoomService = $dealerShowRoomService;
        $this->adCreator             = $adCreator;
        $this->adDeleteService       = $adDeleteService;
    }

    public function handle(): int
    {
        $this->info(sprintf('Command started at %s', (new DateTime())->format('Y-m-d H:i:s')));

        $filePath = $this->saveXmlLocally();
        $xml      = simplexml_load_file($filePath);

        $user = $this->getUser();
        auth()->login($user);

        $market = $this->getMarket();

        $sellers = $this->getSellersCollection($xml);
        if (null === $sellers) {
            $ads            = $this->getAdsCollectionFromParent($xml);
            $this->adsCount = count($ads);
            foreach ($ads as $ad) {
                try {
                    $dealerOutput = $this->processDealerAndShowRoom($ad, $market);
                    if (null === $dealerOutput) {
                        continue;
                    }
                    $this->totalAdsCounter++;
                    $adInfo = $this->generateAdInfo($ad);
                    $this->processAd($adInfo, $user, $market, $dealerOutput);
                } catch (Exception $exception) {
                    $this->error(
                        sprintf(
                            '====> Ad %d errored with message: %s; %d/%d; RAM Used: %s',
                            $ad->NumeroOferta,
                            $exception->getMessage(),
                            $this->localAdCounter,
                            $this->adsCount,
                            $this->getUsedMemory()
                        )
                    );
                    $this->erroredAdsCounter++;
                }

                if (count($this->importedAdsIds) > 500) {
                    $this->cleanUpAds();
                }
            }

            $this->cleanUpAds();

            $this->deleteImportFile($filePath);
            $this->printCommandStats();

            return Command::SUCCESS;
        }

        foreach ($sellers as $seller) {
            $dealerOutput = $this->processDealerAndShowRoom($seller, $market);
            if (null === $dealerOutput) {
                continue;
            }
            $this->importedSellersIds[] = $dealerOutput->getDealer()->external_id;
            $ads                        = $this->getAdsCollectionFromParent($seller);

            $this->importedAdsIds = [];
            foreach ($ads as $ad) {
                $this->totalAdsCounter++;
                $adInfo = $this->generateAdInfo($ad);
                $this->processAd($adInfo, $user, $market, $dealerOutput);
            }

            $this->cleanUpAds($dealerOutput->getDealer());
        }
        $this->cleanUpDealers($this->importedSellersIds);

        $this->deleteImportFile($filePath);

        $this->printCommandStats();

        return Command::SUCCESS;
    }

    protected function printCommandStats(): void
    {
        $this->info(sprintf('Command ended at %s', (new DateTime())->format('Y-m-d H:i:s')));
        $this->info(sprintf('Total new dealers created: %d', $this->newDealersCreated));
        $this->info(sprintf('Total ads processed: %d', $this->totalAdsCounter));
        $this->info(sprintf('Total ads created: %d', $this->successfulAdsCounter));
        $this->info(sprintf('Total ads updated: %d', $this->updatedAdsCounter));
        $this->info(sprintf('Total ads skipped: %d', $this->skippedAdsCounter));
        $this->info(sprintf('Total ads errored: %d', $this->erroredAdsCounter));
    }

    protected function processAd(
        ImportAdInfoOutput $adInfo,
        User $user,
        Market $market,
        ImportSellerOutput $dealerOutput
    ): ?Ad {
        $this->importedAdsIds[] = $adInfo->getExternalId();

        $existingAd = $this->findAd($adInfo->getExternalId());
        if ($existingAd instanceof Ad) {
            $this->localAdCounter++;

            if (false === $this->updateAd($existingAd, $adInfo)) {
                $this->skippedAdsCounter++;

                return $existingAd;
            }

            $this->updatedAdsCounter++;

            return $existingAd;
        }

        try {
            $startTime     = microtime(true);
            $ad            = $this->createAd(
                $adInfo,
                $user,
                $market->id,
                $dealerOutput->getDealer(),
                $dealerOutput->getShowRoom()
            );
            $endTime       = microtime(true);
            $executionTime = ($endTime - $startTime);
            $this->info(
                sprintf(
                    '====> Ad %d was successfully created in %ss; %d/%d; RAM Used: %s',
                    $adInfo->getExternalId(),
                    $executionTime,
                    $this->localAdCounter + 1,
                    $this->adsCount,
                    $this->getUsedMemory()
                )
            );
            $this->successfulAdsCounter++;
            $this->localAdCounter++;

            return $ad;
        } catch (Exception $exception) {
            $this->error(
                sprintf(
                    '====> Ad %d errored with message: %s; %d/%d; RAM Used: %s',
                    $adInfo->getExternalId(),
                    $exception->getMessage(),
                    $this->localAdCounter + 1,
                    $this->adsCount,
                    $this->getUsedMemory()
                )
            );

            return null;
        }
    }

    protected function createAd(
        ImportAdInfoOutput $adInfo,
        User $user,
        string $marketId,
        Dealer $dealer,
        DealerShowRoom $showRoom
    ): Ad {
        $adInput = [
            'title'                    => $adInfo->getTitle(),
            'description'              => $adInfo->getDescription(),
            'status'                   => ApprovalStatusEnum::APPROVED,
            'user_id'                  => $user->id,
            'market_id'                => $marketId,
            'source'                   => $this->getSourceName(),
            'external_id'              => $adInfo->getExternalId(),
            'images'                   => [],
            'images_processing_status' => ImageProcessingStatusEnum::PENDING,
            'auto_ad'                  => [
                'price'                        => $adInfo->getPrice(),
                'price_contains_vat'           => $adInfo->isPriceContainsVat(),
                'vin'                          => null,
                'doors'                        => (string) $adInfo->getDoors(),
                'seats'                        => (string) $adInfo->getSeats(),
                'mileage'                      => $adInfo->getMileage(),
                'exterior_color'               => $adInfo->getColor(),
                'interior_color'               => null,
                'condition'                    => $adInfo->getCondition(),
                'dealer_id'                    => $dealer->id,
                'dealer_show_room_id'          => $showRoom->id,
                'email_address'                => $showRoom->email_address,
                'address'                      => $showRoom->address,
                'zip_code'                     => $showRoom->zip_code,
                'city'                         => $showRoom->city,
                'country'                      => $showRoom->country,
                'mobile_number'                => $showRoom->mobile_number,
                'youtube_link'                 => null,
                'ad_fuel_type_id'              => optional($adInfo->getFuelType())->id,
                'ad_body_type_id'              => optional($adInfo->getBodyType())->id,
                'ad_transmission_type_id'      => optional($adInfo->getTransmissionType())->id,
                'ad_drive_type_id'             => null,
                'first_registration_month'     => optional($adInfo->getRegistrationDate())->month,
                'first_registration_year'      => optional($adInfo->getRegistrationDate())->year,
                'engine_displacement'          => $adInfo->getEngineDisplacement(),
                'power_hp'                     => (string) $adInfo->getPowerHp(),
                'owners'                       => (string) $adInfo->getOwners(),
                'inspection_valid_until_month' => null,
                'inspection_valid_until_year'  => null,
                'make_id'                      => $adInfo->getMake()->id,
                'model_id'                     => $adInfo->getModel()->id,
                'generation_id'                => null,
                'series_id'                    => null,
                'trim_id'                      => null,
                'equipment_id'                 => null,
                'additional_vehicle_info'      => $adInfo->getAdditionalVehicleInfo(),
                'co2_emission'                 => $adInfo->getCo2Emissions(),
                'options'                      => [],
            ],
        ];

        if (0 < count($adInfo->getImages())) {
            foreach ($adInfo->getImages() as $image) {
                $adInput['images'][] = [
                    'url'         => $image->getUrl(),
                    'extension'   => $image->getExtension(),
                    'is_external' => true,
                ];
            }
        }

        return $this->adCreator->create(AdTypeEnum::AUTO_SLUG, $adInput);
    }

    protected function updateAd(Ad $ad, ImportAdInfoOutput $adInfo): bool
    {
        if ($adInfo->getLastModified() instanceof Carbon && $ad->autoAd->updated_at >= $adInfo->getLastModified()) {
            return false;
        }

        $changed = false;
        if (null === $ad->autoAd->transmissionType && $adInfo->getTransmissionType() instanceof CarTransmissionType) {
            $ad->autoAd->ad_transmission_type_id = $adInfo->getTransmissionType()->id;
            $changed                             = true;
        }
        if (null === $ad->autoAd->bodyType && $adInfo->getBodyType() instanceof CarBodyType) {
            $ad->autoAd->ad_body_type_id = $adInfo->getBodyType()->id;
            $changed                     = true;
        }
        if (null === $ad->autoAd->fuelType && $adInfo->getFuelType() instanceof CarFuelType) {
            $ad->autoAd->ad_fuel_type_id = $adInfo->getFuelType()->id;
            $changed                     = true;
        }
        if ('other' === $ad->autoAd->exterior_color && 'other' !== $adInfo->getColor()) {
            $ad->autoAd->exterior_color = $adInfo->getColor();
            $changed                    = true;
        }
        if ($ad->autoAd->price !== $adInfo->getPrice()) {
            $ad->autoAd->price = $adInfo->getPrice();

            $changed = true;
        }
        if ($ad->autoAd->price_contains_vat !== $adInfo->isPriceContainsVat()) {
            $ad->autoAd->price_contains_vat = $adInfo->isPriceContainsVat();

            $changed = true;
        }
        if (true === $changed) {
            $ad->autoAd->save();

            return true;
        }

        return false;
    }

    protected function saveXmlLocally(): string
    {
        $directory = '/tmp/imports';
        $filePath  = sprintf('%s/%s_%s.xml', $directory, Carbon::now()->format('dmY'), strtolower(get_class($this)));

        if (false === is_dir($directory)) {
            mkdir($directory);
        }

        if (file_exists($filePath)) {
            return $filePath;
        }

        $rh = fopen($this->getImportFileUrl(), 'rb');
        $wh = fopen($filePath, 'wb');

        while ( ! feof($rh)) {
            if (fwrite($wh, fread($rh, 1024)) === false) {
                return '';
            }
        }

        fclose($rh);
        fclose($wh);

        return $filePath;
    }

    protected function deleteImportFile(string $path): void
    {
        unlink($path);
    }

    /**
     * Get the user to which we will attach all the new ads.
     *
     * @return User
     */
    protected function getUser(): User
    {
        /** @var User $user */
        $user = User::query()->where('email', '=', 'admin@autosmotos.es')->first();

        return $user;
    }

    protected function getMarket(): Market
    {
        $market = $this->marketManager->findOneBy(['internal_name' => $this->getMarketInternalName()]);
        if (null === $market) {
            throw new ModelNotFoundException('The "spain" market does not exist.');
        }

        return $market;
    }

    protected function processDealerAndShowRoom(SimpleXMLElement $container, Market $market): ?ImportSellerOutput
    {
        try {
            $sellerInfo = $this->generateSellerInfo($container);
            $dealer     = $this->findOrCreateDealer($sellerInfo);
            $showRoom   = $this->findOrCreateDealerShowRoom($sellerInfo, $dealer, $market);

            return new ImportSellerOutput($dealer, $showRoom);
        } catch (Exception $exception) {
            $this->error(
                sprintf(
                    '==> Failed to load seller with error: %s...; RAM Used: %s',
                    $exception->getMessage(),
                    $this->getUsedMemory()
                )
            );

            return null;
        }
    }

    protected function findOrCreateDealer(ImportSellerInfoOutput $seller): Dealer
    {
        $dealer = Dealer::query()
                        ->where('vat_number', '=', $seller->getVatNumber())
                        ->where('slug', '=', Str::slug($seller->getCompanyName()))
                        ->first();

        if ($dealer instanceof Dealer) {
            if (null === $dealer->external_id || null === $dealer->source) {
                $dealer->external_id = $seller->getExternalId();
                $dealer->source      = $this->getSourceName();

                $dealer->save();
            }

            return $dealer;
        }

        $dealerInput = [
            'company_name'  => $seller->getCompanyName(),
            'vat_number'    => $seller->getVatNumber(),
            'address'       => $seller->getAddress(),
            'zip_code'      => $seller->getZipCode(),
            'city'          => $seller->getCity(),
            'country'       => $this->getCountryName(),
            'logo_path'     => null,
            'email_address' => $seller->getEmailAddress(),
            'phone_number'  => $this->formatPhoneNumber($seller->getPhoneNumber()),
            'source'        => $this->getSourceName(),
            'external_id'   => $seller->getExternalId(),
        ];

        if ($seller->getLogoUrl() !== null && $seller->getLogoUrl() !== '') {
            $dealerInput['logo'] = [
                'body' => (string) Image::make($seller->getLogoUrl())->encode('data-url'),
            ];
        }

        $dealer = $this->dealerService->create($dealerInput);
        $this->newDealersCreated++;

        return $dealer;
    }

    protected function findOrCreateDealerShowRoom(
        ImportSellerInfoOutput $seller,
        Dealer $dealer,
        Market $market
    ): DealerShowRoom {
        if (0 < $dealer->showRooms->count()) {
            return $dealer->showRooms->first();
        }

        $showRoomInput = [
            'name'            => $dealer->company_name,
            'address'         => $dealer->address,
            'zip_code'        => $dealer->zip_code,
            'city'            => $dealer->city,
            'country'         => $this->getCountryName(),
            'latitude'        => $seller->getLatitude(),
            'longitude'       => $seller->getLongitude(),
            'email_address'   => $dealer->email_address,
            'mobile_number'   => $dealer->phone_number,
            'whatsapp_number' => $seller->getWhatsappNumber(),
            'market_id'       => $market->id,
            'dealer_id'       => $dealer->id,
        ];

        return $this->dealerShowRoomService->create($showRoomInput);
    }

    protected function findAd(string $externalId): ?Ad
    {
        /** @var Ad|null $ad */
        $ad = Ad::query()
                ->where('type', '=', AdTypeEnum::AUTO_SLUG)
                ->where('external_id', '=', $externalId)
                ->where('source', '=', $this->getSourceName())
                ->first();

        return $ad;
    }

    protected function formatPhoneNumber(string $phoneNumber): string
    {
        $phoneNumber = str_replace(' ', '', $phoneNumber);
        if (9 === strlen($phoneNumber)) {
            return sprintf('%s%s', $this->getPhonePrefix(), $phoneNumber);
        }

        return $phoneNumber;
    }

    /**
     * Deletes the ads that were no longer included for the specified dealer in the import file.
     *
     * @param Dealer|null $dealer
     */
    protected function cleanUpAds(?Dealer $dealer = null): void
    {
        $query = Ad::query()
                   ->join('auto_ads', 'auto_ads.ad_id', '=', 'ads.id')
                   ->where('ads.source', '=', $this->getSourceName())
                   ->whereNotIn('ads.external_id', $this->importedAdsIds)
                   ->whereNotNull('ads.external_id');
        if ($dealer instanceof Dealer) {
            $query->where('auto_ads.dealer_id', '=', $dealer->id);
        }
        $ads = $query->get();

        $deletedAdsCounter = 0;
        foreach ($ads as $ad) {
            $this->adDeleteService->delete($ad);
            $deletedAdsCounter++;
        }

        $this->importedAdsIds = [];

        $this->info(
            sprintf(
                '==> Deleted %d old ads (%s); RAM Used: %s',
                $deletedAdsCounter,
                $dealer instanceof Dealer ? $dealer->id : '-',
                $this->getUsedMemory()
            )
        );
    }

    /**
     * Deletes the ads of the dealers that were no longer included in the import file.
     *
     * @param array $externalDealerIds
     */
    protected function cleanUpDealers(array $externalDealerIds): void
    {
        $dealers = Dealer::query()
                         ->whereNotIn('external_id', $externalDealerIds)
                         ->whereNotNull('external_id')
                         ->where('source', '=', $this->getSourceName())
                         ->get();

        $deletedAdsCounter = 0;
        /** @var Dealer $dealer */
        foreach ($dealers as $dealer) {
            $ads = Ad::query()
                     ->select('ads.*')
                     ->join('auto_ads', 'auto_ads.ad_id', '=', 'ads.id')
                     ->where('auto_ads.dealer_id', '=', $dealer->id)
                     ->where('ads.source', '=', $this->getSourceName())
                     ->whereNotNull('ads.external_id')
                     ->get();

            foreach ($ads as $ad) {
                $this->adDeleteService->delete($ad);
                $deletedAdsCounter++;
            }
        }

        $this->info(
            sprintf(
                '==> Deleted %d ads from %d dealers no longer in the import; RAM Used: %s',
                $deletedAdsCounter,
                $dealers->count(),
                $this->getUsedMemory()
            )
        );
    }

    abstract protected function getMarketInternalName(): string;

    abstract protected function getImportFileUrl(): string;

    abstract protected function getSellersCollection(SimpleXMLElement $xml): ?array;

    abstract protected function getAdsCollectionFromParent(SimpleXMLElement $parentXml): ?array;

    abstract protected function generateSellerInfo(SimpleXMLElement $container): ImportSellerInfoOutput;

    abstract protected function generateAdInfo(SimpleXMLElement $ad): ImportAdInfoOutput;

    abstract protected function getSourceName(): string;

    abstract protected function getCountryName(): string;

    abstract protected function getPhonePrefix(): string;

    private function getUsedMemory(): string
    {
        return sprintf("%sMB", intval(memory_get_usage(true) / 1024 / 1024));
    }
}
