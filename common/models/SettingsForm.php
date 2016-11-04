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
	//one signal
	const ONE_SIGNAL_APP_ID = 'one_signal_app_id';
	const ONE_SIGNAL_API_URL = 'one_signal_api_url';
	const ONE_SIGNAL_API_AUTHORIZATION = 'one_signal_api_authorization';

	public $home_url;
	public $facebook_app_id_key;
	public $facebook_app_secret;
	public $twillio_sid;
	public $twillio_token;
	public $twillio_number;
	public $s3_key;
	public $s3_secret;
	public $one_signal_app_id;
	public $one_signal_app_url;
	public $one_signal_app_authorization;

	public function rules()
	{
		return [
			[['home_url'], 'required'],
			[['facebook_app_id_key',
				'facebook_app_secret',
				'twillio_sid',
				'twillio_token',
				'twillio_number',
				'one_signal_app_id',
				'one_signal_app_url',
				'one_signal_app_authorization',
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
		Setting::setSettingValueByName(SettingsForm::S3_KEY, $this->one_signal_app_id);
		Setting::setSettingValueByName(SettingsForm::S3_SECRET, $this->one_signal_app_url);
		Setting::setSettingValueByName(SettingsForm::S3_SECRET, $this->one_signal_app_authorization);
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
		$this->one_signal_app_id = Setting::getSettingValueByName(SettingsForm::ONE_SIGNAL_APP_ID);
		$this->one_signal_app_url = Setting::getSettingValueByName(SettingsForm::ONE_SIGNAL_API_URL);
		$this->one_signal_app_authorization = Setting::getSettingValueByName(SettingsForm::ONE_SIGNAL_API_AUTHORIZATION);
	}
}