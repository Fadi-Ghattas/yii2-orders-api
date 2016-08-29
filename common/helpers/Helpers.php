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
    //web
    public static function formatResponse($success, $message, $data) {
        if(!isset($data[0]) && !empty($data))
        {
            return ['success' => $success,
                    'message' => $message,
                    'data' => (!is_null($data) ? [$data] : $data)];
        } else {
            return ['success' => $success,
                    'message' => $message,
                    'data' => $data];
        }
    }

    public static function HttpException($status_code,$message, $data)
    {
        $response = \Yii::$app->getResponse();
        $response->setStatusCode($status_code);
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

    //db
    public static function linkManyToMany($relationship_model, $related_id ,$new_entities, $old_entities, $entity_id, $relationship_entity_id , $restaurant_id = 0)
    {
        $models = [];
        foreach ($old_entities as $old_entity) {
            $models[$old_entity->$entity_id] = $old_entity;
        }
        $old_entities = $models;

        foreach ($new_entities as $entity) {

            if(!isset($entity['id']))
                return Helpers::HttpException(422,'validation failed', ['error' => "add-on id is required"]);
            if(empty($entity['id']))
                return Helpers::HttpException(422,'validation failed', ['error' => "add-on id can't be blank"]);
            if(!intval($entity['id']))
                return Helpers::HttpException(422,'validation failed', ['error' => "add-on id must be integer"]);

            if (!array_key_exists($entity['id'], $old_entities)) {

//                if ($restaurant_id) {
//                    if (empty(Addons::getAddOn($restaurant_id, $entity['id'])))
//                        return Helpers::HttpException(422, 'validation failed', ['error' => "There add-on dos't exist"]);
//                }
                
                $model_entity = $relationship_model;
                $model_entity->$entity_id = $entity['id'];
                $model_entity->$relationship_entity_id = $related_id;
                $model_entity->validate();
                $model_entity->save();
            } else {
                unset($old_entities[$entity['id']]);
            }
        }

        if (!empty($old_entities))
            foreach ($old_entities as $old_entity)
                $old_entity->delete();
    }
    
}