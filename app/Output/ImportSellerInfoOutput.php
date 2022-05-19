<?php

declare(strict_types=1);

namespace App\Output;

/**
 * @package App\Output
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class ImportSellerInfoOutput
{
    private string $externalId;
    private string $companyName;
    private ?string $vatNumber;
    private string $address;
    private string $zipCode;
    private string $city;
    private string $country;
    private string $emailAddress;
    private string $phoneNumber;
    private ?string $logoUrl;
    private ?string $whatsappNumber;
    private ?string $latitude;
    private ?string $longitude;

    public function __construct(
        string $externalId,
        string $companyName,
        ?string $vatNumber,
        string $address,
        string $zipCode,
        string $city,
        string $country,
        string $emailAddress,
        string $phoneNumber,
        ?string $logoUrl = null,
        ?string $whatsappNumber = null,
        ?string $latitude = null,
        ?string $longitude = null,
    ) {
        $this->externalId     = $externalId;
        $this->companyName    = $companyName;
        $this->vatNumber      = $vatNumber;
        $this->address        = $address;
        $this->zipCode        = $zipCode;
        $this->city           = $city;
        $this->country        = $country;
        $this->logoUrl        = $logoUrl;
        $this->emailAddress   = $emailAddress;
        $this->phoneNumber    = $phoneNumber;
        $this->whatsappNumber = $whatsappNumber;
        $this->latitude       = $latitude;
        $this->longitude      = $longitude;
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
     * Get the value of the companyName property.
     *
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * Get the value of the vatNumber property.
     *
     * @return string|null
     */
    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * Get the value of the address property.
     *
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * Get the value of the zipCode property.
     *
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * Get the value of the city property.
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Get the value of the country property.
     *
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * Get the value of the emailAddress property.
     *
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * Get the value of the phoneNumber property.
     *
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * Get the value of the logoUrl property.
     *
     * @return string|null
     */
    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    /**
     * Get the value of the whatsappNumber property.
     *
     * @return string|null
     */
    public function getWhatsappNumber(): ?string
    {
        return $this->whatsappNumber;
    }

    /**
     * Get the value of the latitude property.
     *
     * @return string|null
     */
    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    /**
     * Get the value of the longitude property.
     *
     * @return string|null
     */
    public function getLongitude(): ?string
    {
        return $this->longitude;
    }
}
