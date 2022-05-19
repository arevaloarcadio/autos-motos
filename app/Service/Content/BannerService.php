<?php

declare(strict_types=1);

namespace App\Service\Content;

use App\Models\Banner;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image as ImageFacade;
use Intervention\Image\Image;

/**
 * @package App\Service\Content
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class BannerService
{
    public function create(array $input): Banner
    {
        $input     = $this->validator($input)->validate();
        $imagePath = $this->validateAndUploadImage($input);

        $banner              = new Banner();
        $banner->location    = $input['location'];
        $banner->image_path  = $imagePath;
        $banner->link        = $input['link'] ?? null;
        $banner->order_index = $this->generateOrderIndex($input['location']);

        $banner->save();

        return $banner;
    }

    public function update(string $id, array $input): Banner
    {
        $input = $this->validator($input, false)->validate();
        /** @var Banner $banner */
        $banner       = Banner::query()->findOrFail($id);
        $banner->link = $input['link'] ?? null;

        $input['location'] = $banner->location;
        if (isset($input['image'])) {
            $oldImage           = $banner->image_path;
            $imagePath          = $this->validateAndUploadImage($input);
            $banner->image_path = $imagePath;

            Storage::disk('s3')->delete($oldImage);
        }

        if (isset($input['order_index']) && ! (intval($input['order_index']) === $banner->order_index)) {
            $banner = $this->regenerateOrderIndex($banner, intval($input['order_index']));
        }
        $banner->save();

        return $banner;
    }

    public function delete(Banner $banner): bool
    {
        Storage::disk('s3')->delete($banner->image_path);
        Banner::query()
              ->where('location', '=', $banner->location)
              ->where('order_index', '>', $banner->order_index)
              ->where('id', '!=', $banner->id)
              ->decrement('order_index');

        return $banner->delete();
    }

    private function validateAndUploadImage(array $input): string
    {
        $image      = $this->getOriginalImageData($input['image']);
        $dimensions = [
            'width'  => $image->getWidth(),
            'height' => $image->getHeight(),
        ];
        $this->imageValidator($input['location'], $dimensions)->validate();

        return $this->uploadImage($input['image']);
    }

    private function generateOrderIndex(string $location): int
    {
        $lastBanner = Banner::query()
                            ->where('location', '=', $location)
                            ->orderBy('order_index', 'DESC')
                            ->first(['order_index']);

        if ($lastBanner instanceof Banner) {
            return $lastBanner->order_index + 1;
        }

        return 1;
    }

    private function regenerateOrderIndex(Banner $banner, int $newOrderIndex): Banner
    {
        $totalBanners = Banner::query()
                              ->where('location', '=', $banner->location)
                              ->count();
        if ($newOrderIndex > $totalBanners) {
            $newOrderIndex = $totalBanners;
        }
        if ($newOrderIndex <= 0) {
            $newOrderIndex = 1;
        }

        if ($newOrderIndex < $banner->order_index) {
            Banner::query()
                  ->where('location', '=', $banner->location)
                  ->where('order_index', '>=', $newOrderIndex)
                  ->where('id', '!=', $banner->id)
                  ->increment('order_index');
        }

        if ($newOrderIndex > $banner->order_index) {
            Banner::query()
                  ->where('location', '=', $banner->location)
                  ->where('order_index', '<=', $newOrderIndex)
                  ->where('id', '!=', $banner->id)
                  ->decrement('order_index');
        }


        $banner->order_index = $newOrderIndex;

        return $banner;
    }

    /**
     * @param array $imageData
     *
     * @return string
     */
    private function uploadImage(array $imageData): string
    {
        $fileName          = Carbon::now()->timestamp;
        $originalImageData = (string) $this->getOriginalImageData($imageData);
        $basePath          = 'banners';
        $imageName         = sprintf('%s.%s', $fileName, $imageData['extension']);
        $path              = sprintf('%s/%s', $basePath, $imageName);
        Storage::disk('s3')->put(
            $path,
            $originalImageData,
            'public'
        );

        return $path;
    }

    /**
     * @param array $imageData
     *
     * @return Image
     */
    private function getOriginalImageData(array $imageData): Image
    {
        $imageComponents = explode(',', $imageData['body']);
        $base64Image     = base64_decode(array_pop($imageComponents));

        return ImageFacade::make($base64Image)
                          ->encode($imageData['extension'], 100);
    }

    /**
     * @param array $data
     * @param bool  $isCreate
     *
     * @return ValidatorInterface
     */
    protected function validator(array $data, bool $isCreate = true): ValidatorInterface
    {
        $rules = [
            'image'       => ['nullable'],
            'link'        => ['nullable', 'url'],
            'order_index' => ['nullable', 'integer'],
        ];

        if (true === $isCreate) {
            $rules = [
                'location' => ['required', Rule::in(array_keys(config('banner')))],
                'image'    => ['required'],
                'link'     => ['nullable', 'url'],
            ];
        }

        return Validator::make($data, $rules);
    }

    protected function imageValidator(string $location, array $imageDimensions): ValidatorInterface
    {
        $location = config(sprintf('banner.%s', $location));

        return Validator::make(
            $imageDimensions,
            [
                'width'  => ['required', 'integer', sprintf('size:%d', $location['width'])],
                'height' => ['required', 'integer', sprintf('size:%d', $location['height'])],
            ],
            [
                'width.size'  => __('validation.dimensions_width'),
                'height.size' => __('validation.dimensions_height'),
            ],
            [
                'width'  => 'image',
                'height' => 'image',
            ]
        );
    }
}
