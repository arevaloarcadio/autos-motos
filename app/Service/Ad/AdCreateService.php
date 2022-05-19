<?php

declare(strict_types=1);

namespace App\Service\Ad;

use App\Enum\Ad\AdSourceEnum;
use App\Enum\Ad\ImageProcessingStatusEnum;
use App\Enum\Core\ApprovalStatusEnum;
use App\Enum\User\RoleEnum;
use App\Models\Ad;
use App\Models\AdImage;
use App\Models\User;
use App\Service\Ad\Validator\AdValidator;
use App\Service\Market\MarketStorageFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * @package App\Service\Ad
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdCreateService
{
    /**
     * @var AdValidator
     */
    private $validator;

    /**
     * @var AdImageService
     */
    private $imageService;

    public function __construct(AdValidator $validator, AdImageService $imageService)
    {
        $this->validator    = $validator;
        $this->imageService = $imageService;
    }

    public function create(array $input): Ad
    {
        $input = $this->addUserRelatedData($input);
        $input = $this->validator->validate($input);

        $slug = isset($input['external_id']) ?
            Str::slug(sprintf('%s %s %s', $input['title'], $input['external_id'], time())) :
            Str::slug(sprintf('%s %s', $input['title'], time()));

        $ad                           = new Ad();
        $ad->slug                     = $slug;
        $ad->title                    = $input['title'];
        $ad->description              = $input['description'];
        $ad->type                     = $input['type'];
        $ad->market_id                = $input['market_id'];
        $ad->user_id                  = $input['user_id'] ?? null;
        $ad->status                   = $input['status'] ?? ApprovalStatusEnum::PENDING_APPROVAL;
        $ad->external_id              = $input['external_id'] ?? null;
        $ad->source                   = $input['source'] ?? AdSourceEnum::PORTAL;
        $ad->images_processing_status = isset($input['images_processing_status']) ?
            $input['images_processing_status'] : ImageProcessingStatusEnum::NOT_AVAILABLE;
        $ad->save();

        $this->createImages($ad, $input['images'] ?? []);

        $ad->thumbnail = $ad->getThumbnailPath();
        $ad->save();

        return $ad;
    }

    /**
     * @param Ad    $ad
     * @param array $images
     *
     * @return AdImage[]
     */
    private function createImages(Ad $ad, array $images): array
    {
        if ( ! (null === $ad->external_id) && $ad->images_processing_status === ImageProcessingStatusEnum::PENDING) {
            return $this->imageService->createExternalImages($ad, $images);
        }

        return $this->imageService->createAndUploadAdImages($ad, $images);
    }

    private function addUserRelatedData(array $input): array
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (false === $currentUser->hasRole(RoleEnum::ADMIN)) {
            $input['user_id'] = $currentUser->id;
        }

        $input['market_id'] = $input['market_id'] ?? MarketStorageFacade::getMarket()->id;

        return $input;
    }
}
