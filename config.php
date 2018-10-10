<?php

return [
    'connection' => [
        'host'=>'mysql:host=127.0.0.1',
        'database'=>'2simplify-rev',
        'username'=>'root',
        'password'=>'ajidb',
        'option'=>[],
    ],
    
    'salt' => '16101993',

    //related to mail configuration
    /* 'host' => 'srv21.niagahoster.com',
    'smtpAuth' => true,
    'username' => 'aji@sentranetcomindo.com',
    'password' => 'aji123',
    'smtpSecure' => 'ssl',
    'port' => 465, */

    'host' => 'smtp.gmail.com',
    'smtpAuth' => true,
    'username' => 'mahfudz.aji16@gmail.com',
    'password' => 'pancasila',
    'smtpSecure' => 'ssl',
    'port' => 465,

    //messages
    'message' => [
        'errorColor' => 'red',
        'successColor' => 'green',
    ],

    'maxDataInAPage' => 20,
    
    'maxPages' => 10,

    'recaptcha' => [
        'development' => [
            'siteKey' => '6LeYU0gUAAAAANcDT5k3qBt3xSabPl19-oLnVrt_',
            'secretKey' => '6LeYU0gUAAAAAHmK7awvdRB_P4VRA3GgR0SD_8Pv',
            'lang' => 'id'
        ],
        'production' => [
            'siteKey' => '',
            'secretKey' => '',
            'lang' => 'en'
        ]
    ]

]

?>