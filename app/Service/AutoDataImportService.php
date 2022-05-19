<?php
declare(strict_types=1);

namespace App\Service;

use App\Console\Commands\ImportCarData;
use App\Manager\CarAd\CarBodyTypeManager;
use App\Manager\CarAd\CarFuelTypeManager;
use App\Manager\CarAd\CarGenerationManager;
use App\Manager\CarAd\CarMakeManager;
use App\Manager\CarAd\CarModelManager;
use App\Manager\CarAd\CarSpecManager;
use App\Manager\CarAd\CarTransmissionTypeManager;
use App\Manager\CarAd\CarWheelDriveTypeManager;
use App\Models\CarBodyType;
use App\Models\CarFuelType;
use App\Models\CarGeneration;
use App\Models\CarMake;
use App\Models\CarModel;
use App\Models\CarSpec;
use App\Models\CarTransmissionType;
use App\Models\CarWheelDriveType;
use App\XMLParser\Output\XMLNode;
use App\XMLParser\XMLParser;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @package App\Service
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class AutoDataImportService
{
    /**
     * @var XMLParser
     */
    private $xmlParser;
    
    /**
     * @var CarMakeManager
     */
    private $carMakeManager;
    
    /**
     * @var CarModelManager
     */
    private $carModelManager;
    
    /**
     * @var CarGenerationManager
     */
    private $carGenerationManager;
    
    /**
     * @var CarSpecManager
     */
    private $carSpecManager;
    
    /**
     * @var CarBodyTypeManager
     */
    private $carBodyTypeManager;
    
    /**
     * @var CarFuelTypeManager
     */
    private $carFuelTypeManager;
    
    /**
     * @var CarTransmissionTypeManager
     */
    private $carTransmissionTypeManager;
    
    /**
     * @var CarWheelDriveTypeManager
     */
    private $carWheelDriveTypeManager;
    
    /**
     * @var ImportCarData
     */
    private $command;
    
    /**
     * AutoDataImportService constructor.
     *
     * @param XMLParser                  $xmlParser
     * @param CarMakeManager             $carMakeManager
     * @param CarModelManager            $carModelManager
     * @param CarGenerationManager       $carGenerationManager
     * @param CarSpecManager             $carSpecManager
     * @param CarBodyTypeManager         $carBodyTypeManager
     * @param CarFuelTypeManager         $carFuelTypeManager
     * @param CarTransmissionTypeManager $carTransmissionTypeManager
     * @param CarWheelDriveTypeManager   $carWheelDriveTypeManager
     */
    public function __construct(
        XMLParser $xmlParser,
        CarMakeManager $carMakeManager,
        CarModelManager $carModelManager,
        CarGenerationManager $carGenerationManager,
        CarSpecManager $carSpecManager,
        CarBodyTypeManager $carBodyTypeManager,
        CarFuelTypeManager $carFuelTypeManager,
        CarTransmissionTypeManager $carTransmissionTypeManager,
        CarWheelDriveTypeManager $carWheelDriveTypeManager
    ) {
        $this->xmlParser                  = $xmlParser;
        $this->carMakeManager             = $carMakeManager;
        $this->carModelManager            = $carModelManager;
        $this->carGenerationManager       = $carGenerationManager;
        $this->carSpecManager             = $carSpecManager;
        $this->carBodyTypeManager         = $carBodyTypeManager;
        $this->carFuelTypeManager         = $carFuelTypeManager;
        $this->carTransmissionTypeManager = $carTransmissionTypeManager;
        $this->carWheelDriveTypeManager   = $carWheelDriveTypeManager;
    }
    
    public function execute(ImportCarData $command)
    {
        $this->command = $command;
        $this->xmlParser->open(Storage::disk('s3')->url('imports/data.xml'));
        $this->xmlParser->loop(
            'brand',
            function (XMLNode $brandNode) {
                $this->processBrands($brandNode);
                unset($brandNode);
            }
        );
        
        $this->xmlParser->close();
    }
    
    /**
     * @param XMLNode $brandNode
     */
    private function processBrands(XMLNode $brandNode): void
    {
        $brandIdNode     = $this->xmlParser->getChildNode($brandNode, 'id');
        $brandNameNode   = $this->xmlParser->getChildNode($brandNode, 'name');
        $brandModelsNode = $this->xmlParser->getChildNode($brandNode, 'models');
        
        $this->outputProgress(sprintf("\nProcessing make %s", $brandNameNode->getContent()));
        
        $make = $this->carMakeManager->findOneBy(['external_id' => $brandIdNode->getContent()]);
        if (null === $make) {
            $make              = new CarMake();
            $make->name        = $brandNameNode->getContent();
            $make->external_id = $brandIdNode->getContent();
            /** @var CarMake $make */
            $make = $this->carMakeManager->save($make);
        }
        $this->xmlParser->loopXml(
            $brandModelsNode->getOuterXml(),
            'model',
            function (XMLNode $modelNode) use ($make) {
                $this->processModel($modelNode, $make);
                unset($modelNode);
            }
        );
    }
    
    /**
     * @param XMLNode $modelNode
     * @param CarMake $make
     */
    private function processModel(XMLNode $modelNode, CarMake $make): void
    {
        $modelIdNode          = $this->xmlParser->getChildNode($modelNode, 'id');
        $modelNameNode        = $this->xmlParser->getChildNode($modelNode, 'name');
        $modelGenerationsNode = $this->xmlParser->getChildNode($modelNode, 'generations');
        
        $this->outputProgress(sprintf('Processing model %s', $modelNameNode->getContent()));
        
        $model = $this->carModelManager->findOneBy(['external_id' => $modelIdNode->getContent()]);
        if (null === $model) {
            $model              = new CarModel();
            $model->name        = $modelNameNode->getContent();
            $model->external_id = $modelIdNode->getContent();
            $model->make()->associate($make);
            $model = $this->saveModel($model);
        }
        
        $this->xmlParser->loopXml(
            $modelGenerationsNode->getOuterXml(),
            'generation',
            function (XMLNode $generationNode) use ($model) {
                $this->processGeneration($generationNode, $model);
                unset($generationNode);
            }
        );
    }
    
    /**
     * @param CarModel $model
     * @param int      $attempts
     *
     * @return CarModel
     */
    private function saveModel(CarModel $model, int $attempts = 1): CarModel
    {
        try {
            /** @var CarModel $model */
            $model = $this->carModelManager->save($model);
        } catch (QueryException $exception) {
            $errorCode = $exception->errorInfo[1];
            if ($errorCode == 1062) {
                $model->slug = Str::slug(sprintf('%s %d', $model->name, $attempts));
                $this->saveModel($model, ++$attempts);
            }
        }
        
        return $model;
    }
    
    /**
     * @param XMLNode  $generationNode
     * @param CarModel $model
     */
    private function processGeneration(XMLNode $generationNode, CarModel $model): void
    {
        $generationIdNode            = $this->xmlParser->getChildNode($generationNode, 'id');
        $generationNameNode          = $this->xmlParser->getChildNode($generationNode, 'name');
        $generationModelYearNode     = $this->xmlParser->getChildNode($generationNode, 'modelYear');
        $generationModificationsNode = $this->xmlParser->getChildNode($generationNode, 'modifications');
        
        $this->outputProgress(sprintf('Processing generation %s', $generationNameNode->getContent()));
        
        $generation = $this->carGenerationManager->findOneBy(
            ['external_id' => $generationIdNode->getContent()]
        );
        if (null === $generation) {
            $generation              = new CarGeneration();
            $generation->name        = $generationNameNode->getContent();
            $generation->year        = $generationModelYearNode->getContent();
            $generation->external_id = $generationIdNode->getContent();
            $generation->model()->associate($model);
            /** @var CarGeneration $generation */
            $generation = $this->carGenerationManager->save($generation);
        }
        $this->xmlParser->loopXml(
            $generationModificationsNode->getOuterXml(),
            'modification',
            function (XMLNode $modificationNode) use ($generation) {
                $this->processModification($modificationNode, $generation);
                unset($modificationNode);
            }
        );
    }
    
    /**
     * @param XMLNode       $modificationNode
     * @param CarGeneration $generation
     */
    private function processModification(XMLNode $modificationNode, CarGeneration $generation): void
    {
        $modificationData = $this->xmlParser->getArrayFromNode($modificationNode);
        $id               = intval($modificationData['id']);
        /** @var CarSpec $spec */
        $spec             = $this->carSpecManager->findOneBy(['external_id' => $id]);
        if (null === $spec) {
            $spec = new CarSpec();
            $spec->make()->associate($generation->model->make);
            $spec->model()->associate($generation->model);
            $spec->generation()->associate($generation);
            $spec->external_id = $id;
            if (isset($modificationData['coupe'])) {
                $bodyTypeName         = $modificationData['coupe'];
                $bodyTypeInternalName = Str::slug($bodyTypeName, '_');
                $bodyTypeSlug         = Str::slug($bodyTypeName, '-');
                $bodyType             = $this->carBodyTypeManager->findOneBy(
                    ['internal_name' => $bodyTypeInternalName]
                );
                if (null === $bodyType) {
                    $bodyType                = new CarBodyType();
                    $bodyType->internal_name = $bodyTypeInternalName;
                    $bodyType->slug          = $bodyTypeSlug;
                    $bodyType->icon_url      = sprintf('icons/%s.png', $bodyTypeSlug);
                    $bodyType->external_name = $bodyTypeName;
                    /** @var CarBodyType $bodyType */
                    $bodyType = $this->carBodyTypeManager->save($bodyType);
                }
                $spec->bodyType()->associate($bodyType);
            }
            $spec->engine = $modificationData['engine'];
            $this->outputProgress(sprintf('Processing spec %s', $spec->engine));
            
            $spec->doors               = $modificationData['doors'] ?? null;
            $spec->doors_min           = isset($modificationData['doorsMin']) ? intval(
                $modificationData['doorsMin']
            ) : null;
            $spec->doors_max           = isset($modificationData['doorsMax']) ? intval(
                $modificationData['doorsMax']
            ) : null;
            $spec->power_hp            = isset($modificationData['powerRpm']) ? intval(
                $modificationData['powerHp']
            ) : null;
            $spec->power_rpm           = $modificationData['powerRpm'] ?? null;
            $spec->power_rpm_min       = isset($modificationData['powerRpmLow']) ? intval(
                $modificationData['powerRpmLow']
            ) : null;
            $spec->power_rpm_max       = isset($modificationData['powerRpmHigh']) ? intval(
                $modificationData['powerRpmHigh']
            ) : null;
            $spec->engine_displacement = isset($modificationData['engineDisplacement']) ? intval(
                $modificationData['engineDisplacement']
            ) : null;
            if (isset($modificationData['yearstart'])) {
                $spec->production_start_year = intval($modificationData['yearstart']);
                if ($spec->production_start_year < 1901) {
                    $spec->production_start_year = 1901;
                }
            }
            
            if (isset($modificationData['yearstop'])) {
                $spec->production_end_year = intval($modificationData['yearstop']);
                if ($spec->production_end_year < 1901) {
                    $spec->production_end_year = 1901;
                }
            }
            $spec->last_external_update = Carbon::parse($modificationData['update']);
            
            $fuelTypeName         = $modificationData['fuel'];
            $fuelTypeInternalName = Str::slug($fuelTypeName, '_');
            $fuelType             = $this->carFuelTypeManager->findOneBy(['internal_name' => $fuelTypeInternalName]);
            if (null === $fuelType) {
                $fuelType                = new CarFuelType();
                $fuelType->internal_name = $fuelTypeInternalName;
                $fuelType->external_name = $fuelTypeName;
                /** @var CarFuelType $fuelType */
                $fuelType = $this->carFuelTypeManager->save($fuelType);
            }
            $spec->fuelType()->associate($fuelType);
            
            $transmissionType = $this->getTransmissionType($modificationData);
            $spec->transmissionType()->associate($transmissionType);
            
            $spec->gears = $this->getGearsCount($modificationData);
            
            if (isset($modificationData['drive'])) {
                $driveTypeName         = $modificationData['drive'];
                $driveTypeInternalName = Str::slug($driveTypeName, '_');
                $driveType             = $this->carWheelDriveTypeManager->findOneBy(
                    ['internal_name' => $driveTypeInternalName]
                );
                if (null === $driveType) {
                    $driveType                = new CarWheelDriveType();
                    $driveType->internal_name = $driveTypeInternalName;
                    $driveType->external_name = $driveTypeName;
                    /** @var CarWheelDriveType $driveType */
                    $driveType = $this->carWheelDriveTypeManager->save($driveType);
                }
                $spec->wheelDriveType()->associate($driveType);
            }
            if (array_key_exists('electricMotorPowerHp', $modificationData)) {
                $spec->electric_power_hp = $modificationData['electricMotorPowerHp'];
            }
            
            if (array_key_exists('batteryCapacity', $modificationData)) {
                $spec->battery_capacity = $modificationData['batteryCapacity'];
            }
            
            if (array_key_exists('electricMotorPowerRpm', $modificationData)) {
                $spec->electric_power_rpm = $modificationData['electricMotorPowerRpm'];
            }
            
            if (array_key_exists('electricMotorPowerRpmLow', $modificationData)) {
                $spec->electric_power_rpm_min = intval($modificationData['electricMotorPowerRpmLow']);
            }
            
            if (array_key_exists('electricMotorPowerRpmHigh', $modificationData)) {
                $spec->electric_power_rpm_max = intval($modificationData['electricMotorPowerRpmHigh']);
            }
            
            $this->carSpecManager->save($spec);
            
            return;
        }
        
        $spec->engine_displacement = isset($modificationData['engineDisplacement']) ? intval(
            $modificationData['engineDisplacement']
        ) : null;
        $this->carSpecManager->save($spec);
    }
    
    /**
     * @param array $data
     *
     * @return CarTransmissionType
     */
    private function getTransmissionType(array $data): CarTransmissionType
    {
        $transmissionTypeName         = $this->getTransmissionName($data);
        $transmissionTypeInternalName = Str::slug($transmissionTypeName, '_');
        $transmissionType             = $this->carTransmissionTypeManager->findOneBy(
            ['internal_name' => $transmissionTypeInternalName]
        );
        
        if (null === $transmissionType) {
            $transmissionType                = new CarTransmissionType();
            $transmissionType->internal_name = $transmissionTypeInternalName;
            $transmissionType->external_name = $transmissionTypeName;
            /** @var CarTransmissionType $transmissionType */
            $transmissionType = $this->carTransmissionTypeManager->save($transmissionType);
        }
        
        return $transmissionType;
    }
    
    /**
     * @param array $data
     *
     * @return string
     */
    private function getTransmissionName(array $data): string
    {
        if (array_key_exists('gearboxAT', $data)) {
            return 'Automatic';
        }
        
        return 'Manual';
    }
    
    /**
     * @param array $data
     *
     * @return int|null
     */
    private function getGearsCount(array $data): ?int
    {
        if (array_key_exists('gearboxMT', $data)) {
            return intval($data['gearboxMT']);
        }
        
        if (array_key_exists('gearboxAT', $data)) {
            return intval($data['gearboxAT']);
        }
        
        return null;
    }
    
    /**
     * @param string $message
     */
    private function outputProgress(string $message): void
    {
        $this->command->info(
            sprintf(
                '%s - RAM Used: %s',
                $message,
                $this->getMemoryUsed()
            )
        );
    }
    
    private function getMemoryUsed(): string
    {
        return sprintf("%sMB", intval(memory_get_usage(true) / 1024 / 1024));
    }
}
