<?php
return [
    'backend' => [
        'frontName' => 'vbke'
    ],
    'crypt' => [
        'key' => 'b527a807e42131b0508ae9e97fe2063c'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'magento',
                'username' => 'voltbike',
                'password' => 'Br4529!Rz',
                'active' => '1'
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'files'
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'config_webservice' => 1,
        'translate' => 1,
        'compiled_config' => 1,
        'vertex' => 1
    ],
    'install' => [
        'date' => 'Mon, 08 Oct 2018 04:57:06 +0000'
    ],
    'downloadable_domains' => [
        'www.voltbike.com'
    ]
];
