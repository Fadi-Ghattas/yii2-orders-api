<?php
namespace common\models;


use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\helpers\Helpers;
use Facebook\Facebook;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property string $role
 * @property string $facebook_id
 * @property string $source
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $deleted_at
 * @property integer $last_logged_at
 * @property string $password write-only password
 *
 * @property Clients[] $clients
 * @property Restaurants[] $restaurants
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $role;

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const CLIENT = 'client';
    const RESTAURANT_MANAGER = 'restaurant_manager';
    const ADMIN = "admin";
    const SOURCE_BASIC = "basic";
    const SOURCE_FACEBOOK = "facebook";

    const SCENARIO_SIGN_UP_FACEBOOK = 'sign_up_facebook';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['username', 'auth_key', 'password_hash', 'email', 'created_at'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['username', 'auth_key', 'email', 'created_at'], 'required', 'on' => self::SCENARIO_SIGN_UP_FACEBOOK],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['deleted_at', 'last_logged_at', 'facebook_id', 'source'], 'safe'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClients()
    {
        return $this->hasMany(Clients::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRestaurants()
    {
        return $this->hasMany(Restaurants::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }


    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        if (User::getRoleName(Yii::$app->user->id) != User::RESTAURANT_MANAGER && $password == $this->password_hash)
            return true;

        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }


    /**
     * Returns user role name according to RBAC
     * @return string
     */
    public static function getRoleName($user_id)
    {
        $roles = Yii::$app->authManager->getRolesByUser($user_id);
        if (!$roles) {
            return null;
        }

        reset($roles);
        /* @var $role \yii\rbac\Role */
        $role = current($roles);

        return $role->name;
    }

    public function afterValidate()
    {
        if ($this->hasErrors()) {
            return Helpers::HttpException(422, 'validation failed', ['error' => $this->errors]);
        }
    }

    function afterSave($insert, $changedAttributes)
    {
        $auth = Yii::$app->authManager;
        if ($insert) {
            if ($this->role == User::CLIENT) {
                $clientRole = $auth->getRole(User::CLIENT);
                $auth->assign($clientRole, $this->id);
            } else if ($this->role == User::RESTAURANT_MANAGER) {
                $restaurantManagerRole = $auth->getRole(User::RESTAURANT_MANAGER);
                $auth->assign($restaurantManagerRole, $this->id);
            } else if ($this->role == User::ADMIN) {
                $adminRole = $auth->getRole(User::ADMIN);
                $auth->assign($adminRole, $this->id);
            } else {
                return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong while saving the user role, try again later or contact the admin.']);
            }
        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    public static function NewBasicSignUp($username, $email, $phone_number, $password, $role)
    {
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $new_user = new User();
            $new_user->setAttributes(['username' => $username, 'email' => $email, 'source' => self::SOURCE_BASIC]);
            $new_user->setPassword($password);
            $new_user->generateAuthKey();
            $new_user->last_logged_at = date('Y-m-d H:i:s');
            $new_user->created_at = strtotime(date('Y-m-d H:i:s'));
            $new_user->role = $role;
            $new_user->save();
            $new_client = new Clients();
            $new_client->setAttributes(['user_id' => $new_user->id, 'phone_number' => $phone_number]);
            $new_client->save();
            $transaction->commit();
            return $new_user;
        } catch (\Exception $e) {
            $transaction->rollBack();
        }
        return false;
    }

    public static function NewFacebookSignUp($full_name, $email, $picture, $facebook_id, $role)
    {
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $new_user = new User();
            $new_user->setAttributes([
                'username' => $full_name,
                'email' => $email,
                'facebook_id' => $facebook_id,
                'source' => self::SOURCE_FACEBOOK
            ]);
            //let be change from the settings
            $new_user->setPassword($facebook_id);
            $new_user->generateAuthKey();
            $new_user->last_logged_at = date('Y-m-d H:i:s');
            $new_user->created_at = strtotime(date('Y-m-d H:i:s'));
            $new_user->role = $role;
            $new_user->save();
            $new_client = new Clients(['scenario' => User::SCENARIO_SIGN_UP_FACEBOOK]);
            $new_client->user_id = $new_user->id;
            $new_client->image = $picture;
            $new_client->save();
            $transaction->commit();
            return $new_user;
        } catch (\Exception $e) {
            $transaction->rollBack();
        }
        return false;
    }

    public static function Login($email, $password)
    {
        /**
         * @var User $user
         */
        $user = User::findOne(['email' => $email]);

        $user_role = User::getRoleName($user->id);

        if ($user_role != self::CLIENT)
            return Helpers::HttpException(403, "forbidden", ['error' => "You must sing up for client account first."]);

        if (!$user) {
            return Helpers::HttpException(422, 'validation failed', ['error' => 'Invalid email or password.']);
        }
        //TODO if not user Verified not allowed to login
        if (!$user->validatePassword($password)) {
            return Helpers::HttpException(422, 'validation failed', ['error' => 'Invalid password.']);
        }

        if (!$user->regGenerateAuthKey()) {
            return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later']);
        }

        $user->last_logged_at = date('Y-m-d H:i:s');
        $user->save();

        return $user;
    }

    public static function VerifyFB($email, $facebook_id, $access_token)
    {
        $try = 1;
        while ($try <= 3) {
            try {
                $facebook = new Facebook([
                    'app_id' => Setting::getSettingValueByName(SettingsForm::FACEBOOK_APP_ID_KEY),
                    'app_secret' => Setting::getSettingValueByName(SettingsForm::FACEBOOK_APP_SECRET),
                    'default_graph_version' => 'v2.7'
                ]);
                //LET ANDROID AND IOS MAKE SURE THAT THEY SEND access_token with email permissions
                $response = $facebook->sendRequest('GET', '/me?fields=id,name,email', [], $access_token);
                $me = $response->getGraphUser();
                $email_validation = $me->getEmail() == $email;
                $id_validation = $me->getId() == $facebook_id;
                return $email_validation && $id_validation;
            } catch (\Exception $e) {
                if ($e instanceof \Facebook\Exceptions\FacebookResponseException && $e->getCode() == 190)
                    return Helpers::HttpException(422, 'facebook validation failed', ['error' => $e->getMessage()]);
                if ($try == 3) {
                    return Helpers::HttpException(422, 'facebook login failed', ['error' => $e->getMessage()]);
                }
                $try++;
            }
        }
        return false;
    }

    public function regGenerateAuthKey()
    {
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $this->generateAuthKey();
            $this->save();
            $transaction->commit();
            return $this;
        } catch (\Exception $e) {
            $transaction->rollBack();
        }
        return false;
    }

    public function getUserClientFields()
    {
        $client = Clients::findOne(['user_id' => $this->id]);
        return [
            'full_name' => (string)$this->username,
            'email' => (string)$this->email,
            'auth_key' => (string)$this->auth_key,
            'phone_number' => (string)$client->phone_number,
            'image' => (string)$client->image,
            'is_verified' => (bool)$client->verified,
            'addresses' => $client->addresses
        ];
    }
}

