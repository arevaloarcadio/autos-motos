<?php

declare(strict_types=1);

namespace App\Output;

/**
 * @package App\Output
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class ImportAdImageOutput
{
    private string $url;
    private string $extension;

    public function __construct(string $url, string $extension)
    {
        $this->url       = $url;
        $this->extension = $extension;
    }

    /**
     * Get the value of the url property.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get the value of the extension property.
     *
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }
}
