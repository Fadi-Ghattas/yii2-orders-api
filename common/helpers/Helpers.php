<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/15/2016
 * Time: 11:52 PM
 */

namespace common\helpers;


use Yii;
use yii\web\Response;
use common\models\Setting;
use common\models\SettingsForm;
use Mailgun\Mailgun;

class Helpers
{

	const ONE_SIGNAL_ACTION_ORDER = 'order';

	//web
	public static function formatResponse($success, $message, $data)
	{	

		
		// Daleen was here .... :)
		if(isset($data['error']) && is_array($data['error']) ) {

			$error  = array();
			foreach ($data['error'] as $key => $value) {
				$err['error'] = (isset($value[0]) ? $value[0] : $value );
				$error[] = $err;
			}
			$data = $error;
		}
		
		
		
		if (!isset($data[0]) && !empty($data)) {
			return ['success' => $success,
				'message' => $message,
				'data' => [$data]];
		} else {
			return ['success' => $success,
				'message' => $message,
				'data' => $data];
		}
	}

	public static function HttpException($status_code, $message, $data)
	{
		$response = \Yii::$app->getResponse();
		$response->setStatusCode($status_code);
		$response->format = Response::FORMAT_JSON;
		$response->data = self::formatResponse(FALSE, $message, $data);
		$response->send();
		die();
	}

	public static function formatJsonIdName($json)
	{
		$formatted_json = [];
		foreach ($json as $data) {
			$single_json = [];
			$single_json[$data->id] = $data->name;
			$formatted_json [] = $single_json;
		}
		return $formatted_json;
	}

	public static function formatArrayJsonIdName($json)
	{
		$formatted_json = [];
		foreach ($json as $data) {
			$single_json = [];
			$single_json[$data['id']] = $data['name'];
			$formatted_json [] = $single_json;
		}
		return $formatted_json;
	}

	public static function validateSetEmpty($values)
	{
		foreach ($values as $value) {
			if (!isset($values))
				return Helpers::HttpException(422, 'validation failed', ['error' => $value . ' is required']);
			if (empty($values))
				return Helpers::HttpException(422, 'validation failed', ['error' => $value . " can't be blank"]);
		}

		return TRUE;
	}

	public static function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$d = \DateTime::createFromFormat($format, $date);
		if (!($d && $d->format($format) == $date))
			return Helpers::HttpException(422, 'validation failed', ['error' => "date format is invalid"]);

