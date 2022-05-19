<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Enum\Ad\ColorEnum;
use App\Enum\Ad\ConditionEnum;
use App\Models\CarAd\CarBodyType;
use App\Models\CarAd\CarFuelType;
use App\Models\CarAd\CarTransmissionType;
use App\Models\Data\Make;
use App\Models\Data\Model;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;

/**
 * @package App\Console\Commands\Import
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
trait AutoAdImportHelperTrait
{
    protected function findMake(string $externalMake): Make
    {
        if ('' === $externalMake) {
            throw new Exception('no_make');
        }
        $externalMake = mb_strtolower(trim($externalMake));

        $make = Make::query()
                    ->where('ad_type', '=', 'auto')
                    ->where('name', '=', $externalMake)->first();

        $knownMakes = [
            'mercedes'    => 'mercedes-benz',
            'rolls royce' => 'rolls-royce',
            'citroën'     => 'citroen',
            'land-rover'  => 'land rover',
        ];
        if (null === $make && isset($knownMakes[$externalMake])) {
            $make = Make::query()->where('name', '=', $knownMakes[$externalMake])->first();
        }

        if ($make instanceof Make) {
            return $make;
        }

        throw new Exception(sprintf('invalid_make: %s', $externalMake));
    }

    protected function findModel(string $externalModel, Make $make): Model
    {
        if ('' === $externalModel) {
            throw new Exception('no_model');
        }
        $model = $this->queryModel($externalModel, $make->id);

        if (null === $model) {
            $externalModel = mb_strtolower(trim($externalModel), 'UTF-8');
            $model         = $this->queryModel($externalModel, $make->id);
        }

        if (null === $model && Str::contains($externalModel, strtolower($make->name))) {
            $model = $this->queryModel(
                trim(Str::replaceFirst(strtolower($make->name), '', $externalModel)),
                $make->id
            );
        }

        if (null === $model && intval($externalModel) > 0) {
            $model = $this->queryModel((string) intval($externalModel), $make->id);
        }

        if (null === $model && $make->name === 'BMW' && Str::contains($externalModel, 'serie')) {
            $modelParts = explode(' ', $externalModel);
            $model      = $this->queryModel(sprintf('%d Series', $modelParts[1]), $make->id);
        }

        if (null === $model && $make->name === 'Mercedes-Benz' &&
            (Str::contains($externalModel, 'clase') || Str::contains($externalModel, 'classe'))) {
            $modelParts = explode(' ', $externalModel);
            $model      = $this->queryModel(sprintf('%s-Class', $modelParts[1]), $make->id);
        }

        if (null === $model) {
            $modelParts = explode(' ', $externalModel);
            $model      = $this->queryModel($modelParts[0], $make->id);
        }

        if (null === $model) {
            $modelParts = explode(' ', $externalModel);
            if (isset($modelParts[1])) {
                $model = $this->queryModel($modelParts[1], $make->id);
            }
        }

        $knownModels = [
            'clio sporter' => 'Clio',
            'mini'         => 'one',
            'discovery 4'  => 'discovery',
            'evoque'       => 'Range Rover Evoque',
            'cc'           => 'Passat CC',
            'xc-60'        => 'xc60',
        ];
        if (null === $model && isset($knownModels[$externalModel])) {
            $model = $this->queryModel($knownModels[$externalModel], $make->id);
        }

        if ($model instanceof Model) {
            return $model;
        }

        throw new Exception(sprintf('invalid_model for make %s: %s', $make->name, $externalModel));
    }

    protected function queryModel(string $name, string $makeId): ?Model
    {
        /** @var Model $instance */
        $instance = Model::query()->where('name', '=', $name)
                         ->where('ad_type', '=', 'auto')
                         ->where('make_id', '=', $makeId)
                         ->first();

        return $instance;
    }

    protected function getColor(string $externalColor): string
    {
        $externalColor = strtolower(trim($externalColor));
        $colors        = $this->getColorOptions();

        if (isset($colors[$externalColor])) {
            return $colors[$externalColor];
        }

        return 'other';
    }

    protected function getColorOptions(): array
    {
        return [
            'BLANCO GLACIAR METALIZADO' => ColorEnum::WHITE,
            'azul claro'                => ColorEnum::BLUE,
            'verde claro'               => ColorEnum::GREEN,
            'plata'                     => ColorEnum::SILVER,
            'rojo oscuro'               => ColorEnum::RED,
            'gris claro'                => ColorEnum::GRAY,
            'gris-negro'                => ColorEnum::GRAY,
            'rojo'                      => ColorEnum::RED,
            'azul'                      => ColorEnum::BLUE,
            'plateado'                  => ColorEnum::SILVER,
            'blanco'                    => ColorEnum::WHITE,
            'negro'                     => ColorEnum::BLACK,
            'marrón'                    => ColorEnum::BROWN,
            'gris'                      => ColorEnum::GRAY,
            'oro'                       => ColorEnum::GOLD,
            'verde'                     => ColorEnum::GREEN,
            'beige'                     => ColorEnum::BEIGE,
        ];
    }

    protected function getCondition(string $externalCondition): string
    {
        $externalCondition = strtolower(trim($externalCondition));
        $conditions        = [
            'vehículos de ocasión' => ConditionEnum::USED,
            'vehículos nuevos'     => ConditionEnum::NEW,
            'KM0'                  => ConditionEnum::NEW,
        ];

        if (isset($conditions[$externalCondition])) {
            return $conditions[$externalCondition];
        }

        return ConditionEnum::OTHER;
    }

    protected function findFuelType(string $externalFuel): ?CarFuelType
    {
        if ('' === $externalFuel) {
            return null;
        }
        $externalFuel = strtolower(trim($externalFuel));
        $fuels        = $this->getFuelOptions();

        if (isset($fuels[$externalFuel])) {
            /** @var CarFuelType $fuelType */
            $fuelType = CarFuelType::query()->where('internal_name', '=', $fuels[$externalFuel])
                                   ->where('ad_type', '=', 'auto')
                                   ->first();

            return $fuelType;
        }

        return null;
    }

    protected function getFuelOptions(): array
    {
        return [
            'diésel'                    => 'diesel',
            'eléctrico'                 => 'electric',
            'gas'                       => 'gas_gasoline',
            'gas licuado (glp)'         => 'gas_gasoline',
            'gas natural (cng)'         => 'gas_gasoline',
            'gasolina'                  => 'gas_gasoline',
            'híbrido (gasolina)'        => 'hybrid_petrol_electric',
            'híbrido (diésel)'          => 'hybrid_diesel_electric',
            'híbrido'                   => 'other',
            'etanol'                    => 'ethanol',
            'híbrido enchufable (phev)' => 'other',
        ];
    }

    protected function findBodyType(string $externalBody): ?CarBodyType
    {
        if ('' === $externalBody) {
            return null;
        }
        $externalBody = strtolower(trim($externalBody));
        $bodyTypes    = $this->getBodyOptions();

        if (isset($bodyTypes[$externalBody])) {
            /** @var CarBodyType $bodyType */
            $bodyType = CarBodyType::query()
                                   ->where('internal_name', '=', $bodyTypes[$externalBody])
                                   ->where('ad_type', '=', 'auto')
                                   ->first();

            return $bodyType;
        }

        return null;
    }

    protected function getBodyOptions(): array
    {
        return [
            'berlina'             => 'sedan',
            'cabriolet'           => 'convertible',
            'coche sin carnet'    => null,
            'deportivo'           => 'sport_coupe',
            'familiar'            => 'minivan',
            'furgoneta'           => 'minivan',
            'monovolumen'         => 'minivan',
            'pickup'              => 'suv_crossover',
            'sedan'               => 'sedan',
            'todoterreno'         => 'suv_crossover',
            'utilitario'          => null,
            'vehículo industrial' => null,
            '4x4 SUV'             => 'suv_crossover',
            'coupé'               => 'sport_coupe',
        ];
    }

    protected function findTransmissionType(string $externalTransmission): ?CarTransmissionType
    {
        if ('' === $externalTransmission) {
            return null;
        }
        $externalTransmission = strtolower(trim($externalTransmission));
        $transmissions        = $this->getTransmissionOptions();

        if (isset($transmissions[$externalTransmission])) {
            /** @var CarTransmissionType $transmission */
            $transmission = CarTransmissionType::query()
                                               ->where('internal_name', '=', $transmissions[$externalTransmission])
                                               ->where('ad_type', '=', 'auto')
                                               ->first();

            return $transmission;
        }

        return null;
    }

    protected function getTransmissionOptions(): array
    {
        return [
            'manual'     => 'manual',
            'automático' => 'automatic',
        ];
    }

    protected function formatStringValue(string $value): ?string
    {
        return $value === '' ? null : $value;
    }

    protected function formatFloatValue(string $value): ?float
    {
        if ($value === '' || floatval($value) === 0.0) {
            return null;
        }

        return floatval($value);
    }

    protected function formatIntValue(string $value): ?int
    {
        if ($value === '' || intval($value) === 0) {
            return null;
        }

        return intval($value);
    }

    protected function formatPhoneNumber(string $phoneNumber): string
    {
        if ($phoneNumber === '') {
            return '-';
        }
        $phoneString  = trim(str_replace([' ', '.'], '', $phoneNumber));
        $phoneNumbers = explode('/', $phoneString);
        $phoneNumber  = array_shift($phoneNumbers);
        if (9 === strlen($phoneNumber)) {
            return sprintf('+34%s', $phoneNumber);
        }

        return $phoneNumber;
    }
}
