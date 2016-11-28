<?php
	/**
	 * Created by PhpStorm.
	 * User: Fadi
	 * Date: 8/24/2016
	 * Time: 11:03 PM
	 */

	namespace api\modules\v1\controllers;

	use common\models\Orders;
	use common\models\OrderStatus;
	use Yii;
	use common\models\Countries;
	use common\models\States;
	use common\helpers\Helpers;
	use common\emails\EmailHandler;
	use yii\rest\ActiveController;
	use yii\filters\auth\CompositeAuth;
	use yii\filters\auth\HttpBearerAuth;
	use yii\web\MethodNotAllowedHttpException;
	use yii\filters\Cors;

	
	class CommonController extends ActiveController
	{
		public $modelClass = '';

		public function behaviors()
		{
			$behaviors = parent::behaviors();
			$behaviors['corsFilter'] = [
	                'class' => Cors::className(),
		            'cors' => [
		                // restrict access to
		                'Origin' => ['*'],
		                'Access-Control-Request-Method' => ['POST', 'GET'],
		                // Allow only POST and PUT methods
		                'Access-Control-Request-Headers' => [' X-Requested-With'],
		                // Allow only headers 'X-Wsse'
		                'Access-Control-Allow-Credentials' => true,
		                // Allow OPTIONS caching
		                'Access-Control-Max-Age' => 3600,
		                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
		                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
		            ],
	        ];

			$behaviors['authenticator'] = [
				'class' => CompositeAuth::className(),
				'except' => ['states', 'countries'],
				'authMethods' => [
					HttpBearerAuth::className(),
				],
			];

			return $behaviors;
		}

		public function actionCountries()
		{
			$request = Yii::$app->request;
			$get_data = $request->get();

			if ($request->isGet) {
				if (empty($get_data))
					return Countries::getCountries();
			}

			throw new MethodNotAllowedHttpException("Method Not Allowed");
		}

		public function actionStates()
		{
			$request = Yii::$app->request;
			$get_data = $request->get();

			if ($request->isGet) {
				if (empty($get_data))
					return States::getStates();
			}

			throw new MethodNotAllowedHttpException("Method Not Allowed");
		}

		public function actionOrderStatus()
		{
			$request = Yii::$app->request;
			$get_data = $request->get();

			if ($request->isGet) {
				if (empty($get_data))
					return OrderStatus::getOrderStatus();
			}

			throw new MethodNotAllowedHttpException("Method Not Allowed");
		}


		public function actionEmailWebsite()
		{	
			$request = Yii::$app->request;
			

			if($request->isPost){

				$post_data = $request->post();

				if (!isset($post_data['name']) ||
					!isset($post_data['email']) ||
					!isset($post_data['number']) ||
					!isset($post_data['message']) 
				)
				return Helpers::HttpException(422, 'validation failed', ['error' => "data are missing"]);
				
				extract($post_data);
				if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
				  return Helpers::HttpException(422, 'validation failed' , ['error' => "$email is not a valid email address"]);
				} 

				$_SERVER['HTTP_REFERER'] = 'http://dev.foodtime/#contact';

				$allowedDomains = array('foodtime.asia', 'dev.foodtime');
				$referer = $_SERVER['HTTP_REFERER'];

				$domain = parse_url($referer); //If yes, parse referrer

				if(in_array( $domain['host'], $allowedDomains)) {
					
					EmailHandler::sendEmailWebsite($email,$name,$number,$message);
					return Helpers::formatResponse(TRUE, 'Thank you for your enquiry. Our experts will get back to you shortly via phone or email.', NULL);

				}else{
					return Helpers::HttpException(403, "Forbidden", ['error' => "The request is understood, but it has been refused or access is not allowed."]);
				}



			}
			
			return Helpers::HttpException(405, "Method Not Allowed", NULL);
			
		}


		public function beforeAction($event)
		{
			$request_action = explode('/', Yii::$app->getRequest()->getUrl());
			$actions = [
				'countries' => ['GET'],
				'states' => ['GET'],
				'order-status' => ['GET'],
				'email-website' => ['POST'],
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