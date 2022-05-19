<?php

declare(strict_types=1);

namespace App\Service\Ad;

use App\Enum\Ad\AdImageVersionTypeEnum;
use App\Enum\Ad\AdTypeEnum;
use App\Models\Ad;
use App\Models\AdImage;
use App\Models\AdImageVersion;
use App\Output\IImageOutput;
use App\Output\ImageUploadOutput;
use App\Output\ImageVersionUploadOutput;
use Aws\S3\Exception\S3Exception;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use Ramsey\Uuid\Uuid;
use function GuzzleHttp\Promise\all;

/**
 * @package App\Service\Ad
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
class AdImageService
{

    public $pathPrefix = 'listings';
    /**
     * @param array $images
     * @param Ad    $ad
     *
     * @return AdImage[]
     */
    public function createAndUploadAdImages(Ad $ad, array $images): array
    {
        $files = $this->prepareImagesForUpload($ad, $images);
        $this->uploadImagesInBulk($files);
        File::deleteDirectory(public_path(sprintf('image-uploads/' . $this->pathPrefix.'/%s', $ad->id)));

        return $this->createAdImagesAndVersions($ad, $files);
    }

    /**
     * @param Ad    $ad
     * @param array $images
     *
     * @return AdImage[]
     */
    public function createExternalImages(Ad $ad, array $images): array
    {
        $output = [];
        foreach ($images as $key => $image) {
            $output[] = $this->createExternalImageForAd($ad, $image['url'], $key);
        }

        return $output;
    }

    /**
     * @param Ad     $ad
     * @param string $url
     * @param int    $index
     *
     * @return AdImage
     */
    private function createExternalImageForAd(Ad $ad, string $url, int $index = 0): AdImage
    {
        $adImage              = new AdImage();
        $adImage->path        = $url;
        $adImage->order_index = $index;
        $adImage->is_external = true;
        $adImage->ad()->associate($ad);

        $adImage->save();

        return $adImage;
    }


    /**
     * @param Ad                  $ad
     * @param ImageUploadOutput[] $imageUploadOutputs
     *
     * @return AdImage[]
     */
    public function createAdImagesAndVersions(Ad $ad, array $imageUploadOutputs): array
    {
        $output = [];
        foreach ($imageUploadOutputs as $index => $imageUpload) {
            $adImage              = new AdImage();
            $adImage->path        = $imageUpload->getPath();
            $adImage->order_index = $index;
            $adImage->ad()->associate($ad);

            $adImage->save();

            foreach ($imageUpload->getVersions() as $version) {
                $imageVersion       = new AdImageVersion();
                $imageVersion->name = $version->getType();
                $imageVersion->path = $version->getPath();
                $imageVersion->adImage()->associate($adImage);

                $imageVersion->save();
            }

            $output[] = $adImage;
        }

        return $output;
    }

    public function updateImages(Ad $ad, array $images): array
    {
        $output = [];

        $imagesToCreate = [];
        foreach ($images as $key => $image) {
            if (isset($image['id'])) {
                /** @var AdImage $adImage */
                $adImage              = AdImage::find($image['id']);
                $adImage->order_index = $key;
                $adImage->save();

                $output[] = $adImage;
                continue;
            }

            if (isset($image['body'])) {
                $imagesToCreate[] = $image;
                continue;
            }
        }

        $files = $this->prepareImagesForUpload($ad, $imagesToCreate);
        $this->uploadImagesInBulk($files);
        File::deleteDirectory(public_path(sprintf('image-uploads/' . $this->pathPrefix.'/%s', $ad->id)));

        $createImagesOutput = $this->createAdImagesAndVersions($ad, $files);

        $output = array_merge($output, $createImagesOutput);

        $imagesIds = collect($output)->pluck('id')->toArray();
        $adImages  = AdImage::query()
                            ->where('ad_id', '=', $ad->id)
                            ->whereNotIn('id', $imagesIds)
                            ->get();
        $this->deleteImages($adImages);


        $ad->thumbnail = $ad->getThumbnailPath();

        return $output;
    }

    public function updateImagesAndSave(Ad $ad, array $images): array
    {
        $output = $this->updateImages($ad, $images);
        $ad->save();

        return $output;
    }

    /**
     * @param Collection $images
     *
     * @throws Exception
     */
    private function deleteImages(Collection $images): void
    {
        $pathsToDelete = [];
        /** @var AdImage $image */
        foreach ($images as $image) {
            $pathsToDelete[] = $image->path;
            /** @var AdImageVersion $imageVersion */
            foreach ($image->versions as $imageVersion) {
                $pathsToDelete[] = $imageVersion->path;
            }

            $image->delete();
        }

        Storage::disk('s3')->delete($pathsToDelete);
    }

    /**
     * @param string $imageBody
     *
     * @return string
     */
    private function getOriginalImage(string $imageBody): string
    {
        $imageComponents = explode(',', $imageBody);

        return base64_decode(array_pop($imageComponents));
    }

    /**
     * @param ImageUploadOutput[] $files
     */
    private function uploadImagesInBulk(array $files)
    {
        $s3Client = new S3Client(
            [
                'region'  => env('AWS_DEFAULT_REGION'),
                'version' => 'latest',
            ]
        );
        $promises = [];

        foreach ($files as $imageUpload) {
            $promises[] = $this->generateMultipartUploader($s3Client, $imageUpload)->promise();

            foreach ($imageUpload->getVersions() as $imageVersionUpload) {
                $promises[] = $this->generateMultipartUploader($s3Client, $imageVersionUpload)->promise();
            }
        }

        $aggregate = all($promises);

        //        try {
        $result = $aggregate->wait();
        //        } catch (S3Exception $e) {
        // Handle the error
        // echo $e->getMessage();
        //        }
    }

    private function generateMultipartUploader(S3Client $s3Client, IImageOutput $imageOutput): MultipartUploader
    {
        return new MultipartUploader(
            $s3Client,
            public_path(sprintf('image-uploads/%s', $imageOutput->getPath())),
            [
                'bucket'      => env('AWS_BUCKET'),
                'key'         => $imageOutput->getPath(),
                'concurrency' => 25,
                'acl'         => 'public-read',
            ]
        );
    }

    /**
     * @param Ad    $ad
     * @param array $images
     *
     * @return ImageUploadOutput[]
     */
    private function prepareImagesForUpload(Ad $ad, array $images): array
    {
        $output = [];
        foreach ($images as $key => $image) {
            if (in_array($ad->type, [AdTypeEnum::MECHANIC_SLUG, AdTypeEnum::RENTAL_SLUG])) {
                $output[] = $this->createCompanyImages($ad, $image);
                continue;
            }
            $output[] = $this->createNormalImages($ad, $image);
        }

        return $output;
    }

    private function createNormalImages(Ad $ad, array $image): ImageUploadOutput
    {
        $uniqueIdentifier  = Uuid::uuid4()->toString();
        $originalImageData = $this->getOriginalImage($image['body']);
        $basePath          = sprintf('%s/%s/%s',$this->pathPrefix, $ad->id, $uniqueIdentifier);
        $originalImagePath = sprintf('%s/%s.%s', $basePath, $uniqueIdentifier, $image['extension']);

        File::makeDirectory(public_path(sprintf('image-uploads/%s', $basePath)), 0775, true);
        Image::make($originalImageData)
             ->save(public_path(sprintf('image-uploads/%s', $originalImagePath)), 80, $image['extension']);

        $versions        = [];
        $versionBasePath = sprintf('%s/versions', $basePath);
        File::makeDirectory(public_path(sprintf('image-uploads/%s', $versionBasePath)), 0775, true);
        foreach (AdImageVersionTypeEnum::getTypes() as $type => $resolutionMetadata) {
            $versionName = sprintf('%s-%s.%s', $uniqueIdentifier, $type, 'jpg');
            $versionPath = sprintf('%s/%s', $versionBasePath, $versionName);

            $watermark = Image::make(public_path('/images/watermark.png'))
                              ->resize(
                                  $resolutionMetadata['width'] / 6,
                                  null,
                                  function (Constraint $constraint) {
                                      $constraint->aspectRatio();
                                  }
                              );
            Image::make($originalImageData)
                 ->fit(
                     $resolutionMetadata['width'],
                     $resolutionMetadata['height'],
                     function (Constraint $constraint) {
                         $constraint->aspectRatio();
                     }
                 )
                 ->insert($watermark, 'bottom-right')
                 ->save(public_path(sprintf('image-uploads/%s', $versionPath)), 80, 'jpg');


            $versions[] = new ImageVersionUploadOutput($versionPath, $type);
        }

        return new ImageUploadOutput($originalImagePath, $versions);
    }

    private function createCompanyImages(Ad $ad, array $image): ImageUploadOutput
    {
        $uniqueIdentifier  = Uuid::uuid4()->toString();
        $originalImageData = $this->getOriginalImage($image['body']);
        $basePath          = sprintf('%s/%s/%s', $this->pathPrefix, $ad->id, $uniqueIdentifier);
        $originalImagePath = sprintf('%s/%s.%s', $basePath, $uniqueIdentifier, $image['extension']);

        File::makeDirectory(public_path(sprintf('image-uploads/%s', $basePath)), 0775, true);

        $resizedOriginalImage = Image::make($originalImageData)
                                     ->resize(
                                         AdImageVersionTypeEnum::COMPANY_IMAGE_WIDTH,
                                         AdImageVersionTypeEnum::COMPANY_IMAGE_HEIGHT,
                                         function (Constraint $constraint) {
                                             $constraint->aspectRatio();
                                         }
                                     );

        $originalImage = Image::canvas(
            AdImageVersionTypeEnum::THUMBNAIL_MAX_WIDTH,
            AdImageVersionTypeEnum::THUMBNAIL_MAX_HEIGHT,
            '#ffffff'
        )->insert($resizedOriginalImage, 'center')->save(
            public_path(sprintf('image-uploads/%s', $originalImagePath)),
            80,
            $image['extension']
        );

        $versions        = [];
        $versionBasePath = sprintf('%s/versions', $basePath);
        File::makeDirectory(public_path(sprintf('image-uploads/%s', $versionBasePath)), 0775, true);

        $type               = AdImageVersionTypeEnum::THUMBNAIL;
        $resolutionMetadata = AdImageVersionTypeEnum::getMetadataByType($type);
        $versionName        = sprintf('%s-%s.%s', $uniqueIdentifier, $type, 'jpg');
        $versionPath        = sprintf('%s/%s', $versionBasePath, $versionName);

        Image::make($originalImage)
             ->fit(
                 $resolutionMetadata['width'],
                 $resolutionMetadata['height'],
                 function (Constraint $constraint) {
                     $constraint->aspectRatio();
                 }
             )
             ->save(public_path(sprintf('image-uploads/%s', $versionPath)), 80, 'jpg');


        $versions[] = new ImageVersionUploadOutput($versionPath, $type);

        return new ImageUploadOutput($originalImagePath, $versions);
    }
}
