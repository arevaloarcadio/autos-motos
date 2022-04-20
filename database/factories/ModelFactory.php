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
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AutoAd::class, static function (Faker\Generator $faker) {
    return [
        'ad_id' => $faker->sentence,
        'price' => $faker->randomNumber(5),
        'price_contains_vat' => $faker->boolean(),
        'vin' => $faker->sentence,
        'doors' => $faker->randomNumber(5),
        'mileage' => $faker->randomNumber(5),
        'exterior_color' => $faker->sentence,
        'interior_color' => $faker->sentence,
        'condition' => $faker->sentence,
        'dealer_id' => $faker->sentence,
        'dealer_show_room_id' => $faker->sentence,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email_address' => $faker->sentence,
        'address' => $faker->text(),
        'zip_code' => $faker->sentence,
        'city' => $faker->sentence,
        'country' => $faker->sentence,
        'mobile_number' => $faker->sentence,
        'landline_number' => $faker->sentence,
        'whatsapp_number' => $faker->sentence,
        'youtube_link' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'ad_fuel_type_id' => $faker->sentence,
        'ad_body_type_id' => $faker->sentence,
        'ad_transmission_type_id' => $faker->sentence,
        'ad_drive_type_id' => $faker->sentence,
        'first_registration_month' => $faker->randomNumber(5),
        'first_registration_year' => $faker->randomNumber(5),
        'engine_displacement' => $faker->randomNumber(5),
        'power_hp' => $faker->randomNumber(5),
        'owners' => $faker->randomNumber(5),
        'inspection_valid_until_month' => $faker->randomNumber(5),
        'inspection_valid_until_year' => $faker->randomNumber(5),
        'make_id' => $faker->sentence,
        'model_id' => $faker->sentence,
        'generation_id' => $faker->sentence,
        'series_id' => $faker->sentence,
        'trim_id' => $faker->sentence,
        'equipment_id' => $faker->sentence,
        'additional_vehicle_info' => $faker->sentence,
        'seats' => $faker->randomNumber(5),
        'fuel_consumption' => $faker->randomNumber(5),
        'co2_emissions' => $faker->randomNumber(5),
        'latitude' => $faker->sentence,
        'longitude' => $faker->sentence,
        'geocoding_status' => $faker->sentence,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AutoAd::class, static function (Faker\Generator $faker) {
    return [
        'ad_id' => $faker->sentence,
        'price' => $faker->randomNumber(5),
        'vin' => $faker->sentence,
        'doors' => $faker->randomNumber(5),
        'mileage' => $faker->randomNumber(5),
        'exterior_color' => $faker->sentence,
        'interior_color' => $faker->sentence,
        'condition' => $faker->sentence,
        'dealer_id' => $faker->sentence,
        'dealer_show_room_id' => $faker->sentence,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email_address' => $faker->sentence,
        'address' => $faker->text(),
        'zip_code' => $faker->sentence,
        'city' => $faker->sentence,
        'country' => $faker->sentence,
        'mobile_number' => $faker->sentence,
        'landline_number' => $faker->sentence,
        'whatsapp_number' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'ad_fuel_type_id' => $faker->sentence,
        'ad_body_type_id' => $faker->sentence,
        'ad_transmission_type_id' => $faker->sentence,
        'ad_drive_type_id' => $faker->sentence,
        'first_registration_month' => $faker->randomNumber(5),
        'first_registration_year' => $faker->randomNumber(5),
        'engine_displacement' => $faker->randomNumber(5),
        'power_hp' => $faker->randomNumber(5),
        'owners' => $faker->randomNumber(5),
        'inspection_valid_until_month' => $faker->randomNumber(5),
        'inspection_valid_until_year' => $faker->randomNumber(5),
        'make_id' => $faker->sentence,
        'model_id' => $faker->sentence,
        'generation_id' => $faker->sentence,
        'series_id' => $faker->sentence,
        'trim_id' => $faker->sentence,
        'equipment_id' => $faker->sentence,
        'additional_vehicle_info' => $faker->sentence,
        'seats' => $faker->randomNumber(5),
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CarBodyType::class, static function (Faker\Generator $faker) {
    return [
        'internal_name' => $faker->sentence,
        'slug' => $faker->unique()->slug,
        'icon_url' => $faker->sentence,
        'external_name' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CarFuelType::class, static function (Faker\Generator $faker) {
    return [
        'internal_name' => $faker->sentence,
        'slug' => $faker->unique()->slug,
        'external_name' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CarTransmissionType::class, static function (Faker\Generator $faker) {
    return [
        'internal_name' => $faker->sentence,
        'slug' => $faker->unique()->slug,
        'external_name' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
