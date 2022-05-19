<?php

declare(strict_types=1);

namespace App\Service\Data;

use App\Console\Commands\Data\ImportAutoData;
use App\Service\HasCommandOutputs;

/**
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AutoImportService
{
    use HasCommandOutputs;

    /**
     * @var MakeImportService
     */
    private $makeImportService;

    /**
     * @var ModelImportService
     */
    private $modelImportService;

    /**
     * @var GenerationImportService
     */
    private $generationImportService;

    /**
     * @var SeriesImportService
     */
    private $seriesImportService;

    /**
     * @var TrimImportService
     */
    private $trimImportService;

    /**
     * @var SpecificationImportService
     */
    private $specificationImportService;

    /**
     * @var SpecificationValueImportService
     */
    private $specificationValueImportService;

    /**
     * @var OptionImportService
     */
    private $optionImportService;
    /**
     * @var EquipmentImportService
     */
    private $equipmentImportService;
    /**
     * @var EquipmentOptionImportService
     */
    private $equipmentOptionImportService;

    public function __construct(
        MakeImportService $makeImportService,
        ModelImportService $modelImportService,
        GenerationImportService $generationImportService,
        SeriesImportService $seriesImportService,
        TrimImportService $trimImportService,
        SpecificationImportService $specificationImportService,
        SpecificationValueImportService $specificationValueImportService,
        OptionImportService $optionImportService,
        EquipmentImportService $equipmentImportService,
        EquipmentOptionImportService $equipmentOptionImportService
    ) {
        $this->modelImportService              = $modelImportService;
        $this->generationImportService         = $generationImportService;
        $this->makeImportService               = $makeImportService;
        $this->seriesImportService             = $seriesImportService;
        $this->trimImportService               = $trimImportService;
        $this->specificationImportService      = $specificationImportService;
        $this->specificationValueImportService = $specificationValueImportService;
        $this->optionImportService             = $optionImportService;
        $this->equipmentImportService          = $equipmentImportService;
        $this->equipmentOptionImportService    = $equipmentOptionImportService;
    }

    public function import(ImportAutoData $command): void
    {
        $this->outputProgress($command, 'Importing makes...');
        $this->makeImportService->execute();
        $this->outputProgress($command, 'DONE.');

        $this->outputProgress($command, 'Importing models...');
        $this->modelImportService->execute();
        $this->outputProgress($command, 'DONE.');

        $this->outputProgress($command, 'Importing generations...');
        $this->generationImportService->execute();
        $this->outputProgress($command, 'DONE.');

        $this->outputProgress($command, 'Importing series...');
        $this->seriesImportService->execute();
        $this->outputProgress($command, 'DONE.');

        $this->outputProgress($command, 'Importing trims...');
        $this->trimImportService->execute();
        $this->outputProgress($command, 'DONE.');

        $this->outputProgress($command, 'Importing specifications...');
        $this->specificationImportService->execute();
        $this->outputProgress($command, 'DONE.');

        $this->outputProgress($command, 'Importing specification values...');
        $this->specificationValueImportService->execute();
        $this->outputProgress($command, 'DONE.');

        $this->outputProgress($command, 'Importing options...');
        $this->optionImportService->execute();
        $this->outputProgress($command, 'DONE.');

        $this->outputProgress($command, 'Importing equipments...');
        $this->equipmentImportService->execute();
        $this->outputProgress($command, 'DONE.');

        $this->outputProgress($command, 'Importing equipment options...');
        $this->equipmentOptionImportService->execute();
        $this->outputProgress($command, 'DONE.');
    }
}