		return TRUE;
	}

	public static function split_on($string, $num)
	{
		$length = strlen($string);
		$output[0] = substr($string, 0, $num);
		$output[1] = substr($string, $num, $length);
		return $output;
	}

	//db
	public static function linkManyToMany($relationship_model, $related_id, $new_entities, $old_entities, $entity_id, $relationship_entity_id, $restaurant_id = 0)
	{
		$models = [];
		foreach ($old_entities as $old_entity) {
			$models[$old_entity->$entity_id] = $old_entity;
		}
		$old_entities = $models;

		foreach ($new_entities as $entity) {

			if (!isset($entity['id']))
				return Helpers::HttpException(422, 'validation failed', ['error' => "add-on id is required"]);
			if (empty($entity['id']))
				return Helpers::HttpException(422, 'validation failed', ['error' => "add-on id can't be blank"]);
			if (!intval($entity['id']))
				return Helpers::HttpException(422, 'validation failed', ['error' => "add-on id must be integer"]);

			if (!array_key_exists($entity['id'], $old_entities)) {

//                if ($restaurant_id) {
//                    if (empty(Addons::getAddOn($restaurant_id, $entity['id'])))
//                        return Helpers::HttpException(422, 'validation failed', ['error' => "There add-on dos't exist"]);
//                }

				$model_entity = $relationship_model;
				$model_entity->$entity_id = $entity['id'];
				$model_entity->$relationship_entity_id = $related_id;
				$model_entity->validate();
				$model_entity->save();
			} else {
				unset($old_entities[$entity['id']]);
			}
		}

		if (!empty($old_entities))
			foreach ($old_entities as $old_entity)
				$old_entity->delete();
	}

	public static function getCountryTimeZone($country)
	{
		switch ($country) {
			case 'Malaysia':
				return 'Asia/Kuala_Lumpur';
			case 'Egypt':
				return 'Africa/Cairo';
			default:
				return 'UTC';
		}
	}

	public static function generateRandomFourDigits()
	{
		return rand(1000, 9999);
	}

	public static function sendSms($message, $number)
	{
		try {
			$number = '+' . str_replace('+', '', $number);
			$twillio = Yii::$app->twillio;
			$message = $twillio->getClient()->account->messages->sendMessage(
				Setting::getSettingValueByName(SettingsForm::TWILLIO_NUMBER),
				$number,
				$message
			);
			return $message->body;
		} catch (\Services_Twilio_RestException $e) {
			if ($e->getCode() == 21211)
				return Helpers::HttpException(422, 'validation failed', ['error' => 'phone number ' . $number . ' format is not valid.']);
			return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later or contact the admin']);
		}
		return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later or contact the admin']);
	}

	public static function getImageFileContentType($extension)
	{
		switch ($extension) {
			case 'png':
				return "image/png";
				break;
			case "jpeg":
				return 'image/jpeg';
				break;
			case "bmp":
				return 'image/bmp';
				break;
			case "jpg":
				return 'image/jpg';
				break;
			default:
				return FALSE;
				break;
		}
	}

	public static function resizeImage($imageBase64, $width = 150, $height = 150)
	{
		$image = imagecreatefromstring(base64_decode($imageBase64));
		$origWidth = imagesx($image);
		$origHeight = imagesy($image);
		$resizeImage = imagecreatetruecolor($width, $height);
		imagecopyresampled($resizeImage, $image, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);
		ob_start();
		imagepng($resizeImage);
		$resizeImage = ob_get_contents();
		ob_end_clean();
		return base64_encode($resizeImage);
	}

	public static function sendOneSignalMessage($title, $content, $action, $uuid)
	{
		$headings = [
			"en" => "FoodTime - $title",
		];

		$fields = [
			'app_id' => Setting::getSettingValueByName(SettingsForm::ONE_SIGNAL_APP_ID),
			'include_player_ids' => $uuid,
			'data' => $action,
			'contents' => $content,
			'headings' => $headings,
		];

		$fields = json_encode($fields);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, Setting::getSettingValueByName(SettingsForm::ONE_SIGNAL_API_URL));
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8',
			'Authorization: Basic ' . Setting::getSettingValueByName(SettingsForm::ONE_SIGNAL_API_AUTHORIZATION)]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	public static function sendMailgunEmail($from, $to, $subject, $emailTemplate)
	{
		$mgClient = new Mailgun(Setting::getSettingValueByName(SettingsForm::MAIL_GUN_API_KEY));
		$domain = Setting::getSettingValueByName(SettingsForm::MAIL_GUN_DOMAIN);

		# Make the call to the client.
			$result = $mgClient->sendMessage("$domain",
			[
				'from' => $from,
				'to' => $to,
				'subject' => $subject,
				//'text' => 'Your mail do not support HTML',
				'html'    => $emailTemplate
			]);
	}

	// Daleen was here ..
	public static function getImageFullUrl($imagePath)
	{	
		if (empty($imagePath))
			return null;
		else{
			// for old images ... 
			if(substr($imagePath, 0, 4) == 'http' || strpos($imagePath, 'http') !== false )
				return (string) $imagePath;
			else
				return (string) Setting::getSettingValueByName(SettingsForm::S3_BUCKET_URL).$imagePath;
		}
		return null;
	}

	// Daleen was here....
	public static function getImageThumbnail ($imagePath) 
	{

		if (empty($imagePath))
			return null;
		else{

			// for old images ... 
			if(substr($imagePath, 0, 4) == 'http' || strpos($imagePath, 'http') !== false ){
				
				//$imagePath  ='https://s3-ap-southeast-1.amazonaws.com/pic.foodhunting.asia/18/1478768092_res_18_mci_216_mi_112.jpg';
				return (string) substr_replace($imagePath,'_thumbnail',strrpos($imagePath,'.'),0);
			}else{

				/*echo $imagePath;
				echo "<br>";
				echo substr_replace($imagePath,'/thumbnail',strrpos($imagePath,'/'),0);
				echo "<br>";*/
				return (string) Setting::getSettingValueByName(SettingsForm::S3_BUCKET_URL).substr_replace($imagePath,'/thumbnail',strrpos($imagePath,'/'),0);

			}
				
		}

	}

}