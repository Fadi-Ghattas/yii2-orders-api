<?php


$config = [

    'components' => [
        'urlManagerFrontEnd' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => '/jommakan/',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ]
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
//    $config['bootstrap'][] = 'debug';
//    $config['modules']['debug'] = 'yii\debug\Module';
}

return $config;
