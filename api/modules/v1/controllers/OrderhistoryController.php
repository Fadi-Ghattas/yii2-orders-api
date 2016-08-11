<?php
/**
 * Created by PhpStorm.
 * User: yousef
 * Date: 23/06/16
 * Time: 12:34 PM
 */

namespace api\modules\v1\controllers;


use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;


/**
 * Class OrderhstoryController
 * @Orderhstory api\modules\v1\controllers
 * @link api/web/v1/Orderhstory
 */
class OrderhistoryController extends ActiveController
{
    public $modelClass = 'common\models\OrderHistory';

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