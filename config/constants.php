<?php
$redirect_url = url('/hupspot-authentication');
return [

    'hubspot' => [

        'client_id' => '339cbfeb-f042-4523-b862-b8bf61647c81',
        'client_secret' => '0f4f32da-cf89-42ee-b3dd-e0a3e478b370',
        'auth_url' => 'https://app.hubspot.com/oauth/authorize?client_id=339cbfeb-f042-4523-b862-b8bf61647c81&redirect_uri=https://corporategift.dev-techloyce.com/hupspot-authentication&scope=contacts%20content%20automation%20tickets',
        'redirect_uri' => $redirect_url,
        'version' => 'v1',
    ],
   
    'cg_settings' => [
        'token' => '4574|HVSfrDHblEqW7K612JFxaITk8D29wqEUx7UWgt8f',
        'domain_uri' => 'https://api.development.corporategift.com'
    ],

];