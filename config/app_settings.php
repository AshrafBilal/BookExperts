<?php

return [
    
    // All the sections for the settings page
    'sections' => [
        'app' => [
            'title' => 'General Settings',
            'descriptions' => 'Application general settings.', // (optional)
            'icon' => 'fa fa-cog', // (optional)
            
            'inputs' => [
                [
                    'name' => 'app_name', // unique key for setting
                    'type' => 'text', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                    'label' => 'App Name', // label for input
                    // optional properties
                    'placeholder' => 'Application Name', // placeholder for input
                    'class' => 'form-control', // override global input_class
                    'style' => '', // any inline styles
                    'rules' => 'required|min:2|max:30', // validation rules for this input
                    'value' => 'Just Say What', // any default value
                    'hint' => 'You can set the app name here' // help block text for input
                ],
                
                [
                    'name' => 'googe_api_key', // unique key for setting
                    'type' => 'text', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                    'label' => 'Google API Key', // label for input
                    // optional properties
                    'placeholder' => 'Google API Key', // placeholder for input
                    'class' => 'form-control', // override global input_class
                    'style' => '', // any inline styles
                    'rules' => 'sometimes|nullable|min:5|max:250', // validation rules for this input
                    'value' => '', // any default value
                    'hint' => 'You can set the Google API Key here' // help block text for input
                ],
                [
                    'name' => 'twilio_sid', // unique key for setting
                    'type' => 'text', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                    'label' => 'Twilio SID', // label for input
                    // optional properties
                    'placeholder' => 'Twilio SID', // placeholder for input
                    'class' => 'form-control', // override global input_class
                    'style' => '', // any inline styles
                    'rules' => 'sometimes|nullable|min:5|max:250', // validation rules for this input
                    'value' => 'AC272dc14808a7ba9c081286903ecf49e9', // any default value
                    'hint' => 'You can set the Twilio SID Key here' // help block text for input
                ],
                [
                    'name' => 'twilio_auth_token', // unique key for setting
                    'type' => 'text', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                    'label' => 'Twilio Auth Token', // label for input
                    // optional properties
                    'placeholder' => 'Twilio Auth Token', // placeholder for input
                    'class' => 'form-control', // override global input_class
                    'style' => '', // any inline styles
                    'rules' => 'sometimes|nullable|min:5|max:250', // validation rules for this input
                    'value' => 'c66c5a37f6a0d55e4140e5b0c38aeee7', // any default value
                    'hint' => 'You can set the Twilio Auth Token here' // help block text for input
                ],
                [
                    'name' => 'fcm_server_key', // unique key for setting
                    'type' => 'text', // type of input can be text, number, textarea, select, boolean, checkbox etc.
                    'label' => 'FCM server key', // label for input
                    // optional properties
                    'placeholder' => 'FCM server key', // placeholder for input
                    'class' => 'form-control', // override global input_class
                    'style' => '', // any inline styles
                    'rules' => 'sometimes|nullable|min:5|max:250', // validation rules for this input
                    'value' => '', // any default value
                    'hint' => 'You can set the FCM server key here' // help block text for input
                ],
                [
                    'name' => 'provider_nearby_radius',
                    'type' => 'number',
                    'label' => 'Provider nearby radius (KM)',
                    'data_type' => 'int',
                    'min' => 1,
                    'max' => 999999,
                    'rules' => 'required|min:1|max:999999',
                    'placeholder' => 'Provider nearby radius (KM)',
                    'class' => 'form-control',
                    // 'style' => 'color:red',
                    'value' => 50,
                    'hint' => 'You can set the Provider nearby radius (KM) for show provider in customer side.'
                ],
                [
                    'name' => 'admin_commission',
                    'type' => 'number',
                    'label' => 'Admin  Fee',
                    'data_type' => 'int',
                    'min' => 0,
                    'max' => 99,
                    'rules' => 'required|min:0|max:99',
                    'placeholder' => 'Admin  fee per booking',
                    'class' => 'form-control',
                    'value' => 0,
                    'hint' => 'You can set the Admin  fee per booking here.'
                ],
                /*  [
                 'name' => 'logo',
                 'type' => 'image',
                 'label' => 'Upload logo',
                 'hint' => 'Must be an image and cropped in desired size',
                 'rules' => 'image|max:500',
                 'disk' => 'public', // which disk you want to upload
                 'path' => 'app', // path on the disk,
                 'preview_class' => 'thumbnail',
                 'preview_style' => 'height:40px'
                 ]   */
            ]
        ],
        'email' => [
            'title' => 'Contact Us  Information',
            'descriptions' => 'How app email will be sent.',
            'icon' => 'fa fa-envelope',
            
            'inputs' => [
                [
                    'name' => 'address',
                    'type' => 'textarea',
                    'label' => 'Address',
                    'placeholder' => 'Address',
                    'rules' => 'required|max:500',
                ],
                [
                    'name' => 'contact_number',
                    'type' => 'number',
                    'label' => 'Contact Number',
                    'data_type' => 'int',
                    'placeholder' => 'Contact Number',
                    'rules' => 'required|min:6|max:16',
                ],
                [
                    'name' => 'company_number',
                    'type' => 'number',
                    'label' => 'Company Number',
                    'placeholder' => 'Company Number',
                    'rules' => 'required|min:6|max:16',
                    'value'=>'13483461'
                ],
                [
                    'name' => 'contact_email',
                    'type' => 'email',
                    'label' => 'Contact  Email',
                    'placeholder' => 'Contact email',
                    'rules' => 'required|email',
                ],
                [
                    'name' => 'facebook_url',
                    'type' => 'text',
                    'label' => 'Facebook Url',
                    'placeholder' => 'Facebook Url',
                    'rules' => 'required|url|max:500',
                ],
                [
                    'name' => 'instagram_url',
                    'type' => 'text',
                    'label' => 'Instagram Url',
                    'placeholder' => 'Instagram Url',
                    'rules' => 'required|url|max:500',
                ],
                [
                    'name' => 'twitter_url',
                    'type' => 'text',
                    'label' => 'Twitter Url',
                    'placeholder' => 'Twitter Url',
                    'rules' => 'required|url|max:500',
                ],
                [
                    'name' => 'android_customer_app_link',
                    'type' => 'text',
                    'label' => 'Android Customer APP Link',
                    'placeholder' => 'Android Customer App Link',
                    'rules' => 'sometimes|nullable|url|max:500',
                ],
                [
                    'name' => 'android_provider_app_link',
                    'type' => 'text',
                    'label' => 'Android Provider APP Link',
                    'placeholder' => 'Android Provider App Link',
                    'rules' => 'sometimes|nullable|url|max:500',
                ],
                [
                    'name' => 'ios_customer_app_link',
                    'type' => 'text',
                    'label' => 'IOS Customer APP Link',
                    'placeholder' => 'IOS Customer APP Link',
                    'rules' => 'sometimes|nullable|url|max:500',
                ],
                [
                    'name' => 'ios_provider_app_link',
                    'type' => 'text',
                    'label' => 'IOS Provider APP Link',
                    'placeholder' => 'IOS Provider APP Link',
                    'rules' => 'sometimes|nullable|url|max:500',
                ],
             
            ]
        ]
    ],
    
    // Setting page url, will be used for get and post request
    'url' => 'settings',
    
    // Any middleware you want to run on above route
    'middleware' => [
        'auth',
        'admin',
        'preventBackHistory'],
    
    // View settings
    //'setting_page_view' => 'app_settings::settings_page',
    'setting_page_view' => 'settings.index',
    
    'flash_partial' => 'app_settings::_flash',
    
    // Setting section class setting
    'section_class' => 'card mb-3',
    'section_heading_class' => 'card-header',
    'section_body_class' => 'card-body',
    
    // Input wrapper and group class setting
    'input_wrapper_class' => 'form-group',
    'input_class' => 'form-control',
    'input_error_class' => 'has-error',
    'input_invalid_class' => 'is-invalid',
    'input_hint_class' => 'form-text text-muted',
    'input_error_feedback_class' => 'text-danger',
    
    // Submit button
    'submit_btn_text' => 'Save Settings',
    'submit_success_message' => 'Settings has been saved.',
    
    // Remove any setting which declaration removed later from sections
    'remove_abandoned_settings' => false,
    
    // Controller to show and handle save setting
    'controller' => '\QCod\AppSettings\Controllers\AppSettingController',
    
    // settings group
    'setting_group' => function() {
    // return 'user_'.auth()->id();
    return 'default';
    }
    ];
