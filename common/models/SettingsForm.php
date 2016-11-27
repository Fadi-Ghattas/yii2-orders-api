<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 7/27/2016
 * Time: 2:40 PM
 */

namespace common\models;

use Yii;
use yii\base\Model;

class SettingsForm extends Model
{

	const HOME_URL = 'home_url';
	//facebook keys
	const FACEBOOK_APP_ID_KEY = 'facebook_app_id';
	const FACEBOOK_APP_SECRET = 'facebook_app_secret';
	//twillio
	const TWILLIO_SID = 'twillio_sid';
	const TWILLIO_TOKEN = 'twillio_token';
	const TWILLIO_NUMBER = 'twillio_number';
	//ASW S3
	const S3_KEY = 's3_key';
	const S3_SECRET = 's3_secret';
	const S3_BUCKET_NAME = 's3_bucket_name';
	const S3_BUCKET_URL = 's3_bucket_url';
	//one signal
	const ONE_SIGNAL_APP_ID = 'one_signal_app_id';
	const ONE_SIGNAL_API_URL = 'one_signal_api_url';
	const ONE_SIGNAL_API_AUTHORIZATION = 'one_signal_api_authorization';
	//mail gun
	const MAIL_GUN_API_KEY = 'mail_gun_api_key';
	const MAIL_GUN_DOMAIN = 'mail_gun_domain';
	//Food Hunting
	CONST HELLO_EMAIL = 'hello_email';
	CONST INFO_EMAIL = 'info_email';
	CONST EMAIL_NAME = 'email_name';

	public $home_url;
	public $facebook_app_id_key;
	public $facebook_app_secret;
	public $twillio_sid;
	public $twillio_token;
	public $twillio_number;
	public $s3_key;
	public $s3_secret;
	public $s3_bucket_name;
	public $s3_bucket_url;
	public $one_signal_app_id;
	public $one_signal_app_url;
	public $one_signal_app_authorization;
	public $mail_gun_api_key;
	public $mail_gun_domain;
	public $hello_email;
	public $info_email;
	public $email_name;

	public function rules()
	{
		return [
			[['home_url'], 'required'],
			[[	'facebook_app_id_key',
				'facebook_app_secret',
				'twillio_sid',
				'twillio_token',
				'twillio_number',
				's3_key',
				's3_secret',
				's3_bucket_name',
				'one_signal_app_id',
				'one_signal_app_url',
				'one_signal_app_authorization',
				'mail_gun_api_key',
				'mail_gun_domain',
				'email',
				'email_name',
			], 'string', 'max' => 200],

		];
	}

	public function saveSettings()
	{
		Setting::setSettingValueByName(SettingsForm::HOME_URL, $this->home_url);
		Setting::setSettingValueByName(SettingsForm::FACEBOOK_APP_ID_KEY, $this->facebook_app_id_key);
		Setting::setSettingValueByName(SettingsForm::FACEBOOK_APP_SECRET, $this->facebook_app_secret);
		Setting::setSettingValueByName(SettingsForm::TWILLIO_SID, $this->twillio_sid);
		Setting::setSettingValueByName(SettingsForm::TWILLIO_TOKEN, $this->twillio_token);
		Setting::setSettingValueByName(SettingsForm::TWILLIO_NUMBER, $this->twillio_number);
		Setting::setSettingValueByName(SettingsForm::S3_KEY, $this->s3_key);
		Setting::setSettingValueByName(SettingsForm::S3_SECRET, $this->s3_secret);
		Setting::setSettingValueByName(SettingsForm::S3_BUCKET_NAME, $this->s3_bucket_name);
		Setting::setSettingValueByName(SettingsForm::S3_BUCKET_URL, $this->s3_bucket_url);
		Setting::setSettingValueByName(SettingsForm::ONE_SIGNAL_APP_ID, $this->one_signal_app_id);
		Setting::setSettingValueByName(SettingsForm::ONE_SIGNAL_API_URL, $this->one_signal_app_url);
		Setting::setSettingValueByName(SettingsForm::ONE_SIGNAL_API_AUTHORIZATION, $this->one_signal_app_authorization);
		Setting::setSettingValueByName(SettingsForm::MAIL_GUN_API_KEY, $this->mail_gun_api_key);
		Setting::setSettingValueByName(SettingsForm::MAIL_GUN_DOMAIN, $this->mail_gun_domain);
		Setting::setSettingValueByName(SettingsForm::HELLO_EMAIL, $this->hello_email);
		Setting::setSettingValueByName(SettingsForm::INFO_EMAIL, $this->info_email);
		Setting::setSettingValueByName(SettingsForm::EMAIL_NAME, $this->email_name);
	}

	public function fill()
	{
		$this->home_url = Setting::getSettingValueByName(SettingsForm::HOME_URL);
		$this->facebook_app_id_key = Setting::getSettingValueByName(SettingsForm::FACEBOOK_APP_ID_KEY);
		$this->facebook_app_secret = Setting::getSettingValueByName(SettingsForm::FACEBOOK_APP_SECRET);
		$this->twillio_sid = Setting::getSettingValueByName(SettingsForm::TWILLIO_SID);
		$this->twillio_token = Setting::getSettingValueByName(SettingsForm::TWILLIO_TOKEN);
		$this->twillio_number = Setting::getSettingValueByName(SettingsForm::TWILLIO_NUMBER);
		$this->s3_key = Setting::getSettingValueByName(SettingsForm::S3_KEY);
		$this->s3_secret = Setting::getSettingValueByName(SettingsForm::S3_SECRET);
		$this->s3_bucket_name = Setting::getSettingValueByName(SettingsForm::S3_BUCKET_NAME);
		$this->s3_bucket_url = Setting::getSettingValueByName(SettingsForm::S3_BUCKET_URL);
		$this->one_signal_app_id = Setting::getSettingValueByName(SettingsForm::ONE_SIGNAL_APP_ID);
		$this->one_signal_app_url = Setting::getSettingValueByName(SettingsForm::ONE_SIGNAL_API_URL);
		$this->one_signal_app_authorization = Setting::getSettingValueByName(SettingsForm::ONE_SIGNAL_API_AUTHORIZATION);
		$this->mail_gun_api_key = Setting::getSettingValueByName(SettingsForm::MAIL_GUN_API_KEY);
		$this->mail_gun_domain = Setting::getSettingValueByName(SettingsForm::MAIL_GUN_DOMAIN);
		$this->hello_email = Setting::getSettingValueByName(SettingsForm::HELLO_EMAIL);
		$this->info_email = Setting::getSettingValueByName(SettingsForm::INFO_EMAIL);
		$this->email_name = Setting::getSettingValueByName(SettingsForm::EMAIL_NAME);
	}
}