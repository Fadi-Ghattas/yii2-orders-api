<?php

namespace common\models;

use common\helpers\Helpers;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "orders".
 *
 * @property string $id
 * @property string $restaurant_id
 * @property string $client_id
 * @property string $reference_number
 * @property string $total
 * @property string $total_with_voucher
 * @property string $commission_amount
 * @property string $address_id
 * @property string $note
 * @property string $voucher_id
 * @property string $payment_method_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $status_id
 *
 * @property OrderItems[] $orderItems
 * @property OrderStatus $status
 * @property Addresses $address
 * @property Clients $client
 * @property PaymentMethods $paymentMethod
 * @property Restaurants $restaurant
 * @property Vouchers $voucher
 */
class Orders extends \yii\db\ActiveRecord
{

    const SCENARIO_ALL_ORDERS = 'all_orders';
    const SCENARIO_ORDER_DETAILS = 'order_details';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['restaurant_id', 'client_id', 'reference_number', 'total', 'commission_amount', 'address_id', 'payment_method_id', 'status_id'], 'required'],
            [['restaurant_id', 'client_id', 'address_id', 'voucher_id', 'payment_method_id', 'status_id'], 'integer'],
            [['total', 'total_with_voucher', 'commission_amount'], 'number'],
            [['note'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['reference_number'], 'string', 'max' => 255],
            [['reference_number'], 'unique'],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderStatus::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Addresses::className(), 'targetAttribute' => ['address_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['payment_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethods::className(), 'targetAttribute' => ['payment_method_id' => 'id']],
            [['restaurant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Restaurants::className(), 'targetAttribute' => ['restaurant_id' => 'id']],
            [['voucher_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vouchers::className(), 'targetAttribute' => ['voucher_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'restaurant_id' => 'Restaurant ID',
            'client_id' => 'Client ID',
            'reference_number' => 'Reference Number',
            'total' => 'Total',
            'total_with_voucher' => 'Total With Voucher',
            'commission_amount' => 'Commission Amount',
            'address_id' => 'Address ID',
            'note' => 'Note',
            'voucher_id' => 'Voucher ID',
            'payment_method_id' => 'Payment Method ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'status_id' => 'Status ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItems::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(OrderStatus::className(), ['id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Addresses::className(), ['id' => 'address_id'])->where(['deleted_at' => null]);
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
    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethods::className(), ['id' => 'payment_method_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRestaurant()
    {
        return $this->hasOne(Restaurants::className(), ['id' => 'restaurant_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVoucher()
    {
        return $this->hasOne(Vouchers::className(), ['id' => 'voucher_id']);
    }

    /**
     * @inheritdoc
     * @return OrdersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrdersQuery(get_called_class());
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

    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                self::SCENARIO_ALL_ORDERS => [
                    'id',
                    'reference_number',
                    'status' => function () {
                        return $this->status->name;
                    },
                    'name' => function () {
                        return $this->client->user->username;
                    },
                    'date' => function () {
                        return date('d/m/Y', strtotime($this->created_at));
                    },
                    'time' => function () {
                        return date('h:i A', strtotime($this->created_at));
                    }
                ],

                self::SCENARIO_ORDER_DETAILS => [
                    'id',
                    'reference_number',
                    'total',
                    'total_with_voucher',
                    'commission_amount',
                    'status' => function () {
                        return $this->status->name;
                    },
                    'vouchers' => function () {
//                        return $this->voucher;
                        return [];
                    },
                    'customer_details' => function(){
                        $customer_details = array();
                        $customer_details['id'] = $this->client->id;
                        $customer_details['name'] = $this->client->user->username;
                        $customer_details['email'] = $this->client->user->email;
                        $customer_details['phone_number'] = $this->client->phone_number;
                        $address = Addresses::find()->where(['client_id' => $this->client->id])->andWhere(['is_default' => 1])->andWhere(['deleted_at' => null])->one();
                        if(empty($address))
                            $address = Addresses::find()->where(['client_id' => $this->client->id])->andWhere(['deleted_at' => null])->orderBy('created_at DESC')->one();
                        $customer_details['address'] = $address;
                        return $customer_details;
                    },
                    'order_items' => function () {
                        $order_items = array();
                        foreach ($this->orderItems as $orderItem){
                            $singleOrderItem = array();
                            $singleOrderItem['id'] = $orderItem->id;
                            $singleOrderItem['price'] = $orderItem->price;
                            $singleOrderItem['quantity'] = $orderItem->quantity;
                            $singleOrderItem['note'] = $orderItem->note;
                            $singleOrderItem['menu_item_id'] = $orderItem->item->id;
                            $singleOrderItem['name'] = $orderItem->item->name;
                            $singleOrderItem['description'] = $orderItem->item->description;
                            $singleOrderItem['discount'] = $orderItem->item->discount;
                            $singleOrderItem['addons'] = $orderItem->orderItemAddons;
                            $singleOrderItem['item_choices'] = $orderItem->orderItemChoices;
                            $order_items [] = $singleOrderItem;
                        }
                        return $order_items;
                    },
                    'payment_method' => function () {
                        return $this->paymentMethod;
                    }
                ],
            ]
        );
    }

    public static function getRestaurantOrderById($restaurant_id, $orderID)
    {
        return self::find()->where(['restaurant_id' => $restaurant_id])->andWhere(['id' => $orderID])->andWhere(['deleted_at' => null])->one();
    }

    public static function getOrders()
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        $request = Yii::$app->request;
        $get_data = $request->get();
        $page = 1;
        $limit = -1;

        if (isset($get_data['limit']) && isset($get_data['page'])) {
            Helpers::validateSetEmpty([$get_data['page'], $get_data['limit']]);
            if (is_int(intval(trim($get_data['page']))) && is_int(intval(trim($get_data['limit'])))) {
                $page = intval(trim($get_data['page']));
                $limit = intval(trim($get_data['limit']));
            } else {
                return Helpers::HttpException(422, 'validation failed', ['error' => 'page and limit must be integer']);
            }
        }

        if (isset($get_data['date']) && !empty($get_data['date']))
            Helpers::validateDate(trim($get_data['date']), 'Y-m-d');

        $orders = new Orders();
        $query = $orders::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
        $query->andFilterWhere(['restaurant_id' => $restaurant->id, 'deleted_at' => null]);
        if (isset($get_data['date']) && !empty($get_data['date']))
            $query->andFilterWhere(['>=', 'created_at', trim($get_data['date'])]);
        if ($limit != -1)
            $query->limit($limit)->offset($page - 1);
        return Helpers::formatResponse(true, 'get success', $dataProvider->models);
    }

    public static function getRestaurantOrder($orderID)
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        $order = self::getRestaurantOrderById($restaurant->id, $orderID);
        if (empty($order))
            return Helpers::HttpException(404, 'get failed', ['error' => "this order dos't exist"]);

        return Helpers::formatResponse(true, 'get success', $order);
    }

    public static function updateRestaurantOrderStatus($orderID, $data)
    {
        $restaurant = Restaurants::checkRestaurantAccess();
        $order = self::getRestaurantOrderById($restaurant->id, $orderID);
        if (empty($order))
            return Helpers::HttpException(404, 'get failed', ['error' => "this order dos't exist"]);

        if (!isset($data['status_id']))
            return Helpers::HttpException(422, 'validation failed', ['error' => 'status_id is required']);

        $order->status_id = $data['status_id'];
        $order->validate();
        $isUpdated = $order->save();

        if (!$isUpdated)
            return Helpers::HttpException(422, 'update failed', null);

        return Helpers::formatResponse(true, 'update success', ['id' => $order->id]);
    }

    public function fields()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if ($request->isGet) {
            if (!isset($get_data['id']))
                return $this->scenarios()[self::SCENARIO_ALL_ORDERS];
            else if (!empty($get_data) && isset($get_data['id']))
                return $this->scenarios()[self::SCENARIO_ORDER_DETAILS];
        }
        return parent::fields();
    }

}
