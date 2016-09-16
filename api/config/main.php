<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'jommakan-api',
    'name' => 'jommakan',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class' => 'api\modules\v1\Module'
        ]
    ],
    'components' => [
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if($response->format == 'html'){
                    $response->format = \yii\web\Response::FORMAT_JSON;
                    $response->data = [
                        'success' => false,
                        'message' => $response->statusText,
                        'data' => null
                    ];
                } else if ($response->statusCode == 401) {
                    $response->data = [
                        'success' => false,
                        'message' => "Unauthorized",
                        'data' => [['error' =>"You are requesting with an invalid credential"]]
                    ];
                }
            },
        ],
        'formatter' => [
            'datetimeFormat' => 'php:d/m/Y H:i:s',
            'dateFormat' => 'php:d-m-Y',
            'timeFormat' => 'php:H:i:s',
            'decimalSeparator' => '.',
            'thousandSeparator' => ' ',
            'timeZone' => 'UTC'
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'urlManagerFrontEnd' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => '/',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/vendor'],
                    'extraPatterns' => [
                        'GET,POST,PUT,DELETE login' => 'login',
                        'GET,POST,PUT,DELETE logout' => 'logout',
                        'GET,POST,PUT,DELETE profile' => 'profile',
                        'GET,POST,PUT,DELETE profile/{id}' => 'profile',
                        'GET,POST,PUT,DELETE menu' => 'menu',
                        'GET,POST,PUT,DELETE menu/{id}' => 'menu',
                        'GET,POST,PUT,DELETE add-on' => 'add-on',
                        'GET,POST,PUT,DELETE add-on/{id}' => 'add-on',
                        'GET,POST,PUT,DELETE item-choices' => 'item-choices',
                        'GET,POST,PUT,DELETE item-choices/{id}' => 'item-choices',
                        'GET,POST,PUT,DELETE blacklisted-clients' => 'blacklisted-clients',
                        'GET,POST,PUT,DELETE blacklisted-clients/{id}' => 'blacklisted-clients',
                        'GET,POST,PUT,DELETE reviews' => 'reviews',
                        'GET,POST,PUT,DELETE menu-items' => 'menu-items',
                        'GET,POST,PUT,DELETE menu-items/{id}' => 'menu-items',
                        'GET,POST,PUT,DELETE orders' => 'orders',
                        'GET,POST,PUT,DELETE orders/{id}' => 'orders',
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\w+>'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/common'],
                    'extraPatterns' => [
                        'GET,POST,PUT,DELETE countries' => 'countries',
                        'GET,POST,PUT,DELETE states' => 'states',
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\w+>'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/client'],
                    'extraPatterns' => [
                        'GET,POST,PUT,DELETE sign-up' => 'sign-up',
                        'GET,POST,PUT,DELETE log-in' => 'log-in',
                        'GET,POST,PUT,DELETE log-out' => 'log-out',
                        'GET,POST,PUT,DELETE restaurants' => 'restaurants',
                        'GET,POST,PUT,DELETE restaurants/{id}' => 'restaurants',
                        'GET,POST,PUT,DELETE menu-items/{id}' => 'menu-items',
                        'GET,POST,PUT,DELETE cuisines' => 'cuisines',
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\w+>'
                    ]
                ],
//                [
//                    'class' => 'yii\rest\UrlRule',
//                    'controller' => ['v1/vendor/menu'],
//                    'extraPatterns' => [
//                        'POST' => 'create', // 'xxxxx' refers to 'actionXxxxx'
//                        'PUT {id}' => 'update',
//                        'PATCH {id}' => 'update',
//                        'DELETE {id}' => 'delete',
//                        'GET {id}' => 'view',
//                        'GET' => 'index',
//                    ],
//                    'tokens' => [
//                        '{id}' => '<id:\\w+>'
//                    ]
//                ],
            ],
        ]
    ],
    'params' => $params,
];



