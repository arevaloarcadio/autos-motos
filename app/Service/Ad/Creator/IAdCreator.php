<?php
declare(strict_types=1);

namespace App\Service\Ad\Creator;

use App\Models\Ad;
use Illuminate\View\View;

/**
 * @package App\Service\Ad\Creator
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
interface IAdCreator
{
    public function supports(string $adType): bool;
    
    public function presentForm(): View;
    
    public function getInputKey(): string;
    
    public function create(array $input): Ad;
}
