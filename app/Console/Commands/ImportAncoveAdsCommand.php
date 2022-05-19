<?php

namespace App\Console\Commands;

use App\Enum\Ad\AdSourceEnum;
use App\Enum\Ad\ConditionEnum;
use App\Output\ImportAdImageOutput;
use App\Output\ImportAdInfoOutput;
use App\Output\ImportSellerInfoOutput;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

class ImportAncoveAdsCommand extends AbstractAutoAdImportCommand
{
    use AutoAdImportHelperTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ads:ancove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ancove ads import';

    protected function getMarketInternalName(): string
    {
        return 'spain';
    }

    protected function getImportFileUrl(): string
    {
        return env('ANCOVE_IMPORT_URL');
    }

    protected function getSellersCollection(SimpleXMLElement $xml): ?array
    {
        return null;
    }

    protected function getAdsCollectionFromParent(SimpleXMLElement $parentXml): ?array
    {
        $initial = (array) $parentXml;

        return $initial['ad'];
    }

    protected function generateSellerInfo(SimpleXMLElement $container): ImportSellerInfoOutput
    {
        return new ImportSellerInfoOutput(
            $this->generateDealerExternalId(trim((string) $container->NombreComercial)),
            trim((string) $container->NombreComercial),
            null,
            trim((string) $container->region),
            trim((string) $container->postcode),
            trim((string) $container->city),
            $this->getCountryName(),
            trim((string) $container->email),
            $this->formatPhoneNumber((string) $container->telefono)
        );
    }

    protected function generateAdInfo(SimpleXMLElement $ad): ImportAdInfoOutput
    {
        $make         = $this->findMake(trim((string) $ad->make));
        $model        = $this->findModel(trim((string) $ad->model), $make);
        $transmission = $this->findTransmissionType(trim((string) $ad->transmission));
        $fuelType     = $this->findFuelType(trim((string) $ad->fuel));
        $images       = $this->processImages($ad);
        $mileage      = $this->formatIntValue(trim((string) $ad->mileage));

        return new ImportAdInfoOutput(
            trim((string) $ad->id),
            trim((string) $ad->title),
            trim((string) $ad->content),
            $make,
            $model,
            $this->formatFloatValue(trim((string) $ad->price)),
            $this->getColor(trim((string) $ad->color)),
            $mileage,
            $this->getConditionByMileage($mileage),
            $this->processRegistrationDate(trim((string) $ad->year)),
            false,
            $images,
            $this->formatStringValue(trim((string) $ad->version)),
            $transmission,
            null,
            $fuelType,
            $this->formatIntValue(trim((string) $ad->doors)),
            $this->formatIntValue(trim((string) $ad->seats)),
            $this->processEngineDisplacement(trim((string) $ad->engine_size)),
            $this->formatIntValue(trim((string) $ad->power)),
            null,
            null,
            Carbon::now()
        );
    }

    protected function getConditionByMileage(int $mileage): string
    {
        if ($mileage < 100) {
            return ConditionEnum::NEW;
        }

        return ConditionEnum::USED;
    }


    protected function getSourceName(): string
    {
        return AdSourceEnum::ANCOVE_IMPORT;
    }

    protected function getCountryName(): string
    {
        return 'España';
    }

    protected function getPhonePrefix(): string
    {
        return '+34';
    }

    /**
     * @param SimpleXMLElement $ad
     *
     * @return ImportAdImageOutput[]
     */
    private function processImages(SimpleXMLElement $ad): array
    {
        $output = [];
        foreach ($ad->pictures->picture as $picture) {
            $url       = trim((string) $picture->picture_url);
            $parts     = explode('.', $url);
            $extension = array_pop($parts);

            $output[] = new ImportAdImageOutput($url, $extension);
        }

        return $output;
    }

    private function processRegistrationDate(string $year): ?Carbon
    {
        if ('' === $year) {
            return null;
        }

        return Carbon::createFromFormat('m.Y', sprintf('01.%s', $year));
    }

    private function processEngineDisplacement(string $engineSize): ?int
    {
        if ('' === $engineSize) {
            return null;
        }

        return $this->formatIntValue($this->formatFloatValue($engineSize) * 1000);
    }

    private function generateDealerExternalId(string $dealerName)
    {
        $chars    = str_split(md5($dealerName));
        $numbers  = array_filter($chars, fn(string $letter) => is_numeric($letter));
        $firstTen = array_slice($numbers, 0, 6);

        return sprintf('%s%d', implode('', $firstTen), strlen($dealerName));
    }
}
