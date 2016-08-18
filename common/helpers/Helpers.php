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

    public static function formatJsonIdName($json)
    {
        $formatted_json = array();
        foreach ($json as $data){
            $single_json = array();
            $single_json[$data->id] = $data->name;
            $formatted_json [] = $single_json;
        }
        return $formatted_json;
    }
}