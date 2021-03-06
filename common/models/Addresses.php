<?php

	namespace common\models;


	use Yii;
	use common\helpers\Helpers;

	/**
	 * This is the model class for table "addresses".
	 *
	 * @property string   $id
	 * @property string   $client_id
	 * @property string   $area_id
	 * @property string   $created_at
	 * @property string   $updated_at
	 * @property string   $deleted_at
	 * @property integer  $is_default
	 * @property string   $building_name
	 * @property string   $floor_unit
	 * @property string   $street_no
	 * @property string   $postcode
	 * @property string   $company
	 * @property string   $label
	 *
	 * @property Areas    $area
	 * @property Clients  $client
	 * @property Orders[] $orders
	 */
	class Addresses extends \yii\db\ActiveRecord
	{
//    const SCENARIO_GET_ADDRESSES = 'get_addresses';
//    const SCENARIO_GET_ADDRESS = 'get_address';
//    const SCENARIO_GET_BY_RESTAURANTS_MANGER = 'get_by_restaurants_manger';

		/**
		 * @inheritdoc
		 */
		public static function tableName()
		{
			return 'addresses';
		}

		/**
		 * @inheritdoc
		 */
		public function rules()
		{
			return [
				[['client_id', 'area_id', 'building_name', 'floor_unit', 'street_no', 'label'], 'required'],
				[['client_id', 'area_id'], 'integer'],
				[['is_default'], 'boolean'],
				[['created_at', 'updated_at', 'deleted_at'], 'safe'],
				[['building_name', 'floor_unit', 'street_no', 'postcode', 'company', 'label'], 'string', 'max' => 255],
				[['area_id'], 'exist', 'skipOnError' => TRUE, 'targetClass' => Areas::className(), 'targetAttribute' => ['area_id' => 'id']],
				[['client_id'], 'exist', 'skipOnError' => TRUE, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
			];
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels()
		{
			return [
				'id' => 'ID',
				'client_id' => 'Client ID',
				'area_id' => 'Area ID',
				'created_at' => 'Created At',
				'updated_at' => 'Updated At',
				'deleted_at' => 'Deleted At',
				'is_default' => 'Is Default',
				'building_name' => 'Building Name',
				'floor_unit' => 'Floor Unit',
				'street_no' => 'Street No',
				'postcode' => 'Postcode',
				'company' => 'Company',
				'label' => 'Label',
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getArea()
		{
			return $this->hasOne(Areas::className(), ['id' => 'area_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getClient()
		{
			return $this->hasOne(Clients::className(), ['id' => 'client_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getOrders()
		{
			return $this->hasMany(Orders::className(), ['address_id' => 'id']);
		}

		/**
		 * @inheritdoc
		 * @return AddressesQuery the active query used by this AR class.
		 */
		public static function find()
		{
			return new AddressesQuery(get_called_class());
		}

		public function afterValidate()
		{
			if ($this->hasErrors()) {
				return Helpers::HttpException(422, 'validation failed', ['error' => $this->errors]);
			}
		}

		public function beforeSave($insert)
		{
			if (!$this->isNewRecord)
				$this->updated_at = date('Y-m-d H:i:s');
			else
				$this->created_at = date('Y-m-d H:i:s');

			return parent::beforeSave($insert); // TODO: Change the autogenerated stub
		}

		public static function getAddresses()
		{
			$client = Clients::getClientByAuthorization();
			$addresses = self::find()->where(['client_id' => $client->id])->andWhere(['deleted_at' => NULL])->orderBy('is_default DESC')->all();
			return Helpers::formatResponse(TRUE, 'get success', $addresses);
		}

		public static function getAddress($address_id)
		{
			$client = Clients::getClientByAuthorization();
			$address = self::find()->where(['id' => $address_id])->andWhere(['deleted_at' => NULL])->one();
			if (!empty($address)) {
				if ($address->client_id != $client->id)
					return Helpers::HttpException(403, "forbidden", ['error' => "you don't have permission to do this action"]);
			}
			if (empty($address))
				return Helpers::HttpException(404, 'deleted failed', ['error' => "This address dos't exist"]);

			return Helpers::formatResponse(TRUE, 'get success', $address);
		}

		public static function createAddress($data)
		{
			$client = Clients::getClientByAuthorization();
			$connection = \Yii::$app->db;
			$transaction = $connection->beginTransaction();
			try {
				$Address = new Addresses();
				$model['Addresses'] = $data;
				$Address->load($model);
				$Address->client_id = $client->id;
				$Address->validate();
				if ($Address->is_default)
					$connection->createCommand()->update('addresses', ['is_default' => 0], 'client_id = ' . $Address->client_id)->execute();
				$Address->save();
				$transaction->commit();
				return Helpers::formatResponse(TRUE, 'create success', ['id' => $Address->id]);
			} catch (\Exception $e) {
				$transaction->rollBack();
				return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
			}
			return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
		}

		public static function updateAddress($address_id, $data)
		{
			$client = Clients::getClientByAuthorization();
			$address = self::find()->where(['id' => $address_id])->andWhere(['deleted_at' => NULL])->one();

			if (empty($address))
				return Helpers::HttpException(404, 'update failed', ['error' => "This address dos't exist"]);


			$connection = \Yii::$app->db;
			$transaction = $connection->beginTransaction();
			try {
				$model['Addresses'] = $data;
				$address->load($model);
				$address->client_id = $client->id;
				$address->validate();
				if ($address->is_default)
					$connection->createCommand()->update('addresses', ['is_default' => 0], 'client_id = ' . $address->client_id)->execute();
				$address->save();
				$transaction->commit();
				return Helpers::formatResponse(TRUE, 'create success', ['id' => $address->id]);
			} catch (\Exception $e) {
				$transaction->rollBack();
				return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
			}
			return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
		}

		public static function deleteAddress($address_id)
		{
			$client = Clients::getClientByAuthorization();
			$address = self::find()->where(['id' => $address_id])->andWhere(['deleted_at' => NULL])->one();

			if (!empty($address)) {
				if ($address->client_id != $client->id)
					return Helpers::HttpException(403, "forbidden", ['error' => "you don't have permission to do this action"]);
			}
			if (empty($address))
				return Helpers::HttpException(404, 'deleted failed', ['error' => "This address dos't exist"]);

			$address->deleted_at = date('Y-m-d H:i:s');

			if (!$address->save(FALSE))
				return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);

			return Helpers::formatResponse(TRUE, 'deleted success', ['id' => $address->id]);
		}

		public function fields()
		{
			return [
				'id' => function () {
					return (int)$this->id;
				},
				'is_default' => function () {
					return (bool)$this->is_default;
				},
				'building_name' => function () {
					return (string)$this->building_name;
				},
				'floor_unit' => function () {
					return (string)$this->floor_unit;
				},
				'street_no' => function () {
					return (string)$this->street_no;
				},
				'postcode' => function () {
					return (!empty($this->postcode) ? (string)$this->postcode : NULL);
				},
				'company' => function () {
					return (!empty($this->company) ? (string)$this->company : NULL);
				},
				'label' => function () {
					return (string)($this->label);
				},
				'area' => function () {
					return $this->area;
				},
			];
		}

//    public function scenarios()
//    {
//        return ArrayHelper::merge(
//            parent::scenarios(),
//            [
//                self::SCENARIO_GET_BY_RESTAURANTS_MANGER => [
//                    'id',
//                    'area' => function(){
//                        return $this->area;
//                    }
//                ],
//                self::SCENARIO_GET_ADDRESS => [
//
//                ],
//            ]
//        );
//    }
	}