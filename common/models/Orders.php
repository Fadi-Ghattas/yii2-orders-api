<?php

namespace common\models;


use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use api\modules\v1\models\MakeOrderForm;
use api\modules\v1\models\OrderItemAddOnForm;
use api\modules\v1\models\OrderItemForm;
use api\modules\v1\models\OrderItemItemChoicesForm;
use api\modules\v1\models\VoucherForm;
use common\helpers\Helpers;

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
 * @property string $delivery_fee
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
    const SCENARIO_CLIENT_ORDERS = 'client_orders';
    const SCENARIO_CLIENT_ORDER_DETAILS = 'client_order_details';

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
            [['total', 'total_with_voucher', 'commission_amount', 'delivery_fee'], 'number'],
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
        $query->orderBy('created_at DESC');
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

    public static function makeOrder($data)
    {
        $client = Clients::getClientByAuthorization();
        if (!$client->verified)
            return Helpers::HttpException(422, 'validation failed', ['error' => 'please verify your account first!!']);

        $makeOrderForm = new MakeOrderForm();
        $makeOrderForm->setAttributes($data);
        if (!$makeOrderForm->validate())
            return Helpers::HttpException(422, 'validation failed', ['error' => $makeOrderForm->errors]);

        if (!$client->checkClientAddress($makeOrderForm->address_id))
            return Helpers::HttpException(422, 'validation failed', ['error' => 'Please provide valid address first!!']);

        $restaurants = Restaurants::find()->where(['id' => $makeOrderForm->restaurant_id])->one();
        if (empty($restaurants))
            return Helpers::HttpException(422, 'validation failed', ['error' => "this restaurants is not exist"]);
        if (!$restaurants->status)
            return Helpers::HttpException(422, 'validation failed', ['error' => "this restaurants is not exist"]);
//        if (!$restaurants->isOpenForOrders())
//            return Helpers::HttpException(422, 'validation failed', ['error' => 'Sorry restaurant ' . $restaurants->name . ' is not taken any order for now pleas try some time later.']);
        if (!$restaurants->checkPaymentMethod($makeOrderForm->payment_method_id))
            return Helpers::HttpException(422, 'validation failed', ['error' => "Sorry restaurant " . $restaurants->name . " don't accept this payment method."]);
        if (!empty(BlacklistedClients::find()->where(['client_id' => $client->id])->andWhere(['restaurant_id' => $restaurants->id])->one()))
            return Helpers::HttpException(422, 'validation failed', ['error' => "Sorry restaurant " . $restaurants->name . " has block you and you can't order from it."]);

        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {

            $orderTotal = 0;
            $orderTotalWithVoucher = 0;

            $order = new Orders();
            $order->restaurant_id = $makeOrderForm->restaurant_id;
            $order->client_id = $client->id;
            $order->address_id = $makeOrderForm->address_id;
            $order->note = $makeOrderForm->note;
            $order->payment_method_id = $makeOrderForm->payment_method_id;
            $order->status_id = 1;
            $order->reference_number = '0';
            $order->total = 0;
            $order->total_with_voucher = 0;
            $order->commission_amount = 0;
            $order->save();
            $order->reference_number = $makeOrderForm->restaurant_id . '_' . $client->id . '_' . $order->id;
            $order->save();

            foreach ($makeOrderForm->items as $item) {
                if (empty($item)) {
                    $transaction->rollBack();
                    return Helpers::HttpException(422, 'validation failed', ['error' => "order item can't be blank."]);
                }
                $orderItemForm = new OrderItemForm();
                $orderItemForm->setAttributes($item);
                if (!$orderItemForm->validate()) {
                    $transaction->rollBack();
                    return Helpers::HttpException(422, 'validation failed', ['error' => $orderItemForm->errors]);
                }

                $menuItem = MenuItems::find()->where(['id' => $orderItemForm->id])->andWhere(['status' => 1])->andWhere(['deleted_at' => null])->one();
                if (empty($menuItem)) {
                    $transaction->rollBack();
                    return Helpers::HttpException(422, 'validation failed', ['error' => "Sorry menu item does not exist"]);
                }

                $orderItem = new OrderItems();
                $orderItem->order_id = $order->id;
                $orderItem->item_id = $menuItem->id;
                $orderItem->price = $menuItem->price;
                $orderItem->quantity = $orderItemForm->quantity;
                $orderItem->note = $orderItemForm->note;

                if (!$orderItem->validate()) {
                    $transaction->rollBack();
                    return Helpers::HttpException(422, 'validation failed', ['error' => $orderItem->errors]);
                }

                $orderItem->save();

                $orderTotal += $menuItem->price * $orderItemForm->quantity;

                if (!empty($orderItemForm->add_on)) {
                    foreach ($orderItemForm->add_on as $orderItemAddOn) {

                        $orderItemAddOnForm = new OrderItemAddOnForm();
                        $orderItemAddOnForm->setAttributes($orderItemAddOn);
                        if (!$orderItemAddOnForm->validate()) {
                            $transaction->rollBack();
                            return Helpers::HttpException(422, 'validation failed', ['error' => $orderItemAddOnForm->errors]);
                        }
                        $menuItemAddOn = MenuItemAddon::find()->where(['menu_item_id' => $menuItem->id])->andWhere(['addon_id' => $orderItemAddOnForm->id])->one();
                        if (empty($menuItemAddOn)) {

                            return Helpers::HttpException(422, 'validation failed', ['error' => "Sorry there is add on not belong to this menu item."]);
                        }
                        $addOn = Addons::find()->where(['id' => $orderItemAddOnForm->id])->andWhere(['restaurant_id' => $makeOrderForm->restaurant_id])->andWhere(['status' => 1])->andWhere(['deleted_at' => null])->one();
                        if (empty($addOn)) {
                            $transaction->rollBack();
                            return Helpers::HttpException(422, 'validation failed', ['error' => "Sorry there is add on not belong to this restaurant."]);
                        }

                        $orderItemAddOnModel = new OrderItemAddon();
                        $orderItemAddOnModel->order_item_id = $orderItem->id;
                        $orderItemAddOnModel->addon_id = $addOn->id;
                        $orderItemAddOnModel->price = $addOn->price;
                        $orderItemAddOnModel->quantity = $orderItemAddOnForm->quantity;
                        if (!$orderItemAddOnModel->validate()) {
                            $transaction->rollBack();
                            return Helpers::HttpException(422, 'validation failed', ['error' => $orderItemAddOnModel->errors]);
                        }
                        $orderItemAddOnModel->save();
                        $orderTotal += $addOn->price * $orderItemAddOnForm->quantity;
                    }
                }

                if (!empty($orderItemForm->item_choices)) {

                    $orderItemItemChoicesForm = new OrderItemItemChoicesForm();
                    $orderItemItemChoicesForm->setAttributes($orderItemForm->item_choices);
                    if (!$orderItemItemChoicesForm->validate()) {
                        $transaction->rollBack();
                        return Helpers::HttpException(422, 'validation failed', ['error' => $orderItemItemChoicesForm->errors]);
                    }
                    $menuItemChoice = MenuItemChoice::find()->where(['menu_item_id' => $menuItem->id])->andWhere(['choice_id' => $orderItemItemChoicesForm->id])->one();
                    if (empty($menuItemChoice)) {
                        $transaction->rollBack();
                        return Helpers::HttpException(422, 'validation failed', ['error' => "Sorry there is item choice not belong to this menu item."]);
                    }

                    $itemChoices = ItemChoices::find()->where(['id' => $orderItemItemChoicesForm->id])->andWhere(['restaurant_id' => $makeOrderForm->restaurant_id])->andWhere(['status' => 1])->andWhere(['deleted_at' => null])->one();
                    if (empty($itemChoices)) {
                        $transaction->rollBack();
                        return Helpers::HttpException(422, 'validation failed', ['error' => "Sorry there is item Choices not belong to this restaurant."]);
                    }

                    $orderItemChoicesModel = new OrderItemChoices();
                    $orderItemChoicesModel->order_item_id = $orderItem->id;
                    $orderItemChoicesModel->item_choice_id = $itemChoices->id;
                    if (!$orderItemChoicesModel->validate()) {
                        $transaction->rollBack();
                        return Helpers::HttpException(422, 'validation failed', ['error' => $orderItemChoicesModel->errors]);
                    }
                    $orderItemChoicesModel->save();
                }
            }

            if (!empty($makeOrderForm->voucher_code)) {
                $voucherForm = new VoucherForm();
                $voucherForm->setAttributes(['restaurant_id' => $makeOrderForm->restaurant_id, 'voucher_code' => $makeOrderForm->voucher_code, 'order_total_amount' => $orderTotal]);
                if (!$voucherForm->validate()) {
                    $transaction->rollBack();
                    return Helpers::HttpException(422, 'validation failed', ['error' => $voucherForm->errors]);
                }

                if (!$restaurants->accepts_vouchers)
                    return Helpers::HttpException(422, 'validation failed', ['error' => 'this restaurants currently not accepting vouchers']);

                $voucher = Vouchers::find()->where(['code' => $voucherForm->voucher_code])->one();
                if (empty($voucher)) {
                    $transaction->rollBack();
                    return Helpers::HttpException(422, 'validation failed', ['error' => 'there is no voucher with this code please try again with different code']);
                }
                if (!$voucher->isStart($voucherForm->restaurant_id)) {
                    $transaction->rollBack();
                    //return Helpers::HttpException(422, 'validation failed', ['error' => 'this voucher is not yet active please check the voucher start date']);
                    return Helpers::HttpException(422, 'validation failed', ['error' => 'you can use this voucher after ' . $voucher->getStartDate($voucherForm->restaurant_id)]);
                }
                if ($voucher->isExpired($voucherForm->restaurant_id)) {
                    $transaction->rollBack();
                    return Helpers::HttpException(422, 'validation failed', ['error' => 'this voucher date is expired']);
                }

                if (doubleval($voucher->minimum_order) > doubleval($voucherForm->order_total_amount)) {
                    $transaction->rollBack();
                    return Helpers::HttpException(422, 'validation failed', ['error' => 'sorry but this voucher work only for order total bigger than ' . doubleval($voucher->minimum_order)]);
                }

                $clientVoucher = ClientsVouchers::find()->where(['client_id' => $client->id])->andWhere(['voucher_id' => $voucher->id])->one();
                if (!empty($clientVoucher)) {
                    $transaction->rollBack();
                    return Helpers::HttpException(422, 'validation failed', ['error' => 'you can use this voucher for one time only']);
                } else {
                    $clientVoucher = new ClientsVouchers();
                    $clientVoucher->client_id = $client->id;
                    $clientVoucher->voucher_id = $voucher->id;
                    $clientVoucher->save();
                    $order->voucher_id = $voucher->id;
                }

                $orderTotalWithVoucher = $orderTotal - $voucher->value;
            } else {
                $orderTotalWithVoucher = $orderTotal;
            }

            if (doubleval($restaurants->minimum_order_amount) >= $orderTotal) {
                $transaction->rollBack();
                return Helpers::HttpException(422, 'validation failed', ['error' => 'sorry but your order total is ' . $orderTotal . ', and it must be greater than ' . $restaurants->minimum_order_amount . ' to procedure with your order.']);
            }

            $order->total = $orderTotal + $restaurants->delivery_fee;
            $order->total_with_voucher = $orderTotalWithVoucher + $restaurants->delivery_fee;
            $order->delivery_fee = $restaurants->delivery_fee;
            $order->commission_amount = Commissions::getOrderCommissions($orderTotal)->value;
            if (!$order->validate()) {
                $transaction->rollBack();
                return Helpers::HttpException(422, 'validation failed', ['error' => $order->errors]);
            }
            $order->save();

            $transaction->commit();
            $response = [
                'id' => (int)$order->id,
                'status' => (string)$order->status->name,
                'reference_number' => (string)$order->reference_number,
                'restaurant_name' => (string)$restaurants->name,
                'order_date_time' => (string)Restaurants::getDateTimeBaseOnRestaurantCountry($restaurants->id, $order->created_at),
                'restaurant_delivery_fee' => (float)$restaurants->delivery_fee,
                'restaurant_delivery_time' => (int)$restaurants->delivery_duration,
                'total' => (float)$order->total,
                'total_with_voucher' => (float)$order->total_with_voucher,
            ];
            return Helpers::formatResponse(true, 'make order success', $response);
        } catch (\Exception $e) {
            $transaction->rollBack();
            return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
        }
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
                        return $this->status;
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
                    'note',
                    'delivery_fee' => function () {
                        return $this->delivery_fee;
                    },
                    'status' => function () {
                        return $this->status->name;
                    },
                    'vouchers' => function () {
                        return (!empty($this->voucher) ? $this->voucher->getVoucherFields() : []) ;
                    },
                    'customer_details' => function () {
                        $customer_details = array();
                        $customer_details['id'] = $this->client->id;
                        $customer_details['name'] = $this->client->user->username;
                        $customer_details['email'] = $this->client->user->email;
                        $customer_details['phone_number'] = $this->client->phone_number;
                        $address = Addresses::find()->where(['client_id' => $this->client->id])->andWhere(['is_default' => 1])->andWhere(['deleted_at' => null])->one();
                        if (empty($address))
                            $address = Addresses::find()->where(['client_id' => $this->client->id])->andWhere(['deleted_at' => null])->orderBy('created_at DESC')->one();
                        $customer_details['address'] = $address;
                        return $customer_details;
                    },
                    'order_items' => function () {
                        $order_items = array();
                        foreach ($this->orderItems as $orderItem) {
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
                    },
                    'created_at' => function () {
                        return (string)date('d/m/Y H:i:s', strtotime($this->created_at));
                    }
                ],
                self::SCENARIO_CLIENT_ORDERS => [
                    'id',
                    'status' => function () {
                        return (string)$this->status->name;
                    },
                    'reference_number' => function () {
                        return (string)$this->reference_number;
                    },
                    'restaurant_name' => function () {
                        return (string)$this->restaurant->name;
                    },
                    'restaurant_logo' => function () {
                        return (string)$this->restaurant->image;
                    },
                    'order_date_time' => function () {
                        return (string)Restaurants::getDateTimeBaseOnRestaurantCountry($this->restaurant->id, $this->created_at);
                    },
                ],
                self::SCENARIO_CLIENT_ORDER_DETAILS => [
                    'id',
                    'status' => function () {
                        return (string)$this->status->name;
                    },
                    'reference_number' => function () {
                        return (string)$this->reference_number;
                    },
                    'restaurant_name' => function () {
                        return (string)$this->restaurant->name;
                    },
                    'restaurant_logo' => function () {
                        return (string)$this->restaurant->image;
                    },
                    'order_date_time' => function () {
                        return (string)Restaurants::getDateTimeBaseOnRestaurantCountry($this->restaurant->id, $this->created_at);
                    },
                    'order_delivery_fee' => function () {
                        return (float)$this->delivery_fee;
                    },
                    'restaurant_delivery_duration' => function () {
                        return (int)$this->restaurant->delivery_duration;
                    },
                    'total' => function () {
                        return (float)$this->total;
                    },
                    'total_with_voucher' => function () {
                        return (float)$this->total_with_voucher;
                    },
                    'address' => function () {
                        return $this->address;
                    },
                    'items' => function () {
                        return $this->orderItems;
                    },
                ]
            ]
        );
    }

    public function fields()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        $request_action = explode('?', Yii::$app->getRequest()->getUrl());
        $request_action = explode('/', $request_action[0]);

        if ((in_array('clients', $request_action) && in_array('orders', $request_action)) && $request->isGet) {
            if (!isset($get_data['id']))
                return $this->scenarios()[self::SCENARIO_CLIENT_ORDERS];
            else if (!empty($get_data) && isset($get_data['id']))
                return $this->scenarios()[self::SCENARIO_CLIENT_ORDER_DETAILS];
        } else if (in_array('orders', $request_action) && $request->isGet) {
            if (!isset($get_data['id']))
                return $this->scenarios()[self::SCENARIO_ALL_ORDERS];
            else if (!empty($get_data) && isset($get_data['id']))
                return $this->scenarios()[self::SCENARIO_ORDER_DETAILS];
        }
        return parent::fields();
    }

}
