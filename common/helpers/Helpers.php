<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/15/2016
 * Time: 11:52 PM
 */

namespace common\helpers;


use yii\web\Response;

class Helpers
{
    public static function UnprocessableEntityHttpException($message, $data)
    {
        $response = \Yii::$app->getResponse();
        $response->setStatusCode(422);
        $response->format = Response::FORMAT_JSON;
        $response->data = ['success' => false,
                           'message' => $message,
                           'data' => $data];
        $response->send();
        die();
    }
}