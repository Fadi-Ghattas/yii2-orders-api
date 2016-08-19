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
                } else if ($response->statusCode != 200 && $response->statusCode != 422) {
                    $response->data = [
                        'success' => false,
                        'message' => $response->data['message'],
                        'data' => null
                    ];
                }
            },
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
                        'GET,POST,PUT,DELETE menu' => 'menu',
                        'GET,POST,PUT,DELETE menu/{id}' => 'menu',

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



