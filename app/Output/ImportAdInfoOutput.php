<?php

declare(strict_types=1);

namespace App\Output;

use App\Models\CarBodyType;
use App\Models\CarFuelType;
use App\Models\CarTransmissionType;
use App\Models\Make;
use App\Models\Models;
use Carbon\Carbon;

/**
 * @package App\Output
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class ImportAdInfoOutput
{
    private string $externalId;
    private string $title;
    private string $description;
    private Make $make;
    private Models $model;
    private float $price;
    private bool $priceContainsVat;
    private string $color;
    private int $mileage;
    private string $condition;
    private ?Carbon $registrationDate;
    private array $images;
    private ?string $additionalVehicleInfo;
    private ?CarTransmissionType $transmissionType;
    private ?CarBodyType $bodyType;
    private ?CarFuelType $fuelType;
    private ?int $doors;
    private ?int $seats;
    private ?int $engineDisplacement;
    private ?int $powerHp;
    private ?float $co2Emissions;
    private ?int $owners;
    private ?Carbon $lastModified;

    public function __construct(
        string $externalId,
        string $title,
        string $description,
        Make $make,
        Models $model,
        float $price,
        string $color,
        int $mileage,
        string $condition,
        ?Carbon $registrationDate = null,
        bool $priceContainsVat = false,
        array $images = [],
        ?string $additionalVehicleInfo = null,
        ?CarTransmissionType $transmissionType = null,
        ?CarBodyType $bodyType = null,
        ?CarFuelType $fuelType = null,
        ?int $doors = null,
        ?int $seats = null,
        ?int $engineDisplacement = null,
        ?int $powerHp = null,
        ?float $co2Emissions = null,
        ?int $owners = null,
        ?Carbon $lastModified = null
    ) {
        $this->externalId            = $externalId;
        $this->title                 = $title;
        $this->description           = $description;
        $this->make                  = $make;
        $this->model                 = $model;
        $this->price                 = $price;
        $this->color                 = $color;
        $this->mileage               = $mileage;
        $this->condition             = $condition;
        $this->registrationDate      = $registrationDate;
        $this->priceContainsVat      = $priceContainsVat;
        $this->images                = $images;
        $this->additionalVehicleInfo = $additionalVehicleInfo;
        $this->transmissionType      = $transmissionType;
        $this->bodyType              = $bodyType;
        $this->fuelType              = $fuelType;
        $this->doors                 = $doors;
        $this->seats                 = $seats;
        $this->engineDisplacement    = $engineDisplacement;
        $this->powerHp               = $powerHp;
        $this->co2Emissions          = $co2Emissions;
        $this->owners                = $owners;
        $this->lastModified          = $lastModified;
    }

    /**
     * Get the value of the externalId property.
     *
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
     * Get the value of the title property.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the value of the description property.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the value of the make property.
     *
     * @return Make
     */
    public function getMake(): Make
    {
        return $this->make;
    }

    /**
     * Get the value of the model property.
     *
     * @return Model
     */
    public function getModel(): Models
    {
        return $this->model;
    }

    /**
     * Get the value of the price property.
     *
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Get the value of the priceContainsVat property.
     *
     * @return bool
     */
    public function isPriceContainsVat(): bool
    {
        return $this->priceContainsVat;
    }

    /**
     * Get the value of the color property.
     *
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Get the value of the mileage property.
     *
     * @return int
     */
    public function getMileage(): int
    {
        return $this->mileage;
    }

    /**
     * Get the value of the condition property.
     *
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * Get the value of the registrationDate property.
     *
     * @return Carbon|null
     */
    public function getRegistrationDate(): ?Carbon
    {
        return $this->registrationDate;
    }

    /**
     * Get the value of the images property.
     *
     * @return ImportAdImageOutput[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * Get the value of the additionalVehicleInfo property.
     *
     * @return string|null
     */
    public function getAdditionalVehicleInfo(): ?string
    {
        return $this->additionalVehicleInfo;
    }

    /**
     * Get the value of the transmissionType property.
     *
     * @return CarTransmissionType|null
     */
    public function getTransmissionType(): ?CarTransmissionType
    {
        return $this->transmissionType;
    }

    /**
     * Get the value of the bodyType property.
     *
     * @return CarBodyType|null
     */
    public function getBodyType(): ?CarBodyType
    {
        return $this->bodyType;
    }

    /**
     * Get the value of the fuelType property.
     *
     * @return CarFuelType|null
     */
    public function getFuelType(): ?CarFuelType
    {
        return $this->fuelType;
    }

    /**
     * Get the value of the doors property.
     *
     * @return int|null
     */
    public function getDoors(): ?int
    {
        return $this->doors;
    }

    /**
     * Get the value of the seats property.
     *
     * @return int|null
     */
    public function getSeats(): ?int
    {
        return $this->seats;
    }

    /**
     * Get the value of the engineDisplacement property.
     *
     * @return int|null
     */
    public function getEngineDisplacement(): ?int
    {
        return $this->engineDisplacement;
    }

    /**
     * Get the value of the powerHp property.
     *
     * @return int|null
     */
    public function getPowerHp(): ?int
    {
        return $this->powerHp;
    }

    /**
     * Get the value of the co2Emissions property.
     *
     * @return float|null
     */
    public function getCo2Emissions(): ?float
    {
        return $this->co2Emissions;
    }

    /**
     * Get the value of the owners property.
     *
     * @return int|null
     */
    public function getOwners(): ?int
    {
        return $this->owners;
    }

    /**
     * Get the value of the lastModified property.
     *
     * @return Carbon|null
     */
    public function getLastModified(): ?Carbon
    {
        return $this->lastModified;
    }

}
