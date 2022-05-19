<?php

declare(strict_types=1);

namespace App\Output;

/**
 * @package App\Output
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
interface IImageOutput
{
    public function getPath(): string;
}
