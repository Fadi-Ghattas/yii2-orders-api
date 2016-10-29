<?php

	namespace common\models;

	use common\helpers\Helpers;
	use Yii;

	/**
	 * This is the model class for table "order_status".
	 *
	 * @property string   $id
	 * @property string   $name
	 *
	 * @property Orders[] $orders
	 */
	class OrderStatus extends \yii\db\ActiveRecord
	{
		/**
		 * @inheritdoc
		 */
		public static function tableName()
		{
			return 'order_status';
		}

		/**
		 * @inheritdoc
		 */
		public function rules()
		{
			return [
				[['name'], 'required'],
				[['name'], 'string', 'max' => 255],
			];
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels()
		{
			return [
				'id' => 'ID',
				'name' => 'Name',
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getOrders()
		{
			return $this->hasMany(Orders::className(), ['status_id' => 'id']);
		}

		/**
		 * @inheritdoc
		 * @return OrderStatusQuery the active query used by this AR class.
		 */
		public static function find()
		{
			return new OrderStatusQuery(get_called_class());
		}

		public function afterValidate()
		{
			if ($this->hasErrors()) {
				return Helpers::HttpException(422, 'validation failed', ['error' => $this->errors]);
			}
		}

		public static function getOrderStatus()
		{
			$orderStatus = OrderStatus::find()->all();
			if (empty($orderStatus))
				return Helpers::HttpException(422, 'validation failed', ['error' => "order dos't exist"]);
			return Helpers::formatResponse(TRUE, 'get success', $orderStatus);
		}
	}
