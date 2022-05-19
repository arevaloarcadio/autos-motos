<?php
declare(strict_types=1);

namespace App\Service\Dealer;

use App\Enum\Ad\AdSourceEnum;
use App\Enum\Core\ApprovalStatusEnum;
use App\Enum\User\RoleEnum;
use App\Manager\Dealer\DealerManager;
use App\Manager\Market\MarketManager;
use App\Models\Dealer;
use App\Models\Market;
use App\Models\User;
use App\Service\User\UserCreateService;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;

/**
 * @package App\Service\Dealer
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class DealerService
{
    private const IMAGE_WIDTH     = 500;
    private const IMAGE_EXTENSION = 'png';

    /**
     * @var DealerManager
     */
    private $dealerManager;

    /**
     * @var UserCreateService
     */
    private $userCreateService;

    /**
     * @var MarketManager
     */
    private $marketManager;

    /**
     * @var DealerShowRoomService
     */
    private $dealerShowRoomService;

    /**
     * DealerCreateService constructor.
     *
     * @param DealerManager         $dealerManager
     * @param DealerShowRoomService $dealerShowRoomService
     * @param UserCreateService     $userCreateService
     * @param MarketManager         $marketManager
     */
    public function __construct(
        DealerManager $dealerManager,
        DealerShowRoomService $dealerShowRoomService,
        UserCreateService $userCreateService,
        MarketManager $marketManager
    ) {
        $this->dealerManager         = $dealerManager;
        $this->userCreateService     = $userCreateService;
        $this->marketManager         = $marketManager;
        $this->dealerShowRoomService = $dealerShowRoomService;
    }

    public function create(array $input, ?UploadedFile $logo = null): Dealer
    {
        $input         = $this->validator($input)->validate();
        $input['slug'] = Str::slug($input['company_name']);
        if ($logo instanceof UploadedFile) {
            $input['logo'] = [
                'body' => base64_encode(file_get_contents($logo->path())),
            ];
        }
        if (isset($input['logo'])) {
            $input['logo_path'] = $this->uploadLogo($input['logo'], $input['slug']);
            unset($input['logo']);
        }
        $input['source'] = isset($input['source']) ? $input['source'] : AdSourceEnum::PORTAL;

        $dealer         = new Dealer($input);
        $dealer->status = ApprovalStatusEnum::APPROVED;
        /** @var Dealer $dealer */
        $dealer = $this->dealerManager->save($dealer);

        return $dealer;
    }

    /**
     * @param array             $input
     * @param UploadedFile|null $logo
     *
     * @return Dealer
     */
    public function createAccount(array $input, ?UploadedFile $logo = null): Dealer
    {
        $input['slug'] = isset($input['company_name']) ? Str::slug($input['company_name']) : null;
        $input         = $this->validator($input, true)->validate();
        $showRoomInput = $input['show_room'];
        $userInput     = $input['user'];
        unset($input['show_room']);
        unset($input['user']);
        /** @var Market $market */
        $market                   = $this->marketManager->findOne($showRoomInput['market_id']);
        $showRoomInput['country'] = $market->name;

        $dealer = $this->create($input, $logo);

        $showRoomInput['dealer_id'] = $dealer->id;
        $this->dealerShowRoomService->create($showRoomInput);

        $userInput['dealer_id'] = $dealer->id;
        $this->userCreateService->create($userInput);

        return $dealer;
    }

    public function update(string $id, array $input): Dealer
    {
        $input = $this->validator($input)->validate();
        /** @var Dealer $dealer */
        $dealer = Dealer::query()->findOrFail($id);

        if (isset($input['logo'])) {
            $input['logo_path'] = $this->uploadLogo($input['logo'], $dealer->slug);
            unset($input['logo']);
            Storage::disk('s3')->delete($dealer->logo_path);
        }

        $dealer->update($input);

        return $dealer;
    }

    /**
     * @param array  $logoData
     * @param string $dealerSlug
     *
     * @return string
     */
    private function uploadLogo(array $logoData, string $dealerSlug): string
    {
        $fileName          = Carbon::now()->timestamp;
        $originalImageData = $this->getOriginalImageData($logoData['body']);
        $basePath          = sprintf('dealers/%s', $dealerSlug);
        $imageName         = sprintf('%s.%s', $fileName, self::IMAGE_EXTENSION);
        $path              = sprintf('%s/%s', $basePath, $imageName);
        Storage::disk('s3')->put(
            $path,
            $originalImageData,
            'public'
        );

        return $path;
    }

    /**
     * @param string $imageBody
     *
     * @return string
     */
    private function getOriginalImageData(string $imageBody): string
    {
        $imageComponents = explode(',', $imageBody);
        $base64Image     = base64_decode(array_pop($imageComponents));

        return (string) Image::make($base64Image)
                             ->resize(
                                 self::IMAGE_WIDTH,
                                 null,
                                 function (Constraint $constraint) {
                                     $constraint->aspectRatio();
                                 }
                             )
                             ->encode(self::IMAGE_EXTENSION, 80);
    }

    /**
     * @param array $data
     * @param bool  $isCreate
     *
     * @return ValidatorInterface
     */
    protected function validator(array $data, bool $isCreate = false): ValidatorInterface
    {
        $rules = [
            'address'       => 'required',
            'zip_code'      => 'required',
            'city'          => 'required',
            'country'       => 'required',
            'logo'          => 'nullable',
            'email_address' => 'required|email',
            'phone_number'  => 'required|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i',
            'company_name'  => 'required',
            'vat_number'    => 'required',
            'description'   => 'nullable|string|max:500',
            'is_external'   => 'nullable',
            'source'        => 'nullable',
            'external_id'   => 'nullable',
        ];

        if (true === $isCreate) {
            $rules['slug']                      = 'required|unique:dealers,slug';
            $rules['logo']                      = 'required';
            $rules['show_room.name']            = 'required';
            $rules['show_room.address']         = 'required';
            $rules['show_room.zip_code']        = 'required';
            $rules['show_room.city']            = 'required';
            $rules['show_room.market_id']       = 'required|exists:markets,id';
            $rules['show_room.email_address']   = 'required|email';
            $rules['show_room.mobile_number']   = 'required|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i';
            $rules['show_room.whatsapp_number'] = 'nullable|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i';
            $rules['show_room.landline_number'] = 'nullable|regex:/^\+(?:[0-9] ?){6,14}[0-9]/i';
            $rules['show_room.latitude']        = 'required';
            $rules['show_room.longitude']       = 'required';
            $rules['user.first_name']           = 'required';
            $rules['user.last_name']            = 'required';
            $rules['user.email']                = 'required|email|unique:users,email';
            $rules['vat_number']                = 'required|unique:dealers,vat_number';
            $rules['terms_agreed']              = 'required';

            if (Auth::guest()) {
                $rules['user.password'] = 'required|string|min:8|confirmed';
            }
        }

        /** @var User $currentUser */
        $currentUser = Auth::user();
        if ($currentUser instanceof User && true === $currentUser->hasRole(RoleEnum::ADMIN)) {
            $rules['vat_number']   = 'nullable';
            $rules['phone_number'] = 'required';
        }

        return Validator::make(
            $data,
            $rules,
            [
                'phone_number.regex'              => __('validation.invalid_phone_number'),
                'show_room.address.required'      => __('validation.choose_address'),
                'show_room.mobile_number.regex'   => __('validation.invalid_phone_number'),
                'show_room.whatsapp_number.regex' => __('validation.invalid_phone_number'),
                'show_room.landline_number.regex' => __('validation.invalid_phone_number'),
                'terms_agreed.required'           => __('validation.terms_agreement'),
            ],
            [
                'show_room.latitude'  => 'show_room.address',
                'show_room.longitude' => 'show_room.address',
            ]
        );
    }
}
