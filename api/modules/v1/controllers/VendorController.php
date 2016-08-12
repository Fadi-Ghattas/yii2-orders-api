<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/10/2016
 * Time: 6:05 AM
 */

namespace api\modules\v1\controllers;

use common\models\Restaurants;
use Yii;
use common\models\User;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use yii\base\InvalidValueException;
use yii\db\IntegrityException;
use yii\helpers\Html;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\base\UserException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\NotFoundHttpException;

class VendorController extends ActiveController
{
    public $modelClass = 'common\models\Restaurants';


    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'except' => ['login'],
                    'authMethods' => [
                        HttpBearerAuth::className(),
                    ],
                ],
            ]
        );
    }

    public function actionLogin()
    {
        $post_data = Yii::$app->request->post();
        $model = new LoginForm();
        $model->username = $post_data['email'];
        if ($model->load($post_data, '') && $model->login()) {
            $restaurantManager = User::findByEmail($post_data['email']);
            if(empty($restaurantManager))
                throw new NotFoundHttpException('User not found.');
            if(User::getRoleName(Yii::$app->user->id) != User::RESTAURANT_MANAGER)
                throw new ForbiddenHttpException('This account is not a restaurant account.');

            return ['auth_key' => $restaurantManager['auth_key']];

        } else {
            throw new UserException(strip_tags(Html::errorSummary($model, ['header' => '', 'footer' => ''])));
        }
        throw new UserException('Please provide valid data.');
    }

    public function actionLogout()
    {
        $post_data = Yii::$app->request->post();
        $headers = Yii::$app->getRequest()->getHeaders();
        $restaurantManager = User::findIdentityByAccessToken(explode(' ',$headers['authorization'])[1]);

        if(empty($restaurantManager))
            throw new NotFoundHttpException('User not found.');
        if(User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
            throw new ForbiddenHttpException('This account is not a restaurant account.');
        if($post_data['password'] != $restaurantManager['password_hash'])
            throw new UserException('The password incorrect.');

        $user = User::findOne($restaurantManager->id);
        $restaurants = Restaurants::find(['=','user_id',$restaurantManager->id])->one();

        $transaction = User::getDb()->beginTransaction();

        try {
            $user->password_hash = 'RESTAURANT_DEACTIVATED';
            $user->save(false);
            $restaurants->status = 0;
            $restaurants->save(false);
            $transaction->commit();
            return ['status' => 'true'];
        } catch(\Exception $e) {
            $transaction->rollback();
            throw new ServerErrorHttpException('Something went wrong please try again..');
            //throw $e;
        }
    }

    public function afterAction($action, $result)
    {
        $response = array();
        $result = parent::afterAction($action, $result);

        switch ($action->id) {
            case 'view':
                $response['status'] = 'success';
                $response['message'] = 'Restaurant profile has been retrieved successfully.';
                $response['data'] = $result;
                break;
            case 'update':
                $response['status'] = 'success';
                $response['message'] = 'Restaurant profile has been updated successfully.';
                $response['data']['updated'] = 'true';
                break;
            case 'login':
                $response['status'] = 'success';
                $response['message'] = 'Restaurant user has been logged in successfully.';
                $response['data'] = $result;
                break;
            default:
                $response['status'] = 'error';
                $response['message'] = "You don't have permission to do this action.";
                $response['data']['error'] = 'true';
        }

        return $response;
    }
}