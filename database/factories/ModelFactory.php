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
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Ad::class, static function (Faker\Generator $faker) {
    return [
        'slug' => $faker->unique()->slug,
        'title' => $faker->sentence,
        'description' => $faker->text(),
        'thumbnail' => $faker->sentence,
        'status' => $faker->randomNumber(5),
        'type' => $faker->sentence,
        'is_featured' => $faker->boolean(),
        'user_id' => $faker->sentence,
        'market_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'external_id' => $faker->randomNumber(5),
        'source' => $faker->sentence,
        'images_processing_status' => $faker->sentence,
        'images_processing_status_text' => $faker->text(),
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Ad::class, static function (Faker\Generator $faker) {
    return [
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AdImage::class, static function (Faker\Generator $faker) {
    return [
        'ad_id' => $faker->sentence,
        'path' => $faker->sentence,
        'is_external' => $faker->boolean(),
        'order_index' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AdImageVersion::class, static function (Faker\Generator $faker) {
    return [
        'ad_image_id' => $faker->sentence,
        'name' => $faker->firstName,
        'path' => $faker->sentence,
        'is_external' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AdMake::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'slug' => $faker->unique()->slug,
        'ad_type' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AdModel::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'slug' => $faker->unique()->slug,
        'ad_type' => $faker->sentence,
        'parent_id' => $faker->sentence,
        'ad_make_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AutoAdOption::class, static function (Faker\Generator $faker) {
    return [
        'auto_ad_id' => $faker->sentence,
        'auto_option_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AutoOption::class, static function (Faker\Generator $faker) {
    return [
        'internal_name' => $faker->sentence,
        'slug' => $faker->unique()->slug,
        'parent_id' => $faker->sentence,
        'ad_type' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Banner::class, static function (Faker\Generator $faker) {
    return [
        'location' => $faker->sentence,
        'image_path' => $faker->sentence,
        'link' => $faker->sentence,
        'order_index' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CarBodyType::class, static function (Faker\Generator $faker) {
    return [
        'internal_name' => $faker->sentence,
        'slug' => $faker->unique()->slug,
        'icon_url' => $faker->sentence,
        'external_name' => $faker->sentence,
        'ad_type' => $faker->sentence,
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
        'ad_type' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CarGeneration::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'year' => $faker->date(),
        'car_model_id' => $faker->sentence,
        'external_id' => $faker->randomNumber(5),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CarMake::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'slug' => $faker->unique()->slug,
        'external_id' => $faker->randomNumber(5),
        'is_active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CarModel::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'slug' => $faker->unique()->slug,
        'car_make_id' => $faker->sentence,
        'external_id' => $faker->randomNumber(5),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CarSpec::class, static function (Faker\Generator $faker) {
    return [
        'car_make_id' => $faker->sentence,
        'car_model_id' => $faker->sentence,
        'car_generation_id' => $faker->sentence,
        'car_body_type_id' => $faker->sentence,
        'engine' => $faker->sentence,
        'doors' => $faker->sentence,
        'doors_min' => $faker->randomNumber(5),
        'doors_max' => $faker->randomNumber(5),
        'power_hp' => $faker->randomNumber(5),
        'power_rpm' => $faker->sentence,
        'power_rpm_min' => $faker->randomNumber(5),
        'power_rpm_max' => $faker->randomNumber(5),
        'engine_displacement' => $faker->randomNumber(5),
        'production_start_year' => $faker->date(),
        'production_end_year' => $faker->date(),
        'car_fuel_type_id' => $faker->sentence,
        'car_transmission_type_id' => $faker->sentence,
        'gears' => $faker->randomNumber(5),
        'car_wheel_drive_type_id' => $faker->sentence,
        'battery_capacity' => $faker->randomFloat,
        'electric_power_hp' => $faker->randomNumber(5),
        'electric_power_rpm' => $faker->sentence,
        'electric_power_rpm_min' => $faker->randomNumber(5),
        'electric_power_rpm_max' => $faker->randomNumber(5),
        'external_id' => $faker->randomNumber(5),
        'last_external_update' => $faker->dateTime,
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
        'ad_type' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CarWheelDriveType::class, static function (Faker\Generator $faker) {
    return [
        'internal_name' => $faker->sentence,
        'slug' => $faker->unique()->slug,
        'external_name' => $faker->sentence,
        'ad_type' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Dealer::class, static function (Faker\Generator $faker) {
    return [
        'slug' => $faker->unique()->slug,
        'company_name' => $faker->sentence,
        'vat_number' => $faker->sentence,
        'address' => $faker->sentence,
        'zip_code' => $faker->sentence,
        'city' => $faker->sentence,
        'country' => $faker->sentence,
        'logo_path' => $faker->sentence,
        'email_address' => $faker->sentence,
        'phone_number' => $faker->sentence,
        'status' => $faker->randomNumber(5),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'description' => $faker->text(),
        'external_id' => $faker->randomNumber(5),
        'source' => $faker->sentence,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\DealerShowRoom::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'address' => $faker->sentence,
        'zip_code' => $faker->sentence,
        'city' => $faker->sentence,
        'country' => $faker->sentence,
        'latitude' => $faker->sentence,
        'longitude' => $faker->sentence,
        'email_address' => $faker->sentence,
        'mobile_number' => $faker->sentence,
        'landline_number' => $faker->sentence,
        'whatsapp_number' => $faker->sentence,
        'dealer_id' => $faker->sentence,
        'market_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Equipment::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'trim_id' => $faker->sentence,
        'year' => $faker->randomNumber(5),
        'ad_type' => $faker->sentence,
        'external_id' => $faker->randomNumber(5),
        'external_updated_at' => $faker->dateTime,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\EquipmentOption::class, static function (Faker\Generator $faker) {
    return [
        'equipment_id' => $faker->sentence,
        'option_id' => $faker->sentence,
        'is_base' => $faker->boolean(),
        'ad_type' => $faker->sentence,
        'external_id' => $faker->randomNumber(5),
        'external_updated_at' => $faker->dateTime,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Generation::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'model_id' => $faker->sentence,
        'year_begin' => $faker->randomNumber(5),
        'year_end' => $faker->randomNumber(5),
        'is_active' => $faker->boolean(),
        'ad_type' => $faker->sentence,
        'external_id' => $faker->randomNumber(5),
        'external_updated_at' => $faker->dateTime,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Locale::class, static function (Faker\Generator $faker) {
    return [
        'internal_name' => $faker->sentence,
        'code' => $faker->sentence,
        'icon' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Make::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'slug' => $faker->unique()->slug,
        'is_active' => $faker->boolean(),
        'ad_type' => $faker->sentence,
        'external_id' => $faker->randomNumber(5),
        'external_updated_at' => $faker->dateTime,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Market::class, static function (Faker\Generator $faker) {
    return [
        'internal_name' => $faker->sentence,
        'slug' => $faker->unique()->slug,
        'domain' => $faker->sentence,
        'default_locale_id' => $faker->sentence,
        'icon' => $faker->sentence,
        'mobile_number' => $faker->sentence,
        'whatsapp_number' => $faker->sentence,
        'email_address' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'order_index' => $faker->boolean(),
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\MechanicAd::class, static function (Faker\Generator $faker) {
    return [
        'ad_id' => $faker->sentence,
        'address' => $faker->text(),
        'latitude' => $faker->sentence,
        'longitude' => $faker->sentence,
        'zip_code' => $faker->sentence,
        'city' => $faker->sentence,
        'country' => $faker->sentence,
        'mobile_number' => $faker->sentence,
        'whatsapp_number' => $faker->sentence,
        'website_url' => $faker->sentence,
        'email_address' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'geocoding_status' => $faker->sentence,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Model::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'slug' => $faker->unique()->slug,
        'make_id' => $faker->sentence,
        'is_active' => $faker->boolean(),
        'ad_type' => $faker->sentence,
        'external_id' => $faker->randomNumber(5),
        'external_updated_at' => $faker->dateTime,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\MotoAd::class, static function (Faker\Generator $faker) {
    return [
        'ad_id' => $faker->sentence,
        'make_id' => $faker->sentence,
        'custom_make' => $faker->sentence,
        'model_id' => $faker->sentence,
        'custom_model' => $faker->sentence,
        'fuel_type_id' => $faker->sentence,
        'body_type_id' => $faker->sentence,
        'transmission_type_id' => $faker->sentence,
        'drive_type_id' => $faker->sentence,
        'first_registration_month' => $faker->randomNumber(5),
        'first_registration_year' => $faker->randomNumber(5),
        'inspection_valid_until_month' => $faker->randomNumber(5),
        'inspection_valid_until_year' => $faker->randomNumber(5),
        'last_customer_service_month' => $faker->randomNumber(5),
        'last_customer_service_year' => $faker->randomNumber(5),
        'owners' => $faker->randomNumber(5),
        'weight_kg' => $faker->randomNumber(5),
        'engine_displacement' => $faker->randomNumber(5),
        'mileage' => $faker->randomNumber(5),
        'power_kw' => $faker->randomNumber(5),
        'gears' => $faker->randomNumber(5),
        'cylinders' => $faker->randomNumber(5),
        'emission_class' => $faker->sentence,
        'fuel_consumption' => $faker->randomNumber(5),
        'co2_emissions' => $faker->randomNumber(5),
        'condition' => $faker->sentence,
        'color' => $faker->sentence,
        'price' => $faker->randomNumber(5),
        'price_contains_vat' => $faker->boolean(),
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
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Operation::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'context' => $faker->text(),
        'status' => $faker->sentence,
        'status_text' => $faker->text(),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Option::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'slug' => $faker->unique()->slug,
        'parent_id' => $faker->sentence,
        'ad_type' => $faker->sentence,
        'external_id' => $faker->randomNumber(5),
        'external_updated_at' => $faker->dateTime,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\RentalAd::class, static function (Faker\Generator $faker) {
    return [
        'ad_id' => $faker->sentence,
        'address' => $faker->text(),
        'latitude' => $faker->sentence,
        'longitude' => $faker->sentence,
        'zip_code' => $faker->sentence,
        'city' => $faker->sentence,
        'country' => $faker->sentence,
        'mobile_number' => $faker->sentence,
        'whatsapp_number' => $faker->sentence,
        'website_url' => $faker->sentence,
        'email_address' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Role::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Series::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'model_id' => $faker->sentence,
        'generation_id' => $faker->sentence,
        'is_active' => $faker->boolean(),
        'ad_type' => $faker->sentence,
        'external_id' => $faker->randomNumber(5),
        'external_updated_at' => $faker->dateTime,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ShopAd::class, static function (Faker\Generator $faker) {
    return [
        'ad_id' => $faker->sentence,
        'category' => $faker->sentence,
        'make_id' => $faker->sentence,
        'model' => $faker->sentence,
        'manufacturer' => $faker->sentence,
        'code' => $faker->sentence,
        'condition' => $faker->sentence,
        'price' => $faker->randomNumber(5),
        'price_contains_vat' => $faker->boolean(),
        'dealer_id' => $faker->sentence,
        'dealer_show_room_id' => $faker->sentence,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email_address' => $faker->sentence,
        'address' => $faker->text(),
        'zip_code' => $faker->sentence,
        'city' => $faker->sentence,
        'country' => $faker->sentence,
        'latitude' => $faker->sentence,
        'longitude' => $faker->sentence,
        'mobile_number' => $faker->sentence,
        'landline_number' => $faker->sentence,
        'whatsapp_number' => $faker->sentence,
        'youtube_link' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Specification::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'slug' => $faker->unique()->slug,
        'parent_id' => $faker->sentence,
        'ad_type' => $faker->sentence,
        'external_id' => $faker->randomNumber(5),
        'external_updated_at' => $faker->dateTime,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Translation::class, static function (Faker\Generator $faker) {
    return [
        'locale_id' => $faker->sentence,
        'translation_key' => $faker->sentence,
        'translation_value' => $faker->text(),
        'resource_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Trim::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'model_id' => $faker->sentence,
        'series_id' => $faker->sentence,
        'production_year_start' => $faker->randomNumber(5),
        'production_year_end' => $faker->randomNumber(5),
        'is_active' => $faker->boolean(),
        'ad_type' => $faker->sentence,
        'external_id' => $faker->randomNumber(5),
        'external_updated_at' => $faker->dateTime,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\TrimSpecification::class, static function (Faker\Generator $faker) {
    return [
        'trim_id' => $faker->sentence,
        'specification_id' => $faker->sentence,
        'value' => $faker->sentence,
        'unit' => $faker->sentence,
        'ad_type' => $faker->sentence,
        'external_id' => $faker->randomNumber(5),
        'external_updated_at' => $faker->dateTime,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\TruckAd::class, static function (Faker\Generator $faker) {
    return [
        'ad_id' => $faker->sentence,
        'make_id' => $faker->sentence,
        'custom_make' => $faker->sentence,
        'model' => $faker->sentence,
        'truck_type' => $faker->sentence,
        'fuel_type_id' => $faker->sentence,
        'vehicle_category_id' => $faker->sentence,
        'transmission_type_id' => $faker->sentence,
        'cab' => $faker->sentence,
        'construction_year' => $faker->randomNumber(5),
        'first_registration_month' => $faker->randomNumber(5),
        'first_registration_year' => $faker->randomNumber(5),
        'inspection_valid_until_month' => $faker->randomNumber(5),
        'inspection_valid_until_year' => $faker->randomNumber(5),
        'owners' => $faker->randomNumber(5),
        'construction_height_mm' => $faker->randomNumber(5),
        'lifting_height_mm' => $faker->randomNumber(5),
        'lifting_capacity_kg_m' => $faker->randomNumber(5),
        'permanent_total_weight_kg' => $faker->randomNumber(5),
        'allowed_pulling_weight_kg' => $faker->randomNumber(5),
        'payload_kg' => $faker->randomNumber(5),
        'max_weight_allowed_kg' => $faker->randomNumber(5),
        'empty_weight_kg' => $faker->randomNumber(5),
        'loading_space_length_mm' => $faker->randomNumber(5),
        'loading_space_width_mm' => $faker->randomNumber(5),
        'loading_space_height_mm' => $faker->randomNumber(5),
        'loading_volume_m3' => $faker->randomNumber(5),
        'load_capacity_kg' => $faker->randomNumber(5),
        'operating_weight_kg' => $faker->randomNumber(5),
        'operating_hours' => $faker->randomNumber(5),
        'axes' => $faker->randomNumber(5),
        'wheel_formula' => $faker->sentence,
        'hydraulic_system' => $faker->sentence,
        'seats' => $faker->randomNumber(5),
        'mileage' => $faker->randomNumber(5),
        'power_kw' => $faker->randomNumber(5),
        'emission_class' => $faker->sentence,
        'fuel_consumption' => $faker->randomNumber(5),
        'co2_emissions' => $faker->randomNumber(5),
        'condition' => $faker->sentence,
        'interior_color' => $faker->sentence,
        'exterior_color' => $faker->sentence,
        'price' => $faker->randomNumber(5),
        'price_contains_vat' => $faker->boolean(),
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
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, static function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'mobile_number' => $faker->sentence,
        'landline_number' => $faker->sentence,
        'whatsapp_number' => $faker->sentence,
        'email' => $faker->email,
        'email_verified_at' => $faker->dateTime,
        'password' => bcrypt($faker->password),
        'dealer_id' => $faker->sentence,
        'remember_token' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UsersFavouriteAd::class, static function (Faker\Generator $faker) {
    return [
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UsersFavouriteAdSearch::class, static function (Faker\Generator $faker) {
    return [
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UserRole::class, static function (Faker\Generator $faker) {
    return [
        'user_id' => $faker->sentence,
        'role_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\VehicleCategory::class, static function (Faker\Generator $faker) {
    return [
        'internal_name' => $faker->sentence,
        'slug' => $faker->unique()->slug,
        'ad_type' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\MotoAdOption::class, static function (Faker\Generator $faker) {
    return [
        'moto_ad_id' => $faker->sentence,
        'option_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\MobileHomeAd::class, static function (Faker\Generator $faker) {
    return [
        'ad_id' => $faker->sentence,
        'make_id' => $faker->sentence,
        'custom_make' => $faker->sentence,
        'model_id' => $faker->sentence,
        'custom_model' => $faker->sentence,
        'fuel_type_id' => $faker->sentence,
        'vehicle_category_id' => $faker->sentence,
        'transmission_type_id' => $faker->sentence,
        'construction_year' => $faker->randomNumber(5),
        'first_registration_month' => $faker->randomNumber(5),
        'first_registration_year' => $faker->randomNumber(5),
        'inspection_valid_until_month' => $faker->randomNumber(5),
        'inspection_valid_until_year' => $faker->randomNumber(5),
        'owners' => $faker->randomNumber(5),
        'length_cm' => $faker->randomNumber(5),
        'width_cm' => $faker->randomNumber(5),
        'height_cm' => $faker->randomNumber(5),
        'max_weight_allowed_kg' => $faker->randomNumber(5),
        'payload_kg' => $faker->randomNumber(5),
        'engine_displacement' => $faker->randomNumber(5),
        'mileage' => $faker->randomNumber(5),
        'power_kw' => $faker->randomNumber(5),
        'axes' => $faker->randomNumber(5),
        'seats' => $faker->randomNumber(5),
        'sleeping_places' => $faker->randomNumber(5),
        'beds' => $faker->sentence,
        'emission_class' => $faker->sentence,
        'fuel_consumption' => $faker->randomNumber(5),
        'co2_emissions' => $faker->randomNumber(5),
        'condition' => $faker->sentence,
        'color' => $faker->sentence,
        'price' => $faker->randomNumber(5),
        'price_contains_vat' => $faker->boolean(),
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
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\MobileHomeAdOption::class, static function (Faker\Generator $faker) {
    return [
        'mobile_home_ad_id' => $faker->sentence,
        'option_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Characteristic::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\SubCharacteristic::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'characteristic_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        
        
    ];
});
