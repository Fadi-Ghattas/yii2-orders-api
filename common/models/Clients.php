<?php

namespace common\models;


use Yii;
use common\helpers\Helpers;

/**
 * This is the model class for table "clients".
 *
 * @property string $id
 * @property integer $active
 * @property string $phone_number
 * @property string $image
 * @property string $created_at
 * @property string $updated_at
 * @property integer $user_id
 * @property string $deleted_at
 * @property integer $verified
 *
 * @property Addresses[] $addresses
 * @property BlacklistedClients[] $blacklistedClients
 * @property User $user
 * @property FavoriteRestaurants[] $favoriteRestaurants
 * @property Feedbacks[] $feedbacks
 * @property Orders[] $orders
 * @property ResetPasswords[] $resetPasswords
 * @property Reviews[] $reviews
 */
class Clients extends \yii\db\ActiveRecord
{
    const SCENARIO_SIGN_UP_FACEBOOK = 'sign_up_facebook';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clients';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['active', 'user_id', 'verified'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['user_id', 'phone_number'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['user_id', 'image'], 'required', 'on' => self::SCENARIO_SIGN_UP_FACEBOOK],
            [['phone_number', 'image'], 'string', 'max' => 255],
            [['phone_number'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active' => 'Active',
            'phone_number' => 'Phone Number',
            'image' => 'Image',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => 'User ID',
            'deleted_at' => 'Deleted At',
            'verified' => 'Verified',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany(Addresses::className(), ['client_id' => 'id'])->where(['deleted_at' => null])->orderBy('is_default DESC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBlacklistedClients()
    {
        return $this->hasMany(BlacklistedClients::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavoriteRestaurants()
    {
        return $this->hasMany(FavoriteRestaurants::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbacks()
    {
        return $this->hasMany(Feedbacks::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResetPasswords()
    {
        return $this->hasMany(ResetPasswords::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Reviews::className(), ['client_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return ClientsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClientsQuery(get_called_class());
    }

    public function afterValidate()
    {
        if ($this->hasErrors()) {
            return Helpers::HttpException(422, 'validation failed', ['error' => $this->errors]);
        }
    }

    public static function checkClientAuthorization()
    {
        $headers = Yii::$app->request->headers;
        $authorization = explode(' ', $headers['authorization'])[1];
        $ClientUser = User::findIdentityByAccessToken($authorization);
        if (empty($ClientUser))
            return Helpers::HttpException(404, 'not found', ['error' => 'client not found']);
        $client_id = Clients::findOne(['user_id' => $ClientUser->id])->id;
        if (empty($client_id))
            return Helpers::HttpException(404, 'not found', ['error' => 'client not found']);
        return $client_id;
    }

    public static function getClientByAuthorization()
    {
        $headers = Yii::$app->request->headers;
        $authorization = explode(' ', $headers['authorization'])[1];
        $ClientUser = User::findIdentityByAccessToken($authorization);
        if (empty($ClientUser))
            return Helpers::HttpException(404, 'not found', ['error' => 'client not found']);
        $client = Clients::findOne(['user_id' => $ClientUser->id]);
        if (empty($client))
            return Helpers::HttpException(404, 'not found', ['error' => 'client not found']);
        return $client;
    }

    public static function updateClient($post_data)
    {
        $clientId = self::checkClientAuthorization();
        $client = Clients::findOne(['id' => $clientId]);
        $lockedValues = ['image', 'email', 'rank', 'password_hash', 'auth_key', 'role', 'facebook_id', 'source', 'status', 'last_logged_at'];
        foreach ($post_data as $lockedValueKey => $lockedValue) {
            if (in_array($lockedValueKey, $lockedValues))
                return Helpers::HttpException(403, "forbidden", ['error' => $lockedValueKey . " can't be changed"]);
        }

        if (isset($post_data['phone_number'])) {
            $client->phone_number = $post_data['phone_number'];
            $client->verified = 0;
        }

        if (isset($post_data['is_verified'])) {
            $client->verified = $post_data['is_verified'];
        }

        $user = User::findOne(['id' => $client->user_id]);
        if (isset($post_data['full_name'])) {
            $user->username = $post_data['full_name'];
        }

        if (!$client->save() && $user->save())
            return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later']);

        return $user->getUserClientFields();
    }

    public static function sendVerificationSmsCode()
    {
        $clientId = self::checkClientAuthorization();
        $client = Clients::findOne(['id' => $clientId]);
        if (empty($client->phone_number))
            return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide valid phone number first!!']);

        $code = Helpers::sendSms(Helpers::generateRandomFourDigits(), $client->phone_number);
        $code = str_replace("Sent from your Twilio trial account -", '', $code);
        return Helpers::formatResponse(true, 'Verification code sent successfully', ['code' => trim($code)]);
    }

    public function beforeSave($insert)
    {
        if (!$this->isNewRecord)
            $this->updated_at = date('Y-m-d H:i:s');
        else
            $this->created_at = date('Y-m-d H:i:s');

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function fields()
    {
        return [
            'id' => function () {
                return (int)$this->id;
            },
            'active' => function () {
                return (bool)$this->active;
            },
            'verified' => function () {
                return (bool)$this->verified;
            },
            'phone_number' => function () {
                return (string)$this->phone_number;
            },
            'image' => function () {
                return (string)$this->image;
            },
            'email' => function () {
                return (string)$this->user->email;
            },
            'name' => function () {
                return (string)$this->user->username;
            }
        ];

    }
}
