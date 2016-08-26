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

    public static function formatResponse($success, $message, $data) {
        return ['success' => $success, 'message' => $message, 'data' => (!is_null($data) ? [$data] : $data)];
    }

    public static function UnprocessableEntityHttpException($message, $data)
    {
        $response = \Yii::$app->getResponse();
        $response->setStatusCode(422);
        $response->format = Response::FORMAT_JSON;
        $response->data = self::formatResponse(false, $message, $data);
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

    public static function formatArrayJsonIdName($json)
    {
        $formatted_json = array();
        foreach ($json as $data){
            $single_json = array();
            $single_json[$data['id']] = $data['name'];
            $formatted_json [] = $single_json;
        }
        return $formatted_json;
    }
}