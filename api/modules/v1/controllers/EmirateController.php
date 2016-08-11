<?php
/*
 * Created by PhpStorm.
 * User: Ahmad
 * Date: 2016-06-12
 * Time: 4:34 PM
 */

namespace api\modules\v1\controllers;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;

/**
 * Class EmirateController
 * @package api\modules\v1\controllers
 * @link api/web/v1/emirate
 */
class EmirateController extends ActiveController
{
    public $modelClass = 'common\models\Emirate';

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'except' => ['index'],
                    'authMethods' => [
                        HttpBearerAuth::className(),
                    ],
                ],
            ]
        );
    }

}