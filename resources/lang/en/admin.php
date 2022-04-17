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

    // Do not delete me :) I'm used for auto-generation
];