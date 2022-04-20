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

    // Do not delete me :) I'm used for auto-generation
];