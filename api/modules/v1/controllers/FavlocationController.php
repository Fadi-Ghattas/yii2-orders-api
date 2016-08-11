<?php
/*
 * Created by PhpStorm.
 * User: Ahmad
 * Date: 2016-06-12
 * Time: 4:34 PM
 */

namespace api\modules\v1\controllers;

use common\models\Favlocation;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;

/**
 * Class FavlocationController
 * @package api\modules\v1\controllers
 * @link api/web/v1/Favlocation
 */
class FavlocationController extends ActiveController
{
    public $modelClass = 'common\models\Favlocation';

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
    public function actionMy()
    {
        return Favlocation::getMyLocations();
    }
}