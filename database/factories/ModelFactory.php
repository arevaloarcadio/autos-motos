<?php

/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Brackets\AdminAuth\Models\AdminUser::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => bcrypt($faker->password),
        'remember_token' => null,
        'activated' => true,
        'forbidden' => $faker->boolean(),
        'language' => 'en',
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'last_login_at' => $faker->dateTime,
        
    ];
});/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\VehicleCategory::class, static function (Faker\Generator $faker) {
    return [
        'icon' => $faker->sentence,
        'name' => $faker->firstName,
        'type_ads' => $faker->randomNumber(5),
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Brand::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'logo' => $faker->sentence,
        'top' => $faker->randomNumber(5),
        'slug' => $faker->unique()->slug,
        'meta_title' => $faker->sentence,
        'meta_description' => $faker->text(),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Category::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'order_level' => $faker->randomNumber(5),
        'icon' => $faker->sentence,
        'slug' => $faker->unique()->slug,
        'ads_type' => $faker->boolean(),
        'meta_title' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Attribute::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'searched' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'featured' => $faker->boolean(),
        'is_choice' => $faker->boolean(),
        'order_level' => $faker->randomNumber(5),
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AttributeValue::class, static function (Faker\Generator $faker) {
    return [
        'attribute_id' => $faker->randomNumber(5),
        'value' => $faker->sentence,
        'color_code' => $faker->sentence,
        'ads_type' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Store::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'email' => $faker->email,
        'phone' => $faker->sentence,
        'city' => $faker->sentence,
        'code_postal' => $faker->sentence,
        'whatsapp' => $faker->sentence,
        'country_id' => $faker->sentence,
        'user_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Company::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'cif' => $faker->sentence,
        'phone' => $faker->sentence,
        'city' => $faker->sentence,
        'code_postal' => $faker->sentence,
        'whatsapp' => $faker->sentence,
        'logo' => $faker->sentence,
        'description' => $faker->text(),
        'country_id' => $faker->sentence,
        'user_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
