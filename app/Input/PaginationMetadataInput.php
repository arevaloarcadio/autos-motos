<?php
declare(strict_types=1);

namespace App\Input;

use App\Enum\PaginationMetadataDefaultsEnum;
use Illuminate\Http\Request;

/**
 * Defines the modelling of a pagination metadata input.
 *
 * @package App\Input
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class PaginationMetadataInput
{
    /**
     * @var int
     */
    private $itemsPerPage;
    
    /**
     * @var int
     */
    private $page;
    
    /**
     * @var string|null
     */
    private $orderBy;
    
    /**
     * @var string|null
     */
    private $orderDir;
    
    /**
     * PaginationMetadataInput constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->itemsPerPage = intval($request->get('itemsPerPage', PaginationMetadataDefaultsEnum::ITEMS_PER_PAGE));
        $this->page         = intval($request->get('page', 1));
        $this->orderBy      = $request->get('orderBy', PaginationMetadataDefaultsEnum::ORDER_BY_COLUMN);
        $this->orderDir     = $request->get('orderDir', PaginationMetadataDefaultsEnum::ORDER_BY_DIR);
    }
    
    /**
     * Get the value of the itemsPerPage property.
     *
     * @return int
     */
    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }
    
    /**
     * Get the value of the page property.
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }
    
    /**
     * Get the value of the orderBy property.
     *
     * @return string|null
     */
    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }
    
    /**
     * Get the value of the orderDir property.
     *
     * @return string|null
     */
    public function getOrderDir(): ?string
    {
        return $this->orderDir;
    }
}
