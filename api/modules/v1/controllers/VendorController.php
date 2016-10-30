<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/10/2016
 * Time: 6:05 AM
 */


//https://api.jommakan.asia/

namespace api\modules\v1\controllers;


use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use common\helpers\Helpers;
use common\models\User;
use common\models\LoginForm;
use common\models\Restaurants;
use common\models\MenuCategories;
use common\models\Addons;
use common\models\ItemChoices;
use common\models\BlacklistedClients;
use common\models\Reviews;
use common\models\MenuItems;
use common\models\Orders;

class VendorController extends ActiveController
{
	public $modelClass = 'common\models\Restaurants';

	public function behaviors()
	{
		$behaviors = parent::behaviors();
		$behaviors['authenticator'] = [
			'class' => CompositeAuth::className(),
			'except' => ['login'],
			'authMethods' => [
				HttpBearerAuth::className(),
			],
		];

		return $behaviors;
	}

	public function actions()
	{
		$actions = parent::actions();
		unset($actions['create']);
		unset($actions['update']);
		unset($actions['delete']);
		unset($actions['view']);
		unset($actions['index']);
		unset($actions['options']);
		return $actions;
	}

	public function actionLogin()
	{
		$post_data = Yii::$app->request->post();

		if (!isset($post_data['email']) || !isset($post_data['password']))
			return Helpers::HttpException(422, 'validation failed', ['error' => 'email and password are required for login']);

		$restaurantManager = User::findByEmail($post_data['email']);
		if (empty($restaurantManager))
			return Helpers::HttpException(404, 'not found', ['error' => 'user not found']);
		if (User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
			return Helpers::HttpException(403, "forbidden", ['error' => "This account is not a restaurant account"]);
		if ($restaurantManager->password_hash != $post_data['password'])
			return Helpers::HttpException(422, 'validation failed', ['error' => "Password is in incorrect"]);
		if (!Restaurants::find()->where(['user_id' => $restaurantManager->id])->one()->status)
			return Helpers::HttpException(403, "forbidden", ['error' => "This account is deactivated"]);
		if (!is_null($restaurantManager->last_logged_at))
			return Helpers::HttpException(403, "forbidden", ['error' => "You already logged in"]);

		$model = new LoginForm();
		$model->username = $post_data['email'];
		$model->password = $post_data['password'];
		$model->email = $post_data['email'];
		if ($model->load($post_data, '') && $model->login()) {
			try {
				$restaurantManager->last_logged_at = date('Y-m-d H:i:s');;
				if ($restaurantManager->save(FALSE))
					return Helpers::formatResponse(TRUE, 'login success', ['auth_key' => $restaurantManager['auth_key']]);
			} catch (\Exception $e) {
				return Helpers::HttpException(422, 'login failed', ['error' => 'something went wrong please try again..']);
//                throw $e;
			}
		} else {
			return Helpers::HttpException(422, $model->errors, NULL);
		}
		return Helpers::HttpException(422, 'validation failed', ['error' => 'Please provide valid data']);
	}

	public function actionLogout()
	{
		$post_data = Yii::$app->request->post();
		$headers = Yii::$app->getRequest()->getHeaders();

		if (empty($post_data))
			return Helpers::HttpException(422, 'validation failed', ['error' => 'Please provide data']);

		if (!isset($post_data['email']) || !isset($post_data['password']))
			return Helpers::HttpException(422, 'validation failed', ['error' => 'The email and password are required']);

		$restaurantManager = User::findIdentityByAccessToken(explode(' ', $headers['authorization'])[1]);
		if (empty($restaurantManager))
			return Helpers::HttpException(404, 'not found', ['error' => 'user not found']);

		if ($post_data['email'] != $restaurantManager->email || $post_data['password'] != $restaurantManager->password_hash)
			return Helpers::HttpException(422, 'validation failed', ['error' => 'The email or password incorrect']);

		if (User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
			return Helpers::HttpException(403, "This account is not a restaurant account", NULL);

//        $user = User::findOne($restaurantManager->id);
		$restaurants = Restaurants::find()->where(['user_id' => $restaurantManager->id])->one();
		$restaurants->action = 'logout';

		$transaction = Restaurants::getDb()->beginTransaction();

		try {
//            $user->password_hash = 'RESTAURANT_DEACTIVATED';
//            $user->save(false);
			$restaurantManager->last_logged_at = NULL;
			$restaurantManager->save(FALSE);
			$restaurants->status = 0;
			$restaurants->logout_at = date('Y-m-d H:i:s');
			$restaurants->save(FALSE);
			$transaction->commit();
			return Helpers::formatResponse(TRUE, 'You have been logged out successful', NULL);
		} catch (\Exception $e) {
			$transaction->rollback();
			return Helpers::HttpException(422, 'logout failed', ['error' => 'something went wrong please try again..']);
//            throw $e;
		}
	}

	public function actionProfile()
	{
		$request = Yii::$app->request;
		$get_data = $request->get();

		if ($request->isGet && empty($get_data)) {
			return Helpers::formatResponse(TRUE, 'get success', Restaurants::checkRestaurantAccess());
		} else if ($request->isPut && empty($get_data)) {
			if (empty($request->post()))
				return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
			return Restaurants::updateRestaurant($request->post());
		}
		return Helpers::HttpException(405, "Method Not Allowed", NULL);
	}

	public function actionMenu()
	{
		$request = Yii::$app->request;
		$get_data = $request->get();

		if ($request->isGet) {
			if (empty($get_data))
				return MenuCategories::getMenuCategories();
			else if (!empty($get_data) && isset($get_data['id']))
				return MenuCategories::getMenuCategoryItemsResponse($get_data['id']);
		} else if ($request->isPost && empty($get_data)) {
			if (empty($request->post()))
				return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
			return MenuCategories::createCategory($request->post());
		} else if ($request->isPut && !empty($get_data)) {
			if (empty($request->post()))
				return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
			return MenuCategories::updateCategory($get_data['id'], $request->post());
		} else if ($request->isDelete && !empty($get_data)) {
			return MenuCategories::deleteCategory($get_data['id']);
		}

		return Helpers::HttpException(405, "Method Not Allowed", NULL);
	}

	public function actionAddOn()
	{
		$request = Yii::$app->request;
		$get_data = $request->get();

		if ($request->isGet) {
			if (empty($get_data))
				return Addons::getRestaurantAddOns();
			else if (!empty($get_data) && isset($get_data['id']))
				return Addons::getRestaurantAddOn($get_data['id']);
		} else if ($request->isPost && empty($get_data)) {
			if (empty($request->post()))
				return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
			return Addons::createAddOn($request->post());
		} else if ($request->isPut && !empty($get_data)) {
			if (empty($request->post()))
				return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
			return Addons::updateAddOn($get_data['id'], $request->post());
		} else if ($request->isDelete && !empty($get_data)) {
			return Addons::deleteAddOn($get_data['id']);
		}

		return Helpers::HttpException(405, "Method Not Allowed", NULL);
	}

	public function actionItemChoices()
	{
		$request = Yii::$app->request;
		$get_data = $request->get();

		if ($request->isGet) {
			if (empty($get_data))
				return ItemChoices::getRestaurantItemsChoices();
			else if (!empty($get_data) && isset($get_data['id']))
				return ItemChoices::getRestaurantItemChoice($get_data['id']);
		} else if ($request->isPost && empty($get_data)) {
			if (empty($request->post()))
				return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
			return ItemChoices::createItemChoice($request->post());
		} else if ($request->isPut && !empty($get_data)) {
			if (empty($request->post()))
				return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
			return ItemChoices::updateItemChoice($get_data['id'], $request->post());
		} else if ($request->isDelete && !empty($get_data)) {
			return ItemChoices::deleteItemChoice($get_data['id']);
		}

		return Helpers::HttpException(405, "Method Not Allowed", NULL);
	}

	public function actionBlacklistedClients()
	{
		$request = Yii::$app->request;
		$get_data = $request->get();

		if ($request->isGet) {
			if (empty($get_data))
				return BlacklistedClients::getRestaurantBlacklistedClients();
		} else if ($request->isPost && empty($get_data)) {
			if (empty($request->post()))
				return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
			return BlacklistedClients::createItemBlacklistedClient($request->post());
		} else if ($request->isDelete && !empty($get_data)) {
			return BlacklistedClients::deleteBlacklistedClient($get_data['id']);
		}

		return Helpers::HttpException(405, "Method Not Allowed", NULL);
	}

	public function actionMenuItems()
	{

		$request = Yii::$app->request;
		$get_data = $request->get();

		if ($request->isGet) {
			if (!empty($get_data) && isset($get_data['id']))
				return MenuItems::getRestaurantMenuItem($get_data['id']);
		} else if ($request->isPost && empty($get_data)) {
			if (empty($request->post()))
				return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
			return MenuItems::createMenuItem($request->post());
		} else if ($request->isPut && !empty($get_data)) {
			if (empty($request->post()))
				return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
			return MenuItems::updateMenuItem($get_data['id'], $request->post());
		} else if ($request->isDelete && !empty($get_data)) {
			return MenuItems::deleteMenuItem($get_data['id']);
		}

		return Helpers::HttpException(405, "Method Not Allowed", NULL);
	}

	public function actionReviews()
	{
		$request = Yii::$app->request;
		$get_data = $request->get();

		if ($request->isGet) {
			if (empty($get_data))
				return Reviews::getReviews();
		}

		return Helpers::HttpException(405, "Method Not Allowed", NULL);
	}

	public function actionOrders()
	{
		$request = Yii::$app->request;
		$get_data = $request->get();

		if ($request->isGet) {
			if (!isset($get_data['id']))
				return Orders::getOrders();
			else if (!empty($get_data) && isset($get_data['id']))
				return Orders::getRestaurantOrder($get_data['id']);
		} else if ($request->isPut && !empty($get_data)) {
			if (empty($request->post()))
				return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
			return Orders::updateRestaurantOrderStatus($get_data['id'], $request->post());
		}

		return Helpers::HttpException(405, "Method Not Allowed", NULL);
	}

	public function beforeAction($event)
	{
		$request_action = explode('/', Yii::$app->getRequest()->getUrl());
		$actions = [
			'login' => ['POST'],
			'logout' => ['POST'],
			'menu' => ['GET', 'PUT', 'POST', 'DELETE'],
			'profile' => ['GET', 'PUT'],
			'add-on' => ['GET', 'PUT', 'POST', 'DELETE'],
			'item-choices' => ['GET', 'PUT', 'POST', 'DELETE'],
			'blacklisted-clients' => ['GET', 'POST', 'DELETE'],
			'reviews' => ['GET'],
			'menu-items' => ['GET', 'PUT', 'POST', 'DELETE'],
			'orders' => ['GET', 'PUT'],
		];

		foreach ($actions as $action => $verb) {
			if (in_array($action, $request_action)) {
				if (!in_array(Yii::$app->getRequest()->getMethod(), $actions[$action]))
					return Helpers::HttpException(405, "Method Not Allowed", NULL);
			}
		}
		return parent::beforeAction($event);
	}
}