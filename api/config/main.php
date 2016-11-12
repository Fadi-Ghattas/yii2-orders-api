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
                } else if ($response->statusCode == 400) {
                    $response->data = [
                        'success' => false,
                        'message' => "Bad Request",
                        'data' => [['error' => $response->data['message']]]
                    ];
                }
                else if ($response->statusCode == 401) {
                    $response->data = [
                        'success' => false,
                        'message' => "Unauthorized",
                        'data' => [['error' => "You are requesting with an invalid credential"]]
                    ];
                }
//                else if ($response->statusCode == 500) {
//                    $response->data = [
//                        'success' => false,
//                        'message' => "server error",
//                        'data' => [['error' => "Something went wrong, try again later."]]
//                    ];
//                }
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
                        'GET,POST,PUT,DELETE order-status' => 'order-status',
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
                        'GET,POST,PUT,DELETE address' => 'address',
                        'GET,POST,PUT,DELETE address/{id}' => 'address',
                        'GET,POST,PUT,DELETE sms-code' => 'sms-code',
                        'GET,POST,PUT,DELETE reset-password-sms-code' => 'reset-password-sms-code',
                        'GET,POST,PUT,DELETE reset-password' => 'reset-password',
                        'GET,POST,PUT,DELETE change-password' => 'change-password',
                        'GET,POST,PUT,DELETE new-restaurant' => 'new-restaurant',
                        'GET,POST,PUT,DELETE validate-voucher' => 'validate-voucher',
                        'GET,POST,PUT,DELETE orders' => 'orders',
                        'GET,POST,PUT,DELETE orders/{id}' => 'orders',
                        'GET,POST,PUT,DELETE reviews' => 'reviews',
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\w+>'
                    ]
                ],
            ],
        ]
    ],
    'params' => $params,
];