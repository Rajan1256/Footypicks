<?php

return array(

    'ios'     => array(
        // 'environment' =>'development',
        'environment' =>'production',
        // 'certificate' => base_path('push.pem'),
        // 'passPhrase'  =>'fotty',
        'certificate' => base_path('production.pem'),
        'passPhrase'  =>'welcome',
        'service'     =>'apns'
    ),
    'appNameAndroid' => array(
        'environment' =>'production',
        'apiKey'      =>'yourAPIKey',
        'service'     =>'gcm'
    )

);