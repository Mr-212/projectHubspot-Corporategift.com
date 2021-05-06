<?php
$redirect_url = 'https://corporategift.dev-techloyce.com/hupspot-authentication';

$auth_url = "https://app.hubspot.com/oauth/authorize?client_id=339cbfeb-f042-4523-b862-b8bf61647c81&redirect_uri=".$redirect_url."&scope=contacts%20timeline%20business-intelligence%20oauth%20forms%20files%20integration-sync%20forms-uploaded-files%20crm.import&optional_scope=content%20reports%20social%20automation%20actions%20transactional-email%20tickets&state=y";


return [

    'hubspot' => [
        'client_id' => '339cbfeb-f042-4523-b862-b8bf61647c81',
        'client_secret' => '0f4f32da-cf89-42ee-b3dd-e0a3e478b370',
        'auth_url' => $auth_url,
        'redirect_uri' => $redirect_url,
        'version' => 'v1',
        'api_url' =>'https://api.hubapi.com',
    ],
   
    'cg_settings' => [
        'token' => '4574|HVSfrDHblEqW7K612JFxaITk8D29wqEUx7UWgt8f',
        'domain_uri' => 'https://api.development.corporategift.com'
    ],

];