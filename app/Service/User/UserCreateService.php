<?php
declare(strict_types=1);

namespace App\Service\User;

use App\Manager\User\RoleManager;
use App\Manager\User\UserManager;
use App\Manager\User\UserRoleManager;
use App\Models\User;
use App\Models\UserRole;
use App\Notifications\UserCreated;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Support\Str;

/**
 * Defines the business logic associated with user creation.
 *
 * @package App\Service\User
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class UserCreateService
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var RoleManager
     */
    private $roleManager;

    /**
     * @var UserRoleManager
     */
    private $userRoleManager;

    /**
     * UserCreateService constructor.
     *
     * @param UserManager     $userManager
     * @param RoleManager     $roleManager
     * @param UserRoleManager $userRoleManager
     */
    public function __construct(
        UserManager $userManager,
        RoleManager $roleManager,
        UserRoleManager $userRoleManager
    ) {
        $this->userManager     = $userManager;
        $this->roleManager     = $roleManager;
        $this->userRoleManager = $userRoleManager;
    }

    /**
     * @param array $userCreateInput
     *
     * @return User
     */
    public function create(array $userCreateInput): User
    {
        $hasSetPassword              = isset($userCreateInput['password']);
        $userCreateInput['password'] = $this->checkPassword($userCreateInput);
        $data                        = $this->getValidator($userCreateInput)->validate();

        $user = $this->createUser($data);
        $this->createUserRole($user);

        if (false === $hasSetPassword) {
            $user->notify(new UserCreated($userCreateInput['password']));
        }
        event(new Registered($user));

        return $user;
    }

    /**
     * @param array $data
     *
     * @return User
     */
    private function createUser(array $data): User
    {
        $user                    = new User();
        $user->first_name        = $data['first_name'];
        $user->last_name         = $data['last_name'];
        $user->email             = $data['email'];
        $user->password          = Hash::make($data['password']);
        $user->dealer_id         = $data['dealer_id'] ?? null;
        $user->email_verified_at = Carbon::now();
        $this->userManager->save($user);

        return $user;
    }


    /**
     * @param User $user
     *
     * @return UserRole
     */
    private function createUserRole(User $user): UserRole
    {
        $role     = $this->roleManager->findOneBy(['name' => 'USER']);
        $userRole = new UserRole();
        $userRole->role()->associate($role);
        $userRole->user()->associate($user);
        $this->userRoleManager->save($userRole);

        return $userRole;
    }

    /**
     * @param array $userCreateInput
     *
     * @return string
     */
    private function checkPassword(array $userCreateInput): string
    {
        if (false === isset($userCreateInput['password'])) {
            return Str::random();
        }

        return $userCreateInput['password'];
    }

    /**
     * @param array $userCreateInput
     *
     * @return ValidatorInterface
     */
    public function getValidator(array $userCreateInput): ValidatorInterface
    {
        $rules = [
            'first_name'      => ['required', 'string', 'max:255'],
            'last_name'       => ['required', 'string', 'max:255'],
            'email'           => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile_number'   => ['nullable'],
            'landline_number' => ['nullable'],
            'whatsapp_number' => ['nullable'],
            'dealer_id'       => ['nullable', 'exists:dealers,id'],
            'password'        => ['required', 'string', 'min:8'],
        ];

        return Validator::make($userCreateInput, $rules);
    }


}
