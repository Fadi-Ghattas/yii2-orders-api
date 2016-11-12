<?php

namespace common\models;


use Yii;
use common\helpers\Helpers;
use api\modules\v1\models\ResetPasswordSmsCodeForm;
use api\modules\v1\models\ResetPasswordForm;
use api\modules\v1\models\ChangePassword;

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
 * @property ClientsVouchers[] $clientsVouchers
 * @property Vouchers[] $vouchers
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
            [['active', 'user_id'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['user_id', 'phone_number'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['user_id', 'image'], 'required', 'on' => self::SCENARIO_SIGN_UP_FACEBOOK],
            [['phone_number', 'image'], 'string', 'max' => 255],
            [['verified'], 'boolean'],
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
    public function getClientsVouchers()
    {
        return $this->hasMany(ClientsVouchers::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVouchers()
    {
        return $this->hasMany(Vouchers::className(), ['id' => 'voucher_id'])->viaTable('clients_vouchers', ['client_id' => 'id']);
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

    public static function getClientByAuthorization()
    {
        $headers = Yii::$app->request->headers;
        $authorization = explode(' ', $headers['authorization'])[1];
        $ClientUser = User::findIdentityByAccessToken($authorization);
        if (empty($ClientUser))
            return Helpers::HttpException(404, 'not found', ['error' => 'client not found']);
        $client = Clients::find()->where(['user_id' => $ClientUser->id])->one();
        if (empty($client))
            return Helpers::HttpException(404, 'not found', ['error' => 'client not found']);
        return $client;
    }

    public static function updateClient($post_data)
    {
        $client = self::getClientByAuthorization();
        $lockedValues = ['image', 'email', 'rank', 'password_hash', 'auth_key', 'role', 'facebook_id', 'source', 'status', 'last_logged_at'];
        foreach ($post_data as $lockedValueKey => $lockedValue) {
            if (in_array($lockedValueKey, $lockedValues))
                return Helpers::HttpException(403, "forbidden", ['error' => $lockedValueKey . " can't be changed"]);
        }

        if (isset($post_data['is_verified'])) {
            $client->verified = $post_data['is_verified'];
        }

        if (isset($post_data['phone_number'])) {
            $client->phone_number = $post_data['phone_number'];
            $client->verified = 0;
        }

        $user = User::find()->where(['id' => $client->user_id])->one();
        if (isset($post_data['full_name'])) {
            $user->username = $post_data['full_name'];
        }

        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $client->validate();
            $user->validate();
            $client->save();
            $user->save();
            $transaction->commit();
            return $user->getUserClientFields();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later']);
            //throw $e;
        }
        return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later']);
    }

    public static function sendVerificationSmsCode()
    {
        $client = self::getClientByAuthorization();
        if (empty($client->phone_number))
            return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide valid phone number first!!']);

        $code = Helpers::sendSms(Helpers::generateRandomFourDigits(), $client->phone_number);
        $code = str_replace("Sent from your Twilio trial account -", '', trim($code));
        return Helpers::formatResponse(true, 'Verification code sent successfully', ['code' => (int)$code]);
    }

    public static function resetPasswordSmsCode($data)
    {
        $reset_password_sms_code_form = new ResetPasswordSmsCodeForm();
        $reset_password_sms_code_form->setAttributes($data);
        if (!$reset_password_sms_code_form->validate())
            return Helpers::HttpException(422, 'validation failed', ['error' => $reset_password_sms_code_form->errors]);
        $client = self::findOne(['phone_number' => $reset_password_sms_code_form->phone_number]);
        if (empty($client))
            return Helpers::HttpException(422, 'validation failed', ['error' => 'there is no account with this phone number please try to sing up first!!']);
        $code = Helpers::sendSms(Helpers::generateRandomFourDigits(), $reset_password_sms_code_form->phone_number);
        $code = str_replace("Sent from your Twilio trial account -", '', trim($code));
        return Helpers::formatResponse(true, 'Rest password code sent successfully', ['code' => (int)$code]);
    }

    public static function resetPassword($data)
    {
        $reset_password_form = new ResetPasswordForm();
        $reset_password_form->setAttributes($data);
        if (!$reset_password_form->validate())
            return Helpers::HttpException(422, 'validation failed', ['error' => $reset_password_form->errors]);
        $client = self::findOne(['phone_number' => $reset_password_form->phone_number]);
        if (empty($client))
            return Helpers::HttpException(422, 'validation failed', ['error' => 'there is no account with this phone number please try to sing up first!!']);
        $user = User::findOne(['id' => $client->user_id]);
        $user->setPassword($reset_password_form->new_password);
        if (!$user->save())
            return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
        $user = User::Login($user->email, $user->password_hash);
        if (!$user)
            return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
        return Helpers::formatResponse(true, 'rest password and log in success', $user->getUserClientFields());
    }

    public static function changePassword($data)
    {
        $client = self::getClientByAuthorization();
        $user = User::findOne(['id' => $client->user_id]);
        $change_password = new ChangePassword();
        $change_password->setAttributes($data);
        if (!$change_password->validate())
            return Helpers::HttpException(422, 'validation failed', ['error' => $change_password->errors]);
        if (!Yii::$app->getSecurity()->validatePassword($change_password->old_password, $user->password_hash))
            return Helpers::HttpException(422, 'validation failed', ['error' => 'old password incorrect please try again.']);
        $user->setPassword($change_password->new_password);
        if (!$user->save())
            return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
        $user = User::Login($user->email, $user->password_hash);
        if (!$user)
            return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
        return Helpers::formatResponse(true, 'rest password success', $user->getUserClientFields());
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
                return (!empty($this->phone_number) ? (string)$this->phone_number : null);
            },
            'image' => function () {
                return (!empty($this->image) ? (string)$this->image : null);
            },
            'email' => function () {
                return (string)$this->user->email;
            },
            'name' => function () {
                return (string)$this->user->username;
            }
        ];

    }

    public function checkClientAddress($addressId)
    {
        foreach ($this->addresses as $address) {
            if ($address->id == $addressId)
                return 1;
        }
        return 0;
    }

    public static function getClientOrders()
    {
        $client = self::getClientByAuthorization();
        $orders = Orders::find()->where(['client_id' => $client->id])->orderBy('created_at DESC')->all();
        return Helpers::formatResponse(true, 'get success',  $orders);
    }

    public static function getClientOrder($order_id)
    {
        $client = self::getClientByAuthorization();
        $order = Orders::find()->where(['client_id' => $client->id])->andWhere(['id' => $order_id])->one();
        if (empty($order))
            return Helpers::HttpException(404, 'not found', ['error' => 'order not found']);
        return Helpers::formatResponse(true, 'get success', $order);
    }

    public static function postReviews($data)
    {
        $reviews = new Reviews();
        $reviews->setAttributes($data);
        $headers = Yii::$app->request->headers;
        if (isset($headers['authorization'])) {
            $client = Clients::getClientByAuthorization();
            $reviews->client_id = $client->id;
        }
        $reviews->validate();
        if (!$reviews->save())
            return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later']);
        return Helpers::formatResponse(true, 'Thank you for rating this order, your review will be published shortly.', ['id' => $reviews->id]);
    }
}
