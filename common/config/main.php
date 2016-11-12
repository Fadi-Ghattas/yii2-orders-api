<?php
//use common\models\Setting;
//use common\models\SettingsForm;

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'twillio' => [
            'class' => 'bryglen\twillio\Twillio',
            'sid' => 'AC6be9ed5be2fd7a4c51c4a91a1d0f89ba',
            'token' => '9d5b4295f9dc4e7521249aa1905928bf',
        ],
        'formatter' => [
            'dateFormat' => 'd-M-Y',
            'datetimeFormat' => 'd-M-Y H:i:s',
            'timeFormat' => 'H:i:s',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['user'],

        ],
    ],
    'modules' => [
        'rbac' =>  [
            'class' => 'johnitvn\rbacplus\Module'
        ], 'gridview' =>  [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ]
    ]
];