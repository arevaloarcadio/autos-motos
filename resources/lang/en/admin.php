<?php

return [
    'admin-user' => [
        'title' => 'Users',

        'actions' => [
            'index' => 'Users',
            'create' => 'New User',
            'edit' => 'Edit :name',
            'edit_profile' => 'Edit Profile',
            'edit_password' => 'Edit Password',
        ],

        'columns' => [
            'id' => 'ID',
            'last_login_at' => 'Last login',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email' => 'Email',
            'password' => 'Password',
            'password_repeat' => 'Password Confirmation',
            'activated' => 'Activated',
            'forbidden' => 'Forbidden',
            'language' => 'Language',
                
            //Belongs to many relations
            'roles' => 'Roles',
                
        ],
    ],

    'vehicle-category' => [
        'title' => 'Vehicle Categories',

        'actions' => [
            'index' => 'Vehicle Categories',
            'create' => 'New Vehicle Category',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'icon' => 'Icon',
            'name' => 'Name',
            'type_ads' => 'Type ads',
            
        ],
    ],

    'brand' => [
        'title' => 'Brands',

        'actions' => [
            'index' => 'Brands',
            'create' => 'New Brand',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'logo' => 'Logo',
            'top' => 'Top',
            'slug' => 'Slug',
            'meta_title' => 'Meta title',
            'meta_description' => 'Meta description',
            
        ],
    ],

    'category' => [
        'title' => 'Categories',

        'actions' => [
            'index' => 'Categories',
            'create' => 'New Category',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'order_level' => 'Order level',
            'icon' => 'Icon',
            'slug' => 'Slug',
            'ads_type' => 'Ads type',
            'meta_title' => 'Meta title',
            
        ],
    ],

    'attribute' => [
        'title' => 'Attributes',

        'actions' => [
            'index' => 'Attributes',
            'create' => 'New Attribute',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'searched' => 'Searched',
            'featured' => 'Featured',
            'is_choice' => 'Is choice',
            'order_level' => 'Order level',
            
        ],
    ],

    'attribute-value' => [
        'title' => 'Attribute Values',

        'actions' => [
            'index' => 'Attribute Values',
            'create' => 'New Attribute Value',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'attribute_id' => 'Attribute',
            'value' => 'Value',
            'color_code' => 'Color code',
            'ads_type' => 'Ads type',
            
        ],
    ],

    'store' => [
        'title' => 'Stores',

        'actions' => [
            'index' => 'Stores',
            'create' => 'New Store',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'city' => 'City',
            'code_postal' => 'Code postal',
            'whatsapp' => 'Whatsapp',
            'country_id' => 'Country',
            'user_id' => 'User',
            
        ],
    ],

    'company' => [
        'title' => 'Companies',

        'actions' => [
            'index' => 'Companies',
            'create' => 'New Company',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'cif' => 'Cif',
            'phone' => 'Phone',
            'city' => 'City',
            'code_postal' => 'Code postal',
            'whatsapp' => 'Whatsapp',
            'logo' => 'Logo',
            'description' => 'Description',
            'country_id' => 'Country',
            'user_id' => 'User',
            
        ],
    ],

    'auto-ad' => [
        'title' => 'Auto Ads',

        'actions' => [
            'index' => 'Auto Ads',
            'create' => 'New Auto Ad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'ad_id' => 'Ad',
            'price' => 'Price',
            'price_contains_vat' => 'Price contains vat',
            'vin' => 'Vin',
            'doors' => 'Doors',
            'mileage' => 'Mileage',
            'exterior_color' => 'Exterior color',
            'interior_color' => 'Interior color',
            'condition' => 'Condition',
            'dealer_id' => 'Dealer',
            'dealer_show_room_id' => 'Dealer show room',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email_address' => 'Email address',
            'address' => 'Address',
            'zip_code' => 'Zip code',
            'city' => 'City',
            'country' => 'Country',
            'mobile_number' => 'Mobile number',
            'landline_number' => 'Landline number',
            'whatsapp_number' => 'Whatsapp number',
            'youtube_link' => 'Youtube link',
            'ad_fuel_type_id' => 'Ad fuel type',
            'ad_body_type_id' => 'Ad body type',
            'ad_transmission_type_id' => 'Ad transmission type',
            'ad_drive_type_id' => 'Ad drive type',
            'first_registration_month' => 'First registration month',
            'first_registration_year' => 'First registration year',
            'engine_displacement' => 'Engine displacement',
            'power_hp' => 'Power hp',
            'owners' => 'Owners',
            'inspection_valid_until_month' => 'Inspection valid until month',
            'inspection_valid_until_year' => 'Inspection valid until year',
            'make_id' => 'Make',
            'model_id' => 'Model',
            'generation_id' => 'Generation',
            'series_id' => 'Series',
            'trim_id' => 'Trim',
            'equipment_id' => 'Equipment',
            'additional_vehicle_info' => 'Additional vehicle info',
            'seats' => 'Seats',
            'fuel_consumption' => 'Fuel consumption',
            'co2_emissions' => 'Co2 emissions',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'geocoding_status' => 'Geocoding status',
            
        ],
    ],

    'auto-ad' => [
        'title' => 'Auto Ads',

        'actions' => [
            'index' => 'Auto Ads',
            'create' => 'New Auto Ad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'ad_id' => 'Ad',
            'price' => 'Price',
            'vin' => 'Vin',
            'doors' => 'Doors',
            'mileage' => 'Mileage',
            'exterior_color' => 'Exterior color',
            'interior_color' => 'Interior color',
            'condition' => 'Condition',
            'dealer_id' => 'Dealer',
            'dealer_show_room_id' => 'Dealer show room',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email_address' => 'Email address',
            'address' => 'Address',
            'zip_code' => 'Zip code',
            'city' => 'City',
            'country' => 'Country',
            'mobile_number' => 'Mobile number',
            'landline_number' => 'Landline number',
            'whatsapp_number' => 'Whatsapp number',
            'ad_fuel_type_id' => 'Ad fuel type',
            'ad_body_type_id' => 'Ad body type',
            'ad_transmission_type_id' => 'Ad transmission type',
            'ad_drive_type_id' => 'Ad drive type',
            'first_registration_month' => 'First registration month',
            'first_registration_year' => 'First registration year',
            'engine_displacement' => 'Engine displacement',
            'power_hp' => 'Power hp',
            'owners' => 'Owners',
            'inspection_valid_until_month' => 'Inspection valid until month',
            'inspection_valid_until_year' => 'Inspection valid until year',
            'make_id' => 'Make',
            'model_id' => 'Model',
            'generation_id' => 'Generation',
            'series_id' => 'Series',
            'trim_id' => 'Trim',
            'equipment_id' => 'Equipment',
            'additional_vehicle_info' => 'Additional vehicle info',
            'seats' => 'Seats',
            
        ],
    ],

    'car-body-type' => [
        'title' => 'Car Body Types',

        'actions' => [
            'index' => 'Car Body Types',
            'create' => 'New Car Body Type',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'internal_name' => 'Internal name',
            'slug' => 'Slug',
            'icon_url' => 'Icon url',
            'external_name' => 'External name',
            
        ],
    ],

    'car-fuel-type' => [
        'title' => 'Car Fuel Types',

        'actions' => [
            'index' => 'Car Fuel Types',
            'create' => 'New Car Fuel Type',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'internal_name' => 'Internal name',
            'slug' => 'Slug',
            'external_name' => 'External name',
            
        ],
    ],

    'car-transmission-type' => [
        'title' => 'Car Transmission Types',

        'actions' => [
            'index' => 'Car Transmission Types',
            'create' => 'New Car Transmission Type',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'internal_name' => 'Internal name',
            'slug' => 'Slug',
            'external_name' => 'External name',
            
        ],
    ],

    'ad' => [
        'title' => 'Ads',

        'actions' => [
            'index' => 'Ads',
            'create' => 'New Ad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'slug' => 'Slug',
            'title' => 'Title',
            'description' => 'Description',
            'thumbnail' => 'Thumbnail',
            'status' => 'Status',
            'type' => 'Type',
            'is_featured' => 'Is featured',
            'user_id' => 'User',
            'market_id' => 'Market',
            'external_id' => 'External',
            'source' => 'Source',
            'images_processing_status' => 'Images processing status',
            'images_processing_status_text' => 'Images processing status text',
            
        ],
    ],

    'ad' => [
        'title' => 'Ads',

        'actions' => [
            'index' => 'Ads',
            'create' => 'New Ad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            
        ],
    ],

    'ad' => [
        'title' => 'Ads',

        'actions' => [
            'index' => 'Ads',
            'create' => 'New Ad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'slug' => 'Slug',
            'title' => 'Title',
            'description' => 'Description',
            'thumbnail' => 'Thumbnail',
            'status' => 'Status',
            'type' => 'Type',
            'is_featured' => 'Is featured',
            'user_id' => 'User',
            'market_id' => 'Market',
            'external_id' => 'External',
            'source' => 'Source',
            'images_processing_status' => 'Images processing status',
            'images_processing_status_text' => 'Images processing status text',
            
        ],
    ],

    'ad-image' => [
        'title' => 'Ad Images',

        'actions' => [
            'index' => 'Ad Images',
            'create' => 'New Ad Image',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'ad_id' => 'Ad',
            'path' => 'Path',
            'is_external' => 'Is external',
            'order_index' => 'Order index',
            
        ],
    ],

    'ad-image-version' => [
        'title' => 'Ad Image Versions',

        'actions' => [
            'index' => 'Ad Image Versions',
            'create' => 'New Ad Image Version',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'ad_image_id' => 'Ad image',
            'name' => 'Name',
            'path' => 'Path',
            'is_external' => 'Is external',
            
        ],
    ],

    'ad-make' => [
        'title' => 'Ad Makes',

        'actions' => [
            'index' => 'Ad Makes',
            'create' => 'New Ad Make',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'ad_type' => 'Ad type',
            
        ],
    ],

    'ad-model' => [
        'title' => 'Ad Models',

        'actions' => [
            'index' => 'Ad Models',
            'create' => 'New Ad Model',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'ad_type' => 'Ad type',
            'parent_id' => 'Parent',
            'ad_make_id' => 'Ad make',
            
        ],
    ],

    'auto-ad' => [
        'title' => 'Auto Ads',

        'actions' => [
            'index' => 'Auto Ads',
            'create' => 'New Auto Ad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'ad_id' => 'Ad',
            'price' => 'Price',
            'price_contains_vat' => 'Price contains vat',
            'vin' => 'Vin',
            'doors' => 'Doors',
            'mileage' => 'Mileage',
            'exterior_color' => 'Exterior color',
            'interior_color' => 'Interior color',
            'condition' => 'Condition',
            'dealer_id' => 'Dealer',
            'dealer_show_room_id' => 'Dealer show room',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email_address' => 'Email address',
            'address' => 'Address',
            'zip_code' => 'Zip code',
            'city' => 'City',
            'country' => 'Country',
            'mobile_number' => 'Mobile number',
            'landline_number' => 'Landline number',
            'whatsapp_number' => 'Whatsapp number',
            'youtube_link' => 'Youtube link',
            'ad_fuel_type_id' => 'Ad fuel type',
            'ad_body_type_id' => 'Ad body type',
            'ad_transmission_type_id' => 'Ad transmission type',
            'ad_drive_type_id' => 'Ad drive type',
            'first_registration_month' => 'First registration month',
            'first_registration_year' => 'First registration year',
            'engine_displacement' => 'Engine displacement',
            'power_hp' => 'Power hp',
            'owners' => 'Owners',
            'inspection_valid_until_month' => 'Inspection valid until month',
            'inspection_valid_until_year' => 'Inspection valid until year',
            'make_id' => 'Make',
            'model_id' => 'Model',
            'generation_id' => 'Generation',
            'series_id' => 'Series',
            'trim_id' => 'Trim',
            'equipment_id' => 'Equipment',
            'additional_vehicle_info' => 'Additional vehicle info',
            'seats' => 'Seats',
            'fuel_consumption' => 'Fuel consumption',
            'co2_emissions' => 'Co2 emissions',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'geocoding_status' => 'Geocoding status',
            
        ],
    ],

    'auto-ad-option' => [
        'title' => 'Auto Ad Options',

        'actions' => [
            'index' => 'Auto Ad Options',
            'create' => 'New Auto Ad Option',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'auto_ad_id' => 'Auto ad',
            'auto_option_id' => 'Auto option',
            
        ],
    ],

    'auto-option' => [
        'title' => 'Auto Options',

        'actions' => [
            'index' => 'Auto Options',
            'create' => 'New Auto Option',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'internal_name' => 'Internal name',
            'slug' => 'Slug',
            'parent_id' => 'Parent',
            'ad_type' => 'Ad type',
            
        ],
    ],

    'banner' => [
        'title' => 'Banners',

        'actions' => [
            'index' => 'Banners',
            'create' => 'New Banner',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'location' => 'Location',
            'image_path' => 'Image path',
            'link' => 'Link',
            'order_index' => 'Order index',
            
        ],
    ],

    'car-body-type' => [
        'title' => 'Car Body Types',

        'actions' => [
            'index' => 'Car Body Types',
            'create' => 'New Car Body Type',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'internal_name' => 'Internal name',
            'slug' => 'Slug',
            'icon_url' => 'Icon url',
            'external_name' => 'External name',
            'ad_type' => 'Ad type',
            
        ],
    ],

    'car-fuel-type' => [
        'title' => 'Car Fuel Types',

        'actions' => [
            'index' => 'Car Fuel Types',
            'create' => 'New Car Fuel Type',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'internal_name' => 'Internal name',
            'slug' => 'Slug',
            'external_name' => 'External name',
            'ad_type' => 'Ad type',
            
        ],
    ],

    'car-generation' => [
        'title' => 'Car Generations',

        'actions' => [
            'index' => 'Car Generations',
            'create' => 'New Car Generation',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'year' => 'Year',
            'car_model_id' => 'Car model',
            'external_id' => 'External',
            
        ],
    ],

    'car-make' => [
        'title' => 'Car Makes',

        'actions' => [
            'index' => 'Car Makes',
            'create' => 'New Car Make',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'external_id' => 'External',
            'is_active' => 'Is active',
            
        ],
    ],

    'car-model' => [
        'title' => 'Car Models',

        'actions' => [
            'index' => 'Car Models',
            'create' => 'New Car Model',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'car_make_id' => 'Car make',
            'external_id' => 'External',
            
        ],
    ],

    'car-spec' => [
        'title' => 'Car Specs',

        'actions' => [
            'index' => 'Car Specs',
            'create' => 'New Car Spec',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'car_make_id' => 'Car make',
            'car_model_id' => 'Car model',
            'car_generation_id' => 'Car generation',
            'car_body_type_id' => 'Car body type',
            'engine' => 'Engine',
            'doors' => 'Doors',
            'doors_min' => 'Doors min',
            'doors_max' => 'Doors max',
            'power_hp' => 'Power hp',
            'power_rpm' => 'Power rpm',
            'power_rpm_min' => 'Power rpm min',
            'power_rpm_max' => 'Power rpm max',
            'engine_displacement' => 'Engine displacement',
            'production_start_year' => 'Production start year',
            'production_end_year' => 'Production end year',
            'car_fuel_type_id' => 'Car fuel type',
            'car_transmission_type_id' => 'Car transmission type',
            'gears' => 'Gears',
            'car_wheel_drive_type_id' => 'Car wheel drive type',
            'battery_capacity' => 'Battery capacity',
            'electric_power_hp' => 'Electric power hp',
            'electric_power_rpm' => 'Electric power rpm',
            'electric_power_rpm_min' => 'Electric power rpm min',
            'electric_power_rpm_max' => 'Electric power rpm max',
            'external_id' => 'External',
            'last_external_update' => 'Last external update',
            
        ],
    ],

    'car-transmission-type' => [
        'title' => 'Car Transmission Types',

        'actions' => [
            'index' => 'Car Transmission Types',
            'create' => 'New Car Transmission Type',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'internal_name' => 'Internal name',
            'slug' => 'Slug',
            'external_name' => 'External name',
            'ad_type' => 'Ad type',
            
        ],
    ],

    'car-wheel-drive-type' => [
        'title' => 'Car Wheel Drive Types',

        'actions' => [
            'index' => 'Car Wheel Drive Types',
            'create' => 'New Car Wheel Drive Type',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'internal_name' => 'Internal name',
            'slug' => 'Slug',
            'external_name' => 'External name',
            'ad_type' => 'Ad type',
            
        ],
    ],

    'dealer' => [
        'title' => 'Dealers',

        'actions' => [
            'index' => 'Dealers',
            'create' => 'New Dealer',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'slug' => 'Slug',
            'company_name' => 'Company name',
            'vat_number' => 'Vat number',
            'address' => 'Address',
            'zip_code' => 'Zip code',
            'city' => 'City',
            'country' => 'Country',
            'logo_path' => 'Logo path',
            'email_address' => 'Email address',
            'phone_number' => 'Phone number',
            'status' => 'Status',
            'description' => 'Description',
            'external_id' => 'External',
            'source' => 'Source',
            
        ],
    ],

    'dealer-show-room' => [
        'title' => 'Dealer Show Rooms',

        'actions' => [
            'index' => 'Dealer Show Rooms',
            'create' => 'New Dealer Show Room',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'address' => 'Address',
            'zip_code' => 'Zip code',
            'city' => 'City',
            'country' => 'Country',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'email_address' => 'Email address',
            'mobile_number' => 'Mobile number',
            'landline_number' => 'Landline number',
            'whatsapp_number' => 'Whatsapp number',
            'dealer_id' => 'Dealer',
            'market_id' => 'Market',
            
        ],
    ],

    'equipment' => [
        'title' => 'Equipment',

        'actions' => [
            'index' => 'Equipment',
            'create' => 'New Equipment',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'trim_id' => 'Trim',
            'year' => 'Year',
            'ad_type' => 'Ad type',
            'external_id' => 'External',
            'external_updated_at' => 'External updated at',
            
        ],
    ],

    'equipment-option' => [
        'title' => 'Equipment Options',

        'actions' => [
            'index' => 'Equipment Options',
            'create' => 'New Equipment Option',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'equipment_id' => 'Equipment',
            'option_id' => 'Option',
            'is_base' => 'Is base',
            'ad_type' => 'Ad type',
            'external_id' => 'External',
            'external_updated_at' => 'External updated at',
            
        ],
    ],

    'generation' => [
        'title' => 'Generations',

        'actions' => [
            'index' => 'Generations',
            'create' => 'New Generation',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'model_id' => 'Model',
            'year_begin' => 'Year begin',
            'year_end' => 'Year end',
            'is_active' => 'Is active',
            'ad_type' => 'Ad type',
            'external_id' => 'External',
            'external_updated_at' => 'External updated at',
            
        ],
    ],

    'locale' => [
        'title' => 'Locales',

        'actions' => [
            'index' => 'Locales',
            'create' => 'New Locale',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'internal_name' => 'Internal name',
            'code' => 'Code',
            'icon' => 'Icon',
            
        ],
    ],

    'make' => [
        'title' => 'Makes',

        'actions' => [
            'index' => 'Makes',
            'create' => 'New Make',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'is_active' => 'Is active',
            'ad_type' => 'Ad type',
            'external_id' => 'External',
            'external_updated_at' => 'External updated at',
            
        ],
    ],

    'market' => [
        'title' => 'Markets',

        'actions' => [
            'index' => 'Markets',
            'create' => 'New Market',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'internal_name' => 'Internal name',
            'slug' => 'Slug',
            'domain' => 'Domain',
            'default_locale_id' => 'Default locale',
            'icon' => 'Icon',
            'mobile_number' => 'Mobile number',
            'whatsapp_number' => 'Whatsapp number',
            'email_address' => 'Email address',
            'order_index' => 'Order index',
            
        ],
    ],

    'mechanic-ad' => [
        'title' => 'Mechanic Ads',

        'actions' => [
            'index' => 'Mechanic Ads',
            'create' => 'New Mechanic Ad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'ad_id' => 'Ad',
            'address' => 'Address',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'zip_code' => 'Zip code',
            'city' => 'City',
            'country' => 'Country',
            'mobile_number' => 'Mobile number',
            'whatsapp_number' => 'Whatsapp number',
            'website_url' => 'Website url',
            'email_address' => 'Email address',
            'geocoding_status' => 'Geocoding status',
            
        ],
    ],

    'model' => [
        'title' => 'Models',

        'actions' => [
            'index' => 'Models',
            'create' => 'New Model',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'make_id' => 'Make',
            'is_active' => 'Is active',
            'ad_type' => 'Ad type',
            'external_id' => 'External',
            'external_updated_at' => 'External updated at',
            
        ],
    ],

    'moto-ad' => [
        'title' => 'Moto Ads',

        'actions' => [
            'index' => 'Moto Ads',
            'create' => 'New Moto Ad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'ad_id' => 'Ad',
            'make_id' => 'Make',
            'custom_make' => 'Custom make',
            'model_id' => 'Model',
            'custom_model' => 'Custom model',
            'fuel_type_id' => 'Fuel type',
            'body_type_id' => 'Body type',
            'transmission_type_id' => 'Transmission type',
            'drive_type_id' => 'Drive type',
            'first_registration_month' => 'First registration month',
            'first_registration_year' => 'First registration year',
            'inspection_valid_until_month' => 'Inspection valid until month',
            'inspection_valid_until_year' => 'Inspection valid until year',
            'last_customer_service_month' => 'Last customer service month',
            'last_customer_service_year' => 'Last customer service year',
            'owners' => 'Owners',
            'weight_kg' => 'Weight kg',
            'engine_displacement' => 'Engine displacement',
            'mileage' => 'Mileage',
            'power_kw' => 'Power kw',
            'gears' => 'Gears',
            'cylinders' => 'Cylinders',
            'emission_class' => 'Emission class',
            'fuel_consumption' => 'Fuel consumption',
            'co2_emissions' => 'Co2 emissions',
            'condition' => 'Condition',
            'color' => 'Color',
            'price' => 'Price',
            'price_contains_vat' => 'Price contains vat',
            'dealer_id' => 'Dealer',
            'dealer_show_room_id' => 'Dealer show room',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email_address' => 'Email address',
            'address' => 'Address',
            'zip_code' => 'Zip code',
            'city' => 'City',
            'country' => 'Country',
            'mobile_number' => 'Mobile number',
            'landline_number' => 'Landline number',
            'whatsapp_number' => 'Whatsapp number',
            'youtube_link' => 'Youtube link',
            
        ],
    ],

    'operation' => [
        'title' => 'Operations',

        'actions' => [
            'index' => 'Operations',
            'create' => 'New Operation',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'context' => 'Context',
            'status' => 'Status',
            'status_text' => 'Status text',
            
        ],
    ],

    'option' => [
        'title' => 'Options',

        'actions' => [
            'index' => 'Options',
            'create' => 'New Option',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'parent_id' => 'Parent',
            'ad_type' => 'Ad type',
            'external_id' => 'External',
            'external_updated_at' => 'External updated at',
            
        ],
    ],

    'rental-ad' => [
        'title' => 'Rental Ads',

        'actions' => [
            'index' => 'Rental Ads',
            'create' => 'New Rental Ad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'ad_id' => 'Ad',
            'address' => 'Address',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'zip_code' => 'Zip code',
            'city' => 'City',
            'country' => 'Country',
            'mobile_number' => 'Mobile number',
            'whatsapp_number' => 'Whatsapp number',
            'website_url' => 'Website url',
            'email_address' => 'Email address',
            
        ],
    ],

    'role' => [
        'title' => 'Roles',

        'actions' => [
            'index' => 'Roles',
            'create' => 'New Role',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            
        ],
    ],

    'series' => [
        'title' => 'Series',

        'actions' => [
            'index' => 'Series',
            'create' => 'New Series',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'model_id' => 'Model',
            'generation_id' => 'Generation',
            'is_active' => 'Is active',
            'ad_type' => 'Ad type',
            'external_id' => 'External',
            'external_updated_at' => 'External updated at',
            
        ],
    ],

    'shop-ad' => [
        'title' => 'Shop Ads',

        'actions' => [
            'index' => 'Shop Ads',
            'create' => 'New Shop Ad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'ad_id' => 'Ad',
            'category' => 'Category',
            'make_id' => 'Make',
            'model' => 'Model',
            'manufacturer' => 'Manufacturer',
            'code' => 'Code',
            'condition' => 'Condition',
            'price' => 'Price',
            'price_contains_vat' => 'Price contains vat',
            'dealer_id' => 'Dealer',
            'dealer_show_room_id' => 'Dealer show room',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email_address' => 'Email address',
            'address' => 'Address',
            'zip_code' => 'Zip code',
            'city' => 'City',
            'country' => 'Country',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'mobile_number' => 'Mobile number',
            'landline_number' => 'Landline number',
            'whatsapp_number' => 'Whatsapp number',
            'youtube_link' => 'Youtube link',
            
        ],
    ],

    'specification' => [
        'title' => 'Specifications',

        'actions' => [
            'index' => 'Specifications',
            'create' => 'New Specification',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'parent_id' => 'Parent',
            'ad_type' => 'Ad type',
            'external_id' => 'External',
            'external_updated_at' => 'External updated at',
            
        ],
    ],

    'translation' => [
        'title' => 'Translations',

        'actions' => [
            'index' => 'Translations',
            'create' => 'New Translation',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'locale_id' => 'Locale',
            'translation_key' => 'Translation key',
            'translation_value' => 'Translation value',
            'resource_id' => 'Resource',
            
        ],
    ],

    'trim' => [
        'title' => 'Trims',

        'actions' => [
            'index' => 'Trims',
            'create' => 'New Trim',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'model_id' => 'Model',
            'series_id' => 'Series',
            'production_year_start' => 'Production year start',
            'production_year_end' => 'Production year end',
            'is_active' => 'Is active',
            'ad_type' => 'Ad type',
            'external_id' => 'External',
            'external_updated_at' => 'External updated at',
            
        ],
    ],

    'trim-specification' => [
        'title' => 'Trim Specifications',

        'actions' => [
            'index' => 'Trim Specifications',
            'create' => 'New Trim Specification',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'trim_id' => 'Trim',
            'specification_id' => 'Specification',
            'value' => 'Value',
            'unit' => 'Unit',
            'ad_type' => 'Ad type',
            'external_id' => 'External',
            'external_updated_at' => 'External updated at',
            
        ],
    ],

    'truck-ad' => [
        'title' => 'Truck Ads',

        'actions' => [
            'index' => 'Truck Ads',
            'create' => 'New Truck Ad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'ad_id' => 'Ad',
            'make_id' => 'Make',
            'custom_make' => 'Custom make',
            'model' => 'Model',
            'truck_type' => 'Truck type',
            'fuel_type_id' => 'Fuel type',
            'vehicle_category_id' => 'Vehicle category',
            'transmission_type_id' => 'Transmission type',
            'cab' => 'Cab',
            'construction_year' => 'Construction year',
            'first_registration_month' => 'First registration month',
            'first_registration_year' => 'First registration year',
            'inspection_valid_until_month' => 'Inspection valid until month',
            'inspection_valid_until_year' => 'Inspection valid until year',
            'owners' => 'Owners',
            'construction_height_mm' => 'Construction height mm',
            'lifting_height_mm' => 'Lifting height mm',
            'lifting_capacity_kg_m' => 'Lifting capacity kg m',
            'permanent_total_weight_kg' => 'Permanent total weight kg',
            'allowed_pulling_weight_kg' => 'Allowed pulling weight kg',
            'payload_kg' => 'Payload kg',
            'max_weight_allowed_kg' => 'Max weight allowed kg',
            'empty_weight_kg' => 'Empty weight kg',
            'loading_space_length_mm' => 'Loading space length mm',
            'loading_space_width_mm' => 'Loading space width mm',
            'loading_space_height_mm' => 'Loading space height mm',
            'loading_volume_m3' => 'Loading volume m3',
            'load_capacity_kg' => 'Load capacity kg',
            'operating_weight_kg' => 'Operating weight kg',
            'operating_hours' => 'Operating hours',
            'axes' => 'Axes',
            'wheel_formula' => 'Wheel formula',
            'hydraulic_system' => 'Hydraulic system',
            'seats' => 'Seats',
            'mileage' => 'Mileage',
            'power_kw' => 'Power kw',
            'emission_class' => 'Emission class',
            'fuel_consumption' => 'Fuel consumption',
            'co2_emissions' => 'Co2 emissions',
            'condition' => 'Condition',
            'interior_color' => 'Interior color',
            'exterior_color' => 'Exterior color',
            'price' => 'Price',
            'price_contains_vat' => 'Price contains vat',
            'dealer_id' => 'Dealer',
            'dealer_show_room_id' => 'Dealer show room',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email_address' => 'Email address',
            'address' => 'Address',
            'zip_code' => 'Zip code',
            'city' => 'City',
            'country' => 'Country',
            'mobile_number' => 'Mobile number',
            'landline_number' => 'Landline number',
            'whatsapp_number' => 'Whatsapp number',
            'youtube_link' => 'Youtube link',
            
        ],
    ],

    'user' => [
        'title' => 'Users',

        'actions' => [
            'index' => 'Users',
            'create' => 'New User',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'mobile_number' => 'Mobile number',
            'landline_number' => 'Landline number',
            'whatsapp_number' => 'Whatsapp number',
            'email' => 'Email',
            'email_verified_at' => 'Email verified at',
            'password' => 'Password',
            'dealer_id' => 'Dealer',
            
        ],
    ],

    'users-favourite-ad' => [
        'title' => 'Users Favourite Ads',

        'actions' => [
            'index' => 'Users Favourite Ads',
            'create' => 'New Users Favourite Ad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            
        ],
    ],

    'users-favourite-ad-search' => [
        'title' => 'Users Favourite Ad Searches',

        'actions' => [
            'index' => 'Users Favourite Ad Searches',
            'create' => 'New Users Favourite Ad Search',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            
        ],
    ],

    'user-role' => [
        'title' => 'User Roles',

        'actions' => [
            'index' => 'User Roles',
            'create' => 'New User Role',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'user_id' => 'User',
            'role_id' => 'Role',
            
        ],
    ],

    'vehicle-category' => [
        'title' => 'Vehicle Categories',

        'actions' => [
            'index' => 'Vehicle Categories',
            'create' => 'New Vehicle Category',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'internal_name' => 'Internal name',
            'slug' => 'Slug',
            'ad_type' => 'Ad type',
            
        ],
    ],

    'moto-ad-option' => [
        'title' => 'Moto Ad Options',

        'actions' => [
            'index' => 'Moto Ad Options',
            'create' => 'New Moto Ad Option',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'moto_ad_id' => 'Moto ad',
            'option_id' => 'Option',
            
        ],
    ],

    'mobile-home-ad' => [
        'title' => 'Mobile Home Ads',

        'actions' => [
            'index' => 'Mobile Home Ads',
            'create' => 'New Mobile Home Ad',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'ad_id' => 'Ad',
            'make_id' => 'Make',
            'custom_make' => 'Custom make',
            'model_id' => 'Model',
            'custom_model' => 'Custom model',
            'fuel_type_id' => 'Fuel type',
            'vehicle_category_id' => 'Vehicle category',
            'transmission_type_id' => 'Transmission type',
            'construction_year' => 'Construction year',
            'first_registration_month' => 'First registration month',
            'first_registration_year' => 'First registration year',
            'inspection_valid_until_month' => 'Inspection valid until month',
            'inspection_valid_until_year' => 'Inspection valid until year',
            'owners' => 'Owners',
            'length_cm' => 'Length cm',
            'width_cm' => 'Width cm',
            'height_cm' => 'Height cm',
            'max_weight_allowed_kg' => 'Max weight allowed kg',
            'payload_kg' => 'Payload kg',
            'engine_displacement' => 'Engine displacement',
            'mileage' => 'Mileage',
            'power_kw' => 'Power kw',
            'axes' => 'Axes',
            'seats' => 'Seats',
            'sleeping_places' => 'Sleeping places',
            'beds' => 'Beds',
            'emission_class' => 'Emission class',
            'fuel_consumption' => 'Fuel consumption',
            'co2_emissions' => 'Co2 emissions',
            'condition' => 'Condition',
            'color' => 'Color',
            'price' => 'Price',
            'price_contains_vat' => 'Price contains vat',
            'dealer_id' => 'Dealer',
            'dealer_show_room_id' => 'Dealer show room',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email_address' => 'Email address',
            'address' => 'Address',
            'zip_code' => 'Zip code',
            'city' => 'City',
            'country' => 'Country',
            'mobile_number' => 'Mobile number',
            'landline_number' => 'Landline number',
            'whatsapp_number' => 'Whatsapp number',
            'youtube_link' => 'Youtube link',
            
        ],
    ],

    'mobile-home-ad-option' => [
        'title' => 'Mobile Home Ad Options',

        'actions' => [
            'index' => 'Mobile Home Ad Options',
            'create' => 'New Mobile Home Ad Option',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'mobile_home_ad_id' => 'Mobile home ad',
            'option_id' => 'Option',
            
        ],
    ],

    'characteristic' => [
        'title' => 'Characteristics',

        'actions' => [
            'index' => 'Characteristics',
            'create' => 'New Characteristic',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            
        ],
    ],

    'sub-characteristic' => [
        'title' => 'Sub Characteristics',

        'actions' => [
            'index' => 'Sub Characteristics',
            'create' => 'New Sub Characteristic',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'characteristic_id' => 'Characteristic',
            
        ],
    ],

    'ad-sub-characteristic' => [
        'title' => 'Ad Sub Characteristics',

        'actions' => [
            'index' => 'Ad Sub Characteristics',
            'create' => 'New Ad Sub Characteristic',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'ad_id' => 'Ad',
            'sub_characteristic_id' => 'Sub characteristic',
            
        ],
    ],

    'review' => [
        'title' => 'Reviews',

        'actions' => [
            'index' => 'Reviews',
            'create' => 'New Review',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'testimony' => 'Testimony',
            'user_id' => 'User',
            'score' => 'Score',
            
        ],
    ],

    'review' => [
        'title' => 'Reviews',

        'actions' => [
            'index' => 'Reviews',
            'create' => 'New Review',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'testimony' => 'Testimony',
            'ad_id' => 'Ad',
            'user_creator_id' => 'User creator',
            'score' => 'Score',
            
        ],
    ],

    // Do not delete me :) I'm used for auto-generation
];