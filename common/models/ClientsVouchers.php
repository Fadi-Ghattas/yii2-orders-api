<?php

namespace common\models;


use Yii;
use common\helpers\Helpers;
use api\modules\v1\models\VoucherForm;

/**
 * This is the model class for table "clients_vouchers".
 *
 * @property string $client_id
 * @property string $voucher_id
 *
 * @property Clients $client
 * @property Vouchers $voucher
 */
class ClientsVouchers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clients_vouchers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'voucher_id'], 'required'],
            [['client_id', 'voucher_id'], 'integer'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['voucher_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vouchers::className(), 'targetAttribute' => ['voucher_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'client_id' => 'Client ID',
            'voucher_id' => 'Voucher ID',
        ];
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
    public function getVoucher()
    {
        return $this->hasOne(Vouchers::className(), ['id' => 'voucher_id']);
    }

    /**
     * @inheritdoc
     * @return ClientsVouchersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClientsVouchersQuery(get_called_class());
    }

    public function afterValidate()
    {
        if ($this->hasErrors()) {
            return Helpers::HttpException(422, 'validation failed', ['error' => $this->errors]);
        }
    }

    public static function validateClientVoucher($data)
    {
        $client_id = Clients::checkClientAuthorization();
        $client = Clients::find()->where(['id' => $client_id])->one();
        if (!$client->verified)
            return Helpers::HttpException(422, 'validation failed', ['error' => 'please verify your account first!!']);

        $voucherForm = new VoucherForm();
        $voucherForm->setAttributes($data);
        if (!$voucherForm->validate())
            return Helpers::HttpException(422, 'validation failed', ['error' => $voucherForm->errors]);

        if (!Restaurants::isAcceptsVouchers($voucherForm->restaurant_id))
            return Helpers::HttpException(422, 'validation failed', ['error' => 'this restaurants currently not accepting vouchers']);

        $voucher = Vouchers::find()->where(['code' => $voucherForm->voucher_code])->one();
        if (empty($voucher))
            return Helpers::HttpException(422, 'validation failed', ['error' => 'there is no voucher with this code please try again with different code']);
        if (!$voucher->isStart($voucherForm->restaurant_id))
            //return Helpers::HttpException(422, 'validation failed', ['error' => 'this voucher is not yet active please check the voucher start date']);
            return Helpers::HttpException(422, 'validation failed', ['error' => 'you can use this voucher after ' . $voucher->getStartDate($voucherForm->restaurant_id)]);
        if ($voucher->isExpired($voucherForm->restaurant_id))
            return Helpers::HttpException(422, 'validation failed', ['error' => 'this voucher date is expired']);
        if (doubleval($voucher->minimum_order) > doubleval($voucherForm->order_total_amount))
            return Helpers::HttpException(422, 'validation failed', ['error' => 'sorry but this voucher work only for order total bigger than ' . doubleval($voucher->minimum_order)]);

        $clientVoucher = self::find()->where(['client_id' => $client_id])->andWhere(['voucher_id' => $voucher->id])->one();
        if (!empty($clientVoucher))
            return Helpers::HttpException(422, 'validation failed', ['error' => 'you can use this voucher for on time only']);

//        $clientVoucher = new ClientsVouchers();
//        $clientVoucher->client_id = $client_id;
//        $clientVoucher->voucher_id = $voucher->id;
//
//        if (!$clientVoucher->save())
//            return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
//        return Helpers::formatResponse(true, 'rest password success', $voucher->getClientVoucherFields());
        return Helpers::formatResponse(true, 'voucher is valid',  ['voucher_code' => $voucherForm->voucher_code]);
    }

}
