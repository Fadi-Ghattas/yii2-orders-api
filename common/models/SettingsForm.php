<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 7/27/2016
 * Time: 2:40 PM
 */

namespace backend\models;

use Yii;
use yii\base\Model;

class SettingsForm extends Model
{

    const HOME_URL = 'home_url';
    //facebook keys
    const FACEBOOK_APP_ID_KEY = 'facebook_app_id';
    const FACEBOOK_APP_SECRET = 'facebook_app_secret';

    public $home_url;
    public $facebook_app_id_key;
    public $facebook_app_secret;

    public function rules()
    {
        return [
            [['home_url'], 'required'],
            [['facebook_app_id_key',
                'facebook_app_secret',
            ], 'string', 'max' => 200]

        ];
    }

    public function saveSettings()
    {
        Setting::setSettingValueByName(SettingsForm::HOME_URL, $this->home_url);
        Setting::setSettingValueByName(SettingsForm::FACEBOOK_APP_ID_KEY, $this->facebook_app_id_key);
        Setting::setSettingValueByName(SettingsForm::FACEBOOK_APP_SECRET, $this->facebook_app_secret);
    }
    
    public function fill()
    {
        $this->home_url = Setting::getSettingValueByName(SettingsForm::HOME_URL);
        $this->facebook_app_id_key = Setting::getSettingValueByName(SettingsForm::FACEBOOK_APP_ID_KEY);
        $this->facebook_app_secret = Setting::getSettingValueByName(SettingsForm::FACEBOOK_APP_SECRET);
    }
}